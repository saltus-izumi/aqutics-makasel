<?php

namespace App\Livewire\Admin\Progress\Te;

use App\Models\TeProgress;
use App\Models\TeProgressFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class HardStep4 extends Component
{
    use WithFileUploads;

    public $teProgress = null;
    public $pcHachuDate;

    // 発注書
    public array $purchaseOrderUploads = [];
    public array $purchaseOrderFiles = [];

    public string $componentId = '';

    protected $listeners = ['teProgressUpdated' => 'reloadProgress'];
    protected array $teProgressMap = [
        'pcHachuDate' => 'pc_hachu_date',
    ];
    protected function rules(): array
    {
        return [
            'pcHachuDate' => [
                'nullable',
                'string',
                function (string $attribute, $value, \Closure $fail): void {
                    if (blank($value)) {
                        return;
                    }

                    $value = trim((string) $value);
                    if (!preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $value)) {
                        $fail('発注日は正しい日付で入力してください。');
                        return;
                    }

                    [$year, $month, $day] = array_map('intval', explode('/', $value));
                    if (!checkdate($month, $day, $year)) {
                        $fail('発注日は正しい日付で入力してください。');
                    }
                },
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'pcHachuDate.string' => '発注日は文字列で入力してください。',
        ];
    }


    public function mount($teProgress)
    {
        $this->teProgress = $teProgress;
        $this->pcHachuDate = $this->formatDateForDisplay($teProgress?->pc_hachu_date);
        $this->componentId = $this->getId();
        $this->loadPurchaseOrderFiles();
    }

    public function updatedPcHachuDate($value): void
    {
        $this->saveMappedField('pcHachuDate', $value);
    }

    protected function saveMappedField(string $propertyName, $value): void
    {
        if (!array_key_exists($propertyName, $this->teProgressMap) || !$this->teProgress) {
            return;
        }

        $this->validateOnly($propertyName);

        $trimmed = is_string($value) ? trim($value) : trim((string) $value);
        $normalized = $trimmed === '' ? null : $trimmed;

        if ($propertyName === 'pcHachuDate') {
            $normalized = $this->formatDateForStorage($normalized);
            $this->pcHachuDate = $this->formatDateForDisplay($normalized);
        }

        $column = $this->teProgressMap[$propertyName];
        $this->teProgress->{$column} = $normalized;

        if ($propertyName === 'pcHachuDate') {
            $this->teProgress->pc_hachu_date_state = $normalized ? 1 : 0;
        }

        $this->teProgress->save();

        $this->dispatch('teProgressUpdated', teProgressId: $this->teProgress->id);
    }

    public function savePurchaseOrderUploads(): void
    {
        if (!$this->teProgress) {
            return;
        }

        foreach ($this->purchaseOrderUploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/te/{$this->teProgress->id}");

            TeProgressFile::create([
                'te_progress_id' => $this->teProgress->id,
                'file_kind' => TeProgressFile::FILE_KIND_PURCHASE_ORDER,
                'file_name' => $original,
                'file_path' => $path,
                'upload_at' => now(),
            ]);
        }

        $this->purchaseOrderUploads = [];
        $this->loadPurchaseOrderFiles();
    }

    public function removePurchaseOrderFile($fileId): void
    {
        if (!$this->teProgress || !$fileId) {
            return;
        }

        $file = TeProgressFile::query()
            ->where('te_progress_id', $this->teProgress->id)
            ->where('file_kind', TeProgressFile::FILE_KIND_PURCHASE_ORDER)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            return;
        }

        if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();
        $this->loadPurchaseOrderFiles();
    }

    public function reloadProgress($teProgressId = null)
    {
        if (!$this->teProgress) {
            return;
        }

        if ($teProgressId !== null && (int) $teProgressId !== (int) $this->teProgress->id) {
            return;
        }

        $this->teProgress = TeProgress::query()
            ->find($this->teProgress->id);

        $this->pcHachuDate = $this->formatDateForDisplay($this->teProgress?->pc_hachu_date);
    }

    protected function loadPurchaseOrderFiles(): void
    {
        if (!$this->teProgress) {
            $this->purchaseOrderFiles = [];
            return;
        }

        $files = TeProgressFile::query()
            ->where('te_progress_id', $this->teProgress->id)
            ->where('file_kind', TeProgressFile::FILE_KIND_PURCHASE_ORDER)
            ->orderBy('id')
            ->get();

        $this->purchaseOrderFiles = $files->map(function (TeProgressFile $file) {
            return [
                'id' => $file->id,
                'file_name' => $file->file_name ?? '',
                'url' => route('admin.progress.te.preview', ['teProgressFileId' => $file->id]),
                'file_path' => $file->file_path ?? '',
                'mime_type' => $this->getFileMimeType($file),
            ];
        })->all();
    }

    protected function getFileMimeType(TeProgressFile $file): string
    {
        if (!$file->file_path || !Storage::disk('local')->exists($file->file_path)) {
            return '';
        }

        $fullPath = Storage::disk('local')->path($file->file_path);
        return mime_content_type($fullPath) ?: '';
    }

    protected function formatDateForDisplay($value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return Carbon::parse($value)->format('Y/m/d');
    }

    protected function formatDateForStorage(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.admin.progress.te.hard-step4');
    }
}

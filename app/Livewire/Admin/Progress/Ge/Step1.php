<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\GeProgressFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Step1 extends Component
{
    use WithFileUploads;

    public $progress = null;
    public $securityDepositAmount = null;
    public $proratedRentAmount = null;
    public $penaltyForfeitureAmount = null;
    public $inspectionRequestMessage = null;
    public array $step1Uploads = [];
    public array $step1Files = [];
    public string $componentId = '';
    protected array $geProgressMap = [
        'securityDepositAmount' => 'security_deposit_amount',
        'proratedRentAmount' => 'prorated_rent_amount',
        'penaltyForfeitureAmount' => 'penalty_forfeiture_amount',
        'inspectionRequestMessage' => 'inspection_request_message',
    ];
    protected function rules(): array
    {
        return [
            'securityDepositAmount' => ['nullable', 'regex:/^[0-9,]+$/'],
            'proratedRentAmount' => ['nullable', 'regex:/^[0-9,]+$/'],
            'penaltyForfeitureAmount' => ['nullable', 'regex:/^[0-9,]+$/'],
            'inspectionRequestMessage' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'securityDepositAmount.regex' => '敷金預託等は半角数字とカンマで入力してください。',
            'proratedRentAmount.regex' => '日割り家賃は半角数字とカンマで入力してください。',
            'penaltyForfeitureAmount.regex' => '違約金（償却）は半角数字とカンマで入力してください。',
        ];
    }

    public function mount($progress)
    {
        $this->progress = $progress;
        $this->securityDepositAmount = $progress->geProgress?->security_deposit_amount;
        $this->proratedRentAmount = $progress->geProgress?->prorated_rent_amount;
        $this->penaltyForfeitureAmount = $progress->geProgress?->penalty_forfeiture_amount;
        $this->inspectionRequestMessage = $progress->geProgress?->inspection_request_message;
        $this->componentId = $this->getId();
        $this->loadStep1Files();
    }

    public function updated($propertyName, $value)
    {
        if (!array_key_exists($propertyName, $this->geProgressMap)) {
            return;
        }

        // null対策
        if (!$this->progress?->geProgress) {
            return;
        }

        $this->validateOnly($propertyName);

        switch($propertyName) {
            case 'securityDepositAmount':
            case 'proratedRentAmount':
            case 'penaltyForfeitureAmount':
                $value = str_replace(',', '', (string) $value);
                break;
        }

        $value = $value ? $value : null;

        $column = $this->geProgressMap[$propertyName];
        $this->progress->geProgress->{$column} = $value;
        $this->progress->geProgress->save();

        $this->dispatch('geProgressUpdated', progressId: $this->progress->id);
    }

    public function saveStep1Uploads(): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress) {
            return;
        }

        foreach ($this->step1Uploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/ge/{$geProgress->id}");

            GeProgressFile::create([
                'ge_progress_id' => $geProgress->id,
                'file_kind' => GeProgressFile::FILE_KIND_STEP1,
                'file_name' => $original,
                'file_path' => $path,
                'upload_at' => now(),
            ]);
        }

        $this->step1Uploads = [];
        $this->loadStep1Files();
    }

    public function removeStep1File($fileId): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress || !$fileId) {
            return;
        }

        $file = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_STEP1)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            return;
        }

        if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();
        $this->loadStep1Files();
    }

    protected function loadStep1Files(): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress) {
            $this->step1Files = [];
            return;
        }

        $files = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_STEP1)
            ->orderBy('id')
            ->get();

        $this->step1Files = $files->map(function (GeProgressFile $file) {
            return [
                'id' => $file->id,
                'file_name' => $file->file_name ?? '',
                'url' => route('admin.progress.ge.preview', ['geProgressFileId' => $file->id]),
                'file_path' => $file->file_path ?? '',
                'mime_type' => $this->getFileMimeType($file),
            ];
        })->all();
    }

    protected function getFileUrl(GeProgressFile $file): ?string
    {
        if (!$file->file_path) {
            return null;
        }

        return Storage::disk('local')->url($file->file_path);
    }

    protected function getFileMimeType(GeProgressFile $file): string
    {
        if (!$file->file_path || !Storage::disk('local')->exists($file->file_path)) {
            return '';
        }

        $fullPath = Storage::disk('local')->path($file->file_path);
        return mime_content_type($fullPath) ?: '';
    }

    public function render()
    {
        return view('livewire.admin.progress.ge.step1');
    }
}

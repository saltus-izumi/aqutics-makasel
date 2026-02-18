<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\GeProgressFile;
use App\Models\Progress;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Step4 extends Component
{
    use WithFileUploads;

    public $progress = null;
    public $costAmount;
    public $chargeAmount;
    public $profitAmount;
    public $profitRate;
    public $responsiblePersonMessage;

    // 上代見積もり
    public array $retailEstimateUploads = [];
    public array $retailEstimateFiles = [];

    public array $moveOutSettlementFiles = [];      // 退去時清算書
    public array $lowerEstimateFiles = [];          // 下代見積もり
    public array $walkthroughPhotoFiles = [];       // 立会写真

    public string $componentId = '';

    protected $listeners = ['geProgressUpdated' => 'reloadProgress'];
    protected array $geProgressMap = [
        'costAmount' => 'cost_amount',
        'chargeAmount' => 'charge_amount',
        'responsiblePersonMessage' => 'responsible_person_message',
    ];
    protected function rules(): array
    {
        return [
            'costAmount' => ['nullable', 'regex:/^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'],
            'chargeAmount' => ['nullable', 'regex:/^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'],
            'responsiblePersonMessage' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'costAmount.regex' => '下代は半角数字で入力してください。',
            'chargeAmount.regex' => '上代は半角数字で入力してください。',
        ];
    }

    public function mount($progress)
    {
        $this->progress = $progress;
        $this->costAmount = $progress->geProgress?->cost_amount;
        $this->chargeAmount = $progress->geProgress?->charge_amount;
        $this->responsiblePersonMessage = $progress->geProgress?->responsible_person_message;
        $this->componentId = $this->getId();
        $this->loadRetailEstimateFiles();
        $this->loadMoveOutSettlementFiles();
        $this->loadLowerEstimateFiles();
        $this->loadWalkthroughPhotoFiles();
        $this->calcProfit();
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
            case 'costAmount':
            case 'chargeAmount':
                $value = str_replace(',', '', (string) $value);
                break;
        }

        $value = trim($value) ? trim($value) : null;

        $column = $this->geProgressMap[$propertyName];
        $this->progress->geProgress->{$column} = $value;
        $this->progress->geProgress->save();

        $this->calcProfit();

        $this->dispatch('geProgressUpdated', progressId: $this->progress->id);
    }

    public function saveRetailEstimateUploads(): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress) {
            return;
        }

        foreach ($this->retailEstimateUploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/ge/{$geProgress->id}");

            GeProgressFile::create([
                'ge_progress_id' => $geProgress->id,
                'file_kind' => GeProgressFile::FILE_KIND_RETAIL_ESTIMATE,
                'file_name' => $original,
                'file_path' => $path,
                'upload_at' => now(),
            ]);
        }

        $this->retailEstimateUploads = [];
        $this->loadRetailEstimateFiles();
    }

    public function removeRetailEstimateFile($fileId): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress || !$fileId) {
            return;
        }

        $file = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_RETAIL_ESTIMATE)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            return;
        }

        if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();
        $this->loadRetailEstimateFiles();
    }

    public function reloadProgress($progressId = null)
    {
        if (!$this->progress) {
            return;
        }

        if ($progressId !== null && (int) $progressId !== (int) $this->progress->id) {
            return;
        }

        $this->progress = Progress::query()
            ->with('geProgress')
            ->find($this->progress->id);
    }

    protected function loadRetailEstimateFiles(): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress) {
            $this->retailEstimateFiles = [];
            return;
        }

        $files = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_RETAIL_ESTIMATE)
            ->orderBy('id')
            ->get();

        $this->retailEstimateFiles = $files->map(function (GeProgressFile $file) {
            return [
                'id' => $file->id,
                'file_name' => $file->file_name ?? '',
                'url' => route('admin.progress.ge.preview', ['geProgressFileId' => $file->id]),
                'file_path' => $file->file_path ?? '',
                'mime_type' => $this->getFileMimeType($file),
            ];
        })->all();
    }

    protected function loadMoveOutSettlementFiles(): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress) {
            $this->moveOutSettlementFiles = [];
            return;
        }

        $files = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_MOVE_OUT_SETTLEMENT)
            ->orderBy('id')
            ->get();

        $this->moveOutSettlementFiles = $files
            ->mapWithKeys(function (GeProgressFile $file) {
                return [
                    route('admin.progress.ge.preview', ['geProgressFileId' => $file->id]) => $file->file_name ?? '',
                ];
            })
            ->all();
    }

    protected function loadLowerEstimateFiles(): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress) {
            $this->lowerEstimateFiles = [];
            return;
        }

        $files = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_LOWER_ESTIMATE)
            ->orderBy('id')
            ->get();

        $this->lowerEstimateFiles = $files
            ->mapWithKeys(function (GeProgressFile $file) {
                return [
                    route('admin.progress.ge.preview', ['geProgressFileId' => $file->id]) => $file->file_name ?? '',
                ];
            })
            ->all();
    }

    protected function loadWalkthroughPhotoFiles(): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress) {
            $this->walkthroughPhotoFiles = [];
            return;
        }

        $files = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_WALKTHROUGH_PHOTO)
            ->orderBy('id')
            ->get();

        $this->walkthroughPhotoFiles = $files
            ->mapWithKeys(function (GeProgressFile $file) {
                return [
                    route('admin.progress.ge.preview', ['geProgressFileId' => $file->id]) => $file->file_name ?? '',
                ];
            })
            ->all();
    }

    protected function getFileMimeType(GeProgressFile $file): string
    {
        if (!$file->file_path || !Storage::disk('local')->exists($file->file_path)) {
            return '';
        }

        $fullPath = Storage::disk('local')->path($file->file_path);
        return mime_content_type($fullPath) ?: '';
    }

    protected function calcProfit() {
        $profitAmount = (int)$this->progress->geProgress->charge_amount - (int)$this->progress->geProgress->cost_amount;
        if ($this->progress->geProgress->charge_amount > 0) {
            $profitRate = $profitAmount / $this->progress->geProgress->charge_amount * 100;
        } else {
            $profitRate = 0;
        }

        $this->profitAmount = number_format($profitAmount);
        $this->profitRate = round($profitRate, 2);
    }

    public function render()
    {
        return view('livewire.admin.progress.ge.step4');
    }
}

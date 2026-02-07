<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\GeProgressFile;
use App\Models\Progress;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Step2 extends Component
{
    public $progress = null;
    public $transferDueDate;
    public $subtotalAAmount;
    public $subtotalBAmount;
    public $subtotalCAmount;
    public $otherAmount;
    public $inspectionCompletedMessage;
    public array $moveOutSettlementUploads = [];
    public array $moveOutSettlementFiles = [];
    public array $costEstimateUploads = [];
    public array $costEstimateFiles = [];
    public array $walkthroughPhotoUploads = [];
    public array $walkthroughPhotoFiles = [];

    protected $listeners = ['geProgressUpdated' => 'reloadProgress'];
    protected array $geProgressMap = [
        'transferDueDate' => 'transfer_due_date',
        'subtotalAAmount' => 'subtotal_a_amount',
        'subtotalBAmount' => 'subtotal_b_amount',
        'subtotalCAmount' => 'subtotal_c_amount',
        'otherAmount' => 'other_amount',
        'inspectionCompletedMessage' => 'inspection_completed_message',
    ];
    protected function rules(): array
    {
        return [
            'subtotalAAmount' => ['nullable', 'regex:/^[0-9,]+$/'],
            'subtotalBAmount' => ['nullable', 'regex:/^[0-9,]+$/'],
            'subtotalCAmount' => ['nullable', 'regex:/^[0-9,]+$/'],
            'inspectionCompletedMessage' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'subtotalAAmount.regex' => '小計Aは半角数字で入力してください。',
            'subtotalBAmount.regex' => '小計は半角数字で入力してください。',
            'subtotalCAmount.regex' => '小計Aは半角数字で入力してください。',
            'otherAmount.regex' => 'その他は半角数字で入力してください。',
        ];
    }


    public function mount($progress)
    {
        $this->progress = $progress;
        $this->transferDueDate = $progress->geProgress?->transfer_due_date;
        $this->subtotalAAmount = $progress->geProgress?->subtotal_a_amount;
        $this->subtotalBAmount = $progress->geProgress?->subtotal_b_amount;
        $this->subtotalCAmount = $progress->geProgress?->subtotal_c_amount;
        $this->otherAmount = $progress->geProgress?->other_amount;
        $this->inspectionCompletedMessage = $progress->geProgress?->inspection_completed_message;
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
            case 'subtotalAAmount':
            case 'subtotalBAmount':
            case 'subtotalCAmount':
            case 'otherAmount':
                $value = str_replace(',', '', (string) $value);
                break;
        }

        $value = trim($value) ? trim($value) : null;

        $column = $this->geProgressMap[$propertyName];
        $this->progress->geProgress->{$column} = $value;
        $this->progress->geProgress->save();

        $this->dispatch('geProgressUpdated', progressId: $this->progress->id);
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

        $this->moveOutSettlementFiles = $files->map(function (GeProgressFile $file) {
            return [
                'id' => $file->id,
                'file_name' => $file->file_name ?? '',
                'url' => route('admin.progress.ge.preview', ['geProgressFileId' => $file->id]),
                'file_path' => $file->file_path ?? '',
                'mime_type' => $this->getFileMimeType($file),
            ];
        })->all();
    }

    protected function loadCostEstimateFiles(): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress) {
            $this->costEstimateFiles = [];
            return;
        }

        $files = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_COST_ESTIMATE)
            ->orderBy('id')
            ->get();

        $this->costEstimateFiles = $files->map(function (GeProgressFile $file) {
            return [
                'id' => $file->id,
                'file_name' => $file->file_name ?? '',
                'url' => route('admin.progress.ge.preview', ['geProgressFileId' => $file->id]),
                'file_path' => $file->file_path ?? '',
                'mime_type' => $this->getFileMimeType($file),
            ];
        })->all();
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

        $this->walkthroughPhotoFiles = $files->map(function (GeProgressFile $file) {
            return [
                'id' => $file->id,
                'file_name' => $file->file_name ?? '',
                'url' => route('admin.progress.ge.preview', ['geProgressFileId' => $file->id]),
                'file_path' => $file->file_path ?? '',
                'mime_type' => $this->getFileMimeType($file),
            ];
        })->all();
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
        return view('livewire.admin.progress.ge.step2');
    }
}

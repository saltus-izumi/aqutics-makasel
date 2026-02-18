<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\GeProgressFile;
use App\Models\Progress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Step2 extends Component
{
    use WithFileUploads;

    public $progress = null;
    public $transferDueDate;
    public $subtotalAAmount;
    public $subtotalBAmount;
    public $subtotalCAmount;
    public $otherAmount;
    public $inspectionCompletedMessage;

    public $constructionCostExclTax;        // 工事負担額（税抜）
    public $constructionCostInclTax;        // 工事負担額（税込）
    public $settlementAmount;               // 精算額

    // 退去時清算書
    public array $moveOutSettlementUploads = [];
    public array $moveOutSettlementFiles = [];

    // 下代見積もり
    public array $lowerEstimateUploads = [];
    public array $lowerEstimateFiles = [];

    // 立会写真
    public array $walkthroughPhotoUploads = [];
    public array $walkthroughPhotoFiles = [];
    public string $componentId = '';

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
            'transferDueDate' => ['nullable', 'date'],
            'subtotalAAmount' => ['nullable', 'regex:/^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'],
            'subtotalBAmount' => ['nullable', 'regex:/^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'],
            'subtotalCAmount' => ['nullable', 'regex:/^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'],
            'otherAmount' => ['nullable', 'regex:/^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'],
            'inspectionCompletedMessage' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'subtotalAAmount.regex' => '小計Aは半角数字で入力してください。',
            'subtotalBAmount.regex' => '小計Bは半角数字で入力してください。',
            'subtotalCAmount.regex' => '小計Cは半角数字で入力してください。',
            'otherAmount.regex' => 'その他は半角数字で入力してください。',
        ];
    }


    public function mount($progress)
    {
        $this->progress = $progress;
        $this->transferDueDate = $progress->geProgress?->transfer_due_date?->format('Y-m-d');
Log::debug($this->transferDueDate);

        $this->subtotalAAmount = $progress->geProgress?->subtotal_a_amount;
        $this->subtotalBAmount = $progress->geProgress?->subtotal_b_amount;
        $this->subtotalCAmount = $progress->geProgress?->subtotal_c_amount;
        $this->otherAmount = $progress->geProgress?->other_amount;
        $this->inspectionCompletedMessage = $progress->geProgress?->inspection_completed_message;
        $this->componentId = $this->getId();
        $this->loadMoveOutSettlementFiles();
        $this->loadLowerEstimateFiles();
        $this->loadWalkthroughPhotoFiles();
        $this->calcConstructionCost();
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
        $this->calcConstructionCost();
    }

    public function saveMoveOutSettlementUploads(): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress) {
            return;
        }

        foreach ($this->moveOutSettlementUploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/ge/{$geProgress->id}");

            GeProgressFile::create([
                'ge_progress_id' => $geProgress->id,
                'file_kind' => GeProgressFile::FILE_KIND_MOVE_OUT_SETTLEMENT,
                'file_name' => $original,
                'file_path' => $path,
                'upload_at' => now(),
            ]);
        }

        $this->moveOutSettlementUploads = [];
        $this->loadMoveOutSettlementFiles();
    }

    public function removeMoveOutSettlementFile($fileId): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress || !$fileId) {
            return;
        }

        $file = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_MOVE_OUT_SETTLEMENT)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            return;
        }

        if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();
        $this->loadMoveOutSettlementFiles();
    }

    public function saveLowerEstimateUploads(): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress) {
            return;
        }

        foreach ($this->lowerEstimateUploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/ge/{$geProgress->id}");

            GeProgressFile::create([
                'ge_progress_id' => $geProgress->id,
                'file_kind' => GeProgressFile::FILE_KIND_LOWER_ESTIMATE,
                'file_name' => $original,
                'file_path' => $path,
                'upload_at' => now(),
            ]);
        }

        // ファイルがなければ見積書受信日をNULLにする
        if (count($this->lowerEstimateUploads) > 0 && !$this->progress->genpuku_mitsumori_recieved_date) {
            $this->progress->genpuku_mitsumori_recieved_date = now();
            $this->progress->save();
            $this->dispatch('geProgressUpdated', progressId: $this->progress->id);
        }

        $this->lowerEstimateUploads = [];
        $this->loadLowerEstimateFiles();
    }

    public function removeLowerEstimateFile($fileId): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress || !$fileId) {
            return;
        }

        $file = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_LOWER_ESTIMATE)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            return;
        }

        if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();

        $fileCount = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_LOWER_ESTIMATE)
            ->count();

        // ファイルがなければ見積書受信日をNULLにする
        if ($fileCount == 0 && $this->progress->genpuku_mitsumori_recieved_date) {
            $this->progress->genpuku_mitsumori_recieved_date = null;
            $this->progress->save();
            $this->dispatch('geProgressUpdated', progressId: $this->progress->id);
        }

        $this->loadLowerEstimateFiles();
    }

    public function saveWalkthroughPhotoUploads(): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress) {
            return;
        }

        foreach ($this->walkthroughPhotoUploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/ge/{$geProgress->id}");

            GeProgressFile::create([
                'ge_progress_id' => $geProgress->id,
                'file_kind' => GeProgressFile::FILE_KIND_WALKTHROUGH_PHOTO,
                'file_name' => $original,
                'file_path' => $path,
                'upload_at' => now(),
            ]);
        }

        $this->walkthroughPhotoUploads = [];
        $this->loadWalkthroughPhotoFiles();
    }

    public function removeWalkthroughPhotoFile($fileId): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress || !$fileId) {
            return;
        }

        $file = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_WALKTHROUGH_PHOTO)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            return;
        }

        if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();
        $this->loadWalkthroughPhotoFiles();
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

        $this->calcConstructionCost();
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

        $this->lowerEstimateFiles = $files->map(function (GeProgressFile $file) {
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

    protected function calcConstructionCost() {
        $this->constructionCostExclTax = (int)$this->subtotalAAmount + (int)$this->subtotalBAmount + (int)$this->subtotalCAmount;
        $this->constructionCostInclTax = floor($this->constructionCostExclTax * 1.1);

        $this->settlementAmount = $this->constructionCostInclTax +
            (int)$this->progress->geProgress?->security_deposit_amount +
            (int)$this->progress->geProgress?->prorated_rent_amount -
            (int)$this->progress->geProgress?->penalty_forfeiture_amount +
            (int)$this->otherAmount;
    }

    public function render()
    {
        return view('livewire.admin.progress.ge.step2');
    }
}

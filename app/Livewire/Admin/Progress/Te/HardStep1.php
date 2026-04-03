<?php

namespace App\Livewire\Admin\Progress\Te;

use App\Models\TeProgress;
use App\Models\TeProgressFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class HardStep1 extends Component
{
    use WithFileUploads;

    public $teProgress = null;
    public $costAmount;
    public $chargeAmount;
    public $profitAmount;
    public $profitRate;
    public $executorToResponsibleMessage;

    // 現調報告書
    public array $onSiteInspectionReportUploads = [];
    public array $onSiteInspectionReportFiles = [];

    // 下代見積もり
    public array $lowerEstimateUploads = [];
    public array $lowerEstimateFiles = [];

    // 上代見積もり
    public array $retailEstimateUploads = [];
    public array $retailEstimateFiles = [];
    public string $componentId = '';

    protected $listeners = ['teProgressUpdated' => 'reloadProgress'];
    protected array $teProgressMap = [
        'costAmount' => 'cost_amount',
        'chargeAmount' => 'charge_amount',
        'executorToResponsibleMessage' => 'executor_to_responsible_message',
    ];
    protected function rules(): array
    {
        return [
            'costAmount' => ['nullable', 'regex:/^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'],
            'chargeAmount' => ['nullable', 'regex:/^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'],
            'executorToResponsibleMessage' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'costAmount.regex' => '下代は半角数字で入力してください。',
            'chargeAmount.regex' => '上代は半角数字で入力してください。',
        ];
    }


    public function mount($teProgress)
    {
        $this->teProgress = $teProgress;
        $this->costAmount = $teProgress?->cost_amount;
        $this->chargeAmount = $teProgress?->charge_amount;
        $this->calcProfit();
        $this->executorToResponsibleMessage = $teProgress?->executor_to_responsible_message;
        $this->componentId = $this->getId();
        $this->loadOnSiteInspectionReportFiles();
        $this->loadLowerEstimateFiles();
        $this->loadRetailEstimateFiles();
    }

    public function updatedCostAmount($value): void
    {
        $this->saveMappedField('costAmount', $value);
    }

    public function updatedChargeAmount($value): void
    {
        $this->saveMappedField('chargeAmount', $value);
    }

    public function updatedExecutorToResponsibleMessage($value): void
    {
        $this->saveMappedField('executorToResponsibleMessage', $value);
    }

    protected function saveMappedField(string $propertyName, $value): void
    {
        if (!array_key_exists($propertyName, $this->teProgressMap) || !$this->teProgress) {
            return;
        }

        $this->validateOnly($propertyName);

        if (in_array($propertyName, ['costAmount', 'chargeAmount'], true)) {
            $value = str_replace(',', '', (string) $value);
        }

        $trimmed = is_string($value) ? trim($value) : trim((string) $value);
        $normalized = $trimmed === '' ? null : $trimmed;

        $column = $this->teProgressMap[$propertyName];
        $this->teProgress->{$column} = $normalized;
        $this->teProgress->save();

        $this->calcProfit();
        $this->dispatch('teProgressUpdated', teProgressId: $this->teProgress->id);
    }

    public function saveOnSiteInspectionReportUploads(): void
    {
        if (!$this->teProgress) {
            return;
        }

        foreach ($this->onSiteInspectionReportUploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/te/{$this->teProgress->id}");

            TeProgressFile::create([
                'te_progress_id' => $this->teProgress->id,
                'file_kind' => TeProgressFile::FILE_KIND_ON_SITE_INSPECTION_REPORT,
                'file_name' => $original,
                'file_path' => $path,
                'upload_at' => now(),
            ]);
        }

        $this->onSiteInspectionReportUploads = [];
        $this->loadOnSiteInspectionReportFiles();
    }

    public function removeOnSiteInspectionReportFile($fileId): void
    {
        if (!$this->teProgress || !$fileId) {
            return;
        }

        $file = TeProgressFile::query()
            ->where('te_progress_id', $this->teProgress->id)
            ->where('file_kind', TeProgressFile::FILE_KIND_ON_SITE_INSPECTION_REPORT)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            return;
        }

        if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();
        $this->loadOnSiteInspectionReportFiles();
    }

    public function saveLowerEstimateUploads(): void
    {
        if (!$this->teProgress) {
            return;
        }

        foreach ($this->lowerEstimateUploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/te/{$this->teProgress->id}");

            TeProgressFile::create([
                'te_progress_id' => $this->teProgress->id,
                'file_kind' => TeProgressFile::FILE_KIND_LOWER_ESTIMATE,
                'file_name' => $original,
                'file_path' => $path,
                'upload_at' => now(),
            ]);
        }

        // ファイルがなければ見積書受信日をNULLにする
        if (count($this->lowerEstimateUploads) > 0 && !$this->teProgress->cost_received_date) {
            $this->teProgress->cost_received_date = now();
            $this->teProgress->cost_received_date_state = 1;
            $this->teProgress->save();
            $this->dispatch('teProgressUpdated', teProgressId: $this->teProgress->id);
        }

        $this->lowerEstimateUploads = [];
        $this->loadLowerEstimateFiles();
    }

    public function removeLowerEstimateFile($fileId): void
    {
        if (!$this->teProgress || !$fileId) {
            return;
        }

        $file = TeProgressFile::query()
            ->where('te_progress_id', $this->teProgress->id)
            ->where('file_kind', TeProgressFile::FILE_KIND_LOWER_ESTIMATE)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            return;
        }

        if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();

        $fileCount = TeProgressFile::query()
            ->where('te_progress_id', $this->teProgress->id)
            ->where('file_kind', TeProgressFile::FILE_KIND_LOWER_ESTIMATE)
            ->count();

        // ファイルがなければ見積書受信日をNULLにする
        if ($fileCount == 0 && $this->teProgress->cost_received_date) {
            $this->teProgress->cost_received_date = null;
            $this->teProgress->cost_received_date_state = 0;
            $this->teProgress->save();
            $this->dispatch('teProgressUpdated', teProgressId: $this->teProgress->id);
        }

        $this->loadLowerEstimateFiles();
    }

    public function saveRetailEstimateUploads(): void
    {
        if (!$this->teProgress) {
            return;
        }

        foreach ($this->retailEstimateUploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/te/{$this->teProgress->id}");

            TeProgressFile::create([
                'te_progress_id' => $this->teProgress->id,
                'file_kind' => TeProgressFile::FILE_KIND_RETAIL_ESTIMATE,
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
        if (!$this->teProgress || !$fileId) {
            return;
        }

        $file = TeProgressFile::query()
            ->where('te_progress_id', $this->teProgress->id)
            ->where('file_kind', TeProgressFile::FILE_KIND_RETAIL_ESTIMATE)
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

        $this->costAmount = $this->teProgress?->cost_amount;
        $this->chargeAmount = $this->teProgress?->charge_amount;
        $this->executorToResponsibleMessage = $this->teProgress?->executor_to_responsible_message;
        $this->calcProfit();
    }

    protected function loadOnSiteInspectionReportFiles(): void
    {
        if (!$this->teProgress) {
            $this->onSiteInspectionReportFiles = [];
            return;
        }

        $files = TeProgressFile::query()
            ->where('te_progress_id', $this->teProgress->id)
            ->where('file_kind', TeProgressFile::FILE_KIND_ON_SITE_INSPECTION_REPORT)
            ->orderBy('id')
            ->get();

        $this->onSiteInspectionReportFiles = $files->map(function (TeProgressFile $file) {
            return [
                'id' => $file->id,
                'file_name' => $file->file_name ?? '',
                'url' => route('admin.progress.te.preview', ['teProgressFileId' => $file->id]),
                'file_path' => $file->file_path ?? '',
                'mime_type' => $this->getFileMimeType($file),
            ];
        })->all();
    }

    protected function loadLowerEstimateFiles(): void
    {
        if (!$this->teProgress) {
            $this->lowerEstimateFiles = [];
            return;
        }

        $files = TeProgressFile::query()
            ->where('te_progress_id', $this->teProgress->id)
            ->where('file_kind', TeProgressFile::FILE_KIND_LOWER_ESTIMATE)
            ->orderBy('id')
            ->get();

        $this->lowerEstimateFiles = $files->map(function (TeProgressFile $file) {
            return [
                'id' => $file->id,
                'file_name' => $file->file_name ?? '',
                'url' => route('admin.progress.te.preview', ['teProgressFileId' => $file->id]),
                'file_path' => $file->file_path ?? '',
                'mime_type' => $this->getFileMimeType($file),
            ];
        })->all();
    }

    protected function loadRetailEstimateFiles(): void
    {
        if (!$this->teProgress) {
            $this->retailEstimateFiles = [];
            return;
        }

        $files = TeProgressFile::query()
            ->where('te_progress_id', $this->teProgress->id)
            ->where('file_kind', TeProgressFile::FILE_KIND_RETAIL_ESTIMATE)
            ->orderBy('id')
            ->get();

        $this->retailEstimateFiles = $files->map(function (TeProgressFile $file) {
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

    protected function calcProfit() {
        if ($this->teProgress) {
            $this->costAmount = $this->teProgress->cost_amount;
            $this->chargeAmount = $this->teProgress->charge_amount;
        }

        $costAmount = (int) str_replace(',', '', (string) ($this->costAmount ?? 0));
        $chargeAmount = (int) str_replace(',', '', (string) ($this->chargeAmount ?? 0));

        if (!$this->teProgress && $costAmount === 0 && $chargeAmount === 0) {
            $this->profitAmount = number_format(0);
            $this->profitRate = 0;
            return;
        }

        $profitAmount = $chargeAmount - $costAmount;
        if ($chargeAmount > 0) {
            $profitRate = $profitAmount / $chargeAmount * 100;
        } else {
            $profitRate = 0;
        }

        $this->profitAmount = number_format($profitAmount);
        $this->profitRate = round($profitRate, 2);
    }

    public function render()
    {
        return view('livewire.admin.progress.te.hard-step1');
    }
}

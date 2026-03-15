<?php

namespace App\Livewire\Admin\Progress\En;

use App\Models\EnProgress;
use App\Models\GeProgressFile;
use App\Models\GuaranteeCompany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class ContractTerms extends Component
{
    use WithFileUploads;

    public $enProgress = null;
    public $latestGeProgress = null;
    public $guaranteeCompanyOptions = [];

    public $securityDepositAmount = null;
    public $proratedRentAmount = null;
    public $penaltyForfeitureAmount = null;
    public $inspectionRequestMessage = null;
    public $isStep1Confirmed = false;
    public array $step1Uploads = [];
    public array $step1Files = [];
    public string $componentId = '';

    protected $listeners = ['enProgressUpdated' => 'reloadProgress'];
    protected array $contractTermFieldConfig = [
        'fr_active_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
        'fr_start_date' => ['rules' => ['nullable', 'date'], 'type' => 'date'],
        'fr_end_date' => ['rules' => ['nullable', 'date'], 'type' => 'date'],
        'desired_contract_date' => ['rules' => ['nullable', 'date'], 'type' => 'date'],
        'planned_payment_date' => ['rules' => ['nullable', 'date'], 'type' => 'date'],
        'desired_move_in_date' => ['rules' => ['nullable', 'date'], 'type' => 'date'],
        'renewal_fee' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'guarantee_company_id' => ['rules' => ['nullable'], 'type' => 'string'],
        'guarantee_company_plan' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'guarantee_company_monthly_fee' => ['rules' => ['nullable', 'regex:/^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'], 'type' => 'integer'],
        'guarantee_company_status' => ['rules' => ['nullable', 'integer'], 'type' => 'integer'],
        'fire_insurance_name' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'fire_insurance_monthly_fee' => ['rules' => ['nullable', 'regex:/^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'], 'type' => 'integer'],
        'fire_insurance_status' => ['rules' => ['nullable', 'integer'], 'type' => 'integer'],
        'anshin_support_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
        'move_out_cleaning_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
        'ac_cleaning_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
        'cancellation_penalty_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
        'pet_allowed_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
        'instrument_allowed_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
        'fr_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
        'two_person_allowed_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
    ];

    public function mount($enProgress)
    {
        $this->enProgress = $enProgress;
        $this->latestGeProgress = $enProgress->progress?->latestGeProgress;
        $this->guaranteeCompanyOptions = GuaranteeCompany::query()
            ->orderBy('id')
            ->pluck('company_name', 'id')
            ->toArray();

        $this->securityDepositAmount = $enProgress?->security_deposit_amount;
        $this->proratedRentAmount = $enProgress?->prorated_rent_amount;
        $this->penaltyForfeitureAmount = $enProgress?->penalty_forfeiture_amount;
        $this->inspectionRequestMessage = $enProgress?->inspection_request_message;
        $this->isStep1Confirmed = $enProgress?->is_step1_confirmed;
        $this->componentId = $this->getId();
        $this->loadStep1Files();
    }

    public function saveFieldByName(string $fieldName, $value): void
    {
        if (!$this->enProgress || !array_key_exists($fieldName, $this->contractTermFieldConfig)) {
            return;
        }

        $ruleSet = [
            $fieldName => $this->contractTermFieldConfig[$fieldName]['rules'],
        ];

        $validator = Validator::make([$fieldName => $value], $ruleSet);
        if ($validator->fails()) {
            return;
        }

        $normalizedValue = $this->normalizeContractTermValue($fieldName, $value);

        $this->enProgress->{$fieldName} = $normalizedValue;
        $this->enProgress->save();

        $this->dispatch('enProgressUpdated', enProgressId: $this->enProgress->id);
    }

    protected function normalizeContractTermValue(string $fieldName, $value)
    {
        $type = $this->contractTermFieldConfig[$fieldName]['type'] ?? 'string';

        if ($value === '') {
            return $type === 'boolean' ? false : null;
        }

        return match ($type) {
            'boolean' => $this->normalizeBoolean($value),
            'integer' => $this->normalizeInteger($value),
            'date' => $this->normalizeDate($value),
            default => $this->normalizeString($value),
        };
    }

    protected function normalizeBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array((string) $value, ['1', 'true', 'on'], true);
    }

    protected function normalizeInteger($value): ?int
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);
        if ($trimmed === '') {
            return null;
        }

        return (int) str_replace(',', '', $trimmed);
    }

    protected function normalizeDate($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);
        if ($trimmed === '') {
            return null;
        }

        $timestamp = strtotime(str_replace('/', '-', $trimmed));
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d', $timestamp);
    }

    protected function normalizeString($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }

    public function updateMoveOutReportDate(): void
    {
        if ($this->enProgress->move_out_report_date) {
            return;
        }

        $this->enProgress->move_out_report_date = now();
        $this->enProgress->save();
    }

    public function saveStep1Uploads(): void
    {
        foreach ($this->step1Uploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/ge/{$this->enProgress->id}");

            GeProgressFile::create([
                'ge_progress_id' => $this->enProgress->id,
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
        if (!$this->enProgress || !$fileId) {
            return;
        }

        $file = GeProgressFile::query()
            ->where('ge_progress_id', $this->enProgress->id)
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
        if (!$this->enProgress) {
            $this->step1Files = [];
            return;
        }

        $files = GeProgressFile::query()
            ->where('ge_progress_id', $this->enProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_STEP1)
            ->orderBy('id')
            ->get();

        $this->step1Files = $files->map(function (GeProgressFile $file) {
            return [
                'id' => $file->id,
                'file_name' => $file->file_name ?? '',
                'url' => route('admin.progress.ge.preview', ['enProgressFileId' => $file->id]),
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

    public function reloadProgress($enProgressId = null)
    {
        if (!$this->enProgress) {
            return;
        }

        if ($enProgressId !== null && (int) $enProgressId !== (int) $this->enProgress->id) {
            return;
        }

        $this->enProgress = EnProgress::query()
            ->find($this->enProgress->id);
    }


    public function render()
    {
        return view('livewire.admin.progress.en.contract-terms');
    }
}

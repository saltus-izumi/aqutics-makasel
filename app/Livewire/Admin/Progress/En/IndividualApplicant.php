<?php

namespace App\Livewire\Admin\Progress\En;

use App\Models\EnProgressFile;
use App\Models\EnProgress;
use App\Models\EnProgressIndividualApplicant;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class IndividualApplicant extends Component
{
    use WithFileUploads;

    public $enProgress = null;
    public $enProgressIndividualApplicant = null;
    public $latestGeProgress = null;
    public array $originalContractFileKinds = [];
    public array $fileUrls = [];

    protected $listeners = ['enProgressUpdated' => 'reloadProgress'];
    protected array $contractTermFieldConfig = [
        'last_name' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'first_name' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'last_kana' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'first_kana' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'gender' => ['rules' => ['nullable', 'integer'], 'type' => 'integer'],
        'birth_date' => ['rules' => ['nullable', 'date'], 'type' => 'date'],
        'spouse_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
        'mobile_phone_number' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'email' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'residence_type' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'residence_years' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'move_reason' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'moving_guidance' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'occupation' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'workplace_name' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'workplace_kana' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'workplace_phone_number' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'industry' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'years_of_service' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'annual_income' => ['rules' => ['nullable', 'regex:/^$|^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'], 'type' => 'integer'],
    ];

    public function mount($enProgress)
    {
        $this->enProgress = $enProgress;
        $this->enProgressIndividualApplicant = $enProgress->enProgressIndividualApplicant;
        $this->latestGeProgress = $enProgress->progress?->latestGeProgress;
        $this->originalContractFileKinds = $this->getOriginalContractFileKinds();
        $this->loadOriginalContractFileUrls();
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

        $applicant = $this->enProgressIndividualApplicant;
        if (!$applicant) {
            $applicant = new EnProgressIndividualApplicant();
            $applicant->en_progress_id = $this->enProgress->id;
        }

        $applicant->{$fieldName} = $normalizedValue;
        $applicant->save();

        $this->enProgressIndividualApplicant = $applicant;
        $this->enProgress->setRelation('enProgressIndividualApplicant', $applicant);

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

    public function updatedFileUrls($value, $fileKind): void
    {
        if (!$this->enProgress || !array_key_exists((int) $fileKind, $this->originalContractFileKinds)) {
            return;
        }

        $validator = Validator::make(
            ['file_url' => $value],
            ['file_url' => ['nullable', 'string', 'max:2048']]
        );
        if ($validator->fails()) {
            return;
        }

        $normalizedUrl = $this->normalizeString($value);
        $fileKind = (int) $fileKind;

        $file = EnProgressFile::query()
            ->where('en_progress_id', $this->enProgress->id)
            ->where('file_kind', $fileKind)
            ->first();

        if (!$file && $normalizedUrl === null) {
            $this->fileUrls[$fileKind] = '';
            return;
        }

        if (!$file) {
            $file = new EnProgressFile();
            $file->en_progress_id = $this->enProgress->id;
            $file->file_kind = $fileKind;
        }

        $file->file_url = $normalizedUrl;
        $file->save();

        $this->fileUrls[$fileKind] = $normalizedUrl ?? '';
        $this->dispatch('enProgressUpdated', enProgressId: $this->enProgress->id);
    }

    protected function getOriginalContractFileKinds(): array
    {
        return [
            EnProgressFile::FILE_KIND_REGISTRY_CERTIFICATE => '登記簿謄本(土地建物)',
            EnProgressFile::FILE_KIND_COST_BREAKDOWN => '諸費用明細書',
            EnProgressFile::FILE_KIND_RENTAL_CONTRACT => '賃貸借契約書（定期借家契約書）',
            EnProgressFile::FILE_KIND_IMPORTANT_EXPLANATION => '重要事項説明書',
            EnProgressFile::FILE_KIND_ELECTRONIC_CONTRACT_CONSENT => '電子契約承諾証明書',
            EnProgressFile::FILE_KIND_PARENTAL_CONSENT => '親権者同意書',
            EnProgressFile::FILE_KIND_GUARANTOR_PLEDGE => '連帯保証人確約書',
            EnProgressFile::FILE_KIND_DISPUTE_PREVENTION_ORDINANCE => '紛争防止条例',
            EnProgressFile::FILE_KIND_PRIVACY_POLICY => '個人情報取り扱い',
            EnProgressFile::FILE_KIND_MEMORANDUM => '覚書',
            EnProgressFile::FILE_KIND_SETTLEMENT_AGREEMENT => '示談書',
        ];
    }

    protected function loadOriginalContractFileUrls(): void
    {
        if (!$this->enProgress) {
            $this->fileUrls = [];
            return;
        }

        $fileKinds = array_map('intval', array_keys($this->originalContractFileKinds));
        $files = EnProgressFile::query()
            ->where('en_progress_id', $this->enProgress->id)
            ->whereIn('file_kind', $fileKinds)
            ->get()
            ->keyBy('file_kind');

        $fileUrls = [];
        foreach ($fileKinds as $fileKind) {
            $fileUrls[$fileKind] = (string) ($files->get($fileKind)?->file_url ?? '');
        }

        $this->fileUrls = $fileUrls;
    }

    public function updateMoveOutReportDate(): void
    {
        if ($this->enProgress->move_out_report_date) {
            return;
        }

        $this->enProgress->move_out_report_date = now();
        $this->enProgress->save();
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
            ->with('enProgressIndividualApplicant')
            ->find($this->enProgress->id);
        $this->enProgressIndividualApplicant = $this->enProgress?->enProgressIndividualApplicant;
        $this->loadOriginalContractFileUrls();
    }


    public function render()
    {
        return view('livewire.admin.progress.en.individual-applicant');
    }
}

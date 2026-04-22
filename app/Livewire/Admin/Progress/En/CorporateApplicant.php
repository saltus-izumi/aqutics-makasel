<?php

namespace App\Livewire\Admin\Progress\En;

use App\Models\EnProgressFile;
use App\Models\EnProgress;
use App\Models\EnProgressCorporateApplicant;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class CorporateApplicant extends Component
{
    use WithFileUploads;

    public $enProgress = null;
    public $enProgressCorporateApplicant = null;
    public $latestGeProgress = null;
    public array $originalContractFileKinds = [];
    public array $creditScreeningFileSections = [];
    public array $guaranteeRiskTransferFileSections = [];
    public array $propertyHandoverFileKinds = [];
    public array $fileUrls = [];

    protected $listeners = ['enProgressUpdated' => 'reloadProgress'];
    protected array $contractTermFieldConfig = [
        'company_name' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'company_kana' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'head_office_phone_number' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'head_office_fax_number' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'email' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'industry' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'capital' => ['rules' => ['nullable', 'regex:/^$|^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'], 'type' => 'integer'],
        'number_of_employees' => ['rules' => ['nullable', 'regex:/^$|^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'], 'type' => 'integer'],
        'established_date' => ['rules' => ['nullable', 'date'], 'type' => 'date'],
        'representative_last_name' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'representative_first_name' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'representative_last_kana' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'representative_first_kana' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'contact_last_name' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'contact_first_name' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'contact_last_kana' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'contact_first_kana' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'contact_department' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'contact_phone_number' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
    ];

    public function mount($enProgress)
    {
        $this->enProgress = $enProgress;
        $this->enProgressCorporateApplicant = $enProgress->enProgressCorporateApplicant;
        $this->latestGeProgress = $enProgress->progress?->latestGeProgress;
        $this->originalContractFileKinds = $this->getOriginalContractFileKinds();
        $this->creditScreeningFileSections = $this->getCreditScreeningFileSections();
        $this->guaranteeRiskTransferFileSections = $this->getGuaranteeRiskTransferFileSections();
        $this->propertyHandoverFileKinds = $this->getPropertyHandoverFileKinds();
        $this->loadManagedFileUrls();
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

        $applicant = $this->enProgressCorporateApplicant;
        if (!$applicant) {
            $applicant = new EnProgressCorporateApplicant();
            $applicant->en_progress_id = $this->enProgress->id;
        }

        $applicant->{$fieldName} = $normalizedValue;
        $applicant->save();

        $this->enProgressCorporateApplicant = $applicant;
        $this->enProgress->setRelation('enProgressCorporateApplicant', $applicant);

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
        if (!$this->enProgress || !array_key_exists((int) $fileKind, $this->getManagedFileKinds())) {
            return;
        }

        $validator = Validator::make(
            ['file_url' => $value],
            ['file_url' => ['nullable', 'string', 'max:2048', 'regex:/^https?:\/\/.+$/i']]
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

    protected function getCreditScreeningFileSections(): array
    {
        return [
            [
                'title' => '■標準',
                'items' => [
                    EnProgressFile::FILE_KIND_APPLICATION_FORM => '入居申込書',
                    EnProgressFile::FILE_KIND_RESIDENT_RECORD => '住民票（入居者全員）',
                    EnProgressFile::FILE_KIND_IDENTITY_DOCUMENT => '身分証明書',
                    EnProgressFile::FILE_KIND_PROFILE_PHOTO => '顔写真',
                    EnProgressFile::FILE_KIND_SALARY_STATEMENT => '給与明細（収入証明）',
                ],
            ],
            [
                'title' => '■外国籍',
                'items' => [
                    EnProgressFile::FILE_KIND_PASSPORT_COPY => 'パスポート写し',
                    EnProgressFile::FILE_KIND_RESIDENCE_CARD_COPY => '在留カード写し',
                ],
            ],
            [
                'title' => '■法人',
                'items' => [
                    EnProgressFile::FILE_KIND_COMPANY_REGISTRY => '法人全部事項証明書',
                    EnProgressFile::FILE_KIND_COMPANY_SEAL_CERTIFICATE => '法人印鑑証明書',
                    EnProgressFile::FILE_KIND_FINANCIAL_STATEMENTS => '決算報告書（3期分）',
                    EnProgressFile::FILE_KIND_TAX_CERTIFICATE => '納税証明書（その1、その2）',
                    EnProgressFile::FILE_KIND_EMPLOYEE_CERTIFICATE => '従業者証明書',
                    EnProgressFile::FILE_KIND_COMPANY_PROFILE => '会社概要',
                ],
            ],
            [
                'title' => '■連帯保証人',
                'items' => [
                    EnProgressFile::FILE_KIND_GUARANTOR_INCOME_PROOF => '収入証明書',
                    EnProgressFile::FILE_KIND_GUARANTOR_RESIDENT_RECORD => '住民票（入居者全員）',
                    EnProgressFile::FILE_KIND_GUARANTOR_IDENTITY_DOCUMENT => '身分証明書',
                ],
            ],
        ];
    }

    protected function getCreditScreeningFileKinds(): array
    {
        $fileKinds = [];

        foreach ($this->creditScreeningFileSections as $section) {
            $fileKinds += $section['items'] ?? [];
        }

        return $fileKinds;
    }

    protected function getManagedFileKinds(): array
    {
        return $this->originalContractFileKinds
            + $this->getCreditScreeningFileKinds()
            + $this->getGuaranteeRiskTransferFileKinds()
            + $this->propertyHandoverFileKinds;
    }

    protected function getGuaranteeRiskTransferFileSections(): array
    {
        return [
            [
                'title' => '■保証会社',
                'items' => [
                    EnProgressFile::FILE_KIND_APPROVAL_NOTICE => '承認通知書',
                    EnProgressFile::FILE_KIND_GUARANTEE_CONTRACT => '保証委託契約書',
                ],
            ],
            [
                'title' => '■火災保険',
                'items' => [
                    EnProgressFile::FILE_KIND_FIRE_INSURANCE_GUIDE => '火災保険のご案内',
                    EnProgressFile::FILE_KIND_CONTRACT_CONFIRMATION => '契約内容確認書',
                ],
            ],
        ];
    }

    protected function getGuaranteeRiskTransferFileKinds(): array
    {
        $fileKinds = [];

        foreach ($this->guaranteeRiskTransferFileSections as $section) {
            $fileKinds += $section['items'] ?? [];
        }

        return $fileKinds;
    }

    protected function getPropertyHandoverFileKinds(): array
    {
        return [
            EnProgressFile::FILE_KIND_KEY_RECEIPT => '鍵預かり書',
            EnProgressFile::FILE_KIND_MOVE_IN_CHECKLIST => '入居時チェックシート',
            EnProgressFile::FILE_KIND_RESTORE_MEMO => '原復メモ',
        ];
    }

    protected function loadManagedFileUrls(): void
    {
        if (!$this->enProgress) {
            $this->fileUrls = [];
            return;
        }

        $fileKinds = array_map('intval', array_keys($this->getManagedFileKinds()));
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
            ->with('enProgressCorporateApplicant')
            ->find($this->enProgress->id);
        $this->enProgressCorporateApplicant = $this->enProgress?->enProgressCorporateApplicant;
        $this->loadManagedFileUrls();
    }


    public function render()
    {
        return view('livewire.admin.progress.en.corporate-applicant');
    }
}

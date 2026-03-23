<?php

namespace App\Livewire\Admin\Progress\En;

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
    }


    public function render()
    {
        return view('livewire.admin.progress.en.corporate-applicant');
    }
}

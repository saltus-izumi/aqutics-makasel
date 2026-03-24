<?php

namespace App\Livewire\Admin\Progress\En;

use App\Models\EnProgress;
use App\Models\GuaranteeCompany;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class GuaranteeCompanyScreening extends Component
{
    use WithFileUploads;

    public $enProgress = null;
    public $latestGeProgress = null;
    public $guaranteeCompanyOptions = [];

    protected $listeners = ['enProgressUpdated' => 'reloadProgress'];
    protected array $contractTermFieldConfig = [
        'guarantee_company_id' => ['rules' => ['nullable'], 'type' => 'string'],
        'screening_application_date' => ['rules' => ['nullable', 'date'], 'type' => 'date'],
        'screening_result' => ['rules' => ['nullable', 'integer'], 'type' => 'integer'],
        'approval_number' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'approval_guarantee_company_plan' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'guarantor_fee_burden' => ['rules' => ['nullable', 'regex:/^$|^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'], 'type' => 'integer'],
        'condition_summary' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'approval_notice_url' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
    ];

    public function mount($enProgress)
    {
        $this->enProgress = $enProgress;
        $this->latestGeProgress = $enProgress->progress?->latestGeProgress;
        $this->guaranteeCompanyOptions = GuaranteeCompany::query()
            ->orderBy('id')
            ->pluck('company_name', 'id')
            ->toArray();
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
        return view('livewire.admin.progress.en.guarantee-company-screening');
    }
}

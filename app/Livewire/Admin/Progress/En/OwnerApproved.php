<?php

namespace App\Livewire\Admin\Progress\En;

use App\Models\EnProgress;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class OwnerApproved extends Component
{
    use WithFileUploads;

    public $enProgress = null;

    protected $listeners = ['enProgressUpdated' => 'reloadProgress'];
    protected array $contractTermFieldConfig = [
        'identity_verification_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
        'condition_match_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
        'antisocial_check_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
        'special_agreement_note_flag' => ['rules' => ['boolean'], 'type' => 'boolean'],
        'risk_category' => ['rules' => ['nullable', 'integer'], 'type' => 'integer'],
        'escalation_flag' => ['rules' => ['nullable', 'integer', 'in:0,1'], 'type' => 'integer'],
        'wp_approver_id' => ['rules' => ['nullable', 'integer'], 'type' => 'integer'],
        'wp_screening_memo' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
    ];

    public function mount($enProgress)
    {
        $this->enProgress = $enProgress;
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
        return view('livewire.admin.progress.en.owner-approved');
    }
}

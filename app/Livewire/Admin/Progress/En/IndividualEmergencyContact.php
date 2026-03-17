<?php

namespace App\Livewire\Admin\Progress\En;

use App\Models\EnProgress;
use App\Models\EnProgressEmergencyContact;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class IndividualEmergencyContact extends Component
{
    use WithFileUploads;

    public $enProgress = null;
    public $enProgressEmergencyContact = null;
    public $latestGeProgress = null;

    protected $listeners = ['enProgressUpdated' => 'reloadProgress'];
    protected array $contractTermFieldConfig = [
        'last_name' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'first_name' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'last_kana' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'first_kana' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'gender' => ['rules' => ['nullable', 'integer'], 'type' => 'integer'],
        'birth_date' => ['rules' => ['nullable', 'date'], 'type' => 'date'],
        'relationship' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'postal_code' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'prefecture' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'city' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'street' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'building' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'phone_number' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'mobile_phone_number' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'workplace_or_school_name' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'workplace_or_school_kana' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'workplace_or_school_phone_number' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
    ];

    public function mount($enProgress)
    {
        $this->enProgress = $enProgress;
        $this->enProgressEmergencyContact = $enProgress->enProgressEmergencyContact;
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

        $emergencyContact = $this->enProgressEmergencyContact;
        if (!$emergencyContact) {
            $emergencyContact = new EnProgressEmergencyContact();
            $emergencyContact->en_progress_id = $this->enProgress->id;
        }

        $emergencyContact->{$fieldName} = $normalizedValue;
        $emergencyContact->save();

        $this->enProgressEmergencyContact = $emergencyContact;
        $this->enProgress->setRelation('enProgressEmergencyContact', $emergencyContact);

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
            ->with('enProgressEmergencyContact')
            ->find($this->enProgress->id);
        $this->enProgressEmergencyContact = $this->enProgress?->enProgressEmergencyContact;
    }


    public function render()
    {
        return view('livewire.admin.progress.en.individual-emergency-contact');
    }
}

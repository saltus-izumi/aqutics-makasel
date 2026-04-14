<?php

namespace App\Livewire\Admin\Progress\En;

use App\Models\EnProgress;
use App\Models\EnProgressOccupant;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class IndividualOccupant extends Component
{
    use WithFileUploads;

    public $enProgress = null;
    public $enProgressOccupants = null;
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
        'mobile_phone_number' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'workplace_or_school_name' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'workplace_or_school_kana' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
    ];

    public function mount($enProgress)
    {
        $this->enProgress = $enProgress;
        $this->enProgressOccupants = $enProgress->enProgressOccupants
            ?->sortBy('occupant_seq')
            ->values() ?? collect();
    }

    public function saveFieldByName(int $occupantId, string $fieldName, $value): void
    {
        if (!$this->enProgress || !$occupantId || !array_key_exists($fieldName, $this->contractTermFieldConfig)) {
            return;
        }

        $occupant = EnProgressOccupant::query()
            ->where('id', $occupantId)
            ->where('en_progress_id', $this->enProgress->id)
            ->first();
        if (!$occupant) {
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

        $occupant->{$fieldName} = $normalizedValue;
        $occupant->save();

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

    public function addOccupant(): void
    {
        if (!$this->enProgress) {
            return;
        }

        $nextOccupantSeq = (int) EnProgressOccupant::query()
            ->where('en_progress_id', $this->enProgress->id)
            ->max('occupant_seq') + 1;

        EnProgressOccupant::query()->create([
            'en_progress_id' => $this->enProgress->id,
            'occupant_seq' => $nextOccupantSeq,
        ]);

        $this->reloadProgress($this->enProgress->id);
    }

    public function removeOccupant(int $occupantId): void
    {
        if (!$this->enProgress || !$occupantId) {
            return;
        }

        $occupant = EnProgressOccupant::query()
            ->where('id', $occupantId)
            ->where('en_progress_id', $this->enProgress->id)
            ->first();
        if (!$occupant) {
            return;
        }

        $occupant->delete();
        $this->reloadProgress($this->enProgress->id);
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
            ->with('enProgressOccupants')
            ->find($this->enProgress->id);
        $this->enProgressOccupants = $this->enProgress?->enProgressOccupants
            ?->sortBy('occupant_seq')
            ->values() ?? collect();
    }


    public function render()
    {
        return view('livewire.admin.progress.en.individual-occupant');
    }
}

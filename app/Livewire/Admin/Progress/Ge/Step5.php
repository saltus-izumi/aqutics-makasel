<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\GeProgress;
use App\Models\GeProgressFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Step5 extends Component
{
    use WithFileUploads;

    public $geProgress = null;
    public $isProperWorkBurden;
    public $isProperPrice;
    public $correctionInstructionMessage;
    public $estimateNoteMessage;

    public string $componentId = '';

    protected $listeners = ['geProgressUpdated' => 'reloadProgress'];
    protected array $geProgressMap = [
        'isProperWorkBurden' => 'is_proper_work_burden',
        'isProperPrice' => 'is_proper_price',
        'correctionInstructionMessage' => 'correction_instruction_message',
        'estimateNoteMessage' => 'estimate_note_message',
    ];
    protected function rules(): array
    {
        return [
            'isProperWorkBurden' => ['nullable'],
            'isProperPrice' => ['nullable'],
            'correctionInstructionMessage' => ['nullable', 'string'],
            'estimateNoteMessage' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
        ];
    }

    public function mount($geProgress)
    {
        $this->geProgress = $geProgress;
        $this->isProperWorkBurden = $geProgress?->is_proper_work_burden;
        $this->isProperPrice = $geProgress?->is_proper_price;
        $this->correctionInstructionMessage = $geProgress?->correction_instruction_message;
        $this->estimateNoteMessage = $geProgress?->estimate_note_message;
        $this->componentId = $this->getId();
    }

    public function updated($propertyName, $value)
    {
        if (!array_key_exists($propertyName, $this->geProgressMap)) {
            return;
        }

        // null対策
        if (!$this->geProgress) {
            return;
        }

        $this->validateOnly($propertyName);

        $value = trim($value) ? trim($value) : null;

        $column = $this->geProgressMap[$propertyName];
        $this->geProgress->{$column} = $value;
        $this->geProgress->save();

        $this->dispatch('geProgressUpdated', geProgressId: $this->geProgress->id);
    }

    public function reloadProgress($geProgressId = null)
    {
        if (!$this->geProgress) {
            return;
        }

        if ($geProgressId !== null && (int) $geProgressId !== (int) $this->geProgress->id) {
            return;
        }

        $this->geProgress = GeProgress::query()
            ->find($this->geProgress->id);
    }

    public function render()
    {
        return view('livewire.admin.progress.ge.step5');
    }
}

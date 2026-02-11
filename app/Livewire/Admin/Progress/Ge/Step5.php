<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\GeProgressFile;
use App\Models\Progress;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Step5 extends Component
{
    use WithFileUploads;

    public $progress = null;
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

    public function mount($progress)
    {
        $this->progress = $progress;
        $this->isProperWorkBurden = $progress->geProgress?->is_proper_work_burden;
        $this->isProperPrice = $progress->geProgress?->is_proper_price;
        $this->correctionInstructionMessage = $progress->geProgress?->correction_instruction_message;
        $this->estimateNoteMessage = $progress->geProgress?->estimate_note_message;
        $this->componentId = $this->getId();
    }

    public function updated($propertyName, $value)
    {
        if (!array_key_exists($propertyName, $this->geProgressMap)) {
            return;
        }

        // null対策
        if (!$this->progress?->geProgress) {
            return;
        }

        $this->validateOnly($propertyName);

        $value = trim($value) ? trim($value) : null;

        $column = $this->geProgressMap[$propertyName];
        $this->progress->geProgress->{$column} = $value;
        $this->progress->geProgress->save();

        $this->dispatch('geProgressUpdated', progressId: $this->progress->id);
    }

    public function reloadProgress($progressId = null)
    {
        if (!$this->progress) {
            return;
        }

        if ($progressId !== null && (int) $progressId !== (int) $this->progress->id) {
            return;
        }

        $this->progress = Progress::query()
            ->with('geProgress')
            ->find($this->progress->id);
    }

    public function render()
    {
        return view('livewire.admin.progress.ge.step5');
    }
}

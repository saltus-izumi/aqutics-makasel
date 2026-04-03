<?php

namespace App\Livewire\Admin\Progress\Te;

use App\Models\TeProgress;
use Livewire\Component;
use Livewire\WithFileUploads;

class HardStep2 extends Component
{
    use WithFileUploads;

    public $teProgress = null;
    public $isProperWorkBurden;
    public $isProperPrice;
    public $correctionInstructionMessage;
    public $estimateNoteMessage;
    public $pcStatusRemarks;
    public $statusRemarks;

    public string $componentId = '';

    protected $listeners = ['teProgressUpdated' => 'reloadProgress'];
    protected array $teProgressMap = [
        'isProperWorkBurden' => 'is_proper_work_burden',
        'isProperPrice' => 'is_proper_price',
        'correctionInstructionMessage' => 'correction_instruction_message',
        'estimateNoteMessage' => 'estimate_note_message',
        'pcStatusRemarks' => 'pc_status_remarks',
        'statusRemarks' => 'status_remarks',
    ];
    protected function rules(): array
    {
        return [
            'isProperWorkBurden' => ['nullable'],
            'isProperPrice' => ['nullable'],
            'correctionInstructionMessage' => ['nullable', 'string'],
            'estimateNoteMessage' => ['nullable', 'string'],
            'pcStatusRemarks' => ['nullable', 'string'],
            'statusRemarks' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
        ];
    }

    public function mount($teProgress)
    {
        $this->teProgress = $teProgress;
        $this->isProperWorkBurden = $teProgress?->is_proper_work_burden;
        $this->isProperPrice = $teProgress?->is_proper_price;
        $this->correctionInstructionMessage = $teProgress?->correction_instruction_message;
        $this->estimateNoteMessage = $teProgress?->estimate_note_message;
        $this->pcStatusRemarks = $teProgress?->pc_status_remarks;
        $this->statusRemarks = $teProgress?->status_remarks;
        $this->componentId = $this->getId();
    }

    public function updated($propertyName, $value)
    {
        if (!array_key_exists($propertyName, $this->teProgressMap)) {
            return;
        }

        // null対策
        if (!$this->teProgress) {
            return;
        }

        $this->validateOnly($propertyName);

        $value = trim($value) ? trim($value) : null;

        $column = $this->teProgressMap[$propertyName];
        $this->teProgress->{$column} = $value;
        $this->teProgress->save();

        $this->dispatch('teProgressUpdated', teProgressId: $this->teProgress->id);
    }

    public function reloadProgress($teProgressId = null)
    {
        if (!$this->teProgress) {
            return;
        }

        if ($teProgressId !== null && (int) $teProgressId !== (int) $this->teProgress->id) {
            return;
        }

        $this->teProgress = TeProgress::query()
            ->find($this->teProgress->id);
    }

    public function render()
    {
        return view('livewire.admin.progress.te.hard-step2');
    }
}

<?php

namespace App\Livewire\Admin\Progress\Ge;

use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Step1 extends Component
{
    public $progress = null;
    public $securityDepositAmount = null;
    public $proratedRentAmount = null;
    public $penaltyForfeitureAmount = null;
    public $inspectionRequestMessage = null;
    protected array $geProgressMap = [
        'securityDepositAmount' => 'security_deposit_amount',
        'proratedRentAmount' => 'prorated_rent_amount',
        'penaltyForfeitureAmount' => 'penalty_forfeiture_amount',
        'inspectionRequestMessage' => 'inspection_request_message',
    ];
    protected function rules(): array
    {
        return [
            'securityDepositAmount' => ['nullable', 'regex:/^[0-9,]+$/'],
            'proratedRentAmount' => ['nullable', 'regex:/^[0-9,]+$/'],
            'penaltyForfeitureAmount' => ['nullable', 'regex:/^[0-9,]+$/'],
            'inspectionRequestMessage' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'securityDepositAmount.regex' => '敷金預託等は半角数字とカンマで入力してください。',
            'proratedRentAmount.regex' => '日割り家賃は半角数字とカンマで入力してください。',
            'penaltyForfeitureAmount.regex' => '違約金（償却）は半角数字とカンマで入力してください。',
        ];
    }

    public function mount($progress)
    {
        $this->progress = $progress;
        $this->securityDepositAmount = $progress->geProgress?->security_deposit_amount;
        $this->proratedRentAmount = $progress->geProgress?->prorated_rent_amount;
        $this->penaltyForfeitureAmount = $progress->geProgress?->penalty_forfeiture_amount;
        $this->inspectionRequestMessage = $progress->geProgress?->inspection_request_message;
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

        switch($propertyName) {
            case 'securityDepositAmount':
            case 'proratedRentAmount':
            case 'penaltyForfeitureAmount':
                $value = str_replace(',', '', (string) $value);
                break;
        }

        $value = $value ? $value : null;

        $column = $this->geProgressMap[$propertyName];
        $this->progress->geProgress->{$column} = $value;
        $this->progress->geProgress->save();

        $this->dispatch('geProgressUpdated', progressId: $this->progress->id);
    }

    public function render()
    {
        return view('livewire.admin.progress.ge.step1');
    }
}

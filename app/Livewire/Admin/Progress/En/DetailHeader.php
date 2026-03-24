<?php

namespace App\Livewire\Admin\Progress\En;

use App\Models\EnProgress;
use App\Models\TradingCompany;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DetailHeader extends Component
{
    public $enProgress = null;
    public array $averageLt = [];
    public $mode = 'contract';
    public $tradingCompanyId = null;
    public $cancellationReason = null;
    public $restorationCompanies = [];

    protected array $enProgressMap = [
        'tradingCompanyId' => 'trading_company_id',
    ];

    protected array $progressMap = [
        'trading_company_id' => 'genpuku_gyousha_id'
    ];

    protected function rules(): array
    {
        return [
            'tradingCompanyId' => ['nullable'],
        ];
    }

    protected function messages(): array
    {
        return [
        ];
    }

    public function mount()
    {
        $companies = TradingCompany::query()
            ->where('kind', TradingCompany::KIND_RESTORATION)
            ->get();

        foreach ($companies as $company) {
            $option = $company->name;
            if ($company->personnel1 || $company->personnel2 || $company->personnel3) {
                $option .= '（担当：' . ($company->personnel1 ?: $company->personnel2 ?: $company->personnel3) . '）';
            }
            $this->restorationCompanies[$company->id] = $option;
        }
        $this->tradingCompanyId = $this->enProgress->trading_company_id ?? $this->enProgress->progress->investment?->restoration_company_id;
// dump($this->tradingCompanyId);
// dump($this->restorationCompanies);

        $this->buildAverageLt();
    }

    protected function buildAverageLt(): void
    {
        $pairs = [
            'cost_received' => ['move_out_date', 'cost_received_date'],
            'owner_proposed' => ['cost_received_date', 'owner_proposed_date'],
            'owner_approved' => ['owner_proposed_date', 'owner_approved_date'],
            'ordered' => ['owner_approved_date', 'ordered_date'],
            'completion_received' => ['ordered_date', 'completion_received_date'],
        ];

        foreach ($pairs as $key => [$startPath, $endPath]) {
            $start = $this->enProgress->{$startPath};
            $end = $this->enProgress->{$endPath};
            if (!$start || !$end) {
                $this->averageLt[$key] = '';
            }
            else {
                $this->averageLt[$key] = $start->diffInDays($end);
            }
        }
    }

    protected function isReProposeOrCancelLocked(): bool
    {
        return in_array($this->enProgress?->next_action, [
            EnProgress::NEXT_ACTION_CANCEL,
        ], true);
    }

    public function updated($propertyName, $value)
    {
        if (!array_key_exists($propertyName, $this->enProgressMap)) {
            return;
        }

        // null対策
        if (!$this->enProgress) {
            return;
        }

        $this->validateOnly($propertyName);

        if (is_string($value)) {
            $value = trim($value) !== '' ? trim($value) : null;
        }
        DB::transaction(function () use ($propertyName, $value) {
            $column = $this->enProgressMap[$propertyName];
            $this->enProgress->{$column} = $value;
            $this->enProgress->save();

            if (array_key_exists($column, $this->progressMap)) {
                $progressColumn = $this->progressMap[$column];
                $this->enProgress->progress->{$progressColumn} = $value;
                $this->enProgress->progress->save();
            }
        });

        $this->dispatch('enProgressUpdated', progressId: $this->enProgress->id);
    }

    public function showCancelModal()
    {
        if ($this->isReProposeOrCancelLocked()) {
            return;
        }

        $this->cancellationReason = null;
        $this->resetValidation('cancellationReason');
        $this->dispatch('open-cancel-progress-modal');
    }

    public function cancelProgress()
    {
        if ($this->isReProposeOrCancelLocked()) {
            return;
        }

        if (is_string($this->cancellationReason)) {
            $this->cancellationReason = trim($this->cancellationReason);
        }

        $this->validate([
            'cancellationReason' => ['required', 'string'],
        ], [
            'cancellationReason.required' => 'キャンセル理由は必須です。',
        ]);

        DB::transaction(function () {
            $this->enProgress->cancellation_reason = $this->cancellationReason;
            $this->enProgress->cancellation_date = now();
            $this->enProgress->cancellation_date_state = 1;
            $this->enProgress->next_action = EnProgress::NEXT_ACTION_CANCEL;
            $this->enProgress->save();
        });

        $this->dispatch('close-cancel-progress-modal');
        $this->dispatch('enProgressUpdated', enProgressId: $this->enProgress->id);
    }

    public function render()
    {
        return view('livewire.admin.progress.en.detail-header');
    }
}

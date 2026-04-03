<?php

namespace App\Livewire\Admin\Progress\Te;

use App\Models\GeProgress;
use App\Models\GeProgressFile;
use App\Models\TradingCompany;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DetailHeader extends Component
{
    public $teProgress = null;
    public array $averageLt = [];
    public $mode = 'move-out-settlement';
    public $tradingCompanyId = null;
    public $restorationCompanies = [];

    protected array $teProgressMap = [
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
        $this->tradingCompanyId = $this->teProgress->trading_company_id ?? $this->teProgress->investment?->restoration_company_id;
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
            $start = $this->teProgress->{$startPath};
            $end = $this->teProgress->{$endPath};
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
        return in_array($this->teProgress?->next_action, [
            GeProgress::NEXT_ACTION_RE_PROPOSED,
            GeProgress::NEXT_ACTION_CANCEL,
        ], true);
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

        if (is_string($value)) {
            $value = trim($value) !== '' ? trim($value) : null;
        }
        DB::transaction(function () use ($propertyName, $value) {
            $column = $this->teProgressMap[$propertyName];
            $this->teProgress->{$column} = $value;
            $this->teProgress->save();

            if (array_key_exists($column, $this->progressMap)) {
                $progressColumn = $this->progressMap[$column];
                $this->teProgress->{$progressColumn} = $value;
                $this->teProgress->save();
            }
        });

        $this->dispatch('teProgressUpdated', progressId: $this->teProgress->id);
    }

    public function rePropose()
    {
        if ($this->isReProposeOrCancelLocked()) {
            return;
        }

        DB::transaction(function () {
            // 現在のプロセス管理データを修正
            $this->teProgress->next_action = GeProgress::NEXT_ACTION_RE_PROPOSED;
            $this->teProgress->save();

            // 再提案データ作成
            $teProgress = new GeProgress([
                'progress_id' => $this->teProgress_id,
                'reproposal_count' => $this->teProgress->reproposal_count + 1,
                'responsible_user_id' => $this->teProgress->responsible_user_id,
                'executor_user_id' => $this->teProgress->executor_user_id,
                'trading_company_id' => $this->teProgress->trading_company_id,
                'move_out_received_date' => $this->teProgress->move_out_received_date,
                'move_out_date' => $this->teProgress->move_out_date,
                'security_deposit_amount' => $this->teProgress->security_deposit_amount,
                'prorated_rent_amount' => $this->teProgress->prorated_rent_amount,
                'penalty_forfeiture_amount' => $this->teProgress->penalty_forfeiture_amount,
                'inspection_request_message' => $this->teProgress->inspection_request_message,
                'step1_confirmed' => $this->teProgress->step1_confirmed,
                'move_out_report_date' => $this->teProgress->move_out_report_date,
            ]);
            $teProgress->resetNextAction();
            $teProgress->save();

            foreach ($this->teProgress->step1Files as $step1File) {
                $newFile = new GeProgressFile($step1File->toArray());
                $newFile->ge_progress_id = $teProgress->id;
                $newFile->save();
            }

            foreach ($this->teProgress->moveOutSettlementFiles as $moveOutSettlementFile) {
                $newFile = new GeProgressFile($moveOutSettlementFile->toArray());
                $newFile->ge_progress_id = $teProgress->id;
                $newFile->save();
            }

        });
    }

    public function cancelProgress()
    {
        if ($this->isReProposeOrCancelLocked()) {
            return;
        }

        DB::transaction(function () {
            $this->teProgress->kaiyaku_cancellation_date = now();
            $this->teProgress->save();

            $this->teProgress->teProgress->next_action = GeProgress::NEXT_ACTION_CANCEL;
            $this->teProgress->teProgress->save();
        });
    }

    public function render()
    {
        return view('livewire.admin.progress.te.detail-header');
    }
}

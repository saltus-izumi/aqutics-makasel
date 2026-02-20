<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\GeProgress;
use App\Models\GeProgressFile;
use App\Models\TradingCompany;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DetailHeader extends Component
{
    public $geProgress = null;
    public array $averageLt = [];
    public $mode = 'move-out-settlement';
    public $tradingCompanyId = null;
    public $restorationCompanies = [];

    protected array $geProgressMap = [
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
        $this->tradingCompanyId = $this->geProgress->trading_company_id ?? $this->geProgress->progress->investment?->restoration_company_id;
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
            $start = $this->geProgress->{$startPath};
            $end = $this->geProgress->{$endPath};
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
        return in_array($this->geProgress?->next_action, [
            GeProgress::NEXT_ACTION_RE_PROPOSED,
            GeProgress::NEXT_ACTION_CANCEL,
        ], true);
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

        if (is_string($value)) {
            $value = trim($value) !== '' ? trim($value) : null;
        }
        DB::transaction(function () use ($propertyName, $value) {
            $column = $this->geProgressMap[$propertyName];
            $this->geProgress->{$column} = $value;
            $this->geProgress->save();

            if (array_key_exists($column, $this->progressMap)) {
                $progressColumn = $this->progressMap[$column];
                $this->geProgress->progress->{$progressColumn} = $value;
                $this->geProgress->progress->save();
            }
        });

        $this->dispatch('geProgressUpdated', progressId: $this->geProgress->id);
    }

    public function rePropose()
    {
        if ($this->isReProposeOrCancelLocked()) {
            return;
        }

        DB::transaction(function () {
            // 現在のプロセス管理データを修正
            $this->geProgress->next_action = GeProgress::NEXT_ACTION_RE_PROPOSED;
            $this->geProgress->save();

            // 再提案データ作成
            $geProgress = new GeProgress([
                'progress_id' => $this->geProgress->progress_id,
                'reproposal_count' => $this->geProgress->reproposal_count + 1,
                'responsible_user_id' => $this->geProgress->responsible_user_id,
                'executor_user_id' => $this->geProgress->executor_user_id,
                'trading_company_id' => $this->geProgress->trading_company_id,
                'move_out_received_date' => $this->geProgress->move_out_received_date,
                'move_out_date' => $this->geProgress->move_out_date,
                'security_deposit_amount' => $this->geProgress->security_deposit_amount,
                'prorated_rent_amount' => $this->geProgress->prorated_rent_amount,
                'penalty_forfeiture_amount' => $this->geProgress->penalty_forfeiture_amount,
                'inspection_request_message' => $this->geProgress->inspection_request_message,
                'step1_confirmed' => $this->geProgress->step1_confirmed,
                'move_out_report_date' => $this->geProgress->move_out_report_date,
            ]);
            $geProgress->resetNextAction();
            $geProgress->save();

            foreach ($this->geProgress->step1Files as $step1File) {
                $newFile = new GeProgressFile($step1File->toArray());
                $newFile->ge_progress_id = $geProgress->id;
                $newFile->save();
            }

            foreach ($this->geProgress->moveOutSettlementFiles as $moveOutSettlementFile) {
                $newFile = new GeProgressFile($moveOutSettlementFile->toArray());
                $newFile->ge_progress_id = $geProgress->id;
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
            $this->geProgress->kaiyaku_cancellation_date = now();
            $this->geProgress->save();

            $this->geProgress->geProgress->next_action = GeProgress::NEXT_ACTION_CANCEL;
            $this->geProgress->geProgress->save();
        });
    }

    public function render()
    {
        return view('livewire.admin.progress.ge.detail-header');
    }
}

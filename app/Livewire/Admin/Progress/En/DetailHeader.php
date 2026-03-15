<?php

namespace App\Livewire\Admin\Progress\En;

use App\Models\GeProgress;
use App\Models\GeProgressFile;
use App\Models\TradingCompany;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DetailHeader extends Component
{
    public $enProgress = null;
    public array $averageLt = [];
    public $mode = 'move-out-settlement';
    public $tradingCompanyId = null;
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
            GeProgress::NEXT_ACTION_RE_PROPOSED,
            GeProgress::NEXT_ACTION_CANCEL,
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

    public function rePropose()
    {
        if ($this->isReProposeOrCancelLocked()) {
            return;
        }

        DB::transaction(function () {
            // 現在のプロセス管理データを修正
            $this->enProgress->next_action = GeProgress::NEXT_ACTION_RE_PROPOSED;
            $this->enProgress->save();

            // 再提案データ作成
            $enProgress = new GeProgress([
                'progress_id' => $this->enProgress->progress_id,
                'reproposal_count' => $this->enProgress->reproposal_count + 1,
                'responsible_user_id' => $this->enProgress->responsible_user_id,
                'executor_user_id' => $this->enProgress->executor_user_id,
                'trading_company_id' => $this->enProgress->trading_company_id,
                'move_out_received_date' => $this->enProgress->move_out_received_date,
                'move_out_date' => $this->enProgress->move_out_date,
                'security_deposit_amount' => $this->enProgress->security_deposit_amount,
                'prorated_rent_amount' => $this->enProgress->prorated_rent_amount,
                'penalty_forfeiture_amount' => $this->enProgress->penalty_forfeiture_amount,
                'inspection_request_message' => $this->enProgress->inspection_request_message,
                'step1_confirmed' => $this->enProgress->step1_confirmed,
                'move_out_report_date' => $this->enProgress->move_out_report_date,
            ]);
            $enProgress->resetNextAction();
            $enProgress->save();

            foreach ($this->enProgress->step1Files as $step1File) {
                $newFile = new GeProgressFile($step1File->toArray());
                $newFile->ge_progress_id = $enProgress->id;
                $newFile->save();
            }

            foreach ($this->enProgress->moveOutSettlementFiles as $moveOutSettlementFile) {
                $newFile = new GeProgressFile($moveOutSettlementFile->toArray());
                $newFile->ge_progress_id = $enProgress->id;
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
            $this->enProgress->kaiyaku_cancellation_date = now();
            $this->enProgress->save();

            $this->enProgress->enProgress->next_action = GeProgress::NEXT_ACTION_CANCEL;
            $this->enProgress->enProgress->save();
        });
    }

    public function render()
    {
        return view('livewire.admin.progress.en.detail-header');
    }
}

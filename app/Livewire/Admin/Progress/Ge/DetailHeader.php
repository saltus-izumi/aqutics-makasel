<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\GeProgress;
use App\Models\TradingCompany;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DetailHeader extends Component
{
    public $progress = null;
    public array $averageLt = [];
    public $mode = 'move-out-settlement';
    public $genpukuGyoushaId = null;
    public $restorationCompanies = [];

    protected array $progressMap = [
        'genpukuGyoushaId' => 'genpuku_gyousha_id',
    ];

    protected function rules(): array
    {
        return [
            'genpukuGyoushaId' => ['nullable'],
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
        $this->genpukuGyoushaId = $this->progress->genpuku_gyousha_id ?? $this->progress->investment?->restoration_company_id;
// dump($this->genpukuGyoushaId);
// dump($this->restorationCompanies);

        $this->buildAverageLt();
    }

    protected function buildAverageLt(): void
    {
        $pairs = [
            'genpuku_mitsumori_recieved' => ['taikyo_date', 'genpuku_mitsumori_recieved_date'],
            'genpuku_teian_date' => ['genpuku_mitsumori_recieved_date', 'genpuku_teian_date'],
            'genpuku_teian_kyodaku' => ['genpuku_teian_date', 'genpuku_teian_kyodaku_date'],
            'genpuku_kouji_hachu' => ['genpuku_teian_kyodaku_date', 'genpuku_kouji_hachu_date'],
            'kanko_jyushin_date' => ['genpuku_kouji_hachu_date', 'kanko_jyushin_date'],
        ];

        foreach ($pairs as $key => [$startPath, $endPath]) {
            $start = $this->progress->{$startPath};
            $end = $this->progress->{$endPath};
            if (!$start || !$end) {
                $this->averageLt[$key] = '';
            }
            else {
                $this->averageLt[$key] = $start->diffInDays($end);
            }
        }
    }

    public function updated($propertyName, $value)
    {
        if (!array_key_exists($propertyName, $this->progressMap)) {
            return;
        }

        // null対策
        if (!$this->progress?->geProgress) {
            return;
        }

        $this->validateOnly($propertyName);

        if (is_string($value)) {
            $value = trim($value) !== '' ? trim($value) : null;
        }

        $column = $this->progressMap[$propertyName];
        $this->progress->{$column} = $value;
        $this->progress->save();

        $this->dispatch('geProgressUpdated', progressId: $this->progress->id);
    }

    public function rePropose()
    {
        DB::transaction(function () {
            // 現在のプロセス管理データを修正
            $this->progress->geProgress->next_action = GeProgress::NEXT_ACTION_RE_PROPOSED;
            $this->progress->geProgress->save();




        });
    }

    public function cancelProgress()
    {
        DB::transaction(function () {
            $this->progress->kaiyaku_cancellation_date = now();
            $this->progress->save();

            $this->progress->geProgress->next_action = GeProgress::NEXT_ACTION_CANCEL;
            $this->progress->geProgress->save();
        });
    }

    public function render()
    {
        return view('livewire.admin.progress.ge.detail-header');
    }
}

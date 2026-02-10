<?php

namespace App\Livewire\Admin\Progress\Ge;

use Livewire\Component;

class DetailHeader extends Component
{
    public $progress = null;
    public array $averageLt = [];
    public $mode = 'move-out-settlement';

    public function mount()
    {
        $this->buildAverageLt($this->progress);
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

    public function render()
    {
        return view('livewire.admin.progress.ge.detail-header');
    }
}

<?php

namespace App\Livewire\Admin\Investment;

use App\Models\Investment;
use Livewire\Component;
use Livewire\WithPagination;

class InvestmentList extends Component
{
    use WithPagination;

    public $investmentSearchKeyword = '';
    public $isManagementActive = false;

    public function mount()
    {
    }

    public function updatedInvestmentSearchKeyword(): void
    {
        $this->resetPage();
    }

    public function updatedIsManagementActive(): void
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $searchKeyword = trim((string) $this->investmentSearchKeyword);

        $investments = Investment::query()
            ->with(['landlord.owner'])
            ->withCount('investmentRooms')
            ->when($searchKeyword !== '', function ($query) use ($searchKeyword) {
                $searchLike = '%' . $searchKeyword . '%';

                $query->where(function ($q) use ($searchKeyword, $searchLike) {
                    $q->where('investment_name', 'like', $searchLike)
                        ->orWhere('landlord_name', 'like', $searchLike)
                        ->orWhere('landlord_personal_name', 'like', $searchLike)
                        ->orWhereHas('landlord', function ($landlordQuery) use ($searchLike) {
                            $landlordQuery->where('name', 'like', $searchLike)
                                ->orWhereHas('owner', function ($ownerQuery) use ($searchLike) {
                                    $ownerQuery->where('name', 'like', $searchLike);
                                });
                        });

                    if (ctype_digit($searchKeyword)) {
                        $q->orWhere('id', (int) $searchKeyword);
                    }
                });
            })
            ->when((bool) $this->isManagementActive, function ($query) {
                $query->where('is_management_active', true);
            })
            ->orderBy('id')
            ->paginate(50);

        return view('livewire.admin.investment.investment-list', [
            'investments' => $investments,
        ]);
    }
}

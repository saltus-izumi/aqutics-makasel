<?php

namespace App\View\Components\Owner;

use App\Models\Investment;
use App\Models\Owner;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class AuthLayout extends Component
{

    public $investments;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $owner = Auth::guard('owner')->user();

        if ($owner) {
            $this->investments = Investment::query()
                ->with([
                    'investmentRooms',
                    'landlord',
                ])
                ->whereHas('landlord', function ($query) use ($owner) {
                    $query->where('owner_id', $owner->id);
                })
                ->get();
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('layouts.owner.auth-layout');
    }
}

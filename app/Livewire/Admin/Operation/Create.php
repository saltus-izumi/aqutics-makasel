<?php

namespace App\Livewire\Admin\Operation;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

use App\Models\Owner;
use App\Models\OperationKind;
use App\Models\Investment;

class Create extends Component
{
    public $operationKindOptions = [];
    public $ownerOptions = [];
    public $investmentOptions = [];

    public $owner_id = '';
    public $investment_id = '';

    public function mount()
    {
        $this->operationKindOptions = OperationKind::getGroupOptions();
        $this->ownerOptions = Owner::getOptions();
        $this->investmentOptions = [];
    }

    // オーナー選択時のイベントハンドラ（wire:model.liveから自動呼び出し）
    public function updatedOwnerId($value)
    {
        Log::info('updatedOwnerId called', ['value' => $value]);

        $this->investment_id = ''; // オーナー変更時は物件選択をリセット

        if ($value) {
            // オーナーに紐づく物件オプションを取得
            $this->investmentOptions = Investment::getOptionsByOwner($value);
        } else {
            $this->investmentOptions = [];
        }

        // Alpine.jsにオプション更新を通知
        // $this->dispatch('update-select-options', name: 'investment_id', options: $this->investmentOptions);
        $this->dispatch('select-search2-options',
            name: 'investment_id',
            options: $this->investmentOptions,
            value: '',
        );
    }

    // 物件選択時のイベントハンドラ
    public function updatedInvestmentId($value)
    {
        Log::info('updatedInvestmentId called', ['value' => $value]);
        // 必要に応じて追加処理を記述
    }

    public function render()
    {
        return view('livewire.admin.operation.create');
    }
}

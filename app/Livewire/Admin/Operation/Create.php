<?php

namespace App\Livewire\Admin\Operation;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

use App\Models\Owner;
use App\Models\OperationKind;
use App\Models\Investment;
use App\Models\InvestmentRoom;

class Create extends Component
{
    public $operationKindOptions = [];
    public $ownerOptions = [];
    public $investmentOptions = [];
    public $investmentRoomOptions = [];
    public $operationTemplateOptions = [];

    public $operation_kind_id = '';
    public $owner_id = '';
    public $investment_id = '';
    public $investment_room_id = '';
    public $operation_template_id = '';

    public function mount()
    {
        $this->operationKindOptions = OperationKind::getGroupOptions();
        $this->ownerOptions = Owner::getOptions();
        $this->investmentOptions = [];
        $this->investmentRoomOptions = [];
    }

    // オペレーション選択時のイベントハンドラ（wire:model.liveから自動呼び出し）
    public function updatedOperationKind($value)
    {
        Log::info('updatedOperationKind called', ['value' => $value]);

        $this->operation_template_id = ''; // オペレーション変更時はカテゴリ（テンプレート）選択をリセット

        if ($value) {
            // オーナーに紐づく物件オプションを取得
            $this->operationTemplateOptions = Investment::getOptionsByOwner($value);
        } else {
            $this->operationTemplateOptions = [];
        }

        // Alpine.jsにオプション更新を通知
        $this->dispatch('select-search2-options',
            name: 'operation_template_id',
            options: $this->operationTemplateOptions,
            value: '',
        );
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

        $this->investment_room_id = ''; // 物件変更時は部屋選択をリセット

        if ($value) {
            // オーナーに紐づく物件オプションを取得
            $this->investmentRoomOptions = InvestmentRoom::getOptionsByInvestment($value);
        } else {
            $this->investmentRoomOptions = [];
        }

        // Alpine.jsにオプション更新を通知
        $this->dispatch('select-search2-options',
            name: 'investment_room_id',
            options: $this->investmentRoomOptions,
            value: '',
        );

    }

    public function render()
    {
        return view('livewire.admin.operation.create');
    }
}

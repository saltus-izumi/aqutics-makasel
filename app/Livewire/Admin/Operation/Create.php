<?php

namespace App\Livewire\Admin\Operation;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

use App\Models\Owner;
use App\Models\OperationKind;
use App\Models\Investment;
use App\Models\InvestmentRoom;
use App\Models\OperationTemplate;

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
    public $template = '';
    public $title = '';

    public function mount()
    {
        $oldOperationKindId = old('operation_kind_id');
        $oldOwnerId = old('owner_id');
        $oldInvestmentId = old('investment_id');
        $oldInvestmentRoomId = old('investment_room_id');
        $oldOperationTemplateId = old('operation_template_id');
        $oldTemplate = old('template');
        $oldTitle = old('title');

        $this->operation_kind_id = $oldOperationKindId !== null ? $oldOperationKindId : '';
        $this->owner_id = $oldOwnerId !== null ? $oldOwnerId : '';
        $this->investment_id = $oldInvestmentId !== null ? $oldInvestmentId : '';
        $this->investment_room_id = $oldInvestmentRoomId !== null ? $oldInvestmentRoomId : '';
        $this->operation_template_id = $oldOperationTemplateId !== null ? $oldOperationTemplateId : '';
        $this->template = $oldTemplate !== null ? $oldTemplate : '';
        $this->title = $oldTitle !== null ? $oldTitle : '';

        $this->operationKindOptions = OperationKind::getGroupOptions();
        $this->ownerOptions = Owner::getOptions();
        $this->investmentOptions = $this->owner_id
            ? Investment::getOptionsByOwner($this->owner_id)
            : [];
        $this->investmentRoomOptions = $this->investment_id
            ? InvestmentRoom::getOptionsByInvestment($this->investment_id)
            : [];
        $this->operationTemplateOptions = $this->operation_kind_id
            ? OperationTemplate::getOptionsByOperationGroupId($this->operation_kind_id)
            : [];
    }

    // オペレーション選択時のイベントハンドラ（wire:model.liveから自動呼び出し）
    public function updatedOperationKindId($value)
    {
        Log::info('updatedOperationKind called', ['value' => $value]);

        $this->operation_template_id = ''; // オペレーション変更時はカテゴリ（テンプレート）選択をリセット

        if ($value) {
            // オーナーに紐づく物件オプションを取得
            $this->operationTemplateOptions = OperationTemplate::getOptionsByOperationGroupId($value);
        } else {
            $this->operationTemplateOptions = [];
        }

        // Alpine.jsにオプション更新を通知
        $this->dispatch('select-search-options',
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
        $this->dispatch('select-search-options',
            name: 'investment_id',
            options: $this->investmentOptions,
            value: '',
        );

        $this->investmentRoomOptions = [];
        $this->dispatch('select-search-options',
            name: 'investment_room_id',
            options: $this->investmentRoomOptions,
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
        $this->dispatch('select-search-options',
            name: 'investment_room_id',
            options: $this->investmentRoomOptions,
            value: '',
        );
    }

    // カテゴリ時のイベントハンドラ
    public function updatedOperationTemplateId($value)
    {
        Log::info('updatedOperationTempalteId called', ['value' => $value]);

        $operationTemplate = OperationTemplate::find($value);
        if ($operationTemplate) {
            Log::info('get template', ['template' => $operationTemplate]);
            $this->template = $operationTemplate->value;
            $this->title = $operationTemplate->title;
        } else {
            $this->template = '';
            $this->title = '';
        }
    }


    public function render()
    {
        return view('livewire.admin.operation.create');
    }
}

<?php

namespace App\Livewire\Admin\Operation;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

use App\Models\Owner;
use App\Models\Operation;
use App\Models\OperationKind;
use App\Models\Investment;
use App\Models\InvestmentRoom;
use App\Models\OperationTemplate;
use App\Models\TeProgress;

class Create extends Component
{
    public $operationKindOptions = [];
    public $ownerOptions = [];
    public $investmentOptions = [];
    public $investmentRoomOptions = [];
    public $operationTemplateOptions = [];

    public $operationKindId = '';
    public $ownerId = '';
    public $investmentId = '';
    public $investmentRoomId = '';
    public $operationTemplateId = '';
    public $template = '';
    public $title = '';
    public $message = '';
    public $retailEstimateFiles = [];
    public $completionPhotoFiles = [];
    public $otherFiles = [];

    public $operationId = null;
    public $operation = null;
    public $teProgressId = null;
    public $teProgress = null;
    public $geProgressId = null;

    public function mount()
    {
        $oldOperationKindId = old('operation_kind_id');
        $oldOwnerId = old('owner_id');
        $oldInvestmentId = old('investment_id');
        $oldInvestmentRoomId = old('investment_room_id');
        $oldOperationTemplateId = old('operation_template_id');
        $oldTemplate = old('template');
        $oldTitle = old('title');
        $oldMessage = old('message');

        // オペレーションID
        if ($this->operationId) {
            $this->operation = Operation::with([
                    'threadMessage',
                    'retailEstimateFiles',
                    'completionPhotoFiles',
                    'otherFiles',
                ])
                ->where('id', $this->operationId)
                ->first();
            $oldOperationKindId = old('operation_kind_id', $this->operation->operation_kind_id);
            $oldOwnerId = old('owner_id', $this->operation->owner_id);
            $oldInvestmentId = old('investment_id', $this->operation->investment_id);
            $oldInvestmentRoomId = old('investment_room_id', $this->operation->investment_room_id);
            $oldOperationTemplateId = old('operation_template_id', $this->operation->operation_template_id);
            $oldTemplate = old('template', $this->operation->threadMessage?->body);
            $oldTitle = old('title', $this->operation->threadMessage?->title);
            $oldMessage = old('message', $this->operation->threadMessage?->extended_message);
            $this->teProgressId = $this->operation->te_progress_id;
            $this->retailEstimateFiles = $this->operation->retailEstimateFiles ?? [];
            $this->completionPhotoFiles = $this->operation->completionPhotoFiles ?? [];
            $this->otherFiles = $this->operation->otherFiles ?? [];
        }

        if ($this->teProgressId) {
            // TEプロセスIDが指定されている場合
            $this->teProgress = TeProgress::with([
                    'investment',
                    'investment.landlord',
                    'investment.landlord.owner',
                    'investmentRoom',
                    'retailEstimateFiles',
                    'completionPhotoFiles',
                ])
                ->where('id', $this->teProgressId)
                ->first();

            if ($this->teProgress) {
                $oldOwnerId = old('owner_id', $this->teProgress->investment?->landlord?->owner_id);
                $oldInvestmentId = old('investment_id', $this->teProgress->investment_id);
                $oldInvestmentRoomId = old('investment_room_id', $this->teProgress->investment_room_uid);
            }
        }

        $this->operationKindId = $oldOperationKindId !== null ? $oldOperationKindId : '';
        $this->ownerId = $oldOwnerId !== null ? $oldOwnerId : '';
        $this->investmentId = $oldInvestmentId !== null ? $oldInvestmentId : '';
        $this->investmentRoomId = $oldInvestmentRoomId !== null ? $oldInvestmentRoomId : '';
        $this->operationTemplateId = $oldOperationTemplateId !== null ? $oldOperationTemplateId : '';
        $this->template = $oldTemplate !== null ? $oldTemplate : '';
        $this->title = $oldTitle !== null ? $oldTitle : '';
        $this->message = $oldMessage !== null ? $oldMessage : '';

        $this->operationKindOptions = OperationKind::getGroupOptions();
        $this->ownerOptions = Owner::getOptions();
        $this->investmentOptions = $this->ownerId
            ? Investment::getOptionsByOwner($this->ownerId)
            : [];
        $this->investmentRoomOptions = $this->investmentId
            ? InvestmentRoom::getOptionsByInvestment($this->investmentId)
            : [];
        $this->operationTemplateOptions = $this->operationKindId
            ? OperationTemplate::getOptionsByOperationGroupId($this->operationKindId)
            : [];
    }

    // オペレーション選択時のイベントハンドラ（wire:model.liveから自動呼び出し）
    public function updatedOperationKindId($value)
    {
        Log::info('updatedOperationKind called', ['value' => $value]);

        $this->operationTemplateId = ''; // オペレーション変更時はカテゴリ（テンプレート）選択をリセット

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

        $this->investmentId = ''; // オーナー変更時は物件選択をリセット

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

        $this->investmentRoomId = ''; // 物件変更時は部屋選択をリセット

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

<div class="tw:px-[20px] tw:w-fit">
    <div class="tw:border-b tw:pb-[21px] tw:h-[72px] tw:flex tw:items-end">
        <x-form.select-search name="operation_kind_id" wire:model.live="operationKindId" :options="$operationKindOptions" :empty="true" :value="$operationKindId" class="tw:text-[1.2rem] tw:w-[532px]" />
    </div>
    <div class="tw:pt-[21px] tw:flex tw:gap-x-[38px]">
        <div class="tw:w-[532px] tw:flex tw:flex-col tw:gap-y-[21px]">
            <div>
                <div class="tw:pb-1">
                    オーナー<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.select-search name="owner_id" wire:model.live="ownerId" :options="$ownerOptions" :empty="true" :value="$ownerId" class="tw:text-[1.2rem]" :readonly="$teProgressId || $geProgressId" />
                <x-form.error-message>{{ $errors->first('owner_id') }}</x-form.error-message>
            </div>
            <div>
                <div class="tw:pb-1">
                    物件<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.select-search name="investment_id" wire:model.live="investmentId" :options="$investmentOptions" :empty="true" :value="$investmentId" class="tw:text-[1.2rem]" :readonly="$teProgressId || $geProgressId" />
                <x-form.error-message>{{ $errors->first('investment_id') }}</x-form.error-message>
            </div>
            <div>
                <div class="tw:pb-1">
                    部屋<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.select-search name="investment_room_id" wire:model.live="investmentRoomId" :options="$investmentRoomOptions" :empty="true" :value="$investmentRoomId" class="tw:text-[1.2rem]" :readonly="$teProgressId || $geProgressId" />
                <x-form.error-message>{{ $errors->first('investment_room_id') }}</x-form.error-message>
            </div>
            <div>
                <div class="tw:pb-1">
                    カテゴリ(通知種別) <x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.select-search name="operation_template_id" wire:model.live="operationTemplateId" :options="$operationTemplateOptions" :empty="true" :value="$operationTemplateId" class="tw:text-[1.2rem]" />
                <x-form.error-message>{{ $errors->first('operation_template_id') }}</x-form.error-message>
            </div>
            <div>
                <div class="tw:pb-1">
                    テンプレート
                </div>
                <x-form.textarea name="template" class="tw:text-[1.2rem]" rows="10" wire:model="template"></x-form.textarea>
            </div>
        </div>
        <div class="tw:w-[1026px] tw:flex tw:flex-col tw:gap-y-[21px]">
            <div>
                <div class="tw:pb-1">
                    カスタマイズタイトル<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.input name="title" wire:model="title" class="tw:text-[1.2rem]" />
                <x-form.error-message>{{ $errors->first('title') }}</x-form.error-message>
            </div>
            <div>
                <div class="tw:pb-1">
                    カスタマイズ項目
                </div>
                <x-form.textarea name="message" wire:model="message" class="tw:text-[1.2rem] tw:h-[502px]"></x-form.textarea>
            </div>
        </div>
    </div>
    <div class="tw:mt-[21px]">
        添付ファイル・画像、ＰＤＦ、Excel、Wordファイルが送信可能です。（可能ファイル数：20個／1ファイルの最大サイズ：25MB）
        <x-form.multi_file_upload name="operation_files" class="tw:h-[84px]" maxFileCount="20" maxFileSize="25MB" :files="$otherFiles" />
    </div>
    @if ($teProgress)
        <div class="tw:mt-[21px]">
            上代見積もり
            @foreach ($teProgress->retailEstimateFiles as $retailEstimateFile)
                <x-form.checkbox name="retail_estimate_files[]" :value="$retailEstimateFile->id" :checked="in_array($retailEstimateFile->id, collect($retailEstimateFiles)->pluck('te_progress_file_id')?->all())">{{ $retailEstimateFile->file_name }}</x-form.checkbox>
            @endforeach
        </div>
        <div class="tw:mt-[21px]">
            完工写真
            @foreach ($teProgress->completionPhotoFiles as $completionPhotoFile)
                <x-form.checkbox name="completion_photo_files[]" :value="$completionPhotoFile->id" :checked="in_array($completionPhotoFile->id, collect($completionPhotoFiles)->pluck('te_progress_file_id')?->all())">{{ $completionPhotoFile->file_name }}</x-form.checkbox>
            @endforeach
        </div>
    @endif
    @if ($geProgress)
        <div class="tw:mt-[21px]">
            上代見積もり
            @foreach ($geProgress->retailEstimateFiles as $retailEstimateFile)
                <x-form.checkbox name="retail_estimate_files[]" :value="$retailEstimateFile->id" :checked="in_array($retailEstimateFile->id, collect($retailEstimateFiles)->pluck('ge_progress_file_id')?->all())">{{ $retailEstimateFile->file_name }}</x-form.checkbox>
            @endforeach
        </div>
        <div class="tw:mt-[21px]">
            完工写真
            @foreach ($geProgress->completionPhotoFiles as $completionPhotoFile)
                <x-form.checkbox name="completion_photo_files[]" :value="$completionPhotoFile->id" :checked="in_array($completionPhotoFile->id, collect($completionPhotoFiles)->pluck('ge_progress_file_id')?->all())">{{ $completionPhotoFile->file_name }}</x-form.checkbox>
            @endforeach
        </div>
    @endif
    <input type="hidden" name="te_progress_id" value="{{ $teProgressId }}">
    <input type="hidden" name="ge_progress_id" value="{{ $geProgressId }}">
    <input type="hidden" name="thread_id" value="{{ $threadId }}">
    <input type="hidden" name="operation_id" value="{{ $operation?->id }}">
    <input type="hidden" name="thread_message_id" value="{{ $operation?->thread_message_id }}">
    <input type="hidden" name="ge_progress_step" value="{{ $geProgressStep }}">
</div>

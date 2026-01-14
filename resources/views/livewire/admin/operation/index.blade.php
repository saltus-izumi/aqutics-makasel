<div class="tw:px-[20px] tw:w-fit">
    <div class="tw:border-b tw:pb-[21px] tw:h-[72px] tw:flex tw:items-end">
    </div>
    <div class="tw:pt-[21px] tw:flex tw:gap-x-[38px]">
        <div class="tw:w-[250px] tw:px-[15px] tw:flex tw:flex-col tw:gap-y-[21px]">
            <div>
                <div class="tw:pb-1">
                    オペレーション所有者
                </div>
                <x-form.select-search name="assigned_user_id" :options="$userOptions" :empty="true" :value="$assignedUserId" class="tw:text-[1.2rem]" />
            </div>
            <div>
                <div class="tw:pb-1">
                    作成者
                </div>
                <x-form.select-search name="created_user_id" :options="$userOptions" :empty="true" :value="$createdUserId" class="tw:text-[1.2rem]" />
            </div>
            <div>
                <div class="tw:pb-1">
                    オーナー
                </div>
                <x-form.select-search name="owner_id" :options="$ownerOptions" :empty="true" :value="$ownerId" class="tw:text-[1.2rem]" />
            </div>
            <div>
                <div class="tw:pb-1">
                    カテゴリ
                </div>
                <x-form.select-search name="operation_template_id" :options="$operationTemplateOptions" :empty="true" :value="$operationTemplateId" class="tw:text-[1.2rem]" />
            </div>
            <div>
                <div class="tw:pb-1">
                    オペレーション
                </div>
                <x-form.select-search name="operation_kind_id" :options="$operationKindOptions" :empty="true" :value="$operationKindId" class="tw:text-[1.2rem]" />
            </div>
            <div>
                <div class="tw:pb-1">
                    ステータス
                </div>
                <x-form.select-search name="thread_status" :options="$threadStatusOptions" :empty="true" :value="$threadStatus" class="tw:text-[1.2rem]" />
            </div>
            <div>
                <div class="tw:pb-1">
                    既読 / 未読
                </div>
                <x-form.select-search name="is_read" :options="$isReadOptions" :empty="true" :value="$isRead" class="tw:text-[1.2rem]" />
            </div>
            <div>
                <div class="tw:pb-1">
                    作成日
                </div>
                <x-form.input type="date" name="first_post_at_from" :value="$firstPostAtFrom" class="tw:text-[1.2rem]" />
                <x-form.input type="date" name="first_post_at_to" :value="$firstPostAtTo" class="tw:text-[1.2rem] tw:mt-1" />
            </div>
            <div>
            </div>
        </div>
    </div>
</div>

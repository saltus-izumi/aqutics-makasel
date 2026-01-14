<x-admin.auth-layout title="オペレーション一覧">
    <div class="tw:px-[20px] tw:w-fit">
        <div class="tw:border-b tw:pb-[21px] tw:h-[72px] tw:flex tw:items-end">
        </div>
        <div class="tw:pt-[21px] tw:flex tw:gap-x-[38px]">
            <div class="tw:w-[250px] tw:px-[15px] tw:flex tw:flex-col tw:gap-y-[21px]" x-data="operationFilter()">
                <div>
                    <div class="tw:pb-1">
                        オペレーション所有者
                    </div>
                    <x-form.select-search name="assigned_user_id" :options="$userOptions" :empty="true" :value="old('assigned_user_id')" class="tw:text-[1.2rem]" />
                </div>
                <div>
                    <div class="tw:pb-1">
                        作成者
                    </div>
                    <x-form.select-search name="created_user_id" :options="$userOptions" :empty="true" :value="old('created_user_id')" class="tw:text-[1.2rem]" />
                </div>
                <div>
                    <div class="tw:pb-1">
                        オーナー
                    </div>
                    <x-form.select-search name="owner_id" :options="$ownerOptions" :empty="true" :value="old('owner_id')" class="tw:text-[1.2rem]" />
                </div>
                <div>
                    <div class="tw:pb-1">
                        カテゴリ
                    </div>
                    <x-form.select-search name="operation_template_id" :options="$operationTemplateOptions" :empty="true" :value="old('operation_template_id')" class="tw:text-[1.2rem]" />
                </div>
                <div>
                    <div class="tw:pb-1">
                        オペレーション
                    </div>
                    <x-form.select-search name="operation_kind_id" :options="$operationKindOptions" :empty="true" :value="old('operation_kind_id')" class="tw:text-[1.2rem]" />
                </div>
                <div>
                    <div class="tw:pb-1">
                        ステータス
                    </div>
                    <x-form.select-search name="thread_status" :options="$threadStatusOptions" :empty="true" :value="old('thread_status')" class="tw:text-[1.2rem]" />
                </div>
                <div>
                    <div class="tw:pb-1">
                        既読 / 未読
                    </div>
                    <x-form.select-search name="is_read" :options="$isReadOptions" :empty="true" :value="old('is_read')" class="tw:text-[1.2rem]" />
                </div>
                <div>
                    <div class="tw:pb-1">
                        作成日
                    </div>
                    <x-form.input-date type="date" name="first_post_at_from" :value="old('first_post_at_from')" class="tw:text-[1.2rem]" />
                    <x-form.input type="date" name="first_post_at_to" :value="old('first_post_at_to')" class="tw:text-[1.2rem] tw:mt-1" />
                    <div class="tw:mt-1 tw:w-full tw:text-right">
                        <button type="button" class="tw:text-[1.2rem] tw:text-pm_blue_001 tw:cursor-pointer" x-on:click="clearFilters()">
                            フィルターをクリア
                        </button>
                    </div>
                </div>
                <div>
                    <x-button.blue type="submit" class="tw:w-full tw:text-[1.2rem]">検索</x-button.blue>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('operationFilter', () => ({
                    clearFilters() {
                        window.dispatchEvent(new CustomEvent('select-search-clear'));

                        this.$el.querySelectorAll('input:not([type="hidden"])').forEach((input) => {
                            if (input.type === 'checkbox' || input.type === 'radio') {
                                input.checked = false;
                            } else {
                                input.value = '';
                            }
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                    },
                }));
            });
        </script>
    @endpush
</x-admin.auth-layout>

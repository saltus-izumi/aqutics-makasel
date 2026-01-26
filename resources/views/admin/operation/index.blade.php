<x-admin.auth-layout title="オペレーション一覧">
    <div class="tw:pl-[20px] tw:h-full" x-data="operationFilter()">
        <div class="tw:border-b tw:h-[72px] tw:flex tw:items-end tw:pl-[250px]">
            <div class="tw:flex">
                <a href="{{ route('admin.operation.index', array_merge(request()->query(), ['is_draft' => 0])) }}">
                    <div @class([
                        'tw:h-[42px] tw:w-[175px] tw:text-[1.4rem] tw:text-center tw:leading-[42px] tw:cursor-pointer',
                        'tw:bg-pm_gray_003 tw:border-b-[4px] tw:border-b-pm_blue_001' => ($conditions['is_draft'] ?? '0') == '0',
                        'tw:bg-pm_gray_004' => ($conditions['is_draft'] ?? '0') == '1',
                    ])>送信済み</div>
                </a>
                <a href="{{ route('admin.operation.index', array_merge(request()->query(), ['is_draft' => 1])) }}">
                    <div @class([
                        'tw:h-[42px] tw:w-[175px] tw:text-[1.4rem] tw:text-center tw:leading-[42px] tw:cursor-pointer',
                        'tw:bg-pm_gray_003 tw:border-b-[4px] tw:border-b-pm_blue_001' => ($conditions['is_draft'] ?? '0') == '1',
                        'tw:bg-pm_gray_004' => ($conditions['is_draft'] ?? '0') == '0',
                    ])>下書き</div>
                </a>
            </div>
        </div>
        <div class="tw:flex tw:h-[calc(100%-72px)]">
            <div class="tw:h-full tw:w-[250px] tw:py-[21px] tw:pr-[20px] tw:border-r tw:overflow-y-auto">
                <form method="get" action="{{ route('admin.operation.index') }}" class="tw:flex tw:flex-col tw:gap-y-[21px]" x-ref="filterForm">
                    <div>
                        <div class="tw:pb-1">
                            オペレーション所有者
                        </div>
                        <x-form.select-search name="assigned_user_id" :options="$userOptions" :empty="true" :value="$conditions['assigned_user_id'] ?? ''" class="tw:text-[1.2rem]" />
                    </div>
                    <div>
                        <div class="tw:pb-1">
                            作成者
                        </div>
                        <x-form.select-search name="created_user_id" :options="$userOptions" :empty="true" :value="$conditions['created_user_id'] ?? ''" class="tw:text-[1.2rem]" />
                    </div>
                    <div>
                        <div class="tw:pb-1">
                            オーナー
                        </div>
                        <x-form.select-search name="owner_id" :options="$ownerOptions" :empty="true" :value="$conditions['owner_id'] ?? ''" class="tw:text-[1.2rem]" />
                    </div>
                    <div>
                        <div class="tw:pb-1">
                            カテゴリ
                        </div>
                        <x-form.select-search name="operation_template_id" :options="$operationTemplateOptions" :empty="true" :value="$conditions['operation_template_id'] ?? ''" class="tw:text-[1.2rem]" />
                    </div>
                    <div>
                        <div class="tw:pb-1">
                            オペレーション
                        </div>
                        <x-form.select-search name="operation_kind_id" :options="$operationKindOptions" :empty="true" :value="$conditions['operation_kind_id'] ?? ''" class="tw:text-[1.2rem]" />
                    </div>
                    <div>
                        <div class="tw:pb-1">
                            ステータス
                        </div>
                        <x-form.select-search name="thread_status" :options="$threadStatusOptions" :empty="true" :value="$conditions['thread_status'] ?? ''" class="tw:text-[1.2rem]" />
                    </div>
                    <div>
                        <div class="tw:pb-1">
                            既読 / 未読
                        </div>
                        <x-form.select-search name="is_read" :options="$isReadOptions" :empty="true" :value="$conditions['is_read'] ?? ''" class="tw:text-[1.2rem]" />
                    </div>
                    <div>
                        <div class="tw:pb-1">
                            作成日
                        </div>
                        <x-form.input type="date" name="first_post_at_from" :value="$conditions['first_post_at_from'] ?? ''" class="tw:text-[1.2rem]" />
                        <x-form.input type="date" name="first_post_at_to" :value="$conditions['first_post_at_to'] ?? ''" class="tw:text-[1.2rem] tw:mt-1" />
                        <div class="tw:mt-1 tw:w-full tw:text-right">
                            <button type="button" class="tw:text-[1.2rem] tw:text-pm_blue_001 tw:cursor-pointer" x-on:click="clearFilters()">
                                フィルターをクリア
                            </button>
                        </div>
                    </div>
                    <div>
                        <x-button.blue type="submit" class="tw:w-full tw:text-[1.2rem]">検索</x-button.blue>
                        <input type="hidden" name="investment_id" value="{{ $conditions['investment_id'] ?? '' }}" x-ref="investmentId">
                    </div>
                </form>
            </div>
            <div class="tw:h-full tw:w-[calc(100%-250px)] tw:overflow-auto">
                <div class="tw:w-fit tw:px-[20px]">
                    <div class="tw:h-[134px] tw:w-[1295px] tw:py-[21px] tw:flex tw:gap-x-[38px] tw:z-[150] tw:bg-white">
                        <div class="tw:pr-[75px]">
                            <x-form.input class="tw:w-[245px] tw:text-[1.2rem]" placeholder="🔍 物件(ID)検索" :value="$conditions['investment_id'] ?? ''" x-ref="searchInput" x-on:keydown.enter.prevent="submitSearch()" />
                        </div>
                        <div class="tw:flex-1">
                            <table class="pm-table">
                                <thead>
                                    <tr>
                                        <th class="tw:w-[76px] tw:bg-pm_gray_004" rowspan="2">承諾タイプ</th>
                                        <th class="tw:w-[76px] tw:bg-pm_gray_004">EN</th>
                                        <th class="tw:h-[21px] tw:bg-pm_gray_004" colspan="3">LE</th>
                                        <th class="tw:h-[21px] tw:bg-pm_gray_004" colspan="2">TE</th>
                                    </tr>
                                    <tr>
                                        <th class="tw:w-[70px] tw:bg-pm_gray_004">入居申込</th>
                                        <th class="tw:w-[70px] tw:bg-pm_gray_004">原復提案</th>
                                        <th class="tw:w-[70px] tw:bg-pm_gray_004">新規提案</th>
                                        <th class="tw:w-[70px] tw:bg-pm_gray_004">空室提案</th>
                                        <th class="tw:w-[70px] tw:bg-pm_gray_004">修繕提案</th>
                                        <th class="tw:w-[70px] tw:bg-pm_gray_004">その他</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="tw:w-[70px] tw:bg-pm_gray_004">未承諾在庫</th>
                                        <td class="tw:text-[2.4rem]">3</td>
                                        <td class="tw:text-[2.4rem]">5</td>
                                        <td class="tw:text-[2.4rem]">1</td>
                                        <td class="tw:text-[2.4rem]">2</td>
                                        <td class="tw:text-[2.4rem]">0</td>
                                        <td class="tw:text-[2.4rem]">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tw:flex">
                            <div class="tw:w-[76px] tw:text-center">
                                提案済
                                <div class="">
                                    <i class="fas fa-comment-edit"></i>
                                    1
                                </div>
                            </div>
                            <div class="tw:w-[76px] tw:text-center">
                                承諾済
                                <div class="">
                                    <i class="fas fa-comment-check"></i>
                                    1
                                </div>
                            </div>
                            <div class="tw:w-[76px] tw:text-center">
                                却下
                                <div class="">
                                    <i class="fas fa-comment-times"></i>
                                    1
                                </div>
                            </div>
                            <div class="tw:w-[76px] tw:text-center">
                                再提案済
                                <div class="">
                                    <i class="fas fa-comment-plus"></i>
                                    1
                                </div>
                            </div>
                            <div class="tw:w-[76px] tw:text-center">
                                中止
                                <div class="">
                                    <i class="fas fa-comment-slash"></i>
                                    1
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tw:h-[calc(100%-134px)]">
                        <livewire:admin.operation.operation-list :conditions="$conditions" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('operationFilter', () => ({
                    submitSearch() {
                        this.$refs.investmentId.value = this.$refs.searchInput.value;
                        this.$refs.filterForm.submit();
                    },
                    clearFilters() {
                        window.dispatchEvent(new CustomEvent('select-search-clear'));

                        const form = this.$refs.filterForm;
                        form.querySelectorAll('input:not([type="hidden"])').forEach((input) => {
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

<x-admin.auth-layout title="原復プロセス管理">
    <div class="tw:px-[26px] tw:py-[21px]">
        <div class="tw:w-[286px]">
            <div class="tw:text-[1.3rem]">
                物件選択
            </div>
            <div>
                <x-form.input class="tw:w-[245px] tw:text-[1.2rem]" placeholder="オーナー名（ID）、物件名" :value="$conditions['investment_id'] ?? ''" x-ref="searchInput" x-on:keydown.enter.prevent="submitSearch()" />
            </div>
            <div class="tw:h-[42px] tw:leading-[42px]">
                <x-form.checkbox class="tw:text-[1.1rem]">未完了のみ表示</x-form.checkbox>
            </div>
        </div>
        <div class="tw:w-full tw:flex tw:border-b tw:mb-[21px]">
            <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-[1.4rem] tw:font-bold tw:text-center tw:bg-[#d9d9d9] tw:border-b tw:border-b-3 tw:border-b-pm_blue_001">原復</div>
            <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-[1.4rem] tw:font-bold tw:text-center tw:bg-[#efefef]">LE</div>
            <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-[1.4rem] tw:font-bold tw:text-center tw:bg-[#efefef]">TE</div>
            <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-[1.4rem] tw:font-bold tw:text-center tw:bg-[#efefef]">EN</div>
            <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-[1.4rem] tw:font-bold tw:text-center tw:bg-[#efefef]">更新</div>
            <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-[1.4rem] tw:font-bold tw:text-center tw:bg-[#efefef]">解約</div>
        </div>
        <div class="tw:px-[26px] tw:overflow-hidden">
            <livewire:admin.ge-progress.progress-list />
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

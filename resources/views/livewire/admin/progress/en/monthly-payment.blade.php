<div
    x-data="enMonthlyPayment()"
    @change="handleChange($event)"
>
    <div class="tw:flex tw:h-[42px] tw:items-end">
        <div class="tw:w-[130px] tw:text-[1.2rem] tw:font-bold">月額支払い</div>
        <div class="tw:w-[676px] tw:text-[1.2rem] tw:font-bold tw:text-right">
            月額合計
            <span class="tw:pl-4 tw:text-[1.9rem]">108900</span>
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">賃料</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">共益費</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">その他固定費</div>
        <div class="tw:w-[104px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">町内会費</div>
        <div class="tw:w-[104px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">駐車場</div>
        <div class="tw:w-[104px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">水道代</div>
        <div class="tw:w-[104px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">振替手数料</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input-number name="rent_fee" :value="$enProgress?->rent_fee" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="common_service_fee" :value="$enProgress?->common_service_fee" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="other_fixed_fee" :value="$enProgress?->other_fixed_fee" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[104px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="neighborhood_fee" :value="$enProgress?->neighborhood_fee" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[104px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="parking_fee" :value="$enProgress?->parking_fee" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[104px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="water_fee" :value="$enProgress?->water_fee" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[104px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="transfer_fee" :value="$enProgress?->transfer_fee" class="tw:!h-[40px]" :border="false" />
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('enMonthlyPayment', () => ({
                handleChange(event) {
                    const target = event?.target;
                    if (!target) {
                        return;
                    }

                    const fieldName = (target.name || '').trim();
                    if (!fieldName) {
                        return;
                    }

                    let value = target.value;
                    if (target.type === 'checkbox') {
                        value = target.checked;
                    } else if (target.type === 'radio') {
                        if (!target.checked) {
                            return;
                        }
                        value = target.value;
                    }

                    this.$wire.call('saveFieldByName', fieldName, value);
                },
            }));
        });
    </script>
@endpush

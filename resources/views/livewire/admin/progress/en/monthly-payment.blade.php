<div
    x-data="enMonthlyPaymentComponent({
        initialFields: @js([
            'rent_fee' => $enProgress?->rent_fee,
            'common_service_fee' => $enProgress?->common_service_fee,
            'other_fixed_fee' => $enProgress?->other_fixed_fee,
            'neighborhood_fee' => $enProgress?->neighborhood_fee,
            'parking_fee' => $enProgress?->parking_fee,
            'water_fee' => $enProgress?->water_fee,
            'transfer_fee' => $enProgress?->transfer_fee,
        ]),
    })"
    @input="handleInput($event)"
    @change="handleChange($event)"
>
    <div class="tw:flex tw:h-[42px] tw:items-end">
        <div class="tw:w-[130px] tw:text-[1.2rem] tw:font-bold">月額支払い</div>
        <div class="tw:w-[676px] tw:text-[1.2rem] tw:font-bold tw:text-right">
            月額合計
            <span class="tw:pl-4 tw:text-[1.9rem]" x-text="formatMonthlyTotal()">{{ number_format($this->monthlyTotal) }}</span>
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
        (() => {
            window.enMonthlyPaymentComponent = (params = {}) => ({
                monthlyFieldNames: ['rent_fee', 'common_service_fee', 'other_fixed_fee', 'neighborhood_fee', 'parking_fee', 'water_fee', 'transfer_fee'],
                fields: params.initialFields || {},
                toHalfWidthDigits(value) {
                    return String(value ?? '')
                        .replace(/[０-９]/g, (ch) => String.fromCharCode(ch.charCodeAt(0) - 0xFEE0))
                        .replace(/[＋]/g, '+')
                        .replace(/[－−]/g, '-');
                },
                parseInteger(value) {
                    const normalized = this.toHalfWidthDigits(value).replace(/,/g, '').trim();
                    if (normalized === '' || normalized === '+' || normalized === '-') {
                        return 0;
                    }

                    const parsed = Number(normalized);
                    if (!Number.isFinite(parsed)) {
                        return 0;
                    }

                    return Math.trunc(parsed);
                },
                monthlyTotal() {
                    return this.monthlyFieldNames.reduce((sum, fieldName) => {
                        return sum + this.parseInteger(this.fields[fieldName]);
                    }, 0);
                },
                formatMonthlyTotal() {
                    return this.monthlyTotal().toLocaleString('ja-JP');
                },
                updateFieldFromTarget(target) {
                    if (!target) {
                        return null;
                    }

                    const fieldName = (target.name || '').trim();
                    if (!fieldName || !this.monthlyFieldNames.includes(fieldName)) {
                        return null;
                    }

                    let value = target.value;
                    if (target.type === 'checkbox') {
                        value = target.checked;
                    } else if (target.type === 'radio') {
                        if (!target.checked) {
                            return null;
                        }
                        value = target.value;
                    }

                    this.fields[fieldName] = value;
                    return { fieldName, value };
                },
                handleInput(event) {
                    this.updateFieldFromTarget(event?.target);
                },
                handleChange(event) {
                    const updated = this.updateFieldFromTarget(event?.target);
                    if (!updated) {
                        return;
                    }

                    this.$wire.call('saveFieldByName', updated.fieldName, updated.value);
                },
            });
        })();
    </script>
@endpush

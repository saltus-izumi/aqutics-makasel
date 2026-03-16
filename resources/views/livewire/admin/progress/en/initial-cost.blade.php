<div
    x-data="enInitialCost({
        initialFields: @js([
            'deposit_fee' => $enProgress?->deposit_fee,
            'security_deposit_fee' => $enProgress?->security_deposit_fee,
            'cleaning_fee' => $enProgress?->cleaning_fee,
            'key_money' => $enProgress?->key_money,
            'key_antibacterial_fee' => $enProgress?->key_antibacterial_fee,
        ]),
    })"
    @input="handleInput($event)"
    @change="handleChange($event)"
>
    <div class="tw:flex tw:h-[42px] tw:items-end">
        <div class="tw:w-[130px] tw:text-[1.2rem] tw:font-bold">初期費用</div>
        <div class="tw:w-[676px] tw:text-[1.2rem] tw:font-bold tw:text-right">
            初期合計
            <span class="tw:pl-4 tw:text-[1.9rem]" x-text="formatCostTotal()">0</span>
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">敷金</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">保証金</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">退去時清掃費</div>
        <div class="tw:w-[104px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">礼金</div>
        <div class="tw:w-[104px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">鍵・抗菌費</div>
        <div class="tw:w-[104px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0"></div>
        <div class="tw:w-[104px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0"></div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input-number name="deposit_fee" :value="$enProgress?->deposit_fee" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="security_deposit_fee" :value="$enProgress?->security_deposit_fee" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="cleaning_fee" :value="$enProgress?->cleaning_fee" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[104px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="key_money" :value="$enProgress?->key_money" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[104px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="key_antibacterial_fee" :value="$enProgress?->key_antibacterial_fee" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[104px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
        </div>
        <div class="tw:w-[104px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('enInitialCost', (params = {}) => ({
                costFieldNames: [
                    'deposit_fee',
                    'security_deposit_fee',
                    'cleaning_fee',
                    'key_money',
                    'key_antibacterial_fee',
                ],
                fields: params.initialFields || {},
                monthlyTotal: 0,
                init() {
                    this.recalculateMonthlyTotal();
                },
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
                recalculateMonthlyTotal() {
                    this.monthlyTotal = this.costFieldNames.reduce((sum, fieldName) => {
                        return sum + this.parseInteger(this.fields[fieldName]);
                    }, 0);
                },
                formatCostTotal() {
                    return this.monthlyTotal.toLocaleString('ja-JP');
                },
                updateField(target) {
                    if (!target) {
                        return null;
                    }

                    const fieldName = (target.name || '').trim();
                    if (!fieldName || !this.costFieldNames.includes(fieldName)) {
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
                    this.recalculateMonthlyTotal();

                    return { fieldName, value };
                },
                handleInput(event) {
                    this.updateField(event?.target);
                },
                handleChange(event) {
                    const updated = this.updateField(event?.target);
                    if (!updated) {
                        return;
                    }

                    this.$wire.call('saveFieldByName', updated.fieldName, updated.value);
                },
            }));
        });
    </script>
@endpush

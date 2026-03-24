<div
    x-data="enInitialPayment({
        initialFields: @js([
            'total_payment_amount' => $enProgress?->total_payment_amount,
            'invoice_due_date' => $enProgress?->invoice_due_date,
            'payment_status' => $enProgress?->payment_status,
            'payment_proof_url' => $enProgress?->payment_proof_url,
            'payment_confirmed_user_id' => $enProgress?->payment_confirmed_user_id,
            'initial_cost_memo' => $enProgress?->initial_cost_memo,
        ]),
    })"
    @input="handleInput($event)"
    @change="handleChange($event)"
>
    <div class="tw:h-[21px] tw:text-[1.2rem] tw:font-bold">
        ４｜初期費用入金
    </div>
    <div class="tw:flex">
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">請求金額合計</div>
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">入金額合計</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">請求期限</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">入金状況</div>
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">入金証跡URL</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">入金確認者</div>
        <div class="tw:w-[754px] tw:h-[21px] tw:pl-[26px] tw:text-left tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">初期費用メモ</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[182px] tw:h-[42px] tw:flex tw:items-center tw:justify-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:leading-[40px]">
            {{ number_format($this->initialCost)}}
        </div>
        <div class="tw:w-[182px] tw:h-[42px] tw:flex tw:items-center tw:justify-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="total_payment_amount" :value="$enProgress?->total_payment_amount" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:flex tw:items-center tw:justify-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-date name="invoice_due_date" :value="$enProgress?->invoice_due_date" class="tw:!h-[40px] tw:text-center" :border="false" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:flex tw:items-center tw:justify-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.select2 name="payment_status" :value="$enProgress?->payment_status" :options="App\Models\EnProgress::PAYMENT_STATUS" empty=" " class="tw:!w-[110px]" />
        </div>
        <div class="tw:w-[182px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.input name="payment_proof_url" :value="$enProgress?->payment_proof_url" class="tw:!h-[40px] tw:!text-left" :border="false" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="payment_confirmed_user_id" :value="$enProgress?->payment_confirmed_user_id" :options="$enResponsibleShortOptions" empty=" " class="tw:!w-[110px]" />
        </div>
        <div class="tw:w-[754px] tw:h-[42px] tw:text-left tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="initial_cost_memo" :value="$enProgress?->initial_cost_memo" class="tw:!h-[40px] tw:!text-left" :border="false" />
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            window.enInitialPayment = (params = {}) => ({
                fields: params.initialFields || {},
                saveTimers: {},
                saveDelayMs: 400,
                lastSavedFields: {},
                init() {
                    Object.entries(this.fields).forEach(([fieldName, value]) => {
                        this.lastSavedFields[fieldName] = this.toComparableValue(value);
                    });
                },
                toComparableValue(value) {
                    return value === null || value === undefined ? '' : String(value);
                },
                updateFieldFromTarget(target) {
                    if (!target) {
                        return null;
                    }
                    const fieldName = (target.name || '').trim();
                    if (!fieldName) {
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
                saveField(fieldName, value) {
                    const comparable = this.toComparableValue(value);
                    if (this.lastSavedFields[fieldName] === comparable) {
                        return;
                    }

                    this.lastSavedFields[fieldName] = comparable;
                    this.$wire.call('saveFieldByName', fieldName, value);
                },
                queueSave(fieldName, value) {
                    if (this.saveTimers[fieldName]) {
                        window.clearTimeout(this.saveTimers[fieldName]);
                    }

                    this.saveTimers[fieldName] = window.setTimeout(() => {
                        delete this.saveTimers[fieldName];
                        this.saveField(fieldName, value);
                    }, this.saveDelayMs);
                },
                handleInput(event) {
                    const updated = this.updateFieldFromTarget(event?.target);
                    if (!updated) {
                        return;
                    }

                    this.queueSave(updated.fieldName, updated.value);
                },
                handleChange(event) {
                    const updated = this.updateFieldFromTarget(event?.target);
                    if (!updated) {
                        return;
                    }

                    if (this.saveTimers[updated.fieldName]) {
                        window.clearTimeout(this.saveTimers[updated.fieldName]);
                        delete this.saveTimers[updated.fieldName];
                    }

                    this.saveField(updated.fieldName, updated.value);
                },
            });
        })();
    </script>
@endpush

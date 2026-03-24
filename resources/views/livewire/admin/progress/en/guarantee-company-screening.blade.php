<div
    x-data="enGuaranteeCompanyScreening({
        initialFields: @js([
            'guarantee_company_id' => $enProgress?->guarantee_company_id,
            'screening_application_date' => $enProgress?->screening_application_date,
            'screening_result' => $enProgress?->screening_result,
            'approval_number' => $enProgress?->approval_number,
            'approval_guarantee_company_plan' => $enProgress?->approval_guarantee_company_plan,
            'guarantor_fee_burden' => $enProgress?->guarantor_fee_burden,
            'condition_summary' => $enProgress?->condition_summary,
            'approval_notice_url' => $enProgress?->approval_notice_url,
        ]),
    })"
    @input="handleInput($event)"
    @change="handleChange($event)"
>
    <div class="tw:h-[21px] tw:text-[1.2rem] tw:font-bold">
        １｜保証会社審査
    </div>
    <div class="tw:flex">
        <div class="tw:w-[260px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">保証会社名</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">審査申込日</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">審査結果</div>
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">承認番号</div>
        <div class="tw:w-[338px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">保証プラン</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">保証料負担</div>
        <div class="tw:w-[338px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">条件付の場合（条件要約）</div>
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">承認通知書URL</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[260px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.select name="guarantee_company_id" :value="$enProgress?->guarantee_company_id" :options="$guaranteeCompanyOptions" class="tw:!w-full tw:!h-[40px]" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-date name="screening_application_date" :value="$enProgress?->screening_application_date" class="tw:!h-[40px] tw:!text-center" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="screening_result" :value="$enProgress?->screening_result" :options="App\Models\EnProgress::SCREENING_RESULT" empty=" " class="tw:!w-[110px]" />
        </div>
        <div class="tw:w-[182px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="approval_number" :value="$enProgress?->approval_number" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[338px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="approval_guarantee_company_plan" :value="$enProgress?->approval_guarantee_company_plan" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="guarantor_fee_burden" :value="$enProgress?->guarantor_fee_burden" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[338px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="condition_summary" :value="$enProgress?->condition_summary" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[182px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="approval_notice_url" :value="$enProgress?->approval_notice_url" class="tw:!h-[40px]" :border="false" />
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            window.enGuaranteeCompanyScreening = (params = {}) => ({
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

<div
    x-data="enContractTerms({
        initialFields: @js([
            'fr_active_flag' => (bool) $enProgress?->fr_active_flag,
            'fr_start_date' => $enProgress?->fr_start_date,
            'fr_end_date' => $enProgress?->fr_end_date,
            'desired_contract_date' => $enProgress?->desired_contract_date,
            'planned_payment_date' => $enProgress?->planned_payment_date,
            'desired_move_in_date' => $enProgress?->desired_move_in_date,
            'contract_start_date' => $enProgress?->contract_start_date,
            'contract_end_date' => $enProgress?->contract_end_date,
            'renewal_fee' => $enProgress?->renewal_fee,
            'guarantee_company_id' => $enProgress?->guarantee_company_id,
            'guarantee_company_plan' => $enProgress?->guarantee_company_plan,
            'guarantee_company_monthly_fee' => $enProgress?->guarantee_company_monthly_fee,
            'guarantee_company_status' => $enProgress?->guarantee_company_status,
            'fire_insurance_name' => $enProgress?->fire_insurance_name,
            'fire_insurance_monthly_fee' => $enProgress?->fire_insurance_monthly_fee,
            'fire_insurance_status' => $enProgress?->fire_insurance_status,
            'anshin_support_flag' => $enProgress?->anshin_support_flag,
            'move_out_cleaning_flag' => $enProgress?->move_out_cleaning_flag,
            'ac_cleaning_flag' => $enProgress?->ac_cleaning_flag,
            'cancellation_penalty_flag' => $enProgress?->cancellation_penalty_flag,
            'pet_allowed_flag' => $enProgress?->pet_allowed_flag,
            'instrument_allowed_flag' => $enProgress?->instrument_allowed_flag,
            'fr_flag' => $enProgress?->fr_flag,
            'two_person_allowed_flag' => $enProgress?->two_person_allowed_flag,
        ]),
    })"
    @input="handleInput($event)"
    @change="handleChange($event)"
>
    <div class="tw:h-[42px] tw:flex tw:mt-[21px] tw:items-center tw:justify-end tw:gap-x-[26px]">
        <x-button.black class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">マイソク</x-button.black>
        <x-button.black class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">新規募集条件</x-button.black>
        <x-button.black class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">WP審査基準</x-button.black>
    </div>
    <div class="tw:h-[21px] tw:text-[1.2rem] tw:font-bold">
        契約条件（申込ID：{{ $enProgress->application_id }}）
    </div>
    <div class="tw:flex">
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">完工予定日</div>
        <div class="tw:w-[208px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">完工日</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">契約形態</div>
        <div class="tw:w-[286px] tw:h-[21px] tw:text-left tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0 tw:pl-3">
            <x-form.checkbox name="fr_active_flag" :checked="(bool) $enProgress?->fr_active_flag" class="tw:!text-[#ff0000] tw:accent-[#ff0000]" label="" label_class="tw:!text-[#ff0000]">FR期間</x-form.checkbox>
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            {{ $latestGeProgress?->completion_scheduled_date?->format('Y年m月d日') }}
        </div>
        <div class="tw:w-[208px] tw:h-[21px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            {{ $latestGeProgress?->completion_received_date?->format('Y年m月d日') }}
        </div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            {{ $enProgress->contract_type }}
        </div>
        <div class="tw:w-[286px] tw:h-[21px] tw:text-left tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:pl-3 tw:text-[#ff0000]">
            <x-form.input-date name="fr_start_date" :value="$enProgress?->fr_start_date" class="tw:!h-[19px] tw:!w-[90px] tw:text-[#ff0000]" />
            〜
            <x-form.input-date name="fr_end_date" :value="$enProgress?->fr_end_date" class="tw:!h-[19px] tw:!w-[90px] tw:text-[#ff0000]" />
            (<span x-text="getPeriodText('fr_start_date', 'fr_end_date')">-</span>)
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">契約希望日</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">入金予定日</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">入居希望日</div>
        <div class="tw:w-[286px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">契約期間</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">更新料</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input-date name="desired_contract_date" :value="$enProgress?->desired_contract_date" class="tw:!h-[40px] tw:!text-center" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-l-0 tw:border-t-0">
            <x-form.input-date name="planned_payment_date" :value="$enProgress?->planned_payment_date" class="tw:!h-[40px] tw:!text-center" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-date name="desired_move_in_date" :value="$enProgress?->desired_move_in_date" class="tw:!h-[40px] tw:!text-center" />
        </div>
        <div class="tw:w-[286px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.input-date name="contract_start_date" :value="$enProgress?->contract_start_date" class="tw:!h-[40px] tw:!w-[90px]" />
            〜
            <x-form.input-date name="contract_end_date" :value="$enProgress?->contract_end_date" class="tw:!h-[40px] tw:!w-[90px]" />
            (<span x-text="getPeriodText('contract_start_date', 'contract_end_date')">-</span>)
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="renewal_fee" :value="$enProgress?->renewal_fee" class="tw:!h-[40px] tw:text-center tw:border-0" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[208px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">保証会社名</div>
        <div class="tw:w-[312px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">プラン名</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">月額費用</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">法人除外</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">連帯保証</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[208px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.select name="guarantee_company_id" :value="$enProgress?->guarantee_company_id" :options="$guaranteeCompanyOptions" class="tw:!w-[206px] tw:!h-[40px]" />
        </div>
        <div class="tw:w-[312px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="guarantee_company_plan" :value="$enProgress?->guarantee_company_plan" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="guarantee_company_monthly_fee" :value="$enProgress?->guarantee_company_monthly_fee" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.radio name="guarantee_company_status" :checked="$enProgress?->guarantee_company_status" :value="App\Models\EnProgress::GUARANTEE_COMPANY_STATUS_CORPORATE_EXEMPT" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.radio name="guarantee_company_status" :checked="$enProgress?->guarantee_company_status" :value="App\Models\EnProgress::GUARANTEE_COMPANY_STATUS_JOINT_GUARANTOR" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[520px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">火災保険名</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">月額費用</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">法人除外</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">個人加入</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[520px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="fire_insurance_name" :value="$enProgress?->fire_insurance_name" class="tw:!h-[40px] tw:border-0" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="fire_insurance_monthly_fee" :value="$enProgress?->fire_insurance_monthly_fee" class="tw:!h-[40px] tw:border-0" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.radio name="fire_insurance_status" :checked="$enProgress?->fire_insurance_status" :value="App\Models\EnProgress::FIRE_INSURANCE_STATUS_CORPORATE_EXEMPT" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.radio name="fire_insurance_status" :checked="$enProgress?->fire_insurance_status" :value="App\Models\EnProgress::FIRE_INSURANCE_STATUS_INDIVIDUAL" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">安心入居</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">退去時清掃徴収</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">ACクリーニング</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">解約違約金</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">ペット</div>
        <div class="tw:w-[156px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">楽器</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:leading-[40px]">
            <x-form.select2 name="anshin_support_flag" :value="$enProgress?->anshin_support_flag" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="move_out_cleaning_flag" :value="$enProgress?->move_out_cleaning_flag" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="ac_cleaning_flag" :value="$enProgress?->ac_cleaning_flag" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="cancellation_penalty_flag" :value="$enProgress?->cancellation_penalty_flag" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="pet_allowed_flag" :value="$enProgress?->pet_allowed_flag" :options="['0' => '不可', '1' => '可']" />
        </div>
        <div class="tw:w-[156px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="instrument_allowed_flag" :value="$enProgress?->instrument_allowed_flag" :options="['0' => '不可', '1' => '可']" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">FR</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">二人入居</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0"></div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0"></div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0"></div>
        <div class="tw:w-[156px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0"></div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:leading-[40px]">
            <x-form.select2 name="fr_flag" :value="$enProgress?->fr_flag" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="two_person_allowed_flag" :value="$enProgress?->two_person_allowed_flag" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[156px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="" :options="['0' => 'ー', '1' => '◯']" />
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('enContractTerms', (params = {}) => ({
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
                parseDate(value) {
                    if (!value) {
                        return null;
                    }
                    const normalized = String(value).trim().replace(/\//g, '-');
                    const date = new Date(normalized);
                    if (Number.isNaN(date.getTime())) {
                        return null;
                    }
                    return date;
                },
                calcMonthDiff(startValue, endValue) {
                    const startDate = this.parseDate(startValue);
                    const endDate = this.parseDate(endValue);
                    if (!startDate || !endDate) {
                        return null;
                    }

                    const monthDiff = (endDate.getFullYear() - startDate.getFullYear()) * 12
                        + (endDate.getMonth() - startDate.getMonth());

                    if (monthDiff < 0) {
                        return null;
                    }

                    return monthDiff;
                },
                getPeriodText(startFieldName, endFieldName) {
                    const monthDiff = this.calcMonthDiff(this.fields[startFieldName], this.fields[endFieldName]);
                    if (monthDiff === null) {
                        return '-';
                    }
                    return `${monthDiff}ヶ月`;
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
            }));
        });
    </script>
@endpush

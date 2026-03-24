<div
    x-data="enWpScreening({
        initialFields: @js([
            'identity_verification_flag' => (bool) $enProgress?->identity_verification_flag,
            'condition_match_flag' => (bool) $enProgress?->condition_match_flag,
            'antisocial_check_flag' => (bool) $enProgress?->antisocial_check_flag,
            'special_agreement_note_flag' => (bool) $enProgress?->special_agreement_note_flag,
            'risk_category' => $enProgress?->risk_category,
            'escalation_flag' => $enProgress?->escalation_flag,
            'wp_approver_id' => $enProgress?->wp_approver_id,
            'wp_screening_memo' => $enProgress?->wp_screening_memo,
        ]),
    })"
    @input="handleInput($event)"
    @change="handleChange($event)"
>
    <div class="tw:h-[21px] tw:text-[1.2rem] tw:font-bold">
        ２｜WP審査（社内審査）
    </div>
    <div class="tw:flex">
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">本人確認</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">条件整合</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">反社確認</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">特約覚書</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">リスク区分</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">エスカレーション</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">WP承認者</div>
        <div class="tw:w-[988px] tw:h-[21px] tw:pl-[26px] tw:text-left tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">WP審査メモ</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[78px] tw:h-[42px] tw:flex tw:items-center tw:justify-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.checkbox name="identity_verification_flag" :checked="$enProgress?->identity_verification_flag" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:flex tw:items-center tw:justify-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.checkbox name="condition_match_flag" :checked="$enProgress?->condition_match_flag" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:flex tw:items-center tw:justify-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.checkbox name="antisocial_check_flag" :checked="$enProgress?->antisocial_check_flag" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:flex tw:items-center tw:justify-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.checkbox name="special_agreement_note_flag" :checked="$enProgress?->special_agreement_note_flag" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="risk_category" :value="$enProgress?->risk_category" :options="App\Models\EnProgress::RISK_CATEGORY" empty=" " class="tw:!w-[110px]" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="escalation_flag" :value="$enProgress?->escalation_flag" :options="['1'=>'要', '0'=>'不要']" class="tw:!w-[110px]" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="wp_approver_id" :value="$enProgress?->wp_approver_id" :options="$enResponsibleShortOptions" empty=" " class="tw:!w-[110px]" />
        </div>
        <div class="tw:w-[988px] tw:h-[42px] tw:text-left tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="wp_screening_memo" :value="$enProgress?->wp_screening_memo" class="tw:!h-[40px] tw:!text-left" :border="false" />
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            window.enWpScreening = (params = {}) => ({
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

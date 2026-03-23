<div
    x-data="enCorporateEmergencyContact({
        initialFields: @js([
            'last_name' => $enProgressEmergencyContact?->last_name,
            'first_name' => $enProgressEmergencyContact?->first_name,
            'last_kana' => $enProgressEmergencyContact?->last_kana,
            'first_kana' => $enProgressEmergencyContact?->first_kana,
            'gender' => $enProgressEmergencyContact?->gender,
            'birth_date' => $enProgressEmergencyContact?->birth_date?->format('Y/m/d'),
            'relationship' => $enProgressEmergencyContact?->relationship,
            'mobile_phone_number' => $enProgressEmergencyContact?->mobile_phone_number,
            'workplace_or_school_name' => $enProgressEmergencyContact?->workplace_or_school_name,
            'workplace_or_school_phone_number' => $enProgressEmergencyContact?->workplace_or_school_phone_number,
        ]),
    })"
    @input="handleInput($event)"
    @change="handleChange($event)"
>
    <div class="tw:h-[21px] tw:text-[1.2rem] tw:font-bold">
        緊急連絡先
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">氏名</div>
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">カナ（氏名）</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">性別</div>
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">生年月日（〇歳）</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">続柄</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[42px] tw:flex tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="last_name" :value="$enProgressEmergencyContact?->last_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
            <x-form.input name="first_name" :value="$enProgressEmergencyContact?->first_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[234px] tw:h-[42px] tw:flex tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="last_kana" :value="$enProgressEmergencyContact?->last_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
            <x-form.input name="first_kana" :value="$enProgressEmergencyContact?->first_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.select name="gender" :value="$enProgressEmergencyContact?->gender" :options="App\Models\EnProgressEmergencyContact::GENDER" class="tw:!h-[40px]" />
        </div>
        <div class="tw:w-[182px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:flex">
            <x-form.input-date name="birth_date" :value="$enProgressEmergencyContact?->birth_date" class="tw:!h-[40px] tw:!text-center" />
            <div class="tw:!h-[40px] tw:leading-[40px] tw:pr-5">({{ $enProgressEmergencyContact?->birth_date?->age }})</div>
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="relationship" :value="$enProgressEmergencyContact?->relationship" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[806px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">自宅住所</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[806px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">携帯電話番号</div>
        <div class="tw:w-[286px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">勤務先</div>
        <div class="tw:w-[286px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">電話番号</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="mobile_phone_number" :value="$enProgressEmergencyContact?->mobile_phone_number" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[286px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="workplace_or_school_name" :value="$enProgressEmergencyContact?->workplace_or_school_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[286px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="workplace_or_school_phone_number" :value="$enProgressEmergencyContact?->workplace_or_school_phone_number" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            window.enCorporateEmergencyContact = (params = {}) => ({
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

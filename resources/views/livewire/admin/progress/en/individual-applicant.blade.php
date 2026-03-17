<div
    x-data="enIndividualApplicant({
        initialFields: @js([
            'last_name' => $enProgressIndividualApplicant?->last_name,
            'first_name' => $enProgressIndividualApplicant?->first_name,
            'last_kana' => $enProgressIndividualApplicant?->last_kana,
            'first_kana' => $enProgressIndividualApplicant?->first_kana,
            'gender' => $enProgressIndividualApplicant?->gender,
            'birth_date' => $enProgressIndividualApplicant?->birth_date?->format('Y/m/d'),
            'spouse_flag' => $enProgressIndividualApplicant?->spouse_flag,
            'mobile_phone_number' => $enProgressIndividualApplicant?->mobile_phone_number,
            'email' => $enProgressIndividualApplicant?->email,
            'residence_type' => $enProgressIndividualApplicant?->residence_type,
            'residence_years' => $enProgressIndividualApplicant?->residence_years,
            'move_reason' => $enProgressIndividualApplicant?->move_reason,
            'moving_guidance' => $enProgressIndividualApplicant?->moving_guidance,
            'occupation' => $enProgressIndividualApplicant?->occupation,
            'workplace_name' => $enProgressIndividualApplicant?->workplace_name,
            'workplace_kana' => $enProgressIndividualApplicant?->workplace_kana,
            'workplace_phone_number' => $enProgressIndividualApplicant?->workplace_phone_number,
            'industry' => $enProgressIndividualApplicant?->industry,
            'years_of_service' => $enProgressIndividualApplicant?->years_of_service,
            'annual_income' => $enProgressIndividualApplicant?->annual_income,
        ]),
    })"
    @input="handleInput($event)"
    @change="handleChange($event)"
>
    <div class="tw:h-[84px] tw:flex tw:justify-between tw:items-end">
        <div class="tw:h-[21px] tw:text-[1.2rem] tw:font-bold">
            申込人（個人）
        </div>
        <div class="tw:h-[84px] tw:flex tw:flex-col tw:justify-center tw:gap-y-[10px]">
            <div class="tw:flex tw:gap-x-[26px]">
                <x-button.light-blue class="tw:!h-[23px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">原契約統制</x-button.light-blue>
                <x-button.light-blue class="tw:!h-[23px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">入居審査・与信証跡</x-button.light-blue>
            </div>
            <div class="tw:flex tw:gap-x-[26px]">
                <x-button.light-blue class="tw:!h-[23px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">保証・リスク移転</x-button.light-blue>
                <x-button.light-blue class="tw:!h-[23px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">物件引渡・状態証跡</x-button.light-blue>
            </div>
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">氏名</div>
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">カナ（氏名）</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">性別</div>
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">生年月日（〇歳）</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">配偶者</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[42px] tw:flex tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="last_name" :value="$enProgressIndividualApplicant?->last_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
            <x-form.input name="first_name" :value="$enProgressIndividualApplicant?->first_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[234px] tw:h-[42px] tw:flex tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="last_kana" :value="$enProgressIndividualApplicant?->last_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
            <x-form.input name="first_kana" :value="$enProgressIndividualApplicant?->first_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.select name="gender" :value="$enProgressIndividualApplicant?->gender" :options="App\Models\EnProgressIndividualApplicant::GENDER" class="tw:!h-[40px]" />
        </div>
        <div class="tw:w-[182px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:flex">
            <x-form.input-date name="birth_date" :value="$enProgressIndividualApplicant?->birth_date" class="tw:!h-[40px] tw:!text-center" />
            <div class="tw:!h-[40px] tw:leading-[40px] tw:pr-5">({{ $enProgressIndividualApplicant?->birth_date?->age }})</div>
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.select name="spouse_flag" :value="$enProgressIndividualApplicant?->spouse_flag" :options="[0 => 'なし', 1 => 'あり']" class="tw:!h-[40px]" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">携帯電話番号</div>
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">メールアドレス</div>
        <div class="tw:w-[338px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">現住所</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="mobile_phone_number" :value="$enProgressIndividualApplicant?->mobile_phone_number" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[234px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="email" :value="$enProgressIndividualApplicant?->email" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[338px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">居住種別</div>
        <div class="tw:w-[104px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">居住年数</div>
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">転居理由</div>
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">引越し案内</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="residence_type" :value="$enProgressIndividualApplicant?->residence_type" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[104px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="residence_years" :value="$enProgressIndividualApplicant?->residence_years" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[234px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="move_reason" :value="$enProgressIndividualApplicant?->move_reason" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[234px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="moving_guidance" :value="$enProgressIndividualApplicant?->moving_guidance" class="tw:!h-[40px]" :border="false" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">職業</div>
        <div class="tw:w-[338px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">勤務先/学校名</div>
        <div class="tw:w-[338px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">カナ（職業名）</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="occupation" :value="$enProgressIndividualApplicant?->occupation" class="tw:!h-[40px] tw:text-center" :border="false" />
        </div>
        <div class="tw:w-[338px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="workplace_name" :value="$enProgressIndividualApplicant?->workplace_name" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[338px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="workplace_kana" :value="$enProgressIndividualApplicant?->workplace_kana" class="tw:!h-[40px]" :border="false" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">勤務先電話番号</div>
        <div class="tw:w-[338px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">勤務先所在地</div>
        <div class="tw:w-[104px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">業種</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">勤続年数</div>
        <div class="tw:w-[104px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">税込年収</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[182px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="workplace_phone_number" :value="$enProgressIndividualApplicant?->workplace_phone_number" class="tw:!h-[40px] tw:text-center" :border="false" />
        </div>
        <div class="tw:w-[338px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
        </div>
        <div class="tw:w-[104px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="industry" :value="$enProgressIndividualApplicant?->industry" class="tw:!h-[40px] tw:text-center" :border="false" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="years_of_service" :value="$enProgressIndividualApplicant?->years_of_service" class="tw:!h-[40px] tw:text-center" :border="false" />
        </div>
        <div class="tw:w-[104px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="annual_income" :value="$enProgressIndividualApplicant?->annual_income" class="tw:!h-[40px] tw:text-center" :border="false" />
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            window.enIndividualApplicant = (params = {}) => ({
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

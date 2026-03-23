<div
    x-data="enCorporateApplicant({
        initialFields: @js([
            'company_name' => $enProgressCorporateApplicant?->company_name,
            'company_kana' => $enProgressCorporateApplicant?->company_kana,
            'head_office_phone_number' => $enProgressCorporateApplicant?->head_office_phone_number,
            'head_office_fax_number' => $enProgressCorporateApplicant?->head_office_fax_number,
            'email' => $enProgressCorporateApplicant?->email,
            'industry' => $enProgressCorporateApplicant?->industry,
            'capital' => $enProgressCorporateApplicant?->capital,
            'number_of_employees' => $enProgressCorporateApplicant?->number_of_employees,
            'established_date' => $enProgressCorporateApplicant?->established_date?->format('Y/m/d'),
            'representative_last_name' => $enProgressCorporateApplicant?->representative_last_name,
            'representative_first_name' => $enProgressCorporateApplicant?->representative_first_name,
            'representative_last_kana' => $enProgressCorporateApplicant?->representative_last_kana,
            'representative_first_kana' => $enProgressCorporateApplicant?->representative_first_kana,
            'contact_last_name' => $enProgressCorporateApplicant?->contact_last_name,
            'contact_first_name' => $enProgressCorporateApplicant?->contact_first_name,
            'contact_last_kana' => $enProgressCorporateApplicant?->contact_last_kana,
            'contact_first_kana' => $enProgressCorporateApplicant?->contact_first_kana,
            'contact_department' => $enProgressCorporateApplicant?->contact_department,
            'contact_phone_number' => $enProgressCorporateApplicant?->contact_phone_number,
        ]),
    })"
    @input="handleInput($event)"
    @change="handleChange($event)"
>
    <div class="tw:h-[84px] tw:flex tw:justify-between tw:items-end">
        <div class="tw:h-[21px] tw:text-[1.2rem] tw:font-bold">
            申込人（法人）
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
        <div class="tw:w-[364px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">会社名</div>
        <div class="tw:w-[442px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">カナ（会社名）</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[364px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="company_name" :value="$enProgressCorporateApplicant?->company_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[442px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="company_kana" :value="$enProgressCorporateApplicant?->company_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[806px] tw:h-[21px] tw:text-left tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">本社所在地</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[806px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">本社電話番号</div>
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">本社FAX</div>
        <div class="tw:w-[338px] tw:h-[21px] tw:text-left tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">メールアドレス</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="head_office_phone_number" :value="$enProgressCorporateApplicant?->head_office_phone_number" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[234px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="head_office_fax_number" :value="$enProgressCorporateApplicant?->head_office_fax_number" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[338px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="email" :value="$enProgressCorporateApplicant?->email" class="tw:!h-[40px]" :border="false" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">職業</div>
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">資本金</div>
        <div class="tw:w-[156px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">従業員数</div>
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">設立年月日</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="industry" :value="$enProgressCorporateApplicant?->industry" class="tw:!h-[40px] tw:text-center" :border="false" />
        </div>
        <div class="tw:w-[338px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="capital" :value="$enProgressCorporateApplicant?->capital" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[156px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="number_of_employees" :value="$enProgressCorporateApplicant?->number_of_employees" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[182px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-date name="established_date" :value="$enProgressCorporateApplicant?->established_date?->format('Y/m/d')" class="tw:!h-[40px] tw:text-center" :border="false" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">代表者氏名</div>
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">カナ（代表者）</div>
        <div class="tw:w-[338px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">現住所</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[42px] tw:flex tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="representative_last_name" :value="$enProgressCorporateApplicant?->representative_last_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
            <x-form.input name="representative_first_name" :value="$enProgressCorporateApplicant?->representative_first_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[234px] tw:h-[42px] tw:flex tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="representative_last_kana" :value="$enProgressCorporateApplicant?->representative_last_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
            <x-form.input name="representative_first_kana" :value="$enProgressCorporateApplicant?->representative_first_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[338px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">担当者</div>
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">カナ（担当者）</div>
        <div class="tw:w-[156px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">所属部署</div>
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">担当電話番号</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[234px] tw:h-[42px] tw:flex tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="contact_last_name" :value="$enProgressCorporateApplicant?->contact_last_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
            <x-form.input name="contact_first_name" :value="$enProgressCorporateApplicant?->contact_first_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[234px] tw:h-[42px] tw:flex tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="contact_last_kana" :value="$enProgressCorporateApplicant?->contact_last_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
            <x-form.input name="contact_first_kana" :value="$enProgressCorporateApplicant?->contact_first_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[156px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="contact_department" :value="$enProgressCorporateApplicant?->contact_department" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
        <div class="tw:w-[182px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="contact_phone_number" :value="$enProgressCorporateApplicant?->contact_phone_number" class="tw:!h-[40px] tw:!text-center" :border="false" />
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            window.enCorporateApplicant = (params = {}) => ({
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

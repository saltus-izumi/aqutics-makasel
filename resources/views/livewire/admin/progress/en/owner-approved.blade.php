<div
    x-data="enOwnerApproved({
        initialFields: @js([
            'approval_method' => $enProgress?->approval_method,
            'approval_acquired_date' => $enProgress?->approval_acquired_date,
            'approval_condition' => $enProgress?->approval_condition,
            'approval_condition_detail' => $enProgress?->approval_condition_detail,
            'owner_approval_memo' => $enProgress?->owner_approval_memo,
        ]),
    })"
    @input="handleInput($event)"
    @change="handleChange($event)"
>
    <div class="tw:h-[21px] tw:text-[1.2rem] tw:font-bold">
        ３｜OWN承諾
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">承諾方式</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">承諾取得日</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">承諾条件</div>
        <div class="tw:w-[312px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">承諾条件（条件がある場合）</div>
        <div class="tw:w-[988px] tw:h-[21px] tw:pl-[26px] tw:text-left tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">OWN承諾メモ</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[42px] tw:flex tw:items-center tw:justify-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.select2 name="approval_method" :value="$enProgress?->approval_method" :options="App\Models\EnProgress::APPROVAL_METHOD" empty=" " class="tw:!w-[110px]" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-date name="approval_acquired_date" :value="$enProgress?->approval_acquired_date" class="tw:!h-[40px] tw:!text-center" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="approval_condition" :value="$enProgress?->approval_condition" :options="['1'=>'あり', '0'=>'なし']" class="tw:!w-[110px]" />
        </div>
        <div class="tw:w-[312px] tw:h-[42px] tw:text-left tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="approval_condition_detail" :value="$enProgress?->approval_condition_detail" class="tw:!h-[40px] tw:!text-left" :border="false" />
        </div>
        <div class="tw:w-[988px] tw:h-[42px] tw:text-left tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="owner_approval_memo" :value="$enProgress?->owner_approval_memo" class="tw:!h-[40px] tw:!text-left" :border="false" />
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            window.enOwnerApproved = (params = {}) => ({
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

<div
    class="tw:w-[832px]"
    x-data="teProgressStep1({
        initialFields: @js([
            'category1_master_id' => $teProgress?->category1_master_id,
            'category2_master_id' => $teProgress?->category2_master_id,
            'category3_master_id' => $teProgress?->category3_master_id,
            'title' => $teProgress?->title,
            'trading_company_1_id' => $teProgress?->trading_company_1_id,
            'trading_company_2_id' => $teProgress?->trading_company_2_id,
            'trading_company_3_id' => $teProgress?->trading_company_3_id,
        ]),
    })"
    @input="handleInput($event)"
    @change="handleChange($event)"
>
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3] tw:text-[1.1rem]">
        STEP１（実行担当：提案準備）
    </div>
    <div class="tw:w-full tw:px-[26px]">
        <div class="tw:w-full tw:pr-1 tw:text-right tw:text-[1.2rem]">
            最終更新日 {{ $teProgress->updated_at->format('Y/m/d') }}
        </div>
        <div class="tw:w-full tw:flex tw:flex-between">
            <div>
                <table class="tw:table-fixed">
                    <tr class="tw:h-[42px]">
                        <td class="tw:w-[52px] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">保守</td>
                        <td class="tw:w-[52px] tw:text-center tw:border tw:border-[#cccccc]">{{ $teProgress->investment?->facility_maintenance ? '○' : '' }}</td>
                        <td class="tw:w-[52px] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">3万</td>
                        <td class="tw:w-[52px] tw:text-center tw:border tw:border-[#cccccc]">{{ $teProgress->investment?->three_repair ? '○' : '' }}</td>
                        <td class="tw:w-[52px] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">安心</td>
                        <td class="tw:w-[52px] tw:text-center tw:border tw:border-[#cccccc]">{{ ($teProgress->investmentRoomResident?->ansin_support || $teProgress->investment?->has_emergency_support) ? '○' : '' }}</td>

                    </tr>
                </table>
            </div>
            <div class="tw:h-[42px] tw:flex-1 tw:flex tw:items-center tw:justify-end tw:gap-x-[26px]">
                <x-button.black class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[120px]">漏水標準価格</x-button.black>
                <x-button.black class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[120px]">設備標準価格</x-button.black>
                <x-button.black class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[120px]">適正判断基準</x-button.black>
            </div>
        </div>
        <div class="tw:h-[62px] tw:w-full tw:mt-[21px] tw:flex tw:gap-x-[26px]">
            <div class="tw:w-[234px]">
                大カテゴリ<br>
                <x-form.select-search name="category1_master_id" :value="$teProgress->category1_master_id" :options="$category1MasterOptions" />
            </div>
            <div class="tw:w-[234px]">
                中カテゴリ<br>
                <x-form.select-search name="category2_master_id" :value="$teProgress->category2_master_id" :options="$category2MasterOptions" />
            </div>
            <div class="tw:w-[234px]">
                小カテゴリ<br>
                <x-form.select-search name="category3_master_id" :value="$teProgress->category3_master_id" :options="$category3MasterOptions" />
            </div>
        </div>
        <div class="tw:h-[62px] tw:w-full">
            <div class="tw:w-[754px]">
                タイトル<br>
                <x-form.input name="title" :value="$teProgress->title"  />
            </div>
        </div>
        <div class="tw:h-[62px] tw:w-full tw:mt-[21px] tw:flex tw:gap-x-[26px]">
            <div class="tw:w-[234px]">
                1次対応指定業者<br>
                <x-form.select-search name="trading_company_1_id" :value="$teProgress->trading_company_1_id" :options="$tradingCompanyOptions" empty=" " />
            </div>
            <div class="tw:w-[234px]">
                2次対応指定業者<br>
                <x-form.select-search name="trading_company_2_id" :value="$teProgress->trading_company_2_id" :options="$tradingCompanyOptions" empty=" " />
            </div>
            <div class="tw:w-[234px]">
                3次対応指定業者<br>
                <x-form.select-search name="trading_company_3_id" :value="$teProgress->trading_company_3_id" :options="$tradingCompanyOptions" empty=" " />
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('teProgressStep1', (params = {}) => ({
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
            }));
        });
    </script>
@endpush

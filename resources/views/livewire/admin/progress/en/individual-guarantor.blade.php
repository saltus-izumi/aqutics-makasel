@php
    $occupantInitialFields = collect($enProgressGuarantors)->mapWithKeys(function ($enProgressGuarantor) {
        return [
            (string) $enProgressGuarantor->id => [
                'last_name' => $enProgressGuarantor?->last_name,
                'first_name' => $enProgressGuarantor?->first_name,
                'last_kana' => $enProgressGuarantor?->last_kana,
                'first_kana' => $enProgressGuarantor?->first_kana,
                'gender' => $enProgressGuarantor?->gender,
                'birth_date' => $enProgressGuarantor?->birth_date?->format('Y/m/d'),
                'relationship' => $enProgressGuarantor?->relationship,
                'mobile_phone_number' => $enProgressGuarantor?->mobile_phone_number,
                'workplace_or_school_name' => $enProgressGuarantor?->workplace_or_school_name,
                'workplace_or_school_kana' => $enProgressGuarantor?->workplace_or_school_kana,
            ],
        ];
    })->toArray();
@endphp

<div
    x-data="enOccupant({
        initialFields: @js($occupantInitialFields),
    })"
    @input="handleInput($event)"
    @change="handleChange($event)"
>
    <div class="tw:flex tw:flex-col tw:gap-y-[21px]">
    @foreach ($enProgressGuarantors as $enProgressGuarantor)
        <div data-occupant-id="{{ $enProgressGuarantor->id }}">
            <div class="tw:flex tw:justify-between">
                <div class="tw:h-[21px] tw:text-[1.2rem] tw:font-bold">
                    連帯保証人{{ $loop->iteration  }}
                </div>
                @if ($loop->iteration > 1)
                    <div
                        class="tw:text-red-600 tw:cursor-pointer"
                        x-on:click="if (!confirm('連帯保証人情報を削除します。よろしいですか？')) { return; } $wire.removeOccupant({{ $enProgressGuarantor->id }});"
                    >
                        ー連帯保証人{{ $loop->iteration }}
                    </div>
                @endif
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
                    <x-form.input name="last_name" :value="$enProgressGuarantor?->last_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
                    <x-form.input name="first_name" :value="$enProgressGuarantor?->first_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[234px] tw:h-[42px] tw:flex tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="last_kana" :value="$enProgressGuarantor?->last_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
                    <x-form.input name="first_kana" :value="$enProgressGuarantor?->first_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.select name="gender" :value="$enProgressGuarantor?->gender" :options="App\Models\EnProgressIndividualApplicant::GENDER" class="tw:!h-[40px]" />
                </div>
                <div class="tw:w-[182px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:flex">
                    <x-form.input-date name="birth_date" :value="$enProgressGuarantor?->birth_date" class="tw:!h-[40px] tw:!text-center" />
                    <div class="tw:!h-[40px] tw:leading-[40px] tw:pr-5">({{ $enProgressGuarantor?->birth_date?->age }})</div>
                </div>
                <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="relationship" :value="$enProgressGuarantor?->relationship" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
            </div>
            <div class="tw:flex">
                <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">携帯電話番号</div>
                <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">メールアドレス</div>
                <div class="tw:w-[338px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">現住所</div>
            </div>
            <div class="tw:flex">
                <div class="tw:w-[234px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
                    <x-form.input name="mobile_phone_number" :value="$enProgressGuarantor?->mobile_phone_number" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[234px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="workplace_or_school_name" :value="$enProgressGuarantor?->workplace_or_school_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[338px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="workplace_or_school_kana" :value="$enProgressGuarantor?->workplace_or_school_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
            </div>
            <div class="tw:flex">
                <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">自宅電話番号</div>
                <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">居住種別</div>
                <div class="tw:w-[104px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">居住年数</div>
                <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">ー</div>
            </div>
            <div class="tw:flex">
                <div class="tw:w-[234px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
                    <x-form.input name="phone_number" :value="$enProgressGuarantor?->phone_number" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[234px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="residence_type" :value="$enProgressGuarantor?->residence_type" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[104px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="residence_years" :value="$enProgressGuarantor?->residence_years" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[234px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
                    ー
                </div>
            </div>

            <div class="tw:h-[21px] tw:mt-[21px] tw:text-[1.2rem] tw:font-bold">
                連帯保証人の勤務先
            </div>
            <div class="tw:flex">
                <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">職業</div>
                <div class="tw:w-[338px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">勤務先/学校名</div>
                <div class="tw:w-[338px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">カナ（職業名）</div>
            </div>
            <div class="tw:flex">
                <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
                    <x-form.input name="occupation" :value="$enProgressGuarantor?->occupation" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[338px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="workplace_name" :value="$enProgressGuarantor?->workplace_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[338px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="workplace_kana" :value="$enProgressGuarantor?->workplace_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
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
                    <x-form.input name="workplace_phone_number" :value="$enProgressGuarantor?->workplace_phone_number" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[338px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="residence_type" :value="$enProgressGuarantor?->residence_type" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[104px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="industry" :value="$enProgressGuarantor?->industry" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="years_of_service" :value="$enProgressGuarantor?->years_of_service" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[104px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="annual_income" :value="$enProgressGuarantor?->annual_income" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
            </div>
            <div class="tw:flex">
                <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">資本金</div>
                <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">設立年月日</div>
                <div class="tw:w-[156px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">収入日</div>
                <div class="tw:w-[286px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">ー</div>
            </div>
            <div class="tw:flex">
                <div class="tw:w-[182px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
                    <x-form.input-number name="capital" :value="$enProgressGuarantor?->capital" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[182px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input-date name="established_date" :value="$enProgressGuarantor?->established_date" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[156px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="income_day" :value="$enProgressGuarantor?->income_day" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[286px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
                    ー
                </div>
            </div>

        </div>
    @endforeach
    </div>
    <div class="tw:text-[#4a86e8] tw:cursor-pointer" wire:click="addOccupant">
        ＋連帯保証人{{ count($enProgressGuarantors) + 1 }}
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            window.enOccupant = (params = {}) => ({
                fieldsByOccupant: params.initialFields || {},
                saveTimers: {},
                saveDelayMs: 400,
                lastSavedFields: {},
                init() {
                    Object.entries(this.fieldsByOccupant).forEach(([occupantId, fields]) => {
                        Object.entries(fields || {}).forEach(([fieldName, value]) => {
                            this.lastSavedFields[this.buildSaveKey(occupantId, fieldName)] = this.toComparableValue(value);
                        });
                    });
                },
                toComparableValue(value) {
                    return value === null || value === undefined ? '' : String(value);
                },
                buildSaveKey(occupantId, fieldName) {
                    return `${occupantId}:${fieldName}`;
                },
                updateFieldFromTarget(target) {
                    if (!target) {
                        return null;
                    }

                    const fieldName = (target.name || '').trim();
                    if (!fieldName) {
                        return null;
                    }

                    const occupantContainer = target.closest('[data-occupant-id]');
                    const occupantId = occupantContainer?.dataset?.occupantId;
                    if (!occupantId) {
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

                    if (!this.fieldsByOccupant[occupantId]) {
                        this.fieldsByOccupant[occupantId] = {};
                    }
                    this.fieldsByOccupant[occupantId][fieldName] = value;

                    return { occupantId, fieldName, value };
                },
                saveField(occupantId, fieldName, value) {
                    const saveKey = this.buildSaveKey(occupantId, fieldName);
                    const comparable = this.toComparableValue(value);
                    if (this.lastSavedFields[saveKey] === comparable) {
                        return;
                    }

                    this.lastSavedFields[saveKey] = comparable;
                    this.$wire.call('saveFieldByName', Number(occupantId), fieldName, value);
                },
                queueSave(occupantId, fieldName, value) {
                    const saveKey = this.buildSaveKey(occupantId, fieldName);
                    if (this.saveTimers[saveKey]) {
                        window.clearTimeout(this.saveTimers[saveKey]);
                    }

                    this.saveTimers[saveKey] = window.setTimeout(() => {
                        delete this.saveTimers[saveKey];
                        this.saveField(occupantId, fieldName, value);
                    }, this.saveDelayMs);
                },
                handleInput(event) {
                    const updated = this.updateFieldFromTarget(event?.target);
                    if (!updated) {
                        return;
                    }

                    this.queueSave(updated.occupantId, updated.fieldName, updated.value);
                },
                handleChange(event) {
                    const updated = this.updateFieldFromTarget(event?.target);
                    if (!updated) {
                        return;
                    }

                    const saveKey = this.buildSaveKey(updated.occupantId, updated.fieldName);
                    if (this.saveTimers[saveKey]) {
                        window.clearTimeout(this.saveTimers[saveKey]);
                        delete this.saveTimers[saveKey];
                    }

                    this.saveField(updated.occupantId, updated.fieldName, updated.value);
                },
            });
        })();
    </script>
@endpush

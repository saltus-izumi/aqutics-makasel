@php
    $occupantInitialFields = collect($enProgressOccupants)->mapWithKeys(function ($enProgressOccupant) {
        return [
            (string) $enProgressOccupant->id => [
                'last_name' => $enProgressOccupant?->last_name,
                'first_name' => $enProgressOccupant?->first_name,
                'last_kana' => $enProgressOccupant?->last_kana,
                'first_kana' => $enProgressOccupant?->first_kana,
                'gender' => $enProgressOccupant?->gender,
                'birth_date' => $enProgressOccupant?->birth_date?->format('Y/m/d'),
                'relationship' => $enProgressOccupant?->relationship,
                'mobile_phone_number' => $enProgressOccupant?->mobile_phone_number,
                'workplace_or_school_name' => $enProgressOccupant?->workplace_or_school_name,
                'workplace_or_school_kana' => $enProgressOccupant?->workplace_or_school_kana,
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
    @foreach ($enProgressOccupants as $enProgressOccupant)
        <div data-occupant-id="{{ $enProgressOccupant->id }}">
            <div class="tw:flex tw:justify-between">
                <div class="tw:h-[21px] tw:text-[1.2rem] tw:font-bold">
                    入居者{{ $loop->iteration  }}
                </div>
                @if ($loop->iteration > 1)
                    <div
                        class="tw:text-red-600 tw:cursor-pointer"
                        x-on:click="if (!confirm('入居者情報を削除します。よろしいですか？')) { return; } $wire.removeOccupant({{ $enProgressOccupant->id }});"
                    >
                        ー入居者{{ $loop->iteration }}
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
                    <x-form.input name="last_name" :value="$enProgressOccupant?->last_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
                    <x-form.input name="first_name" :value="$enProgressOccupant?->first_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[234px] tw:h-[42px] tw:flex tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="last_kana" :value="$enProgressOccupant?->last_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
                    <x-form.input name="first_kana" :value="$enProgressOccupant?->first_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.select name="gender" :value="$enProgressOccupant?->gender" :options="App\Models\EnProgressIndividualApplicant::GENDER" class="tw:!h-[40px]" />
                </div>
                <div class="tw:w-[182px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:flex">
                    <x-form.input-date name="birth_date" :value="$enProgressOccupant?->birth_date" class="tw:!h-[40px] tw:!text-center" />
                    <div class="tw:!h-[40px] tw:leading-[40px] tw:pr-5">({{ $enProgressOccupant?->birth_date?->age }})</div>
                </div>
                <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="relationship" :value="$enProgressOccupant?->relationship" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
            </div>
            <div class="tw:flex">
                <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">携帯電話番号</div>
                <div class="tw:w-[286px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">勤務先/学校名</div>
                <div class="tw:w-[286px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">カナ（職業名）</div>
            </div>
            <div class="tw:flex">
                <div class="tw:w-[234px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
                    <x-form.input name="mobile_phone_number" :value="$enProgressOccupant?->mobile_phone_number" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[286px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="workplace_or_school_name" :value="$enProgressOccupant?->workplace_or_school_name" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
                <div class="tw:w-[286px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="workplace_or_school_kana" :value="$enProgressOccupant?->workplace_or_school_kana" class="tw:!h-[40px] tw:!text-center" :border="false" />
                </div>
            </div>
        </div>
    @endforeach
    </div>
    <div class="tw:text-[#4a86e8] tw:cursor-pointer" wire:click="addOccupant">
        ＋入居者{{ count($enProgressOccupants) + 1 }}
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

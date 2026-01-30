@props([
    'name' => 'date',
    'value' => '',
    'year_from' => null,
    'year_to' => null,
    'clear_label' => '削除',
    'disable_label' => '無効',
    'is_error' => false,
])

@php
    $today = \Illuminate\Support\Carbon::today();
    $selected = null;
    if (!empty($value)) {
        try {
            $selected = \Illuminate\Support\Carbon::parse($value);
        } catch (\Throwable $e) {
            $selected = null;
        }
    }
    $baseYear = $today->year;
    $yearFrom = $year_from ?? ($baseYear - 10);
    $yearTo = $year_to ?? ($baseYear + 2);
    $years = range($yearFrom, $yearTo);
    $months = range(1, 12);
    $initialDate = $selected ??  \Illuminate\Support\Carbon::create(1900, 12, 1);
@endphp

<div
    x-data="calendarPicker({
        name: {{ json_encode($name) }},
        value: {{ json_encode($selected ? $selected->format('Y-m-d') : '') }},
        year: {{ $initialDate->year }},
        month: {{ $initialDate->month }},
        years: {{ json_encode($years) }},
    })"
    x-on:calendar-set.window="handleSetEvent($event)"
    @class([
        'tw:flex tw:flex-col tw:gap-2',
        $attributes->get('class'),
    ])
    {{ $attributes->except('class')->whereDoesntStartWith('wire:model') }}
>
    <input
        type="hidden"
        name="{{ $name }}"
        data-calendar-input
        x-ref="hiddenInput"
        x-model="selectedDate"
        {{ $attributes->whereStartsWith('wire:model') }}
    >

    <div class="tw:flex tw:items-center tw:gap-2 tw:flex-wrap">
        <select
            x-model.number="year"
            class="tw:select tw:select-bordered tw:h-[1.7rem] tw:bg-white tw:!w-auto tw:!pl-[5px] {{ $is_error ? 'tw:bg-red-100' : '' }}"
        >
            @foreach ($years as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
        <select
            x-model.number="month"
            class="tw:select tw:select-bordered tw:h-[1.7rem] tw:bg-white tw:!w-auto tw:!pl-[5px] {{ $is_error ? 'tw:bg-red-100' : '' }}"
        >
            @foreach ($months as $m)
                <option value="{{ $m }}">{{ $m }}</option>
            @endforeach
        </select>
        <button
            type="button"
            class="tw:btn tw:btn-sm tw:btn-outline tw:h-[1.7rem] tw:min-h-0 tw:px-3 tw:text-blue-600"
            x-on:click="disableDate()"
        >{{ $disable_label }}</button>
        <button
            type="button"
            class="tw:btn tw:btn-sm tw:btn-outline tw:h-[1.7rem] tw:min-h-0 tw:px-3 tw:text-red-600"
            x-on:click="clearDate()"
        >{{ $clear_label }}</button>
    </div>

    <div class="tw:grid tw:grid-cols-7 tw:gap-2 tw:text-center tw:text-[1rem] tw:text-gray-600">
        <template x-for="label in weekLabels" :key="label">
            <div class="tw:py-1" x-text="label"></div>
        </template>
    </div>

    <div class="tw:grid tw:grid-cols-7 tw:gap-2">
        <template x-for="cell in cells" :key="cell.key">
            <button
                type="button"
                class="tw:h-10 tw:w-[2rem] tw:rounded tw:text-[1rem] tw:flex tw:items-center tw:justify-center"
                :class="cell.day ? dayClass(cell) : 'tw:cursor-default tw:bg-transparent'"
                :disabled="!cell.day"
                x-on:click="selectDate(cell)"
            >
                <span x-text="cell.day ? cell.day : ''"></span>
            </button>
        </template>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('calendarPicker', (config = {}) => ({
                    name: config.name ?? '',
                    selectedDate: config.value ?? '',
                    year: config.year ?? new Date().getFullYear(),
                    month: config.month ?? (new Date().getMonth() + 1),
                    years: config.years ?? [],
                    weekLabels: ['月', '火', '水', '木', '金', '土', '日'],
                    cells: [],
                    todayStr: '',

                    init() {
                        this.selectedDate = String(this.selectedDate ?? '').replace(/^\s+|\s+$/g, '');
                        this.refreshToday();
                        this.syncFromSelectedDate();
                        if (!this.selectedDate) {
                            this.setToToday();
                        }
                        this.updateCalendar();
                        this.$watch('year', () => this.updateCalendar());
                        this.$watch('month', () => this.updateCalendar());
                        this.$watch('selectedDate', () => this.syncFromSelectedDate());
                    },

                    updateCalendar() {
                        this.ensureYearRange();
                        this.buildCalendar();
                    },

                    buildCalendar() {
                        const firstDay = new Date(this.year, this.month - 1, 1);
                        const daysInMonth = new Date(this.year, this.month, 0).getDate();
                        const firstDayIndex = (firstDay.getDay() + 6) % 7;
                        const totalCells = Math.ceil((firstDayIndex + daysInMonth) / 7) * 7;
                        const cells = [];

                        for (let i = 0; i < totalCells; i += 1) {
                            const dayNumber = i - firstDayIndex + 1;
                            if (dayNumber > 0 && dayNumber <= daysInMonth) {
                                const date = this.formatDate(this.year, this.month, dayNumber);
                                cells.push({
                                    key: `${this.year}-${this.month}-${dayNumber}`,
                                    day: dayNumber,
                                    date,
                                    isToday: this.isToday(date),
                                });
                            } else {
                                cells.push({
                                    key: `empty-${this.year}-${this.month}-${i}`,
                                    day: null,
                                });
                            }
                        }

                        this.cells = cells;
                    },

                    selectDate(cell) {
                        if (!cell?.day) {
                            return;
                        }
                        this.selectedDate = cell.date;
                        this.dispatchInput();
                    },

                    clearDate() {
                        this.selectedDate = '';
                        this.dispatchInput();
                    },

                    disableDate() {
                        this.selectedDate = 'ー';
                        this.dispatchInput();
                    },

                    handleSetEvent(event) {
                        var detail = event?.detail ?? {};
                        if (detail.name && this.name && detail.name !== this.name) {
                            return;
                        }
                        this.setDate(detail.value ?? '', { silent: detail.silent });
                    },

                    setDate(value, options = {}) {
                        const { silent = false } = options;
                        const next = String(value ?? '').replace(/^\s+|\s+$/g, '');
                        this.selectedDate = next;
                        if (!next) {
                            this.setToToday();
                            this.updateCalendar();
                        }
                        if (!silent) {
                            this.dispatchInput();
                        }
                    },

                    dispatchInput() {
                        const input = this.$refs.hiddenInput;
                        if (!input) {
                            return;
                        }
                        const value = this.selectedDate ?? '';
                        input.value = value;
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                        window.dispatchEvent(new CustomEvent('calendar-input', {
                            detail: { name: this.name, value },
                        }));
                    },

                    syncFromSelectedDate() {

                        if (!this.selectedDate) {
                            return;
                        }
                        const parts = String(this.selectedDate).split(/[-/]/);
                        if (parts.length < 3) {
                            return;
                        }
                        const year = Number(parts[0]);
                        const month = Number(parts[1]);
                        if (!Number.isNaN(year) && !Number.isNaN(month)) {
                            this.year = year;
                            this.month = month;
                        }
                    },

                    dayClass(cell) {
                        const base = 'tw:border tw:border-gray-200 tw:bg-white hover:tw:bg-gray-100';
                        const selected = this.selectedDate === cell.date ? 'tw:text-red-600 tw:border-black' : '';
                        const today = cell.isToday ? 'tw:font-semibold' : '';
                        return [base, selected, today].filter(Boolean).join(' ');
                    },

                    formatDate(year, month, day) {
                        const mm = String(month).padStart(2, '0');
                        const dd = String(day).padStart(2, '0');
                        return `${year}-${mm}-${dd}`;
                    },

                    isToday(date) {
                        return date === this.todayStr;
                    },

                    refreshToday() {
                        const now = new Date();
                        this.todayStr = this.formatDate(now.getFullYear(), now.getMonth() + 1, now.getDate());
                    },

                    setToToday() {
                        this.refreshToday();
                        const parts = this.todayStr.split('-');
                        if (parts.length < 2) {
                            return;
                        }
                        this.year = Number(parts[0]);
                        this.month = Number(parts[1]);
                    },

                    ensureYearRange() {
                        const year = Number(this.year);
                        if (Number.isNaN(year)) {
                            return;
                        }
                        if (!Array.isArray(this.years) || this.years.length === 0) {
                            this.years = this.buildYearRange(year);
                            return;
                        }
                        const min = Math.min(...this.years);
                        const max = Math.max(...this.years);
                        if (year < min || year > max) {
                            this.years = this.buildYearRange(year);
                        }
                    },

                    buildYearRange(center) {
                        const range = [];
                        for (let y = center - 5; y <= center + 5; y += 1) {
                            range.push(y);
                        }
                        return range;
                    },
                }));
            });
        </script>
    @endpush
@endonce

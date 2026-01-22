@props([
    'name' => '',
    'value' => '',
    'empty' => false,
    'options' => [],
    'placeholder' => '選択してください',
    'is_error' => false,
    'disabled' => false,
    'readonly' => false,
])

@php
    $emptyLabel = is_bool($empty) ? ($empty ? "\u{00A0}" : '') : $empty;
    $hasValue = !($value === '' || $value === null);
    // optionsをJavaScript用にフラット化（group対応）
    $flatOptions = [];
    foreach ($options as $key => $item) {
        if (is_array($item)) {
            foreach ($item as $key2 => $item2) {
                $flatOptions[] = [
                    'value' => (string) $key2,
                    'label' => $item2,
                    'group' => $key,
                ];
            }
        } else {
            $flatOptions[] = [
                'value' => (string) $key,
                'label' => $item,
                'group' => null,
            ];
        }
    }
    $selectedLabel = '';
    if ($hasValue) {
        $selectedValue = (string) $value;
        foreach ($flatOptions as $option) {
            if ($option['value'] === $selectedValue) {
                $selectedLabel = $option['label'];
                break;
            }
        }
    }
@endphp

<div
    x-data="selectSearch({
        name: {{ json_encode($name) }},
        options: {{ json_encode($flatOptions) }},
        value: '{{ $value }}',
        placeholder: '{{ $placeholder }}',
        emptyLabel: '{{ $emptyLabel }}',
    })"
    wire:ignore
    x-on:click.outside="close()"
    x-on:keydown.escape.window="close()"
    x-on:select-search-clear.window="clearSelection($event)"
    x-on:select-search-options.window="handleOptionsUpdate($event)"
    @class([
        'tw:relative',
        'tw:w-full' => !$attributes->has('class') || !str_contains($attributes->get('class'), 'tw:w-'),
        $attributes->get('class'),
    ])
    {{ $attributes->except('class')->whereDoesntStartWith('wire:model') }}
>
    <input
        type="hidden"
        name="{{ $name }}"
        x-ref="hiddenInput"
        x-model="selectedValue"
        {{ $attributes->whereStartsWith('wire:model') }}
        @disabled($disabled)
    >

    <button
        type="button"
        x-ref="button"
        x-on:click="toggle()"
        @disabled($disabled || $readonly)
        class="tw:w-full tw:flex tw:items-center tw:justify-between tw:border tw:rounded tw:px-3 tw:py-2 tw:bg-white tw:text-left tw:cursor-pointer hover:tw:border-gray-400 focus:tw:outline-none focus:tw:ring-2 focus:tw:ring-blue-500 {{ $is_error ? 'tw:border-red-500' : 'tw:border-gray-300' }} {{ $disabled ? 'tw:opacity-50 tw:cursor-not-allowed' : '' }}"
    >
        <span
            class="tw:block tw:truncate {{ $selectedLabel !== '' ? '' : 'tw:text-gray-400' }}"
            x-text="selectedValue !== '' ? selectedLabel : placeholder"
            x-bind:class="selectedValue !== '' ? '' : 'tw:text-gray-400'"
        >{{ $selectedLabel !== '' ? $selectedLabel : $placeholder }}</span>
        <svg class="tw:w-4 tw:h-4 tw:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div
        x-show="isOpen"
        x-transition:enter="tw:transition tw:ease-out tw:duration-100"
        x-transition:enter-start="tw:opacity-0 tw:scale-95"
        x-transition:enter-end="tw:opacity-100 tw:scale-100"
        x-transition:leave="tw:transition tw:ease-in tw:duration-75"
        x-transition:leave-start="tw:opacity-100 tw:scale-100"
        x-transition:leave-end="tw:opacity-0 tw:scale-95"
        class="tw:absolute tw:z-50 tw:mt-1 tw:w-full tw:bg-white tw:border tw:border-gray-300 tw:rounded tw:shadow-lg tw:z-600"
        x-cloak
    >
        <div class="tw:p-2 tw:border-b tw:border-gray-200">
            <input
                type="text"
                x-ref="search"
                x-model="search"
                placeholder="検索..."
                class="tw:w-full tw:px-3 tw:py-2 tw:border tw:border-gray-300 tw:rounded focus:tw:outline-none focus:tw:ring-2 focus:tw:ring-blue-500"
            >
        </div>

        <ul class="tw:max-h-60 tw:overflow-auto tw:py-1">
            <template x-if="emptyLabel">
                <li
                    x-on:click="select('', emptyLabel)"
                    x-on:mouseenter="hoveredIndex = -1"
                    x-on:mouseleave="hoveredIndex = null"
                    x-bind:class="hoveredIndex === -1 ? 'tw:bg-blue-100' : ''"
                    class="tw:px-3 tw:py-2 tw:cursor-pointer hover:tw:bg-gray-100"
                >
                    <span x-text="emptyLabel"></span>
                </li>
            </template>

            <template x-for="(item, index) in filteredOptions" :key="item.value">
                <li>
                    <template x-if="item.group && (index === 0 || filteredOptions[index - 1]?.group !== item.group)">
                        <div class="tw:px-3 tw:py-2 tw:font-bold tw:text-gray-700 tw:bg-gray-50" x-text="item.group"></div>
                    </template>

                    <div
                        x-on:click="select(item.value, item.label)"
                        x-on:mouseenter="hoveredIndex = index"
                        x-on:mouseleave="hoveredIndex = null"
                        x-bind:class="[
                            hoveredIndex === index ? 'tw:bg-blue-100' : '',
                            item.group ? 'tw:pl-6' : 'tw:pl-3'
                        ]"
                        class="tw:pr-3 tw:py-2 tw:cursor-pointer hover:tw:bg-gray-100"
                    >
                        <span class="tw:block tw:truncate" x-text="item.label"></span>
                    </div>
                </li>
            </template>

            <template x-if="filteredOptions.length === 0 && search">
                <li class="tw:px-3 tw:py-2 tw:text-gray-500">該当なし</li>
            </template>
        </ul>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('selectSearch', (config = {}) => ({
                    name: config.name ?? '',
                    options: config.options ?? [],
                    selectedValue: config.value ?? '',
                    selectedLabel: '',
                    placeholder: config.placeholder ?? '選択してください',
                    emptyLabel: config.emptyLabel ?? '',
                    isOpen: false,
                    search: '',
                    hoveredIndex: null,
                    selectedEmpty: false,

                    init() {
                        this.updateLabelFromValue();
                        this.$watch('options', () => {
                            this.updateLabelFromValue();
                        });
                    },

                    handleOptionsUpdate(event) {
                        const detail = event?.detail ?? {};
                        if (detail.name && this.name && detail.name !== this.name) {
                            return;
                        }
                        this.setOptions(detail.options ?? [], detail.value);
                    },

                    normalizeOptions(options) {
                        if (Array.isArray(options)) {
                            if (options.length === 0) {
                                return [];
                            }
                            if (options[0] && Object.prototype.hasOwnProperty.call(options[0], 'value')) {
                                return options.map((item) => ({
                                    value: String(item.value ?? ''),
                                    label: item.label ?? '',
                                    group: item.group ?? null,
                                }));
                            }
                        }
                        if (options && typeof options === 'object') {
                            const normalized = [];
                            Object.entries(options).forEach(([key, value]) => {
                                if (value && typeof value === 'object' && !Array.isArray(value)) {
                                    Object.entries(value).forEach(([childKey, childValue]) => {
                                        normalized.push({
                                            value: String(childKey),
                                            label: childValue,
                                            group: key,
                                        });
                                    });
                                } else {
                                    normalized.push({
                                        value: String(key),
                                        label: value,
                                        group: null,
                                    });
                                }
                            });
                            return normalized;
                        }
                        return [];
                    },

                    setOptions(options, value = null) {
                        this.options = this.normalizeOptions(options);
                        if (value !== null && value !== undefined) {
                            this.selectedValue = String(value);
                            this.selectedEmpty = this.selectedValue === '';
                        } else if (!this.options.some(o => o.value === this.selectedValue)) {
                            this.selectedValue = '';
                            this.selectedEmpty = false;
                        }
                        this.updateLabelFromValue();
                        this.dispatchInput();
                    },

                    dispatchInput() {
                        const input = this.$refs.hiddenInput;
                        if (!input) {
                            return;
                        }
                        input.value = this.selectedValue ?? '';
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    },

                    updateLabelFromValue() {
                        if (!this.selectedValue) {
                            this.selectedLabel = this.selectedEmpty ? (this.emptyLabel ?? '') : '';
                            return;
                        }
                        const found = this.options.find(o => o.value === this.selectedValue);
                        this.selectedLabel = found ? found.label : '';
                    },

                    get filteredOptions() {
                        if (!this.search) {
                            return this.options;
                        }
                        const query = this.search.toLowerCase();
                        return this.options.filter(o => {
                            return o.label.toLowerCase().includes(query) ||
                                (o.group && o.group.toLowerCase().includes(query));
                        });
                    },

                    toggle() {
                        this.isOpen ? this.close() : this.open();
                    },

                    open() {
                        this.isOpen = true;
                        this.search = '';
                        this.$nextTick(() => {
                            this.$refs.search?.focus();
                        });
                    },

                    close() {
                        this.isOpen = false;
                        this.search = '';
                    },

                    select(value, label) {
                        this.selectedValue = value;
                        this.selectedEmpty = !value;
                        this.selectedLabel = value ? label : (this.emptyLabel ?? '');
                        this.close();
                        this.$refs.button?.focus();
                        this.dispatchInput();
                    },

                    clearSelection(event) {
                        const detail = event?.detail ?? {};
                        if (detail.name && this.name && detail.name !== this.name) {
                            return;
                        }
                        this.selectedValue = '';
                        this.selectedEmpty = false;
                        this.selectedLabel = '';
                        this.close();
                        this.dispatchInput();
                    },
                }));
            });
        </script>
    @endpush
@endonce

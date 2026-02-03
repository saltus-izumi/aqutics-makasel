@props([
    'name' => '',
    'value' => '',
    'empty' => false,
    'options' => [],
    'placeholder' => '選択してください',
    'border' => true,
    'is_error' => false,
    'disabled' => false,
    'readonly' => false,
])

@php
    $flatOptions = [];
    foreach ($options as $key => $item) {
        $flatOptions[] = [
            'value' => (string) $key,
            'label' => $item,
        ];
    }

    $emptyLabel = $empty ? "\u{3000}" : '';
    $selectedLabel = '';
    $selectedValue = $value === null ? '' : (string) $value;
    if ($selectedValue !== '') {
        foreach ($flatOptions as $option) {
            if ($option['value'] === $selectedValue) {
                $selectedLabel = $option['label'];
                break;
            }
        }
    } elseif ($emptyLabel !== '') {
        $selectedLabel = $emptyLabel;
    }
@endphp

<div
    x-data="selectSearch2({
        name: {{ json_encode($name) }},
        options: {{ json_encode($flatOptions) }},
        value: '{{ $selectedValue }}',
        placeholder: {{ json_encode($placeholder) }},
        emptyLabel: {{ json_encode($emptyLabel) }},
    })"
    x-modelable="selectedValue"
    wire:ignore
    x-on:click.outside="close()"
    x-on:keydown.escape.window="close()"
    @class([
        'tw:relative',
        'tw:w-full' => !$attributes->has('class') || !str_contains($attributes->get('class'), 'tw:w-'),
        $attributes->get('class'),
    ])
    {{ $attributes->except('class')->whereDoesntStartWith('wire:') }}
>
    <input
        type="hidden"
        name="{{ $name }}"
        x-ref="hiddenInput"
        x-model="selectedValue"
        {{ $attributes->whereStartsWith('wire:') }}
        @disabled($disabled)
    >

    <button
        type="button"
        x-ref="button"
        x-on:click="toggle()"
        @disabled($disabled || $readonly)
        @class([
            'tw:w-full tw:flex tw:items-center tw:justify-between tw:rounded tw:px-3 tw:py-2 tw:bg-white tw:text-left tw:cursor-pointer focus:tw:outline-none focus:tw:ring-2 focus:tw:ring-blue-500',
            'tw:border hover:tw:border-gray-400' => $border,
            'tw:border-red-500' => $border && $is_error,
            'tw:border-gray-300' => $border && ! $is_error,
            'tw:opacity-50 tw:cursor-not-allowed' => $disabled,
        ])
    >
        <span
            class="tw:block tw:truncate"
            x-text="selectedLabel || placeholder"
            x-effect="$el.classList.toggle('tw:text-gray-400', !selectedLabel)"
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
        class="tw:absolute tw:z-50 tw:mt-1 tw:w-full tw:bg-white tw:border tw:border-gray-300 tw:rounded tw:shadow-lg"
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
                <li
                    x-on:click="select(item.value, item.label)"
                    x-on:mouseenter="hoveredIndex = index"
                    x-on:mouseleave="hoveredIndex = null"
                    x-bind:class="hoveredIndex === index ? 'tw:bg-blue-100' : ''"
                    class="tw:px-3 tw:py-2 tw:cursor-pointer hover:tw:bg-gray-100"
                >
                    <span class="tw:block tw:truncate" x-text="item.label"></span>
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
                Alpine.data('selectSearch2', (config = {}) => ({
                    name: config.name ?? '',
                    options: Array.isArray(config.options) ? config.options : [],
                    selectedValue: config.value ?? '',
                    selectedLabel: '',
                    placeholder: config.placeholder ?? '選択してください',
                    emptyLabel: config.emptyLabel ?? '',
                    isOpen: false,
                    search: '',
                    hoveredIndex: null,

                    init() {
                        this.updateLabelFromValue();
                        this.$watch('selectedValue', () => {
                            this.updateLabelFromValue();
                            this.dispatchInput();
                        });
                    },

                    get filteredOptions() {
                        if (!this.search) {
                            return this.options;
                        }
                        const query = this.search.toLowerCase();
                        return this.options.filter(o => String(o.label).toLowerCase().includes(query));
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
                        this.selectedValue = String(value ?? '');
                        this.selectedLabel = label ?? '';
                        this.close();
                        this.$refs.button?.focus();
                        this.$dispatch('selected', { name: this.name, value: this.selectedValue });
                    },

                    updateLabelFromValue() {
                        if (!this.selectedValue) {
                            this.selectedLabel = this.emptyLabel ? this.emptyLabel : '';
                            return;
                        }
                        const found = this.options.find(o => String(o.value) === String(this.selectedValue));
                        this.selectedLabel = found ? found.label : '';
                    },

                    dispatchInput() {
                        const input = this.$refs.hiddenInput;
                        if (!input) {
                            return;
                        }
                        input.value = this.selectedValue ?? '';
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    },

                    clearOptions() {
                        this.options = [];
                        this.selectedValue = '';
                        this.selectedLabel = '';
                        this.dispatchInput();
                    },

                    claerOpetions() {
                        this.clearOptions();
                    },
                }));
            });
        </script>
    @endpush
@endonce

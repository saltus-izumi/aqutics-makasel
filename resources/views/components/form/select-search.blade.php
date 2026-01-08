@props([
    'name' => '',
    'value' => '',
    'empty' => '',
    'options' => [],
    'is_error' => false,
    'placeholder' => '選択してください',
])

@php
    // wire:modelの取得（wire:model.live="owner_id" → "owner_id"）
    $wireModelAttr = $attributes->whereStartsWith('wire:model');
    $wireModel = '';
    foreach ($wireModelAttr as $key => $value) {
        $wireModel = $value;
        break;
    }

    // optionsをJavaScript用にフラット化
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
@endphp

<div
    x-ref="root"
    x-data="selectSearchComponent({
        options: {{ json_encode($flatOptions) }},
        value: '{{ $value }}',
        placeholder: '{{ $placeholder }}',
        emptyLabel: '{{ $empty }}',
        wireModel: '{{ $wireModel }}',
        inputName: '{{ $name }}',
    })"
    x-on:click.outside="close()"
    x-on:keydown.escape.window="close()"
    @update-select-options.window="handleOptionsUpdate($event.detail)"
    @class([
        'tw:relative',
        'tw:w-full' => !$attributes->has('class') || !str_contains($attributes->get('class'), 'tw:w-'),
        $attributes->get('class'),
    ])
    {{ $attributes->except('class') }}
>
    {{-- 隠しinput（フォーム送信用） --}}
    <input type="hidden" name="{{ $name }}" x-model="selectedValue">

    {{-- 選択表示エリア --}}
    <button
        type="button"
        x-ref="button"
        x-on:click="toggle()"
        class="tw:w-full tw:flex tw:items-center tw:justify-between tw:border tw:rounded tw:px-3 tw:py-2 tw:bg-white tw:text-left tw:cursor-pointer hover:tw:border-gray-400 focus:tw:outline-none focus:tw:ring-2 focus:tw:ring-blue-500 {{ $is_error ? 'tw:border-red-500' : 'tw:border-gray-300' }}"
    >
        <span x-text="selectedLabel || placeholder" x-bind:class="selectedLabel ? '' : 'tw:text-gray-400'"></span>
        <svg class="tw:w-4 tw:h-4 tw:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    {{-- ドロップダウン --}}
    <div
        wire:ignore
        x-show="isOpen"
        x-transition:enter="tw:transition tw:ease-out tw:duration-100"
        x-transition:enter-start="tw:opacity-0 tw:scale-95"
        x-transition:enter-end="tw:opacity-100 tw:scale-100"
        x-transition:leave="tw:transition tw:ease-in tw:duration-75"
        x-transition:leave-start="tw:opacity-100 tw:scale-100"
        x-transition:leave-end="tw:opacity-0 tw:scale-95"
        class="tw:absolute tw:z-50 tw:mt-1 tw:w-full tw:bg-white tw:border-b tw:border-gray-300 tw:rounded tw:shadow-lg"
        x-cloak
    >
        {{-- 検索入力 --}}
        <div class="tw:p-2 tw:border-b tw:border-gray-200">
            <input
                type="text"
                x-ref="search"
                x-model="search"
                placeholder="検索..."
                class="tw:w-full tw:px-3 tw:py-2 tw:border tw:border-gray-300 tw:rounded focus:tw:outline-none focus:tw:ring-2 focus:tw:ring-blue-500"
            >
        </div>

        {{-- オプションリスト --}}
        <ul
            class="tw:max-h-60 tw:overflow-auto tw:py-1"
        >
            {{-- 空オプション --}}
            <template x-if="emptyLabel">
                <li
                    x-on:click="select('', emptyLabel)"
                    class="tw:px-3 tw:py-2 tw:cursor-pointer hover:tw:bg-gray-100"
                >
                    <span x-text="emptyLabel"></span>
                </li>
            </template>

            <template x-for="(item, index) in filteredOptions" :key="item.value">
                <li>
                    {{-- グループヘッダー --}}
                    <template x-if="item.group && (index === 0 || filteredOptions[index - 1]?.group !== item.group)">
                        <div class="tw:px-3 tw:py-2 tw:font-bold tw:text-gray-700 tw:bg-gray-50" x-text="item.group"></div>
                    </template>

                    {{-- オプション --}}
                    <div
                        x-on:click="select(item.value, item.label)"
                        x-bind:class="item.group ? 'tw:pl-6' : 'tw:pl-3'"
                        class="tw:pr-3 tw:py-2 tw:cursor-pointer hover:tw:bg-gray-100"
                    >
                        <span x-text="item.label"></span>
                    </div>
                </li>
            </template>

            {{-- 検索結果なし --}}
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
                Alpine.data('selectSearchComponent', (config = {}) => ({
                    options: config.options ?? [],
                    selectedValue: config.value ?? '',
                    selectedLabel: '',
                    placeholder: config.placeholder ?? '選択してください',
                    emptyLabel: config.emptyLabel ?? '',
                    wireModel: config.wireModel ?? '',
                    inputName: config.inputName ?? '',
                    isOpen: false,
                    search: '',

                    init() {
                        // 初期値のラベルを設定
                        this.updateLabelFromValue();

                        // Livewireからのオプション更新イベントをリッスン
                        this.$watch('options', () => {
                            this.updateLabelFromValue();
                        });
                    },

                    updateLabelFromValue() {
                        if (this.selectedValue) {
                            const found = this.options.find(o => o.value === this.selectedValue);
                            this.selectedLabel = found ? found.label : '';
                        } else {
                            this.selectedLabel = '';
                        }
                    },

                    // Livewireからのオプション更新イベントを処理
                    handleOptionsUpdate(detail) {
                        if (detail.name !== this.inputName) return;

                        // PHPの連想配列をフラット配列に変換
                        const newOptions = [];
                        const rawOptions = detail.options || {};
                        for (const [key, value] of Object.entries(rawOptions)) {
                            newOptions.push({
                                value: String(key),
                                label: value,
                                group: null,
                            });
                        }

                        this.options = newOptions;
                        this.selectedValue = '';
                        this.selectedLabel = '';
                    },

                    get filteredOptions() {
                        if (!this.search) {
                            return this.options;
                        }
                        const query = this.search.toLowerCase();
                        return this.options.filter(o =>
                            o.label.toLowerCase().includes(query) ||
                            (o.group && o.group.toLowerCase().includes(query))
                        );
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
                        this.selectedLabel = value ? label : '';
                        this.close();
                        this.$refs.button?.focus();

                        // wire:modelがある場合はLivewireプロパティを直接更新
                        if (this.wireModel && this.$wire) {
                            this.$wire.set(this.wireModel, value);
                        }

                        // 選択変更イベントを発火（Livewire/Alpine.js連携用）
                        const root = this.$refs.root;
                        const hiddenInput = root?.querySelector('input[type="hidden"]');
                        if (hiddenInput) {
                            this.$dispatch('select-changed', { name: hiddenInput.name, value: value });
                        }
                    },
                }));
            });
        </script>
    @endpush
@endonce

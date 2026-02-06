@props([
    'type' => 'text',
    'name' => '',
    'palceholder' => '',
    'after_label' => '',
    'is_error' => false,
    'border' => true,
])
<input type="{{ $type }}" name="{{ $name }}"
    x-data="numberInput()"
    x-init="onInit($el)"
    x-on:focus="onFocus($event)"
    x-on:blur="onBlur($event)"
    x-on:input="onInput($event)"
    @class([
        'tw:border tw:border-gray-300' => $border,
        'tw:placeholder:text-gray-400 tw:py-2 tw:px-3 tw:w-full tw:rounded-md',
        'tw:bg-white' => !$attributes->has('class') || !str_contains($attributes->get('class'), 'tw:bg-'),
        'tw:read-only:bg-gray-100',
        'tw:inline-block' => $after_label,
        $attributes->get('class'),
        'tw:!bg-red-100' => $is_error,
    ])
    {{ $attributes->except('class') }}
    placeholder="{{ $palceholder }}">
@if ($after_label)
    {{ $after_label}}
@endif

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('numberInput', () => ({
                    toHalfWidthDigits(value) {
                        return String(value ?? '').replace(/[０-９]/g, (ch) =>
                            String.fromCharCode(ch.charCodeAt(0) - 0xFEE0)
                        );
                    },
                    normalize(value) {
                        const halfWidth = this.toHalfWidthDigits(value);
                        return halfWidth.replace(/[^\d]/g, '');
                    },
                    formatComma(value) {
                        const digits = this.normalize(value);
                        if (digits === '') {
                            return '';
                        }
                        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    },
                    onInput(event) {
                        event.target.value = this.normalize(event.target.value);
                    },
                    onInit(el) {
                        el.value = this.formatComma(el.value);
                    },
                    onFocus(event) {
                        event.target.value = this.normalize(event.target.value);
                    },
                    onBlur(event) {
                        event.target.value = this.formatComma(event.target.value);
                    },
                }));
            });
        </script>
    @endpush
@endonce

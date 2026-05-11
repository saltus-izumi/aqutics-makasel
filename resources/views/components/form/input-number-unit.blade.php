@props([
    'type' => 'text',
    'name' => '',
    'palceholder' => '',
    'is_error' => false,
    'border' => true,
    'unit' => '',
])
<div
    @class([
        'tw:border tw:border-gray-300' => $border,
        'tw:w-full tw:rounded-md tw:flex tw:items-center',
        'tw:bg-white' => !$attributes->has('class') || !str_contains($attributes->get('class'), 'tw:bg-'),
        $attributes->get('class'),
        'tw:bg-red-100' => $is_error,

    ])
>
    <input type="{{ $type }}" name="{{ $name }}"
        x-data="numberUnitInput()"
        x-init="onInit($el)"
        x-on:focus="onFocus($event)"
        x-on:blur="onBlur($event)"
        x-on:input="onInput($event)"
        @class([
            'tw:placeholder:text-gray-400 tw:py-2 tw:pl-3 tw:mr-1 tw:rounded-md tw:w-[calc(100%-2rem)]',
            'tw:bg-white' => !$attributes->has('textClass') || !str_contains($attributes->get('textClass'), 'tw:bg-'),
            'tw:read-only:bg-gray-100',
            $attributes->get('textClass'),
            'tw:bg-red-100' => $is_error,
            
        ])
        {{ $attributes->except(['class', 'textClass']) }}
        placeholder="{{ $palceholder }}">
    {{ $unit}}
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('numberUnitInput', () => ({
                    toHalfWidthDigits(value) {
                        return String(value ?? '')
                            .replace(/[０-９]/g, (ch) =>
                                String.fromCharCode(ch.charCodeAt(0) - 0xFEE0)
                            )
                            .replace(/[＋]/g, '+')
                            .replace(/[－−]/g, '-');
                    },
                    extractSignAndDigits(value) {
                        const cleaned = this.toHalfWidthDigits(value).replace(/[^\d+-]/g, '');
                        if (cleaned === '') {
                            return { sign: '', digits: '' };
                        }
                        const sign = cleaned.startsWith('-') ? '-' : cleaned.startsWith('+') ? '+' : '';
                        const digits = cleaned.replace(/[+-]/g, '');
                        return { sign, digits };
                    },
                    normalize(value) {
                        const { sign, digits } = this.extractSignAndDigits(value);
                        if (digits === '') {
                            return sign;
                        }
                        return sign + digits;
                    },
                    formatComma(value) {
                        const { sign, digits } = this.extractSignAndDigits(value);
                        if (digits === '') {
                            return sign;
                        }
                        return sign + digits.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
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

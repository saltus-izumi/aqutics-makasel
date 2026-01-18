@props([
    'name' => '',
    'value' => '',
    'checked' => false,
    'label' => '',
    'is_error' => false,
    'label_class' => ''
])
<label class="tw:flex tw:items-center tw:cursor-pointer">
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}"
        @class([
            'tw:checkbox tw:checkbox-sm tw:checkbox-primary tw:border-1 tw:border-solid',
            'tw:bg-white' => !$attributes->has('class') || !str_contains($attributes->get('class'), 'tw:bg-'),
            'tw:read-only:bg-gray-100',
            $attributes->get('class'),
            'tw:bg-red-100' => $is_error,
        ])
        {{ $attributes->except('class') }}
        {{ $checked ? 'checked' : '' }}
    >
    <div class="tw:ml-1 tw:font-normal {{ $label_class }}">{{ $slot }}</div>
</label>

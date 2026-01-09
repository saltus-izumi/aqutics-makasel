@props([
    'type' => 'text',
    'name' => '',
    'palceholder' => '',
    'errors',
])
<textarea type="{{ $type }}" name="{{ $name }}" {{ $attributes->merge([
    'class' => 'tw:border tw:border-gray-300 tw:p-1 tw:w-full tw:bg-white' . ($errors->has($name) ? ' tw:bg-red-100 ' : ''),
]) }} placeholder="{{ $palceholder }}">{{ $slot }}</textarea>

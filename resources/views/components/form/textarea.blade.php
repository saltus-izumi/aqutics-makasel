@props([
    'type' => 'text',
    'name' => '',
    'palceholder' => '',
    'errors',
])
<textarea type="{{ $type }}" name="{{ $name }}" {{ $attributes->merge([
    'class' => 'tw:textarea tw:min-h-auto tw:textarea-bordered tw:leading-[13pt] tw:w-full tw:bg-white' . ($errors->has($name) ? ' tw:bg-red-100 ' : ''),
]) }} placeholder="{{ $palceholder }}">{{ $slot }}</textarea>

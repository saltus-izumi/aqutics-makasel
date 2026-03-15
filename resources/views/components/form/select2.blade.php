@props([
    'name' => '',
    'value' => '',
    'empty' => '',
    'options' => [],
    'is_error' => false,
])
<select name="{{ $name }}" {{ $attributes->merge([
    'class' => 'tw:select tw:select-bordered tw:h-[1.4rem] tw:w-full tw:text-center tw:bg-[#e8eaed] tw:!px-[5px] tw:rounded-full' . ($is_error ? ' tw:bg-red-100 ' : ''),
]) }} >
    @if ($empty)
        <option value="">{{ $empty }}</option>
    @endif
    @foreach ($options as $key => $item)
        <option value="{{ $key }}" {{ $key == $value ? 'selected' : '' }}>{{$item}}</option>
    @endforeach
</select>

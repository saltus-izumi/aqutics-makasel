
@props([
    'type' => 'button'
])
<button type="{{ $type }}" {{ $attributes->merge(['class' => 'bg-[#ffbf00c9] hover:bg-[#ffbf0085] rounded-2xl text-[#585d63] px-[20px] py-[8px] font-bold text-[2rem] min-w-[200px] text-center border-none outline-none']) }}>{{ $slot }}</button>

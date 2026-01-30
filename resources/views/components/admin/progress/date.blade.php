@props([
    'progress' => null,
    'field' => '',
    'format' => 'm/d',
])
@if ($progress->{$field . '_state'} === 0)
    　
@elseif ($progress->{$field . '_state'} === 2)
    ー
@else
    <x-tooltip :text="$progress?->{$field}?->format('Y/m/d')">
        {{ $progress?->{$field}?->format('m/d') ?? '　' }}
    </x-tooltip>
@endif


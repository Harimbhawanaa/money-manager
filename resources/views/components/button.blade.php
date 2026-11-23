@props(['variant' => 'primary', 'href' => null])

@php
    $base = 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition cursor-pointer';
    $style = match ($variant) {
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700',
        'secondary' => 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50',
        default => 'bg-emerald-600 text-white hover:bg-emerald-700',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "$base $style"]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => "$base $style"]) }}>{{ $slot }}</button>
@endif

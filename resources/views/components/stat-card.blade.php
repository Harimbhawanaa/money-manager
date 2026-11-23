@props(['label', 'amount', 'tone' => 'slate', 'note' => null])

@php
    $colors = match ($tone) {
        'green' => 'text-emerald-600',
        'red' => 'text-rose-600',
        default => 'text-slate-800',
    };
@endphp

<x-card>
    <p class="text-sm text-slate-500">{{ $label }}</p>
    <p class="mt-1 text-2xl font-bold {{ $colors }}">
        <x-money :amount="$amount" />
    </p>
    @if ($note)
        <p class="mt-1 text-xs {{ $colors }}">{{ $note }}</p>
    @endif
</x-card>

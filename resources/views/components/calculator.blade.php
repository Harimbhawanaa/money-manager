@php
    $keys = [
        ['C', 'danger'], ['DEL', 'muted'], ['(', 'muted'], [')', 'muted'],
        ['7', ''], ['8', ''], ['9', ''], ['/', 'op'],
        ['4', ''], ['5', ''], ['6', ''], ['*', 'op'],
        ['1', ''], ['2', ''], ['3', ''], ['-', 'op'],
        ['0', ''], ['.', ''], ['=', 'eq'], ['+', 'op'],
    ];
@endphp

<div class="fixed bottom-4 right-4 z-50 flex flex-col items-end gap-2">
    <div id="calc-panel" class="hidden w-72 rounded-2xl bg-white border border-slate-200 shadow-xl p-3">
        <div id="calc-display"
             class="mb-3 rounded-lg bg-slate-900 text-white text-right text-2xl px-3 py-3 overflow-x-auto whitespace-nowrap">0</div>

        <div class="grid grid-cols-4 gap-2">
            @foreach ($keys as [$key, $kind])
                @php
                    $style = match ($kind) {
                        'danger' => 'bg-rose-100 text-rose-700 hover:bg-rose-200',
                        'muted' => 'bg-slate-100 text-slate-600 hover:bg-slate-200',
                        'op' => 'bg-amber-100 text-amber-700 hover:bg-amber-200',
                        'eq' => 'bg-emerald-600 text-white hover:bg-emerald-700',
                        default => 'bg-slate-50 text-slate-800 hover:bg-slate-100 border border-slate-200',
                    };
                    $label = match ($key) { '/' => '÷', '*' => '×', default => $key };
                @endphp
                <button type="button" data-calc="{{ $key }}"
                        class="rounded-lg py-2.5 text-base font-medium cursor-pointer {{ $style }}">{{ $label }}</button>
            @endforeach
        </div>

        <button type="button" data-calc="USE"
                class="mt-3 w-full rounded-lg bg-sky-600 text-white text-sm py-2 hover:bg-sky-700 cursor-pointer">
            Pakai hasil di kolom nominal
        </button>
    </div>

    <button id="calc-toggle" type="button"
            class="rounded-full bg-emerald-600 text-white shadow-lg px-4 py-3 text-sm font-semibold hover:bg-emerald-700 cursor-pointer">
        🧮
    </button>
</div>

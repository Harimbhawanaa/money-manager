@php
    $selectedCategory = old('category_id', $transaction->category_id ?? '');
    $selectedDate = old('transaction_date', isset($transaction) ? $transaction->transaction_date->format('Y-m-d') : now()->format('Y-m-d'));
@endphp

@if ($errors->any())
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium mb-1">Kategori</label>
        <select name="category_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="">— Pilih kategori —</option>
            <optgroup label="Pengeluaran">
                @foreach ($categories->where('type', 'expense') as $category)
                    <option value="{{ $category->id }}" @selected((string) $selectedCategory === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </optgroup>
            <optgroup label="Pemasukan">
                @foreach ($categories->where('type', 'income') as $category)
                    <option value="{{ $category->id }}" @selected((string) $selectedCategory === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </optgroup>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Nominal (Rp)</label>
        <input type="number" name="amount" min="1" step="1" required data-amount-input
               value="{{ old('amount', $transaction->amount ?? '') }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="contoh: 50000">
        <p class="mt-1 text-xs text-slate-400">Bingung ngitung? Pakai tombol 🧮 Kalkulator di pojok kanan bawah.</p>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Tanggal</label>
        <input type="date" name="transaction_date" required value="{{ $selectedDate }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Keterangan</label>
        <input type="text" name="description" maxlength="255"
               value="{{ old('description', $transaction->description ?? '') }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="opsional">
    </div>

    <div class="flex gap-2">
        <x-button type="submit">Simpan</x-button>
        <x-button :href="route('transactions.index')" variant="secondary">Batal</x-button>
    </div>
</div>

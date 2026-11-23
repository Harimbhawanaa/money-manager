@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    <h1 class="text-2xl font-bold">Kategori</h1>

    @if ($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <x-card>
        <h2 class="font-semibold mb-3">Tambah Kategori</h2>
        <form action="{{ route('categories.store') }}" method="POST" class="flex flex-wrap items-end gap-3">
            @csrf
            <div>
                <label class="block text-xs text-slate-500 mb-1">Nama</label>
                <input type="text" name="name" required maxlength="60" value="{{ old('name') }}"
                       class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Tipe</label>
                <select name="type" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="expense" @selected(old('type') === 'expense')>Pengeluaran</option>
                    <option value="income" @selected(old('type') === 'income')>Pemasukan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Warna</label>
                <input type="color" name="color" value="{{ old('color', '#6366f1') }}"
                       class="h-10 w-14 rounded-lg border border-slate-300">
            </div>
            <x-button type="submit">Tambah</x-button>
        </form>
    </x-card>

    <div class="grid gap-4 md:grid-cols-2">
        @foreach ([['Pengeluaran', $expenseCategories], ['Pemasukan', $incomeCategories]] as [$label, $items])
            <x-card>
                <h2 class="font-semibold mb-3">{{ $label }}</h2>
                <ul class="divide-y divide-slate-100 text-sm">
                    @forelse ($items as $category)
                        <li class="flex items-center justify-between py-2">
                            <span>
                                <span class="inline-block w-2.5 h-2.5 rounded-full mr-1.5" style="background-color: {{ $category->color ?? '#94a3b8' }}"></span>
                                {{ $category->name }}
                                <span class="text-xs text-slate-400">({{ $category->transactions_count }} transaksi)</span>
                            </span>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                  onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:underline cursor-pointer">Hapus</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-2 text-slate-500">Belum ada kategori.</li>
                    @endforelse
                </ul>
            </x-card>
        @endforeach
    </div>
@endsection

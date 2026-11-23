@props(['transaction'])

@php($isIncome = $transaction->type === 'income')

<tr class="border-t border-slate-100">
    <td class="py-3 pr-3 whitespace-nowrap text-slate-500">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
    <td class="py-3 pr-3 whitespace-nowrap">
        <span class="inline-block w-2.5 h-2.5 rounded-full mr-1.5"
              style="background-color: {{ $transaction->category->color ?? '#94a3b8' }}"></span>
        {{ $transaction->category->name }}
    </td>
    <td class="py-3 pr-3 text-slate-600">{{ $transaction->description ?: '-' }}</td>
    <td class="py-3 pr-3 text-right whitespace-nowrap font-semibold {{ $isIncome ? 'text-emerald-600' : 'text-rose-600' }}">
        {{ $isIncome ? '+' : '-' }} <x-money :amount="$transaction->amount" />
    </td>
    <td class="py-3 text-right whitespace-nowrap">
        <a href="{{ route('transactions.edit', $transaction) }}" class="text-sm text-sky-600 hover:underline">Edit</a>
        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline"
              onsubmit="return confirm('Hapus transaksi ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="ml-2 text-sm text-rose-600 hover:underline cursor-pointer">Hapus</button>
        </form>
    </td>
</tr>

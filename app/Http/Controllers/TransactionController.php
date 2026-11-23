<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Support\Months;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $categoryId = $request->input('category_id');
        $type = $request->input('type');
        $search = trim((string) $request->input('search', ''));
        $base = Transaction::query()
            ->when($month, fn ($q) => $q->whereMonth('transaction_date', $month))
            ->when($year, fn ($q) => $q->whereYear('transaction_date', $year))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when(in_array($type, ['income', 'expense'], true), fn ($q) => $q->where('type', $type))
            ->when($search !== '', fn ($q) => $q->where(function ($sub) use ($search) {
                $sub->where('description', 'like', '%' . $search . '%')
                    ->orWhereHas('category', fn ($cat) => $cat->where('name', 'like', '%' . $search . '%'));
            }));
        $totalIncome = (clone $base)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $base)->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;
        $transactions = (clone $base)->with('category')->orderByDesc('transaction_date')->orderByDesc('id')->paginate(12)->withQueryString();
        $categories = Category::withCount('transactions')->orderBy('type')->orderBy('name')->get();
        $months = Months::NAMES;
        return view('transactions.index', compact('transactions', 'categories', 'months', 'totalIncome', 'totalExpense', 'balance', 'month', 'year', 'categoryId', 'type', 'search'));
    }

    public function create()
    {
        return view('transactions.create', ['categories' => Category::orderBy('type')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        Transaction::create($this->validated($request));
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Transaction $transaction)
    {
        return view('transactions.edit', ['transaction' => $transaction, 'categories' => Category::orderBy('type')->orderBy('name')->get()]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $transaction->update($this->validated($request));
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
        ]);
        $data['type'] = Category::findOrFail($data['category_id'])->type;
        return $data;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Support\Months;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $month = max(1, min(12, (int) $request->input('month', now()->month)));
        $year = max(2000, min(2100, (int) $request->input('year', now()->year)));

        $categories = Category::where('type', 'expense')->orderBy('name')->get();
        $budgets = Budget::where('month', $month)->where('year', $year)->get()->keyBy('category_id');
        $spent = Transaction::where('type', 'expense')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $totalBudget = (int) $budgets->sum('amount');
        $totalSpent = (int) $spent->sum();
        $remaining = $totalBudget - $totalSpent;
        $usage = $totalBudget > 0 ? round($totalSpent / $totalBudget * 100, 1) : 0;
        $statusCounts = ['on_track' => 0, 'almost' => 0, 'over' => 0];
        $budgetedSpent = 0;
        foreach ($categories as $category) {
            $amount = (int) ($budgets->get($category->id)?->amount ?? 0);
            if ($amount <= 0) continue;
            $used = (int) ($spent[$category->id] ?? 0);
            $budgetedSpent += $used;
            $percent = $used / $amount * 100;
            if ($percent > 100) $statusCounts['over']++;
            elseif ($percent >= 80) $statusCounts['almost']++;
            else $statusCounts['on_track']++;
        }
        $unbudgetedSpent = max(0, $totalSpent - $budgetedSpent);
        $months = Months::NAMES;

        return view('budgets.index', compact(
            'categories', 'budgets', 'spent', 'month', 'year', 'months',
            'totalBudget', 'totalSpent', 'remaining', 'usage', 'statusCounts', 'unbudgetedSpent'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2000,2100'],
            'budgets' => ['required', 'array'],
            'budgets.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $validCategoryIds = Category::where('type', 'expense')->pluck('id')->map(fn ($id) => (string) $id)->all();
        foreach ($data['budgets'] as $categoryId => $amount) {
            if (!in_array((string) $categoryId, $validCategoryIds, true)) continue;
            $attributes = ['category_id' => (int) $categoryId, 'month' => $data['month'], 'year' => $data['year']];
            if ($amount !== null && (int) $amount > 0) {
                Budget::updateOrCreate($attributes, ['amount' => (int) $amount]);
            } else {
                Budget::where($attributes)->delete();
            }
        }

        return redirect()->route('budgets.index', ['month' => $data['month'], 'year' => $data['year']])
            ->with('success', 'Budget berhasil disimpan.');
    }
}

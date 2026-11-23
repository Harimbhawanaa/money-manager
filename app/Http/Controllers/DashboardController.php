<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Transaction;
use App\Support\Months;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        if ($month < 1 || $month > 12 || $year < 2000 || $year > 2100) {
            $month = now()->month;
            $year = now()->year;
        }

        $monthly = Transaction::whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year);
        $totalIncome = (int) (clone $monthly)->where('type', 'income')->sum('amount');
        $totalExpense = (int) (clone $monthly)->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $recent = (clone $monthly)->with('category')
            ->orderByDesc('transaction_date')->orderByDesc('id')->take(5)->get();

        $expenses = (clone $monthly)->where('type', 'expense')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')->with('category')->get()
            ->sortByDesc('total')->values();
        $palette = ['#347DFA', '#9FC4FF', '#8771F8', '#C5A6FA', '#E4DDFF', '#5D8CF9', '#B2AEF5'];
        $spending = $expenses->map(function ($item, $index) use ($palette, $totalExpense) {
            return [
                'name' => $item->category?->name ?? 'Tanpa Kategori',
                'amount' => (int) $item->total,
                'percent' => $totalExpense > 0 ? round($item->total / $totalExpense * 100) : 0,
                'color' => $palette[$index % count($palette)],
            ];
        });

        $spent = $expenses->pluck('total', 'category_id');
        $budgets = Budget::with('category')->where('month', $month)->where('year', $year)
            ->get()->map(function ($budget) use ($spent) {
                $budget->used = (int) ($spent[$budget->category_id] ?? 0);
                $budget->percent = $budget->amount > 0 ? round($budget->used / $budget->amount * 100) : 0;
                return $budget;
            })->sortByDesc('percent')->values();

        $monthName = Months::NAMES[$month] . ' ' . $year;
        return view('dashboard', compact('month', 'year', 'monthName', 'totalIncome',
            'totalExpense', 'balance', 'recent', 'spending', 'budgets'));
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Support\Months;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function index(Request $request)
    {
        $year = max(2000, min(2100, (int) $request->input('year', now()->year)));
        $month = max(1, min(12, (int) $request->input('month', now()->month)));
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $transactions = Transaction::with('category')->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])->get();
        $totalIncome = (int) $transactions->where('type', 'income')->sum('amount');
        $totalExpense = (int) $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;
        $byCategory = $transactions->where('type', 'expense')->groupBy('category_id')->map(function ($items) use ($totalExpense) {
            $amount = (int) $items->sum('amount');
            return ['name' => $items->first()->category?->name ?? 'Tanpa kategori', 'total' => $amount, 'percent' => $totalExpense > 0 ? round($amount / $totalExpense * 100, 1) : 0];
        })->sortByDesc('total')->values();
        $colors = ['#397AFF','#9ABEFF','#8A6CF7','#BEA3FA','#DED7FF','#6DD3E8','#7387D9'];
        $weekly = collect(range(1, 5))->map(function ($week) use ($transactions) {
            $items = $transactions->filter(fn ($t) => min(5, (int) ceil($t->transaction_date->day / 7)) === $week);
            return ['income' => (int) $items->where('type','income')->sum('amount'), 'expense' => (int) $items->where('type','expense')->sum('amount')];
        });
        $trend = collect(range(5,0))->map(function ($offset) use ($start) {
            $date = $start->copy()->subMonths($offset);
            $items = Transaction::whereYear('transaction_date',$date->year)->whereMonth('transaction_date',$date->month)->get();
            return ['label' => $date->translatedFormat('M y'), 'income' => (int) $items->where('type','income')->sum('amount'), 'expense' => (int) $items->where('type','expense')->sum('amount')];
        });
        
$barConfig = [
    'type' => 'bar',
    'data' => [
        'labels' => ['W1', 'W2', 'W3', 'W4', 'W5'],
        'datasets' => [
            [
                'label' => 'Income',
                'data' => $weekly->pluck('income')->all(),
                'backgroundColor' => '#397AFF',
                'borderRadius' => 7,
                'maxBarThickness' => 38,
            ],
            [
                'label' => 'Expense',
                'data' => $weekly->pluck('expense')->all(),
                'backgroundColor' => '#A58BFA',
                'borderRadius' => 7,
                'maxBarThickness' => 38,
            ],
        ],
    ],
    'options' => [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => [
                'position' => 'bottom',
                'labels' => [
                    'usePointStyle' => true,
                    'boxWidth' => 10,
                    'padding' => 18,
                ],
            ],
        ],
        'scales' => [
            'x' => [
                'grid' => ['display' => false],
            ],
            'y' => [
                'beginAtZero' => true,
                'ticks' => [
                    'maxTicksLimit' => 6,
                ],
            ],
        ],
    ],
];

$doughnutConfig = [
    'type' => 'doughnut',
    'data' => [
        'labels' => $byCategory->pluck('name')->all(),
        'datasets' => [
            [
                'data' => $byCategory->pluck('total')->all(),
                'backgroundColor' => $byCategory->keys()
                    ->map(fn ($i) => $colors[$i % count($colors)])
                    ->all(),
                'borderWidth' => 0,
                'hoverOffset' => 5,
            ],
        ],
    ],
    'options' => [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'cutout' => '72%',
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
    ],
];

$trendConfig = [
    'type' => 'line',
    'data' => [
        'labels' => $trend->pluck('label')->all(),
        'datasets' => [
            [
                'label' => 'Income',
                'data' => $trend->pluck('income')->all(),
                'borderColor' => '#397AFF',
                'backgroundColor' => '#397AFF22',
                'borderWidth' => 2.5,
                'tension' => 0.15,
                'fill' => true,
                'pointRadius' => 4,
                'pointBackgroundColor' => '#FFFFFF',
                'pointBorderWidth' => 2,
            ],
            [
                'label' => 'Expense',
                'data' => $trend->pluck('expense')->all(),
                'borderColor' => '#8A6CF7',
                'backgroundColor' => '#8A6CF711',
                'borderWidth' => 2.5,
                'tension' => 0.15,
                'fill' => false,
                'pointRadius' => 4,
                'pointBackgroundColor' => '#FFFFFF',
                'pointBorderWidth' => 2,
            ],
        ],
    ],
    'options' => [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => [
                'position' => 'bottom',
                'labels' => [
                    'usePointStyle' => true,
                    'boxWidth' => 10,
                    'padding' => 18,
                ],
            ],
        ],
        'scales' => [
            'x' => [
                'grid' => ['display' => false],
            ],
            'y' => [
                'beginAtZero' => true,
                'ticks' => [
                    'maxTicksLimit' => 6,
                ],
            ],
        ],
    ],
];

        $months = Months::NAMES;
        return view('chart', compact('year','month','months','totalIncome','totalExpense','balance','byCategory','colors','barConfig','doughnutConfig','trendConfig'));
    }
}

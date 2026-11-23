@extends('layouts.app')
@section('title', 'Chart')

@section('content')
    @php($money = fn ($n) => 'Rp'.number_format((int) $n, 0, ',', '.'))

    <div class="mm-chart-page">
        <div class="mm-chart-heading">
            <div>
                <h1>Chart</h1>
                <p>Analyze your income, expense, and spending patterns.</p>
            </div>

            <form action="{{ route('chart') }}" method="GET" class="mm-chart-filter">
                <i data-lucide="calendar-days"></i>

                <select name="month" aria-label="Bulan" onchange="this.form.submit()">
                    @foreach ($months as $number => $name)
                        <option value="{{ $number }}" @selected($month == $number)>{{ $name }}</option>
                    @endforeach
                </select>

                <select name="year" aria-label="Tahun" onchange="this.form.submit()">
                    @for ($y = now()->year + 1; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>

        <div class="mm-chart-stats">
            <div class="mm-chart-stat mm-chart-stat-income">
                <span class="mm-chart-icon green"><i data-lucide="arrow-up-right"></i></span>
                <div>
                    <span>Total Income</span>
                    <strong>{{ $money($totalIncome) }}</strong>
                </div>
            </div>

            <div class="mm-chart-stat mm-chart-stat-expense">
                <span class="mm-chart-icon purple"><i data-lucide="arrow-down-right"></i></span>
                <div>
                    <span>Total Expense</span>
                    <strong>{{ $money($totalExpense) }}</strong>
                </div>
            </div>

            <div class="mm-chart-stat mm-chart-stat-balance">
                <span class="mm-chart-icon blue"><i data-lucide="layers-2"></i></span>
                <div>
                    <span>Net Balance</span>
                    <strong>{{ $balance < 0 ? '−' : '' }}{{ $money(abs($balance)) }}</strong>
                </div>
            </div>
        </div>

        <div class="mm-chart-grid">
            <section class="mm-chart-panel">
                <header>
                    <span class="mm-chart-icon small"><i data-lucide="chart-no-axes-column"></i></span>
                    <h2>Income vs Expense</h2>
                    <span class="mm-chart-period">This Month</span>
                </header>

                <div class="mm-chart-canvas">
                    <canvas data-config='@json($barConfig)'></canvas>
                </div>
            </section>

            <section class="mm-chart-panel">
                <header>
                    <span class="mm-chart-icon small"><i data-lucide="chart-pie"></i></span>
                    <h2>Spending by Category</h2>
                    <span class="mm-chart-period">This Month</span>
                </header>

                <div class="mm-chart-spending">
                    <div class="mm-chart-donut-wrap">
                        @if ($totalExpense > 0)
                            <canvas data-config='@json($doughnutConfig)'></canvas>
                            <div class="mm-chart-donut-center">
                                <strong>{{ $money($totalExpense) }}</strong>
                                <small>Total Expense</small>
                            </div>
                        @else
                            <p class="mm-chart-empty">No expenses yet</p>
                        @endif
                    </div>

                    <div class="mm-chart-legend">
                        @forelse ($byCategory as $item)
                            <div>
                                <i style="background:{{ $colors[$loop->index % count($colors)] }}"></i>
                                <span>{{ $item['name'] }}</span>
                                <span>{{ $item['percent'] }}%</span>
                                <b>{{ $money($item['total']) }}</b>
                            </div>
                        @empty
                            <p class="mm-chart-empty">Belum ada pengeluaran.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="mm-chart-panel">
                <header>
                    <span class="mm-chart-icon small"><i data-lucide="trending-up"></i></span>
                    <h2>Monthly Trend</h2>
                    <span class="mm-chart-period">Last 6 Months</span>
                </header>

                <div class="mm-chart-canvas">
                    <canvas data-config='@json($trendConfig)'></canvas>
                </div>
            </section>

            <section class="mm-chart-panel">
                <header>
                    <span class="mm-chart-icon small"><i data-lucide="award"></i></span>
                    <h2>Top Expense Categories</h2>
                </header>

                <div class="mm-chart-top">
                    @forelse ($byCategory->take(3) as $item)
                        <div class="mm-chart-top-row">
                            <span class="mm-chart-rank">{{ $loop->iteration }}</span>
                            <span class="mm-chart-icon small purple"><i data-lucide="wallet"></i></span>

                            <div class="mm-chart-top-content">
                                <div>
                                    <strong>{{ $item['name'] }}</strong>
                                    <span>{{ $money($item['total']) }} · {{ $item['percent'] }}%</span>
                                </div>

                                <div class="mm-chart-progress">
                                    <span style="width:{{ min(100, $item['percent']) }}%"></span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="mm-chart-empty">Belum ada pengeluaran.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
@extends('layouts.app')
@section('title', 'Overview')

@section('content')
    @php
        $money = fn ($value) => 'Rp' . number_format((int) $value, 0, ',', '.');
        $monthOptions = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $gradient = $spending->count() ? $spending->map(function ($item) use ($totalExpense) {
            return $item['color'];
        }) : collect();

        $cursor = 0;
        $stops = [];

        foreach ($spending as $item) {
            $start = $cursor;
            $cursor += $totalExpense > 0 ? ($item['amount'] / $totalExpense) * 100 : 0;
            $stops[] = $item['color'] . ' ' . $start . '% ' . $cursor . '%';
        }

        $donutStyle = count($stops)
            ? 'background:conic-gradient(' . implode(', ', $stops) . ')'
            : 'background:#E7EAFE';
    @endphp

    <div class="mm-overview">
        <div class="mm-overview-heading">
            <div>
                <p class="mm-eyebrow">YOUR FINANCIAL SUMMARY</p>
                <h1>Overview</h1>
                <p>Ringkasan keuangan {{ $monthName }}</p>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="mm-period-form" aria-label="Pilih periode">
                <i data-lucide="calendar-days"></i>

                <select name="month" aria-label="Bulan" onchange="this.form.submit()">
                    @foreach ($monthOptions as $i => $label)
                        <option value="{{ $i + 1 }}" @selected($month === $i + 1)>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="year" aria-label="Tahun" onchange="this.form.submit()">
                    @for ($y = now()->year + 1; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" @selected($year === $y)>{{ $y }}</option>
                    @endfor
                </select>

                <i data-lucide="chevron-down"></i>
            </form>
        </div>

        <div class="mm-top-grid">
            <section class="mm-panel mm-balance-card">
                <div class="mm-balance-content">
                    <div class="mm-balance-label">
                        Total Balance
                        <button
                            type="button"
                            class="mm-eye"
                            id="mm-toggle-balance"
                            aria-label="Sembunyikan saldo"
                            aria-pressed="false"
                        >
                            <i data-lucide="eye"></i>
                        </button>
                    </div>

                    <div class="mm-balance-amount" id="mm-balance-amount" data-amount="{{ $money($balance) }}">{{ $money($balance) }}</div>

                    <div class="mm-balance-note">
                        <i data-lucide="wallet"></i> Sisa Uang
                    </div>
                </div>
            </section>

            <section class="mm-panel mm-income-expense">
                <div class="mm-finance-line">
                    <span class="mm-finance-icon mm-green"><i data-lucide="arrow-up-right"></i></span>
                    <span>Income</span>
                    <strong>{{ $money($totalIncome) }}</strong>
                </div>

                <div class="mm-finance-line">
                    <span class="mm-finance-icon mm-purple"><i data-lucide="arrow-down-right"></i></span>
                    <span>Expense</span>
                    <strong>{{ $money($totalExpense) }}</strong>
                </div>
            </section>
        </div>

        <div class="mm-middle-grid">
            <section class="mm-panel mm-spending-card">
                <div class="mm-panel-title">
                    <h2>Spending Overview</h2>
                    <span class="mm-chip">{{ $monthName }}</span>
                </div>

                <div class="mm-spending-body">
                    <div class="mm-donut" style="{{ $donutStyle }}">
                        <div class="mm-donut-hole">
                            <strong>{{ $money($totalExpense) }}</strong>
                            <small>Total Expense</small>
                        </div>
                    </div>

                    <div class="mm-spending-list">
                        @forelse ($spending as $item)
                            <div class="mm-spending-item">
                                <span class="mm-color-dot" style="background:{{ $item['color'] }}"></span>
                                <span class="mm-spending-name">{{ $item['name'] }}</span>
                                <span class="mm-spending-percent">{{ $item['percent'] }}%</span>
                                <strong>{{ $money($item['amount']) }}</strong>
                            </div>
                        @empty
                            <p class="mm-empty">Belum ada pengeluaran pada periode ini.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="mm-panel mm-budget-card">
                <div class="mm-panel-title">
                    <h2>Budgeting Progress</h2>
                    <a href="{{ route('budgets.index') }}" aria-label="Lihat budgeting">
                        <i data-lucide="chevron-right"></i>
                    </a>
                </div>

                <div class="mm-budget-summary">
                    <span class="mm-finance-icon mm-blue"><i data-lucide="target"></i></span>
                    <span>{{ $budgets->filter(fn ($b) => $b->percent < 80)->count() }} dari {{ $budgets->count() }} budget on track</span>
                </div>

                <div class="mm-budget-list">
                    @forelse ($budgets->take(3) as $budget)
                        <div class="mm-budget-item">
                            <span class="mm-budget-icon"><i data-lucide="layers"></i></span>

                            <div class="mm-budget-info">
                                <div class="mm-budget-name">
                                    <strong>{{ $budget->category?->name ?? 'Kategori dihapus' }}</strong>
                                    <b>{{ $budget->percent }}%</b>
                                </div>

                                <small>{{ $money($budget->used) }} / {{ $money($budget->amount) }}</small>

                                <div class="mm-progress">
                                    <span style="width:{{ min($budget->percent, 100) }}%;background:{{ $budget->percent > 100 ? '#F04F70' : ($budget->percent >= 80 ? '#F6AC46' : '#397AFF') }}"></span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="mm-empty">Belum ada budget bulan ini. <a href="{{ route('budgets.index') }}">Buat budget</a></p>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="mm-panel mm-recent-card">
            <div class="mm-panel-title">
                <h2>Recent Records</h2>
                <a class="mm-view-all" href="{{ route('transactions.index') }}">
                    View All <i data-lucide="chevron-right"></i>
                </a>
            </div>

            <div class="mm-recent-list">
                @forelse ($recent as $transaction)
                    <div class="mm-record-row">
                        <span class="mm-record-icon {{ $transaction->type === 'income' ? 'mm-record-income' : 'mm-record-expense' }}">
                            <i data-lucide="{{ $transaction->type === 'income' ? 'arrow-up-right' : 'arrow-down-right' }}"></i>
                        </span>

                        <div class="mm-record-description">
                            <strong>{{ $transaction->description ?: ($transaction->category?->name ?? 'Transaksi') }}</strong>
                            <small>{{ $transaction->category?->name ?? 'Tanpa Kategori' }} · {{ $transaction->transaction_date?->format('d/m/Y') }}</small>
                        </div>

                        <strong class="mm-record-amount {{ $transaction->type === 'income' ? 'mm-positive' : 'mm-negative' }}">{{ $transaction->type === 'income' ? '+' : '−' }}{{ $money($transaction->amount) }}</strong>
                    </div>
                @empty
                    <p class="mm-empty">Belum ada transaksi pada periode ini.</p>
                @endforelse
            </div>
        </section>
    </div>

    <script>
        (function () {
            function setupBalance() {
                const btn = document.getElementById('mm-toggle-balance');
                const amount = document.getElementById('mm-balance-amount');

                if (!btn || !amount || btn.dataset.ready) return;

                btn.dataset.ready = '1';

                btn.addEventListener('click', function () {
                    const hidden = btn.getAttribute('aria-pressed') !== 'true';

                    btn.setAttribute('aria-pressed', String(hidden));
                    btn.setAttribute('aria-label', hidden ? 'Tampilkan saldo' : 'Sembunyikan saldo');
                    amount.textContent = hidden ? 'Rp••••••••' : amount.dataset.amount;
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setupBalance);
            } else {
                setupBalance();
            }
        })();
    </script>
@endsection

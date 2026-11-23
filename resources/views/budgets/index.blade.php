@extends('layouts.app')
@section('title', 'Budgeting')

@section('content')
    @php
        $money = fn ($n) => 'Rp'.number_format((int) $n, 0, ',', '.');
        $budgetedCategories = $categories->filter(fn ($category) => (int) ($budgets->get($category->id)?->amount ?? 0) > 0);
        $monthName = $months[$month] ?? (string) $month;
    @endphp

    <div class="mm-budget-page">
        <div class="mm-budget-heading">
            <div>
                <h1>Budgeting</h1>
                <p>Plan your spending, set budgets, and track progress.</p>
            </div>

            <form method="GET" action="{{ route('budgets.index') }}" class="mm-budget-filter">
                <i data-lucide="calendar-days"></i>

                <select name="month" aria-label="Bulan" onchange="this.form.submit()">
                    @foreach ($months as $number => $name)
                        <option value="{{ $number }}" @selected((int) $month === (int) $number)>{{ $name }}</option>
                    @endforeach
                </select>

                <select name="year" aria-label="Tahun" onchange="this.form.submit()">
                    @foreach (range(now()->year + 2, now()->year - 5) as $y)
                        <option value="{{ $y }}" @selected((int) $year === (int) $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="mm-budget-stats">
            <div class="mm-budget-stat">
                <span class="mm-budget-stat-icon blue"><i data-lucide="wallet"></i></span>
                <div>
                    <small>Total Budget</small>
                    <strong>{{ $money($totalBudget) }}</strong>
                </div>
            </div>

            <div class="mm-budget-stat">
                <span class="mm-budget-stat-icon purple"><i data-lucide="arrow-down-right"></i></span>
                <div>
                    <small>Total Spent</small>
                    <strong>{{ $money($totalSpent) }}</strong>
                </div>
            </div>

            <div class="mm-budget-stat">
                <span class="mm-budget-stat-icon {{ $remaining < 0 ? 'red' : 'green' }}">
                    <i data-lucide="piggy-bank"></i>
                </span>
                <div>
                    <small>Remaining</small>
                    <strong class="{{ $remaining < 0 ? 'mm-budget-danger-text' : '' }}">{{ $remaining < 0 ? '−' : '' }}{{ $money(abs($remaining)) }}</strong>
                </div>
            </div>

            <div class="mm-budget-stat">
                <span class="mm-budget-stat-icon blue"><i data-lucide="gauge"></i></span>
                <div>
                    <small>Budget Usage</small>
                    <strong>{{ number_format($usage, 1, ',', '.') }}%</strong>
                </div>
            </div>
        </div>

        <div class="mm-budget-insights">
            <section class="mm-budget-card">
                <div class="mm-budget-card-heading">
                    <span class="mm-budget-heading-icon"><i data-lucide="chart-pie"></i></span>
                    <h2>Budget Overview</h2>
                </div>

                <div class="mm-budget-overview-body">
                    <div class="mm-budget-ring" style="--budget-fill: {{ min(100, max(0, $usage)) }}%;">
                        <div>
                            <strong>{{ number_format($usage, 1, ',', '.') }}%</strong>
                            <small>Budget used</small>
                        </div>
                    </div>

                    <div class="mm-budget-overview-legend">
                        <div>
                            <i class="mm-budget-dot spent"></i>
                            <span>Spent</span>
                            <b>{{ $money($totalSpent) }}</b>
                        </div>
                        <div>
                            <i class="mm-budget-dot remaining"></i>
                            <span>Remaining</span>
                            <b>{{ $money(max(0, $remaining)) }}</b>
                        </div>

                        @if ($remaining < 0)
                            <p class="mm-budget-danger-text">Over budget by {{ $money(abs($remaining)) }}</p>
                        @endif
                    </div>
                </div>
            </section>

            <section class="mm-budget-card">
                <div class="mm-budget-card-heading">
                    <span class="mm-budget-heading-icon"><i data-lucide="list-checks"></i></span>
                    <h2>Budget Status</h2>
                </div>

                <div class="mm-budget-status-list">
                    <div>
                        <span class="mm-budget-status-icon good"><i data-lucide="check-circle-2"></i></span>
                        <span>On Track</span>
                        <strong>{{ $statusCounts['on_track'] }}</strong>
                    </div>
                    <div>
                        <span class="mm-budget-status-icon warn"><i data-lucide="alert-triangle"></i></span>
                        <span>Almost Limit</span>
                        <strong>{{ $statusCounts['almost'] }}</strong>
                    </div>
                    <div>
                        <span class="mm-budget-status-icon bad"><i data-lucide="alert-circle"></i></span>
                        <span>Over Budget</span>
                        <strong>{{ $statusCounts['over'] }}</strong>
                    </div>
                </div>

                <p class="mm-budget-status-note">Status dihitung untuk kategori yang memiliki budget. Almost Limit = 80–100%.</p>
            </section>

            <section class="mm-budget-card">
                <div class="mm-budget-card-heading">
                    <span class="mm-budget-heading-icon"><i data-lucide="sparkles"></i></span>
                    <h2>Monthly Insight</h2>
                </div>

                @if ($totalBudget > 0)
                    <p class="mm-budget-insight-lead">{{ $usage > 100 ? 'Your spending has exceeded the budget.' : ($usage >= 80 ? 'You are approaching your budget limit.' : 'Your spending is within the planned budget.') }}</p>
                    <p>You've used <strong>{{ number_format($usage, 1, ',', '.') }}%</strong> of your budget for {{ $monthName }} {{ $year }}.</p>
                    <div class="mm-budget-insight-bottom">{{ $remaining >= 0 ? 'Remaining budget: '.$money($remaining) : 'Overspent: '.$money(abs($remaining)) }}</div>
                @else
                    <p class="mm-budget-insight-lead">Start by setting your first budget.</p>
                    <p>Set category limits to monitor your monthly spending.</p>
                @endif

                @if ($unbudgetedSpent > 0)
                    <p class="mm-budget-unbudgeted">{{ $money($unbudgetedSpent) }} spent in categories without a budget.</p>
                @endif
            </section>
        </div>

        <section class="mm-budget-card mm-budget-category-card">
            <div class="mm-budget-category-heading">
                <div>
                    <h2>Budgets by Category</h2>
                    <p>Manage category limits and see how much you've spent.</p>
                </div>

                <button type="button" class="mm-primary-btn" id="mm-budget-open">
                    <i data-lucide="plus"></i> Add Budget
                </button>
            </div>

            <div class="mm-budget-table-wrap">
                <table class="mm-budget-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Budget</th>
                            <th>Spent</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            @php
                                $budgetAmount = (int) ($budgets->get($category->id)?->amount ?? 0);
                                $used = (int) ($spent[$category->id] ?? 0);
                                $percent = $budgetAmount > 0 ? round($used / $budgetAmount * 100, 1) : 0;
                                $status = $budgetAmount <= 0 ? 'none' : ($percent > 100 ? 'over' : ($percent >= 80 ? 'almost' : 'good'));
                            @endphp

                            <tr>
                                <td>
                                    <span class="mm-budget-category-name"><i style="background-color:{{ $category->color ?? '#94a3b8' }}"></i>{{ $category->name }}</span>
                                </td>
                                <td>{{ $budgetAmount > 0 ? $money($budgetAmount) : '—' }}</td>
                                <td>{{ $money($used) }}</td>
                                <td>
                                    <div class="mm-budget-progress-label">{{ $budgetAmount > 0 ? number_format($percent, 1, ',', '.').'%' : 'No budget' }}</div>
                                    <div class="mm-budget-progress">
                                        <span class="{{ $status }}" style="width:{{ min(100, $percent) }}%"></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="mm-budget-status-pill {{ $status }}">{{ $status === 'good' ? 'On Track' : ($status === 'almost' ? 'Almost Limit' : ($status === 'over' ? 'Over Budget' : 'Not Set')) }}</span>
                                </td>
                                <td>
                                    <button type="button" class="mm-budget-edit" data-budget-edit data-category-id="{{ $category->id }}">
                                        {{ $budgetAmount > 0 ? 'Edit' : 'Set' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="mm-budget-empty">No expense categories yet. Create a category in Records first.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="mm-budget-modal" id="mm-budget-modal" hidden>
        <div class="mm-budget-modal-backdrop" data-budget-close></div>

        <div class="mm-budget-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="mm-budget-modal-title" tabindex="-1">
            <div class="mm-budget-modal-heading">
                <div>
                    <h2 id="mm-budget-modal-title">Manage Budgets</h2>
                    <p>Set or update category limits for {{ $monthName }} {{ $year }}.</p>
                </div>
                <button type="button" class="mm-budget-close" data-budget-close aria-label="Close">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('budgets.store') }}">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">

                <div class="mm-budget-modal-fields">
                    @foreach ($categories as $category)
                        <label class="mm-budget-modal-row">
                            <span><i style="background-color:{{ $category->color ?? '#94a3b8' }}"></i>{{ $category->name }}</span>
                            <span class="mm-budget-amount-input">
                                <small>Rp</small>
                                <input
                                    type="number"
                                    min="0"
                                    step="1"
                                    inputmode="numeric"
                                    name="budgets[{{ $category->id }}]"
                                    data-budget-input="{{ $category->id }}"
                                    value="{{ old('budgets.'.$category->id, $budgets->get($category->id)?->amount ?? '') }}"
                                    placeholder="0"
                                >
                            </span>
                        </label>
                    @endforeach
                </div>

                <p class="mm-budget-modal-help">Leave blank or enter 0 to remove a category budget.</p>

                <div class="mm-budget-modal-actions">
                    <button type="button" class="mm-budget-secondary" data-budget-close>Cancel</button>
                    <button type="submit" class="mm-primary-btn">Save Budgets</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('mm-budget-modal');
            if (!modal) return;

            let previousFocus = null;

            const open = (categoryId = null) => {
                previousFocus = document.activeElement;
                modal.hidden = false;
                document.body.style.overflow = 'hidden';

                const target = categoryId
                    ? modal.querySelector('[data-budget-input="' + categoryId + '"]')
                    : modal.querySelector('[data-budget-input]');

                (target || modal.querySelector('.mm-budget-modal-dialog')).focus();
            };

            const close = () => {
                modal.hidden = true;
                document.body.style.overflow = '';
                previousFocus?.focus();
            };

            document.getElementById('mm-budget-open')?.addEventListener('click', () => open());

            document.querySelectorAll('[data-budget-edit]').forEach(btn => {
                btn.addEventListener('click', () => open(btn.dataset.categoryId));
            });

            modal.querySelectorAll('[data-budget-close]').forEach(btn => {
                btn.addEventListener('click', close);
            });

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape' && !modal.hidden) close();
            });

            @if ($errors->any())
                open();
            @endif
        })();
    </script>
@endsection
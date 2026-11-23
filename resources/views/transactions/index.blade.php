@extends('layouts.app')
@section('title', 'Records')

@section('content')
    <section class="mm-records space-y-6">
        <div>
            <h1 class="text-3xl font-bold text-[#101747]">Records</h1>
            <p class="text-[#59618b]">All your income and expense transactions in one place.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            @foreach ([
                ['Total Income', $totalIncome, 'arrow-up-right', 'income'],
                ['Total Expense', $totalExpense, 'arrow-down-right', 'expense'],
                ['Net Balance', $balance, 'layers-2', 'balance'],
            ] as [$label, $value, $icon, $tone])
                <div class="mm-record-stat mm-stat-{{ $tone }}">
                    <div class="mm-record-icon">
                        <i data-lucide="{{ $icon }}"></i>
                    </div>
                    <div>
                        <p>{{ $label }}</p>
                        <strong>{{ $value < 0 ? '-' : '' }}Rp{{ number_format(abs($value), 0, ',', '.') }}</strong>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mm-record-toolbar">
            <form id="record-filters" method="GET" action="{{ route('transactions.index') }}" class="mm-record-filters">
                <label class="mm-search">
                    <i data-lucide="search"></i>
                    <input name="search" value="{{ $search }}" placeholder="Search records..." aria-label="Search records">
                </label>

                <select name="month" aria-label="Bulan">
                    <option value="">All months</option>
                    @foreach ($months as $number => $name)
                        <option value="{{ $number }}" @selected((string) $month === (string) $number)>{{ $name }}</option>
                    @endforeach
                </select>

                <select name="year" aria-label="Tahun">
                    <option value="">All years</option>
                    @foreach (range(now()->year - 5, now()->year + 1) as $y)
                        <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}</option>
                    @endforeach
                </select>

                <select name="type" aria-label="Type">
                    <option value="">All types</option>
                    <option value="income" @selected($type === 'income')>Income</option>
                    <option value="expense" @selected($type === 'expense')>Expense</option>
                </select>

                <select name="category_id" aria-label="Category">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>

                <button class="mm-outline-btn" type="submit">
                    <i data-lucide="filter"></i> Filter
                </button>
                <a class="mm-outline-btn" href="{{ route('transactions.index') }}">Reset</a>
            </form>

            <div class="flex gap-2 flex-wrap">
                <button type="button" class="mm-outline-btn" data-mm-open="category-modal">
                    <i data-lucide="grid-2x2"></i> Categories
                </button>
                <button type="button" class="mm-outline-btn" data-mm-open="record-modal">
                    <i data-lucide="calculator"></i> Calculator
                </button>
            </div>
        </div>

        <div class="mm-record-panel">
            <div class="flex justify-end mb-6">
                <button class="mm-primary-btn" type="button" data-mm-open="record-modal">
                    <i data-lucide="plus"></i> Add Record
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="mm-record-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="whitespace-nowrap">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                <td class="font-semibold">{{ $transaction->description ?: $transaction->category?->name }}</td>
                                <td>
                                    <span class="mm-category-pill"><span class="mm-category-dot" style="background:{{ $transaction->category?->color ?: '#8b7cf6' }}"></span>{{ $transaction->category?->name ?? 'Uncategorized' }}</span>
                                </td>
                                <td>
                                    <span class="mm-type-pill {{ $transaction->type === 'income' ? 'is-income' : 'is-expense' }}">{{ ucfirst($transaction->type) }}</span>
                                </td>
                                <td class="text-right whitespace-nowrap font-semibold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                                </td>
                                <td>
                                    <div class="flex justify-end gap-3">
                                        <a class="text-blue-600" href="{{ route('transactions.edit', $transaction) }}" aria-label="Edit transaction">Edit</a>
                                        <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Delete this record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-500">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-slate-500">No records found for these filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">{{ $transactions->links() }}</div>
        </div>
    </section>

    <div class="mm-modal" id="record-modal" role="dialog" aria-modal="true" aria-labelledby="record-modal-title" hidden>
        <div class="mm-modal-backdrop" data-mm-close></div>

        <div class="mm-modal-dialog mm-modal-wide">
            <div class="mm-modal-heading">
                <div>
                    <h2 id="record-modal-title">Add Record</h2>
                    <p>Add a new income or expense transaction.</p>
                </div>
                <button class="mm-close" data-mm-close aria-label="Close">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('transactions.store') }}" id="mm-add-record">
                @csrf

                <div class="mm-modal-columns">
                    <div class="mm-form-card space-y-4">
                        <div>
                            <label class="mm-label">Type</label>
                            <div class="mm-type-switch">
                                <button type="button" data-record-type="income">↗ Income</button>
                                <button type="button" data-record-type="expense">↘ Expense</button>
                            </div>
                        </div>

                        <div>
                            <label class="mm-label" for="mm-amount">Amount</label>
                            <input id="mm-amount" data-amount-input type="number" name="amount" min="1" step="1" required placeholder="Rp0" class="mm-input">
                        </div>

                        <div>
                            <label class="mm-label" for="mm-category">Category</label>
                            <select id="mm-category" name="category_id" class="mm-input" required>
                                <option value="">Choose category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" data-category-type="{{ $category->type }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mm-label" for="mm-description">Description</label>
                            <input id="mm-description" class="mm-input" name="description" maxlength="255" placeholder="e.g. Lunch, Groceries, Salary...">
                        </div>

                        <div>
                            <label class="mm-label" for="mm-date">Date</label>
                            <input id="mm-date" class="mm-input" type="date" name="transaction_date" required value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mm-form-card">
                        <h3 class="font-bold text-lg mb-3 flex gap-2 items-center">
                            <i data-lucide="calculator" class="text-blue-600"></i> Calculator
                        </h3>
                        <div class="mm-calc-screen" id="mm-calc-screen">0</div>

                        <div class="mm-calc-grid">
                            @foreach (['AC', 'DEL', '%', '/', '7', '8', '9', '*', '4', '5', '6', '-', '1', '2', '3', '+', '0', '00', '.', '='] as $key)
                                <button
                                    type="button"
                                    data-mm-key="{{ $key }}"
                                    class="{{ in_array($key, ['+', '-', '*', '/', '=']) ? 'operator' : '' }}"
                                >{{ ['/' => '÷', '*' => '×', 'DEL' => '⌫'][$key] ?? $key }}</button>
                            @endforeach
                        </div>

                        <button type="button" id="mm-use-result" class="mm-outline-btn w-full justify-center mt-3">
                            Use result as amount
                        </button>
                    </div>
                </div>

                <div class="mm-modal-actions">
                    <button type="button" class="mm-outline-btn" data-mm-close>Cancel</button>
                    <button type="submit" class="mm-primary-btn">
                        <i data-lucide="check"></i> Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mm-modal" id="category-modal" role="dialog" aria-modal="true" aria-labelledby="category-modal-title" hidden>
        <div class="mm-modal-backdrop" data-mm-close></div>

        <div class="mm-modal-dialog mm-category-dialog">
            <div class="mm-modal-heading">
                <div>
                    <h2 id="category-modal-title">Manage Categories</h2>
                    <p>Create, edit, or delete transaction categories.</p>
                </div>
                <button class="mm-close" data-mm-close aria-label="Close">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <div class="mm-category-tabs">
                <button type="button" data-mm-category-tab="expense" class="selected">Expense Categories</button>
                <button type="button" data-mm-category-tab="income">Income Categories</button>
            </div>

            @foreach (['expense', 'income'] as $categoryType)
                <div class="mm-category-list" data-mm-category-panel="{{ $categoryType }}" @if ($categoryType === 'income') hidden @endif>
                    @foreach ($categories->where('type', $categoryType) as $category)
                        <div class="mm-category-entry">
                            <span class="mm-category-dot" style="background:{{ $category->color ?: '#8b7cf6' }}"></span>
                            <div class="flex-1 min-w-0">
                                <strong>{{ $category->name }}</strong>
                                <small>{{ $category->transactions_count }} transactions</small>
                            </div>

                            <button
                                type="button"
                                class="mm-icon-btn"
                                data-mm-edit-category="{{ $category->id }}"
                                aria-label="Edit {{ $category->name }}"
                            >
                                <i data-lucide="pencil"></i>
                            </button>

                            <form
                                method="POST"
                                action="{{ route('categories.destroy', $category) }}"
                                onsubmit="return confirm('Delete this category? Categories in use cannot be deleted.')"
                            >
                                @csrf
                                @method('DELETE')
                                <button class="mm-icon-btn text-red-500" aria-label="Delete {{ $category->name }}">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </form>
                        </div>

                        <form id="mm-category-edit-{{ $category->id }}" method="POST" action="{{ route('categories.update', $category) }}" class="mm-category-edit" hidden>
                            @csrf
                            @method('PUT')
                            <input name="name" value="{{ $category->name }}" required maxlength="60" class="mm-input">
                            <input type="color" name="color" value="{{ $category->color ?: '#8b7cf6' }}" aria-label="Category color">
                            <button type="submit" class="mm-primary-btn">Save</button>
                        </form>
                    @endforeach

                    <form method="POST" action="{{ route('categories.store') }}" class="mm-category-add">
                        @csrf
                        <input type="hidden" name="type" value="{{ $categoryType }}">
                        <input class="mm-input" name="name" maxlength="60" required placeholder="New category name">
                        <input type="color" name="color" value="#8b7cf6" aria-label="New category color">
                        <button type="submit" class="mm-primary-btn">
                            <i data-lucide="plus"></i> Add
                        </button>
                    </form>
                </div>
            @endforeach

            <p class="text-xs text-slate-500 mt-4">Categories used by transactions or budgets cannot be deleted until those records are reassigned.</p>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('') }}" type="text/plain" hidden></script>
    @endpush
@endsection
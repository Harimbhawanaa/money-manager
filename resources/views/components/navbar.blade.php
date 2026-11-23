<nav class="mm-bottom-nav" aria-label="Navigasi utama">
    <div class="mm-bottom-nav-inner">
        <div class="mm-nav-liquid"></div>

        <a
            href="{{ route('dashboard') }}"
            title="Overview"
            aria-label="Overview"
            @if (request()->routeIs('dashboard')) aria-current="page" @endif
            class="mm-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
        >
            <i data-lucide="house"></i>
            <span class="mm-nav-dot"></span>
        </a>

        <a
            href="{{ route('transactions.index') }}"
            title="Records"
            aria-label="Records"
            @if (request()->routeIs('transactions.*') || request()->routeIs('categories.*')) aria-current="page" @endif
            class="mm-nav-link {{ request()->routeIs('transactions.*') || request()->routeIs('categories.*') ? 'active' : '' }}"
        >
            <i data-lucide="receipt-text"></i>
            <span class="mm-nav-dot"></span>
        </a>

        <a
            href="{{ route('chart') }}"
            title="Chart"
            aria-label="Chart"
            @if (request()->routeIs('chart')) aria-current="page" @endif
            class="mm-nav-link {{ request()->routeIs('chart') ? 'active' : '' }}"
        >
            <i data-lucide="chart-no-axes-column"></i>
            <span class="mm-nav-dot"></span>
        </a>

        <a
            href="{{ route('budgets.index') }}"
            title="Budgeting"
            aria-label="Budgeting"
            @if (request()->routeIs('budgets.*')) aria-current="page" @endif
            class="mm-nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}"
        >
            <i data-lucide="layers-2"></i>
            <span class="mm-nav-dot"></span>
        </a>
    </div>
</nav>
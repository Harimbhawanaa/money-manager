<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="view-transition" content="same-origin">
    <title>Money Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="mm-body">

    <div class="mm-shell">
        <header class="mm-header">
            <a class="mm-brand" href="{{ route('dashboard') }}" aria-label="Money Manager — Overview">
                <span class="mm-brand-icon"><i data-lucide="wallet"></i></span>
                <span>Money Manager</span>
                <span class="mm-sparkle" aria-hidden="true"></span>
            </a>
            <div class="mm-period" aria-label="Periode saat ini">
                <i data-lucide="calendar-days"></i>
                <span>{{ now()->translatedFormat('M Y') }}</span>
            </div>
        </header>

        <main class="mm-content">
            @if (session('success'))
                <div class="mm-alert mm-alert-success" role="status">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mm-alert mm-alert-error" role="alert">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>

    <x-navbar />
</body>
</html>

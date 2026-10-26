@php
    // صفحات قانونية عامة بالإنجليزية (مطلوبة لشاشة موافقة Google OAuth) — الاسم والبريد من config/app.php
    $orgName      = config('app.org_name_en');
@endphp
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ $orgName }}</title>
    <meta name="description" content="@yield('description')">
    <link rel="icon" type="image/png" href="/logo/logo-64.png">
    <style>
        @font-face{font-family:'Tajawal';font-weight:400;font-display:swap;src:url('/fonts/tajawal-400.woff2') format('woff2')}
        @font-face{font-family:'Tajawal';font-weight:700;font-display:swap;src:url('/fonts/tajawal-700.woff2') format('woff2')}
        :root { --green:#166534; --green-soft:#f0fdf4; --ink:#1e293b; --muted:#64748b; --rule:#e2e8f0; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #f8fafc; color: var(--ink); font-family: 'Tajawal', system-ui, -apple-system, 'Segoe UI', sans-serif; font-size: 16px; line-height: 1.75; }
        a { color: var(--green); }
        .top { background: #fff; border-bottom: 1px solid var(--rule); }
        .top-inner, .page, .bottom-inner { max-width: 820px; margin: 0 auto; padding: 0 20px; }
        .top-inner { display: flex; align-items: center; justify-content: space-between; gap: 12px; height: 68px; }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--ink); font-weight: 700; }
        .brand img { width: 40px; height: 40px; }
        .top nav { display: flex; gap: 18px; font-size: 14px; }
        .top nav a { text-decoration: none; color: var(--muted); }
        .top nav a.active { color: var(--green); font-weight: 700; }
        .page { padding-top: 36px; padding-bottom: 48px; }
        .card { background: #fff; border: 1px solid var(--rule); border-radius: 16px; padding: 36px 40px; border-top: 4px solid var(--green); }
        h1 { font-size: 30px; line-height: 1.3; margin: 0 0 6px; color: var(--green); }
        .effective { color: var(--muted); font-size: 14px; margin: 0 0 24px; }
        h2 { font-size: 19px; margin: 30px 0 8px; }
        p, li { color: #334155; }
        ul { padding-left: 22px; }
        li { margin: 4px 0; }
        .note { background: var(--green-soft); border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 18px; margin: 14px 0; }
        code { background: #f1f5f9; border-radius: 6px; padding: 1px 6px; font-size: 14px; word-break: break-all; }
        .bottom { border-top: 1px solid var(--rule); background: #fff; }
        .bottom-inner { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 10px; padding-top: 18px; padding-bottom: 18px; font-size: 13px; color: var(--muted); }
        .bottom-inner nav { display: flex; gap: 16px; }
        @media (max-width: 600px) {
            .card { padding: 26px 20px; }
            h1 { font-size: 25px; }
            .top nav { display: none; }
        }
    </style>
</head>
<body>
    <header class="top">
        <div class="top-inner">
            <a class="brand" href="{{ url('/') }}">
                <img src="/logo/logo-96.png" alt="{{ $orgName }} logo" width="40" height="40">
                <span>{{ $orgName }}</span>
            </a>
            <nav>
                <a href="{{ route('privacy') }}" @class(['active' => request()->routeIs('privacy')])>Privacy Policy</a>
                <a href="{{ route('terms') }}" @class(['active' => request()->routeIs('terms')])>Terms of Service</a>
            </nav>
        </div>
    </header>

    <main class="page">
        <article class="card">
            <h1>@yield('title')</h1>
            <p class="effective">Effective date: @yield('effective')</p>
            @yield('content')
        </article>
    </main>

    <footer class="bottom">
        <div class="bottom-inner">
            <span>&copy; {{ date('Y') }} {{ $orgName }}</span>
            <nav>
                <a href="{{ route('privacy') }}">Privacy Policy</a>
                <a href="{{ route('terms') }}">Terms of Service</a>
                <a href="{{ url('/') }}">Home</a>
            </nav>
        </div>
    </footer>
</body>
</html>

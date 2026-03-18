<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول — مسالك النور</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { font-family: 'Tajawal', sans-serif; box-sizing: border-box; }

        body {
            margin: 0; min-height: 100vh;
            display: flex;
            background: #f1f5f9;
        }

        /* ── LEFT PANEL (branding) ── */
        .panel-left {
            display: none;
            width: 46%;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 52px;
            position: relative;
            overflow: hidden;
            background: #052e16;
        }
        @media (min-width: 1024px) { .panel-left { display: flex; } }

        /* Mesh background */
        .panel-left::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 70% 55% at 110% 30%, rgba(34,197,94,0.20) 0%, transparent 60%),
                radial-gradient(ellipse 50% 70% at -10% 80%, rgba(5,150,105,0.18) 0%, transparent 55%);
        }
        /* Large decorative ring */
        .panel-left::after {
            content: '';
            position: absolute;
            top: -120px; left: -120px;
            width: 480px; height: 480px;
            border-radius: 50%;
            border: 70px solid rgba(255,255,255,0.04);
        }
        .panel-ring-2 {
            position: absolute;
            bottom: -80px; right: -80px;
            width: 300px; height: 300px;
            border-radius: 50%;
            border: 50px solid rgba(255,255,255,0.04);
        }

        .panel-content { position: relative; z-index: 10; }

        .brand-mark {
            width: 56px; height: 56px; border-radius: 16px;
            background: rgba(255,255,255,0.10);
            border: 1.5px solid rgba(255,255,255,0.18);
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; font-weight: 900; color: #fff;
            backdrop-filter: blur(8px);
            margin-bottom: 20px;
        }
        .brand-name { font-size: 32px; font-weight: 900; color: #fff; line-height: 1.2; margin: 0 0 8px; }
        .brand-sub  { font-size: 14px; color: rgba(255,255,255,0.45); font-weight: 400; }

        .panel-divider {
            width: 40px; height: 3px; border-radius: 2px;
            background: linear-gradient(90deg, #4ade80, #86efac);
            margin: 28px 0;
        }
        .panel-tagline {
            font-size: 15px; color: rgba(255,255,255,0.60);
            line-height: 1.9; max-width: 280px;
        }

        .features-list { list-style: none; padding: 0; margin: 36px 0 0; }
        .features-list li {
            display: flex; align-items: center; gap: 12px;
            font-size: 13px; color: rgba(255,255,255,0.55);
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .features-list li:last-child { border-bottom: none; }
        .feat-dot {
            width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0;
            background: rgba(74,222,128,0.12);
            border: 1px solid rgba(74,222,128,0.20);
            display: flex; align-items: center; justify-content: center;
        }
        .feat-dot svg { width: 14px; height: 14px; color: #4ade80; }

        .panel-footer-text {
            font-size: 12px; color: rgba(255,255,255,0.25);
        }

        /* ── RIGHT PANEL (form) ── */
        .panel-right {
            flex: 1;
            display: flex; align-items: center; justify-content: center;
            padding: 32px 24px;
        }

        .login-card {
            width: 100%; max-width: 420px;
            background: #fff;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 32px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .card-top-bar {
            height: 4px;
            background: linear-gradient(90deg, #166534, #16a34a, #4ade80);
        }

        .card-body { padding: 36px 36px 32px; }
        @media (max-width: 480px) { .card-body { padding: 28px 24px; } }

        /* Mobile brand */
        .mobile-brand {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 28px;
        }
        @media (min-width: 1024px) { .mobile-brand { display: none; } }
        .mobile-mark {
            width: 40px; height: 40px; border-radius: 11px;
            background: #166534;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 900; color: #fff;
        }
        .mobile-name { font-size: 16px; font-weight: 800; color: #0f172a; }
        .mobile-sub  { font-size: 11px; color: #94a3b8; }

        .card-heading { margin: 0 0 28px; }
        .card-heading h1 { font-size: 22px; font-weight: 900; color: #0f172a; margin: 0 0 4px; }
        .card-heading p  { font-size: 13px; color: #94a3b8; margin: 0; }

        /* Error */
        .alert-error {
            display: flex; align-items: flex-start; gap: 10px;
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 12px; padding: 12px 14px;
            margin-bottom: 20px;
        }
        .alert-error svg { width: 16px; height: 16px; color: #dc2626; flex-shrink: 0; margin-top: 1px; }
        .alert-error span { font-size: 13px; color: #dc2626; font-weight: 500; }

        /* Form */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; font-size: 13px; font-weight: 600;
            color: #374151; margin-bottom: 7px;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; top: 50%; transform: translateY(-50%);
            right: 13px; pointer-events: none;
            display: flex; align-items: center;
        }
        .input-icon svg { width: 16px; height: 16px; color: #94a3b8; }
        .input-toggle {
            position: absolute; top: 50%; transform: translateY(-50%);
            left: 13px; background: none; border: none; cursor: pointer;
            display: flex; align-items: center; padding: 0; color: #94a3b8;
        }
        .input-toggle:hover { color: #475569; }
        .input-toggle svg { width: 16px; height: 16px; }

        .form-input {
            width: 100%; padding: 11px 40px 11px 14px;
            font-size: 14px; font-family: 'Tajawal', sans-serif;
            color: #0f172a; background: #f8fafc;
            border: 1.5px solid #e2e8f0; border-radius: 11px;
            outline: none; transition: all .2s;
        }
        .form-input::placeholder { color: #cbd5e1; }
        .form-input:focus {
            background: #fff;
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22,163,74,0.12);
        }
        .form-input.has-toggle { padding-left: 40px; }
        .form-input.is-error { border-color: #fca5a5; background: #fef2f2; }

        .form-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px;
        }
        .remember-label {
            display: flex; align-items: center; gap: 7px;
            font-size: 13px; color: #475569; cursor: pointer;
        }
        .remember-check {
            width: 16px; height: 16px; border-radius: 4px;
            border: 1.5px solid #d1d5db; cursor: pointer;
            accent-color: #16a34a;
        }

        .btn-submit {
            width: 100%;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: linear-gradient(135deg, #15803d, #166534);
            color: #fff; font-size: 15px; font-weight: 700;
            font-family: 'Tajawal', sans-serif;
            padding: 13px 24px; border-radius: 12px;
            border: none; cursor: pointer;
            box-shadow: 0 4px 16px rgba(21,128,61,0.35);
            transition: all .25s;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #16a34a, #15803d);
            box-shadow: 0 6px 22px rgba(21,128,61,0.45);
            transform: translateY(-1px);
        }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit svg { width: 17px; height: 17px; }

        .card-footer {
            margin-top: 24px; padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }
        .back-link {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 13px; color: #94a3b8; text-decoration: none;
            transition: color .2s;
        }
        .back-link:hover { color: #16a34a; }
        .back-link svg { width: 14px; height: 14px; }
    </style>
</head>
<body>

    {{-- ══════════════════ LEFT: Branding ══════════════════ --}}
    <div class="panel-left">
        <div class="panel-ring-2"></div>

        <div class="panel-content">
            <div class="brand-mark">م</div>
            <h1 class="brand-name">مسالك النور</h1>
            <p class="brand-sub">جمعية خيرية معتمدة</p>
            <div class="panel-divider"></div>
            <p class="panel-tagline">
                منصة متكاملة لإدارة الجمعية الخيرية،
                تتيح لك متابعة الأعضاء والتبرعات
                والمصروفات بكل سهولة وشفافية.
            </p>

            <ul class="features-list">
                <li>
                    <div class="feat-dot">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    إدارة الأعضاء والمستفيدين
                </li>
                <li>
                    <div class="feat-dot">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1"/>
                        </svg>
                    </div>
                    تتبع التبرعات والمصروفات
                </li>
                <li>
                    <div class="feat-dot">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    تقارير وإحصاءات تفصيلية
                </li>
            </ul>
        </div>

        <p class="panel-footer-text panel-content">
            &copy; {{ date('Y') }} جمعية مسالك النور — جميع الحقوق محفوظة
        </p>
    </div>

    {{-- ══════════════════ RIGHT: Form ══════════════════ --}}
    <div class="panel-right">
        <div class="login-card">
            <div class="card-top-bar"></div>
            <div class="card-body">

                {{-- Mobile brand --}}
                <div class="mobile-brand">
                    <div class="mobile-mark">م</div>
                    <div>
                        <p class="mobile-name">مسالك النور</p>
                        <p class="mobile-sub">جمعية خيرية</p>
                    </div>
                </div>

                <div class="card-heading">
                    <h1>مرحباً بك</h1>
                    <p>سجّل دخولك للوصول إلى لوحة التحكم</p>
                </div>

                @if ($errors->any())
                    <div class="alert-error">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="form-group">
                        <label class="form-label" for="email">البريد الإلكتروني</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <input
                                id="email" type="email" name="email"
                                value="{{ old('email') }}"
                                required autofocus autocomplete="email"
                                placeholder="example@domain.com"
                                class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                            >
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="form-group">
                        <label class="form-label" for="password">كلمة المرور</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input
                                id="password" type="password" name="password"
                                required autocomplete="current-password"
                                placeholder="••••••••"
                                class="form-input has-toggle"
                            >
                            <button type="button" class="input-toggle" onclick="togglePassword()">
                                <svg id="eye-open" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eye-closed" class="hidden" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember --}}
                    <div class="form-row">
                        <label class="remember-label">
                            <input id="remember" type="checkbox" name="remember" class="remember-check">
                            تذكرني
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">
                        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        تسجيل الدخول
                    </button>
                </form>

                <div class="card-footer">
                    <a href="{{ url('/') }}" class="back-link">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        العودة إلى الصفحة الرئيسية
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
</body>
</html>

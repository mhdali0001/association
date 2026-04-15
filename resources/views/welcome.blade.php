<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>مسالك النور — جمعية خيرية</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { font-family: 'Tajawal', sans-serif; box-sizing: border-box; }

        :root {
            --green-950: #052e16;
            --green-900: #14532d;
            --green-800: #166534;
            --green-700: #15803d;
            --green-600: #16a34a;
            --green-500: #22c55e;
            --green-400: #4ade80;
            --green-100: #dcfce7;
            --green-50:  #f0fdf4;
            --gold:      #d97706;
            --gold-light:#fef3c7;
            --slate-900: #0f172a;
            --slate-700: #334155;
            --slate-500: #64748b;
            --slate-300: #cbd5e1;
            --slate-100: #f1f5f9;
            --white:     #ffffff;
        }

        body { margin: 0; background: var(--white); color: var(--slate-900); }

        /* ── NAVBAR ── */
        .navbar {
            position: absolute; top: 0; inset-inline: 0; z-index: 40;
            padding: 20px 40px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .navbar-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-mark {
            width: 42px; height: 42px; border-radius: 12px;
            background: rgba(255,255,255,0.18);
            border: 1.5px solid rgba(255,255,255,0.30);
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 18px; color: #fff;
            backdrop-filter: blur(8px);
        }
        .logo-name  { font-weight: 800; font-size: 15px; color: #fff; line-height: 1.2; }
        .logo-sub   { font-size: 10px; color: rgba(255,255,255,0.55); }

        .nav-btn-ghost {
            font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.75);
            text-decoration: none; padding: 6px 12px; border-radius: 8px;
            transition: color .2s;
        }
        .nav-btn-ghost:hover { color: #fff; }
        .nav-btn-solid {
            font-size: 13px; font-weight: 700; color: var(--green-900);
            background: #fff; text-decoration: none;
            padding: 8px 20px; border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: box-shadow .2s, transform .2s;
        }
        .nav-btn-solid:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.2); transform: translateY(-1px); }

        /* ── HERO ── */
        .hero {
            position: relative; overflow: hidden;
            min-height: 100vh;
            display: flex; align-items: center;
            background: var(--green-950);
        }
        .hero-mesh {
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 110% 40%, rgba(22,163,74,0.22) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at -10% 70%, rgba(5,150,105,0.18) 0%, transparent 55%),
                radial-gradient(ellipse 40% 40% at 50% 100%, rgba(21,128,61,0.12) 0%, transparent 50%);
        }
        /* Geometric accent lines */
        .hero-lines {
            position: absolute; inset: 0; overflow: hidden;
            opacity: 0.06; pointer-events: none;
        }
        .hero-lines::before {
            content: '';
            position: absolute; top: -30%; right: -10%;
            width: 600px; height: 600px; border-radius: 50%;
            border: 80px solid #fff;
        }
        .hero-lines::after {
            content: '';
            position: absolute; bottom: -25%; left: 5%;
            width: 350px; height: 350px; border-radius: 50%;
            border: 50px solid #fff;
        }

        .hero-inner {
            position: relative; z-index: 10;
            max-width: 1100px; margin: 0 auto; padding: 140px 40px 80px;
            display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;
        }
        @media (max-width: 900px) {
            .hero-inner { grid-template-columns: 1fr; padding: 120px 24px 60px; }
            .hero-cards  { display: none; }
            .navbar { padding: 16px 24px; }
        }

        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(74,222,128,0.10);
            border: 1px solid rgba(74,222,128,0.25);
            border-radius: 100px; padding: 5px 14px;
            font-size: 12px; font-weight: 600; color: var(--green-400);
            margin-bottom: 22px;
        }
        .hero-eyebrow-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--green-400);
            animation: pulse 2s infinite;
        }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.4)} }

        .hero-title {
            font-size: clamp(40px, 6vw, 68px);
            font-weight: 900; color: #fff;
            line-height: 1.15; margin: 0 0 20px;
        }
        .hero-title-accent {
            background: linear-gradient(90deg, #4ade80, #86efac);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-desc {
            font-size: 16px; line-height: 1.85;
            color: rgba(255,255,255,0.60); margin: 0 0 36px; max-width: 420px;
        }
        .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }

        .btn-hero-primary {
            display: inline-flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, var(--green-600), var(--green-700));
            color: #fff; font-weight: 700; font-size: 14px;
            padding: 13px 28px; border-radius: 12px; text-decoration: none;
            box-shadow: 0 4px 20px rgba(22,163,74,0.40);
            transition: all .25s;
        }
        .btn-hero-primary:hover {
            background: linear-gradient(135deg, var(--green-500), var(--green-600));
            box-shadow: 0 6px 28px rgba(22,163,74,0.55);
            transform: translateY(-2px);
        }
        .btn-hero-secondary {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.18);
            color: rgba(255,255,255,0.85); font-weight: 600; font-size: 14px;
            padding: 13px 28px; border-radius: 12px; text-decoration: none;
            backdrop-filter: blur(8px);
            transition: all .25s;
        }
        .btn-hero-secondary:hover {
            background: rgba(255,255,255,0.14);
            border-color: rgba(255,255,255,0.30);
        }

        /* Stat cards in hero */
        .hero-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .stat-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 16px; padding: 22px;
            backdrop-filter: blur(12px);
            transition: all .3s;
        }
        .stat-card:hover {
            background: rgba(255,255,255,0.09);
            border-color: rgba(74,222,128,0.25);
            transform: translateY(-3px);
        }
        .stat-card.wide { grid-column: span 2; }
        .stat-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: rgba(74,222,128,0.12);
            border: 1px solid rgba(74,222,128,0.20);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 14px;
        }
        .stat-icon svg { width: 18px; height: 18px; color: var(--green-400); }
        .stat-number { font-size: 30px; font-weight: 900; color: #fff; line-height: 1; }
        .stat-label  { font-size: 12px; color: rgba(255,255,255,0.45); margin-top: 5px; }
        .stat-wide-content { display: flex; align-items: center; gap: 16px; }
        .stat-wide-text .stat-title  { font-size: 14px; font-weight: 700; color: #fff; }
        .stat-wide-text .stat-sub    { font-size: 12px; color: rgba(255,255,255,0.45); margin-top: 3px; }

        @keyframes floatY {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-7px); }
        }
        .float-1 { animation: floatY 5s ease-in-out infinite; }
        .float-2 { animation: floatY 5s ease-in-out 1.2s infinite; }
        .float-3 { animation: floatY 5s ease-in-out 0.6s infinite; }

        /* Hero wave */
        .hero-wave {
            position: absolute; bottom: -1px; inset-inline: 0; pointer-events: none;
        }

        /* ── SECTION: FEATURES ── */
        .section-features { padding: 90px 40px; background: var(--white); }
        @media (max-width: 700px) { .section-features { padding: 60px 24px; } }

        .section-label {
            display: inline-block;
            font-size: 11px; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--green-700);
            background: var(--green-50); border: 1px solid var(--green-100);
            border-radius: 100px; padding: 5px 14px; margin-bottom: 14px;
        }
        .section-title {
            font-size: clamp(26px, 4vw, 36px); font-weight: 900;
            color: var(--slate-900); margin: 0 0 12px;
        }
        .section-desc { font-size: 15px; color: var(--slate-500); max-width: 500px; margin: 0 auto; line-height: 1.8; }

        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 52px; }
        @media (max-width: 900px) { .features-grid { grid-template-columns: 1fr; } }

        .feature-card {
            border: 1.5px solid #e9f2ee;
            border-radius: 18px; padding: 28px;
            background: var(--white);
            transition: all .3s;
            position: relative; overflow: hidden;
        }
        .feature-card::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, var(--green-50) 0%, transparent 60%);
            opacity: 0; transition: opacity .3s;
        }
        .feature-card:hover { border-color: #86efac; box-shadow: 0 12px 32px rgba(22,163,74,0.10); transform: translateY(-4px); }
        .feature-card:hover::before { opacity: 1; }

        .feature-icon {
            width: 52px; height: 52px; border-radius: 14px;
            background: linear-gradient(135deg, var(--green-100), #bbf7d0);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px; position: relative;
        }
        .feature-icon svg { width: 24px; height: 24px; color: var(--green-700); }
        .feature-title { font-size: 17px; font-weight: 800; color: var(--slate-900); margin: 0 0 8px; }
        .feature-desc  { font-size: 13px; color: var(--slate-500); line-height: 1.8; margin: 0; }

        /* ── STATS BAND ── */
        .section-stats {
            background: linear-gradient(135deg, var(--green-950) 0%, #0a3d24 100%);
            padding: 70px 40px;
        }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; max-width: 800px; margin: 0 auto; }
        @media (max-width: 700px) { .stats-grid { grid-template-columns: 1fr; } }
        .stat-item { text-align: center; padding: 30px 20px; }
        .stat-item-number {
            font-size: 52px; font-weight: 900;
            background: linear-gradient(135deg, #4ade80, #86efac);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; line-height: 1;
        }
        .stat-item-label { font-size: 13px; color: rgba(255,255,255,0.50); margin-top: 8px; font-weight: 500; }

        /* ── CTA ── */
        .section-cta {
            padding: 80px 40px; background: var(--slate-100);
            text-align: center;
        }
        .cta-box {
            max-width: 600px; margin: 0 auto;
            background: var(--white);
            border: 1.5px solid #e2e8f0;
            border-radius: 24px; padding: 52px 40px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.06);
        }
        .cta-icon {
            width: 56px; height: 56px; border-radius: 16px;
            background: linear-gradient(135deg, var(--green-100), #bbf7d0);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 22px;
        }
        .cta-icon svg { width: 26px; height: 26px; color: var(--green-700); }
        .cta-title { font-size: 24px; font-weight: 900; color: var(--slate-900); margin: 0 0 10px; }
        .cta-desc  { font-size: 14px; color: var(--slate-500); line-height: 1.8; margin: 0 0 30px; }
        .btn-cta {
            display: inline-flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, var(--green-700), var(--green-900));
            color: #fff; font-weight: 700; font-size: 14px;
            padding: 14px 32px; border-radius: 12px; text-decoration: none;
            box-shadow: 0 4px 18px rgba(15,83,52,0.30);
            transition: all .25s;
        }
        .btn-cta:hover { box-shadow: 0 6px 26px rgba(15,83,52,0.45); transform: translateY(-2px); }

        /* ── FOOTER ── */
        .footer {
            background: var(--white); border-top: 1px solid #f0f4f0;
            padding: 28px 40px;
        }
        .footer-inner {
            max-width: 1100px; margin: 0 auto;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            flex-wrap: wrap;
        }
        .footer-brand { display: flex; align-items: center; gap: 10px; }
        .footer-mark {
            width: 32px; height: 32px; border-radius: 9px;
            background: var(--green-800);
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 14px; color: #fff;
        }
        .footer-name { font-size: 13px; font-weight: 700; color: var(--slate-700); }
        .footer-copy { font-size: 12px; color: var(--slate-300); }
        .footer-badge { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--slate-300); }
        .footer-dot   { width: 7px; height: 7px; border-radius: 50%; background: var(--green-500); }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up        { animation: fadeUp .7s ease both; }
        .fade-up-d1     { animation: fadeUp .7s .15s ease both; opacity: 0; }
        .fade-up-d2     { animation: fadeUp .7s .30s ease both; opacity: 0; }
    </style>
</head>
<body>

    {{-- ══════════════════════ NAVBAR ══════════════════════ --}}
    <nav class="navbar">
        <a href="/" class="navbar-logo">
            <div class="logo-mark">م</div>
            <div>
                <p class="logo-name">مسالك النور</p>
                <p class="logo-sub">جمعية خيرية</p>
            </div>
        </a>

        @if(Route::has('login'))
            <div style="display:flex;align-items:center;gap:8px;">
                @auth
                    <a href="{{ url('/dashboard') }}" class="nav-btn-solid">لوحة التحكم</a>
                @else
                    <a href="{{ route('login') }}" class="nav-btn-ghost">تسجيل الدخول</a>
                    <a href="{{ route('login') }}" class="nav-btn-solid">ابدأ الآن</a>
                @endauth
            </div>
        @endif
    </nav>

    {{-- ══════════════════════ HERO ══════════════════════ --}}
    <section class="hero">
        <div class="hero-mesh"></div>
        <div class="hero-lines"></div>

        <div class="hero-inner">
            {{-- Text column --}}
            <div class="fade-up">
                <div class="hero-eyebrow">
                    <span class="hero-eyebrow-dot"></span>
                    جمعية خيرية معتمدة
                </div>

                <h1 class="hero-title">
                    جمعية<br>
                    <span class="hero-title-accent">مسالك النور</span>
                </h1>

                <p class="hero-desc">
                    نسعى إلى بناء مجتمع متماسك قائم على القيم والتعاون،
                    من خلال برامج دعم إنساني وتنموية هادفة تخدم أبناء المجتمع.
                </p>

                <div class="hero-actions">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-hero-primary">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                            الدخول إلى لوحة التحكم
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-hero-primary">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            تسجيل الدخول
                        </a>
                        <a href="#about" class="btn-hero-secondary">تعرف علينا</a>
                    @endauth
                </div>
            </div>

            {{-- Cards column --}}
            <div class="hero-cards fade-up-d1">
                <div class="stat-card wide float-1">
                    <div class="stat-wide-content">
                        <div class="stat-icon" style="margin-bottom:0;flex-shrink:0;">
                            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="stat-wide-text">
                            <div class="stat-title">نظام إدارة متكامل</div>
                            <div class="stat-sub">إدارة الأعضاء، التبرعات والمصروفات</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Wave --}}
        <div class="hero-wave">
            <svg viewBox="0 0 1440 64" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;height:64px;display:block;">
                <path d="M0 64 L0 38 Q360 8 720 38 Q1080 64 1440 38 L1440 64 Z" fill="white"/>
            </svg>
        </div>
    </section>

    {{-- ══════════════════════ FEATURES ══════════════════════ --}}
    <section id="about" class="section-features">
        <div style="max-width:1100px;margin:0 auto;">
            <div style="text-align:center;">
                <span class="section-label">ما نقدمه</span>
                <h2 class="section-title">خدماتنا وبرامجنا</h2>
                <p class="section-desc">نعمل على تقديم أفضل الخدمات الإنسانية والاجتماعية لدعم المحتاجين وتمكين المجتمع</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">التعليم والتنمية</h3>
                    <p class="feature-desc">برامج تعليمية وتدريبية متنوعة لتنمية مهارات أبناء المجتمع وتأهيلهم للمستقبل</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">الدعم الإنساني</h3>
                    <p class="feature-desc">تقديم المساعدات المادية والعينية للأسر المحتاجة وضمان تلبية احتياجاتهم الأساسية</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">بناء المجتمع</h3>
                    <p class="feature-desc">تعزيز الروابط المجتمعية وبناء جسور التواصل والتكافل بين أبناء المنطقة</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════ STATS BAND ══════════════════════ --}}
    <section class="section-stats">
        <div class="stats-grid" style="grid-template-columns:1fr;">
            <div class="stat-item">
                <div class="stat-item-number">100%</div>
                <div class="stat-item-label">شفافية في الإدارة</div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════ CTA ══════════════════════ --}}
    <section class="section-cta">
        <div class="cta-box">
            <div class="cta-icon">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h2 class="cta-title">هل أنت من فريق الجمعية؟</h2>
            <p class="cta-desc">سجّل دخولك للوصول إلى نظام الإدارة المتكامل<br>لإدارة الأعضاء والتبرعات والمصروفات</p>
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-cta">
                    الدخول إلى لوحة التحكم
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-cta">
                    تسجيل الدخول الآن
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            @endauth
        </div>
    </section>

    {{-- ══════════════════════ FOOTER ══════════════════════ --}}
    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <div class="footer-mark">م</div>
                <span class="footer-name">مسالك النور</span>
            </div>
            <p class="footer-copy">&copy; {{ date('Y') }} جمعية مسالك النور &mdash; جميع الحقوق محفوظة</p>
            <div class="footer-badge">
                <span class="footer-dot"></span>
                نظام إدارة الجمعية
            </div>
        </div>
    </footer>

</body>
</html>

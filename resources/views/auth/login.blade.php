<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <!-- Add the viewport meta tag -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'كنترول') }}</title>

    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Optional: Include Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <!-- Modern font for the new login design -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap">

    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">

    <style>
        .login {
            background: url({{ asset('assets/admin/images/login.jpg') }}) no-repeat center center fixed;
            background-size: cover;
        }

        /* Remove fixed width */
        .loginBox {
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 0.75rem;
            overflow: hidden;
            margin-top: 1.5rem;
            /* Remove any fixed width or max-width properties */
        }

        :root {
            --modern-ink: #0f172a;
            --modern-muted: #475569;
            --modern-accent: #f59e0b;
            --modern-accent-2: #0ea5e9;
            --modern-surface: rgba(255, 255, 255, 0.78);
            --modern-border: rgba(255, 255, 255, 0.65);
        }

        body[data-login-theme="legacy"] .login-modern {
            display: none !important;
            pointer-events: none;
        }

        body[data-login-theme="modern"] .login-legacy {
            display: none !important;
            pointer-events: none;
        }

        body[data-login-theme="modern"] .login-modern {
            display: block !important;
            pointer-events: auto;
        }

        .login-theme-toggle {
            position: fixed;
            top: 1.25rem;
            left: 1.25rem;
            z-index: 20;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(15, 23, 42, 0.2);
            color: #0f172a;
            font-size: 0.9rem;
            backdrop-filter: blur(6px);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
        }

        .login-theme-toggle .form-check-input {
            width: 2.6rem;
            height: 1.4rem;
            cursor: pointer;
        }

        .login-theme-toggle .form-check-input:checked {
            background-color: #0ea5e9;
            border-color: #0ea5e9;
        }

        .login-theme-toggle .form-check-label {
            cursor: pointer;
            font-weight: 600;
        }

        .login-modern {
            font-family: "Cairo", "Segoe UI", Tahoma, Arial, sans-serif;
            position: relative;
            background: radial-gradient(circle at top right, #dbeafe 0%, #f8fafc 35%, #fff7ed 100%);
            overflow: visible;
        }

        .login-modern-layout {
            align-items: center;
        }

        .login-modern {
            height: auto !important;
            min-height: 100svh;
            overflow: visible;
        }

        body[data-login-theme="modern"] {
            overflow-y: auto !important;
            overflow-x: hidden !important;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-y: auto;
            height: auto !important;
            min-height: 100%;
        }

        html {
            overflow-y: auto;
        }

        html,
        body {
            min-height: 100%;
            overflow-x: hidden;
        }

        .login-modern .modern-shell {
            padding-top: 2.5rem;
            padding-bottom: 2.5rem;
        }

        body[data-login-theme="modern"] .login-modern .container,
        body[data-login-theme="modern"] .login-modern .row,
        body[data-login-theme="modern"] .login-modern [class*="col-"] {
            overflow: visible !important;
            min-height: 0;
        }

        .login-modern::before,
        .login-modern::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            filter: blur(0.5px);
            opacity: 0.6;
            pointer-events: none;
        }

        .login-modern::before {
            width: 360px;
            height: 360px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.35) 0%, rgba(14, 165, 233, 0) 70%);
            top: -80px;
            right: -120px;
        }

        .login-modern::after {
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.35) 0%, rgba(245, 158, 11, 0) 70%);
            bottom: -140px;
            left: -120px;
        }

        .modern-shell {
            position: relative;
            z-index: 1;
        }

        .modern-card {
            background: var(--modern-surface);
            border: 1px solid var(--modern-border);
            border-radius: 1.5rem;
            padding: 2.25rem;
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(12px);
        }

        .modern-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(14, 165, 233, 0.12);
            color: #0369a1;
            padding: 0.35rem 0.9rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .modern-title {
            color: var(--modern-ink);
            font-weight: 700;
            font-size: clamp(1.6rem, 2.5vw, 2.4rem);
            line-height: 1.3;
        }

        .modern-subtitle {
            color: var(--modern-muted);
            font-size: 1rem;
        }

        .modern-list {
            list-style: none;
            padding: 0;
            margin: 1.5rem 0 0;
        }

        .modern-list li {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.75rem;
            color: var(--modern-muted);
        }

        .modern-form .form-control {
            border-radius: 0.9rem;
            border: 1px solid rgba(15, 23, 42, 0.15);
            background: rgba(255, 255, 255, 0.9);
        }

        .modern-form .form-control:focus {
            border-color: rgba(14, 165, 233, 0.6);
            box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.2);
        }

        .modern-primary {
            background: linear-gradient(120deg, #f59e0b, #f97316);
            border: none;
            color: #1f2937;
            font-weight: 700;
            border-radius: 0.9rem;
            padding: 0.65rem 1.4rem;
        }

        .modern-secondary {
            background: rgba(14, 165, 233, 0.12);
            color: #0369a1;
            border: none;
            border-radius: 0.9rem;
        }

        .login-sponsor-box {
            margin-top: 0.9rem;
            padding: 0.75rem 0.85rem;
            border-radius: 0.85rem;
            border: 1px solid rgba(15, 23, 42, 0.12);
            background: rgba(255, 255, 255, 0.86);
            color: #334155;
            font-size: 0.92rem;
            line-height: 1.65;
        }

        .login-sponsor-box strong {
            color: #0f172a;
        }

        .login-sponsor-phone {
            direction: ltr;
            unicode-bidi: plaintext;
            font-weight: 800;
            color: #075985;
            text-decoration: none;
        }

        .login-preview-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.45rem;
            font-weight: 700;
            color: #0369a1;
            text-decoration: none;
        }

        .login-preview-link:hover {
            color: #0c4a6e;
            text-decoration: underline;
        }

        .login-sponsor-actions {
            margin-top: 0.55rem;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .login-action-icon {
            width: 2.2rem;
            height: 2.2rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: 1px solid rgba(15, 23, 42, 0.14);
            background: rgba(255, 255, 255, 0.92);
            color: #0f172a;
            transition: transform 150ms ease, box-shadow 150ms ease;
        }

        .login-action-icon i {
            font-size: 1rem;
        }

        .login-action-icon:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(15, 23, 42, 0.12);
            color: #0f172a;
        }

        .login-action-icon.whatsapp {
            color: #128C7E;
            border-color: rgba(18, 140, 126, 0.35);
            background: rgba(37, 211, 102, 0.12);
        }

        .login-modern-stack {
            max-width: 760px;
            margin: 0 auto;
        }

        .login-modern-stack > .col-12:last-child {
            margin-bottom: 1.25rem;
        }

        .modern-sponsor-card {
            border: 1px solid rgba(14, 165, 233, 0.24);
            background: linear-gradient(130deg, rgba(14, 165, 233, 0.12), rgba(255, 255, 255, 0.92));
        }

        .sponsor-bounce {
            display: inline-flex;
            animation: sponsorBounce 1.6s ease-in-out infinite;
            transform-origin: center bottom;
        }

        @keyframes sponsorBounce {
            0%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-5px);
            }
            60% {
                transform: translateY(-2px);
            }
        }

        @media (max-width: 991px) {
            .login-modern-layout {
                align-items: initial;
            }

            .login-modern .modern-shell {
                padding-top: max(1.25rem, env(safe-area-inset-top));
                padding-bottom: calc(2rem + env(safe-area-inset-bottom));
            }

            .login-modern .modern-card {
                padding: 1.2rem;
            }

            .login-modern {
                scroll-padding-bottom: calc(2rem + env(safe-area-inset-bottom));
            }

            body[data-login-theme="modern"] {
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            body[data-login-theme="modern"] .login-theme-toggle {
                position: absolute;
            }
        }

        /* === Enterprise AJAX Login Overlay (Modern Theme Only) === */
        .login-modern {
            position: relative;
        }

        body[data-login-ajax-state="submitting"] .login-modern,
        body[data-login-ajax-state="animating"] .login-modern {
            pointer-events: none;
            user-select: none;
        }

        .login-modern .modern-form.is-frozen {
            opacity: 0.92;
            filter: grayscale(0.12);
        }

        .login-enterprise-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            width: 100vw;
            height: 100vh;
            padding: 0;
            opacity: 0;
            will-change: opacity;
        }

        .login-enterprise-overlay.is-visible {
            display: flex;
        }

        /* Fullscreen immersive layer (no modal window) */
        .login-enterprise-overlay .glass {
            position: relative;
            width: 100vw;
            height: 100vh;
            border-radius: 0;
            padding: 0;
            background: linear-gradient(135deg, rgba(2, 6, 23, 0.96), rgba(15, 23, 42, 0.94) 45%, rgba(2, 6, 23, 0.98));
            border: 0;
            backdrop-filter: blur(12px);
            box-shadow: none;
            overflow: hidden;
            color: #e2e8f0;
            will-change: transform, opacity;
        }

        /* Moving light gradient */
        .login-enterprise-overlay .glass::before {
            content: "";
            position: absolute;
            inset: -35%;
            background:
                radial-gradient(circle at 25% 20%, rgba(14, 165, 233, 0.26), rgba(14, 165, 233, 0) 55%),
                radial-gradient(circle at 70% 65%, rgba(245, 158, 11, 0.18), rgba(245, 158, 11, 0) 60%),
                radial-gradient(circle at 40% 85%, rgba(99, 102, 241, 0.14), rgba(99, 102, 241, 0) 55%);
            filter: blur(34px);
            opacity: 0.55;
            transform: translate3d(0, 0, 0);
            animation: overlayDrift 6.5s ease-in-out infinite;
        }

        /* Subtle noise layer */
        .login-enterprise-overlay .glass::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                repeating-linear-gradient(0deg, rgba(255, 255, 255, 0.018), rgba(255, 255, 255, 0.018) 1px, rgba(255, 255, 255, 0) 2px, rgba(255, 255, 255, 0) 4px);
            opacity: 0.22;
            mix-blend-mode: overlay;
            animation: noiseShift 1.6s steps(2, end) infinite;
        }

        @keyframes overlayDrift {
            0% { transform: translate3d(-2%, -1%, 0) scale(1.02); }
            50% { transform: translate3d(2%, 1%, 0) scale(1.04); }
            100% { transform: translate3d(-2%, -1%, 0) scale(1.02); }
        }

        @keyframes noiseShift {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-2%, 1%, 0); }
        }

        .login-enterprise-overlay .content {
            position: relative;
            z-index: 1;
            height: 100%;
            display: grid;
            place-items: center;
            padding: clamp(1.5rem, 3.5vw, 2.4rem) clamp(1rem, 2.8vw, 1.9rem);
        }

        .login-enterprise-overlay .content > div {
            width: min(620px, 94vw);
        }

        .scan-frame {
            width: clamp(80px, 14vw, 105px);
            height: clamp(110px, 5vw, 60px);
            margin: 0 auto 6px;
            border-radius: 999px;
            position: relative;
            display: grid;
            place-items: center;
            will-change: transform;
        }

        .scan-frame .ring {
            position: absolute;
            inset: 0;
            border-radius: 999px;
            border: 2px solid rgba(226, 232, 240, 0.35);
            box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.65) inset;
        }

        .scan-frame .ring.glow {
            border-color: rgba(14, 165, 233, 0.85);
            box-shadow:
                0 0 0 1px rgba(14, 165, 233, 0.35) inset,
                0 0 22px rgba(14, 165, 233, 0.55),
                0 0 62px rgba(245, 158, 11, 0.25);
            opacity: 0;
            will-change: opacity, transform;
        }

        .scan-frame .sweep {
            position: absolute;
            inset: -8px;
            border-radius: 999px;
            background: conic-gradient(from 0deg, rgba(14, 165, 233, 0) 0deg, rgba(14, 165, 233, 0.0) 210deg, rgba(14, 165, 233, 0.55) 260deg, rgba(14, 165, 233, 0) 320deg, rgba(14, 165, 233, 0) 360deg);
            filter: blur(0.2px);
            opacity: 0.85;
            mix-blend-mode: screen;
        }

        .scan-avatar {
            width: clamp(120px, 22vw, 168px);
            height: clamp(120px, 22vw, 168px);
            border-radius: 999px;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 20px 50px rgba(2, 6, 23, 0.55);
            background: rgba(2, 6, 23, 0.25);
            will-change: filter, transform, opacity;
        }

        .scan-check {
            position: absolute;
            bottom: -10px;
            right: -6px;
            width: 54px;
            height: 54px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: rgba(2, 6, 23, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.16);
            box-shadow: 0 18px 40px rgba(2, 6, 23, 0.55);
            opacity: 0;
            transform: scale(0.9);
            will-change: transform, opacity;
        }

        .scan-check i {
            font-size: 1.6rem;
            color: rgba(25, 135, 84, 0.95);
            filter: drop-shadow(0 0 14px rgba(25, 135, 84, 0.35));
        }

        .scan-status {
            margin-top: 0;
            text-align: center;
            padding-inline: 0.35rem;
        }

        .scan-status .sys {
            font-weight: 800;
            font-size: clamp(1.02rem, 2.7vw, 1.22rem);
            line-height: 1.6;
            letter-spacing: 0.1px;
            color: rgba(226, 232, 240, 0.92);
            margin: 0;
        }

        .scan-messages {
            list-style: none;
            margin: 0.05rem 0 0;
            padding: 0;
            display: grid;
            gap: 0.42rem;
            text-align: center;
            color: rgba(226, 232, 240, 0.82);
            font-size: clamp(0.92rem, 2.35vw, 1.02rem);
            line-height: 1.7;
        }

        .scan-messages li {
            padding: 0.28rem 0.62rem;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.10);
            border: 1px solid rgba(148, 163, 184, 0.16);
            backdrop-filter: blur(2px);
        }

        .scan-welcome {
            display: none;
            margin-top: 0.1rem;
            text-align: center;
        }

        .scan-welcome .hello {
            font-size: clamp(1.2rem, 3.2vw, 1.52rem);
            font-weight: 800;
            line-height: 1.45;
            letter-spacing: 0.15px;
            color: #f8fafc;
        }

        .scan-welcome .sub {
            margin-top: 0.25rem;
            font-size: clamp(0.93rem, 2.25vw, 1rem);
            line-height: 1.65;
            color: rgba(226, 232, 240, 0.78);
        }

        .scan-welcome .go {
            margin-top: 0.75rem;
            width: 100%;
            border: 0;
            border-radius: 1rem;
            padding: 0.9rem 1.05rem;
            font-weight: 800;
            font-size: 0.98rem;
            color: #0b1220;
            background: linear-gradient(90deg, rgba(14, 165, 233, 0.95), rgba(245, 158, 11, 0.95));
            box-shadow: 0 18px 40px rgba(14, 165, 233, 0.22);
        }

        @media (max-width: 576px) {
            .scan-status {
                margin-top: 0;
            }

            .scan-messages {
                gap: 0.36rem;
            }

            .scan-messages li {
                padding: 0.24rem 0.52rem;
            }

            .scan-welcome {
                margin-top: 0.36rem;
            }

            .scan-frame {
                margin-bottom: -7px;
            }
        }

        .scan-welcome .go:active {
            transform: translateY(1px);
        }
    </style>
</head>

@php
    $uiPolicy = setting(\App\Enums\SettingKey::UI_MODE_POLICY->value, true) ?: 'user_choice';
    $uiPolicy = in_array($uiPolicy, ['user_choice', 'modern', 'classic'], true) ? $uiPolicy : 'user_choice';
    $loginForcedTheme = $uiPolicy === 'modern' ? 'modern' : ($uiPolicy === 'classic' ? 'legacy' : 'legacy');
    $supportCountryCode = preg_replace('/\D+/', '', (string) config('app.support_country_code', '965')) ?: '965';
    $supportPhoneDigits = preg_replace('/\D+/', '', (string) config('app.support_phone', '55150551')) ?: '55150551';
    $supportCallLink = 'tel:+' . $supportCountryCode . $supportPhoneDigits;
    $supportWhatsappLink = 'https://wa.me/' . $supportCountryCode . $supportPhoneDigits . '?text=' . rawurlencode('ممكن استفسر عن https://kw-control.com/');
@endphp

<body data-login-theme="{{ $loginForcedTheme }}">

    @if (session('success'))
        <div class="alert alert-success" style="color: #25a421;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($uiPolicy === 'user_choice')
        <div class="login-theme-toggle">
            <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" id="loginThemeToggle" aria-pressed="false">
                <label class="form-check-label" for="loginThemeToggle">الشكل الحديث</label>
            </div>
        </div>
    @endif

    <section class="pt-5 login vh-100 login-legacy">
        <!-- Use container-fluid instead of container -->
        <div class="container-fluid mt-5">
                <div class="row justify-content-center">
                    <div class="col-10 col-sm-10 col-xs-12 col-md-6 col-lg-4 ">
                        <h4 class="bg-dark text-white rounded-2 py-1 text-center text-uppercase">control</h4>
                    <h4 class="bg-dark text-white rounded-2 py-1 text-center">كنترول الانتخابات</h4>
                    </div>
                </div>
            <div class="row justify-content-center">
                <div class="col-8 col-sm-10 col-xs-12 col-md-6 col-lg-4">
                    <div class="loginBox">
                        <form method="POST" class="p-4" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <input type="text" placeholder="أدخل رقم الهاتف - الايميل" name="login" id="login"
                                    required autofocus class="form-control">
                                <x-input-error :messages="$errors->get('login')" class="mt-2" />
                            </div>
                            <div class="mb-3">
                                <input type="password" placeholder="الرقم السرى" name="password" id="userPassword"
                                    class="form-control">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="rememberMe">
                                    <label class="form-check-label" for="rememberMe">تذكرنى</label>
                                </div>
                                <button type="submit" class="btn btn-warning mt-2">تسجيل دخول</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="login-modern login-modern-layout">
        <div class="container modern-shell">
            <div class="row g-4 login-modern-stack">
                <div class="col-12">
                    <div class="modern-card modern-sponsor-card">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <span class="modern-badge sponsor-bounce">
                                    <i class="bi bi-megaphone-fill"></i>
                                    رعاية ودعم
                                </span>
                                <h5 class="modern-title mt-2 mb-1">هذا الموقع برعاية أحمد خلف</h5>
                                <p class="modern-subtitle mb-1">للاستفسار:</p>
                                <div class="login-sponsor-actions" aria-label="أزرار التواصل">
                                    <a href="{{ $supportCallLink }}" class="login-action-icon" aria-label="اتصال مباشر" title="اتصال مباشر">
                                        <i class="bi bi-telephone-fill"></i>
                                    </a>
                                    <a href="{{ $supportWhatsappLink }}" target="_blank" rel="noopener" class="login-action-icon whatsapp" aria-label="تواصل واتساب" title="تواصل واتساب">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                            <a href="{{ route('landing.control') }}" class="login-preview-link">لرؤية النظام من الداخل اضغط هنا <i class="bi bi-arrow-left-circle"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="modern-card modern-form" id="modernLoginCard">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="modern-title mb-1">تسجيل الدخول</h4>
                                <span class="modern-subtitle">النسخة الجديدة من لوحة التحكم</span>
                            </div>
                            <span class="modern-badge">نسخة جديدة</span>
                        </div>

                        <div class="alert alert-danger d-none" id="modernAjaxError" role="alert"></div>

                        <form method="POST" action="{{ route('login') }}" id="modernLoginForm" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">رقم الهاتف او الايميل</label>
                                <input type="text" placeholder="اكتب رقم الهاتف او الايميل" name="login" id="loginModern"
                                    required autofocus class="form-control">
                                <x-input-error :messages="$errors->get('login')" class="mt-2" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">الرقم السري</label>
                                <input type="password" placeholder="اكتب الرقم السري" name="password" id="userPasswordModern"
                                    class="form-control">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="rememberMeModern" name="remember" value="1">
                                    <label class="form-check-label" for="rememberMeModern">تذكرني</label>
                                </div>
                                <button type="submit" class="btn modern-primary" id="modernLoginSubmit">تسجيل دخول</button>
                            </div>
                        </form>
                    </div>
                </div>

                    <!--
                <div class="col-12">
                    <div class="modern-card h-100">
                        <span class="modern-badge">
                            <i class="bi bi-stars"></i>
                            تجربة محدثة
                        </span>
                        <h1 class="modern-title mt-3">كنترول الانتخابات</h1>
                        <p class="modern-subtitle mt-3">
                            دخول آمن، واجهة خفيفة، وتفاصيل تساعدك تركز على الشغل من أول لحظة.
                        </p>
                        <ul class="modern-list">
                            <li><i class="bi bi-shield-check"></i> خصوصية أعلى مع تصميم هادئ للعين.</li>
                            <li><i class="bi bi-speedometer2"></i> وصول أسرع لأدوات التحكم.</li>
                            <li><i class="bi bi-sun"></i> ألوان متوازنة تعكس التطوير المستمر.</li>
                        </ul>
                    </div>
                </div>
                        -->
            </div>
        </div>
    </section>

    <!-- Enterprise login overlay (AJAX success sequence) -->
    <div class="login-enterprise-overlay" id="loginEnterpriseOverlay" aria-hidden="true">
        <div class="glass">
            <div class="content">
                <div class="scan-frame">
                    <div class="ring"></div>
                    <div class="ring glow" id="scanGlowRing"></div>
                    <div class="sweep" id="scanSweep"></div>
                    <img class="scan-avatar" id="scanAvatar" src="{{ asset('assets/admin/images/users/user-placeholder.png') }}" alt="profile">
                </div>

                <div class="scan-status">
                    <div class="sys" id="scanSysTitle">جاري بدء التحقق...</div>
                    <ul class="scan-messages" id="scanMessages" aria-live="polite"></ul>
                </div>

                <div class="scan-welcome" id="scanWelcome">
                    <div class="hello" id="scanHello">أهلاً بك</div>
                    <div class="sub">تم تسجيل دخولك بنجاح</div>
                    <button type="button" class="go" id="goDashboardBtn">دخول لوحة التحكم</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- GSAP (animation engine) -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>

    <!-- Include your custom JS files if needed -->
    <script src="{{ asset('assets/admin/js/main.js') }}"></script>

    <script>
        (function () {
            var storageKey = "loginTheme";
            var uiModeKey = "ui_mode";
            var uiModePendingKey = "ui_mode_pending";
            var toggleButton = document.getElementById("loginThemeToggle");
            var UI_POLICY = {!! json_encode($uiPolicy) !!};

            function applyTheme(theme) {
                document.body.setAttribute("data-login-theme", theme);
                if (toggleButton) {
                    toggleButton.checked = theme === "modern";
                    toggleButton.setAttribute("aria-pressed", theme === "modern" ? "true" : "false");
                }
            }

            function getInitialTheme() {
                if (UI_POLICY === 'modern') {
                    return 'modern';
                }
                if (UI_POLICY === 'classic') {
                    return 'legacy';
                }

                var theme = sessionStorage.getItem(storageKey);
                if (theme === "modern" || theme === "legacy") {
                    return theme;
                }

                // Fall back to global ui_mode so homepage/dashboard stays consistent
                try {
                    var ui = localStorage.getItem(uiModeKey);
                    if (ui === 'modern') {
                        return 'modern';
                    }
                } catch (e) {
                    // ignore
                }

                return "legacy";
            }

            function setTheme(theme) {
                if (UI_POLICY === 'modern') {
                    theme = 'modern';
                }
                if (UI_POLICY === 'classic') {
                    theme = 'legacy';
                }

                sessionStorage.setItem(storageKey, theme);
                applyTheme(theme);

                // Keep global ui_mode consistent across login → homepage → dashboard.
                // Mark as pending so the first authenticated page can persist it to DB.
                try {
                    localStorage.setItem(uiModeKey, theme === 'modern' ? 'modern' : 'classic');
                    if (UI_POLICY !== 'modern' && UI_POLICY !== 'classic') {
                        localStorage.setItem(uiModePendingKey, '1');
                    } else {
                        localStorage.removeItem(uiModePendingKey);
                    }
                } catch (e) {
                    // ignore
                }
            }

            applyTheme(getInitialTheme());

            if (toggleButton && UI_POLICY === 'user_choice') {
                toggleButton.addEventListener("change", function (event) {
                    setTheme(event.target.checked ? "modern" : "legacy");
                });
            }

            function canUseAjaxLogin() {
                return !!(window.fetch && window.Promise && window.gsap);
            }

            function getModernForm() {
                return document.getElementById('modernLoginForm');
            }

            function freezeModernForm() {
                var card = document.getElementById('modernLoginCard');
                var form = getModernForm();
                var submit = document.getElementById('modernLoginSubmit');
                if (card) card.classList.add('is-frozen');
                if (submit) submit.setAttribute('disabled', 'disabled');
                if (form) {
                    Array.prototype.forEach.call(form.querySelectorAll('input, button'), function (el) {
                        if (el.id === 'goDashboardBtn') return;
                        el.setAttribute('disabled', 'disabled');
                    });
                }
                document.body.setAttribute('data-login-ajax-state', 'submitting');
            }

            function unfreezeModernForm() {
                var card = document.getElementById('modernLoginCard');
                var form = getModernForm();
                var submit = document.getElementById('modernLoginSubmit');
                if (card) card.classList.remove('is-frozen');
                if (submit) submit.removeAttribute('disabled');
                if (form) {
                    Array.prototype.forEach.call(form.querySelectorAll('input, button'), function (el) {
                        if (el.id === 'goDashboardBtn') return;
                        el.removeAttribute('disabled');
                    });
                }
                document.body.removeAttribute('data-login-ajax-state');
            }

            function setAjaxError(message) {
                var box = document.getElementById('modernAjaxError');
                if (!box) return;
                box.textContent = message || 'تعذر تسجيل الدخول. تحقق من البيانات وحاول مرة أخرى.';
                box.classList.remove('d-none');
            }

            function clearAjaxError() {
                var box = document.getElementById('modernAjaxError');
                if (!box) return;
                box.textContent = '';
                box.classList.add('d-none');
            }

            function showOverlay() {
                var overlay = document.getElementById('loginEnterpriseOverlay');
                if (!overlay) return;
                overlay.classList.add('is-visible');
                overlay.setAttribute('aria-hidden', 'false');
            }

            function setScanHello(userName) {
                var hello = document.getElementById('scanHello');
                if (hello) {
                    hello.textContent = 'أهلاً بك ' + (userName || '');
                }
            }

            function getScanImagesPool() {
                return [
                    "{{ asset('assets/admin/images/0.png') }}",
                    "{{ asset('assets/admin/images/3.png') }}",
                    "{{ asset('assets/admin/images/5.png') }}",
                    "{{ asset('assets/admin/images/9.png') }}",
                    "{{ asset('assets/admin/images/13.png') }}",
                    "{{ asset('assets/admin/images/16.png') }}",
                    "{{ asset('assets/admin/images/17.png') }}",
                    "{{ asset('assets/admin/images/users/user-placeholder.png') }}"
                ];
            }

            function safeUserImage(url) {
                if (!url || typeof url !== 'string') return "{{ asset('assets/admin/images/users/user-placeholder.png') }}";
                return url;
            }

            async function runEnterpriseSequence(payload) {
                var overlay = document.getElementById('loginEnterpriseOverlay');
                var avatar = document.getElementById('scanAvatar');
                var sweep = document.getElementById('scanSweep');
                var glow = document.getElementById('scanGlowRing');
                var messages = document.getElementById('scanMessages');
                var welcome = document.getElementById('scanWelcome');
                var goBtn = document.getElementById('goDashboardBtn');
                var sysTitle = document.getElementById('scanSysTitle');
                var redirectUrl = payload && payload.redirect ? payload.redirect : "{{ url('/') }}";
                var userName = payload && payload.user ? payload.user.name : '';
                var userImage = safeUserImage(payload && payload.user ? payload.user.image : null);

                if (!overlay || !avatar || !messages || !welcome || !goBtn) {
                    window.location.href = redirectUrl;
                    return;
                }

                document.body.setAttribute('data-login-ajax-state', 'animating');

                // Seed UI (stable layout, no shifts)
                messages.innerHTML = '';
                messages.style.minHeight = '0';
                messages.style.marginTop = '0';

                // No button in this experience
                if (goBtn) {
                    goBtn.style.display = 'none';
                }

                welcome.style.display = 'block';
                welcome.style.visibility = 'hidden';
                welcome.style.opacity = '0';

                if (sysTitle) {
                    sysTitle.textContent = 'جاري التحقق...';
                }

                if (glow) {
                    glow.style.opacity = '0';
                    glow.style.transform = 'scale(1)';
                    glow.style.borderColor = 'rgba(14, 165, 233, 0.85)';
                    glow.style.boxShadow = '0 0 0 1px rgba(14, 165, 233, 0.35) inset, 0 0 22px rgba(14, 165, 233, 0.35)';
                }
                if (sweep) {
                    sweep.style.opacity = '0';
                    sweep.style.transform = 'rotate(0deg)';
                }
                avatar.src = "{{ asset('assets/admin/images/users/user-placeholder.png') }}";

                var pool = getScanImagesPool();
                var rafId = 0;
                var autoRedirectTimer = 0;
                var dotsTick = 0;
                var scanCheck = null;

                // ensure check element exists
                (function ensureCheck() {
                    var frame = document.querySelector('.scan-frame');
                    if (!frame) return;
                    var existing = frame.querySelector('.scan-check');
                    if (existing) {
                        scanCheck = existing;
                        scanCheck.style.opacity = '0';
                        scanCheck.style.transform = 'scale(0.9)';
                        return;
                    }
                    var el = document.createElement('div');
                    el.className = 'scan-check';
                    el.setAttribute('aria-hidden', 'true');
                    el.innerHTML = '<i class="bi bi-check2-circle"></i>';
                    frame.appendChild(el);
                    scanCheck = el;
                })();

                function easingOut(t) {
                    return 1 - Math.pow(1 - t, 3);
                }

                function pickRandom() {
                    return pool[Math.floor(Math.random() * pool.length)];
                }

                function cancelRaf() {
                    if (rafId) {
                        cancelAnimationFrame(rafId);
                        rafId = 0;
                    }
                }

                function clearAutoRedirect() {
                    if (autoRedirectTimer) {
                        clearTimeout(autoRedirectTimer);
                        autoRedirectTimer = 0;
                    }
                }

                function sleep(ms) {
                    return new Promise(function (resolve) {
                        autoRedirectTimer = setTimeout(resolve, ms);
                    });
                }

                function setDotsText(base, now) {
                    if (!sysTitle) return;
                    // Update dots at ~6fps using rAF time
                    if (!dotsTick) dotsTick = now;
                    if (now - dotsTick < 160) return;
                    var step = Math.floor((now / 420) % 3) + 1; // 1..3
                    var dots = step === 1 ? '.' : (step === 2 ? '..' : '...');
                    sysTitle.textContent = base + dots;
                    dotsTick = now;
                }

                function setSys(text) {
                    if (sysTitle) sysTitle.textContent = text;
                }

                function getWelcomeNodes() {
                    return {
                        hello: document.getElementById('scanHello'),
                        sub: welcome ? welcome.querySelector('.sub') : null
                    };
                }

                // PHASE 0 — FORM EXIT
                async function phase0FormExit() {
                    return new Promise(function (resolve) {
                        var tl = gsap.timeline({ defaults: { ease: 'power3.inOut' }, onComplete: resolve });
                        tl.to('#modernLoginCard', { duration: 0.6, scale: 0.9, opacity: 0, ease: 'power3.inOut' }, 0);
                        // show overlay after form exits
                        tl.add(function () {
                            showOverlay();
                        }, 0.6);
                        tl.to(overlay, { duration: 0.35, opacity: 1, ease: 'power2.out' }, 0.62);
                    });
                }

                // PHASE 1 — SECURE SCANNING MODE
                async function phase1SecureScan() {
                    return new Promise(function (resolve) {
                        // Scan targets
                        var startIntervalMs = 30;
                        var endIntervalMs = 250;
                        var decelMs = 2500;
                        var scanTotalMs = 2500; // 2.5–3s total (scan portion)

                        var scanStart = performance.now();
                        var lastSwap = 0;

                        // UI prep
                        setSys('جاري التحقق...');
                        if (scanCheck) {
                            scanCheck.style.opacity = '0';
                            scanCheck.style.transform = 'scale(0.9)';
                        }

                        // Heavy blur + brightness reduction while scanning
                        avatar.style.filter = 'blur(20px) brightness(0.82)';

                        // subtle sweep visible
                        if (sweep) {
                            gsap.to(sweep, { duration: 0.2, opacity: 1, ease: 'power2.out' });
                            gsap.to(sweep, { duration: 2.5, rotate: 720, ease: 'none' });
                        }

                        // subtle glow pulse while scanning (blue)
                        var scanPulse = null;
                        if (glow) {
                            scanPulse = gsap.to(glow, { opacity: 0.55, scale: 1.03, duration: 0.9, repeat: -1, yoyo: true, ease: 'sine.inOut' });
                        }

                        function tick(now) {
                            var elapsed = now - scanStart;

                            // dots animation
                            setDotsText('جاري التحقق', now);

                            // deceleration curve
                            var t = Math.min(1, elapsed / decelMs);
                            var eased = easingOut(t);
                            var interval = startIntervalMs + eased * (endIntervalMs - startIntervalMs);
                            if (elapsed > decelMs) interval = endIntervalMs;

                            if (!lastSwap) lastSwap = now;
                            if (now - lastSwap >= interval) {
                                avatar.src = pickRandom();
                                lastSwap = now;
                            }

                            if (elapsed < scanTotalMs) {
                                rafId = requestAnimationFrame(tick);
                                return;
                            }

                            // Lock on correct user
                            cancelRaf();
                            if (scanPulse) {
                                scanPulse.kill();
                                scanPulse = null;
                            }

                            avatar.src = userImage;

                            // Smoothly remove blur (0.6s)
                            var lockTl = gsap.timeline({ defaults: { ease: 'power2.out' } });
                            lockTl.to(avatar, { duration: 0.6, filter: 'blur(0px) brightness(1)' }, 0);

                            // Green border + confirmation pulse
                            if (glow) {
                                glow.style.borderColor = 'rgba(25, 135, 84, 0.95)';
                                glow.style.boxShadow = '0 0 0 1px rgba(25, 135, 84, 0.55) inset, 0 0 30px rgba(25, 135, 84, 0.45), 0 0 80px rgba(25, 135, 84, 0.22)';
                                lockTl.to(glow, { duration: 0.25, opacity: 1 }, 0.1);
                            }
                            lockTl.fromTo('.scan-frame', { scale: 1.05 }, { duration: 0.35, scale: 1 }, 0.1);

                            // Replace text + check icon
                            lockTl.add(function () {
                                setSys('تم التحقق');
                            }, 0.25);

                            if (scanCheck) {
                                lockTl.to(scanCheck, { duration: 0.25, opacity: 1, scale: 1, ease: 'back.out(1.6)' }, 0.25);
                            }

                            // Hide sweep
                            if (sweep) {
                                lockTl.to(sweep, { duration: 0.25, opacity: 0 }, 0.2);
                            }

                            // Keep Phase 1 tight: resolve shortly after blur clears
                            lockTl.add(resolve, 0.65);
                        }

                        rafId = requestAnimationFrame(tick);
                    });
                }

                // PHASE 2 — SECURITY STEPS (Dynamic Completion)
                async function phase2SecuritySteps() {
                    return new Promise(function (resolve) {
                        messages.innerHTML = '';

                        var steps = [
                            { doing: 'جاري تحديث البيانات', done: 'تم تحديث البيانات' },
                            { doing: 'جاري تأمين البيانات', done: 'تم تأمين البيانات' },
                            { doing: 'جاري تأمين جلسة دخولك', done: 'تم تأمين جلسة دخولك' }
                        ];

                        function createRow(text) {
                            var li = document.createElement('li');
                            li.style.display = 'flex';
                            li.style.alignItems = 'center';
                            li.style.justifyContent = 'center';
                            li.style.gap = '0.55rem';
                            li.style.opacity = '0';
                            li.style.transform = 'translateY(10px)';
                            var span = document.createElement('span');
                            span.textContent = text;
                            var icon = document.createElement('i');
                            icon.className = 'bi bi-check2';
                            icon.style.color = 'rgba(25, 135, 84, 0.95)';
                            icon.style.opacity = '0';
                            icon.style.transform = 'scale(0.9)';
                            icon.style.filter = 'drop-shadow(0 0 12px rgba(25, 135, 84, 0.28))';
                            li.appendChild(icon);
                            li.appendChild(span);
                            messages.appendChild(li);
                            return { li: li, text: span, icon: icon };
                        }

                        (async function run() {
                            for (var i = 0; i < steps.length; i++) {
                                var row = createRow(steps[i].doing);
                                await new Promise(function (res) {
                                    gsap.to(row.li, { duration: 0.45, opacity: 1, y: 0, ease: 'power2.out', onComplete: res });
                                });
                                await sleep(800);
                                row.text.textContent = steps[i].done;
                                await new Promise(function (res) {
                                    gsap.to(row.icon, { duration: 0.25, opacity: 1, scale: 1, ease: 'back.out(1.6)', onComplete: res });
                                });
                                await sleep(150);
                            }
                            resolve();
                        })();
                    });
                }

                // PHASE 3 — WELCOME STATE
                async function phase3Welcome() {
                    return new Promise(function (resolve) {
                        var nodes = getWelcomeNodes();
                        if (nodes.hello) {
                            nodes.hello.textContent = 'أهلاً بك ' + (userName || '');
                        }
                        if (nodes.sub) {
                            nodes.sub.textContent = 'جاري تحويلك إلى لوحة التحكم...';
                        }

                        welcome.style.visibility = 'visible';
                        welcome.style.filter = 'drop-shadow(0 0 14px rgba(14, 165, 233, 0.22))';

                        var tl = gsap.timeline({ defaults: { ease: 'power2.out' }, onComplete: resolve });
                        tl.to(welcome, { duration: 0.8, opacity: 1 }, 0);
                    });
                }

                // PHASE 4 — EXIT
                async function phase4Redirect() {
                    return new Promise(function (resolve) {
                        var tl = gsap.timeline({ defaults: { ease: 'power2.inOut' } });
                        tl.to('.login-enterprise-overlay .glass', { duration: 0.35, opacity: 0 }, 0);
                        tl.to(overlay, { duration: 0.45, opacity: 0, ease: 'power2.inOut' }, 0.05);
                        tl.add(function () {
                            resolve();
                        }, 0.3);
                    });
                }

                // Run phases sequentially (clear separation)
                try {
                    await phase0FormExit();
                    await phase1SecureScan();
                    await phase2SecuritySteps();
                    await phase3Welcome();
                    await sleep(2000);
                    await phase4Redirect();
                    window.location.href = redirectUrl;
                } catch (e) {
                    // Safety fallback: redirect without reloading
                    window.location.href = redirectUrl;
                }
            }

            function ajaxLoginSubmit(event) {
                var form = getModernForm();
                if (!form) return;
                if (!canUseAjaxLogin()) return;

                event.preventDefault();

                clearAjaxError();
                var formData = new FormData(form);
                freezeModernForm();
                var csrf = document.querySelector('meta[name="csrf-token"]');
                var csrfToken = csrf ? csrf.getAttribute('content') : '';

                fetch(form.getAttribute('action'), {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(function (res) {
                    if (!res.ok) {
                        return res.json().then(function (data) {
                            throw data;
                        });
                    }
                    return res.json();
                })
                .then(function (data) {
                    if (data && data.success) {
                        runEnterpriseSequence(data);
                        return;
                    }
                    unfreezeModernForm();
                    setAjaxError('حدث خطأ غير متوقع. حاول مرة أخرى.');
                })
                .catch(function (err) {
                    var msg = 'تعذر تسجيل الدخول. تحقق من البيانات وحاول مرة أخرى.';
                    if (err) {
                        if (typeof err.message === 'string' && err.message.trim()) {
                            msg = err.message;
                        } else if (err.errors) {
                            var firstKey = Object.keys(err.errors)[0];
                            if (firstKey && err.errors[firstKey] && err.errors[firstKey][0]) {
                                msg = err.errors[firstKey][0];
                            }
                        }
                    }
                    unfreezeModernForm();
                    setAjaxError(msg);
                });
            }

            // Bind AJAX only for modern theme form
            var modernForm = getModernForm();
            if (modernForm) {
                modernForm.addEventListener('submit', ajaxLoginSubmit);
            }
        })();
    </script>
</body>

</html>

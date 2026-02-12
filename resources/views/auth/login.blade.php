<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <!-- Add the viewport meta tag -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

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
            display: flex !important;
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
            overflow: hidden;
        }

        .login-modern-layout {
            align-items: center;
        }

        .login-modern::before,
        .login-modern::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            filter: blur(0.5px);
            opacity: 0.6;
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
            padding: 1.25rem;
        }

        .login-enterprise-overlay.is-visible {
            display: flex;
        }

        .login-enterprise-overlay .glass {
            position: relative;
            width: min(520px, 92vw);
            border-radius: 1.75rem;
            padding: 2rem 1.75rem;
            background: rgba(15, 23, 42, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(16px);
            box-shadow: 0 40px 90px rgba(2, 6, 23, 0.55);
            overflow: hidden;
            color: #e2e8f0;
        }

        .login-enterprise-overlay .glass::before {
            content: "";
            position: absolute;
            inset: -40%;
            background: conic-gradient(from 180deg, rgba(14, 165, 233, 0.32), rgba(245, 158, 11, 0.22), rgba(99, 102, 241, 0.25), rgba(14, 165, 233, 0.32));
            filter: blur(30px);
            opacity: 0.45;
            animation: overlaySpin 3.4s linear infinite;
        }

        @keyframes overlaySpin {
            to {
                transform: rotate(1turn);
            }
        }

        .login-enterprise-overlay .content {
            position: relative;
            z-index: 1;
        }

        .scan-frame {
            width: 176px;
            height: 176px;
            margin: 0 auto;
            border-radius: 999px;
            position: relative;
            display: grid;
            place-items: center;
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
            width: 132px;
            height: 132px;
            border-radius: 999px;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 20px 50px rgba(2, 6, 23, 0.55);
            background: rgba(2, 6, 23, 0.25);
        }

        .scan-status {
            margin-top: 1.5rem;
            text-align: center;
        }

        .scan-status .sys {
            font-weight: 700;
            color: rgba(226, 232, 240, 0.92);
        }

        .scan-messages {
            list-style: none;
            margin: 1rem 0 0;
            padding: 0;
            display: grid;
            gap: 0.4rem;
            text-align: center;
            color: rgba(226, 232, 240, 0.82);
            font-size: 0.98rem;
        }

        .scan-welcome {
            display: none;
            margin-top: 1.25rem;
            text-align: center;
        }

        .scan-welcome .hello {
            font-size: 1.3rem;
            font-weight: 800;
            color: #f8fafc;
        }

        .scan-welcome .sub {
            margin-top: 0.35rem;
            color: rgba(226, 232, 240, 0.78);
        }

        .scan-welcome .go {
            margin-top: 1.15rem;
            width: 100%;
            border: 0;
            border-radius: 1rem;
            padding: 0.85rem 1rem;
            font-weight: 800;
            color: #0b1220;
            background: linear-gradient(90deg, rgba(14, 165, 233, 0.95), rgba(245, 158, 11, 0.95));
            box-shadow: 0 18px 40px rgba(14, 165, 233, 0.22);
        }

        .scan-welcome .go:active {
            transform: translateY(1px);
        }
    </style>
</head>

<body data-login-theme="legacy">

    @if (session('success'))
        <div class="alert alert-success" style="color: #25a421;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="login-theme-toggle">
        <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" id="loginThemeToggle" aria-pressed="false">
            <label class="form-check-label" for="loginThemeToggle">الشكل الحديث</label>
        </div>
    </div>

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

    <section class="login-modern login-modern-layout vh-100">
        <div class="container modern-shell">
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-6">
                    <div class="modern-card h-100">
                        <span class="modern-badge">
                            <i class="bi bi-stars"></i>
                            تجربة محدثة
                        </span>
                        <h1 class="modern-title mt-3">كنترول الانتخابات بشكل مستقبلي وواضح</h1>
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
                <div class="col-12 col-lg-6">
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
                            <button type="button" class="btn modern-secondary mt-3 w-100" id="backToLegacy">
                                الرجوع للشكل القديم
                            </button>
                        </form>
                    </div>
                </div>
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
            var toggleButton = document.getElementById("loginThemeToggle");
            var backToLegacy = document.getElementById("backToLegacy");

            function applyTheme(theme) {
                document.body.setAttribute("data-login-theme", theme);
                if (toggleButton) {
                    toggleButton.checked = theme === "modern";
                    toggleButton.setAttribute("aria-pressed", theme === "modern" ? "true" : "false");
                }
            }

            function getInitialTheme() {
                return sessionStorage.getItem(storageKey) || "legacy";
            }

            function setTheme(theme) {
                sessionStorage.setItem(storageKey, theme);
                applyTheme(theme);
            }

            applyTheme(getInitialTheme());

            if (toggleButton) {
                toggleButton.addEventListener("change", function (event) {
                    setTheme(event.target.checked ? "modern" : "legacy");
                });
            }

            if (backToLegacy) {
                backToLegacy.addEventListener("click", function () {
                    setTheme("legacy");
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

                // Subtle background blur without touching CSS structure
                overlay.style.background = 'rgba(2, 6, 23, 0.18)';
                overlay.style.backdropFilter = 'blur(8px)';
                overlay.style.webkitBackdropFilter = 'blur(8px)';
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

            function runEnterpriseSequence(payload) {
                var overlay = document.getElementById('loginEnterpriseOverlay');
                var avatar = document.getElementById('scanAvatar');
                var sweep = document.getElementById('scanSweep');
                var glow = document.getElementById('scanGlowRing');
                var messages = document.getElementById('scanMessages');
                var welcome = document.getElementById('scanWelcome');
                var goBtn = document.getElementById('goDashboardBtn');
                var redirectUrl = payload && payload.redirect ? payload.redirect : "{{ url('/') }}";
                var userName = payload && payload.user ? payload.user.name : '';
                var userImage = safeUserImage(payload && payload.user ? payload.user.image : null);

                if (!overlay || !avatar || !messages || !welcome || !goBtn) {
                    window.location.href = redirectUrl;
                    return;
                }

                document.body.setAttribute('data-login-ajax-state', 'animating');

                // seed UI
                messages.innerHTML = '';
                // Keep layout stable inside overlay (no shifting when welcome appears)
                welcome.style.display = 'block';
                welcome.style.visibility = 'hidden';
                welcome.style.opacity = '0';

                // Reserve space for messages to avoid layout jumps
                messages.style.minHeight = '5.2rem';
                messages.style.marginTop = '1rem';

                if (glow) glow.style.opacity = '0';
                if (sweep) sweep.style.opacity = '0.9';
                avatar.src = "{{ asset('assets/admin/images/users/user-placeholder.png') }}";

                var pool = getScanImagesPool();
                var rafId = 0;
                var cycleStart = performance.now();
                // Scan/image cycling should feel cinematic and clearly visible
                // Target: 1.5–2.5 seconds
                var scanDuration = 1850;
                var startIntervalMs = 40;
                var endIntervalMs = 250;
                var lastSwap = 0;

                // 2.5s delay after animation completes
                var autoRedirectDelayMs = 2500;
                var autoRedirectTimer = 0;

                // Subtle glow pulse around the scan frame during scanning
                var glowPulseTween = null;

                function easingOut(t) {
                    // Smooth deceleration curve
                    return 1 - Math.pow(1 - t, 3);
                }

                function pickRandom() {
                    return pool[Math.floor(Math.random() * pool.length)];
                }

                function tick(now) {
                    var t = Math.min(1, (now - cycleStart) / scanDuration);
                    var eased = easingOut(t);

                    // interval grows from 40ms to 250ms
                    var interval = startIntervalMs + eased * (endIntervalMs - startIntervalMs);

                    if (!lastSwap) lastSwap = now;
                    if (now - lastSwap >= interval) {
                        avatar.src = pickRandom();
                        // motion blur simulation (lighter over time)
                        avatar.style.filter = t < 0.7 ? 'blur(2px) saturate(1.12)' : 'blur(0px) saturate(1.0)';
                        lastSwap = now;
                    }

                    if (t < 1) {
                        rafId = requestAnimationFrame(tick);
                    } else {
                        avatar.style.filter = 'blur(0px)';
                        avatar.src = userImage;
                    }
                }

                // Prepare messages as fixed nodes (no incremental layout changes)
                var msgTexts = [
                    'جاري تحديث البيانات...',
                    'جاري تأمين البيانات...',
                    'جاري تأمين جلسة دخولك...'
                ];
                var msgEls = [];
                for (var i = 0; i < msgTexts.length; i++) {
                    var li = document.createElement('li');
                    li.textContent = msgTexts[i];
                    li.style.opacity = '0';
                    li.style.transform = 'translateY(10px)';
                    messages.appendChild(li);
                    msgEls.push(li);
                }

                // Build premium cinematic timeline
                var tl = gsap.timeline({ defaults: { ease: 'power3.inOut' } });

                // Zoom-out + blur the form behind (0.6s, cinematic)
                tl.to('.login-modern .modern-shell', { duration: 0.6, scale: 0.94, filter: 'blur(8px)', opacity: 0.92, ease: 'power3.inOut' }, 0);

                // Overlay fade-in (0.5s) + glass slide-in
                tl.set(overlay, { opacity: 0 }, 0);
                tl.to(overlay, {
                    duration: 0.5,
                    opacity: 1,
                    onStart: function () { showOverlay(); }
                }, 0.05);
                tl.fromTo('.login-enterprise-overlay .glass', { y: 14, opacity: 0 }, { duration: 0.55, y: 0, opacity: 1, ease: 'power3.out' }, 0.12);

                // Sweep rotation (kept light, no heavy loops)
                tl.to(sweep, { duration: 0.2, opacity: 1, ease: 'power2.out' }, 0.2);
                tl.to(sweep, { duration: 1.65, rotate: 360, ease: 'none' }, 0.25);

                tl.add(function () {
                    if (glow) {
                        // start subtle glow pulse early
                        glowPulseTween = gsap.to(glow, {
                            opacity: 0.9,
                            scale: 1.05,
                            duration: 0.9,
                            repeat: -1,
                            yoyo: true,
                            ease: 'sine.inOut'
                        });
                    }
                    rafId = requestAnimationFrame(tick);
                }, 0.35);

                // Messages sequential (0.6s fade-in each, small delays)
                tl.to(msgEls[0], { duration: 0.6, opacity: 1, y: 0, ease: 'power2.out' }, 0.75);
                tl.to(msgEls[1], { duration: 0.6, opacity: 1, y: 0, ease: 'power2.out' }, 1.15);
                tl.to(msgEls[2], { duration: 0.6, opacity: 1, y: 0, ease: 'power2.out' }, 1.55);

                // Lock on authenticated user image toward the end of the scan
                tl.add(function () {
                    cancelAnimationFrame(rafId);
                    avatar.src = userImage;
                    setScanHello(userName);
                    if (glowPulseTween) {
                        glowPulseTween.kill();
                        glowPulseTween = null;
                    }
                }, 2.05);

                // Glow pulse after lock-in (premium finish)
                tl.to(glow, { duration: 0.25, opacity: 1, ease: 'power2.out' }, 2.05);
                tl.to(glow, { duration: 0.9, scale: 1.06, yoyo: true, repeat: 1, ease: 'sine.inOut' }, 2.1);
                tl.to(sweep, { duration: 0.35, opacity: 0, ease: 'power2.out' }, 2.1);

                // Welcome fade-in (0.6s)
                tl.add(function () {
                    welcome.style.visibility = 'visible';
                }, 2.2);
                tl.to(welcome, { duration: 0.6, opacity: 1, ease: 'power2.out' }, 2.2);

                function go() {
                    window.location.href = redirectUrl;
                }

                var goOnce = function () {
                    goBtn.removeEventListener('click', goOnce);
                    if (autoRedirectTimer) {
                        clearTimeout(autoRedirectTimer);
                        autoRedirectTimer = 0;
                    }
                    go();
                };

                goBtn.addEventListener('click', goOnce);

                // Redirect after animation completes + 2.5s delay (no page reload)
                tl.add(function () {
                    autoRedirectTimer = setTimeout(go, autoRedirectDelayMs);
                }, 2.85);
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

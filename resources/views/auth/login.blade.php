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
            display: none;
        }

        body[data-login-theme="modern"] .login-legacy {
            display: none;
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

    <section class="login-modern vh-100 d-flex align-items-center">
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
                    <div class="modern-card modern-form">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="modern-title mb-1">تسجيل الدخول</h4>
                                <span class="modern-subtitle">النسخة الجديدة من لوحة التحكم</span>
                            </div>
                            <span class="modern-badge">نسخة جديدة</span>
                        </div>
                        <form method="POST" action="{{ route('login') }}">
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
                                    <input class="form-check-input" type="checkbox" id="rememberMeModern">
                                    <label class="form-check-label" for="rememberMeModern">تذكرني</label>
                                </div>
                                <button type="submit" class="btn modern-primary">تسجيل دخول</button>
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

    <!-- Include Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

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
        })();
    </script>
</body>

</html>

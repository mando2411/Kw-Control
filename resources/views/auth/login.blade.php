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
    </style>
</head>

<body>

    @if (session('success'))
        <div class="alert alert-success" style="color: #25a421;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <section class="pt-5 login vh-100">
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

    <!-- Include Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Include your custom JS files if needed -->
    <script src="{{ asset('assets/admin/js/main.js') }}"></script>
</body>

</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="كنترول منصة عربية متكاملة لإدارة العمليات والنتائج والتقارير بكفاءة ووضوح.">
    <meta name="keywords" content="كنترول, لوحة تحكم, إدارة النتائج, إدارة المستخدمين, تقارير">
    <title>{{ config('app.name', 'كنترول') }}</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            color: #0f172a;
            font-family: Tahoma, Arial, sans-serif;
        }
        .card {
            width: min(92%, 560px);
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 28px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }
        .title {
            margin: 0 0 10px;
            font-size: 30px;
            font-weight: 700;
        }
        .desc {
            margin: 0 0 22px;
            color: #334155;
            line-height: 1.8;
        }
        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 10px;
            text-decoration: none;
            border: 1px solid #d1d5db;
            color: #0f172a;
            background: #fff;
        }
        .btn-primary {
            background: #0f172a;
            color: #fff;
            border-color: #0f172a;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1 class="title">{{ config('app.name', 'كنترول') }}</h1>
        <p class="desc">منصة عربية متكاملة لإدارة البيانات والنتائج والتقارير باحترافية.</p>
        <div class="actions">
            @auth
                <a class="btn btn-primary" href="{{ url('/dashboard') }}">الذهاب إلى لوحة التحكم</a>
            @else
                <a class="btn btn-primary" href="{{ route('login') }}">تسجيل الدخول</a>
                @if (Route::has('register'))
                    <a class="btn" href="{{ route('register') }}">إنشاء حساب</a>
                @endif
            @endauth
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لا توجد انتخابات</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h3 class="mb-3">لا توجد انتخابات مفعّلة</h3>
                <p class="text-muted mb-4">يرجى التواصل مع المسؤول لتفعيل انتخابات مرتبطة بحسابك قبل المتابعة.</p>
                <a href="{{ url('/') }}" class="btn btn-primary">العودة للرئيسية</a>
            </div>
        </div>
    </div>
</body>
</html>

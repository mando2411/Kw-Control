<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب للانضمام كمتعهد</title>
    <link rel="stylesheet" href="{{ asset('assets/site/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/site/css/all.min.css') }}">
</head>
<body style="background:#f0f2f5;">
<div class="container" style="max-width:580px;padding-top:40px;padding-bottom:40px;">
    <div class="card" style="border:1px solid #dddfe2;border-radius:12px;">
        <div class="card-body p-4">
            <h4 class="mb-2" style="font-weight:800;">طلب الانضمام كمتعهد</h4>
            <p class="text-muted mb-4">أنشئ حسابًا جديدًا لإرسال طلب الانضمام إلى المرشح <strong>{{ $candidate->user?->name }}</strong>.</p>

            <form method="POST" action="{{ route('candidates.join.register.store', ['slug' => $slug]) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">الاسم بالكامل</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">رقم التليفون</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">إنشاء الحساب وإرسال الطلب</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

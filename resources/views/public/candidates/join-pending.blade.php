<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حالة طلب الانضمام</title>
    <link rel="stylesheet" href="{{ asset('assets/site/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/site/css/all.min.css') }}">
</head>
<body style="background:#f0f2f5;">
<div class="container" style="max-width:680px;padding-top:40px;padding-bottom:40px;">
    <div class="card" style="border:1px solid #dddfe2;border-radius:12px;">
        <div class="card-body p-4">
            <h4 class="mb-2" style="font-weight:800;">تم إرسال طلب الانضمام</h4>
            <p class="text-muted mb-3">المرشح: <strong>{{ $candidate->user?->name }}</strong></p>

            @php
                $statusMap = [
                    'pending' => ['text' => 'جاري مراجعة طلب انضمامك من قبل المرشح', 'class' => 'warning'],
                    'approved' => ['text' => 'تمت الموافقة على طلب انضمامك', 'class' => 'success'],
                    'rejected' => ['text' => 'تم رفض طلب انضمامك', 'class' => 'danger'],
                ];
                $statusConfig = $statusMap[$joinRequest->status] ?? $statusMap['pending'];
            @endphp

            <div class="alert alert-{{ $statusConfig['class'] }} mb-0">
                {{ $statusConfig['text'] }}
            </div>
        </div>
    </div>
</div>
</body>
</html>

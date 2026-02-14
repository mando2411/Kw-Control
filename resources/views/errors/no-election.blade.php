@extends('layouts.app')

@section('content')
<div class="container text-center py-5 rtl">
    <h2 class="mb-3">غير مسموح بالدخول</h2>
    <p class="text-muted fs-5">
        أنت لست تابعًا لأي حملة انتخابية حاليًا.
    </p>

    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">
        العودة للرئيسية
    </a>
</div>
@endsection

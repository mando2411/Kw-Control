@extends('layouts.dashboard.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h2 class="h4 mb-1">عرض بيانات الاستيراد</h2>
            <div class="text-muted">الانتخابات: {{ $election->name }} ({{ $election->id }})</div>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">العودة للوحة التحكم</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>الاسم</th>
                            <th>الهاتف</th>
                            <th>البطاقة</th>
                            <th>الفرع</th>
                            <th>المرجع</th>
                            <th>الصندوق</th>
                            <th>القطاع</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($voters as $voter)
                            <tr>
                                <td>{{ $voter->name }}</td>
                                <td>{{ $voter->phone1 }}</td>
                                <td>{{ $voter->albtn }}</td>
                                <td>{{ $voter->alfraa }}</td>
                                <td>{{ $voter->almrgaa }}</td>
                                <td>{{ $voter->alsndok }}</td>
                                <td>{{ $voter->alktaa }}</td>
                                <td>{{ $voter->status ? 'حاضر' : 'غير حاضر' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">لا توجد بيانات للعرض.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {{ $voters->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

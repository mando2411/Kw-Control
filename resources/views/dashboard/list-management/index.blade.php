@extends('layouts.dashboard.app')

@section('content')
<div class="page-body" dir="rtl">
    <div class="container-fluid">
        <x-dashboard.partials.message-alert />

        <div class="card border-0 shadow-sm mb-3" style="border-radius:16px; overflow:hidden;">
            <div class="card-body" style="background:linear-gradient(120deg, rgba(59,130,246,.12), rgba(99,102,241,.10), rgba(255,255,255,.98));">
                <h4 class="mb-1" style="font-weight:900;">إدارة القائمة</h4>
                <p class="mb-0 text-muted" style="font-weight:600;">
                    نسخة حديثة مستقلة لإدارة القائمة، مخصصة للتطوير القادم.
                </p>
            </div>
        </div>

        @if(!empty($currentListLeaderCandidate))
            <div class="alert alert-info fw-bold mb-3">
                القائمة: {{ $currentListLeaderCandidate->list_name ?? 'قائمتي' }}
                — السعة: {{ $listMembersCount }} / {{ $listLimit }}
                — المتبقي: {{ $remainingSlots }}
            </div>
        @endif

        <div class="row g-3">
            <div class="col-lg-4 col-md-6">
                <a class="text-decoration-none" href="{{ route('dashboard.candidates.index') }}">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius:14px;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1" style="font-weight:800;">المرشحين</h6>
                                <small class="text-muted">عرض وإدارة أعضاء القائمة</small>
                            </div>
                            <i class="bi bi-people fs-4 text-primary"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-6">
                <a class="text-decoration-none" href="{{ route('dashboard.candidates.create') }}">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius:14px;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1" style="font-weight:800;">إضافة مرشح</h6>
                                <small class="text-muted">إضافة عضو جديد للقائمة</small>
                            </div>
                            <i class="bi bi-person-plus fs-4 text-success"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

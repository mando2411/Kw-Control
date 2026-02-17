@extends('layouts.dashboard.app')

@section('content')

@if(isset($pendingJoinRequest) && $pendingJoinRequest)
@php
    $candidate = $pendingJoinRequest->candidate;
    $joinStatus = (string) ($pendingJoinRequest->status ?? 'pending');
    $candidateName = $candidate?->user?->name ?? 'المرشح';
    $candidateElection = $candidate?->election?->name ?? 'حملة غير محددة';
    $candidateImage = $candidate?->user?->image ?: ('https://ui-avatars.com/api/?name=' . urlencode($candidateName) . '&background=2563eb&color=fff&size=300');
    $candidateSlug = trim(($candidate?->user?->name ?? 'candidate'));
    $candidateSlug = preg_replace('/\s+/u', '-', $candidateSlug);
    $candidateSlug = trim((string) $candidateSlug, '-');
    $candidateSlug = ($candidateSlug !== '' ? $candidateSlug : 'candidate') . '-' . ($candidate?->id ?? 0);

    $statusConfig = [
        'pending' => [
            'class' => 'pending-status-alert--pending',
            'icon' => 'fa-clock-o',
            'text' => 'جاري مراجعة طلب انضمامك من قبل المرشح',
        ],
        'rejected' => [
            'class' => 'pending-status-alert--rejected',
            'icon' => 'fa-times-circle',
            'text' => 'لقد تم رفض طلبك من قبل المرشح',
        ],
    ][$joinStatus] ?? [
        'class' => 'pending-status-alert--pending',
        'icon' => 'fa-clock-o',
        'text' => 'جاري مراجعة طلب انضمامك من قبل المرشح',
    ];
@endphp

<div class="page-body">
    <div class="container-fluid">
        <div class="pending-join-shell">
            <div class="pending-join-card">
                <div class="pending-join-header">
                    <h3 class="mb-1">طلب الانضمام كمتعهد</h3>
                    <p class="mb-0">حالياً لا يمكن عرض أي أقسام أخرى حتى يراجع المرشح طلبك.</p>
                </div>

                <div class="pending-candidate-box">
                    <div class="pending-candidate-avatar" style="background-image:url('{{ $candidateImage }}')"></div>
                    <div class="pending-candidate-content">
                        <h4>{{ $candidateName }}</h4>
                        <div class="pending-candidate-campaign">
                            <i class="fa fa-flag me-1"></i>
                            {{ $candidateElection }}
                        </div>
                        @if(!$candidate)
                            <div class="pending-candidate-note">تعذر تحميل بيانات المرشح الكاملة مؤقتًا.</div>
                        @endif
                        <a href="{{ route('candidates.public-profile', ['slug' => $candidateSlug]) }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm mt-2">
                            عرض ملف المرشح
                        </a>
                    </div>
                </div>

                <div class="pending-status-alert {{ $statusConfig['class'] }}">
                    <i class="fa {{ $statusConfig['icon'] }} me-2"></i>
                    {{ $statusConfig['text'] }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pending-join-shell {
        min-height: calc(100vh - 200px);
        display: grid;
        place-items: center;
        padding: 1rem 0;
    }

    .pending-join-card {
        width: min(100%, 760px);
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 18px 34px rgba(15, 23, 42, .08);
        padding: 1.1rem;
    }

    .pending-join-header h3 {
        font-size: 1.25rem;
        font-weight: 900;
        color: #0f172a;
    }

    .pending-join-header p {
        color: #64748b;
        font-weight: 600;
        font-size: .93rem;
    }

    .pending-candidate-box {
        margin-top: .95rem;
        border: 1px solid #dbe3ef;
        border-radius: 14px;
        background: linear-gradient(120deg, #f8fbff, #ffffff);
        padding: .85rem;
        display: flex;
        align-items: center;
        gap: .8rem;
    }

    .pending-candidate-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background-size: cover;
        background-position: center;
        border: 2px solid #dbeafe;
        box-shadow: 0 8px 18px rgba(59, 130, 246, .18);
        flex: 0 0 auto;
    }

    .pending-candidate-content h4 {
        margin: 0 0 .2rem;
        font-size: 1.05rem;
        font-weight: 900;
        color: #1e293b;
    }

    .pending-candidate-campaign {
        color: #475569;
        font-weight: 700;
        font-size: .86rem;
    }

    .pending-candidate-note {
        margin-top: .35rem;
        color: #b45309;
        font-size: .78rem;
        font-weight: 700;
    }

    .pending-status-alert {
        margin-top: .95rem;
        border-radius: 12px;
        padding: .75rem .85rem;
        font-weight: 800;
        font-size: .92rem;
    }

    .pending-status-alert.pending-status-alert--pending {
        border: 1px solid #facc15;
        background: #fef9c3;
        color: #854d0e;
    }

    .pending-status-alert.pending-status-alert--rejected {
        border: 1px solid #fca5a5;
        background: #fef2f2;
        color: #991b1b;
    }

    @media (max-width: 576px) {
        .pending-candidate-box {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@else

{{-- voter --}}
{{-- <style>
    .button-arrived {
        background-color: green;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }
    .button-not-arrived {
        background-color: red;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }
    .button-disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style> --}}


{{-- canditates --}}
<style>
    .button {
        margin: 5px;
        padding: 10px;
        font-size: 1.2em;
        cursor: pointer;
    }

    .button-increment {
        background-color: green;
        color: white;
    }

    .button-decrement {
        background-color: red;
        color: white;
    }

    .button-update {
        background-color: blue;
        color: white;
        margin-top: 10px;
    }

    .form-container {
        max-width: 500px;
        margin: 50px auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #f9f9f9;
    }

    .custom-file-input {
        position: absolute;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        opacity: 0;
        cursor: pointer;
    }

    .custom-file-label {
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 10px;
        display: inline-block;
        width: 100%;
        text-align: center;
        background-color: #007bff;
        color: #fff;
        cursor: pointer;
    }

    .btn-custom {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    .btn-custom:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .online {
        animation: myAnim 1000ms ease 0s infinite normal both;
    }

    @keyframes myAnim {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        /* Start slightly lower */
        transition: opacity 0.5s ease, transform 0.5s ease;
        /* Transition effect */
    }

    /* Final state (visible) */
    .fade-in.show {
        opacity: 1;
        transform: translateY(0);
        /* Move to its original position */
    }

    .import-card {
        border: 1px solid #e3e6ea;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .import-header {
        border-bottom: 1px solid #eef1f4;
    }

    .import-help {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .import-warning {
        background: #fff4e5;
        border: 1px solid #ffd19a;
        color: #8a4b00;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 0.95rem;
    }

    .import-option {
        border: 1px solid #e3e6ea;
        border-radius: 10px;
        padding: 12px 14px;
        height: 100%;
        background: #f8f9fb;
        display: flex;
        flex-wrap: nowrap;
        flex-direction: row;
        align-items: flex-start;
        gap: 12px;
        width: 100%;
        min-width: 0;
        white-space: normal;
    }

    .import-option input {
        margin: 0;
        align-self: flex-start;
        flex: 0 0 auto;
    }

    .import-option-wrapper {
        width: 100%;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .import-option-input {
        flex: 0 0 auto;
        margin-top: 4px;
    }

    .import-option-content {
        width: 100%;
        min-width: 0;
        flex: 1 1 auto;
        white-space: normal;
        word-break: normal;
        overflow-wrap: break-word;
    }

    .import-option .option-title {
        font-weight: 600;
        font-size: 1rem;
        white-space: normal;
        min-width: 0;
        word-break: keep-all;
        overflow-wrap: break-word;
        direction: rtl;
        text-align: right;
    }

    .import-option .option-desc {
        font-size: 0.9rem;
        color: #6c757d;
        line-height: 1.5;
        word-break: keep-all;
        overflow-wrap: break-word;
        white-space: normal;
        min-width: 0;
    }

    .import-option.option-danger {
        border-color: #f1b0b7;
        border-right: 4px solid #dc3545;
        background: #fff5f5;
    }

    .import-option-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 48px;
    }

    .import-field.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
    }

    .import-field.is-valid {
        border-color: #198754 !important;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.15);
    }

    .import-error {
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 6px;
    }

    .import-progress {
        height: 10px;
        background: #eef1f4;
        border-radius: 999px;
        overflow: hidden;
    }

    .import-progress-bar {
        height: 100%;
        width: 0;
        background: #0d6efd;
        transition: width 0.2s ease;
    }

    .import-form-desktop {
        display: block;
    }

    .import-form-mobile {
        display: none;
    }

    @media (max-width: 767.98px) {
        .import-form-desktop {
            display: none;
        }

        .import-form-mobile {
            display: block;
        }

        .import-form-mobile .import-stack {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .import-form-mobile .import-stack-item {
            width: 100%;
        }

        .import-form-mobile .import-options-stack {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .import-form-mobile .import-option-mobile {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            width: 100%;
            padding: 14px 14px 12px;
            border-radius: 14px;
            background: #fdf7ef;
            border: 1px solid #f2d2a7;
            box-shadow: 0 8px 20px rgba(138, 75, 0, 0.08);
            direction: rtl;
            text-align: right;
            cursor: pointer;
        }

        .import-form-mobile .import-option-mobile.option-danger {
            background: #fff0f0;
            border-color: #f3b1b7;
            border-right: 6px solid #dc3545;
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.12);
        }

        .import-form-mobile .import-option-input {
            flex: 0 0 auto;
            margin-top: 4px;
        }

        .import-form-mobile .import-option-content {
            min-width: 0;
            width: 100%;
        }

        .import-form-mobile .import-option-mobile .option-title,
        .import-form-mobile .import-option-mobile .option-desc {
            display: block;
            word-break: normal;
            overflow-wrap: break-word;
            white-space: normal;
        }

        .import-form-mobile .form-label {
            font-weight: 600;
            color: #2f2f2f;
        }

        .import-form-mobile .form-select,
        .import-form-mobile .form-control {
            border-radius: 12px;
            border-color: #ead7c6;
            background: #fffaf5;
            padding: 12px 14px;
        }

        .import-form-mobile .import-help {
            font-size: 0.85rem;
            color: #8a4b00;
        }

        .import-form-mobile .import-warning {
            background: #fff1dc;
            border-color: #f3c890;
            color: #7a3e00;
        }

        .import-form-mobile .import-submit {
            border-radius: 999px;
            background: #0f6f5c;
            border-color: #0f6f5c;
            padding: 12px 18px;
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        .import-form-mobile .import-submit:hover {
            background: #0c5c4c;
            border-color: #0c5c4c;
        }

        .import-form-mobile .import-progress {
            height: 12px;
            background: #f1e2d6;
        }

        .import-form-mobile .import-progress-bar {
            background: linear-gradient(90deg, #0f6f5c 0%, #2aa08a 100%);
        }
    }

    .import-submit.is-loading {
        opacity: 0.75;
        cursor: not-allowed;
    }

    .import-section {
        direction: rtl;
        text-align: right;
    }

    .import-section .form-label,
    .import-section .import-help,
    .import-section .import-error,
    .import-section .alert,
    .import-section .import-warning {
        text-align: right;
    }

    .import-section .form-control,
    .import-section .form-select,
    .import-option {
        min-height: 48px;
        font-size: 1rem;
    }

    .import-section .btn {
        min-height: 48px;
        font-size: 1rem;
    }

    .import-option input {
        margin-right: 0;
        margin-left: 0;
    }

    .import-option .option-title {
        font-size: 1rem;
    }

    .import-option .option-desc {
        font-size: 0.95rem;
    }

    .import-progress {
        direction: ltr;
    }


    @media (max-width: 576px) {
        .import-header h2 {
            font-size: 1.1rem;
        }

        .import-help {
            font-size: 0.95rem;
        }

        .import-section .form-control,
        .import-section .form-select {
            font-size: 1rem;
        }

        .import-option {
            padding: 16px;
            gap: 12px;
        }
        .option-title {
        font-size: 16px;
        line-height: 1.6;
    }
    .option-desc {
        font-size: 14px;
        line-height: 1.7;
    }
    }

    @media (max-width: 480px) {
        .import-option {
            flex-direction: row;
            align-items: flex-start;
        }

        .import-option input {
            margin-top: 4px;
        }

        .import-option .option-title,
        .import-option .option-desc {
            text-align: right;
        }
    }

    @media (min-width: 768px) {
        .import-section .import-help {
            font-size: 0.95rem;
        }

        .import-section-desktop {
            display: block;
        }

        .import-section-mobile {
            display: none;
        }
    }
</style>

{{-- Dual UI mode (classic/modern) - architecture only, classic layout untouched --}}
<style>
    .home-ui-toggle {
        position: fixed;
        top: 1.25rem;
        left: 1.25rem;
        z-index: 10060;
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.45rem 0.9rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(15, 23, 42, 0.18);
        color: #0f172a;
        font-size: 0.9rem;
        backdrop-filter: blur(6px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
    }

    .home-ui-toggle .form-check-input {
        width: 2.6rem;
        height: 1.4rem;
        cursor: pointer;
    }

    .home-ui-toggle .form-check-input:checked {
        background-color: #0ea5e9;
        border-color: #0ea5e9;
    }

    .home-ui-toggle .form-check-label {
        cursor: pointer;
        font-weight: 600;
    }

    #homepage-classic,
    #homepage-modern {
        transition: opacity 220ms ease;
        will-change: opacity;
    }

    /* Default: keep modern hidden unless JS enables it */
    #homepage-modern {
        display: none;
        opacity: 0;
    }

    body.ui-modern #homepage-classic,
    html.ui-modern body #homepage-classic {
        display: none;
    }

    body.ui-modern #homepage-modern,
    html.ui-modern body #homepage-modern {
        display: block;
        opacity: 1;
    }

    body.ui-modern #homepage-classic,
    html.ui-modern body #homepage-classic {
        opacity: 0;
    }

    body.ui-mode-switching {
        cursor: progress;
    }
</style>

{{-- ui_mode is bootstrapped early in the dashboard layout to prevent flicker --}}

<div id="homepage-classic">
<div class="projectContainer mx-auto">
    <!-- banner -->
    @if (auth()->user()->candidate()->exists())

    <div class="pt-5 banner rtl">
        <div class="container-fluid">
            <div class="d-flex border-bottom justify-content-center align-items-center">
                <div class="">
                    <figure class="mx-auto text-center rounded-circle overflow-hidden" style="@auth
                          width: 150px;
      height: 150px;
      margin-left: 25px !important;
                  @endauth"><img src="{{auth()->user()->image}}" class="w-100 h-100" alt=""></figure>
                </div>
                <div class="text-center me-3">
                    <h1>{{auth()->user()->name}}</h1>
                    <p class="fs-5"> {{"مرشح". "  ".  auth()->user()->election?->name }} </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <section class="countDown py-3">
        <div class="container">

            <div class="text-center madameenControl mx-auto">
                <h2 class="textMainColor time-election">
                    باقى على موعد الانتخابات
                </h2>

                <div class="row g-3 align-items-center mt-3 mb-1">
                    <div class="col-3">
                        <div class="inner border border-2 p-2 rounded-3 rounded">
                            <div class="textMainColor text-center fw-bold">
                                <span class="fs-2" id="seconds"></span> <br />
                                ثانيه
                            </div>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="inner border border-2 p-2 rounded-3 rounded">
                            <div class="textMainColor text-center fw-bold">
                                <span class="fs-2" id="minutes"></span> <br />
                                دقيقه
                            </div>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="inner border border-2 p-2 rounded-3 rounded">
                            <div class="textMainColor text-center fw-bold">
                                <span class="fs-2" id="hours"></span> <br />
                                ساعه
                            </div>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="inner border border-2 p-2 rounded-3 rounded">
                            <div class="textMainColor text-center fw-bold">
                                <span class="fs-2" id="days"></span> <br />
                                يوم
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="startDate" value="{{ \Carbon\Carbon::parse(auth()->user()->election?->start_date)->format('Y-m-d') }}">
                <input type="hidden" id="startTime" value="{{ \Carbon\Carbon::parse(auth()->user()->election?->start_time)->format('H:i:s') }}">
                <input type="hidden" id="endDate" value="{{ \Carbon\Carbon::parse(auth()->user()->election?->end_date)->format('Y-m-d') }}">
                <input type="hidden" id="endTime" value="{{ \Carbon\Carbon::parse(auth()->user()->election?->end_time)->format('H:i:s') }}">
                <div id="election_start">
                    <p class="text-danger">
                        حتى تاريخ {{ \Carbon\Carbon::parse(auth()->user()->election?->start_date)->format('d/m/Y') }} , الساعة
                        {{ \Carbon\Carbon::parse(auth()->user()->election?->start_time)->format('h:i') }}
                        {{ \Carbon\Carbon::parse(auth()->user()->election?->start_time)->format('A') === 'AM' ? 'ص' : 'م' }}
                    </p>
                </div>
                <div id="election_end" class="d-none">
                    <p class="text-danger">
                        حتى تاريخ {{ \Carbon\Carbon::parse(auth()->user()->election?->end_date)->format('d/m/Y') }} , الساعة
                        {{ \Carbon\Carbon::parse(auth()->user()->election?->end_time)->format('h:i') }}
                        {{ \Carbon\Carbon::parse(auth()->user()->election?->end_time)->format('A') === 'AM' ? 'ص' : 'م' }}
                    </p>
                </div>



            </div>

        </div>
    </section>
    <!-- main -->
    <main>

        @if(auth()->user()->can('statement.show') ||
        auth()->user()->can('madameen') ||
        auth()->user()->can('contractors.list') ||
        auth()->user()->can('statement') ||
        auth()->user()->can('statement.search'))
        <div>
            <h1 class="bg-dark text-white py-2 text-center h2 mb-2">المتعهدين والمضامين</h1>
            <div class="container">
                <div class="row g-3 mb-4">
                    @can('statement.show')
                    <div class="col-lg-2 col-md-3 col-sm-6 flex-grow-1">
                        <div class="w-100">
                            <a href="{{route('dashboard.statement.show')}}">
                                <button class="btn w-100 btn-dark">
                                    <i class="fa fs-5 fa-table-cells d-block my-1"></i>
                                    <h6>العرض الشامل</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can('madameen')
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.madameen')}}">
                                <button class="btn w-100 btn-info">
                                    <i class="fa fs-5 fa-list-check d-block my-1"></i>
                                    <h6>المضامين</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can('contractors.list')
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.contractors.index')}}">
                                <button class="btn w-100 btn-primary">
                                    <i class="fa fs-5 fa-user-check d-block my-1"></i>
                                    <h6>المتعهدين</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can('statement')
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.statement')}}">
                                <button class="btn w-100 btn-warning">
                                    <i class="fa fs-5 fa-clipboard-list d-block my-1"></i>
                                    <h6>كشوف</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can('statement.search')

                    <div class="col-lg-3 col-md-3 col-sm-6 flex-grow-1">
                        <div class="w-100">
                            <a href="{{route('dashboard.statement.search')}}">
                                <button class="btn w-100 btn-secondary">
                                    <i class="fa fs-5 fa-magnifying-glass d-block my-1"></i>
                                    <h6>البحث عن الكشوف</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
        @endif

        <div>
            <h1 class="bg-dark text-white py-2 text-center h2 mb-2">تحضير وفرز (لجان الانتخابات)</h1>
            <div class="container">
                <div class="row g-3 mb-4">
                    @can('candidates.list')

                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.rep-home')}}">
                                <button class="btn w-100 btn-success">
                                    <i class="fa fs-5 fa-user-shield d-block my-1"></i>
                                    <h6>المندوبين</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can('committee.home')

                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.committee.home')}}">
                                <button class="btn w-100 btn-danger">
                                    <i class="fa fs-5 fa-receipt d-block my-1"></i>
                                    <h6>اللجان</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can ('sorting')
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.sorting')}}">
                                <button class="btn w-100 btn-info">
                                    <i class="fa fs-5 fa-rectangle-list d-block my-1"></i>
                                    <h6>الفرز</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can('attending')

                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.attending')}}">
                                <button class="btn w-100 btn-primary">
                                    <i class="fa fs-5 fa-clipboard-check d-block my-1"></i>
                                    <h6>التحضير</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan

                </div>
                
                <div class="row g-3 mb-4">
                    <?php if(isset($show_all_result) && $show_all_result==true){?>
                        <div class="col-lg-4 col-md-3 col-sm-6">
                            <div class="w-100">
                                <a href="{{route('all.results')}}">
                                    <button class="btn w-100 btn-secondary">
                                        <i class="fa fs-5 fa-chart-simple d-block my-1"></i>
                                        <h6>النتائج العامة</h6>
                                    </button>
                                </a>
                            </div>
                        </div>
                    <?php }?>
                    @can ('candidates.index')
                    <div class="col-lg-4 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.results')}}">
                                <button class="btn w-100 btn-dark">
                                    <i class="fa fs-5 fa-chart-simple d-block my-1"></i>
                                    <h6>النتائج</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan

                    @can('statistics')
                    <div class="col-lg-4 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.statistics')}}">
                                <button class="btn w-100 btn-warning">
                                    <i class="fa fs-5 fa-list-check d-block my-1"></i>
                                    <h6>احصائيات</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan

                    @can('reports.create')
                    <div class="col-lg-4 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.reports.create')}}">
                                <button class="btn w-100 btn-primary">
                                    <i class="fa fs-5 fa-receipt d-block my-1"></i>
                                    <h6>التقارير</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
        @if ( auth()->user()->hasRole('Administrator'))
        <div>
            <h1 class="bg-dark text-white py-2 text-center h2 mb-2">أدوات الموقع</h1>
            <div class="container">
                <div class="row g-3 mb-4 align-items-center justify-content-center">
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.delete')}}">
                                <button class="btn w-100 btn-outline-danger">
                                    <i class="fa fs-5 fa-receipt d-block my-1"></i>
                                    <h6>المحذوفين</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.history')}}">
                                <button class="btn w-100 btn-secondary">
                                    <i class="fa fs-5 fa-list-check d-block my-1"></i>
                                    <h6>سجل الموقع</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="">
                                <button class="btn w-100 btn-success">
                                    <i class="fa-brands fs-5 fa-whatsapp d-block my-1"></i>
                                    <h6>whatsapp</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.cards',$data=null)}}">
                                <button class="btn w-100 btn-dark">
                                    <i class="fa fs-5 fa-gear d-block my-1"></i>
                                    <h6>الكوادر واعضاء اللجان</h6>
                                </button>
                            </a>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div>
            <h1 class="bg-dark text-white py-2 text-center h2 mb-2">المتواجدين الأن</h1>

            <div class="table-responsive mb-5">
                <table class="table table-striped w-100  text-center rtl overflow-scroll">
                    <thead class="table-primary  border-0 border-dark border-bottom border-2">
                        <tr class="py-4 ">
                            <th>الاسم</th>
                            <th>أخر ظهور</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider" id="user-list">
                        @php
                        $users=App\Models\User::where('creator_id', auth()->user()->id)->take(10)->get();
                        @endphp
                        @foreach ( $users as $user )
                        @if ($user->isOnline() || $user->isOffline())
                        <tr>
                            <td>
                                <i class="fa fa-circle rounded-circle @if ($user->isOnline())
                                            online text-success
                                            @else
                                            text-danger
                                        @endif ms-1"></i>
                                {{$user->name}}
                            </td>
                            <td>
                                @if ($user->isOffline())
                                {{$user->LoginTime($user->last_active_at)}}
                                @endif
                            </td>
                        </tr>

                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <button id="load-more" data-page="1" class="btn btn-primary">المزيد</button>
            </div>
        </div>
        @endif

        @can('import-voters')
            <div data-voters-import-slot="classic"></div>
        @endcan
    </main>
</div>
</div>

<div id="homepage-modern">
    @include('dashboard.home.modern')
</div>

@can('import-voters')
    {{-- Single shared component rendered once, moved between classic/modern slots by JS --}}
    <div data-voters-import-shared style="display:none;">
        @include('dashboard.partials.voters-import')
    </div>
@endcan

@endif

@endsection
@push('js')
<script>
    (function () {
        var STORAGE_KEY = 'ui_mode';
        var PENDING_KEY = 'ui_mode_pending';
        var MODERN_HREF = "{{ asset('assets/css/home-modern.css') . '?v=' . filemtime(public_path('assets/css/home-modern.css')) }}";
        var PERSIST_URL = "{{ route('ui-mode.update') }}";

        function getEffectiveModeFromDom() {
            if (document.body.classList.contains('ui-modern')) return 'modern';
            if (document.documentElement.classList.contains('ui-modern')) return 'modern';
            return 'classic';
        }

        function applyBodyMode(mode) {
            document.body.classList.remove('ui-modern', 'ui-classic');
            document.body.classList.add(mode === 'modern' ? 'ui-modern' : 'ui-classic');
            document.body.setAttribute('data-ui-mode', mode);

            document.documentElement.classList.remove('ui-modern', 'ui-classic');
            document.documentElement.classList.add(mode === 'modern' ? 'ui-modern' : 'ui-classic');
            document.documentElement.setAttribute('data-ui-mode', mode);
        }

        function setModeValue(mode) {
            try {
                localStorage.setItem(STORAGE_KEY, mode);
            } catch (e) {
                // ignore
            }
            // Do NOT force apply here; applyBodyMode is called when safe (after CSS load)
        }

        function ensureModernCss(shouldLoad) {
            var id = 'homeModernCss';
            var link = document.getElementById(id);
            if (shouldLoad) {
                return new Promise(function (resolve) {
                    if (link && link.sheet) {
                        resolve(true);
                        return;
                    }

                    var isNew = false;
                    if (!link) {
                        isNew = true;
                        link = document.createElement('link');
                        link.id = id;
                        link.rel = 'stylesheet';
                        link.href = MODERN_HREF;
                        document.head.appendChild(link);
                    }

                    var done = false;
                    function finish() {
                        if (done) return;
                        done = true;
                        resolve(!!(link && link.sheet));
                    }

                    link.addEventListener('load', finish, { once: true });
                    link.addEventListener('error', finish, { once: true });

                    // If link existed but is already loading/loaded, resolve quickly
                    if (!isNew) {
                        setTimeout(finish, 120);
                    }
                });
            }

            if (link && link.parentNode) {
                link.parentNode.removeChild(link);
            }

            return Promise.resolve(true);
        }

        function persistModeToServer(mode) {
            var policy = window.__UI_MODE_POLICY__ || 'user_choice';
            if (policy === 'modern' || policy === 'classic') {
                return;
            }

            if (!(window.__UI_MODE_IS_AUTH__ === true || window.__UI_MODE_IS_AUTH__ === 'true')) {
                return;
            }

            var csrf = document.querySelector('meta[name="csrf-token"]');
            var token = csrf ? csrf.getAttribute('content') : '';
            fetch(PERSIST_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                },
                credentials: 'same-origin',
                body: JSON.stringify({ mode: mode })
            }).catch(function () {
                // ignore
            });
        }

        function raf(cb) {
            return (window.requestAnimationFrame || function (fn) { return setTimeout(fn, 0); })(cb);
        }

        function mountVotersImport(mode) {
            var sharedHost = document.querySelector('[data-voters-import-shared]');
            var component = document.querySelector('[data-voters-import-root]');
            if (!component && sharedHost) {
                component = sharedHost.querySelector('[data-voters-import-root]');
            }
            if (!component) return;

            // Apply modern skin only when mounted into modern UI
            component.classList.toggle('hm-import-modern', mode === 'modern');
            component.classList.toggle('hm-import-classic', mode !== 'modern');

            // Re-trigger modern mount animation every time it moves into modern
            component.classList.remove('hm-import-mounted');
            if (mode === 'modern') {
                requestAnimationFrame(function () {
                    component.classList.add('hm-import-mounted');
                });
            }

            var targetSlot = document.querySelector('[data-voters-import-slot="' + mode + '"]');
            if (!targetSlot) {
                targetSlot = document.querySelector('[data-voters-import-slot="classic"]');
            }
            if (!targetSlot) return;

            // Ensure visible when mounted
            component.style.display = '';
            targetSlot.appendChild(component);
        }

        async function switchMode(nextMode, animate) {
            var toggle = document.getElementById('sidebarUiModeToggle') || document.getElementById('homeUiModeToggle');
            var classic = document.getElementById('homepage-classic');
            var modern = document.getElementById('homepage-modern');
            if (!classic || !modern) return;

            var current = getEffectiveModeFromDom();
            if (nextMode === current) {
                if (toggle) {
                    toggle.checked = nextMode === 'modern';
                    toggle.setAttribute('aria-pressed', toggle.checked ? 'true' : 'false');
                }
                return;
            }

            if (toggle) {
                toggle.checked = nextMode === 'modern';
                toggle.setAttribute('aria-pressed', toggle.checked ? 'true' : 'false');
            }

            document.body.classList.add('ui-mode-switching');
            setModeValue(nextMode);

            if (!animate) {
                if (nextMode === 'modern') {
                    await ensureModernCss(true);
                    applyBodyMode('modern');
                    mountVotersImport('modern');
                    classic.style.display = 'none';
                    modern.style.display = 'block';
                } else {
                    applyBodyMode('classic');
                    mountVotersImport('classic');
                    modern.style.display = 'none';
                    classic.style.display = 'block';
                    ensureModernCss(false);
                }
                document.body.classList.remove('ui-mode-switching');
                persistModeToServer(nextMode);
                return;
            }

            var durationMs = 240;

            if (nextMode === 'modern') {
                // Keep classic visible until modern CSS is ready (prevents white flash)
                classic.style.display = 'block';
                classic.style.opacity = '1';
                modern.style.display = 'none';

                var loaded = await ensureModernCss(true);
                if (!loaded) {
                    // If modern CSS fails, keep classic (never blank)
                    setModeValue('classic');
                    applyBodyMode('classic');
                    document.body.classList.remove('ui-mode-switching');
                    return;
                }

                // Apply mode only after CSS is loaded
                applyBodyMode('modern');

                // Move shared import component into modern slot before showing it
                mountVotersImport('modern');

                // Make modern visible BEFORE fading classic out (avoid blank state)
                modern.style.display = 'block';
                classic.style.display = 'block';
                modern.style.opacity = '0';
                classic.style.opacity = '1';

                raf(function () {
                    modern.style.opacity = '1';
                    classic.style.opacity = '0';
                });

                setTimeout(function () {
                    classic.style.display = 'none';
                    classic.style.opacity = '';
                    modern.style.opacity = '';
                    document.body.classList.remove('ui-mode-switching');
                    persistModeToServer('modern');
                }, durationMs);
                return;
            }

            // switching to classic
            applyBodyMode('classic');
            mountVotersImport('classic');
            modern.style.display = 'block';
            classic.style.display = 'block';
            classic.style.opacity = '0';
            modern.style.opacity = '1';

            raf(function () {
                classic.style.opacity = '1';
                modern.style.opacity = '0';
            });

            setTimeout(function () {
                modern.style.display = 'none';
                modern.style.opacity = '';
                classic.style.opacity = '';
                ensureModernCss(false);
                document.body.classList.remove('ui-mode-switching');
                persistModeToServer('classic');
            }, durationMs);
        }

        document.addEventListener('DOMContentLoaded', function () {
            var toggle = document.getElementById('sidebarUiModeToggle') || document.getElementById('homeUiModeToggle');
            var initial = window.__UI_MODE_EFFECTIVE__ || getEffectiveModeFromDom();

            // Ensure DOM matches stored state (no animation on load)
            switchMode(initial, false);

            // Ensure shared component is mounted on initial load
            mountVotersImport(initial);

            if (!toggle) return;
            toggle.checked = initial === 'modern';
            toggle.setAttribute('aria-pressed', toggle.checked ? 'true' : 'false');

            toggle.addEventListener('change', function (e) {
                // Mark pending so other pages can sync instantly if navigation happens
                try {
                    localStorage.setItem(PENDING_KEY, '1');
                } catch (err) {
                    // ignore
                }
                switchMode(e.target.checked ? 'modern' : 'classic', true);
            });
        });
    })();
</script>

<script>
    // Modern homepage animations + countdown (lightweight)
    (function () {
        function isModernActive() {
            return document.body.classList.contains('ui-modern') || document.documentElement.classList.contains('ui-modern');
        }

        function pad2(n) {
            n = Number(n) || 0;
            return n < 10 ? ('0' + n) : String(n);
        }

        function parseDateTime(dateId, timeId) {
            var d = document.getElementById(dateId);
            var t = document.getElementById(timeId);
            if (!d || !t || !d.value || !t.value) return null;
            // Interpret as local time
            return new Date(d.value + 'T' + t.value);
        }

        function startModernCountdown() {
            var root = document.getElementById('hmCountdown');
            if (!root) return;

            var startAt = parseDateTime('hmStartDate', 'hmStartTime');
            var endAt = parseDateTime('hmEndDate', 'hmEndTime');
            if (!startAt || !endAt) return;

            var daysEl = document.getElementById('hmDays');
            var hoursEl = document.getElementById('hmHours');
            var minutesEl = document.getElementById('hmMinutes');
            var secondsEl = document.getElementById('hmSeconds');
            var hintEl = document.getElementById('hmCountdownHint');

            function setAll(d, h, m, s) {
                if (daysEl) daysEl.textContent = String(Math.max(0, d));
                if (hoursEl) hoursEl.textContent = pad2(h);
                if (minutesEl) minutesEl.textContent = pad2(m);
                if (secondsEl) secondsEl.textContent = pad2(s);
            }

            function tick() {
                if (!isModernActive()) return;
                var now = new Date();

                var target = now < startAt ? startAt : endAt;
                var label = now < startAt ? 'باقي على موعد الانتخابات' : 'باقي على نهاية الانتخابات';
                if (hintEl) hintEl.textContent = label;

                var diff = target.getTime() - now.getTime();
                if (diff <= 0) {
                    setAll(0, 0, 0, 0);
                } else {
                    var totalSeconds = Math.floor(diff / 1000);
                    var days = Math.floor(totalSeconds / 86400);
                    var rem = totalSeconds % 86400;
                    var hours = Math.floor(rem / 3600);
                    rem = rem % 3600;
                    var minutes = Math.floor(rem / 60);
                    var seconds = rem % 60;
                    setAll(days, hours, minutes, seconds);
                }

                var next = 1000 - (Date.now() % 1000);
                setTimeout(tick, next);
            }

            tick();
        }

        document.addEventListener('DOMContentLoaded', function () {
            var root = document.getElementById('homeModernRoot');
            if (!root) return;
            if (!isModernActive()) return;

            // Trigger CSS animations smoothly
            requestAnimationFrame(function () {
                root.classList.add('hm-ready');
            });

            startModernCountdown();
        });
    })();
</script>
<script>
    $('#load-more').on('click', function() {
        var page = $(this).data('page');
        page++;

        $.ajax({
            url: '/get-users',
            method: 'GET',
            data: {
                page: page
            },
            success: function(response) {
                if (response.data.length > 0) {
                    response.data.forEach(function(user) {
                        if (user.is_online || user.is_offline) {
                            var row = `
                        <tr>
                            <td>
                                <i class="fa fa-circle rounded-circle ${user.is_online ? 'online text-success' : 'text-danger'} ms-1"></i>
                                ${user.name}
                            </td>
                            <td>
                                ${user.is_offline ? user.last_active_at : ''}
                            </td>
                        </tr>
                    `;

                            $('#user-list').append(row);

                            setTimeout(function() {
                                $('#user-list tr:last-child').css({
                                    'opacity': 1,
                                    'transform': 'translateY(0)',
                                    'transition': 'all 0.5s ease'
                                });
                            }, 100);
                        }
                    });

                    $('#load-more').data('page', page);
                } else {
                    $('#load-more').hide();
                }
            }
        });
    });

    setInterval(function() {
        $.ajax({
            url: '/users/online',
            method: 'GET',
            data: {
                page: $('#load-more').data('page')
            },
            success: function(response) {
                console.log(response);

                $('#user-list tr').css({
                    'opacity': 0,
                    'transform': 'translateY(20px)',
                    'transition': 'all 0.5s ease'
                });
                $('#user-list').html('');


                response.data.forEach(function(user) {
                    if (user.is_online || user.is_offline) {
                        var row = `
                        <tr>
                            <td>
                                <i class="fa fa-circle rounded-circle ${user.is_online ? 'online text-success' : 'text-danger'} ms-1"></i>
                                ${user.name}
                            </td>
                            <td>
                                ${user.is_offline ? user.last_active_at : ''}
                            </td>
                        </tr>
                    `;

                        $('#user-list').append(row);
                        $('#load-more').data('page', 1);

                        setTimeout(function() {
                            $('#user-list tr:last-child').css({
                                'opacity': 1,
                                'transform': 'translateY(0)',
                                'transition': 'all 0.5s ease'
                            });
                        }, 100);
                    }
                });
            }
        });
    }, 120000);
</script>


@endpush
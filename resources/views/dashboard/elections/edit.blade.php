@extends('layouts.dashboard.app')

@section('content')
    <style>
        .election-create-shell {
            animation: electionPageFadeIn 320ms ease-out;
            direction: rtl;
            text-align: right;
        }

        .election-create-intro-card,
        .election-create-form-card {
            border: 0;
            border-radius: 0.9rem;
            transition: transform 220ms ease, box-shadow 220ms ease;
        }

        .election-create-intro-card:hover,
        .election-create-form-card:hover {
            transform: translateY(-2px);
        }

        .election-create-intro-card {
            animation: electionCardSlideUp 360ms ease-out;
        }

        .election-create-form-card {
            animation: electionCardSlideUp 420ms ease-out;
        }

        .election-create-form-card .card-body {
            padding: 1.35rem;
        }

        .election-form-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 0.8rem;
        }

        .election-form-grid .field-col {
            grid-column: span 12;
        }

        @media (min-width: 992px) {
            .election-form-grid .field-col.col-lg-6 {
                grid-column: span 6;
            }

            .election-form-grid .field-col.col-lg-12 {
                grid-column: span 12;
            }
        }

        @keyframes electionPageFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes electionCardSlideUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <form action="{{ route('dashboard.elections.update' , $election) }}" method="POST" class="page-body" dir="rtl">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="تعديل الانتخابات" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.elections.index') }}">الانتخابات</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row election-create-shell">
                <x-dashboard.partials.message-alert />

                <div class="col-12">
                    <div class="card election-create-intro-card shadow-sm mb-3">
                        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <h6 class="mb-1">تعديل الانتخابات</h6>
                                <p class="text-muted mb-0">حدّث بيانات الانتخابات المطلوبة ثم احفظ التعديلات لتطبيقها فورًا.</p>
                            </div>
                            <a href="{{ route('dashboard.elections.index') }}" class="btn btn-light border">
                                <i class="fa fa-arrow-right ms-1"></i>
                                العودة للقائمة
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card election-create-form-card shadow-sm">
                        <div class="card-body">
                            <h6 class="mb-3">بيانات الانتخابات</h6>

                            <div class="election-form-grid">
                                <div class="field-col col-lg-12">
                                    <x-dashboard.form.input-text error-key="name" name="name" :value="$election->name" id="name" label-title="اسم الانتخابات"/>
                                </div>

                                <div class="field-col col-lg-6">
                                    <x-dashboard.form.input-text date error-key="start_date" name="start_date" :value="old('start_date', optional($election->start_date)->format('Y-m-d'))" id="start_date" label-title="تاريخ البداية"/>
                                </div>

                                <div class="field-col col-lg-6">
                                    <x-dashboard.form.input-text date error-key="end_date" name="end_date" :value="old('end_date', optional($election->end_date)->format('Y-m-d'))" id="end_date" label-title="تاريخ النهاية"/>
                                </div>

                                <div class="field-col col-lg-6">
                                    <x-dashboard.form.input-text time error-key="start_time" name="start_time" :value="old('start_time', $election->start_time ? \Carbon\Carbon::parse($election->start_time)->format('H:i') : '')" id="start_time" label-title="وقت البداية"/>
                                </div>

                                <div class="field-col col-lg-6">
                                    <x-dashboard.form.input-text time error-key="end_time" name="end_time" :value="old('end_time', $election->end_time ? \Carbon\Carbon::parse($election->end_time)->format('H:i') : '')" id="end_time" label-title="وقت النهاية"/>
                                </div>

                                <div class="field-col col-lg-12">
                                    <x-dashboard.form.input-text error-key="type" name="type" :value="$election->type" id="type" label-title="نوع الانتخابات"/>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end align-items-center mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check ms-1"></i>
                                    حفظ التعديلات
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection

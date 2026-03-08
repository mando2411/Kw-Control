@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.schools.update', $school) }}" method="POST" class="page-body schools-edit-page" dir="rtl" style="text-align: right;">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="تعديل المدرسة" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.schools.index') }}">المدارس</a>
            </li>
            <li class="breadcrumb-item active">تعديل البيانات</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />

                <div class="col-12">
                    <div class="card schools-edit-shell">
                        <div class="card-body schools-edit-header">
                            <h4 class="schools-edit-title mb-1">بيانات المدرسة</h4>
                            <p class="schools-edit-subtitle mb-0">يمكنك تعديل الاسم والنوع والحملة الانتخابية المرتبطة بالمدرسة.</p>
                        </div>

                        <div class="card-body schools-edit-body">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label for="name" class="form-label">اسم المدرسة <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name', $school->name) }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="مثال: مدرسة النهضة"
                                        required
                                    >
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label for="type" class="form-label">النوع <span class="text-danger">*</span></label>
                                    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="ذكور" @selected(old('type', $school->type) === 'ذكور')>ذكور</option>
                                        <option value="اناث" @selected(old('type', $school->type) === 'اناث')>إناث</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(($hasSchoolElectionColumn ?? true) && $relations['elections']->isNotEmpty())
                                    <div class="col-lg-6">
                                        <label for="election_id" class="form-label">الحملة الانتخابية <span class="text-danger">*</span></label>
                                        <select id="election_id" name="election_id" class="form-select @error('election_id') is-invalid @enderror" required>
                                            <option value="">اختر الحملة الانتخابية</option>
                                            @foreach($relations['elections'] as $election)
                                                <option value="{{ $election->id }}" @selected((string) old('election_id', $school->election_id) === (string) $election->id)>
                                                    {{ $election->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('election_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif

                                <div class="col-lg-6">
                                    <label class="form-label">عدد اللجان المرتبطة</label>
                                    <div class="school-committees-badge">
                                        {{ $school->committees()->count() }} لجنة
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-4">
                                <button type="submit" class="btn btn-primary schools-save-btn">حفظ التعديلات</button>
                                <a href="{{ route('dashboard.schools.index') }}" class="btn btn-outline-secondary schools-cancel-btn">رجوع</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection

@push('css')
    <style>
        .schools-edit-shell {
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 48px rgba(12, 36, 64, 0.12);
            background: linear-gradient(160deg, #f8fbff 0%, #ffffff 100%);
        }

        .schools-edit-header {
            padding: 1.25rem;
            background: radial-gradient(circle at top right, rgba(40, 120, 210, 0.18), transparent 54%), #ffffff;
            border-bottom: 1px solid #e2eaf7;
        }

        .schools-edit-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #153e68;
        }

        .schools-edit-subtitle {
            color: #536f8b;
            font-size: 0.95rem;
        }

        .schools-edit-body {
            padding: 1.25rem;
            background: #ffffff;
        }

        .schools-edit-page .form-label {
            font-weight: 600;
            color: #21496d;
            margin-bottom: 0.45rem;
        }

        .schools-edit-page .form-control,
        .schools-edit-page .form-select {
            min-height: 44px;
            border-radius: 12px;
            border-color: #cfddf1;
            background-color: #fbfdff;
        }

        .schools-edit-page .form-control:focus,
        .schools-edit-page .form-select:focus {
            border-color: #2d7fd8;
            box-shadow: 0 0 0 0.2rem rgba(45, 127, 216, 0.16);
            background-color: #ffffff;
        }

        .school-committees-badge {
            min-height: 44px;
            border: 1px dashed #b8cce7;
            border-radius: 12px;
            padding: 0.6rem 0.8rem;
            display: flex;
            align-items: center;
            background: #f4f8ff;
            color: #1d4f83;
            font-weight: 600;
        }

        .schools-save-btn,
        .schools-cancel-btn {
            border-radius: 12px;
            padding: 0.55rem 1.1rem;
            font-weight: 600;
        }

        @media (max-width: 991px) {
            .schools-edit-header,
            .schools-edit-body {
                padding: 1rem;
            }
        }
    </style>
@endpush

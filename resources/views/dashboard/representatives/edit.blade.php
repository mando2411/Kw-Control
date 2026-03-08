@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.representatives.update', $representative) }}" method="POST" class="page-body representative-edit-page" dir="rtl" style="text-align: right;">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="تعديل المندوب" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.representatives.index') }}">المندوبون</a>
            </li>
            <li class="breadcrumb-item active">تعديل البيانات</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />

                <div class="col-12">
                    <div class="card representative-edit-shell">
                        <div class="card-body representative-edit-header">
                            <h4 class="representative-edit-title mb-1">بيانات المندوب</h4>
                            <p class="representative-edit-subtitle mb-0">تحديث معلومات الحساب وربطه بالحملة الانتخابية واللجنة المناسبة.</p>
                        </div>

                        <div class="card-body representative-edit-body">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label for="name" class="form-label">اسم المندوب <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name', $representative->user?->name) }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        required
                                    >
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        id="phone"
                                        name="phone"
                                        value="{{ old('phone', $representative->user?->phone) }}"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        required
                                    >
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="{{ old('email', $representative->user?->email) }}"
                                        class="form-control @error('email') is-invalid @enderror"
                                    >
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label for="password" class="form-label">كلمة مرور جديدة (اختياري)</label>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                    >
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">الصورة الشخصية (اختياري)</label>
                                    <x-dashboard.form.media title="تحديث صورة المندوب" :images="$representative->user?->image" name="image" />
                                    @error('image')
                                        <span class="text-danger d-block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label for="election_id" class="form-label">الحملة الانتخابية</label>
                                    <select id="election_id" name="election_id" class="form-select @error('election_id') is-invalid @enderror">
                                        <option value="">اختر الحملة الانتخابية</option>
                                        @foreach($relations['elections'] as $election)
                                            <option value="{{ $election->id }}" @selected((string) old('election_id', $representative->election_id) === (string) $election->id)>
                                                {{ $election->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('election_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label for="committee_id" class="form-label">اللجنة</label>
                                    <select id="committee_id" name="committee_id" class="form-select @error('committee_id') is-invalid @enderror">
                                        <option value="">بدون لجنة</option>
                                        @foreach($relations['committees'] as $committee)
                                            <option
                                                value="{{ $committee->id }}"
                                                data-election-id="{{ $committee->election_id ?? '' }}"
                                                @selected((string) old('committee_id', $representative->committee_id) === (string) $committee->id)
                                            >
                                                {{ $committee->name }} ({{ $committee->type }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('committee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <input type="hidden" name="user" value="{{ $representative->user?->id }}">
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-4">
                                <button type="submit" class="btn btn-primary representative-save-btn">حفظ التعديلات</button>
                                <a href="{{ route('dashboard.representatives.index') }}" class="btn btn-outline-secondary representative-cancel-btn">رجوع</a>
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
        .representative-edit-shell {
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 48px rgba(12, 36, 64, 0.12);
            background: linear-gradient(160deg, #f8fbff 0%, #ffffff 100%);
        }

        .representative-edit-header {
            padding: 1.25rem;
            background: radial-gradient(circle at top right, rgba(40, 120, 210, 0.18), transparent 54%), #ffffff;
            border-bottom: 1px solid #e2eaf7;
        }

        .representative-edit-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #153e68;
        }

        .representative-edit-subtitle {
            color: #536f8b;
            font-size: 0.95rem;
        }

        .representative-edit-body {
            padding: 1.25rem;
            background: #ffffff;
        }

        .representative-edit-page .form-label {
            font-weight: 600;
            color: #21496d;
            margin-bottom: 0.45rem;
        }

        .representative-edit-page .form-control,
        .representative-edit-page .form-select {
            min-height: 44px;
            border-radius: 12px;
            border-color: #cfddf1;
            background-color: #fbfdff;
        }

        .representative-edit-page .form-control:focus,
        .representative-edit-page .form-select:focus {
            border-color: #2d7fd8;
            box-shadow: 0 0 0 0.2rem rgba(45, 127, 216, 0.16);
            background-color: #ffffff;
        }

        .representative-save-btn,
        .representative-cancel-btn {
            border-radius: 12px;
            padding: 0.55rem 1.1rem;
            font-weight: 600;
        }

        @media (max-width: 991px) {
            .representative-edit-header,
            .representative-edit-body {
                padding: 1rem;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var electionSelect = document.getElementById('election_id');
            var committeeSelect = document.getElementById('committee_id');
            if (!electionSelect || !committeeSelect) {
                return;
            }

            var allCommitteeOptions = Array.from(committeeSelect.options).map(function (option) {
                return {
                    value: option.value,
                    label: option.text,
                    electionId: option.dataset.electionId || '',
                    selected: option.selected,
                };
            });

            function filterCommitteesByElection() {
                var selectedElection = electionSelect.value;
                var previousValue = committeeSelect.value;

                committeeSelect.innerHTML = '';

                allCommitteeOptions.forEach(function (item, index) {
                    if (index === 0) {
                        committeeSelect.add(new Option(item.label, item.value, false, false));
                        return;
                    }

                    if (!selectedElection || !item.electionId || item.electionId === selectedElection) {
                        var option = new Option(item.label, item.value, false, item.value === previousValue);
                        option.dataset.electionId = item.electionId;
                        committeeSelect.add(option);
                    }
                });

                if (committeeSelect.value !== previousValue) {
                    committeeSelect.value = '';
                }
            }

            electionSelect.addEventListener('change', filterCommitteesByElection);
            filterCommitteesByElection();
        });
    </script>
@endpush

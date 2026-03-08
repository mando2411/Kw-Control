@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.committees.store') }}" method="POST" class="page-body committees-create-page" dir="rtl" style="text-align: right;">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="إضافة لجنة جديدة" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.committees.index') }}">اللجان</a>
            </li>
            <li class="breadcrumb-item active">إنشاء لجنة</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />

                <div class="col-12">
                    <div class="card committees-create-shell">
                        <div class="card-body committees-create-header">
                            <h4 class="committees-create-title mb-1">بيانات اللجنة</h4>
                            <p class="committees-create-subtitle mb-0">أدخل اسم اللجنة وحدد الحملة الانتخابية ثم اختر المدرسة التابعة.</p>
                        </div>

                        <div class="card-body committees-create-body">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label for="name" class="form-label">اسم اللجنة <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name') }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="مثال: لجنة مدرسة النهضة - 01"
                                        required
                                    >
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label for="election_id" class="form-label">الحملة الانتخابية <span class="text-danger">*</span></label>
                                    <select id="election_id" name="election_id" class="form-select @error('election_id') is-invalid @enderror" required>
                                        <option value="">اختر الحملة الانتخابية</option>
                                        @foreach($relations['elections'] as $election)
                                            <option value="{{ $election->id }}" @selected((string) old('election_id') === (string) $election->id)>
                                                {{ $election->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('election_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-8">
                                    <label for="school_id" class="form-label">المدرسة التابعة للجنة <span class="text-danger">*</span></label>
                                    <select id="school_id" name="school_id" class="form-select @error('school_id') is-invalid @enderror" required>
                                        <option value="">اختر المدرسة</option>
                                        @foreach($relations['schools'] as $school)
                                            <option
                                                value="{{ $school->id }}"
                                                data-election-id="{{ $school->election_id ?? '' }}"
                                                data-type="{{ $school->type }}"
                                                @selected((string) old('school_id') === (string) $school->id)
                                            >
                                                {{ $school->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('school_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-1">سيتم عرض المدارس المتوافقة مع الحملة المختارة فقط.</small>
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label">نوع اللجنة</label>
                                    <div id="committeeTypeBadge" class="committee-type-badge">غير محدد بعد</div>
                                    <small class="text-muted d-block mt-1">يتم تحديد النوع تلقائيًا من المدرسة المختارة.</small>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-4">
                                <button type="submit" class="btn btn-primary committees-submit-btn">حفظ اللجنة</button>
                                <a href="{{ route('dashboard.committees.index') }}" class="btn btn-outline-secondary committees-cancel-btn">رجوع</a>
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
        .committees-create-shell {
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 48px rgba(12, 36, 64, 0.12);
            background: linear-gradient(160deg, #f8fbff 0%, #ffffff 100%);
        }

        .committees-create-header {
            padding: 1.25rem;
            background: radial-gradient(circle at top right, rgba(40, 120, 210, 0.18), transparent 54%), #ffffff;
            border-bottom: 1px solid #e2eaf7;
        }

        .committees-create-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #153e68;
        }

        .committees-create-subtitle {
            color: #536f8b;
            font-size: 0.95rem;
        }

        .committees-create-body {
            padding: 1.25rem;
            background: #ffffff;
        }

        .committees-create-page .form-label {
            font-weight: 600;
            color: #21496d;
            margin-bottom: 0.45rem;
        }

        .committees-create-page .form-control,
        .committees-create-page .form-select {
            min-height: 44px;
            border-radius: 12px;
            border-color: #cfddf1;
            background-color: #fbfdff;
        }

        .committees-create-page .form-control:focus,
        .committees-create-page .form-select:focus {
            border-color: #2d7fd8;
            box-shadow: 0 0 0 0.2rem rgba(45, 127, 216, 0.16);
            background-color: #ffffff;
        }

        .committee-type-badge {
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

        .committees-submit-btn,
        .committees-cancel-btn {
            border-radius: 12px;
            padding: 0.55rem 1.1rem;
            font-weight: 600;
        }

        @media (max-width: 991px) {
            .committees-create-header,
            .committees-create-body {
                padding: 1rem;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var electionSelect = document.getElementById('election_id');
            var schoolSelect = document.getElementById('school_id');
            var typeBadge = document.getElementById('committeeTypeBadge');

            if (!electionSelect || !schoolSelect || !typeBadge) {
                return;
            }

            var allSchoolOptions = Array.from(schoolSelect.options).map(function (option) {
                return {
                    value: option.value,
                    label: option.text,
                    electionId: option.dataset.electionId || '',
                    type: option.dataset.type || '',
                    selected: option.selected,
                };
            });

            function normalizeType(typeValue) {
                var normalized = (typeValue || '').trim().toLowerCase();
                if (normalized === 'male' || normalized === 'males' || normalized === 'men' || normalized === 'ذكر' || normalized === 'ذكور') {
                    return 'ذكور';
                }
                if (normalized === 'female' || normalized === 'females' || normalized === 'women' || normalized === 'اناث' || normalized === 'إناث' || normalized === 'انثى' || normalized === 'أنثى') {
                    return 'إناث';
                }
                return typeValue || 'غير محدد بعد';
            }

            function updateTypeBadge() {
                var selectedOption = schoolSelect.options[schoolSelect.selectedIndex];
                var rawType = selectedOption ? selectedOption.dataset.type : '';
                typeBadge.textContent = selectedOption && selectedOption.value ? normalizeType(rawType) : 'غير محدد بعد';
            }

            function filterSchools() {
                var selectedElection = electionSelect.value;
                var previousValue = schoolSelect.value;

                schoolSelect.innerHTML = '';

                allSchoolOptions.forEach(function (item, index) {
                    if (index === 0) {
                        var placeholder = new Option(item.label, item.value, false, false);
                        schoolSelect.add(placeholder);
                        return;
                    }

                    var belongsToElection = !item.electionId || item.electionId === selectedElection;
                    if (!selectedElection || !belongsToElection) {
                        return;
                    }

                    var opt = new Option(item.label, item.value, false, item.value === previousValue);
                    opt.dataset.electionId = item.electionId;
                    opt.dataset.type = item.type;
                    schoolSelect.add(opt);
                });

                if (schoolSelect.value !== previousValue) {
                    schoolSelect.value = '';
                }

                updateTypeBadge();
            }

            electionSelect.addEventListener('change', filterSchools);
            schoolSelect.addEventListener('change', updateTypeBadge);

            filterSchools();
        });
    </script>
@endpush

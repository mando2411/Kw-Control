@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.committees.update' , $committee) }}" method="POST" class="page-body" dir="rtl" style="text-align: right;">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="تعديل اللجنة" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.committees.index') }}">اللجان</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />


                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-text error-key="name" name="name" :value="$committee->name" id="name" label-title="اسم اللجنة"/>

                        <x-dashboard.form.input-select name="election_id"  :options="$relations['elections']" track-by="id"
                            option-lable="name" label-title="الحملة الانتخابية"
                            :value="old('election_id', $committee->election_id)"
                            id="election_id"
                            error-key="election_id"  />

                        <div class="mb-3">
                            <label for="school_id" class="form-label">المدرسة التابعة للجنة</label>
                            <select id="school_id" name="school_id" class="form-control @error('school_id') is-invalid @enderror">
                                <option value="">اختر المدرسة</option>
                                @foreach($relations['schools'] as $school)
                                    <option
                                        value="{{ $school->id }}"
                                        data-election-id="{{ $school->election_id ?? '' }}"
                                        data-type="{{ $school->type }}"
                                        @selected((string) old('school_id', $committee->school_id) === (string) $school->id)
                                    >
                                        {{ $school->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">نوع اللجنة</label>
                            <input type="text" id="committee_type_preview" class="form-control" value="{{ $committee->type }}" readonly>
                            <small class="text-muted">يتم تحديد النوع تلقائيًا حسب المدرسة المختارة.</small>
                        </div>

                        <x-dashboard.form.submit-button/>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var electionSelect = document.getElementById('election_id');
            var schoolSelect = document.getElementById('school_id');
            var typePreview = document.getElementById('committee_type_preview');

            if (!electionSelect || !schoolSelect || !typePreview) {
                return;
            }

            var allOptions = Array.from(schoolSelect.options).map(function (option) {
                return {
                    value: option.value,
                    label: option.text,
                    electionId: option.dataset.electionId || '',
                    type: option.dataset.type || '',
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
                return typeValue || '-';
            }

            function refreshTypePreview() {
                var selected = schoolSelect.options[schoolSelect.selectedIndex];
                if (!selected || !selected.value) {
                    typePreview.value = '-';
                    return;
                }

                typePreview.value = normalizeType(selected.dataset.type || '');
            }

            function filterSchoolsByElection() {
                var electionValue = electionSelect.value;
                var selectedSchool = schoolSelect.value;

                schoolSelect.innerHTML = '';
                allOptions.forEach(function (item, index) {
                    if (index === 0) {
                        schoolSelect.add(new Option(item.label, item.value, false, false));
                        return;
                    }

                    var belongs = !item.electionId || item.electionId === electionValue;
                    if (!electionValue || !belongs) {
                        return;
                    }

                    var option = new Option(item.label, item.value, false, item.value === selectedSchool);
                    option.dataset.electionId = item.electionId;
                    option.dataset.type = item.type;
                    schoolSelect.add(option);
                });

                if (schoolSelect.value !== selectedSchool) {
                    schoolSelect.value = '';
                }

                refreshTypePreview();
            }

            electionSelect.addEventListener('change', filterSchoolsByElection);
            schoolSelect.addEventListener('change', refreshTypePreview);

            filterSchoolsByElection();
        });
    </script>
@endpush

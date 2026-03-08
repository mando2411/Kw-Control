@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.committees.multi') }}" method="POST" class="page-body committees-multi-page" dir="rtl" style="text-align: right;">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="توليد لجان متعددة" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.committees.index') }}">اللجان</a>
            </li>
            <li class="breadcrumb-item active">توليد متعدد</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />

                <div class="col-12">
                    <div class="card committees-multi-shell">
                        <div class="card-body committees-multi-header">
                            <h4 class="committees-multi-title mb-1">إنشاء لجان تلقائيًا</h4>
                            <p class="committees-multi-subtitle mb-0">حدد عدد لجان الذكور والإناث واختر الحملة الانتخابية، وسيتم إنشاء اللجان تلقائيًا.</p>
                        </div>

                        <div class="card-body committees-multi-body">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label for="men" class="form-label">عدد لجان الذكور <span class="text-danger">*</span></label>
                                    <input
                                        type="number"
                                        min="1"
                                        id="men"
                                        name="men"
                                        value="{{ old('men', 1) }}"
                                        class="form-control @error('men') is-invalid @enderror"
                                        required
                                    >
                                    @error('men')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-4">
                                    <label for="women" class="form-label">عدد لجان الإناث <span class="text-danger">*</span></label>
                                    <input
                                        type="number"
                                        min="1"
                                        id="women"
                                        name="women"
                                        value="{{ old('women', 1) }}"
                                        class="form-control @error('women') is-invalid @enderror"
                                        required
                                    >
                                    @error('women')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-4">
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
                            </div>

                            <div class="committees-multi-summary mt-3" id="multiSummaryBox">
                                إجمالي اللجان المتوقع إنشاؤها: <strong id="multiTotal">0</strong>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-4">
                                <button type="submit" class="btn btn-primary committees-multi-submit">إنشاء اللجان</button>
                                <a href="{{ route('dashboard.committees.index') }}" class="btn btn-outline-secondary committees-multi-cancel">رجوع</a>
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
        .committees-multi-shell {
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 48px rgba(12, 36, 64, 0.12);
            background: linear-gradient(165deg, #f8fbff 0%, #ffffff 100%);
        }

        .committees-multi-header {
            padding: 1.25rem;
            background: radial-gradient(circle at top right, rgba(40, 120, 210, 0.18), transparent 55%), #ffffff;
            border-bottom: 1px solid #e2eaf7;
        }

        .committees-multi-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #153e68;
        }

        .committees-multi-subtitle {
            color: #536f8b;
            font-size: 0.95rem;
        }

        .committees-multi-body {
            padding: 1.25rem;
            background: #ffffff;
        }

        .committees-multi-page .form-label {
            font-weight: 600;
            color: #21496d;
            margin-bottom: 0.45rem;
        }

        .committees-multi-page .form-control,
        .committees-multi-page .form-select {
            min-height: 44px;
            border-radius: 12px;
            border-color: #cfddf1;
            background-color: #fbfdff;
        }

        .committees-multi-page .form-control:focus,
        .committees-multi-page .form-select:focus {
            border-color: #2d7fd8;
            box-shadow: 0 0 0 0.2rem rgba(45, 127, 216, 0.16);
            background-color: #ffffff;
        }

        .committees-multi-summary {
            border: 1px dashed #b8cce7;
            border-radius: 12px;
            padding: 0.75rem 0.9rem;
            background: #f4f8ff;
            color: #1d4f83;
            font-weight: 500;
        }

        .committees-multi-submit,
        .committees-multi-cancel {
            border-radius: 12px;
            padding: 0.55rem 1.1rem;
            font-weight: 600;
        }

        @media (max-width: 991px) {
            .committees-multi-header,
            .committees-multi-body {
                padding: 1rem;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var menInput = document.getElementById('men');
            var womenInput = document.getElementById('women');
            var totalElement = document.getElementById('multiTotal');

            if (!menInput || !womenInput || !totalElement) {
                return;
            }

            var updateTotal = function () {
                var men = parseInt(menInput.value || '0', 10);
                var women = parseInt(womenInput.value || '0', 10);
                men = Number.isFinite(men) && men > 0 ? men : 0;
                women = Number.isFinite(women) && women > 0 ? women : 0;
                totalElement.textContent = (men + women).toString();
            };

            menInput.addEventListener('input', updateTotal);
            womenInput.addEventListener('input', updateTotal);
            updateTotal();
        });
    </script>
@endpush

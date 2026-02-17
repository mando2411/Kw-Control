@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.candidates.store' ) }}" method="POST" class="page-body" dir="rtl">
        @csrf

        <x-dashboard.partials.breadcrumb title="إنشاء مرشح" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.candidates.index') }}">المرشحون</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid candidate-create-modern">
            <div class="row g-3">
                <x-dashboard.partials.message-alert />

                <div class="col-12">
                    <div class="candidate-hero-card">
                        <div>
                            <h3 class="candidate-hero-card__title">إنشاء مرشح جديد</h3>
                            <p class="candidate-hero-card__desc mb-0">
                                يتم تعيين الدور تلقائيًا إلى <strong>مرشح</strong> عند الحفظ، لذلك لا حاجة لاختيار الدور يدويًا.
                            </p>
                        </div>
                        <div class="candidate-hero-card__badge">Role: مرشح</div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card candidate-card h-100">
                        <div class="card-body">
                            <h5 class="candidate-card__title">بيانات الحساب الشخصية</h5>
                            <p class="candidate-card__hint">هذه الحقول تنشئ حساب المستخدم المرتبط بالمرشح.</p>

                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">الاسم الكامل <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                <small class="text-muted d-block mt-1">الغرض: اسم المرشح الذي يظهر في القوائم وشاشات الفرز.</small>
                                @error('name')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label fw-bold">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                                <small class="text-muted d-block mt-1">الغرض: قناة التواصل الأساسية لحساب المرشح.</small>
                                @error('phone')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">البريد الإلكتروني (اختياري)</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                                <small class="text-muted d-block mt-1">الغرض: البريد الرسمي للحساب. عند تركه فارغًا ينشئ النظام بريدًا تلقائيًا.</small>
                                @error('email')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">كلمة المرور (اختياري)</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="text-muted d-block mt-1">الغرض: كلمة المرور الأولية. عند تركها فارغة يتم اعتماد كلمة افتراضية.</small>
                                @error('password')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold">الصورة الشخصية (اختياري)</label>
                                <x-dashboard.form.media title="إضافة صورة شخصية" :images="old('image')" name="image" />
                                <small class="text-muted d-block mt-2">الغرض: صورة ملف حساب المرشح.</small>
                                @error('image')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card candidate-card h-100">
                        <div class="card-body">
                            <h5 class="candidate-card__title">البيانات الانتخابية والتنظيمية</h5>
                            <p class="candidate-card__hint">تحدد ارتباط المرشح بالانتخابات وقدرة فريقه التشغيلي.</p>

                            <div class="mb-3">
                                <label for="election_id" class="form-label fw-bold">الانتخابات <span class="text-danger">*</span></label>
                                <select class="form-control" id="election_id" name="election_id" required>
                                    <option value="" disabled selected>اختر الانتخابات</option>
                                    @foreach($relations['elections'] as $election)
                                        <option value="{{ $election->id }}" @selected((string)old('election_id') === (string)$election->id)>
                                            {{ $election->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">الغرض: ربط المرشح بالانتخابات المستهدفة.</small>
                                @error('election_id')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="max_contractor" class="form-label fw-bold">الحد الأقصى للمتعهدين <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" id="max_contractor" name="max_contractor" value="{{ old('max_contractor') }}" required>
                                <small class="text-muted d-block mt-1">الغرض: العدد المستهدف/الأقصى للمتعهدين العاملين مع المرشح.</small>
                                @error('max_contractor')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="max_represent" class="form-label fw-bold">الحد الأقصى للمناديب <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" id="max_represent" name="max_represent" value="{{ old('max_represent') }}" required>
                                <small class="text-muted d-block mt-1">الغرض: العدد المستهدف/الأقصى للمناديب ضمن فريق المرشح.</small>
                                @error('max_represent')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold">بانر المرشح (اختياري)</label>
                                <x-dashboard.form.media title="إضافة بانر المرشح" :images="old('banner')" name="banner" />
                                <small class="text-muted d-block mt-2">الغرض: صورة دعائية تظهر في واجهات العرض.</small>
                                @error('banner')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="candidate-submit-wrap">
                        <button type="submit" class="btn btn-primary candidate-submit-btn">
                            <i class="fa fa-check-circle me-1"></i>
                            حفظ المرشح
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('css')
<style>
    .candidate-create-modern {
        animation: candidateFadeIn .45s ease;
    }

    .candidate-hero-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.2rem;
        border-radius: 14px;
        border: 1px solid rgba(99, 102, 241, 0.2);
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.08), rgba(99, 102, 241, 0.08));
    }

    .candidate-hero-card__title {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
    }

    .candidate-hero-card__desc {
        font-size: .93rem;
        color: #4b5563;
    }

    .candidate-hero-card__badge {
        white-space: nowrap;
        background: #111827;
        color: #fff;
        font-size: .8rem;
        font-weight: 700;
        padding: .35rem .7rem;
        border-radius: 999px;
    }

    .candidate-card {
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.25);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        transition: transform .22s ease, box-shadow .22s ease;
    }

    .candidate-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.10);
    }

    .candidate-card__title {
        margin-bottom: .35rem;
        font-size: 1.02rem;
        font-weight: 800;
    }

    .candidate-card__hint {
        color: #6b7280;
        font-size: .86rem;
        margin-bottom: 1rem;
    }

    .candidate-submit-wrap {
        display: flex;
        justify-content: center;
        padding-top: .25rem;
        padding-bottom: .4rem;
    }

    .candidate-submit-btn {
        min-width: 210px;
        min-height: 44px;
        border-radius: 10px;
        font-weight: 800;
        font-size: .94rem;
        transition: all .2s ease;
    }

    .candidate-submit-btn:hover {
        transform: translateY(-1px);
    }

    @keyframes candidateFadeIn {
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
@endpush

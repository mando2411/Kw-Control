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

                            <div class="mb-3">
                                <label for="candidate_type" class="form-label fw-bold">نوع المرشح <span class="text-danger">*</span></label>
                                <select class="form-control" id="candidate_type" name="candidate_type" required>
                                    <option value="candidate" @selected(old('candidate_type', 'candidate') === 'candidate')>مرشح</option>
                                    <option value="list_leader" @selected(old('candidate_type') === 'list_leader')>مرشح رئيس قائمة</option>
                                </select>
                                <small class="text-muted d-block mt-1">حدد إذا كان المرشح فردي عادي أم رئيس قائمة.</small>
                                @error('candidate_type')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="candidate-list-extra" id="candidate_list_extra_fields" @if(old('candidate_type', 'candidate') !== 'list_leader') style="display: none;" @endif>
                                <div class="candidate-list-extra__inner">
                                    <div class="mb-3">
                                        <label for="list_candidates_count" class="form-label fw-bold">عدد مرشحي القائمة</label>
                                        <input type="number" min="1" class="form-control" id="list_candidates_count" name="list_candidates_count" value="{{ old('list_candidates_count') }}">
                                        <small class="text-muted d-block mt-1">عدد أعضاء القائمة الانتخابية المرتبطين بهذه القائمة.</small>
                                        @error('list_candidates_count')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="list_name" class="form-label fw-bold">اسم القائمة</label>
                                        <input type="text" class="form-control" id="list_name" name="list_name" value="{{ old('list_name') }}" placeholder="اكتب اسم القائمة">
                                        <small class="text-muted d-block mt-1">الاسم الرسمي/الإعلامي للقائمة.</small>
                                        @error('list_name')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">لوجو القائمة</label>
                                        <x-dashboard.form.media title="إضافة لوجو القائمة" :images="old('list_logo')" name="list_logo" />
                                        <small class="text-muted d-block mt-2">صورة شعار القائمة الانتخابية.</small>
                                        @error('list_logo')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="is_actual_list_candidate" class="form-label fw-bold">حالة المرشح داخل القائمة</label>
                                        <select class="form-control" id="is_actual_list_candidate" name="is_actual_list_candidate">
                                            <option value="1" @selected((string) old('is_actual_list_candidate', '1') === '1')>مرشح فعلي داخل القائمة</option>
                                            <option value="0" @selected((string) old('is_actual_list_candidate') === '0')>تنظيم وإدارة فقط</option>
                                        </select>
                                        <small class="text-muted d-block mt-1">حدد هل هذا الحساب يمثل مرشحًا فعليًا ضمن أعضاء القائمة أم حساب إدارة وتنظيم فقط.</small>
                                        @error('is_actual_list_candidate')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                                    </div>
                                </div>
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

    .candidate-list-extra {
        margin-bottom: 1rem;
        border: 1px dashed rgba(99, 102, 241, 0.35);
        border-radius: 12px;
        background: linear-gradient(180deg, rgba(99, 102, 241, 0.06), rgba(14, 165, 233, 0.04));
        overflow: hidden;
    }

    .candidate-list-extra__inner {
        padding: .85rem .85rem .25rem;
        animation: candidateListFieldsIn .28s ease;
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

    @keyframes candidateListFieldsIn {
        from {
            opacity: 0;
            transform: translateY(4px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@push('js')
<script>
    (function () {
        const typeSelect = document.getElementById('candidate_type');
        const listFieldsWrap = document.getElementById('candidate_list_extra_fields');

        if (!typeSelect || !listFieldsWrap) {
            return;
        }

        const controlledIds = [
            'list_candidates_count',
            'list_name',
            'is_actual_list_candidate'
        ];

        function setListFieldsState() {
            const isListLeader = typeSelect.value === 'list_leader';

            listFieldsWrap.style.display = isListLeader ? '' : 'none';

            controlledIds.forEach(function (id) {
                const el = document.getElementById(id);
                if (!el) return;

                if (isListLeader) {
                    el.removeAttribute('disabled');
                } else {
                    el.setAttribute('disabled', 'disabled');
                }
            });
        }

        typeSelect.addEventListener('change', setListFieldsState);
        setListFieldsState();
    })();
</script>
@endpush

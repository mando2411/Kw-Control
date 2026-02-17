@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.candidates.update' , $candidate) }}" method="POST" class="page-body" dir="rtl">
        @csrf
        @method('PUT')
        <input type="hidden" name="user" value="{{ $candidate->user->id }}">

        <x-dashboard.partials.breadcrumb title="تعديل ملف المرشح" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.candidates.index') }}">المرشحون</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid candidate-edit-modern">
            <x-dashboard.partials.message-alert />

            <section class="candidate-profile-hero mb-4">
                <div class="candidate-cover"
                     style="background-image:url('{{ $candidate->banner ?: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1600&q=80' }}')">
                    <div class="candidate-cover__overlay"></div>
                    <div class="candidate-cover__actions">
                        <x-dashboard.form.media title="تغيير صورة الكوفر" :images="$candidate->banner" name="banner" />
                    </div>
                </div>

                <div class="candidate-profile-bar">
                    <div class="candidate-avatar-wrap">
                        <div class="candidate-avatar"
                             style="background-image:url('{{ $candidate->user->image ?: 'https://ui-avatars.com/api/?name='.urlencode($candidate->user->name ?? 'Candidate').'&background=0ea5e9&color=fff&size=256' }}')">
                        </div>
                        <div class="candidate-avatar-edit">
                            <x-dashboard.form.media title="تغيير الصورة الشخصية" :images="$candidate->user->image" name="image" />
                        </div>
                    </div>

                    <div class="candidate-profile-meta">
                        <h2 class="mb-1">{{ $candidate->user->name }}</h2>
                        <div class="candidate-profile-meta__chips">
                            <span class="chip">مرشح</span>
                            <span class="chip">{{ $candidate->election?->name ?? 'انتخابات غير محددة' }}</span>
                            <span class="chip">#{{ $candidate->id }}</span>
                        </div>
                        <p class="mb-0 mt-2 text-muted">قم بتعديل البيانات الشخصية والانتخابية ثم احفظ التغييرات.</p>
                    </div>
                </div>
            </section>

            <div class="row g-3">
                <div class="col-12 col-xl-6">
                    <div class="card candidate-edit-card h-100">
                        <div class="card-body">
                            <h5 class="candidate-edit-card__title">البيانات الشخصية</h5>
                            <p class="candidate-edit-card__hint">هذه البيانات تخص حساب المستخدم المرتبط بالمرشح.</p>

                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">الاسم الكامل <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $candidate->user->name) }}" required>
                                @error('name')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label fw-bold">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $candidate->user->phone) }}" required>
                                @error('phone')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $candidate->user->email) }}">
                                @error('email')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-0">
                                <label for="password" class="form-label fw-bold">كلمة مرور جديدة (اختياري)</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="text-muted d-block mt-1">اترك الحقل فارغًا إذا لا تريد تغيير كلمة المرور الحالية.</small>
                                @error('password')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card candidate-edit-card h-100">
                        <div class="card-body">
                            <h5 class="candidate-edit-card__title">إعدادات الحملة الانتخابية</h5>
                            <p class="candidate-edit-card__hint">حدد الانتخابات وحدود فريق العمل للمرشح.</p>

                            <div class="mb-3">
                                <label for="election_id" class="form-label fw-bold">الانتخابات</label>
                                <select class="form-control" id="election_id" name="election_id">
                                    <option value="" disabled>اختر الانتخابات</option>
                                    @foreach($relations['elections'] as $election)
                                        <option value="{{ $election->id }}" @selected((string)old('election_id', $candidate->election_id) === (string)$election->id)>
                                            {{ $election->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('election_id')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="max_contractor" class="form-label fw-bold">الحد الأقصى للمتعهدين <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" id="max_contractor" name="max_contractor" value="{{ old('max_contractor', $candidate->max_contractor) }}" required>
                                @error('max_contractor')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-0">
                                <label for="max_represent" class="form-label fw-bold">الحد الأقصى للمناديب <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" id="max_represent" name="max_represent" value="{{ old('max_represent', $candidate->max_represent) }}" required>
                                @error('max_represent')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="candidate-edit-submit-wrap">
                        <button type="submit" class="btn btn-primary candidate-edit-submit-btn">
                            <i class="fa fa-save me-1"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('css')
<style>
    .candidate-edit-modern {
        animation: candidateProfileFade .45s ease;
    }

    .candidate-profile-hero {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, .22);
        box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
        background: #fff;
    }

    .candidate-cover {
        position: relative;
        min-height: 240px;
        background-size: cover;
        background-position: center;
    }

    .candidate-cover__overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(2, 6, 23, .55), rgba(2, 6, 23, .12));
    }

    .candidate-cover__actions {
        position: absolute;
        left: 14px;
        bottom: 14px;
        z-index: 2;
        min-width: 180px;
    }

    .candidate-profile-bar {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        padding: 0 1.2rem 1.1rem;
        margin-top: -52px;
    }

    .candidate-avatar-wrap {
        position: relative;
    }

    .candidate-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid #fff;
        background-size: cover;
        background-position: center;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .16);
        background-color: #e2e8f0;
    }

    .candidate-avatar-edit {
        margin-top: .55rem;
        min-width: 170px;
    }

    .candidate-profile-meta h2 {
        font-size: 1.35rem;
        font-weight: 900;
    }

    .candidate-profile-meta__chips {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
    }

    .candidate-profile-meta__chips .chip {
        padding: .28rem .6rem;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
        background: rgba(99, 102, 241, .1);
        color: #3730a3;
        border: 1px solid rgba(99, 102, 241, .18);
    }

    .candidate-edit-card {
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, .24);
        box-shadow: 0 8px 22px rgba(15, 23, 42, .06);
        transition: transform .22s ease, box-shadow .22s ease;
    }

    .candidate-edit-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, .11);
    }

    .candidate-edit-card__title {
        margin-bottom: .3rem;
        font-size: 1.03rem;
        font-weight: 800;
    }

    .candidate-edit-card__hint {
        color: #6b7280;
        font-size: .87rem;
        margin-bottom: 1rem;
    }

    .candidate-edit-submit-wrap {
        display: flex;
        justify-content: center;
        padding-top: .3rem;
        padding-bottom: .4rem;
    }

    .candidate-edit-submit-btn {
        min-width: 220px;
        min-height: 44px;
        border-radius: 10px;
        font-size: .95rem;
        font-weight: 800;
        transition: all .2s ease;
    }

    .candidate-edit-submit-btn:hover {
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .candidate-cover {
            min-height: 190px;
        }

        .candidate-profile-bar {
            flex-direction: column;
            align-items: flex-start;
            margin-top: -44px;
        }

        .candidate-avatar {
            width: 102px;
            height: 102px;
        }

        .candidate-cover__actions,
        .candidate-avatar-edit {
            min-width: 150px;
        }
    }

    @keyframes candidateProfileFade {
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

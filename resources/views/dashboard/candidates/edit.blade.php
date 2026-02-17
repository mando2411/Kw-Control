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

                    <div class="candidate-avatar-center-wrap">
                        <div class="candidate-avatar"
                             style="background-image:url('{{ $candidate->user->image ?: 'https://ui-avatars.com/api/?name='.urlencode($candidate->user->name ?? 'Candidate').'&background=0ea5e9&color=fff&size=256' }}')">
                        </div>
                        <div class="candidate-avatar-edit">
                            <x-dashboard.form.media title="تغيير الصورة الشخصية" :images="$candidate->user->image" name="image" />
                        </div>
                    </div>
                </div>

                <div class="candidate-profile-center">
                    <div class="candidate-name-inline">
                        <div class="editable-display js-display" data-target="nameInput">{{ old('name', $candidate->user->name) }}</div>
                        <input
                            type="text"
                            class="form-control js-input {{ $errors->has('name') ? '' : 'd-none' }}"
                            id="nameInput"
                            name="name"
                            value="{{ old('name', $candidate->user->name) }}"
                            required
                        >
                    </div>
                    @error('name')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror

                    <div class="candidate-profile-meta__chips justify-content-center mt-2">
                        <span class="chip">مرشح</span>
                        <span class="chip">{{ $candidate->election?->name ?? 'انتخابات غير محددة' }}</span>
                        <span class="chip">#{{ $candidate->id }}</span>
                    </div>

                    <p class="mb-0 mt-2 text-muted">اضغط على أي قيمة بالأسفل لفتحها وتعديلها مباشرة.</p>
                </div>
            </section>

            <div class="row g-3">
                <div class="col-12 col-xl-6">
                    <div class="card candidate-edit-card h-100">
                        <div class="card-body">
                            <h5 class="candidate-edit-card__title">البيانات الشخصية</h5>
                            <p class="candidate-edit-card__hint">انقر على القيمة لتعديلها مباشرة.</p>

                            <div class="profile-field">
                                <label>رقم الهاتف <span class="text-danger">*</span></label>
                                <div class="editable-display js-display" data-target="phoneInput">{{ old('phone', $candidate->user->phone) }}</div>
                                <input type="text" class="form-control js-input {{ $errors->has('phone') ? '' : 'd-none' }}" id="phoneInput" name="phone" value="{{ old('phone', $candidate->user->phone) }}" required>
                                @error('phone')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="profile-field">
                                <label>البريد الإلكتروني</label>
                                <div class="editable-display js-display" data-target="emailInput">{{ old('email', $candidate->user->email) ?: '—' }}</div>
                                <input type="email" class="form-control js-input {{ $errors->has('email') ? '' : 'd-none' }}" id="emailInput" name="email" value="{{ old('email', $candidate->user->email) }}">
                                @error('email')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="profile-field mb-0">
                                <label>كلمة مرور جديدة (اختياري)</label>
                                <div class="editable-display js-display" data-target="passwordInput">اضغط لتغيير كلمة المرور</div>
                                <input type="password" class="form-control js-input {{ $errors->has('password') ? '' : 'd-none' }}" id="passwordInput" name="password" placeholder="اتركه فارغًا إن لم ترد التغيير">
                                @error('password')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card candidate-edit-card h-100">
                        <div class="card-body">
                            <h5 class="candidate-edit-card__title">إعدادات الحملة الانتخابية</h5>
                            <p class="candidate-edit-card__hint">البيانات موزعة على شكل ملف بروفايل قابل للتعديل بالنقر.</p>

                            <div class="profile-field">
                                <label>الانتخابات</label>
                                <div class="editable-display js-display" data-target="electionInput">
                                    {{ optional($relations['elections']->firstWhere('id', old('election_id', $candidate->election_id)))->name ?? 'اختر الانتخابات' }}
                                </div>
                                <select class="form-control js-input {{ $errors->has('election_id') ? '' : 'd-none' }}" id="electionInput" name="election_id">
                                    <option value="" disabled>اختر الانتخابات</option>
                                    @foreach($relations['elections'] as $election)
                                        <option value="{{ $election->id }}" @selected((string)old('election_id', $candidate->election_id) === (string)$election->id)>
                                            {{ $election->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('election_id')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="profile-field">
                                <label>الحد الأقصى للمتعهدين <span class="text-danger">*</span></label>
                                <div class="editable-display js-display" data-target="maxContractorInput">{{ old('max_contractor', $candidate->max_contractor) }}</div>
                                <input type="number" min="0" class="form-control js-input {{ $errors->has('max_contractor') ? '' : 'd-none' }}" id="maxContractorInput" name="max_contractor" value="{{ old('max_contractor', $candidate->max_contractor) }}" required>
                                @error('max_contractor')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="profile-field mb-0">
                                <label>الحد الأقصى للمناديب <span class="text-danger">*</span></label>
                                <div class="editable-display js-display" data-target="maxRepresentInput">{{ old('max_represent', $candidate->max_represent) }}</div>
                                <input type="number" min="0" class="form-control js-input {{ $errors->has('max_represent') ? '' : 'd-none' }}" id="maxRepresentInput" name="max_represent" value="{{ old('max_represent', $candidate->max_represent) }}" required>
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
        min-height: 330px;
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

    .candidate-avatar-center-wrap {
        position: absolute;
        left: 50%;
        bottom: -72px;
        transform: translateX(-50%);
        z-index: 3;
        text-align: center;
    }

    .candidate-profile-center {
        text-align: center;
        padding: 92px 1.2rem 1.15rem;
    }

    .candidate-name-inline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 260px;
        max-width: min(92vw, 560px);
    }

    .candidate-avatar {
        width: 144px;
        height: 144px;
        border-radius: 50%;
        border: 4px solid #fff;
        background-size: cover;
        background-position: center;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .16);
        background-color: #e2e8f0;
    }

    .candidate-avatar-edit {
        margin-top: .45rem;
        min-width: 170px;
    }

    .editable-display {
        min-height: 46px;
        border-radius: 10px;
        border: 1px dashed rgba(99, 102, 241, .35);
        background: rgba(99, 102, 241, .08);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: .5rem .8rem;
        font-weight: 700;
        font-size: .98rem;
        color: #1f2937;
        cursor: text;
        transition: all .2s ease;
    }

    .editable-display:hover {
        border-color: rgba(99, 102, 241, .7);
        background: rgba(99, 102, 241, .14);
    }

    .candidate-name-inline .editable-display {
        min-height: 54px;
        width: 100%;
        font-size: 1.35rem;
        font-weight: 900;
        border-radius: 14px;
    }

    .candidate-name-inline .js-input {
        width: 100%;
        min-height: 54px;
        text-align: center;
        font-size: 1.2rem;
        font-weight: 800;
        border-radius: 14px;
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

    .profile-field {
        margin-bottom: 1rem;
    }

    .profile-field label {
        display: block;
        font-weight: 700;
        margin-bottom: .38rem;
        font-size: .9rem;
        color: #111827;
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
            min-height: 250px;
        }

        .candidate-avatar-center-wrap {
            bottom: -56px;
        }

        .candidate-avatar {
            width: 112px;
            height: 112px;
        }

        .candidate-cover__actions,
        .candidate-avatar-edit {
            min-width: 150px;
        }

        .candidate-profile-center {
            padding-top: 74px;
        }

        .candidate-name-inline {
            min-width: min(92vw, 460px);
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

@push('js')
<script>
    (function () {
        const displays = document.querySelectorAll('.js-display[data-target]');

        const closeInput = (input) => {
            const display = document.querySelector('.js-display[data-target="' + input.id + '"]');
            if (!display) return;

            if (input.tagName === 'SELECT') {
                const selectedText = input.options[input.selectedIndex]?.text || '—';
                display.textContent = selectedText;
            } else if (input.type === 'password') {
                display.textContent = input.value ? '••••••••' : 'اضغط لتغيير كلمة المرور';
            } else {
                display.textContent = input.value?.trim() ? input.value.trim() : '—';
            }

            input.classList.add('d-none');
            display.classList.remove('d-none');
        };

        displays.forEach((display) => {
            const targetId = display.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;

            display.addEventListener('click', () => {
                display.classList.add('d-none');
                input.classList.remove('d-none');
                input.focus();
                if (input.select) input.select();
            });

            if (input.tagName === 'SELECT') {
                input.addEventListener('change', () => closeInput(input));
                input.addEventListener('blur', () => closeInput(input));
            } else {
                input.addEventListener('blur', () => closeInput(input));
                input.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        input.blur();
                    }
                });
            }
        });
    })();
</script>
@endpush

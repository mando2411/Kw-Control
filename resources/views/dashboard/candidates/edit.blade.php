@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.candidates.update' , $candidate) }}" method="POST" class="page-body candidate-profile-page" dir="rtl">
        @csrf
        @method('PUT')
        <input type="hidden" name="user" value="{{ $candidate->user->id }}">
        @php
            $candidateRoleId = optional($relations['roles']->firstWhere('name', 'مرشح'))->id;
            $selectedRoles = old('roles', $candidate->user->roles->pluck('id')->toArray());
            if (empty($selectedRoles) && $candidateRoleId) {
                $selectedRoles = [$candidateRoleId];
            }
        @endphp
        @foreach($selectedRoles as $roleId)
            <input type="hidden" name="roles[]" value="{{ $roleId }}">
        @endforeach

        <x-dashboard.partials.breadcrumb title="تعديل ملف المرشح" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.candidates.index') }}">المرشحون</a>
            </li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid candidate-edit-modern">
            <x-dashboard.partials.message-alert />

            <section class="candidate-profile-hero mb-4">
                <div class="candidate-cover"
                     data-default-cover="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1600&q=80"
                     style="background-image:url('{{ $candidate->banner ?: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1600&q=80' }}')">
                    <div class="candidate-cover__overlay"></div>
                    <div class="candidate-cover__actions">
                        <button type="button" class="candidate-media-trigger js-media-trigger" data-menu="coverMenu">
                            <i class="fa fa-camera me-1"></i>
                            تعديل الغلاف
                        </button>
                        <div class="candidate-media-menu d-none" id="coverMenu">
                            <button type="button" class="candidate-media-menu__item js-media-change" data-field="banner">تغيير صورة البانر</button>
                            <button type="button" class="candidate-media-menu__item danger js-media-delete" data-field="banner">حذف صورة البانر</button>
                        </div>

                        <div class="media-hidden-control d-none">
                            <x-dashboard.form.media title="تغيير صورة الكوفر" :images="$candidate->banner" name="banner" />
                        </div>
                    </div>

                    <div class="candidate-avatar-center-wrap">
                        <div class="candidate-avatar"
                             data-default-avatar="https://ui-avatars.com/api/?name={{ urlencode($candidate->user->name ?? 'Candidate') }}&background=0ea5e9&color=fff&size=512"
                             style="background-image:url('{{ $candidate->user->image ?: 'https://ui-avatars.com/api/?name='.urlencode($candidate->user->name ?? 'Candidate').'&background=0ea5e9&color=fff&size=256' }}')">
                        </div>
                        <div class="candidate-avatar-edit">
                            <button type="button" class="candidate-media-trigger candidate-media-trigger--avatar js-media-trigger" data-menu="avatarMenu" aria-label="تعديل الصورة الشخصية">
                                <i class="fa fa-camera"></i>
                            </button>
                            <div class="candidate-media-menu d-none" id="avatarMenu">
                                <button type="button" class="candidate-media-menu__item js-media-change" data-field="image">تغيير الصورة الشخصية</button>
                                <button type="button" class="candidate-media-menu__item danger js-media-delete" data-field="image">حذف الصورة الشخصية</button>
                            </div>

                            <div class="media-hidden-control d-none">
                                <x-dashboard.form.media title="تغيير الصورة الشخصية" :images="$candidate->user->image" name="image" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="candidate-profile-center">
                    <h2 class="candidate-profile-name-text mb-1">{{ old('name', $candidate->user->name) }}</h2>

                    <div class="candidate-profile-meta__chips justify-content-center mt-2">
                        <span class="chip">مرشح</span>
                        <span class="chip">{{ $candidate->election?->name ?? 'انتخابات غير محددة' }}</span>
                        <span class="chip">#{{ $candidate->id }}</span>
                    </div>

                    <p class="mb-0 mt-2 text-muted">اضغط على أي قيمة بالأسفل لفتحها وتعديلها مباشرة.</p>
                </div>
            </section>

            <div class="row g-3">
                <div class="col-12 col-xl-8">
                    <div class="card candidate-edit-card h-100">
                        <div class="card-body">
                            <h5 class="candidate-edit-card__title">المعلومات الشخصية</h5>
                            <p class="candidate-edit-card__hint">تجربة تعديل مباشرة: اضغط على القيمة ثم احفظ أو ألغِ.</p>

                            <div class="profile-inline-item" data-field="nameInput">
                                <div class="profile-inline-item__label">اسم المرشح</div>
                                <div class="profile-inline-item__view js-display" data-target="nameInput">
                                    <span class="js-display-text">{{ old('name', $candidate->user->name) }}</span>
                                    <i class="fa fa-pen"></i>
                                </div>
                                <div class="profile-inline-item__edit d-none js-edit-wrap">
                                    <input type="text" class="form-control js-input" id="nameInput" name="name" value="{{ old('name', $candidate->user->name) }}" required>
                                    <div class="profile-inline-item__actions">
                                        <button type="button" class="btn btn-sm btn-primary js-save">حفظ</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary js-cancel">إلغاء</button>
                                    </div>
                                </div>
                                @error('name')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="profile-inline-item" data-field="phoneInput">
                                <div class="profile-inline-item__label">رقم الهاتف</div>
                                <div class="profile-inline-item__view js-display" data-target="phoneInput">
                                    <span class="js-display-text">{{ old('phone', $candidate->user->phone) ?: '—' }}</span>
                                    <i class="fa fa-pen"></i>
                                </div>
                                <div class="profile-inline-item__edit d-none js-edit-wrap">
                                    <input type="text" class="form-control js-input" id="phoneInput" name="phone" value="{{ old('phone', $candidate->user->phone) }}" required>
                                    <div class="profile-inline-item__actions">
                                        <button type="button" class="btn btn-sm btn-primary js-save">حفظ</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary js-cancel">إلغاء</button>
                                    </div>
                                </div>
                                @error('phone')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="profile-inline-item" data-field="emailInput">
                                <div class="profile-inline-item__label">البريد الإلكتروني</div>
                                <div class="profile-inline-item__view js-display" data-target="emailInput">
                                    <span class="js-display-text">{{ old('email', $candidate->user->email) ?: '—' }}</span>
                                    <i class="fa fa-pen"></i>
                                </div>
                                <div class="profile-inline-item__edit d-none js-edit-wrap">
                                    <input type="email" class="form-control js-input" id="emailInput" name="email" value="{{ old('email', $candidate->user->email) }}">
                                    <div class="profile-inline-item__actions">
                                        <button type="button" class="btn btn-sm btn-primary js-save">حفظ</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary js-cancel">إلغاء</button>
                                    </div>
                                </div>
                                @error('email')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="profile-inline-item" data-field="passwordInput">
                                <div class="profile-inline-item__label">كلمة المرور</div>
                                <div class="profile-inline-item__view js-display" data-target="passwordInput">
                                    <span class="js-display-text">اضغط للتعديل</span>
                                    <i class="fa fa-lock"></i>
                                </div>
                                <div class="profile-inline-item__edit d-none js-edit-wrap">
                                    <input type="password" class="form-control js-input" id="passwordInput" name="password" placeholder="اتركه فارغًا إن لم ترد التغيير">
                                    <div class="profile-inline-item__actions">
                                        <button type="button" class="btn btn-sm btn-primary js-save">حفظ</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary js-cancel">إلغاء</button>
                                    </div>
                                </div>
                                @error('password')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="card candidate-edit-card h-100">
                        <div class="card-body">
                            <h5 class="candidate-edit-card__title">بيانات الحملة</h5>
                            <p class="candidate-edit-card__hint">بيانات مرتبطة بسقف فريق المرشح والانتخابات.</p>

                            <div class="profile-inline-item" data-field="electionInput">
                                <div class="profile-inline-item__label">الانتخابات</div>
                                <div class="profile-inline-item__view js-display" data-target="electionInput">
                                    <span class="js-display-text">{{ optional($relations['elections']->firstWhere('id', old('election_id', $candidate->election_id)))->name ?? 'اختر الانتخابات' }}</span>
                                    <i class="fa fa-pen"></i>
                                </div>
                                <div class="profile-inline-item__edit d-none js-edit-wrap">
                                    <select class="form-control js-input" id="electionInput" name="election_id">
                                        <option value="" disabled>اختر الانتخابات</option>
                                        @foreach($relations['elections'] as $election)
                                            <option value="{{ $election->id }}" @selected((string)old('election_id', $candidate->election_id) === (string)$election->id)>
                                                {{ $election->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="profile-inline-item__actions">
                                        <button type="button" class="btn btn-sm btn-primary js-save">حفظ</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary js-cancel">إلغاء</button>
                                    </div>
                                </div>
                                @error('election_id')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="profile-inline-item" data-field="maxContractorInput">
                                <div class="profile-inline-item__label">الحد الأقصى للمتعهدين</div>
                                <div class="profile-inline-item__view js-display" data-target="maxContractorInput">
                                    <span class="js-display-text">{{ old('max_contractor', $candidate->max_contractor) }}</span>
                                    <i class="fa fa-pen"></i>
                                </div>
                                <div class="profile-inline-item__edit d-none js-edit-wrap">
                                    <input type="number" min="0" class="form-control js-input" id="maxContractorInput" name="max_contractor" value="{{ old('max_contractor', $candidate->max_contractor) }}" required>
                                    <div class="profile-inline-item__actions">
                                        <button type="button" class="btn btn-sm btn-primary js-save">حفظ</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary js-cancel">إلغاء</button>
                                    </div>
                                </div>
                                @error('max_contractor')<span class="d-block text-danger mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="profile-inline-item mb-0" data-field="maxRepresentInput">
                                <div class="profile-inline-item__label">الحد الأقصى للمناديب</div>
                                <div class="profile-inline-item__view js-display" data-target="maxRepresentInput">
                                    <span class="js-display-text">{{ old('max_represent', $candidate->max_represent) }}</span>
                                    <i class="fa fa-pen"></i>
                                </div>
                                <div class="profile-inline-item__edit d-none js-edit-wrap">
                                    <input type="number" min="0" class="form-control js-input" id="maxRepresentInput" name="max_represent" value="{{ old('max_represent', $candidate->max_represent) }}" required>
                                    <div class="profile-inline-item__actions">
                                        <button type="button" class="btn btn-sm btn-primary js-save">حفظ</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary js-cancel">إلغاء</button>
                                    </div>
                                </div>
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
    .candidate-profile-page .open-media { border-radius: 10px; font-weight: 700; }

    .candidate-edit-modern {
        animation: candidateProfileFade .45s ease;
    }

    .candidate-profile-hero {
        border-radius: 16px;
        overflow: visible;
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
        left: 16px;
        top: 16px;
        z-index: 2;
        min-width: 190px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .candidate-media-trigger {
        border: 0;
        background: rgba(15, 23, 42, .66);
        color: #fff;
        border-radius: 999px;
        font-size: .85rem;
        font-weight: 800;
        min-height: 38px;
        padding: .45rem .9rem;
        backdrop-filter: blur(2px);
        transition: all .2s ease;
    }

    .candidate-media-trigger:hover {
        background: rgba(15, 23, 42, .8);
        transform: translateY(-1px);
    }

    .candidate-media-menu {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        background: #fff;
        border: 1px solid rgba(148, 163, 184, .28);
        border-radius: 12px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .14);
        padding: .35rem;
        min-width: 200px;
        z-index: 12;
    }

    .candidate-media-menu__item {
        width: 100%;
        border: 0;
        background: transparent;
        border-radius: 8px;
        min-height: 36px;
        text-align: right;
        font-size: .86rem;
        font-weight: 700;
        color: #0f172a;
        padding: .45rem .6rem;
        transition: all .18s ease;
    }

    .candidate-media-menu__item:hover {
        background: #eef2ff;
    }

    .candidate-media-menu__item.danger {
        color: #b91c1c;
    }

    .candidate-media-menu__item.danger:hover {
        background: #fee2e2;
    }

    .candidate-avatar-center-wrap {
        position: absolute;
        left: 50%;
        bottom: -104px;
        transform: translateX(-50%);
        z-index: 3;
        text-align: center;
    }

    .candidate-profile-center {
        text-align: center;
        padding: 128px 1.2rem 1.15rem;
    }

    .candidate-avatar {
        width: 208px;
        height: 208px;
        border-radius: 50%;
        border: 6px solid #fff;
        background-size: cover;
        background-position: center;
        box-shadow: 0 18px 32px rgba(15, 23, 42, .28), inset 0 0 0 1px rgba(255, 255, 255, .5);
        background-color: #e2e8f0;
    }

    .candidate-avatar-edit {
        position: absolute;
        top: 68%;
        left: 76%;
        transform: translate(-50%, -50%);
        min-width: auto;
        z-index: 9;
    }

    .candidate-media-trigger--avatar {
        width: 44px;
        height: 44px;
        min-height: 44px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(15, 23, 42, .8);
        border: 2px solid rgba(255, 255, 255, .92);
        box-shadow: 0 8px 18px rgba(2, 6, 23, .34);
    }

    .candidate-media-trigger--avatar i {
        font-size: .95rem;
    }

    .candidate-avatar-edit .candidate-media-menu {
        top: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%);
    }

    .candidate-profile-name-text {
        font-size: clamp(1.35rem, 2.6vw, 2rem);
        font-weight: 900;
        color: #0f172a;
        line-height: 1.2;
    }

    .editable-display {
        min-height: 46px;
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, .45);
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .5rem .8rem;
        font-weight: 700;
        font-size: .98rem;
        color: #1f2937;
        cursor: text;
        transition: all .2s ease;
    }

    .editable-display:hover {
        border-color: rgba(99, 102, 241, .65);
        background: #eef2ff;
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

    .profile-inline-item {
        margin-bottom: 1rem;
    }

    .profile-inline-item__label {
        display: block;
        font-weight: 700;
        margin-bottom: .42rem;
        font-size: .9rem;
        color: #111827;
    }

    .profile-inline-item__edit {
        margin-top: .55rem;
    }

    .profile-inline-item__actions {
        display: flex;
        justify-content: flex-end;
        gap: .45rem;
        margin-top: .45rem;
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
            bottom: -84px;
        }

        .candidate-avatar {
            width: 168px;
            height: 168px;
        }

        .candidate-cover__actions,
        .candidate-avatar-edit {
            min-width: 164px;
        }

        .candidate-avatar-edit {
            top: 70%;
            left: 74%;
            min-width: auto;
        }

        .candidate-cover__actions {
            top: 10px;
            left: 10px;
        }

        .candidate-profile-center {
            padding-top: 106px;
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
        const items = document.querySelectorAll('.profile-inline-item');
        const profileNameDisplay = document.querySelector('.candidate-profile-name-text');

        const closeAllMediaMenus = () => {
            document.querySelectorAll('.candidate-media-menu').forEach((menu) => menu.classList.add('d-none'));
        };

        const removeMediaValue = (field) => {
            const gallery = document.getElementById(field);
            if (!gallery) return;

            gallery.querySelectorAll('input[name="' + field + '"]').forEach((node) => node.remove());
            gallery.querySelectorAll('.image-box').forEach((node) => node.remove());
        };

        const syncPreviewToDefault = (field) => {
            if (field === 'banner') {
                const cover = document.querySelector('.candidate-cover');
                if (!cover) return;
                const fallback = cover.dataset.defaultCover;
                if (fallback) cover.style.backgroundImage = 'url("' + fallback + '")';
            }

            if (field === 'image') {
                const avatar = document.querySelector('.candidate-avatar');
                if (!avatar) return;
                const fallback = avatar.dataset.defaultAvatar;
                if (fallback) avatar.style.backgroundImage = 'url("' + fallback + '")';
            }
        };

        document.querySelectorAll('.js-media-trigger').forEach((trigger) => {
            trigger.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                const menuId = trigger.dataset.menu;
                const menu = menuId ? document.getElementById(menuId) : null;
                if (!menu) return;

                const alreadyOpen = !menu.classList.contains('d-none');
                closeAllMediaMenus();
                if (!alreadyOpen) menu.classList.remove('d-none');
            });
        });

        document.querySelectorAll('.js-media-change').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const field = button.dataset.field;
                if (!field) return;

                const uploader = document.querySelector('.media-hidden-control .open-media[data-name="' + field + '"]');
                if (uploader) uploader.click();
                closeAllMediaMenus();
            });
        });

        document.querySelectorAll('.js-media-delete').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const field = button.dataset.field;
                if (!field) return;

                removeMediaValue(field);
                syncPreviewToDefault(field);
                closeAllMediaMenus();
            });
        });

        document.addEventListener('click', (event) => {
            if (event.target.closest('.candidate-media-menu') || event.target.closest('.js-media-trigger')) return;
            closeAllMediaMenus();
        });

        const getFieldText = (input) => {
            if (input.tagName === 'SELECT') {
                return input.options[input.selectedIndex]?.text || '—';
            }
            if (input.type === 'password') {
                return input.value ? '••••••••' : 'اضغط للتعديل';
            }
            return input.value?.trim() ? input.value.trim() : '—';
        };

        const enterEditMode = (item) => {
            const view = item.querySelector('.js-display');
            const edit = item.querySelector('.js-edit-wrap');
            const input = item.querySelector('.js-input');
            if (!view || !edit || !input) return;

            input.dataset.before = input.value;
            view.classList.add('d-none');
            edit.classList.remove('d-none');
            input.focus();
            if (input.select) input.select();
        };

        const leaveEditMode = (item, rollback = false) => {
            const view = item.querySelector('.js-display');
            const edit = item.querySelector('.js-edit-wrap');
            const input = item.querySelector('.js-input');
            const textNode = item.querySelector('.js-display-text');
            if (!view || !edit || !input || !textNode) return;

            if (rollback) {
                input.value = input.dataset.before ?? input.value;
            }

            textNode.textContent = getFieldText(input);
            view.classList.remove('d-none');
            edit.classList.add('d-none');

            if (input.id === 'nameInput' && profileNameDisplay) {
                profileNameDisplay.textContent = input.value?.trim() ? input.value.trim() : '—';
            }
        };

        items.forEach((item) => {
            const view = item.querySelector('.js-display');
            const input = item.querySelector('.js-input');
            const saveBtn = item.querySelector('.js-save');
            const cancelBtn = item.querySelector('.js-cancel');

            if (!view || !input || !saveBtn || !cancelBtn) return;

            view.addEventListener('click', () => enterEditMode(item));
            saveBtn.addEventListener('click', () => leaveEditMode(item, false));
            cancelBtn.addEventListener('click', () => leaveEditMode(item, true));

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' && input.tagName !== 'SELECT') {
                    event.preventDefault();
                    leaveEditMode(item, false);
                }
                if (event.key === 'Escape') {
                    event.preventDefault();
                    leaveEditMode(item, true);
                }
            });

            if (input.tagName === 'SELECT') {
                input.addEventListener('change', () => leaveEditMode(item, false));
            }

            if (!item.querySelector('.js-edit-wrap')?.classList.contains('d-none')) {
                leaveEditMode(item, false);
            }
        });
    })();
</script>
@endpush

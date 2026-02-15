@extends('layouts.dashboard.app')

@section('content')
<style>
    #profile-settings-app {
        direction: rtl;
        animation: profileFadeIn 320ms ease;
    }

    @keyframes profileFadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #profile-settings-app .pf-page-title {
        color: var(--ui-ink, #0f172a);
        font-weight: 900;
        margin-bottom: 4px;
    }

    #profile-settings-app .pf-page-subtitle {
        color: var(--ui-muted, #64748b);
        font-weight: 600;
        margin-bottom: 16px;
    }

    #profile-settings-app .pf-grid {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: 16px;
    }

    #profile-settings-app .pf-card {
        border-radius: 18px;
        border: 1px solid rgba(15, 23, 42, 0.10);
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 18px 44px rgba(2, 6, 23, 0.10);
        overflow: hidden;
    }

    #profile-settings-app .pf-card-h {
        padding: 14px 16px;
        border-bottom: 1px solid rgba(15, 23, 42, 0.10);
        background: linear-gradient(90deg, rgba(14, 165, 233, 0.12), rgba(255, 255, 255, 0.96));
    }

    #profile-settings-app .pf-card-h h5 {
        margin: 0;
        font-weight: 900;
        color: var(--ui-ink, #0f172a);
    }

    #profile-settings-app .pf-card-h p {
        margin: 6px 0 0;
        color: var(--ui-muted, #64748b);
        font-weight: 600;
        font-size: 0.92rem;
    }

    #profile-settings-app .pf-card-b {
        padding: 16px;
    }

    #profile-settings-app .pf-avatar-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
    }

    #profile-settings-app .pf-avatar {
        width: 88px;
        height: 88px;
        border-radius: 999px;
        object-fit: cover;
        border: 1px solid rgba(15, 23, 42, 0.10);
        box-shadow: 0 14px 36px rgba(2, 6, 23, 0.15);
    }

    #profile-settings-app .pf-readonly {
        background: rgba(148, 163, 184, 0.10);
        border: 1px solid rgba(148, 163, 184, 0.25);
        color: #1f2937;
        font-weight: 700;
    }

    #profile-settings-app .pf-help {
        color: #64748b;
        font-size: 0.86rem;
        margin-top: 6px;
        font-weight: 600;
    }

    #profile-settings-app .pf-actions {
        display: flex;
        justify-content: flex-start;
        gap: 10px;
        margin-top: 16px;
    }

    #profile-settings-app .pf-actions .btn {
        border-radius: 999px;
        font-weight: 800;
        padding: 9px 14px;
    }

    #profile-settings-app .pf-role-box {
        border: 1px dashed rgba(14, 165, 233, 0.45);
        border-radius: 14px;
        padding: 12px;
        background: rgba(14, 165, 233, 0.06);
        margin-top: 14px;
    }

    #profile-settings-app .pf-role-box .title {
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 8px;
    }

    @media (max-width: 992px) {
        #profile-settings-app .pf-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<section id="profile-settings-app" class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <h3 class="pf-page-title">تحرير الملف الشخصي</h3>
            <p class="pf-page-subtitle">إدارة بيانات الحساب بشكل آمن وسلس.</p>
        </div>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success" role="alert">تم تحديث بيانات الحساب بنجاح.</div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="alert alert-success" role="alert">تم تحديث كلمة المرور بنجاح.</div>
    @endif

    <div class="pf-grid">
        <div class="pf-card">
            <div class="pf-card-h">
                <h5>بيانات الحساب</h5>
                <p>يمكنك تعديل الصورة الشخصية، رقم الهاتف، البريد الإلكتروني، والصلاحيات (للأدمن فقط).</p>
            </div>
            <div class="pf-card-b">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="pf-avatar-wrap">
                        <img class="pf-avatar" src="{{ $user->image ?: asset('assets/admin/images/users/user-placeholder.png') }}" onerror="this.onerror=null;this.src='{{ asset('assets/admin/images/users/user-placeholder.png') }}';" alt="avatar">
                        <div>
                            <label class="form-label fw-bold mb-1">الصورة الشخصية</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <x-input-error class="mt-2" :messages="$errors->get('image')" />
                            @if($user->image)
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                    <label class="form-check-label" for="remove_image">حذف الصورة الحالية</label>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">الاسم</label>
                        <input type="text" class="form-control pf-readonly" value="{{ $user->name }}" readonly>
                        <div class="pf-help">الاسم ظاهر للمراجعة فقط ولا يمكن تعديله من هذه الصفحة.</div>
                    </div>

                    <div class="mb-3">
                        <label for="profile_email" class="form-label fw-bold">البريد الإلكتروني</label>
                        <input id="profile_email" type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="mb-2">
                        <label for="profile_phone" class="form-label fw-bold">رقم التليفون</label>
                        <input id="profile_phone" type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    @if($isAdmin)
                        <div class="pf-role-box">
                            <div class="title">الصلاحيات (Roles)</div>
                            <input type="hidden" name="roles_present" value="1">
                            <select name="roles[]" class="form-control" multiple size="6">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" @selected($user->roles->contains('id', $role->id))>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pf-help">بما أنك أدمن يمكنك تعديل الصلاحيات مباشرة.</div>
                            <x-input-error class="mt-2" :messages="$errors->get('roles')" />
                            <x-input-error class="mt-2" :messages="$errors->get('roles.*')" />
                        </div>
                    @endif

                    <div class="pf-actions">
                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="pf-card">
            <div class="pf-card-h">
                <h5>تحديث كلمة المرور</h5>
                <p>استخدم كلمة مرور قوية لحماية الحساب.</p>
            </div>
            <div class="pf-card-b">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-bold">كلمة المرور الحالية</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="current-password">
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">كلمة المرور الجديدة</label>
                        <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                        <div class="pf-help">يفضل أن تكون 8 أحرف أو أكثر وتحتوي على أرقام وحروف.</div>
                    </div>

                    <div class="mb-2">
                        <label for="password_confirmation" class="form-label fw-bold">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="pf-actions">
                        <button type="submit" class="btn btn-primary">تحديث كلمة المرور</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

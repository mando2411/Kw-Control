@extends('layouts.dashboard.app')

@section('content')
@if (session('status'))
<p class="text-success h3 rtl">
{{session('status')}}
</p>
@endif
<section class="rtl">
        <div class="container mt-5">

            <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                @csrf
                @method('put')
                <div>
                    <label for="current_password" class="fw-semibold fs-5">الرقم السرى القديم</label>
                    <input type="password" name="current_password" id="current_password" class="form-control mt-2" autocomplete="current-password">
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />


                </div>
                <div class="mt-4">
                    <label for="password" class="fw-semibold fs-5">الرقم السرى الجديد</label>
                    <input type="password" name="password" id="password" class="form-control mt-2" autocomplete="new-password">
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />


                    <p class="text-secondary mb-0">يجب الا يقل الرقم السرى الجديد عن 6 أرقام أو حروف</p>
                </div>
                <div class="mt-2">
                    <label for="password_confirmation" class="fw-semibold fs-5"> اعادة ادخال الرقم السرى الجديد</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control mt-2" autocomplete="new-password">
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />


                    <p class="text-secondary mb-0">بعد تغيير الرقم السرى سوف يطلب الموقع اعادة تسجيل الدخول من جديد باستخدام
                        الرقم السرى الجديد</p>
                </div>
                <button type="submit" class="btn btn-primary my-4">تغيير الرقم السرى</button>
                @if (session('status') === 'password-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
                @endif
            </form>
        </div>
    </section>
@endsection

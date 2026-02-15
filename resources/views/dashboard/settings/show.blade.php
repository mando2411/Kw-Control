@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.settings.update' ) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Settings" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.settings.show') }}">Settings</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->


        <!-- Container-fluid starts-->
        <div class="container-fluid" id="settings-app">
            <div class="row">
                <x-dashboard.partials.message-alert/>
                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.multi-tab-card
                            :tabs="['رسائل', 'تصميم الواجهة']"
                            tab-id="settings">

                            
                            <div class="tab-pane fade active show"
                            id="{{ 'settings-0' }}" role="tabpanel"
                            aria-labelledby="{{ 'settings-0' }}-tab">

                            <x-dashboard.form.input-editor error-key="{{ \App\Enums\SettingKey::MESSAGE->value }}"
                                                             required
                                                             :value="old(\App\Enums\SettingKey::MESSAGE->value.'.0',
                                                              $settings->firstWhere('option_key', \App\Enums\SettingKey::MESSAGE->value)?->option_value[0] ?? '')"
                                                             name="{{ \App\Enums\SettingKey::MESSAGE->value }}[]" id="{{ \App\Enums\SettingKey::MESSAGE->value }}"
                                                             label-title="Whatsapp Message"/>
                       </div>

                        <div class="tab-pane fade"
                             id="{{ 'settings-1' }}" role="tabpanel"
                             aria-labelledby="{{ 'settings-1' }}-tab" style="direction: rtl;">

                            @php
                                $policyCurrent = old(\App\Enums\SettingKey::UI_MODE_POLICY->value.'.0',
                                    $settings->firstWhere('option_key', \App\Enums\SettingKey::UI_MODE_POLICY->value)?->option_value[0] ?? 'user_choice');
                                $policyCurrent = in_array($policyCurrent, ['user_choice', 'modern', 'classic'], true) ? $policyCurrent : 'user_choice';
                            @endphp

                            <div class="alert alert-info" style="border-radius: 14px;">
                                <strong>سياسة تصميم الموقع</strong>
                                <div style="margin-top: 6px;">
                                    اختر كيف يتم تحديد شكل الواجهة في النظام:
                                    <ul style="margin: 10px 0 0;">
                                        <li><strong>اجعل المستخدم يحدد</strong>: يظهر سويتش "الشكل الحديث" في السلايدر وصفحة تسجيل الدخول، ويستطيع كل مستخدم اختيار الشكل.</li>
                                        <li><strong>التصميم الحديث</strong>: يتم تفعيل الشكل الحديث للجميع ويختفي السويتش.</li>
                                        <li><strong>التصميم القديم</strong>: يتم تفعيل الشكل القديم للجميع ويختفي السويتش.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">اختيار تصميم الموقع</label>
                                <select class="form-control" name="{{ \App\Enums\SettingKey::UI_MODE_POLICY->value }}[]" id="{{ \App\Enums\SettingKey::UI_MODE_POLICY->value }}">
                                    <option value="user_choice" @selected($policyCurrent === 'user_choice')>اجعل المستخدم يحدد (السويتش ظاهر)</option>
                                    <option value="modern" @selected($policyCurrent === 'modern')>التصميم الحديث (إجباري)</option>
                                    <option value="classic" @selected($policyCurrent === 'classic')>التصميم القديم (إجباري)</option>
                                </select>
                            </div>

                        </div>

                        </x-dashboard.form.multi-tab-card>
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection

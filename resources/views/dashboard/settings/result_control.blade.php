@extends('layouts.dashboard.app')

@section('content')
    <!-- Container-fluid starts-->
    <div class="container-fluid" id="settings-app">
        <style>
            /* Settings Result Control: Dual UI (classic/modern) */
            #settings-app .settings-modern {
                display: none;
            }

            html.ui-modern #settings-app .settings-classic,
            body.ui-modern #settings-app .settings-classic {
                display: none;
            }

            html.ui-modern #settings-app .settings-modern,
            body.ui-modern #settings-app .settings-modern {
                display: block;
            }

            html.ui-modern #settings-app .settings-modern {
                direction: rtl;
            }

            html.ui-modern #settings-app .sm-page-title,
            body.ui-modern #settings-app .sm-page-title {
                font-weight: 900;
                color: var(--ui-ink);
                margin: 0 0 2px;
            }

            html.ui-modern #settings-app .sm-page-subtitle,
            body.ui-modern #settings-app .sm-page-subtitle {
                color: var(--ui-muted);
                margin: 0 0 14px;
                font-weight: 600;
            }

            html.ui-modern #settings-app .sm-card,
            body.ui-modern #settings-app .sm-card {
                background: var(--ui-surface);
                border: 1px solid var(--ui-border);
                border-radius: 18px;
                box-shadow: 0 18px 44px rgba(2, 6, 23, 0.10);
                overflow: hidden;
            }

            html.ui-modern #settings-app .sm-card-h,
            body.ui-modern #settings-app .sm-card-h {
                padding: 14px 16px;
                border-bottom: 1px solid var(--ui-border);
                background: linear-gradient(90deg, var(--ui-accent-soft), rgba(255, 255, 255, 0.96));
            }

            html.ui-modern #settings-app .sm-card-h h5,
            body.ui-modern #settings-app .sm-card-h h5 {
                margin: 0;
                font-weight: 900;
                color: var(--ui-ink);
                font-size: 1.02rem;
            }

            html.ui-modern #settings-app .sm-card-h p,
            body.ui-modern #settings-app .sm-card-h p {
                margin: 6px 0 0;
                color: var(--ui-muted);
                font-weight: 600;
                font-size: 0.92rem;
            }

            html.ui-modern #settings-app .sm-card-b,
            body.ui-modern #settings-app .sm-card-b {
                padding: 16px;
            }

            html.ui-modern #settings-app .sm-help,
            body.ui-modern #settings-app .sm-help {
                margin: 0 0 12px;
                padding: 10px 12px;
                border-radius: 14px;
                border: 1px solid var(--ui-border);
                background: var(--ui-surface-2);
                color: var(--ui-muted);
                font-weight: 600;
            }

            html.ui-modern #settings-app .sm-actions,
            body.ui-modern #settings-app .sm-actions {
                display: flex;
                justify-content: flex-start;
                gap: 10px;
                margin-top: 14px;
            }

            html.ui-modern #settings-app .sm-actions .btn,
            body.ui-modern #settings-app .sm-actions .btn {
                border-radius: 999px;
                font-weight: 800;
                padding: 10px 14px;
            }

            html.ui-modern #settings-app select.form-control,
            body.ui-modern #settings-app select.form-control {
                border-radius: 14px;
                border: 1px solid var(--ui-border);
                box-shadow: 0 10px 22px rgba(2, 6, 23, 0.06);
                background-color: var(--ui-surface);
                color: var(--ui-ink);
            }

            html.ui-modern #settings-app .sm-split,
            body.ui-modern #settings-app .sm-split {
                display: grid;
                grid-template-columns: 1fr;
                gap: 14px;
            }

            @media (min-width: 992px) {
                html.ui-modern #settings-app .sm-split,
                body.ui-modern #settings-app .sm-split {
                    grid-template-columns: 1fr 1fr;
                }
            }
        </style>

        <div class="row">
            <x-dashboard.partials.message-alert />

            <div class="settings-classic">
                <div class="card tab2-card">
                    <!-- ================================================================== -->
                    <form action="{{ route('dashboard.settings.update' ) }}" method="POST" >
                        @csrf
                        @method('PUT')
                        <div class="card-body needs-validation" style="direction: rtl;">
                            <x-dashboard.form.multi-tab-card :tabs="['للتحكم بامكانية عرض صفحة النتائج العامه']"  tab-id="settings">
                                <div class="tab-pane fade active show"
                                    id="{{ 'settings-0' }}" role="tabpanel"
                                    aria-labelledby="{{ 'settings-0' }}-tab">

                                        @php
                                            $policyCurrent = old(\App\Enums\SettingKey::UI_MODE_POLICY->value.'.0',
                                                $settings->firstWhere('option_key', \App\Enums\SettingKey::UI_MODE_POLICY->value)?->option_value[0] ?? 'user_choice');
                                            $policyCurrent = in_array($policyCurrent, ['user_choice', 'modern', 'classic'], true) ? $policyCurrent : 'user_choice';
                                        @endphp

                                        <div class="col-12 mb-3">
                                            <div class="card border-info" style="padding: 12px; border-radius: 12px;">
                                                <div class="fw-bold" style="margin-bottom: 6px;">اختيار تصميم الموقع</div>
                                                <div style="color: #6c757d; font-weight: 600;">
                                                    هذا الإعداد يحدد هل المستخدم يقدر يختار الشكل (سويتش ظاهر في السلايدر واللوجن) أو يتم إجبار الجميع على تصميم واحد.
                                                </div>
                                                <div style="margin-top: 10px;">
                                                    <select class="form-control" name="{{ \App\Enums\SettingKey::UI_MODE_POLICY->value }}[]" id="ui_mode_policy_rc">
                                                        <option value="user_choice" @selected($policyCurrent === 'user_choice')>اجعل المستخدم يحدد (السويتش ظاهر)</option>
                                                        <option value="modern" @selected($policyCurrent === 'modern')>التصميم الحديث (إجباري)</option>
                                                        <option value="classic" @selected($policyCurrent === 'classic')>التصميم القديم (إجباري)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <div class="card border-danger text-center fw-bold">
                                                <!-- legacy note placeholder -->
                                            </div>
                                        </div>

                                    <input type="hidden" name="{{ \App\Enums\SettingKey::RESULT_CONTROL->value }}__present" value="1">
                                    <x-dashboard.form.input-checkbox error-key="{{ \App\Enums\SettingKey::RESULT_CONTROL->value }}" name="{{ \App\Enums\SettingKey::RESULT_CONTROL->value }}[]" id="{{ \App\Enums\SettingKey::RESULT_CONTROL->value }}"
                                        label-title="عرض النتائج العامه"
                                        :value="old(\App\Enums\SettingKey::RESULT_CONTROL->value.'.0', $settings->firstWhere('option_key', \App\Enums\SettingKey::RESULT_CONTROL->value)?->option_value[0] ?? '')"
                                        />

                                    <select name="{{ \App\Enums\SettingKey::RESULT_CONTROL_CANDIDATE->value }}[]" id="{{ \App\Enums\SettingKey::RESULT_CONTROL_CANDIDATE->value }}" class="form-control">
                                        <option value="" selected>أختر المرشح</option>
                                        @foreach ($candidates as $candidate )
                                            <option value="{{$candidate->user_id}}"
                                            <?php
                                                $check_setting=$settings->firstWhere('option_key', \App\Enums\SettingKey::RESULT_CONTROL_CANDIDATE->value);
                                                if((isset($check_setting)) && ($check_setting->option_value != null) && ($check_setting->option_value[0] == $candidate->user_id) ){
                                                    echo 'selected';
                                                }
                                            ?>
                                            > ({{$candidate->user_id}}) - {{$candidate->user->name}}</option>
                                        @endforeach
                                    </select>
                                    <br>

                                </div>
                            </x-dashboard.form.multi-tab-card>
                            <div style="float: left;">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <!-- ================================================================== -->
                    <hr>
                    <!-- ================================================================== -->
                    <form action="{{ route('attendant.initalize' ) }}" method="POST" >
                        @csrf
                        @method('PUT')
                        <div class="card-body needs-validation" style="direction: rtl;" >
                            <x-dashboard.form.multi-tab-card :tabs="['لتصفير الحضور ل انتخابات معينه']" tab-id="initalize_attendant" >
                                <div class="tab-pane fade active show">
                                    <select name="election_id" id="election_id" class="form-control" required>
                                        <option value="" selected>أختر الانتخابات</option>
                                        @foreach ($elections as $election )
                                            <option value="{{$election->id}}" > ({{$election->name}})</option>
                                        @endforeach
                                    </select>
                                    <br>

                                </div>
                            </x-dashboard.form.multi-tab-card>
                            <div style="float: left;">
                                <button type="submit" class="btn btn-primary">تصفير الحضور</button>
                            </div>
                        </div>
                    </form>
                    <br>
                    <!-- ============================================== -->
                </div>
            </div>

            <div class="settings-modern">
                <div class="col-12 mb-3">
                    <h3 class="sm-page-title">الإعدادات</h3>
                    <p class="sm-page-subtitle">تحكم سريع وآمن في عرض النتائج العامة وتصفير الحضور للانتخابات.</p>
                </div>

                @php
                    $policyCurrent = old(\App\Enums\SettingKey::UI_MODE_POLICY->value.'.0',
                        $settings->firstWhere('option_key', \App\Enums\SettingKey::UI_MODE_POLICY->value)?->option_value[0] ?? 'user_choice');
                    $policyCurrent = in_array($policyCurrent, ['user_choice', 'modern', 'classic'], true) ? $policyCurrent : 'user_choice';
                @endphp

                <div class="sm-card mb-3">
                    <div class="sm-card-h">
                        <h5>تصميم الواجهة</h5>
                        <p>حدد سياسة التصميم: هل المستخدم يختار بنفسه، أم يتم اعتماد تصميم واحد للجميع.</p>
                    </div>
                    <div class="sm-card-b">
                        <div class="sm-help">
                            عند اختيار <strong>اجعل المستخدم يحدد</strong> سيظهر سويتش الواجهة الحديثة في السلايدر وصفحة تسجيل الدخول.
                            أما عند اختيار تصميم إجباري فسيتم تطبيقه تلقائيًا وإخفاء السويتش.
                        </div>

                        <form action="{{ route('dashboard.settings.update' ) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <label class="form-label fw-bold">اختيار تصميم الموقع</label>
                            <select class="form-control" name="{{ \App\Enums\SettingKey::UI_MODE_POLICY->value }}[]" id="ui_mode_policy_rc_modern">
                                <option value="user_choice" @selected($policyCurrent === 'user_choice')>اجعل المستخدم يحدد (السويتش ظاهر)</option>
                                <option value="modern" @selected($policyCurrent === 'modern')>التصميم الحديث (إجباري)</option>
                                <option value="classic" @selected($policyCurrent === 'classic')>التصميم القديم (إجباري)</option>
                            </select>

                            <div class="sm-actions">
                                <button type="submit" class="btn btn-primary">حفظ الإعداد</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="sm-split">
                    <div class="sm-card">
                        <div class="sm-card-h">
                            <h5>التحكم في عرض النتائج العامة</h5>
                            <p>فعّل/أوقف صفحة النتائج العامة، واختر المرشح المسؤول عن الفرز العام.</p>
                        </div>
                        <div class="sm-card-b">
                            <div class="sm-help">
                                ملاحظة: تأكد من إضافة المرشح المطلوب (مثل "مرشح الفرز العام") قبل تفعيل الميزة.
                            </div>

                            <form action="{{ route('dashboard.settings.update' ) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="{{ \App\Enums\SettingKey::RESULT_CONTROL->value }}__present" value="1">

                                <x-dashboard.form.input-checkbox
                                    error-key="{{ \App\Enums\SettingKey::RESULT_CONTROL->value }}"
                                    name="{{ \App\Enums\SettingKey::RESULT_CONTROL->value }}[]"
                                    id="{{ \App\Enums\SettingKey::RESULT_CONTROL->value }}_modern"
                                    label-title="عرض النتائج العامة"
                                    :value="old(\App\Enums\SettingKey::RESULT_CONTROL->value.'.0', $settings->firstWhere('option_key', \App\Enums\SettingKey::RESULT_CONTROL->value)?->option_value[0] ?? '')"
                                />

                                <div class="mt-3">
                                    <label class="form-label fw-bold">المرشح</label>
                                    <select name="{{ \App\Enums\SettingKey::RESULT_CONTROL_CANDIDATE->value }}[]" id="{{ \App\Enums\SettingKey::RESULT_CONTROL_CANDIDATE->value }}_modern" class="form-control">
                                        <option value="" selected>أختر المرشح</option>
                                        @foreach ($candidates as $candidate )
                                            <option value="{{$candidate->user_id}}"
                                            <?php
                                                $check_setting=$settings->firstWhere('option_key', \App\Enums\SettingKey::RESULT_CONTROL_CANDIDATE->value);
                                                if((isset($check_setting)) && ($check_setting->option_value != null) && ($check_setting->option_value[0] == $candidate->user_id) ){
                                                    echo 'selected';
                                                }
                                            ?>
                                            > ({{$candidate->user_id}}) - {{$candidate->user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="sm-actions">
                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="sm-card">
                        <div class="sm-card-h">
                            <h5>تصفير الحضور لانتخابات معينة</h5>
                            <p>يعيد تهيئة بيانات الحضور للانتخابات المحددة.</p>
                        </div>
                        <div class="sm-card-b">
                            <form action="{{ route('attendant.initalize' ) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <label class="form-label fw-bold">الانتخابات</label>
                                <select name="election_id" id="election_id_modern" class="form-control" required>
                                    <option value="" selected>أختر الانتخابات</option>
                                    @foreach ($elections as $election )
                                        <option value="{{$election->id}}"> ({{$election->name}})</option>
                                    @endforeach
                                </select>

                                <div class="sm-actions">
                                    <button type="submit" class="btn btn-primary">تصفير الحضور</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
@endsection
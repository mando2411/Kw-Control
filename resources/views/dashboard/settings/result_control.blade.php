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

                    $themePresetCurrent = old(\App\Enums\SettingKey::UI_MODERN_THEME_PRESET->value.'.0',
                        $settings->firstWhere('option_key', \App\Enums\SettingKey::UI_MODERN_THEME_PRESET->value)?->option_value[0] ?? 'default');
                    $themePresetCurrent = in_array($themePresetCurrent, ['default', 'emerald', 'violet', 'custom'], true) ? $themePresetCurrent : 'default';

                    $themeSettingValue = static function (string $key, string $fallback) use ($settings) {
                        $value = old($key.'.0', $settings->firstWhere('option_key', $key)?->option_value[0] ?? $fallback);
                        return is_string($value) && trim($value) !== '' ? trim($value) : $fallback;
                    };

                    $themeHexValue = static function (string $key, string $fallback) use ($themeSettingValue) {
                        $value = $themeSettingValue($key, $fallback);
                        if (preg_match('/^#([0-9a-fA-F]{6})$/', $value)) {
                            return strtolower($value);
                        }

                        if (preg_match('/^#([0-9a-fA-F]{3})$/', $value, $matches)) {
                            $hex = strtolower($matches[1]);
                            return '#'.$hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
                        }

                        return strtolower($fallback);
                    };

                    $themeLightDefaults = [
                        \App\Enums\SettingKey::UI_MODERN_BTN_PRIMARY->value => ['label' => 'زر أساسي (Primary)', 'default' => '#0ea5e9'],
                        \App\Enums\SettingKey::UI_MODERN_BTN_SECONDARY->value => ['label' => 'زر ثانوي (Secondary)', 'default' => '#6366f1'],
                        \App\Enums\SettingKey::UI_MODERN_BTN_TERTIARY->value => ['label' => 'زر ثالث (Tertiary)', 'default' => '#14b8a6'],
                        \App\Enums\SettingKey::UI_MODERN_BTN_QUATERNARY->value => ['label' => 'زر رابع (Quaternary)', 'default' => '#f59e0b'],
                        \App\Enums\SettingKey::UI_MODERN_TEXT_PRIMARY->value => ['label' => 'نص أساسي', 'default' => '#0f172a'],
                        \App\Enums\SettingKey::UI_MODERN_TEXT_SECONDARY->value => ['label' => 'نص ثانوي', 'default' => '#475569'],
                        \App\Enums\SettingKey::UI_MODERN_BG_PRIMARY->value => ['label' => 'خلفية أساسية', 'default' => '#ffffff'],
                        \App\Enums\SettingKey::UI_MODERN_BG_SECONDARY->value => ['label' => 'خلفية ثانوية', 'default' => '#f8fafc'],
                    ];

                    $themeDarkDefaults = [
                        \App\Enums\SettingKey::UI_MODERN_DARK_BTN_PRIMARY->value => ['label' => 'زر أساسي (Dark)', 'default' => '#38bdf8'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_BTN_SECONDARY->value => ['label' => 'زر ثانوي (Dark)', 'default' => '#818cf8'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_BTN_TERTIARY->value => ['label' => 'زر ثالث (Dark)', 'default' => '#2dd4bf'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_BTN_QUATERNARY->value => ['label' => 'زر رابع (Dark)', 'default' => '#fbbf24'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_TEXT_PRIMARY->value => ['label' => 'نص أساسي (Dark)', 'default' => '#f1f5f9'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_TEXT_SECONDARY->value => ['label' => 'نص ثانوي (Dark)', 'default' => '#cbd5e1'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_BG_PRIMARY->value => ['label' => 'خلفية أساسية (Dark)', 'default' => '#0f172a'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_BG_SECONDARY->value => ['label' => 'خلفية ثانوية (Dark)', 'default' => '#1e293b'],
                    ];

                    $fontDefaults = [
                        \App\Enums\SettingKey::UI_MODERN_FS_XS->value => ['label' => 'حجم XS', 'default' => '0.75rem'],
                        \App\Enums\SettingKey::UI_MODERN_FS_SM->value => ['label' => 'حجم SM', 'default' => '0.875rem'],
                        \App\Enums\SettingKey::UI_MODERN_FS_BASE->value => ['label' => 'الحجم الأساسي', 'default' => '1rem'],
                        \App\Enums\SettingKey::UI_MODERN_FS_LG->value => ['label' => 'حجم LG', 'default' => '1.125rem'],
                        \App\Enums\SettingKey::UI_MODERN_FS_XL->value => ['label' => 'حجم XL', 'default' => '1.25rem'],
                    ];

                    $componentDefaults = [
                        \App\Enums\SettingKey::UI_MODERN_LINK_COLOR->value => ['label' => 'لون الروابط (Light)', 'default' => '#0ea5e9', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_LINK_COLOR->value => ['label' => 'لون الروابط (Dark)', 'default' => '#38bdf8', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_BORDER_COLOR->value => ['label' => 'لون الحدود (Light)', 'default' => '#dbe3ef', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_BORDER_COLOR->value => ['label' => 'لون الحدود (Dark)', 'default' => '#64748b', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_HOME_BOX_BG->value => ['label' => 'خلفية بوكسات الرئيسية (Light)', 'default' => '#f8fafc', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_HOME_BOX_BORDER->value => ['label' => 'حدود بوكسات الرئيسية (Light)', 'default' => '#dbe3ef', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_HOME_BOX_BG->value => ['label' => 'خلفية بوكسات الرئيسية (Dark)', 'default' => '#1e293b', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_HOME_BOX_BORDER->value => ['label' => 'حدود بوكسات الرئيسية (Dark)', 'default' => '#475569', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_RADIUS_CARD->value => ['label' => 'استدارة الكروت', 'default' => '1rem', 'type' => 'size'],
                        \App\Enums\SettingKey::UI_MODERN_RADIUS_INPUT->value => ['label' => 'استدارة المدخلات', 'default' => '0.75rem', 'type' => 'size'],
                        \App\Enums\SettingKey::UI_MODERN_RADIUS_BUTTON->value => ['label' => 'استدارة الأزرار', 'default' => '0.75rem', 'type' => 'size'],
                        \App\Enums\SettingKey::UI_MODERN_SPACE_SECTION->value => ['label' => 'تباعد الأقسام', 'default' => '1.25rem', 'type' => 'size'],
                        \App\Enums\SettingKey::UI_MODERN_SPACE_CARD->value => ['label' => 'Padding الكروت', 'default' => '1rem', 'type' => 'size'],
                        \App\Enums\SettingKey::UI_MODERN_CONTAINER_MAX->value => ['label' => 'أقصى عرض للمحتوى', 'default' => '1320px', 'type' => 'size'],
                        \App\Enums\SettingKey::UI_MODERN_SHADOW_LEVEL->value => ['label' => 'مستوى الظل', 'default' => 'medium', 'type' => 'select'],
                    ];
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

                <div class="sm-card mb-3">
                    <div class="sm-card-h">
                        <h5>نظام الثيمات</h5>
                        <p>اختر ثيم جاهز متناسق (Light + Dark) أو اختر Custom لتفعيل الإعدادات اليدوية بالأسفل.</p>
                    </div>
                    <div class="sm-card-b">
                        <form action="{{ route('dashboard.settings.update' ) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <label class="form-label fw-bold">الثيم الحالي</label>
                            <select class="form-control" name="{{ \App\Enums\SettingKey::UI_MODERN_THEME_PRESET->value }}[]" id="ui_modern_theme_preset">
                                <option value="default" @selected($themePresetCurrent === 'default')>Default (الثيم الأصلي)</option>
                                <option value="emerald" @selected($themePresetCurrent === 'emerald')>Emerald (أخضر/تركواز متناسق)</option>
                                <option value="violet" @selected($themePresetCurrent === 'violet')>Violet (بنفسجي/وردي متناسق)</option>
                                <option value="custom" @selected($themePresetCurrent === 'custom')>Custom (يدوي)</option>
                            </select>

                            <div class="sm-actions">
                                <button type="submit" class="btn btn-primary">تطبيق الثيم</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="sm-card mb-3">
                    <div class="sm-card-h">
                        <h5>الوضع Custom (تخصيص يدوي)</h5>
                        <p>هذه الإعدادات تعمل عند اختيار Custom من نظام الثيمات، وتشمل ألوان الواجهة + بوكسات الرئيسية + الخطوط والتخطيط.</p>
                    </div>
                    <div class="sm-card-b">
                        <div class="sm-help">
                            اختَر اللون مباشرة من المربع، ويمكنك أيضًا تعديل كود اللون يدويًا. أحجام الخطوط بصيغة <strong>rem</strong> أو <strong>px</strong>.
                        </div>

                        <form action="{{ route('dashboard.settings.update' ) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-12">
                                    <h6 class="fw-bold mb-2">1) ألوان الواجهة الحديثة (Light)</h6>
                                </div>

                                @foreach ($themeLightDefaults as $key => $meta)
                                    @php $colorId = 'theme_color_'.$key; @endphp
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label fw-bold">{{ $meta['label'] }}</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="{{ $colorId }}_picker" value="{{ $themeHexValue($key, $meta['default']) }}" data-sync-target="{{ $colorId }}_text" title="اختر اللون">
                                            <input type="text" class="form-control" id="{{ $colorId }}_text" name="{{ $key }}[]" value="{{ $themeHexValue($key, $meta['default']) }}" placeholder="{{ $meta['default'] }}" pattern="^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$">
                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-12 mt-2">
                                    <h6 class="fw-bold mb-2">2) ألوان الوضع الداكن (Dark)</h6>
                                </div>

                                @foreach ($themeDarkDefaults as $key => $meta)
                                    @php $colorId = 'theme_color_'.$key; @endphp
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label fw-bold">{{ $meta['label'] }}</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="{{ $colorId }}_picker" value="{{ $themeHexValue($key, $meta['default']) }}" data-sync-target="{{ $colorId }}_text" title="اختر اللون">
                                            <input type="text" class="form-control" id="{{ $colorId }}_text" name="{{ $key }}[]" value="{{ $themeHexValue($key, $meta['default']) }}" placeholder="{{ $meta['default'] }}" pattern="^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$">
                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-12 mt-2">
                                    <h6 class="fw-bold mb-2">3) أحجام الخطوط</h6>
                                </div>

                                @foreach ($fontDefaults as $key => $meta)
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label fw-bold">{{ $meta['label'] }}</label>
                                        <input type="text" class="form-control" name="{{ $key }}[]" value="{{ $themeSettingValue($key, $meta['default']) }}" placeholder="{{ $meta['default'] }}">
                                    </div>
                                @endforeach

                                <div class="col-12 mt-2">
                                    <h6 class="fw-bold mb-2">4) تحكم شامل للمكونات والتخطيط</h6>
                                </div>

                                @foreach ($componentDefaults as $key => $meta)
                                    @if ($meta['type'] === 'color')
                                        @php $colorId = 'theme_component_'.$key; @endphp
                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-label fw-bold">{{ $meta['label'] }}</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" id="{{ $colorId }}_picker" value="{{ $themeHexValue($key, $meta['default']) }}" data-sync-target="{{ $colorId }}_text" title="اختر اللون">
                                                <input type="text" class="form-control" id="{{ $colorId }}_text" name="{{ $key }}[]" value="{{ $themeHexValue($key, $meta['default']) }}" placeholder="{{ $meta['default'] }}" pattern="^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$">
                                            </div>
                                        </div>
                                    @elseif ($meta['type'] === 'select')
                                        @php $shadowCurrent = $themeSettingValue($key, $meta['default']); @endphp
                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-label fw-bold">{{ $meta['label'] }}</label>
                                            <select class="form-control" name="{{ $key }}[]">
                                                <option value="soft" @selected($shadowCurrent === 'soft')>خفيف</option>
                                                <option value="medium" @selected($shadowCurrent === 'medium')>متوسط</option>
                                                <option value="strong" @selected($shadowCurrent === 'strong')>قوي</option>
                                            </select>
                                        </div>
                                    @else
                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-label fw-bold">{{ $meta['label'] }}</label>
                                            <input type="text" class="form-control" name="{{ $key }}[]" value="{{ $themeSettingValue($key, $meta['default']) }}" placeholder="{{ $meta['default'] }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="sm-actions">
                                <button type="submit" class="btn btn-primary">حفظ نظام الثيم</button>
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

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var colorPickers = document.querySelectorAll('#settings-app input[type="color"][data-sync-target]');
                colorPickers.forEach(function (picker) {
                    var targetId = picker.getAttribute('data-sync-target');
                    var targetInput = document.getElementById(targetId);
                    if (!targetInput) {
                        return;
                    }

                    picker.addEventListener('input', function () {
                        targetInput.value = picker.value;
                    });

                    targetInput.addEventListener('input', function () {
                        var value = (targetInput.value || '').trim();
                        if (/^#([a-fA-F0-9]{6})$/.test(value)) {
                            picker.value = value;
                        } else if (/^#([a-fA-F0-9]{3})$/.test(value)) {
                            var shortHex = value.substring(1);
                            picker.value = '#' + shortHex[0] + shortHex[0] + shortHex[1] + shortHex[1] + shortHex[2] + shortHex[2];
                        }
                    });
                });
            });
        </script>
    </div>
    <!-- Container-fluid Ends-->
@endsection
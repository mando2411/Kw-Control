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

            html.ui-modern #settings-app .sm-tabs,
            body.ui-modern #settings-app .sm-tabs {
                border-bottom: 1px solid var(--ui-border);
                margin-bottom: 14px;
                gap: 8px;
            }

            html.ui-modern #settings-app .sm-tabs .nav-link,
            body.ui-modern #settings-app .sm-tabs .nav-link {
                border: 1px solid var(--ui-border);
                border-bottom: none;
                background: var(--ui-surface-2);
                color: var(--ui-muted);
                border-radius: 12px 12px 0 0;
                font-weight: 800;
                padding: 10px 14px;
            }

            html.ui-modern #settings-app .sm-tabs .nav-link.active,
            body.ui-modern #settings-app .sm-tabs .nav-link.active {
                background: var(--ui-surface);
                color: var(--ui-ink);
                border-color: var(--ui-border);
            }

            html.ui-modern #settings-app .sm-tab-pane,
            body.ui-modern #settings-app .sm-tab-pane {
                display: none;
                gap: 14px;
            }

            html.ui-modern #settings-app .sm-tab-pane.active,
            html.ui-modern #settings-app .sm-tab-pane.show,
            body.ui-modern #settings-app .sm-tab-pane.active,
            body.ui-modern #settings-app .sm-tab-pane.show {
                display: grid;
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
                        \App\Enums\SettingKey::UI_MODERN_FS_XS->value => ['label' => 'حجم XS', 'default' => '0.75rem', 'options' => ['0.625rem', '0.75rem', '0.875rem', '1rem']],
                        \App\Enums\SettingKey::UI_MODERN_FS_SM->value => ['label' => 'حجم SM', 'default' => '0.875rem', 'options' => ['0.75rem', '0.875rem', '1rem', '1.125rem']],
                        \App\Enums\SettingKey::UI_MODERN_FS_BASE->value => ['label' => 'الحجم الأساسي', 'default' => '1rem', 'options' => ['0.875rem', '1rem', '1.125rem', '1.25rem']],
                        \App\Enums\SettingKey::UI_MODERN_FS_LG->value => ['label' => 'حجم LG', 'default' => '1.125rem', 'options' => ['1rem', '1.125rem', '1.25rem', '1.375rem']],
                        \App\Enums\SettingKey::UI_MODERN_FS_XL->value => ['label' => 'حجم XL', 'default' => '1.25rem', 'options' => ['1.125rem', '1.25rem', '1.5rem', '1.75rem']],
                    ];

                    $componentDefaults = [
                        \App\Enums\SettingKey::UI_MODERN_LINK_COLOR->value => ['label' => 'لون الروابط (Light)', 'default' => '#0ea5e9', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_LINK_COLOR->value => ['label' => 'لون الروابط (Dark)', 'default' => '#38bdf8', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_BORDER_COLOR->value => ['label' => 'لون الحدود (Light)', 'default' => '#dbe3ef', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_BORDER_COLOR->value => ['label' => 'لون الحدود (Dark)', 'default' => '#64748b', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_HOME_BOX_BG->value => ['label' => 'خلفية بوكسات الرئيسية (Light)', 'default' => '#f8fafc', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_HOME_BOX_BORDER->value => ['label' => 'حدود بوكسات الرئيسية (Light)', 'default' => '#dbe3ef', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_HOME_CARD_BG->value => ['label' => 'خلفية كروت الرئيسية (Light)', 'default' => '#ffffff', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_HOME_AVATAR_SIZE->value => ['label' => 'حجم صورة أفاتار كارت الرئيسية', 'default' => '62px', 'type' => 'size_select', 'options' => ['48px', '56px', '62px', '72px', '84px', '96px', '108px', '120px', '136px']],
                        \App\Enums\SettingKey::UI_MODERN_DARK_HOME_BOX_BG->value => ['label' => 'خلفية بوكسات الرئيسية (Dark)', 'default' => '#1e293b', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_HOME_BOX_BORDER->value => ['label' => 'حدود بوكسات الرئيسية (Dark)', 'default' => '#475569', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_DARK_HOME_CARD_BG->value => ['label' => 'خلفية كروت الرئيسية (Dark)', 'default' => '#0f172a', 'type' => 'color'],
                        \App\Enums\SettingKey::UI_MODERN_RADIUS_CARD->value => ['label' => 'استدارة الكروت', 'default' => '1rem', 'type' => 'size_select', 'options' => ['0.5rem', '0.75rem', '1rem', '1.25rem', '1.5rem']],
                        \App\Enums\SettingKey::UI_MODERN_RADIUS_INPUT->value => ['label' => 'استدارة المدخلات', 'default' => '0.75rem', 'type' => 'size_select', 'options' => ['0.375rem', '0.5rem', '0.75rem', '1rem']],
                        \App\Enums\SettingKey::UI_MODERN_RADIUS_BUTTON->value => ['label' => 'استدارة الأزرار', 'default' => '0.75rem', 'type' => 'size_select', 'options' => ['0.375rem', '0.5rem', '0.75rem', '1rem']],
                        \App\Enums\SettingKey::UI_MODERN_SPACE_SECTION->value => ['label' => 'تباعد الأقسام', 'default' => '1.25rem', 'type' => 'size_select', 'options' => ['0.75rem', '1rem', '1.25rem', '1.5rem', '2rem']],
                        \App\Enums\SettingKey::UI_MODERN_SPACE_CARD->value => ['label' => 'Padding الكروت', 'default' => '1rem', 'type' => 'size_select', 'options' => ['0.75rem', '1rem', '1.25rem', '1.5rem']],
                        \App\Enums\SettingKey::UI_MODERN_CONTAINER_MAX->value => ['label' => 'أقصى عرض للمحتوى', 'default' => '1320px', 'type' => 'size_select', 'options' => ['1140px', '1200px', '1320px', '1440px', '1600px']],
                        \App\Enums\SettingKey::UI_MODERN_SHADOW_LEVEL->value => ['label' => 'مستوى الظل', 'default' => 'medium', 'type' => 'select'],
                    ];

                    $themeDefaultValues = [];
                    foreach ($themeLightDefaults as $key => $meta) {
                        $themeDefaultValues[$key] = $meta['default'];
                    }
                    foreach ($themeDarkDefaults as $key => $meta) {
                        $themeDefaultValues[$key] = $meta['default'];
                    }
                    foreach ($fontDefaults as $key => $meta) {
                        $themeDefaultValues[$key] = $meta['default'];
                    }
                    foreach ($componentDefaults as $key => $meta) {
                        $themeDefaultValues[$key] = $meta['default'];
                    }

                    $customThemeValues = [];
                    foreach ($themeDefaultValues as $key => $default) {
                        $customThemeValues[$key] = $themeSettingValue($key, (string) $default);
                    }

                    $presetOverrides = [
                        'default' => [],
                        'emerald' => [
                            \App\Enums\SettingKey::UI_MODERN_BTN_PRIMARY->value => '#10b981',
                            \App\Enums\SettingKey::UI_MODERN_BTN_SECONDARY->value => '#0ea5a4',
                            \App\Enums\SettingKey::UI_MODERN_BTN_TERTIARY->value => '#22c55e',
                            \App\Enums\SettingKey::UI_MODERN_TEXT_PRIMARY->value => '#052e2b',
                            \App\Enums\SettingKey::UI_MODERN_TEXT_SECONDARY->value => '#0f766e',
                            \App\Enums\SettingKey::UI_MODERN_BG_SECONDARY->value => '#f0fdfa',
                            \App\Enums\SettingKey::UI_MODERN_DARK_BTN_PRIMARY->value => '#34d399',
                            \App\Enums\SettingKey::UI_MODERN_DARK_BTN_SECONDARY->value => '#2dd4bf',
                            \App\Enums\SettingKey::UI_MODERN_DARK_BTN_TERTIARY->value => '#4ade80',
                            \App\Enums\SettingKey::UI_MODERN_DARK_TEXT_PRIMARY->value => '#ecfeff',
                            \App\Enums\SettingKey::UI_MODERN_DARK_TEXT_SECONDARY->value => '#99f6e4',
                            \App\Enums\SettingKey::UI_MODERN_DARK_BG_PRIMARY->value => '#042f2e',
                            \App\Enums\SettingKey::UI_MODERN_DARK_BG_SECONDARY->value => '#134e4a',
                            \App\Enums\SettingKey::UI_MODERN_LINK_COLOR->value => '#0f766e',
                            \App\Enums\SettingKey::UI_MODERN_DARK_LINK_COLOR->value => '#2dd4bf',
                            \App\Enums\SettingKey::UI_MODERN_BORDER_COLOR->value => '#99f6e4',
                            \App\Enums\SettingKey::UI_MODERN_DARK_BORDER_COLOR->value => '#0f766e',
                            \App\Enums\SettingKey::UI_MODERN_HOME_BOX_BG->value => '#ecfdf5',
                            \App\Enums\SettingKey::UI_MODERN_HOME_BOX_BORDER->value => '#6ee7b7',
                            \App\Enums\SettingKey::UI_MODERN_DARK_HOME_BOX_BG->value => '#14532d',
                            \App\Enums\SettingKey::UI_MODERN_DARK_HOME_BOX_BORDER->value => '#22c55e',
                        ],
                        'violet' => [
                            \App\Enums\SettingKey::UI_MODERN_BTN_PRIMARY->value => '#8b5cf6',
                            \App\Enums\SettingKey::UI_MODERN_BTN_TERTIARY->value => '#ec4899',
                            \App\Enums\SettingKey::UI_MODERN_TEXT_PRIMARY->value => '#1f1147',
                            \App\Enums\SettingKey::UI_MODERN_TEXT_SECONDARY->value => '#5b21b6',
                            \App\Enums\SettingKey::UI_MODERN_BG_SECONDARY->value => '#f5f3ff',
                            \App\Enums\SettingKey::UI_MODERN_DARK_BTN_PRIMARY->value => '#a78bfa',
                            \App\Enums\SettingKey::UI_MODERN_DARK_BTN_TERTIARY->value => '#f472b6',
                            \App\Enums\SettingKey::UI_MODERN_DARK_TEXT_PRIMARY->value => '#f5f3ff',
                            \App\Enums\SettingKey::UI_MODERN_DARK_TEXT_SECONDARY->value => '#ddd6fe',
                            \App\Enums\SettingKey::UI_MODERN_DARK_BG_PRIMARY->value => '#1e1b4b',
                            \App\Enums\SettingKey::UI_MODERN_DARK_BG_SECONDARY->value => '#312e81',
                            \App\Enums\SettingKey::UI_MODERN_LINK_COLOR->value => '#7c3aed',
                            \App\Enums\SettingKey::UI_MODERN_DARK_LINK_COLOR->value => '#a78bfa',
                            \App\Enums\SettingKey::UI_MODERN_BORDER_COLOR->value => '#ddd6fe',
                            \App\Enums\SettingKey::UI_MODERN_DARK_BORDER_COLOR->value => '#7c3aed',
                            \App\Enums\SettingKey::UI_MODERN_HOME_BOX_BG->value => '#f5f3ff',
                            \App\Enums\SettingKey::UI_MODERN_HOME_BOX_BORDER->value => '#c4b5fd',
                            \App\Enums\SettingKey::UI_MODERN_DARK_HOME_BOX_BG->value => '#312e81',
                            \App\Enums\SettingKey::UI_MODERN_DARK_HOME_BOX_BORDER->value => '#8b5cf6',
                        ],
                    ];

                    $themePresetsForJs = [
                        'default' => ['name' => 'Default', 'values' => $themeDefaultValues],
                        'emerald' => ['name' => 'Emerald', 'values' => array_replace($themeDefaultValues, $presetOverrides['emerald'])],
                        'violet' => ['name' => 'Violet', 'values' => array_replace($themeDefaultValues, $presetOverrides['violet'])],
                        'custom' => ['name' => 'Custom', 'values' => $customThemeValues],
                    ];

                    $themeLibraryRaw = $settings->firstWhere('option_key', \App\Enums\SettingKey::UI_MODERN_THEME_LIBRARY->value)?->option_value[0] ?? '[]';
                    $themeLibraryDecoded = json_decode(is_string($themeLibraryRaw) ? $themeLibraryRaw : '[]', true);
                    $themeLibraryDecoded = is_array($themeLibraryDecoded) ? $themeLibraryDecoded : [];

                    $userThemes = [];
                    foreach ($themeLibraryDecoded as $themeItem) {
                        if (!is_array($themeItem)) {
                            continue;
                        }

                        $themeId = trim((string) ($themeItem['id'] ?? ''));
                        if ($themeId === '' || in_array($themeId, ['default', 'emerald', 'violet', 'custom'], true)) {
                            continue;
                        }

                        $themeName = trim((string) ($themeItem['name'] ?? $themeId));
                        $themeValues = is_array($themeItem['values'] ?? null)
                            ? array_replace($themeDefaultValues, $themeItem['values'])
                            : $themeDefaultValues;

                        $normalizedTheme = [
                            'id' => $themeId,
                            'name' => $themeName,
                            'values' => $themeValues,
                        ];

                        $userThemes[] = $normalizedTheme;
                        $themePresetsForJs[$themeId] = ['name' => $themeName, 'values' => $themeValues];
                    }

                    $validPresetIds = array_merge(['default', 'emerald', 'violet', 'custom'], array_map(fn ($theme) => $theme['id'], $userThemes));
                    $themePresetCurrent = in_array($themePresetCurrent, $validPresetIds, true) ? $themePresetCurrent : 'default';
                @endphp

                <ul class="nav nav-tabs sm-tabs" id="settings-modern-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-general-btn" data-tab-target="tab-general" type="button" role="tab" aria-controls="tab-general" aria-selected="true">إعدادات عامة</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-design-btn" data-tab-target="tab-design" type="button" role="tab" aria-controls="tab-design" aria-selected="false">التصميم والثيمات</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-results-btn" data-tab-target="tab-results" type="button" role="tab" aria-controls="tab-results" aria-selected="false">إعدادات النتائج</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-maintenance-btn" data-tab-target="tab-maintenance" type="button" role="tab" aria-controls="tab-maintenance" aria-selected="false">التهيئة والصيانة</button>
                    </li>
                </ul>

                <div class="tab-content" id="settings-modern-tabs-content">
                    <div class="tab-pane fade show active sm-tab-pane" id="tab-general" role="tabpanel" aria-labelledby="tab-general-btn">
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
                    </div>

                    <div class="tab-pane fade sm-tab-pane" id="tab-design" role="tabpanel" aria-labelledby="tab-design-btn">
                        <div class="sm-card mb-3">
                            <div class="sm-card-h">
                                <h5>نظام الثيمات</h5>
                                <p>اختر ثيم جاهز متناسق (Light + Dark) أو اختر Custom لتفعيل الإعدادات اليدوية بالأسفل.</p>
                            </div>
                            <div class="sm-card-b">
                                <form action="{{ route('dashboard.settings.update' ) }}" method="POST" id="theme-preset-form">
                                    @csrf
                                    @method('PUT')

                                    <label class="form-label fw-bold">الثيم الحالي</label>
                                    <select class="form-control" name="{{ \App\Enums\SettingKey::UI_MODERN_THEME_PRESET->value }}[]" id="ui_modern_theme_preset">
                                        <option value="default" @selected($themePresetCurrent === 'default')>Default (الثيم الأصلي)</option>
                                        <option value="emerald" @selected($themePresetCurrent === 'emerald')>Emerald (أخضر/تركواز متناسق)</option>
                                        <option value="violet" @selected($themePresetCurrent === 'violet')>Violet (بنفسجي/وردي متناسق)</option>
                                        <option value="custom" @selected($themePresetCurrent === 'custom')>Custom (يدوي)</option>
                                        @foreach ($userThemes as $theme)
                                            <option data-user-theme="1" value="{{ $theme['id'] }}" @selected($themePresetCurrent === $theme['id'])>{{ $theme['name'] }}</option>
                                        @endforeach
                                    </select>

                                    <input type="hidden" name="{{ \App\Enums\SettingKey::UI_MODERN_THEME_LIBRARY->value }}[]" id="ui_modern_theme_library_input" value="{{ e(json_encode($userThemes, JSON_UNESCAPED_UNICODE)) }}">

                                    <div class="mt-3">
                                        <label class="form-label fw-bold">اسم ثيم جديد</label>
                                        <input type="text" id="new_theme_name" class="form-control" placeholder="مثال: Blue Ocean">
                                    </div>

                                    <div class="sm-actions">
                                        <button type="submit" class="btn btn-primary">تطبيق الثيم</button>
                                        <button type="button" class="btn btn-secondary" id="add-theme-btn">إضافة كثيم</button>
                                        <button type="button" class="btn btn-danger" id="delete-theme-btn">حذف الثيم المحدد</button>
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
                                    اختَر اللون مباشرة من المربع، ويمكنك أيضًا تعديل كود اللون يدويًا. جميع الأحجام هنا أصبحت بخيارات جاهزة.
                                </div>

                                <form action="{{ route('dashboard.settings.update' ) }}" method="POST" id="theme-custom-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="{{ \App\Enums\SettingKey::UI_MODERN_THEME_PRESET->value }}[]" id="ui_modern_theme_preset_custom_hidden" value="{{ $themePresetCurrent }}">
                                    <input type="hidden" name="{{ \App\Enums\SettingKey::UI_MODERN_THEME_LIBRARY->value }}[]" id="ui_modern_theme_library_custom_hidden" value="{{ e(json_encode($userThemes, JSON_UNESCAPED_UNICODE)) }}">

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
                                                    <input type="text" class="form-control" id="{{ $colorId }}_text" name="{{ $key }}[]" data-theme-token="{{ $key }}" value="{{ $themeHexValue($key, $meta['default']) }}" placeholder="{{ $meta['default'] }}" pattern="^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$">
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
                                                    <input type="text" class="form-control" id="{{ $colorId }}_text" name="{{ $key }}[]" data-theme-token="{{ $key }}" value="{{ $themeHexValue($key, $meta['default']) }}" placeholder="{{ $meta['default'] }}" pattern="^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$">
                                                </div>
                                            </div>
                                        @endforeach

                                        <div class="col-12 mt-2">
                                            <h6 class="fw-bold mb-2">3) أحجام الخطوط</h6>
                                        </div>

                                        @foreach ($fontDefaults as $key => $meta)
                                            @php
                                                $fontCurrent = $themeSettingValue($key, $meta['default']);
                                                $fontOptions = $meta['options'] ?? [];
                                                if (!in_array($fontCurrent, $fontOptions, true)) {
                                                    $fontCurrent = $meta['default'];
                                                }
                                            @endphp
                                            <div class="col-md-6 col-lg-3">
                                                <label class="form-label fw-bold">{{ $meta['label'] }}</label>
                                                <select class="form-control" name="{{ $key }}[]" data-theme-token="{{ $key }}">
                                                    @foreach ($fontOptions as $fontOption)
                                                        <option value="{{ $fontOption }}" @selected($fontCurrent === $fontOption)>{{ $fontOption }}</option>
                                                    @endforeach
                                                </select>
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
                                                        <input type="text" class="form-control" id="{{ $colorId }}_text" name="{{ $key }}[]" data-theme-token="{{ $key }}" value="{{ $themeHexValue($key, $meta['default']) }}" placeholder="{{ $meta['default'] }}" pattern="^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$">
                                                    </div>
                                                </div>
                                            @elseif ($meta['type'] === 'select')
                                                @php $shadowCurrent = $themeSettingValue($key, $meta['default']); @endphp
                                                <div class="col-md-6 col-lg-3">
                                                    <label class="form-label fw-bold">{{ $meta['label'] }}</label>
                                                    <select class="form-control" name="{{ $key }}[]" data-theme-token="{{ $key }}">
                                                        <option value="soft" @selected($shadowCurrent === 'soft')>خفيف</option>
                                                        <option value="medium" @selected($shadowCurrent === 'medium')>متوسط</option>
                                                        <option value="strong" @selected($shadowCurrent === 'strong')>قوي</option>
                                                    </select>
                                                </div>
                                            @elseif ($meta['type'] === 'size_select')
                                                @php
                                                    $sizeCurrent = $themeSettingValue($key, $meta['default']);
                                                    $sizeOptions = $meta['options'] ?? [];
                                                    if (!in_array($sizeCurrent, $sizeOptions, true)) {
                                                        $sizeCurrent = $meta['default'];
                                                    }
                                                @endphp
                                                <div class="col-md-6 col-lg-3">
                                                    <label class="form-label fw-bold">{{ $meta['label'] }}</label>
                                                    <select class="form-control" name="{{ $key }}[]" data-theme-token="{{ $key }}">
                                                        @foreach ($sizeOptions as $sizeOption)
                                                            <option value="{{ $sizeOption }}" @selected($sizeCurrent === $sizeOption)>{{ $sizeOption }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <div class="col-md-6 col-lg-3">
                                                    <label class="form-label fw-bold">{{ $meta['label'] }}</label>
                                                    <input type="text" class="form-control" name="{{ $key }}[]" data-theme-token="{{ $key }}" value="{{ $themeSettingValue($key, $meta['default']) }}" placeholder="{{ $meta['default'] }}">
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
                    </div>

                    <div class="tab-pane fade sm-tab-pane" id="tab-results" role="tabpanel" aria-labelledby="tab-results-btn">
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
                    </div>

                    <div class="tab-pane fade sm-tab-pane" id="tab-maintenance" role="tabpanel" aria-labelledby="tab-maintenance-btn">
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

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var tabsRoot = document.getElementById('settings-modern-tabs');
                var tabsContent = document.getElementById('settings-modern-tabs-content');

                if (tabsRoot && tabsContent) {
                    var tabButtons = Array.prototype.slice.call(tabsRoot.querySelectorAll('[data-tab-target]'));
                    var tabPanes = Array.prototype.slice.call(tabsContent.querySelectorAll('.tab-pane'));

                    var activateTab = function (targetId) {
                        tabButtons.forEach(function (button) {
                            var isActive = button.getAttribute('data-tab-target') === targetId;
                            button.classList.toggle('active', isActive);
                            button.setAttribute('aria-selected', isActive ? 'true' : 'false');
                        });

                        tabPanes.forEach(function (pane) {
                            var isActive = pane.id === targetId;
                            pane.classList.toggle('active', isActive);
                            pane.classList.toggle('show', isActive);
                        });
                    };

                    tabButtons.forEach(function (button) {
                        button.addEventListener('click', function () {
                            var targetId = button.getAttribute('data-tab-target');
                            if (targetId) {
                                activateTab(targetId);
                            }
                        });
                    });

                    var initiallyActive = tabsRoot.querySelector('[data-tab-target].active');
                    activateTab(initiallyActive ? initiallyActive.getAttribute('data-tab-target') : 'tab-general');
                }

                var presetSelect = document.getElementById('ui_modern_theme_preset');
                var presetForm = document.getElementById('theme-preset-form');
                var customForm = document.getElementById('theme-custom-form');
                var addThemeBtn = document.getElementById('add-theme-btn');
                var deleteThemeBtn = document.getElementById('delete-theme-btn');
                var newThemeNameInput = document.getElementById('new_theme_name');
                var libraryPresetHidden = document.getElementById('ui_modern_theme_library_input');
                var libraryCustomHidden = document.getElementById('ui_modern_theme_library_custom_hidden');
                var presetCustomHidden = document.getElementById('ui_modern_theme_preset_custom_hidden');

                if (!presetSelect || !presetForm || !customForm || !libraryPresetHidden || !libraryCustomHidden || !presetCustomHidden) {
                    return;
                }

                var builtInPresetIds = ['default', 'emerald', 'violet', 'custom'];
                var presets = @json($themePresetsForJs);
                var defaults = (presets.default && presets.default.values) ? presets.default.values : {};
                var isApplyingPreset = false;

                var parseLibrary = function (raw) {
                    try {
                        var parsed = JSON.parse(raw || '[]');
                        return Array.isArray(parsed) ? parsed : [];
                    } catch (error) {
                        return [];
                    }
                };

                var userThemes = parseLibrary(libraryPresetHidden.value);

                var normalizeHex = function (value) {
                    var v = (value || '').trim();
                    if (/^#([a-fA-F0-9]{6})$/.test(v)) {
                        return v.toLowerCase();
                    }
                    if (/^#([a-fA-F0-9]{3})$/.test(v)) {
                        var s = v.substring(1).toLowerCase();
                        return '#' + s[0] + s[0] + s[1] + s[1] + s[2] + s[2];
                    }
                    return null;
                };

                var themeFieldElements = Array.prototype.slice.call(customForm.querySelectorAll('[data-theme-token]'));

                var snapshotValues = function () {
                    var snapshot = {};
                    themeFieldElements.forEach(function (field) {
                        var key = field.getAttribute('data-theme-token');
                        if (!key) {
                            return;
                        }
                        snapshot[key] = field.value;
                    });
                    return snapshot;
                };

                var setFieldValues = function (values) {
                    themeFieldElements.forEach(function (field) {
                        var key = field.getAttribute('data-theme-token');
                        if (!key) {
                            return;
                        }
                        var nextValue = (values && values[key] != null) ? values[key] : defaults[key];
                        if (nextValue != null) {
                            field.value = String(nextValue);
                        }
                    });
                    syncColorPickersFromText();
                };

                var findUserTheme = function (id) {
                    return userThemes.find(function (theme) {
                        return theme && theme.id === id;
                    }) || null;
                };

                var getPresetValues = function (presetId) {
                    if (presets[presetId] && presets[presetId].values) {
                        return presets[presetId].values;
                    }
                    var userTheme = findUserTheme(presetId);
                    if (userTheme && userTheme.values) {
                        return Object.assign({}, defaults, userTheme.values);
                    }
                    return defaults;
                };

                var syncHiddenLibrary = function () {
                    var payload = JSON.stringify(userThemes);
                    libraryPresetHidden.value = payload;
                    libraryCustomHidden.value = payload;
                };

                var renderUserThemeOptions = function () {
                    var current = presetSelect.value;
                    Array.prototype.slice.call(presetSelect.querySelectorAll('option[data-user-theme="1"]')).forEach(function (option) {
                        option.remove();
                    });

                    userThemes.forEach(function (theme) {
                        var option = document.createElement('option');
                        option.value = theme.id;
                        option.textContent = theme.name;
                        option.setAttribute('data-user-theme', '1');
                        presetSelect.appendChild(option);
                    });

                    if (presetSelect.querySelector('option[value="' + current + '"]')) {
                        presetSelect.value = current;
                    }
                };

                var applyPresetToCustom = function (presetId) {
                    isApplyingPreset = true;
                    setFieldValues(getPresetValues(presetId));
                    presetCustomHidden.value = presetId;
                    isApplyingPreset = false;
                };

                var toSlug = function (name) {
                    return (name || '')
                        .toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '') || 'theme';
                };

                var uniqueThemeId = function (baseId) {
                    var id = baseId;
                    var counter = 2;
                    var exists = function (value) {
                        if (builtInPresetIds.indexOf(value) !== -1) {
                            return true;
                        }
                        return userThemes.some(function (theme) { return theme.id === value; });
                    };
                    while (exists(id)) {
                        id = baseId + '-' + counter;
                        counter += 1;
                    }
                    return id;
                };

                var markAsCustom = function () {
                    if (isApplyingPreset) {
                        return;
                    }
                    presetSelect.value = 'custom';
                    presetCustomHidden.value = 'custom';
                };

                var syncColorPickersFromText = function () {
                    var colorPickers = document.querySelectorAll('#settings-app input[type="color"][data-sync-target]');
                    colorPickers.forEach(function (picker) {
                        var targetId = picker.getAttribute('data-sync-target');
                        var targetInput = document.getElementById(targetId);
                        if (!targetInput) {
                            return;
                        }
                        var normalized = normalizeHex(targetInput.value);
                        if (normalized) {
                            picker.value = normalized;
                        }
                    });
                };

                document.querySelectorAll('#settings-app input[type="color"][data-sync-target]').forEach(function (picker) {
                    var targetId = picker.getAttribute('data-sync-target');
                    var targetInput = document.getElementById(targetId);
                    if (!targetInput) {
                        return;
                    }

                    picker.addEventListener('input', function () {
                        targetInput.value = picker.value;
                        markAsCustom();
                    });

                    targetInput.addEventListener('input', function () {
                        var normalized = normalizeHex(targetInput.value);
                        if (normalized) {
                            picker.value = normalized;
                        }
                        markAsCustom();
                    });
                });

                themeFieldElements.forEach(function (field) {
                    field.addEventListener('change', markAsCustom);
                    field.addEventListener('input', markAsCustom);
                });

                presetSelect.addEventListener('change', function () {
                    applyPresetToCustom(presetSelect.value);
                });

                addThemeBtn.addEventListener('click', function () {
                    var themeName = (newThemeNameInput.value || '').trim();
                    if (!themeName) {
                        alert('اكتب اسم الثيم أولاً.');
                        return;
                    }

                    var newId = uniqueThemeId(toSlug(themeName));
                    var values = snapshotValues();
                    var themeObject = { id: newId, name: themeName, values: values };

                    userThemes.push(themeObject);
                    presets[newId] = { name: themeName, values: Object.assign({}, defaults, values) };
                    syncHiddenLibrary();
                    renderUserThemeOptions();

                    presetSelect.value = newId;
                    presetCustomHidden.value = newId;
                    newThemeNameInput.value = '';
                    presetForm.submit();
                });

                deleteThemeBtn.addEventListener('click', function () {
                    var selected = presetSelect.value;
                    if (builtInPresetIds.indexOf(selected) !== -1) {
                        alert('لا يمكن حذف الثيمات الأساسية.');
                        return;
                    }

                    var before = userThemes.length;
                    userThemes = userThemes.filter(function (theme) {
                        return theme.id !== selected;
                    });

                    if (userThemes.length === before) {
                        return;
                    }

                    delete presets[selected];
                    syncHiddenLibrary();
                    renderUserThemeOptions();
                    presetSelect.value = 'default';
                    presetCustomHidden.value = 'default';
                    presetForm.submit();
                });

                syncHiddenLibrary();
                renderUserThemeOptions();
                applyPresetToCustom(presetSelect.value || 'default');
            });
        </script>
    </div>
    <!-- Container-fluid Ends-->
@endsection
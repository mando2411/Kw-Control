<!DOCTYPE html>
@php
    $uiPolicy = setting(\App\Enums\SettingKey::UI_MODE_POLICY->value, true) ?: 'user_choice';
    $uiPolicy = in_array($uiPolicy, ['user_choice', 'modern', 'classic'], true) ? $uiPolicy : 'user_choice';

    $uiModeServer = auth()->check() ? (auth()->user()->ui_mode ?? 'classic') : null;
    $uiModeServer = in_array($uiModeServer, ['classic', 'modern'], true) ? $uiModeServer : 'classic';
    if ($uiPolicy === 'modern' || $uiPolicy === 'classic') {
        $uiModeServer = $uiPolicy;
    }
    $isHomepage = request()->routeIs('dashboard');

    $loadModernThemeCss = $uiModeServer === 'modern';
    $loadSelect2Assets = request()->routeIs('dashboard.settings.*', 'dashboard.statement.*', 'dashboard.voters.*', 'dashboard.contractors.*');
    $loadAceAssets = request()->routeIs('dashboard.settings.*');
    $loadTagsInputAssets = request()->routeIs('dashboard.settings.*');

    $themeValue = static function (\App\Enums\SettingKey $key, string $fallback): string {
        $value = trim((string) setting($key->value, true));
        return $value !== '' ? $value : $fallback;
    };

    $sanitizeColor = static function (string $value, string $fallback): string {
        $normalized = trim($value);
        if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $normalized)) {
            return $normalized;
        }

        if (preg_match('/^rgba?\((?:\s*\d+\s*,){2,3}\s*(?:\d+|\d*\.\d+)\s*\)$/', $normalized)) {
            return $normalized;
        }

        return $fallback;
    };

    $sanitizeSize = static function (string $value, string $fallback): string {
        $normalized = trim($value);
        return preg_match('/^\d+(?:\.\d+)?(?:px|rem|em)$/', $normalized) ? $normalized : $fallback;
    };

    $sanitizeLengthOrPercent = static function (string $value, string $fallback): string {
        $normalized = trim($value);
        return preg_match('/^\d+(?:\.\d+)?(?:px|rem|em|%)$/', $normalized) ? $normalized : $fallback;
    };

    $themeLibraryRaw = setting(\App\Enums\SettingKey::UI_MODERN_THEME_LIBRARY->value, true);
    if (!is_string($themeLibraryRaw) || trim($themeLibraryRaw) === '') {
        $themeLibraryRaw = '[]';
    }
    $themeLibraryDecoded = json_decode($themeLibraryRaw, true);
    $themeLibraryDecoded = is_array($themeLibraryDecoded) ? $themeLibraryDecoded : [];

    $themeLibraryById = [];
    foreach ($themeLibraryDecoded as $themeItem) {
        if (!is_array($themeItem)) {
            continue;
        }

        $id = strtolower(trim((string) ($themeItem['id'] ?? '')));
        if ($id === '' || in_array($id, ['default', 'emerald', 'violet', 'custom'], true)) {
            continue;
        }
        if (!preg_match('/^[a-z0-9][a-z0-9-]{0,63}$/', $id)) {
            continue;
        }

        $values = $themeItem['values'] ?? null;
        if (!is_array($values)) {
            continue;
        }

        $themeLibraryById[$id] = [
            'id' => $id,
            'name' => trim((string) ($themeItem['name'] ?? $id)),
            'values' => $values,
        ];
    }

    $themePreset = strtolower(trim((string) setting(\App\Enums\SettingKey::UI_MODERN_THEME_PRESET->value, true)));
    $allowedPresets = array_merge(['default', 'emerald', 'violet', 'custom'], array_keys($themeLibraryById));
    $themePreset = in_array($themePreset, $allowedPresets, true) ? $themePreset : 'default';

    $baseFontPreset = [
        'xs' => '0.75rem',
        'sm' => '0.875rem',
        'base' => '1rem',
        'lg' => '1.125rem',
        'xl' => '1.25rem',
    ];

    $themePresetPalettes = [
        'default' => [
            'light' => [
                'btn_primary' => '#0ea5e9',
                'btn_secondary' => '#6366f1',
                'btn_tertiary' => '#14b8a6',
                'btn_quaternary' => '#f59e0b',
                'text_primary' => '#0f172a',
                'text_secondary' => '#475569',
                'bg_primary' => '#ffffff',
                'bg_secondary' => '#f8fafc',
            ],
            'dark' => [
                'btn_primary' => '#38bdf8',
                'btn_secondary' => '#818cf8',
                'btn_tertiary' => '#2dd4bf',
                'btn_quaternary' => '#fbbf24',
                'text_primary' => '#f1f5f9',
                'text_secondary' => '#cbd5e1',
                'bg_primary' => '#0f172a',
                'bg_secondary' => '#1e293b',
            ],
            'surface' => [
                'link_light' => '#0ea5e9',
                'link_dark' => '#38bdf8',
                'border_light' => '#dbe3ef',
                'border_dark' => '#64748b',
                'radius_card' => '1rem',
                'radius_input' => '0.75rem',
                'radius_button' => '0.75rem',
                'space_section' => '1.25rem',
                'space_card' => '1rem',
                'container_max' => '1320px',
                'shadow_level' => 'medium',
                'home_box_bg_light' => '#f8fafc',
                'home_box_border_light' => '#dbe3ef',
                'home_card_bg_light' => '#ffffff',
                'home_avatar_size' => '62px',
                'home_box_bg_dark' => '#1e293b',
                'home_box_border_dark' => '#475569',
                'home_card_bg_dark' => '#0f172a',
            ],
            'font' => $baseFontPreset,
        ],
        'emerald' => [
            'light' => [
                'btn_primary' => '#10b981',
                'btn_secondary' => '#0ea5a4',
                'btn_tertiary' => '#22c55e',
                'btn_quaternary' => '#f59e0b',
                'text_primary' => '#052e2b',
                'text_secondary' => '#0f766e',
                'bg_primary' => '#ffffff',
                'bg_secondary' => '#f0fdfa',
            ],
            'dark' => [
                'btn_primary' => '#34d399',
                'btn_secondary' => '#2dd4bf',
                'btn_tertiary' => '#4ade80',
                'btn_quaternary' => '#fbbf24',
                'text_primary' => '#ecfeff',
                'text_secondary' => '#99f6e4',
                'bg_primary' => '#042f2e',
                'bg_secondary' => '#134e4a',
            ],
            'surface' => [
                'link_light' => '#0f766e',
                'link_dark' => '#2dd4bf',
                'border_light' => '#99f6e4',
                'border_dark' => '#0f766e',
                'radius_card' => '1rem',
                'radius_input' => '0.75rem',
                'radius_button' => '0.75rem',
                'space_section' => '1.25rem',
                'space_card' => '1rem',
                'container_max' => '1320px',
                'shadow_level' => 'medium',
                'home_box_bg_light' => '#ecfdf5',
                'home_box_border_light' => '#6ee7b7',
                'home_card_bg_light' => '#f0fdfa',
                'home_avatar_size' => '62px',
                'home_box_bg_dark' => '#14532d',
                'home_box_border_dark' => '#22c55e',
                'home_card_bg_dark' => '#042f2e',
            ],
            'font' => $baseFontPreset,
        ],
        'violet' => [
            'light' => [
                'btn_primary' => '#8b5cf6',
                'btn_secondary' => '#6366f1',
                'btn_tertiary' => '#ec4899',
                'btn_quaternary' => '#f59e0b',
                'text_primary' => '#1f1147',
                'text_secondary' => '#5b21b6',
                'bg_primary' => '#ffffff',
                'bg_secondary' => '#f5f3ff',
            ],
            'dark' => [
                'btn_primary' => '#a78bfa',
                'btn_secondary' => '#818cf8',
                'btn_tertiary' => '#f472b6',
                'btn_quaternary' => '#fbbf24',
                'text_primary' => '#f5f3ff',
                'text_secondary' => '#ddd6fe',
                'bg_primary' => '#1e1b4b',
                'bg_secondary' => '#312e81',
            ],
            'surface' => [
                'link_light' => '#7c3aed',
                'link_dark' => '#a78bfa',
                'border_light' => '#ddd6fe',
                'border_dark' => '#7c3aed',
                'radius_card' => '1rem',
                'radius_input' => '0.75rem',
                'radius_button' => '0.75rem',
                'space_section' => '1.25rem',
                'space_card' => '1rem',
                'container_max' => '1320px',
                'shadow_level' => 'medium',
                'home_box_bg_light' => '#f5f3ff',
                'home_box_border_light' => '#c4b5fd',
                'home_card_bg_light' => '#ffffff',
                'home_avatar_size' => '62px',
                'home_box_bg_dark' => '#312e81',
                'home_box_border_dark' => '#8b5cf6',
                'home_card_bg_dark' => '#1e1b4b',
            ],
            'font' => $baseFontPreset,
        ],
    ];

    $activePreset = $themePresetPalettes[array_key_exists($themePreset, $themePresetPalettes) ? $themePreset : 'default'];
    $selectedLibraryTheme = $themeLibraryById[$themePreset] ?? null;

    $resolveThemeValue = static function (\App\Enums\SettingKey $key, string $presetValue, callable $sanitize) use ($themePreset, $themeValue, $selectedLibraryTheme) {
        $value = $presetValue;

        if ($themePreset === 'custom') {
            $value = $themeValue($key, $presetValue);
        } elseif (is_array($selectedLibraryTheme) && is_array($selectedLibraryTheme['values'] ?? null)) {
            $value = (string) ($selectedLibraryTheme['values'][$key->value] ?? $presetValue);
        }

        return $sanitize($value, $presetValue);
    };

    $uiThemeLight = [
        'btn_primary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_BTN_PRIMARY, $activePreset['light']['btn_primary'], $sanitizeColor),
        'btn_secondary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_BTN_SECONDARY, $activePreset['light']['btn_secondary'], $sanitizeColor),
        'btn_tertiary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_BTN_TERTIARY, $activePreset['light']['btn_tertiary'], $sanitizeColor),
        'btn_quaternary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_BTN_QUATERNARY, $activePreset['light']['btn_quaternary'], $sanitizeColor),
        'text_primary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_TEXT_PRIMARY, $activePreset['light']['text_primary'], $sanitizeColor),
        'text_secondary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_TEXT_SECONDARY, $activePreset['light']['text_secondary'], $sanitizeColor),
        'bg_primary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_BG_PRIMARY, $activePreset['light']['bg_primary'], $sanitizeColor),
        'bg_secondary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_BG_SECONDARY, $activePreset['light']['bg_secondary'], $sanitizeColor),
    ];

    $uiThemeDark = [
        'btn_primary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_DARK_BTN_PRIMARY, $activePreset['dark']['btn_primary'], $sanitizeColor),
        'btn_secondary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_DARK_BTN_SECONDARY, $activePreset['dark']['btn_secondary'], $sanitizeColor),
        'btn_tertiary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_DARK_BTN_TERTIARY, $activePreset['dark']['btn_tertiary'], $sanitizeColor),
        'btn_quaternary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_DARK_BTN_QUATERNARY, $activePreset['dark']['btn_quaternary'], $sanitizeColor),
        'text_primary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_DARK_TEXT_PRIMARY, $activePreset['dark']['text_primary'], $sanitizeColor),
        'text_secondary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_DARK_TEXT_SECONDARY, $activePreset['dark']['text_secondary'], $sanitizeColor),
        'bg_primary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_DARK_BG_PRIMARY, $activePreset['dark']['bg_primary'], $sanitizeColor),
        'bg_secondary' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_DARK_BG_SECONDARY, $activePreset['dark']['bg_secondary'], $sanitizeColor),
    ];

    $uiFontScale = [
        'xs' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_FS_XS, $activePreset['font']['xs'], $sanitizeSize),
        'sm' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_FS_SM, $activePreset['font']['sm'], $sanitizeSize),
        'base' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_FS_BASE, $activePreset['font']['base'], $sanitizeSize),
        'lg' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_FS_LG, $activePreset['font']['lg'], $sanitizeSize),
        'xl' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_FS_XL, $activePreset['font']['xl'], $sanitizeSize),
    ];

    $uiShadowMap = [
        'soft' => '0 14px 28px rgba(2, 6, 23, 0.10)',
        'medium' => '0 20px 45px rgba(2, 6, 23, 0.14)',
        'strong' => '0 28px 65px rgba(2, 6, 23, 0.20)',
    ];

    $uiShadowLevel = $resolveThemeValue(
        \App\Enums\SettingKey::UI_MODERN_SHADOW_LEVEL,
        $activePreset['surface']['shadow_level'],
        static fn (string $value, string $fallback): string => in_array($value, ['soft', 'medium', 'strong'], true) ? $value : $fallback
    );
    $uiShadowLevel = array_key_exists($uiShadowLevel, $uiShadowMap) ? $uiShadowLevel : 'medium';

    $uiModernSurface = [
        'link_light' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_LINK_COLOR, $activePreset['surface']['link_light'], $sanitizeColor),
        'link_dark' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_DARK_LINK_COLOR, $activePreset['surface']['link_dark'], $sanitizeColor),
        'border_light' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_BORDER_COLOR, $activePreset['surface']['border_light'], $sanitizeColor),
        'border_dark' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_DARK_BORDER_COLOR, $activePreset['surface']['border_dark'], $sanitizeColor),
        'radius_card' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_RADIUS_CARD, $activePreset['surface']['radius_card'], $sanitizeLengthOrPercent),
        'radius_input' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_RADIUS_INPUT, $activePreset['surface']['radius_input'], $sanitizeLengthOrPercent),
        'radius_button' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_RADIUS_BUTTON, $activePreset['surface']['radius_button'], $sanitizeLengthOrPercent),
        'space_section' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_SPACE_SECTION, $activePreset['surface']['space_section'], $sanitizeLengthOrPercent),
        'space_card' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_SPACE_CARD, $activePreset['surface']['space_card'], $sanitizeLengthOrPercent),
        'container_max' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_CONTAINER_MAX, $activePreset['surface']['container_max'], $sanitizeLengthOrPercent),
        'home_box_bg_light' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_HOME_BOX_BG, $activePreset['surface']['home_box_bg_light'], $sanitizeColor),
        'home_box_border_light' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_HOME_BOX_BORDER, $activePreset['surface']['home_box_border_light'], $sanitizeColor),
        'home_card_bg_light' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_HOME_CARD_BG, $activePreset['surface']['home_card_bg_light'], $sanitizeColor),
        'home_avatar_size' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_HOME_AVATAR_SIZE, $activePreset['surface']['home_avatar_size'], $sanitizeLengthOrPercent),
        'home_box_bg_dark' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_DARK_HOME_BOX_BG, $activePreset['surface']['home_box_bg_dark'], $sanitizeColor),
        'home_box_border_dark' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_DARK_HOME_BOX_BORDER, $activePreset['surface']['home_box_border_dark'], $sanitizeColor),
        'home_card_bg_dark' => $resolveThemeValue(\App\Enums\SettingKey::UI_MODERN_DARK_HOME_CARD_BG, $activePreset['surface']['home_card_bg_dark'], $sanitizeColor),
        'shadow' => $uiShadowMap[$uiShadowLevel],
    ];
@endphp

<html lang="{{ app()->getLocale() }}" class="ui-{{ $uiModeServer }}" data-ui-mode="{{ $uiModeServer }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="كنترول منصة عربية متكاملة لإدارة العمليات والنتائج والتقارير بكفاءة ووضوح.">
    <meta name="keywords" content="كنترول, لوحة تحكم, إدارة النتائج, إدارة المستخدمين, تقارير">
    <meta name="author" content="Ahmed Nasr">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        // UI mode bootstrap (runs before paint): prevents flicker and syncs localStorage
        (function () {
            var SERVER_MODE = {!! json_encode($uiModeServer) !!};
            var UI_POLICY = {!! json_encode($uiPolicy) !!};
            var IS_AUTH = {{ auth()->check() ? 'true' : 'false' }};
            var IS_HOMEPAGE = {{ $isHomepage ? 'true' : 'false' }};
            var STORAGE_KEY = 'ui_mode';
            var PENDING_KEY = 'ui_mode_pending';
            var COLOR_STORAGE_KEY = 'ui_color_mode';
            var mode = SERVER_MODE || 'classic';
            var colorMode = 'light';
            var pending = null;
            var IS_FORCED_POLICY = (UI_POLICY === 'modern' || UI_POLICY === 'classic');

            // Global UI policy can force the mode regardless of stored user preference
            if (IS_FORCED_POLICY) {
                mode = UI_POLICY;
                pending = null;
                try {
                    localStorage.setItem(STORAGE_KEY, mode);
                    localStorage.removeItem(PENDING_KEY);
                } catch (e) {
                    // ignore
                }
            }

            try {
                if (!IS_FORCED_POLICY) {
                    pending = localStorage.getItem(PENDING_KEY);
                    var local = localStorage.getItem(STORAGE_KEY);
                    if (pending && (local === 'classic' || local === 'modern')) {
                        mode = local;
                    }
                }

                var storedColor = localStorage.getItem(COLOR_STORAGE_KEY);
                if (storedColor === 'dark' || storedColor === 'light') {
                    colorMode = storedColor;
                }
            } catch (e) {
                // ignore
            }

            if (mode !== 'modern') {
                colorMode = 'light';
            }

            // Apply early to <html> (and later we also set on <body>)
            var root = document.documentElement;
            root.classList.remove('ui-modern', 'ui-classic');
            root.classList.add(mode === 'modern' ? 'ui-modern' : 'ui-classic');
            root.setAttribute('data-ui-mode', mode);
            root.classList.remove('ui-dark', 'ui-light');
            root.classList.add(colorMode === 'dark' ? 'ui-dark' : 'ui-light');
            root.setAttribute('data-ui-color-mode', colorMode);
            root.setAttribute('data-bs-theme', (mode === 'modern' && colorMode === 'dark') ? 'dark' : 'light');

            window.__UI_MODE_SERVER__ = SERVER_MODE;
            window.__UI_MODE_EFFECTIVE__ = mode;
            window.__UI_MODE_POLICY__ = UI_POLICY;
            window.__UI_MODE_IS_AUTH__ = IS_AUTH;
            window.__UI_MODE_IS_HOMEPAGE__ = IS_HOMEPAGE;
            window.__UI_COLOR_MODE_EFFECTIVE__ = colorMode;

            // Preload homepage modern css (fetch only, no apply)
            if (IS_HOMEPAGE) {
                var preloadId = 'homeModernCssPreload';
                if (!document.getElementById(preloadId)) {
                    var preload = document.createElement('link');
                    preload.id = preloadId;
                    preload.rel = 'preload';
                    preload.as = 'style';
                    preload.href = "{{ asset('assets/css/home-modern.css') . '?v=' . filemtime(public_path('assets/css/home-modern.css')) }}";
                    document.head.appendChild(preload);
                }

                // If mode is modern, apply stylesheet early to avoid white flash
                if (mode === 'modern') {
                    var cssId = 'homeModernCss';
                    if (!document.getElementById(cssId)) {
                        var link = document.createElement('link');
                        link.id = cssId;
                        link.rel = 'stylesheet';
                        link.href = "{{ asset('assets/css/home-modern.css') . '?v=' . filemtime(public_path('assets/css/home-modern.css')) }}";
                        document.head.appendChild(link);
                    }
                }
            }

            // Keep localStorage in sync for instant loads
            try {
                localStorage.setItem(STORAGE_KEY, mode);
                localStorage.setItem(COLOR_STORAGE_KEY, colorMode);
            } catch (e) {
                // ignore
            }
        })();

        // Keep a loading mask visible right after login until dashboard is ready
        (function () {
            try {
                var showPostLoginLoading = sessionStorage.getItem('post_login_loading') === '1';
                if (showPostLoginLoading) {
                    document.documentElement.classList.add('post-login-loading');
                }
            } catch (e) {
                // ignore
            }
        })();
    </script>
    {{--    <link rel="icon" href="assets/images/dashboard/favicon.png" type="image/x-icon"> --}}
    {{--    <link rel="shortcut icon" href="assets/images/dashboard/favicon.png" type="image/x-icon"> --}}
    <title>{{ setting(\App\Enums\SettingKey::SITE_TITLE->value, true) ?: config('app.name', 'كنترول') }} | لوحة التحكم</title>

    <!-- Google font-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,500;1,600;1,700;1,800;1,900&display=swap">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">
    @if ($loadSelect2Assets)
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endif
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/admin.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/all.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/style.css') }}">
    @if ($loadModernThemeCss)
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/modern-theme-system.css') }}?v={{ filemtime(public_path('assets/css/modern-theme-system.css')) }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/dashboard-modern-fallback.css') }}?v={{ filemtime(public_path('assets/css/dashboard-modern-fallback.css')) }}">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .logo {
            width: 150px;
            max-height: 150px;
        }
        .modal-body .select2-container{
            width: 100% !important;
        }
        .input-group select.dropdown-toggle{
            background: var(--theme-color);
            border: unset;
            padding-left: 10px;
            font-weight: bold;
        }
        .light .input-group select.dropdown-toggle {
            color: #fff;
        }
        .dark .accordion-button.collapsed {
            color: #fff !important;
            border: 1px solid #fff;
        }

        #postLoginLoadingMask {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(2, 6, 23, 0.96), rgba(15, 23, 42, 0.94) 45%, rgba(2, 6, 23, 0.98));
            color: #e2e8f0;
        }

        html.post-login-loading #postLoginLoadingMask {
            display: flex;
        }

        .post-login-loading__content {
            text-align: center;
            padding: 1.2rem 1.4rem;
        }

        .post-login-loading__spinner {
            width: 56px;
            height: 56px;
            margin: 0 auto 0.95rem;
            border-radius: 999px;
            border: 3px solid rgba(226, 232, 240, 0.2);
            border-top-color: rgba(14, 165, 233, 0.95);
            animation: postLoginSpin 0.9s linear infinite;
        }

        .post-login-loading__text {
            font-weight: 800;
            font-size: 1rem;
            letter-spacing: 0.2px;
        }

        @keyframes postLoginSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* === UI Modern Palette (single source of truth) === */
        html.ui-modern,
        body.ui-modern {
            --ui-ink: {{ $uiThemeLight['text_primary'] }};
            --ui-muted: {{ $uiThemeLight['text_secondary'] }};
            --ui-surface: {{ $uiThemeLight['bg_primary'] }};
            --ui-surface-2: {{ $uiThemeLight['bg_secondary'] }};
            --ui-border: {{ $uiModernSurface['border_light'] }};
            --ui-shadow: {{ $uiModernSurface['shadow'] }};

            --ui-accent: {{ $uiThemeLight['btn_primary'] }};
            --ui-accent-soft: color-mix(in srgb, {{ $uiThemeLight['btn_primary'] }} 14%, transparent);
            --ui-warm: {{ $uiThemeLight['btn_quaternary'] }};
            --ui-warm-soft: color-mix(in srgb, {{ $uiThemeLight['btn_quaternary'] }} 12%, transparent);
            --ui-success: rgba(25, 135, 84, 0.95);
            --ui-danger: rgba(220, 53, 69, 0.95);

            --ui-btn-primary: {{ $uiThemeLight['btn_primary'] }};
            --ui-btn-secondary: {{ $uiThemeLight['btn_secondary'] }};
            --ui-btn-tertiary: {{ $uiThemeLight['btn_tertiary'] }};
            --ui-btn-quaternary: {{ $uiThemeLight['btn_quaternary'] }};
            --ui-btn-ink: #ffffff;

            --ui-text-primary: {{ $uiThemeLight['text_primary'] }};
            --ui-text-secondary: {{ $uiThemeLight['text_secondary'] }};

            --ui-bg-primary: {{ $uiThemeLight['bg_primary'] }};
            --ui-bg-secondary: {{ $uiThemeLight['bg_secondary'] }};

            --ui-link: {{ $uiModernSurface['link_light'] }};
            --ui-home-box-bg: {{ $uiModernSurface['home_box_bg_light'] }};
            --ui-home-box-border: {{ $uiModernSurface['home_box_border_light'] }};
            --ui-home-card-bg: {{ $uiModernSurface['home_card_bg_light'] }};
            --ui-home-avatar-size: {{ $uiModernSurface['home_avatar_size'] }};
            --ui-radius-card: {{ $uiModernSurface['radius_card'] }};
            --ui-radius-input: {{ $uiModernSurface['radius_input'] }};
            --ui-radius-button: {{ $uiModernSurface['radius_button'] }};
            --ui-space-section: {{ $uiModernSurface['space_section'] }};
            --ui-space-card: {{ $uiModernSurface['space_card'] }};
            --ui-container-max: {{ $uiModernSurface['container_max'] }};

            --ui-fs-xs: {{ $uiFontScale['xs'] }};
            --ui-fs-sm: {{ $uiFontScale['sm'] }};
            --ui-fs-base: {{ $uiFontScale['base'] }};
            --ui-fs-lg: {{ $uiFontScale['lg'] }};
            --ui-fs-xl: {{ $uiFontScale['xl'] }};

            --ui-heading-h1: calc(var(--ui-fs-xl) * 1.45);
            --ui-heading-h2: calc(var(--ui-fs-xl) * 1.20);
            --ui-heading-h3: calc(var(--ui-fs-xl) * 1.05);
            --ui-heading-h4: var(--ui-fs-xl);
            --ui-heading-h5: var(--ui-fs-lg);
            --ui-heading-h6: var(--ui-fs-base);

            --ui-dark-1: rgba(2, 6, 23, 0.94);
            --ui-dark-2: rgba(15, 23, 42, 0.88);
            --ui-dark-border: rgba(255, 255, 255, 0.10);
        }

        html.ui-modern.ui-dark,
        body.ui-modern.ui-dark {
            --ui-ink: {{ $uiThemeDark['text_primary'] }};
            --ui-muted: {{ $uiThemeDark['text_secondary'] }};
            --ui-surface: {{ $uiThemeDark['bg_primary'] }};
            --ui-surface-2: {{ $uiThemeDark['bg_secondary'] }};
            --ui-border: {{ $uiModernSurface['border_dark'] }};
            --ui-shadow: {{ $uiModernSurface['shadow'] }};
            --ui-accent: {{ $uiThemeDark['btn_primary'] }};
            --ui-accent-soft: color-mix(in srgb, {{ $uiThemeDark['btn_primary'] }} 16%, transparent);
            --ui-warm: {{ $uiThemeDark['btn_quaternary'] }};
            --ui-warm-soft: color-mix(in srgb, {{ $uiThemeDark['btn_quaternary'] }} 14%, transparent);
            --ui-success: rgba(74, 222, 128, 0.95);
            --ui-danger: rgba(248, 113, 113, 0.95);

            --ui-btn-primary: {{ $uiThemeDark['btn_primary'] }};
            --ui-btn-secondary: {{ $uiThemeDark['btn_secondary'] }};
            --ui-btn-tertiary: {{ $uiThemeDark['btn_tertiary'] }};
            --ui-btn-quaternary: {{ $uiThemeDark['btn_quaternary'] }};
            --ui-btn-ink: #ffffff;

            --ui-text-primary: {{ $uiThemeDark['text_primary'] }};
            --ui-text-secondary: {{ $uiThemeDark['text_secondary'] }};

            --ui-bg-primary: {{ $uiThemeDark['bg_primary'] }};
            --ui-bg-secondary: {{ $uiThemeDark['bg_secondary'] }};

            --ui-link: {{ $uiModernSurface['link_dark'] }};
            --ui-home-box-bg: {{ $uiModernSurface['home_box_bg_dark'] }};
            --ui-home-box-border: {{ $uiModernSurface['home_box_border_dark'] }};
            --ui-home-card-bg: {{ $uiModernSurface['home_card_bg_dark'] }};
            color-scheme: dark;
        }

        html.ui-modern.ui-dark,
        body.ui-modern.ui-dark,
        html.ui-modern.ui-dark .page-body,
        body.ui-modern.ui-dark .page-body {
            background: radial-gradient(circle at 12% 0%, rgba(56, 189, 248, 0.14), transparent 35%), radial-gradient(circle at 88% 0%, rgba(251, 191, 36, 0.10), transparent 38%), linear-gradient(180deg, #020617 0%, #0f172a 100%);
            color: var(--ui-ink);
        }

        /* Modern sidebar toggle (UI mode) */
        .sidebar-toggle-modern {
            display: none;
        }

        /* Modern-only components must stay hidden by default (especially in classic mode) */
        .dashboard-topbar-mobile,
        .dashboard-mobilebar,
        .hm-sidebar-toggle--mobile,
        .dashboard-topbar-mobile .dtm-left,
        .dashboard-topbar-mobile .dtm-center,
        .dashboard-topbar-mobile .dtm-actions,
        .dashboard-topbar-mobile .dtm-user,
        .dashboard-topbar-mobile .dtm-notif,
        .dashboard-topbar-mobile .dtm-notif-badge,
        .dashboard-topbar-mobile .dtm-notif-panel,
        .dashboard-topbar-mobile .dtm-logout,
        .dashboard-topbar-mobile .dtm-avatar {
            display: none;
        }

        html.ui-modern .sidebar-toggle-classic,
        body.ui-modern .sidebar-toggle-classic {
            display: none !important;
        }

        html.ui-modern .sidebar-toggle-modern,
        body.ui-modern .sidebar-toggle-modern {
            display: inline-flex;
            align-items: center;
        }

        .hm-sidebar-toggle {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.55rem 0.9rem;
            border-radius: 999px;
            border: 1px solid var(--ui-border, rgba(15, 23, 42, 0.16));
            background: var(--ui-surface, rgba(255, 255, 255, 0.92));
            color: var(--ui-ink, rgba(15, 23, 42, 0.92));
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.10);
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
            will-change: transform;
        }

        .hm-sidebar-toggle i {
            font-size: 1.05rem;
            color: var(--ui-accent, rgba(14, 165, 233, 0.95));
        }

        .hm-sidebar-toggle-text {
            font-weight: 800;
            font-size: 0.92rem;
        }

        .hm-sidebar-toggle:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(2, 6, 23, 0.14);
            border-color: rgba(14, 165, 233, 0.28);
        }

        .hm-sidebar-toggle:active {
            transform: translateY(0px);
        }

        @media (max-width: 576px) {
            .hm-sidebar-toggle-text {
                display: none;
            }

            .hm-sidebar-toggle {
                padding: 0.55rem 0.75rem;
            }
        }

        /* === Modern Sidebar Skin (only in ui-modern) === */
        html.ui-modern .page-sidebar,
        body.ui-modern .page-sidebar {
            background: linear-gradient(180deg, var(--ui-surface), var(--ui-surface-2));
            border: 1px solid var(--ui-border);
            border-radius: 18px;
            box-shadow: var(--ui-shadow);
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            overflow: hidden;
            margin: 12px;
            height: calc(100vh - 24px);
        }

        /* Legacy theme overrides (admin.css uses very high specificity for sidebar) */
        html.ui-modern .page-wrapper .page-body-wrapper .page-sidebar,
        body.ui-modern .page-wrapper .page-body-wrapper .page-sidebar {
            background: linear-gradient(180deg, var(--ui-surface), var(--ui-surface-2)) !important;
            border: 1px solid var(--ui-border) !important;
            box-shadow: var(--ui-shadow) !important;
        }

        html.ui-modern .page-wrapper .page-body-wrapper .page-sidebar .main-header-left,
        body.ui-modern .page-wrapper .page-body-wrapper .page-sidebar .main-header-left {
            background-color: transparent !important;
            box-shadow: none !important;
        }

        html.ui-modern .page-wrapper .page-body-wrapper .sidebar,
        body.ui-modern .page-wrapper .page-body-wrapper .sidebar {
            background-color: transparent !important;
            box-shadow: none !important;
        }

        html.ui-modern .page-wrapper .page-body-wrapper .page-sidebar .sidebar-menu,
        body.ui-modern .page-wrapper .page-body-wrapper .page-sidebar .sidebar-menu {
            padding: 6px 10px 16px !important;
        }

        html.ui-modern .page-wrapper .page-body-wrapper .page-sidebar .sidebar-menu .sidebar-header,
        body.ui-modern .page-wrapper .page-body-wrapper .page-sidebar .sidebar-menu .sidebar-header {
            background: var(--ui-surface) !important;
            border: 1px solid var(--ui-border) !important;
            color: var(--ui-ink) !important;
            border-radius: 14px !important;
            padding: 10px 12px !important;
        }

        html.ui-modern .page-wrapper .page-body-wrapper .page-sidebar .sidebar-menu .sidebar-header span,
        body.ui-modern .page-wrapper .page-body-wrapper .page-sidebar .sidebar-menu .sidebar-header span {
            color: var(--ui-ink) !important;
            font-weight: 800 !important;
        }

        html.ui-modern .page-wrapper .page-body-wrapper .page-sidebar .sidebar-menu .sidebar-header svg,
        body.ui-modern .page-wrapper .page-body-wrapper .page-sidebar .sidebar-menu .sidebar-header svg {
            stroke: var(--ui-accent) !important;
            color: var(--ui-accent) !important;
        }

        html.ui-modern .page-sidebar .main-header-left,
        body.ui-modern .page-sidebar .main-header-left {
            background: transparent;
        }

        html.ui-modern .page-sidebar .logo-wrapper,
        body.ui-modern .page-sidebar .logo-wrapper {
            margin: 12px 12px 8px;
            padding: 12px 12px;
            border-radius: 16px;
            background: var(--ui-surface);
            border: 1px solid var(--ui-border);
            box-shadow: 0 10px 22px rgba(2, 6, 23, 0.05);
        }

        html.ui-modern .page-sidebar .logo-wrapper img,
        body.ui-modern .page-sidebar .logo-wrapper img {
            max-width: 100%;
            height: auto;
            filter: saturate(1.05) contrast(1.02);
        }

        html.ui-modern .page-sidebar .sidebar,
        body.ui-modern .page-sidebar .sidebar {
            background: transparent;
        }

        html.ui-modern .page-sidebar .sidebar-user,
        body.ui-modern .page-sidebar .sidebar-user {
            padding: 14px 14px;
            margin: 12px 12px 8px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--ui-accent-soft), var(--ui-warm-soft));
            border: 1px solid var(--ui-border);
            box-shadow: 0 18px 44px rgba(2, 6, 23, 0.10);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        html.ui-modern .page-sidebar .sidebar-user img,
        body.ui-modern .page-sidebar .sidebar-user img {
            border-radius: 999px;
            border: 1px solid var(--ui-border);
            box-shadow: 0 18px 50px rgba(2, 6, 23, 0.16);
        }

        html.ui-modern .page-sidebar .sidebar-user h6,
        body.ui-modern .page-sidebar .sidebar-user h6 {
            color: var(--ui-ink);
            font-weight: 900;
            margin: 0;
        }

        html.ui-modern .page-sidebar .sidebar-user p,
        body.ui-modern .page-sidebar .sidebar-user p {
            color: var(--ui-muted);
            margin: 2px 0 0;
        }

        html.ui-modern .page-sidebar .sidebar-menu,
        body.ui-modern .page-sidebar .sidebar-menu {
            padding: 6px 10px 16px;
        }

        html.ui-modern .page-sidebar .sidebar-ui-mode,
        body.ui-modern .page-sidebar .sidebar-ui-mode,
        html.ui-modern .page-sidebar .sidebar-color-mode,
        body.ui-modern .page-sidebar .sidebar-color-mode {
            margin: 8px 12px 10px;
            padding: 10px 12px;
            border-radius: 14px;
            background: var(--ui-surface);
            border: 1px solid var(--ui-border);
            box-shadow: 0 10px 22px rgba(2, 6, 23, 0.06);
        }

        html.ui-modern .page-sidebar .sidebar-ui-mode .form-check-label,
        body.ui-modern .page-sidebar .sidebar-ui-mode .form-check-label,
        html.ui-modern .page-sidebar .sidebar-color-mode .form-check-label,
        body.ui-modern .page-sidebar .sidebar-color-mode .form-check-label {
            color: var(--ui-ink);
            font-weight: 800;
            cursor: pointer;
        }

        html.ui-modern .page-sidebar .sidebar-ui-mode .form-check-input,
        body.ui-modern .page-sidebar .sidebar-ui-mode .form-check-input,
        html.ui-modern .page-sidebar .sidebar-color-mode .form-check-input,
        body.ui-modern .page-sidebar .sidebar-color-mode .form-check-input {
            cursor: pointer;
        }

        html.ui-modern .page-sidebar .sidebar-ui-mode .form-check-input:checked,
        body.ui-modern .page-sidebar .sidebar-ui-mode .form-check-input:checked,
        html.ui-modern .page-sidebar .sidebar-color-mode .form-check-input:checked,
        body.ui-modern .page-sidebar .sidebar-color-mode .form-check-input:checked {
            background-color: rgba(14, 165, 233, 0.95);
            border-color: rgba(14, 165, 233, 0.95);
        }

        html.ui-modern .page-sidebar .sidebar-ui-mode .form-check-input:focus,
        body.ui-modern .page-sidebar .sidebar-ui-mode .form-check-input:focus,
        html.ui-modern .page-sidebar .sidebar-color-mode .form-check-input:focus,
        body.ui-modern .page-sidebar .sidebar-color-mode .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.18);
            border-color: rgba(14, 165, 233, 0.55);
        }

        html.ui-modern .page-sidebar .sidebar-menu > li,
        body.ui-modern .page-sidebar .sidebar-menu > li {
            margin: 6px 0;
        }

        html.ui-modern .page-sidebar a.sidebar-header,
        body.ui-modern .page-sidebar a.sidebar-header {
            border-radius: 14px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--ui-surface) !important;
            border: 1px solid var(--ui-border) !important;
            color: var(--ui-ink) !important;
            box-shadow: 0 10px 22px rgba(2, 6, 23, 0.06);
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease, background 180ms ease;
            will-change: transform;
        }

        html.ui-modern .page-sidebar a.sidebar-header span,
        body.ui-modern .page-sidebar a.sidebar-header span {
            color: var(--ui-ink) !important;
        }

        html.ui-modern .page-sidebar a.sidebar-header i[data-feather],
        body.ui-modern .page-sidebar a.sidebar-header i[data-feather] {
            color: var(--ui-accent) !important;
        }

        html.ui-modern .page-sidebar a.sidebar-header > i:not(.fa-angle-right),
        body.ui-modern .page-sidebar a.sidebar-header > i:not(.fa-angle-right) {
            color: var(--ui-accent) !important;
        }

        html.ui-modern .page-sidebar a.sidebar-header .fa-angle-right,
        body.ui-modern .page-sidebar a.sidebar-header .fa-angle-right {
            color: var(--ui-muted) !important;
            opacity: 0.9;
        }

        html.ui-modern .page-sidebar a.sidebar-header span,
        body.ui-modern .page-sidebar a.sidebar-header span {
            font-weight: 800;
        }

        html.ui-modern .page-sidebar a.sidebar-header:hover,
        body.ui-modern .page-sidebar a.sidebar-header:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(2, 6, 23, 0.10);
            border-color: rgba(14, 165, 233, 0.22);
            background: var(--ui-surface) !important;
        }

        html.ui-modern .page-sidebar li.active-temp > a.sidebar-header,
        body.ui-modern .page-sidebar li.active-temp > a.sidebar-header {
            border-color: rgba(14, 165, 233, 0.30);
            background: linear-gradient(90deg, var(--ui-accent-soft), var(--ui-warm-soft));
            color: var(--ui-ink);
            box-shadow: 0 18px 44px rgba(2, 6, 23, 0.10);
        }

        html.ui-modern .page-sidebar li.active-temp > a.sidebar-header .fa-angle-right,
        body.ui-modern .page-sidebar li.active-temp > a.sidebar-header .fa-angle-right {
            transform: rotate(-90deg);
        }

        html.ui-modern .page-sidebar .sidebar-submenu,
        body.ui-modern .page-sidebar .sidebar-submenu {
            margin: 8px 6px 0;
            padding: 10px 8px;
            border-radius: 14px;
            background: var(--ui-surface-2) !important;
            border: 1px solid var(--ui-border) !important;
        }

        html.ui-modern .page-sidebar .sidebar-submenu a.sidebar-header,
        body.ui-modern .page-sidebar .sidebar-submenu a.sidebar-header {
            background: var(--ui-surface) !important;
            box-shadow: none;
            padding: 9px 10px;
        }

        html.ui-modern .page-sidebar .custom-scrollbar,
        body.ui-modern .page-sidebar .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(14, 165, 233, 0.35) transparent;
        }

        html.ui-modern .page-sidebar .custom-scrollbar::-webkit-scrollbar,
        body.ui-modern .page-sidebar .custom-scrollbar::-webkit-scrollbar {
            width: 10px;
        }

        html.ui-modern .page-sidebar .custom-scrollbar::-webkit-scrollbar-track,
        body.ui-modern .page-sidebar .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        html.ui-modern .page-sidebar .custom-scrollbar::-webkit-scrollbar-thumb,
        body.ui-modern .page-sidebar .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(14, 165, 233, 0.28);
            border-radius: 999px;
            border: 2px solid transparent;
            background-clip: padding-box;
        }

        @media (max-width: 991px) {
            html.ui-modern .page-sidebar,
            body.ui-modern .page-sidebar {
                margin: 0;
                border-radius: 0;
                height: 100vh;
                z-index: 1040 !important;
            }
        }

        /* Mobile: ensure sidebar content isn't hidden behind the bottom bar */
        @media (max-width: 991px) {
            html.ui-modern .page-sidebar .sidebar,
            body.ui-modern .page-sidebar .sidebar {
                padding-bottom: var(--dashboard-mobilebar-offset, 104px) !important;
            }
        }

        /* === Modern Header Skin (only in ui-modern) === */
        html.ui-modern .dashboard-topbar,
        body.ui-modern .dashboard-topbar {
            background: var(--ui-surface) !important;
            border: 1px solid var(--ui-border) !important;
            box-shadow: var(--ui-shadow) !important;
            color: var(--ui-ink) !important;

            left: 12px !important;
            right: 12px !important;
            top: 12px !important;
            width: calc(100% - 24px) !important;
            border-radius: 18px !important;
            height: auto !important;
            padding: 10px 12px !important;
        }

        html.ui-modern .dashboard-topbar-desktop,
        body.ui-modern .dashboard-topbar-desktop {
            direction: rtl;
            position: fixed;
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            flex-wrap: nowrap !important;
            gap: 12px;
        }

        html.ui-modern .dashboard-topbar-desktop .desktop-header-left,
        body.ui-modern .dashboard-topbar-desktop .desktop-header-left,
        html.ui-modern .dashboard-topbar-desktop .desktop-header-right,
        body.ui-modern .dashboard-topbar-desktop .desktop-header-right {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            z-index: 2;
        }

        html.ui-modern .dashboard-topbar-desktop .desktop-header-left,
        body.ui-modern .dashboard-topbar-desktop .desktop-header-left {
            flex: 0 0 auto;
        }

        html.ui-modern .dashboard-topbar-desktop .desktop-header-right,
        body.ui-modern .dashboard-topbar-desktop .desktop-header-right {
            flex: 0 0 auto;
        }

        html.ui-modern .dashboard-topbar-desktop .desktop-header-center,
        body.ui-modern .dashboard-topbar-desktop .desktop-header-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            font-size: 1.05rem;
            font-weight: 900;
            letter-spacing: .25px;
            color: var(--ui-ink);
            white-space: nowrap;
            z-index: 1;
            pointer-events: none;
        }

        html.ui-modern .dashboard-topbar-desktop .desktop-user-avatar,
        body.ui-modern .dashboard-topbar-desktop .desktop-user-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 999px;
            border: 1px solid var(--ui-border);
            overflow: hidden;
            box-shadow: 0 10px 22px rgba(2, 6, 23, 0.08);
            background: var(--ui-surface-2);
            text-decoration: none;
        }

        html.ui-modern .dashboard-topbar-desktop .desktop-user-avatar figure,
        body.ui-modern .dashboard-topbar-desktop .desktop-user-avatar figure {
            width: 100%;
            height: 100%;
            margin: 0;
        }

        html.ui-modern .dashboard-topbar-desktop #sidebar-toggle-modern,
        body.ui-modern .dashboard-topbar-desktop #sidebar-toggle-modern {
            min-width: 44px;
            height: 44px;
            width: 44px;
            padding: 0 !important;
            border-radius: 999px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        html.ui-modern .dashboard-topbar-desktop #sidebar-toggle-modern .hm-sidebar-toggle-text,
        body.ui-modern .dashboard-topbar-desktop #sidebar-toggle-modern .hm-sidebar-toggle-text {
            display: none;
        }

        html.ui-modern .dashboard-topbar-desktop #sidebar-toggle-modern i,
        body.ui-modern .dashboard-topbar-desktop #sidebar-toggle-modern i {
            font-size: 1.05rem;
            color: var(--ui-ink);
        }

        html.ui-modern #toast-container.toast-top-right,
        body.ui-modern #toast-container.toast-top-right {
            top: 92px !important;
            right: 16px !important;
        }

        html.ui-modern #toast-container > .toast,
        body.ui-modern #toast-container > .toast {
            width: 340px;
            border-radius: 14px;
            box-shadow: 0 16px 34px rgba(2, 6, 23, .2);
            opacity: 1 !important;
            padding: 12px 14px 12px 48px;
            background-image: none !important;
            border: 1px solid rgba(148, 163, 184, .28) !important;
            color: #0f172a !important;
            font-weight: 700;
            animation: uiModernToastIn .2s ease-out;
            backdrop-filter: none !important;
        }

        html.ui-modern #toast-container > .toast-success,
        body.ui-modern #toast-container > .toast-success {
            background: #ecfdf5 !important;
            border-color: rgba(16, 185, 129, .45) !important;
            color: #065f46 !important;
        }

        html.ui-modern #toast-container > .toast-error,
        body.ui-modern #toast-container > .toast-error {
            background: #fef2f2 !important;
            border-color: rgba(239, 68, 68, .45) !important;
            color: #991b1b !important;
        }

        html.ui-modern #toast-container > .toast-warning,
        body.ui-modern #toast-container > .toast-warning {
            background: #fffbeb !important;
            border-color: rgba(245, 158, 11, .45) !important;
            color: #92400e !important;
        }

        html.ui-modern #toast-container > .toast-info,
        body.ui-modern #toast-container > .toast-info {
            background: #eff6ff !important;
            border-color: rgba(59, 130, 246, .45) !important;
            color: #1e3a8a !important;
        }

        html.ui-modern #toast-container > .toast .toast-message,
        body.ui-modern #toast-container > .toast .toast-message {
            color: inherit !important;
            line-height: 1.55;
            font-size: .9rem;
            opacity: 1 !important;
        }

        html.ui-modern #toast-container > .toast .toast-progress,
        body.ui-modern #toast-container > .toast .toast-progress {
            background: rgba(15, 23, 42, .2);
        }

        html.ui-modern #toast-container > .toast .toast-close-button,
        body.ui-modern #toast-container > .toast .toast-close-button {
            color: inherit;
            opacity: .8;
        }

        @keyframes uiModernToastIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modern: desktop header vs mobile header separation */
        html.ui-modern .dashboard-topbar-mobile,
        body.ui-modern .dashboard-topbar-mobile,
        html.ui-modern .dashboard-mobilebar,
        body.ui-modern .dashboard-mobilebar {
            display: none;
        }

        @media (max-width: 991px) {
            html.ui-modern .dashboard-topbar-desktop,
            body.ui-modern .dashboard-topbar-desktop {
                display: none !important;
            }

            html.ui-modern .dashboard-topbar-mobile,
            body.ui-modern .dashboard-topbar-mobile {
                display: block;
                background: var(--ui-surface);
                border: 1px solid var(--ui-border);
                box-shadow: var(--ui-shadow);
                color: var(--ui-ink);
                left: 0;
                right: 0;
                top: 0;
                width: 100%;
                z-index: 1105;
            }

            html.ui-modern #toast-container.toast-top-right,
            body.ui-modern #toast-container.toast-top-right {
                top: 74px !important;
                right: 12px !important;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-inner,
            body.ui-modern .dashboard-topbar-mobile .dtm-inner {
                height: 60px;
                display: grid;
                grid-template-columns: auto 1fr auto;
                align-items: center;
                gap: 10px;
                padding: 10px 12px;
                direction: ltr;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-brand,
            body.ui-modern .dashboard-topbar-mobile .dtm-brand {
                text-decoration: none;
                color: var(--ui-ink);
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-weight: 900;
                margin: 0;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-left,
            body.ui-modern .dashboard-topbar-mobile .dtm-left {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                direction: ltr;
                position: relative;
                z-index: 2;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-center,
            body.ui-modern .dashboard-topbar-mobile .dtm-center {
                min-width: 0;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-title,
            body.ui-modern .dashboard-topbar-mobile .dtm-title {
                font-weight: 900;
                letter-spacing: 0.2px;
            }

            html.ui-modern .hm-sidebar-toggle--mobile,
            body.ui-modern .hm-sidebar-toggle--mobile {
                padding: 0.55rem 0.75rem;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-user,
            body.ui-modern .dashboard-topbar-mobile .dtm-user {
                width: 44px;
                height: 44px;
                border-radius: 999px;
                border: 1px solid var(--ui-border);
                background: var(--ui-surface-2);
                box-shadow: 0 10px 22px rgba(2, 6, 23, 0.08);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-actions,
            body.ui-modern .dashboard-topbar-mobile .dtm-actions {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                justify-self: end;
                position: relative;
                z-index: 2;
            }

            html.ui-modern .dashboard-topbar-mobile #user-menu-wrapper-mobile,
            body.ui-modern .dashboard-topbar-mobile #user-menu-wrapper-mobile {
                position: relative;
            }

            html.ui-modern .dashboard-topbar-mobile #notif-menu-wrapper-mobile,
            body.ui-modern .dashboard-topbar-mobile #notif-menu-wrapper-mobile {
                position: relative;
                z-index: 1085;
            }

            html.ui-modern .dashboard-topbar-mobile #user-menu-panel-mobile,
            body.ui-modern .dashboard-topbar-mobile #user-menu-panel-mobile {
                left: 0;
                right: auto;
                inset-inline-start: 0;
                inset-inline-end: auto;
                top: calc(100% + 8px);
                min-width: 172px;
                max-width: calc(100vw - 18px);
                margin: 0;
                transform: none !important;
                z-index: 1080;
            }

            html.ui-modern .dashboard-topbar-mobile #notif-menu-panel-mobile,
            body.ui-modern .dashboard-topbar-mobile #notif-menu-panel-mobile {
                left: auto;
                right: 0;
                inset-inline-start: auto;
                inset-inline-end: 0;
                top: calc(100% + 8px);
                min-width: 290px;
                width: auto;
                max-width: min(92vw, 360px);
                margin: 0;
                transform: none !important;
                z-index: 1081;
                padding: 0;
                overflow: hidden;
                border-radius: 16px;
                border: 1px solid var(--ui-border);
                background: var(--ui-surface);
                box-shadow: 0 20px 44px rgba(2, 6, 23, 0.16);
                direction: ltr;
                text-align: left;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-panel.show,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-panel.show {
                display: block !important;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-head,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                padding: 11px 12px;
                border-bottom: 1px solid var(--ui-border);
                background: linear-gradient(135deg, rgba(14, 165, 233, 0.08), rgba(59, 130, 246, 0.04));
                direction: rtl;
                text-align: right;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-read-all,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-read-all {
                border: 1px solid rgba(14, 165, 233, 0.24);
                background: rgba(14, 165, 233, 0.1);
                color: var(--ui-accent);
                font-size: 0.73rem;
                font-weight: 800;
                padding: 4px 8px;
                border-radius: 999px;
                transition: all 160ms ease;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-read-all:hover,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-read-all:hover {
                transform: translateY(-1px);
                background: rgba(14, 165, 233, 0.16);
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-list,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-list {
                max-height: 340px;
                overflow: auto;
                padding: 8px;
                scrollbar-width: thin;
                scrollbar-color: rgba(14, 165, 233, 0.35) transparent;
                direction: rtl;
                text-align: right;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-list::-webkit-scrollbar,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-list::-webkit-scrollbar {
                width: 8px;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-list::-webkit-scrollbar-thumb,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-list::-webkit-scrollbar-thumb {
                background: rgba(14, 165, 233, 0.3);
                border-radius: 999px;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-item,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-item {
                display: block;
                text-decoration: none;
                color: var(--ui-ink);
                border: 1px solid transparent;
                border-radius: 12px;
                padding: 10px;
                margin-bottom: 7px;
                background: rgba(255, 255, 255, 0.92);
                box-shadow: 0 8px 18px rgba(2, 6, 23, 0.04);
                transition: transform 170ms ease, box-shadow 170ms ease, border-color 170ms ease, background-color 170ms ease;
                animation: notifCardIn 220ms ease both;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-item.unread,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-item.unread {
                background: linear-gradient(135deg, rgba(14, 165, 233, 0.12), rgba(255, 255, 255, 0.98));
                border-color: rgba(14, 165, 233, 0.28);
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-item:hover,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-item:hover {
                transform: translateY(-1px);
                box-shadow: 0 14px 26px rgba(2, 6, 23, 0.08);
                border-color: rgba(14, 165, 233, 0.24);
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-title,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-title {
                font-weight: 800;
                font-size: 0.82rem;
                line-height: 1.45;
                margin-bottom: 3px;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-body,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-body {
                font-size: 0.75rem;
                line-height: 1.5;
                color: var(--ui-muted);
                margin-bottom: 5px;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-meta,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-dot,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-dot {
                width: 8px;
                height: 8px;
                border-radius: 999px;
                background: rgba(14, 165, 233, 0.95);
                box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.16);
                flex: 0 0 auto;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-item:not(.unread) .dtm-notif-dot,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-item:not(.unread) .dtm-notif-dot {
                visibility: hidden;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-time,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-time {
                font-size: 0.7rem;
                color: rgba(100, 116, 139, 0.95);
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-empty,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-empty {
                text-align: center;
                color: var(--ui-muted);
                font-size: 0.82rem;
                padding: 12px 10px;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif,
            html.ui-modern .dashboard-topbar-mobile .dtm-logout,
            body.ui-modern .dashboard-topbar-mobile .dtm-logout {
                width: 42px;
                height: 42px;
                border-radius: 999px;
                border: 1px solid var(--ui-border);
                background: var(--ui-surface-2);
                box-shadow: 0 10px 22px rgba(2, 6, 23, 0.08);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                padding: 0;
                pointer-events: auto;
                touch-action: manipulation;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif {
                color: var(--ui-ink);
                position: relative;
                z-index: 1090;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-badge,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-badge {
                position: absolute;
                top: -4px;
                right: -4px;
                min-width: 18px;
                height: 18px;
                line-height: 18px;
                border-radius: 999px;
                text-align: center;
                padding: 0 4px;
                background: var(--ui-danger);
                color: #fff;
                font-size: 0.68rem;
                font-weight: 800;
                border: 1px solid rgba(255, 255, 255, 0.7);
                display: inline-block;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif-badge[hidden],
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-badge[hidden] {
                display: none;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-logout i,
            body.ui-modern .dashboard-topbar-mobile .dtm-logout i {
                font-size: 1rem;
                color: var(--ui-danger);
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-notif i,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif i {
                font-size: 1rem;
                color: var(--ui-ink);
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-avatar,
            body.ui-modern .dashboard-topbar-mobile .dtm-avatar {
                display: block !important;
                width: 38px;
                height: 38px;
                border-radius: 999px;
                object-fit: cover;
            }

            html.ui-modern .dashboard-mobilebar,
            body.ui-modern .dashboard-mobilebar {
                display: flex;
                gap: 10px;
                position: fixed;
                left: 12px;
                right: 12px;
                bottom: 12px;
                padding: 10px 12px;
                border-radius: 18px;
                background: var(--ui-surface);
                border: 1px solid var(--ui-border);
                box-shadow: var(--ui-shadow);
                z-index: 1030;
            }

            html.ui-modern .dashboard-mobilebar .dmb-item,
            body.ui-modern .dashboard-mobilebar .dmb-item {
                text-decoration: none;
                color: var(--ui-muted);
                flex: 1 1 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 6px;
                padding: 8px 8px;
                border-radius: 14px;
                border: 1px solid transparent;
                font-weight: 800;
                background: var(--ui-surface-2);
            }

            html.ui-modern .dashboard-mobilebar .dmb-item i,
            body.ui-modern .dashboard-mobilebar .dmb-item i {
                font-size: 1.15rem;
                color: var(--ui-accent);
            }

            html.ui-modern .dashboard-mobilebar .dmb-item span,
            body.ui-modern .dashboard-mobilebar .dmb-item span {
                font-size: 0.82rem;
                color: var(--ui-ink);
            }
        }

        /* Push page content below the fixed modern header (dynamic via --dashboard-topbar-offset) */
        html.ui-modern .page-wrapper .page-body-wrapper .page-body,
        body.ui-modern .page-wrapper .page-body-wrapper .page-body {
            margin-top: var(--dashboard-topbar-offset, 112px) !important;
        }

        /* Push page content above the fixed mobile bottom bar */
        @media (max-width: 991px) {
            html.ui-modern .page-wrapper .page-body-wrapper .page-body,
            body.ui-modern .page-wrapper .page-body-wrapper .page-body {
                padding-bottom: var(--dashboard-mobilebar-offset, 104px) !important;
            }
        }

        html.ui-modern .dashboard-topbar .text-secondary,
        body.ui-modern .dashboard-topbar .text-secondary {
            color: var(--ui-muted) !important;
        }

        html.ui-modern .dashboard-topbar .f-nav,
        body.ui-modern .dashboard-topbar .f-nav {
            padding-top: 0 !important;
            gap: 10px;
        }

        html.ui-modern .dashboard-topbar .f-nav figure,
        body.ui-modern .dashboard-topbar .f-nav figure {
            width: 44px !important;
            height: 44px !important;
        }

        html.ui-modern .dashboard-topbar .f-nav a figure,
        body.ui-modern .dashboard-topbar .f-nav a figure {
            border: 1px solid var(--ui-border);
            box-shadow: 0 16px 40px rgba(2, 6, 23, 0.12);
        }

        html.ui-modern .dashboard-topbar .f-nav p,
        body.ui-modern .dashboard-topbar .f-nav p {
            margin: 0;
            line-height: 1.15;
        }

        html.ui-modern .dashboard-topbar .navControll,
        body.ui-modern .dashboard-topbar .navControll {
            gap: 10px;
            align-items: center;
        }

        html.ui-modern .dashboard-topbar .btn,
        body.ui-modern .dashboard-topbar .btn {
            border-radius: 999px !important;
            border: 1px solid var(--ui-border) !important;
            background: var(--ui-surface-2) !important;
            color: var(--ui-ink) !important;
            box-shadow: 0 10px 22px rgba(2, 6, 23, 0.06);
            padding: 8px 12px !important;
        }

        html.ui-modern .dashboard-topbar .btn i,
        body.ui-modern .dashboard-topbar .btn i,
        html.ui-modern .dashboard-topbar .btn svg,
        body.ui-modern .dashboard-topbar .btn svg {
            color: var(--ui-muted);
        }

        html.ui-modern .dashboard-topbar a.home-tn .btn i,
        body.ui-modern .dashboard-topbar a.home-tn .btn i {
            color: var(--ui-accent);
        }

        html.ui-modern .dashboard-topbar .btn:hover,
        body.ui-modern .dashboard-topbar .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(2, 6, 23, 0.10);
            border-color: rgba(14, 165, 233, 0.25) !important;
        }

        html.ui-modern .dashboard-topbar .btn:active,
        body.ui-modern .dashboard-topbar .btn:active {
            transform: translateY(0);
        }

        html.ui-modern .dashboard-topbar #user-menu-dropdown,
        body.ui-modern .dashboard-topbar #user-menu-dropdown {
            background: linear-gradient(90deg, var(--ui-accent-soft), rgba(255, 255, 255, 0.92)) !important;
            border-color: rgba(14, 165, 233, 0.22) !important;
            font-weight: 800;
        }

        html.ui-modern .dashboard-topbar #user-menu-dropdown .fa-user,
        body.ui-modern .dashboard-topbar #user-menu-dropdown .fa-user {
            color: var(--ui-accent) !important;
        }

        html.ui-modern .dashboard-topbar .hm-sidebar-toggle,
        body.ui-modern .dashboard-topbar .hm-sidebar-toggle {
            background: var(--ui-surface) !important;
            border-color: var(--ui-border) !important;
            box-shadow: 0 10px 22px rgba(2, 6, 23, 0.08) !important;
        }

        html.ui-modern .dashboard-topbar .dropdown-menu,
        body.ui-modern .dashboard-topbar .dropdown-menu {
            border-radius: 14px;
            border: 1px solid var(--ui-border);
            background: var(--ui-surface);
            box-shadow: 0 24px 60px rgba(2, 6, 23, 0.16);
            padding: 8px;
        }

        html.ui-modern .dashboard-topbar #notif-menu-wrapper,
        body.ui-modern .dashboard-topbar #notif-menu-wrapper {
            position: relative;
        }

        html.ui-modern .dashboard-topbar .notif-toggle,
        body.ui-modern .dashboard-topbar .notif-toggle {
            position: relative;
            min-width: 44px;
            height: 44px;
            padding: 0 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        html.ui-modern .dashboard-topbar .notif-toggle .bi,
        body.ui-modern .dashboard-topbar .notif-toggle .bi {
            font-size: 1.05rem;
            color: var(--ui-ink);
        }

        html.ui-modern .dashboard-topbar .dtm-notif-badge,
        body.ui-modern .dashboard-topbar .dtm-notif-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            line-height: 18px;
            border-radius: 999px;
            text-align: center;
            padding: 0 4px;
            background: var(--ui-danger);
            color: #fff;
            font-size: 0.68rem;
            font-weight: 800;
            border: 1px solid rgba(255, 255, 255, 0.7);
            display: inline-block;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-badge[hidden],
        body.ui-modern .dashboard-topbar .dtm-notif-badge[hidden] {
            display: none;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-panel,
        body.ui-modern .dashboard-topbar .dtm-notif-panel {
            left: auto;
            right: 0;
            inset-inline-start: auto;
            inset-inline-end: 0;
            min-width: 320px;
            max-width: min(88vw, 380px);
            padding: 0;
            overflow: hidden;
            border-radius: 16px;
            border: 1px solid var(--ui-border);
            background: var(--ui-surface);
            box-shadow: 0 22px 46px rgba(2, 6, 23, 0.16);
            transform: translateY(8px) scale(0.98);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            display: block !important;
            transition: opacity 180ms ease, transform 220ms cubic-bezier(0.22, 1, 0.36, 1), visibility 180ms ease;
            direction: ltr;
            text-align: left;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-panel.show,
        body.ui-modern .dashboard-topbar .dtm-notif-panel.show {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }

        html.ui-modern .dashboard-topbar .dtm-notif-head,
        body.ui-modern .dashboard-topbar .dtm-notif-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 11px 12px;
            border-bottom: 1px solid var(--ui-border);
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.08), rgba(59, 130, 246, 0.04));
            direction: rtl;
            text-align: right;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-read-all,
        body.ui-modern .dashboard-topbar .dtm-notif-read-all {
            border: 1px solid rgba(14, 165, 233, 0.24);
            background: rgba(14, 165, 233, 0.1);
            color: var(--ui-accent);
            font-size: 0.73rem;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 999px;
            transition: all 160ms ease;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-read-all:hover,
        body.ui-modern .dashboard-topbar .dtm-notif-read-all:hover {
            transform: translateY(-1px);
            background: rgba(14, 165, 233, 0.16);
        }

        html.ui-modern .dashboard-topbar .dtm-notif-list,
        body.ui-modern .dashboard-topbar .dtm-notif-list {
            max-height: 340px;
            overflow: auto;
            padding: 8px;
            scrollbar-width: thin;
            scrollbar-color: rgba(14, 165, 233, 0.35) transparent;
            direction: rtl;
            text-align: right;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-list::-webkit-scrollbar,
        body.ui-modern .dashboard-topbar .dtm-notif-list::-webkit-scrollbar {
            width: 8px;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-list::-webkit-scrollbar-thumb,
        body.ui-modern .dashboard-topbar .dtm-notif-list::-webkit-scrollbar-thumb {
            background: rgba(14, 165, 233, 0.3);
            border-radius: 999px;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-item,
        body.ui-modern .dashboard-topbar .dtm-notif-item {
            display: block;
            text-decoration: none;
            color: var(--ui-ink);
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 10px;
            margin-bottom: 7px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 8px 18px rgba(2, 6, 23, 0.04);
            transition: transform 170ms ease, box-shadow 170ms ease, border-color 170ms ease, background-color 170ms ease;
            animation: notifCardIn 220ms ease both;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-item.unread,
        body.ui-modern .dashboard-topbar .dtm-notif-item.unread {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.12), rgba(255, 255, 255, 0.98));
            border-color: rgba(14, 165, 233, 0.28);
        }

        html.ui-modern .dashboard-topbar .dtm-notif-item:hover,
        body.ui-modern .dashboard-topbar .dtm-notif-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 26px rgba(2, 6, 23, 0.08);
            border-color: rgba(14, 165, 233, 0.24);
        }

        html.ui-modern .dashboard-topbar .dtm-notif-title,
        body.ui-modern .dashboard-topbar .dtm-notif-title {
            font-weight: 800;
            font-size: 0.82rem;
            line-height: 1.45;
            margin-bottom: 3px;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-body,
        body.ui-modern .dashboard-topbar .dtm-notif-body {
            font-size: 0.75rem;
            line-height: 1.5;
            color: var(--ui-muted);
            margin-bottom: 5px;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-meta,
        body.ui-modern .dashboard-topbar .dtm-notif-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-dot,
        body.ui-modern .dashboard-topbar .dtm-notif-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: rgba(14, 165, 233, 0.95);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.16);
            flex: 0 0 auto;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-item:not(.unread) .dtm-notif-dot,
        body.ui-modern .dashboard-topbar .dtm-notif-item:not(.unread) .dtm-notif-dot {
            visibility: hidden;
        }

        html.ui-modern .dashboard-topbar .dtm-notif-time,
        body.ui-modern .dashboard-topbar .dtm-notif-time {
            font-size: 0.7rem;
            color: rgba(100, 116, 139, 0.95);
        }

        @keyframes notifCardIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html.ui-modern .dashboard-topbar .dtm-notif-panel,
            body.ui-modern .dashboard-topbar .dtm-notif-panel,
            html.ui-modern .dashboard-topbar-mobile .dtm-notif-panel,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-panel,
            html.ui-modern .dashboard-topbar .dtm-notif-item,
            body.ui-modern .dashboard-topbar .dtm-notif-item,
            html.ui-modern .dashboard-topbar-mobile .dtm-notif-item,
            body.ui-modern .dashboard-topbar-mobile .dtm-notif-item {
                transition: none !important;
                animation: none !important;
            }
        }

        html.ui-modern .dashboard-topbar .dtm-notif-empty,
        body.ui-modern .dashboard-topbar .dtm-notif-empty {
            text-align: center;
            color: var(--ui-muted);
            font-size: 0.82rem;
            padding: 12px 10px;
        }

        html.ui-modern .dashboard-topbar .dropdown-item,
        body.ui-modern .dashboard-topbar .dropdown-item {
            border-radius: 10px;
            color: var(--ui-ink);
            font-weight: 700;
        }

        html.ui-modern .dashboard-topbar .dropdown-item:hover,
        body.ui-modern .dashboard-topbar .dropdown-item:hover {
            background: rgba(14, 165, 233, 0.10);
        }

        @media (max-width: 991px) {
            html.ui-modern .dashboard-topbar,
            body.ui-modern .dashboard-topbar {
                left: 0 !important;
                right: 0 !important;
                top: 0 !important;
                width: 100% !important;
                border-radius: 0 !important;
            }
        }
    </style>
    @stack('css')
</head>

<body class="ui-{{ $uiModeServer }}" data-ui-mode="{{ $uiModeServer }}">
    <div id="postLoginLoadingMask" aria-hidden="true">
        <div class="post-login-loading__content">
            <div class="post-login-loading__spinner"></div>
            <div class="post-login-loading__text">جاري فتح لوحة التحكم...</div>
        </div>
    </div>

    <!-- page-wrapper Start-->
    <div class="page-wrapper">

        @include('layouts.dashboard.navbar')

        <!-- Page Body Start -->
        <div class="page-body-wrapper">
            @if ( (auth()->user()) && auth()->user()->hasRole('Administrator'))
            @include('layouts.dashboard.sidebar')

            @endif
            <div class="page-body">
                @yield('content')
            </div>
            @include('layouts.dashboard.footer')
        </div>
    </div>
    <!-- page-wrapper end-->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @if ($loadAceAssets)
        <script src="{{ asset('assets/admin/js/ace/ace.js') }}"></script>
        <script src="{{ asset('assets/admin/js/ace/mode-css.js') }}"></script>
        <script src="{{ asset('assets/admin/js/ace/worker-css.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/ace-builds@1.32.6/src-min/snippets/css.js"></script>
        <script src="{{ asset('assets/admin/js/ace/ext-language_tools.js') }}"></script>
    @endif
    <script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.easing.1.3.js') }}"></script>
    @if ($loadSelect2Assets)
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @endif
    <script src="{{ asset('assets/admin/js/main.js') }}?v={{ filemtime(public_path('assets/admin/js/main.js')) }}"></script>
    <!-- Bootstrap JS already loaded via assets/admin/js/bootstrap.bundle.min.js -->









    @stack('js-upper')
    <!--script admin-->
    <script>
        window.supportedLocales = {!! collect(config('translatable.locales'))->toJson() !!}
    </script>
    @if ($loadTagsInputAssets)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css"/>
    @endif

    <script>
        (function () {
            if (!window.jQuery) return;
            var $ = window.jQuery;
            if (!$.fn.select2) {
                $.fn.select2 = function () { return this; };
            }
            if (!$.fn.tagsinput) {
                $.fn.tagsinput = function () { return this; };
            }
        })();
    </script>

    <script src="{{ asset('assets/admin/js/admin.js') }}"></script>

    <script>
        // Persist effective UI mode to DB (cross-device) and finalize <body> classes
        (function () {
            var IS_AUTH = !!window.__UI_MODE_IS_AUTH__;
            if (!IS_AUTH) {
                // still ensure body mirrors <html>
                var mode = window.__UI_MODE_EFFECTIVE__ || 'classic';
                var colorMode = window.__UI_COLOR_MODE_EFFECTIVE__ || 'light';
                document.body.classList.remove('ui-modern', 'ui-classic');
                document.body.classList.add(mode === 'modern' ? 'ui-modern' : 'ui-classic');
                document.body.setAttribute('data-ui-mode', mode);
                document.body.classList.remove('ui-dark', 'ui-light');
                document.body.classList.add(colorMode === 'dark' ? 'ui-dark' : 'ui-light');
                document.body.setAttribute('data-ui-color-mode', colorMode);
                document.body.setAttribute('data-bs-theme', (mode === 'modern' && colorMode === 'dark') ? 'dark' : 'light');
                return;
            }

            var mode = window.__UI_MODE_EFFECTIVE__ || 'classic';
            var colorMode = window.__UI_COLOR_MODE_EFFECTIVE__ || 'light';
            var server = window.__UI_MODE_SERVER__ || 'classic';
            var policy = window.__UI_MODE_POLICY__ || 'user_choice';

            document.body.classList.remove('ui-modern', 'ui-classic');
            document.body.classList.add(mode === 'modern' ? 'ui-modern' : 'ui-classic');
            document.body.setAttribute('data-ui-mode', mode);
            document.body.classList.remove('ui-dark', 'ui-light');
            document.body.classList.add(colorMode === 'dark' ? 'ui-dark' : 'ui-light');
            document.body.setAttribute('data-ui-color-mode', colorMode);
            document.body.setAttribute('data-bs-theme', (mode === 'modern' && colorMode === 'dark') ? 'dark' : 'light');

            var pending = null;
            try {
                pending = localStorage.getItem('ui_mode_pending');
            } catch (e) {
                // ignore
            }

            if (!pending && server === mode) {
                return;
            }

            // If the global policy forces the UI, don't overwrite per-user preference in DB.
            if (policy === 'modern' || policy === 'classic') {
                try {
                    localStorage.setItem('ui_mode', policy);
                    localStorage.removeItem('ui_mode_pending');
                } catch (e) {
                    // ignore
                }
                return;
            }

            var csrf = document.querySelector('meta[name="csrf-token"]');
            var token = csrf ? csrf.getAttribute('content') : '';

            fetch("{{ route('ui-mode.update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                },
                credentials: 'same-origin',
                body: JSON.stringify({ mode: mode })
            }).then(function (res) {
                if (!res || !res.ok) {
                    return;
                }
                return res.json().then(function (data) {
                    if (data && data.success) {
                        try {
                            localStorage.removeItem('ui_mode_pending');
                        } catch (e) {
                            // ignore
                        }
                    }
                }).catch(function () {
                    // ignore
                });
            }).catch(function () {
                // ignore
            });
        })();
    </script>

    <script>
        (function () {
            function clearPostLoginLoading() {
                try {
                    sessionStorage.removeItem('post_login_loading');
                } catch (e) {
                    // ignore
                }
                document.documentElement.classList.remove('post-login-loading');
            }

            window.addEventListener('load', function () {
                setTimeout(clearPostLoginLoading, 120);
            });
        })();
    </script>

    <script>
        (function () {
            window.toggleDashboardUserMenu = function (event) {
                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                var userMenuPanel = document.querySelector('#user-menu-panel');
                var userMenuToggle = document.querySelector('#user-menu-dropdown');

                if (!userMenuPanel || !userMenuToggle) {
                    return;
                }

                var isOpen = userMenuPanel.classList.contains('show');
                userMenuPanel.classList.toggle('show', !isOpen);
                userMenuToggle.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
            };

            document.addEventListener('click', function (event) {
                var userMenuPanel = document.querySelector('#user-menu-panel');
                var clickedInsideMenu = event.target.closest('#user-menu-wrapper');
                if (!clickedInsideMenu && userMenuPanel && userMenuPanel.classList.contains('show')) {
                    userMenuPanel.classList.remove('show');
                    var toggleButton = document.querySelector('#user-menu-dropdown');
                    if (toggleButton) {
                        toggleButton.setAttribute('aria-expanded', 'false');
                    }
                }
            });
        })();
    </script>

    <script>
        (function () {
            window.toggleDashboardUserMenuMobile = function (event) {
                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                var userMenuPanel = document.querySelector('#user-menu-panel-mobile');
                var userMenuToggle = document.querySelector('#user-menu-dropdown-mobile');

                if (!userMenuPanel || !userMenuToggle) {
                    return;
                }

                var isOpen = userMenuPanel.classList.contains('show');
                userMenuPanel.classList.toggle('show', !isOpen);
                userMenuToggle.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
            };

            document.addEventListener('click', function (event) {
                var userMenuPanel = document.querySelector('#user-menu-panel-mobile');
                var clickedInsideMenu = event.target.closest('#user-menu-wrapper-mobile');
                if (!clickedInsideMenu && userMenuPanel && userMenuPanel.classList.contains('show')) {
                    userMenuPanel.classList.remove('show');
                    var toggleButton = document.querySelector('#user-menu-dropdown-mobile');
                    if (toggleButton) {
                        toggleButton.setAttribute('aria-expanded', 'false');
                    }
                }
            });
        })();
    </script>

    <script>
        (function () {
            var notifiers = [];
            var hasFetchedNotifications = false;

            function initNotifier(config) {
                var notifPanel = document.getElementById(config.panelId);
                var notifToggle = document.getElementById(config.toggleId);
                var notifList = document.getElementById(config.listId);
                var notifBadge = document.getElementById(config.badgeId);
                var readAllBtn = document.getElementById(config.readAllId);

                if (!notifPanel || !notifToggle || !notifList || !notifBadge || !readAllBtn) {
                    return null;
                }

                return {
                    wrapperSelector: config.wrapperSelector,
                    notifPanel: notifPanel,
                    notifToggle: notifToggle,
                    notifList: notifList,
                    notifBadge: notifBadge,
                    readAllBtn: readAllBtn,
                };
            }

            var desktopNotifier = initNotifier({
                wrapperSelector: '#notif-menu-wrapper',
                panelId: 'notif-menu-panel',
                toggleId: 'notif-menu-dropdown',
                listId: 'dtmNotifList',
                badgeId: 'dtmNotifBadge',
                readAllId: 'dtmNotifReadAll',
            });

            var mobileNotifier = initNotifier({
                wrapperSelector: '#notif-menu-wrapper-mobile',
                panelId: 'notif-menu-panel-mobile',
                toggleId: 'notif-menu-dropdown-mobile',
                listId: 'dtmNotifListMobile',
                badgeId: 'dtmNotifBadgeMobile',
                readAllId: 'dtmNotifReadAllMobile',
            });

            if (desktopNotifier) notifiers.push(desktopNotifier);
            if (mobileNotifier) notifiers.push(mobileNotifier);
            if (!notifiers.length) {
                return;
            }

            window.toggleDashboardNotifMenu = function (event) {
                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                if (!desktopNotifier) {
                    return;
                }

                toggleNotifierMenu(desktopNotifier, true);
            };

            window.toggleDashboardNotifMenuMobile = function (event) {
                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                if (!mobileNotifier) {
                    return;
                }

                toggleNotifierMenu(mobileNotifier, true);
            };

            function csrfToken() {
                var meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.getAttribute('content') : '';
            }

            function renderBadge(count) {
                var unread = Number(count || 0);
                notifiers.forEach(function (entry) {
                    if (unread > 0) {
                        entry.notifBadge.hidden = false;
                        entry.notifBadge.textContent = unread > 99 ? '99+' : String(unread);
                    } else {
                        entry.notifBadge.hidden = true;
                        entry.notifBadge.textContent = '0';
                    }
                });
            }

            function renderItems(items) {
                function esc(value) {
                    return String(value || '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function safeUrl(value) {
                    var url = String(value || '');
                    if (!url || url === '#') {
                        return 'javascript:void(0)';
                    }

                    if (url.indexOf('/') === 0 || url.indexOf('http://') === 0 || url.indexOf('https://') === 0) {
                        return url;
                    }

                    return 'javascript:void(0)';
                }

                if (!Array.isArray(items) || items.length === 0) {
                    notifiers.forEach(function (entry) {
                        entry.notifList.innerHTML = '<div class="dtm-notif-empty">لا توجد إشعارات حالياً.</div>';
                    });
                    return;
                }

                var html = items.map(function (item) {
                    var itemUrl = safeUrl(item.url);
                    var unreadClass = item.read_at ? '' : ' unread';

                    return '' +
                        '<a href="' + itemUrl + '" class="dtm-notif-item' + unreadClass + '" data-notif-id="' + item.id + '">' +
                            '<div class="dtm-notif-title">' + esc(item.title || 'إشعار جديد') + '</div>' +
                            '<div class="dtm-notif-body">' + esc(item.body || '') + '</div>' +
                            '<div class="dtm-notif-meta">' +
                                '<div class="dtm-notif-time">' + esc(item.created_at || '') + '</div>' +
                                '<span class="dtm-notif-dot" aria-hidden="true"></span>' +
                            '</div>' +
                        '</a>';
                }).join('');

                notifiers.forEach(function (entry) {
                    entry.notifList.innerHTML = html;
                });
            }

            function toggleNotifierMenu(entry, shouldFetch) {
                var isOpen = entry.notifPanel.classList.contains('show');

                notifiers.forEach(function (other) {
                    if (other !== entry) {
                        other.notifPanel.classList.remove('show');
                        other.notifToggle.setAttribute('aria-expanded', 'false');
                    }
                });

                entry.notifPanel.classList.toggle('show', !isOpen);
                entry.notifToggle.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');

                if (!isOpen && shouldFetch) {
                    renderBadge(0);
                    markAllAsRead({ refresh: false, optimisticUi: true });

                    if (!hasFetchedNotifications) {
                        fetchNotifications();
                    }
                }
            }

            var notificationsPollTimer = null;
            var notificationsPollingDisabled = false;
            var notificationsFailureCount = 0;
            var notificationsBaseInterval = 30000;
            var notificationsMaxBackoff = 300000;

            function clearNotificationsTimer() {
                if (notificationsPollTimer) {
                    clearTimeout(notificationsPollTimer);
                    notificationsPollTimer = null;
                }
            }

            function scheduleNotificationsPoll(delay) {
                if (notificationsPollingDisabled) {
                    return;
                }

                clearNotificationsTimer();
                notificationsPollTimer = setTimeout(function () {
                    fetchNotifications();
                }, typeof delay === 'number' ? delay : notificationsBaseInterval);
            }

            function notificationsBackoffDelay() {
                var exponent = Math.max(0, notificationsFailureCount - 1);
                return Math.min(notificationsBaseInterval * Math.pow(2, exponent), notificationsMaxBackoff);
            }

            function fetchNotifications() {
                if (notificationsPollingDisabled) {
                    return;
                }

                if (document.hidden || (typeof navigator !== 'undefined' && navigator.onLine === false)) {
                    scheduleNotificationsPoll(notificationsBaseInterval);
                    return;
                }

                fetch("{{ route('dashboard.notifications.index') }}", {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function (response) {
                    if (response.status === 403) {
                        notificationsPollingDisabled = true;
                        clearNotificationsTimer();
                        return null;
                    }

                    if (!response.ok) {
                        throw new Error('Failed to fetch notifications');
                    }
                    return response.json();
                })
                .then(function (data) {
                    if (!data) {
                        scheduleNotificationsPoll(notificationsBaseInterval);
                        return;
                    }

                    notificationsFailureCount = 0;
                    hasFetchedNotifications = true;

                    renderBadge(data.unread_count || 0);
                    renderItems(data.items || []);

                    scheduleNotificationsPoll(notificationsBaseInterval);
                })
                .catch(function () {
                    notificationsFailureCount += 1;
                    scheduleNotificationsPoll(notificationsBackoffDelay());
                });
            }

            function markNotificationAsRead(id) {
                if (!id) {
                    return Promise.resolve();
                }

                return fetch("{{ url('dashboard/notifications') }}/" + id + "/read", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function (response) {
                    if (!response.ok) {
                        return;
                    }
                    return response.json();
                })
                .then(function (data) {
                    if (data && typeof data.unread_count !== 'undefined') {
                        renderBadge(data.unread_count || 0);
                    }
                })
                .catch(function () {});
            }

            function markAllAsRead(options) {
                var opts = options || {};
                var shouldRefresh = typeof opts.refresh === 'boolean' ? opts.refresh : true;
                var optimisticUi = typeof opts.optimisticUi === 'boolean' ? opts.optimisticUi : true;

                if (optimisticUi) {
                    notifiers.forEach(function (entry) {
                        var unreadItems = entry.notifList.querySelectorAll('.dtm-notif-item.unread');
                        unreadItems.forEach(function (item) {
                            item.classList.remove('unread');
                        });
                    });
                }

                fetch("{{ route('dashboard.notifications.read-all') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Failed to mark all as read');
                    }
                    return response.json();
                })
                .then(function () {
                    renderBadge(0);
                    if (shouldRefresh) {
                        fetchNotifications();
                    }
                })
                .catch(function () {});
            }

            notifiers.forEach(function (entry) {
                entry.readAllBtn.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    markAllAsRead();
                });

                entry.notifList.addEventListener('click', function (event) {
                    var link = event.target.closest('.dtm-notif-item');
                    if (!link) {
                        return;
                    }

                    var id = link.getAttribute('data-notif-id');
                    markNotificationAsRead(id);
                });
            });

            document.addEventListener('click', function (event) {
                notifiers.forEach(function (entry) {
                    var clickedInsideNotif = event.target.closest(entry.wrapperSelector);
                    if (!clickedInsideNotif && entry.notifPanel.classList.contains('show')) {
                        entry.notifPanel.classList.remove('show');
                        entry.notifToggle.setAttribute('aria-expanded', 'false');
                    }
                });
            });

            window.addEventListener('online', function () {
                if (notificationsPollingDisabled) {
                    return;
                }

                notificationsFailureCount = 0;
                fetchNotifications();
            });

            document.addEventListener('visibilitychange', function () {
                if (notificationsPollingDisabled) {
                    return;
                }

                if (!document.hidden) {
                    fetchNotifications();
                }
            });

            fetchNotifications();
        })();
    </script>

    <script>
        // Modern sidebar toggle delegates to the existing #sidebar-toggle behavior
        (function () {
            function bind() {
                var modernBtn = document.getElementById('sidebar-toggle-modern');
                if (!modernBtn) return;
                if (modernBtn.dataset.bound === '1') return;
                modernBtn.dataset.bound = '1';

                modernBtn.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    // Primary: mimic admin.js behavior directly (most reliable)
                    var sidebar = document.querySelector('.page-sidebar');
                    var header = document.querySelector('.page-main-header');
                    if (sidebar) sidebar.classList.toggle('open');
                    if (header) header.classList.toggle('open');

                    // Fallback: try triggering the legacy handler as well (if bound)
                    var classicIcon = document.getElementById('sidebar-toggle');
                    if (!classicIcon) return;
                    if (window.jQuery) {
                        window.jQuery(classicIcon).triggerHandler('click');
                        return;
                    }
                    classicIcon.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
                });
            }

            function bindMobile() {
                var mobileBtn = document.getElementById('sidebar-toggle-modern-mobile');
                if (!mobileBtn) return;
                if (mobileBtn.dataset.bound === '1') return;
                mobileBtn.dataset.bound = '1';

                mobileBtn.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    var sidebar = document.querySelector('.page-sidebar');
                    var header = document.querySelector('.page-main-header');
                    if (sidebar) sidebar.classList.toggle('open');
                    if (header) header.classList.toggle('open');

                    var classicIcon = document.getElementById('sidebar-toggle');
                    if (!classicIcon) return;
                    if (window.jQuery) {
                        window.jQuery(classicIcon).triggerHandler('click');
                        return;
                    }
                    classicIcon.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    bind();
                    bindMobile();
                }, { once: true });
            } else {
                bind();
                bindMobile();
            }
        })();
    </script>

    <script>
        // Sidebar UI mode switch (classic/modern) - hosted in page-sidebar
        (function () {
            function applyColorModeClasses(colorMode) {
                var mode = colorMode === 'dark' ? 'dark' : 'light';
                var root = document.documentElement;
                var isModern = root.classList.contains('ui-modern');

                root.classList.remove('ui-dark', 'ui-light');
                root.classList.add(mode === 'dark' ? 'ui-dark' : 'ui-light');
                root.setAttribute('data-ui-color-mode', mode);
                root.setAttribute('data-bs-theme', (isModern && mode === 'dark') ? 'dark' : 'light');

                if (document.body) {
                    document.body.classList.remove('ui-dark', 'ui-light');
                    document.body.classList.add(mode === 'dark' ? 'ui-dark' : 'ui-light');
                    document.body.setAttribute('data-ui-color-mode', mode);
                    document.body.setAttribute('data-bs-theme', (isModern && mode === 'dark') ? 'dark' : 'light');
                }

                window.__UI_COLOR_MODE_EFFECTIVE__ = mode;
            }

            function applyModeClasses(mode) {
                var root = document.documentElement;
                root.classList.remove('ui-modern', 'ui-classic');
                root.classList.add(mode === 'modern' ? 'ui-modern' : 'ui-classic');
                root.setAttribute('data-ui-mode', mode);

                if (document.body) {
                    document.body.classList.remove('ui-modern', 'ui-classic');
                    document.body.classList.add(mode === 'modern' ? 'ui-modern' : 'ui-classic');
                    document.body.setAttribute('data-ui-mode', mode);
                }

                var currentColor = window.__UI_COLOR_MODE_EFFECTIVE__ || 'light';
                applyColorModeClasses(currentColor);
            }

            function bindColorToggle() {
                var colorToggle = document.getElementById('sidebarColorModeToggle');
                if (!colorToggle) return;
                if (colorToggle.dataset.bound === '1') return;
                colorToggle.dataset.bound = '1';

                var mode = window.__UI_COLOR_MODE_EFFECTIVE__ || 'light';
                applyColorModeClasses(mode);

                colorToggle.checked = mode === 'dark';
                colorToggle.setAttribute('aria-pressed', colorToggle.checked ? 'true' : 'false');

                colorToggle.addEventListener('change', function (e) {
                    var nextMode = e.target && e.target.checked ? 'dark' : 'light';
                    colorToggle.setAttribute('aria-pressed', nextMode === 'dark' ? 'true' : 'false');

                    try {
                        localStorage.setItem('ui_color_mode', nextMode);
                    } catch (err) {
                        // ignore
                    }

                    applyColorModeClasses(nextMode);
                });
            }

            function bind() {
                var toggle = document.getElementById('sidebarUiModeToggle');
                if (!toggle) return;
                if (toggle.dataset.bound === '1') return;
                toggle.dataset.bound = '1';

                var mode = window.__UI_MODE_EFFECTIVE__ || (document.documentElement.classList.contains('ui-modern') ? 'modern' : 'classic');
                toggle.checked = mode === 'modern';
                toggle.setAttribute('aria-pressed', toggle.checked ? 'true' : 'false');

                // On homepage, the page-specific switcher handles animated switching + CSS loading
                if (window.__UI_MODE_IS_HOMEPAGE__) {
                    return;
                }

                toggle.addEventListener('change', function (e) {
                    var nextMode = e.target && e.target.checked ? 'modern' : 'classic';
                    toggle.setAttribute('aria-pressed', nextMode === 'modern' ? 'true' : 'false');

                    try {
                        localStorage.setItem('ui_mode', nextMode);
                        localStorage.setItem('ui_mode_pending', '1');
                    } catch (err) {
                        // ignore
                    }

                    applyModeClasses(nextMode);
                    window.location.reload();
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    bind();
                    bindColorToggle();
                }, { once: true });
            } else {
                bind();
                bindColorToggle();
            }
        })();
    </script>

    <script>
        // Modern header: compute and apply content offset to avoid overlap (handles wrapping on small screens)
        (function () {
            function updateOffset() {
                var root = document.documentElement;
                if (!root.classList.contains('ui-modern')) return;

                var isMobile = window.matchMedia && window.matchMedia('(max-width: 991px)').matches;
                var bar = document.querySelector(isMobile ? '.dashboard-topbar-mobile' : '.dashboard-topbar');
                if (!bar) return;

                var rect = bar.getBoundingClientRect();
                if (!rect || !rect.bottom) return;

                var gap = 12;
                var offset = Math.ceil(rect.bottom + gap);
                root.style.setProperty('--dashboard-topbar-offset', offset + 'px');

                if (isMobile) {
                    var mb = document.querySelector('.dashboard-mobilebar');
                    if (mb) {
                        var mbr = mb.getBoundingClientRect();
                        var bottomGap = 12;
                        var bottomOffset = Math.ceil((window.innerHeight - mbr.top) + bottomGap);
                        root.style.setProperty('--dashboard-mobilebar-offset', bottomOffset + 'px');
                    }
                }
            }

            var rafId = 0;
            function schedule() {
                if (rafId) cancelAnimationFrame(rafId);
                rafId = requestAnimationFrame(function () {
                    rafId = 0;
                    updateOffset();
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', schedule, { once: true });
            } else {
                schedule();
            }

            window.addEventListener('resize', schedule);
        })();
    </script>

    <script>

        $(document).keypress(
 function(event){
   if (event.which == '13') {
     event.preventDefault();
   }
});
       $('.auto-translate').each(function () {
           $(this).on('click', function () {
               if(confirm('This will overwrite any translated property!')) {
                   $(this).addClass('disabled')
                   let $icon = $(this).find('.icon')
                   $icon.removeClass('fa-language')
                   $icon.addClass('fa-spinner fa-spin')
                   axios.post("{{ route('dashboard.model.auto.translate') }}", {
                       model: $(this).data('model'),
                       id: $(this).data('id'),
                   })
                       .then(response=> toastr.success(response.data.message))
                       .catch(error=> toastr.error(error?.response?.data?.message || "Unexpected Error!"))
                       .finally(()=> {
                           $(this).removeClass('disabled')
                           $icon.addClass('fa-language')
                           $icon.removeClass('fa-spinner fa-spin')
                       })
               }
           })
       })
       $(document).ready(function () {
  $(".js-example-basic-single").select2();
});
   </script>
   <script>
    setInterval(function() {
        fetch('/keep-alive', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: 'ping' })
        }).then(response => {
            if (!response.ok) {
                console.log('Failed to send ping');
            }
        }).catch(error => {
            console.log('Error:', error);
        });
    }, 120000);
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    @isset($dataTable)
        <x-dashboard.partials.delete-resource-modal />
        {!! $dataTable->scripts() !!}
    @endisset
    @stack('js')
    
    
    <!-- Ace Editor CDN -->
@if ($loadAceAssets)
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/ace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/ext-language_tools.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/mode-css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/worker-css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/beautify-css.min.js"></script>
@endif

<script>
    document.addEventListener('hide.bs.modal', function (event) {
        var active = document.activeElement;
        if (active && event.target && event.target.contains(active) && typeof active.blur === 'function') {
            active.blur();
        }
    });
</script>

</body>

</html>

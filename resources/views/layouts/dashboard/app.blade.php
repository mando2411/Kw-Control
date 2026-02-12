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
@endphp

<html lang="{{ app()->getLocale() }}" class="ui-{{ $uiModeServer }}" data-ui-mode="{{ $uiModeServer }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="New vision Dashboard">
    <meta name="keywords" content="admin dashboard, New vision, web app">
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
            var mode = SERVER_MODE || 'classic';
            var pending = null;

            // Global UI policy can force the mode regardless of stored user preference
            if (UI_POLICY === 'modern' || UI_POLICY === 'classic') {
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
                pending = localStorage.getItem(PENDING_KEY);
                var local = localStorage.getItem(STORAGE_KEY);
                if (pending && (local === 'classic' || local === 'modern')) {
                    mode = local;
                }
            } catch (e) {
                // ignore
            }

            // Apply early to <html> (and later we also set on <body>)
            var root = document.documentElement;
            root.classList.remove('ui-modern', 'ui-classic');
            root.classList.add(mode === 'modern' ? 'ui-modern' : 'ui-classic');
            root.setAttribute('data-ui-mode', mode);

            window.__UI_MODE_SERVER__ = SERVER_MODE;
            window.__UI_MODE_EFFECTIVE__ = mode;
            window.__UI_MODE_POLICY__ = UI_POLICY;
            window.__UI_MODE_IS_AUTH__ = IS_AUTH;
            window.__UI_MODE_IS_HOMEPAGE__ = IS_HOMEPAGE;

            // Preload homepage modern css (fetch only, no apply)
            if (IS_HOMEPAGE) {
                var preloadId = 'homeModernCssPreload';
                if (!document.getElementById(preloadId)) {
                    var preload = document.createElement('link');
                    preload.id = preloadId;
                    preload.rel = 'preload';
                    preload.as = 'style';
                    preload.href = "{{ asset('assets/css/home-modern.css') }}";
                    document.head.appendChild(preload);
                }

                // If mode is modern, apply stylesheet early to avoid white flash
                if (mode === 'modern') {
                    var cssId = 'homeModernCss';
                    if (!document.getElementById(cssId)) {
                        var link = document.createElement('link');
                        link.id = cssId;
                        link.rel = 'stylesheet';
                        link.href = "{{ asset('assets/css/home-modern.css') }}";
                        document.head.appendChild(link);
                    }
                }
            }

            // Keep localStorage in sync for instant loads
            try {
                localStorage.setItem(STORAGE_KEY, mode);
            } catch (e) {
                // ignore
            }
        })();
    </script>
    {{--    <link rel="icon" href="assets/images/dashboard/favicon.png" type="image/x-icon"> --}}
    {{--    <link rel="shortcut icon" href="assets/images/dashboard/favicon.png" type="image/x-icon"> --}}
    <title>{{ setting(\App\Enums\SettingKey::SITE_TITLE->value, true) }} | Dashboard</title>

    <!-- Google font-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,500;1,600;1,700;1,800;1,900&display=swap">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/admin.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/all.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/style.css') }}">
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

        /* === UI Modern Palette (single source of truth) === */
        html.ui-modern,
        body.ui-modern {
            --ui-ink: rgba(15, 23, 42, 0.92);
            --ui-muted: rgba(71, 85, 105, 0.92);
            --ui-surface: rgba(255, 255, 255, 0.98);
            --ui-surface-2: rgba(255, 255, 255, 0.94);
            --ui-border: rgba(15, 23, 42, 0.10);
            --ui-shadow: 0 24px 60px rgba(2, 6, 23, 0.16);

            --ui-accent: rgba(14, 165, 233, 0.95);
            --ui-accent-soft: rgba(14, 165, 233, 0.12);
            --ui-warm: rgba(245, 158, 11, 0.92);
            --ui-warm-soft: rgba(245, 158, 11, 0.10);
            --ui-success: rgba(25, 135, 84, 0.95);
            --ui-danger: rgba(220, 53, 69, 0.95);

            --ui-dark-1: rgba(2, 6, 23, 0.94);
            --ui-dark-2: rgba(15, 23, 42, 0.88);
            --ui-dark-border: rgba(255, 255, 255, 0.10);
        }

        /* Modern sidebar toggle (UI mode) */
        .sidebar-toggle-modern {
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
        body.ui-modern .page-sidebar .sidebar-ui-mode {
            margin: 8px 12px 10px;
            padding: 10px 12px;
            border-radius: 14px;
            background: var(--ui-surface);
            border: 1px solid var(--ui-border);
            box-shadow: 0 10px 22px rgba(2, 6, 23, 0.06);
        }

        html.ui-modern .page-sidebar .sidebar-ui-mode .form-check-label,
        body.ui-modern .page-sidebar .sidebar-ui-mode .form-check-label {
            color: var(--ui-ink);
            font-weight: 800;
            cursor: pointer;
        }

        html.ui-modern .page-sidebar .sidebar-ui-mode .form-check-input,
        body.ui-modern .page-sidebar .sidebar-ui-mode .form-check-input {
            cursor: pointer;
        }

        html.ui-modern .page-sidebar .sidebar-ui-mode .form-check-input:checked,
        body.ui-modern .page-sidebar .sidebar-ui-mode .form-check-input:checked {
            background-color: rgba(14, 165, 233, 0.95);
            border-color: rgba(14, 165, 233, 0.95);
        }

        html.ui-modern .page-sidebar .sidebar-ui-mode .form-check-input:focus,
        body.ui-modern .page-sidebar .sidebar-ui-mode .form-check-input:focus {
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
                z-index: 1030;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-inner,
            body.ui-modern .dashboard-topbar-mobile .dtm-inner {
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                padding: 10px 12px;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-brand,
            body.ui-modern .dashboard-topbar-mobile .dtm-brand {
                text-decoration: none;
                color: var(--ui-ink);
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-weight: 900;
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
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-logout,
            body.ui-modern .dashboard-topbar-mobile .dtm-logout {
                width: 42px;
                height: 42px;
                border-radius: 999px;
                border: 1px solid var(--ui-border);
                background: var(--ui-surface-2);
                color: var(--ui-danger);
                box-shadow: 0 10px 22px rgba(2, 6, 23, 0.08);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-logout i,
            body.ui-modern .dashboard-topbar-mobile .dtm-logout i {
                font-size: 1rem;
                color: var(--ui-danger);
            }

            html.ui-modern .dashboard-topbar-mobile .dtm-avatar,
            body.ui-modern .dashboard-topbar-mobile .dtm-avatar {
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
</head>

<body class="ui-{{ $uiModeServer }}" data-ui-mode="{{ $uiModeServer }}">
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
    <script src="{{ asset('assets/admin/js/ace/ace.js') }}"></script>
    <script src="{{ asset('assets/admin/js/ace/mode-css.js') }}"></script>
    <script src="{{ asset('assets/admin/js/ace/worker-css.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/ace-builds@1.32.6/src-min/snippets/css.js"></script>
    <script src="{{ asset('assets/admin/js/ace/ext-language_tools.js') }}"></script>
    <script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.easing.1.3.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/admin/js/main.js') }}?v={{ filemtime(public_path('assets/admin/js/main.js')) }}"></script>
    <!-- Bootstrap JS already loaded via assets/admin/js/bootstrap.bundle.min.js -->









    @stack('js-upper')
    <!--script admin-->
    <script>
        window.supportedLocales = {!! collect(config('translatable.locales'))->toJson() !!}
    </script>
    <!-- Tagsinput -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css"/>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    <script src="{{ asset('assets/admin/js/admin.js') }}"></script>

    <script>
        // Persist effective UI mode to DB (cross-device) and finalize <body> classes
        (function () {
            var IS_AUTH = !!window.__UI_MODE_IS_AUTH__;
            if (!IS_AUTH) {
                // still ensure body mirrors <html>
                var mode = window.__UI_MODE_EFFECTIVE__ || 'classic';
                document.body.classList.remove('ui-modern', 'ui-classic');
                document.body.classList.add(mode === 'modern' ? 'ui-modern' : 'ui-classic');
                document.body.setAttribute('data-ui-mode', mode);
                return;
            }

            var mode = window.__UI_MODE_EFFECTIVE__ || 'classic';
            var server = window.__UI_MODE_SERVER__ || 'classic';
            var policy = window.__UI_MODE_POLICY__ || 'user_choice';

            document.body.classList.remove('ui-modern', 'ui-classic');
            document.body.classList.add(mode === 'modern' ? 'ui-modern' : 'ui-classic');
            document.body.setAttribute('data-ui-mode', mode);

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
                document.addEventListener('DOMContentLoaded', bind, { once: true });
            } else {
                bind();
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/ace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/ext-language_tools.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/mode-css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/worker-css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/beautify-css.min.js"></script>

</body>

</html>

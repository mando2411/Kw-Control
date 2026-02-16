@php
  $uiThemeLight = [
    'btn_primary' => '#0ea5e9',
    'btn_secondary' => '#6366f1',
    'btn_tertiary' => '#14b8a6',
    'btn_quaternary' => '#f59e0b',
    'text_primary' => '#0f172a',
    'text_secondary' => '#475569',
    'bg_primary' => '#ffffff',
    'bg_secondary' => '#f8fafc',
  ];

  $uiThemeDark = [
    'btn_primary' => '#38bdf8',
    'btn_secondary' => '#818cf8',
    'btn_tertiary' => '#2dd4bf',
    'btn_quaternary' => '#fbbf24',
    'text_primary' => '#f1f5f9',
    'text_secondary' => '#cbd5e1',
    'bg_primary' => '#0f172a',
    'bg_secondary' => '#1e293b',
  ];

  $uiModernSurface = [
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
    'shadow' => '0 20px 45px rgba(2, 6, 23, 0.14)',
  ];

  $uiFontScale = [
    'xs' => '0.75rem',
    'sm' => '0.875rem',
    'base' => '1rem',
    'lg' => '1.125rem',
    'xl' => '1.25rem',
  ];
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl" class="ui-modern ui-light" data-ui-mode="modern" data-ui-color-mode="light" data-bs-theme="light">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{$contractor->name}}</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/all.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/modern-theme-system.css') }}?v={{ filemtime(public_path('assets/css/modern-theme-system.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/dashboard-modern-fallback.css') }}?v={{ filemtime(public_path('assets/css/dashboard-modern-fallback.css')) }}">

    <script>
      (function () {
        var root = document.documentElement;
        var colorMode = 'light';

        try {
          var storedColor = localStorage.getItem('ui_color_mode');
          if (storedColor === 'dark' || storedColor === 'light') {
            colorMode = storedColor;
          }
        } catch (e) {}

        root.classList.remove('ui-light', 'ui-dark');
        root.classList.add(colorMode === 'dark' ? 'ui-dark' : 'ui-light');
        root.setAttribute('data-ui-color-mode', colorMode);
        root.setAttribute('data-bs-theme', colorMode === 'dark' ? 'dark' : 'light');

        document.addEventListener('DOMContentLoaded', function () {
          if (!document.body) return;
          document.body.classList.remove('ui-light', 'ui-dark');
          document.body.classList.add('ui-modern');
          document.body.classList.add(colorMode === 'dark' ? 'ui-dark' : 'ui-light');
          document.body.setAttribute('data-ui-mode', 'modern');
          document.body.setAttribute('data-ui-color-mode', colorMode);
          document.body.setAttribute('data-bs-theme', colorMode === 'dark' ? 'dark' : 'light');
        });
      })();
    </script>

    @php
      $uiThemeLight = (isset($uiThemeLight) && is_array($uiThemeLight)) ? $uiThemeLight : [
        'btn_primary' => '#0ea5e9',
        'btn_secondary' => '#6366f1',
        'btn_tertiary' => '#14b8a6',
        'btn_quaternary' => '#f59e0b',
        'text_primary' => '#0f172a',
        'text_secondary' => '#475569',
        'bg_primary' => '#ffffff',
        'bg_secondary' => '#f8fafc',
      ];

      $uiThemeDark = (isset($uiThemeDark) && is_array($uiThemeDark)) ? $uiThemeDark : [
        'btn_primary' => '#38bdf8',
        'btn_secondary' => '#818cf8',
        'btn_tertiary' => '#2dd4bf',
        'btn_quaternary' => '#fbbf24',
        'text_primary' => '#f1f5f9',
        'text_secondary' => '#cbd5e1',
        'bg_primary' => '#0f172a',
        'bg_secondary' => '#1e293b',
      ];

      $uiModernSurface = (isset($uiModernSurface) && is_array($uiModernSurface)) ? $uiModernSurface : [
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
        'shadow' => '0 20px 45px rgba(2, 6, 23, 0.14)',
      ];

      $uiFontScale = (isset($uiFontScale) && is_array($uiFontScale)) ? $uiFontScale : [
        'xs' => '0.75rem',
        'sm' => '0.875rem',
        'base' => '1rem',
        'lg' => '1.125rem',
        'xl' => '1.25rem',
      ];
    @endphp

    <style>
      html.ui-modern,
      body.ui-modern {
        --ui-ink: {{ $uiThemeLight['text_primary'] }};
        --ui-muted: {{ $uiThemeLight['text_secondary'] }};
        --ui-surface: {{ $uiThemeLight['bg_primary'] }};
        --ui-surface-2: {{ $uiThemeLight['bg_secondary'] }};
        --ui-border: {{ $uiModernSurface['border_light'] }};
        --ui-shadow: {{ $uiModernSurface['shadow'] }};
        --ui-accent: {{ $uiThemeLight['btn_primary'] }};
        --ui-btn-primary: {{ $uiThemeLight['btn_primary'] }};
        --ui-btn-secondary: {{ $uiThemeLight['btn_secondary'] }};
        --ui-btn-tertiary: {{ $uiThemeLight['btn_tertiary'] }};
        --ui-btn-quaternary: {{ $uiThemeLight['btn_quaternary'] }};
        --ui-text-primary: {{ $uiThemeLight['text_primary'] }};
        --ui-text-secondary: {{ $uiThemeLight['text_secondary'] }};
        --ui-bg-primary: {{ $uiThemeLight['bg_primary'] }};
        --ui-bg-secondary: {{ $uiThemeLight['bg_secondary'] }};
        --ui-link: {{ $uiModernSurface['link_light'] }};
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
      }

      html.ui-modern.ui-dark,
      body.ui-modern.ui-dark {
        --ui-ink: {{ $uiThemeDark['text_primary'] }};
        --ui-muted: {{ $uiThemeDark['text_secondary'] }};
        --ui-surface: {{ $uiThemeDark['bg_primary'] }};
        --ui-surface-2: {{ $uiThemeDark['bg_secondary'] }};
        --ui-border: {{ $uiModernSurface['border_dark'] }};
        --ui-accent: {{ $uiThemeDark['btn_primary'] }};
        --ui-btn-primary: {{ $uiThemeDark['btn_primary'] }};
        --ui-btn-secondary: {{ $uiThemeDark['btn_secondary'] }};
        --ui-btn-tertiary: {{ $uiThemeDark['btn_tertiary'] }};
        --ui-btn-quaternary: {{ $uiThemeDark['btn_quaternary'] }};
        --ui-text-primary: {{ $uiThemeDark['text_primary'] }};
        --ui-text-secondary: {{ $uiThemeDark['text_secondary'] }};
        --ui-bg-primary: {{ $uiThemeDark['bg_primary'] }};
        --ui-bg-secondary: {{ $uiThemeDark['bg_secondary'] }};
        --ui-link: {{ $uiModernSurface['link_dark'] }};
        color-scheme: dark;
      }

      body.ui-modern {
        background: var(--ui-surface, #fff);
        color: var(--ui-ink, #0f172a);
      }
    </style>

  </head>
  <style>
    .contractor-profile-page {
      direction: rtl;
      text-align: right;
    }

    .contractor-page-container {
      width: min(var(--ui-container-max, 1320px), 95vw);
      margin-inline: auto;
    }

    .contractor-layout-block {
      width: min(var(--ui-container-max, 1320px), 95vw);
      margin: 1rem auto;
      background: var(--ui-bg-primary, #fff);
      border: 1px solid var(--ui-border, #dbe3ef);
      border-radius: calc(var(--ui-radius-card, 1rem) + 0.15rem);
      box-shadow: 0 12px 28px rgba(2, 6, 23, 0.09);
      padding: clamp(0.85rem, 1.8vw, 1.3rem);
    }

    .contractor-layout-block .table-responsive {
      border-radius: var(--ui-radius-card, 1rem);
      overflow: auto;
    }

    .contractor-top-cta {
      background: linear-gradient(120deg, var(--ui-btn-primary, #0ea5e9), var(--ui-btn-secondary, #6366f1), var(--ui-btn-tertiary, #14b8a6));
      color: #fff;
      padding: 1rem 1rem;
      box-shadow: 0 14px 34px rgba(2, 6, 23, 0.22);
      position: relative;
      overflow: hidden;
    }

    .contractor-top-cta::after {
      content: "";
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 14% 20%, rgba(255, 255, 255, 0.18), transparent 30%), radial-gradient(circle at 86% 78%, rgba(255, 255, 255, 0.14), transparent 32%);
      pointer-events: none;
    }

    .contractor-top-cta__inner {
      max-width: min(1280px, 94vw);
      margin: 0 auto;
      display: flex;
      direction: rtl;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      flex-wrap: wrap;
      position: relative;
      z-index: 1;
    }

    .contractor-top-cta__text {
      font-size: clamp(0.96rem, 1.6vw, 1.15rem);
      font-weight: 900;
      margin: 0;
      letter-spacing: -0.01em;
      display: flex;
      align-items: center;
      gap: 0.45rem;
    }

    .contractor-top-cta__offer {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      margin: 0 0 0.45rem;
      padding: 0.36rem 0.8rem;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.5);
      color: #fff;
      font-size: clamp(0.84rem, 1.4vw, 0.98rem);
      font-weight: 900;
      letter-spacing: -0.01em;
      direction: rtl;
      unicode-bidi: plaintext;
    }

    .contractor-top-cta__offer .ltr-fragment {
      direction: ltr;
      unicode-bidi: isolate;
      display: inline-block;
    }

    .contractor-top-cta__hint {
      margin: 0;
      font-size: clamp(0.78rem, 1.2vw, 0.9rem);
      font-weight: 600;
      opacity: 0.94;
    }

    .contractor-top-cta__link {
      color: #0f172a;
      background: linear-gradient(135deg, #ffffff, rgba(255, 255, 255, 0.88));
      border: 1px solid rgba(255, 255, 255, 0.55);
      border-radius: 999px;
      padding: 0.58rem 1.2rem;
      font-weight: 900;
      font-size: 0.94rem;
      text-decoration: none;
      transition: transform 180ms ease, background-color 180ms ease, box-shadow 180ms ease;
      white-space: nowrap;
      box-shadow: 0 10px 24px rgba(2, 6, 23, 0.2);
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
    }

    .contractor-top-cta__link:hover {
      color: #0f172a;
      background: #ffffff;
      transform: translateY(-2px);
      box-shadow: 0 14px 30px rgba(2, 6, 23, 0.26);
    }

    .contractor-hero-wrap {
      padding: 1.25rem 0 0.3rem;
    }

    .contractor-hero {
      background: linear-gradient(145deg, var(--ui-bg-primary, #ffffff), var(--ui-bg-secondary, #f8fafc));
      border: 1px solid var(--ui-border, #dbe3ef);
      border-radius: calc(var(--ui-radius-card, 1rem) + 0.4rem);
      box-shadow: var(--ui-shadow, 0 20px 45px rgba(2, 6, 23, 0.14));
      padding: clamp(1rem, 2.2vw, 1.6rem);
      display: grid;
      grid-template-columns: minmax(92px, 140px) 1fr;
      gap: 1rem;
      align-items: center;
    }

    .contractor-hero-media {
      width: clamp(92px, 12vw, 140px);
      height: clamp(92px, 12vw, 140px);
      border-radius: 50%;
      overflow: hidden;
      border: 3px solid rgba(255, 255, 255, 0.9);
      box-shadow: 0 12px 30px rgba(2, 6, 23, 0.22);
      background: #e2e8f0;
    }

    .contractor-hero-media img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .contractor-hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      background: color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 14%, transparent);
      color: var(--ui-btn-primary, #0ea5e9);
      border: 1px solid color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 36%, transparent);
      border-radius: 999px;
      padding: 0.25rem 0.75rem;
      font-size: 0.78rem;
      font-weight: 800;
      margin-bottom: 0.35rem;
    }

    .contractor-hero-name {
      margin: 0;
      color: var(--ui-text-primary, #0f172a);
      font-size: clamp(1.25rem, 2.6vw, 2rem);
      font-weight: 900;
      line-height: 1.15;
      letter-spacing: -0.02em;
    }

    .contractor-hero-sub {
      margin: 0.25rem 0 0;
      color: var(--ui-text-secondary, #475569);
      font-size: clamp(0.92rem, 1.8vw, 1.04rem);
      font-weight: 700;
    }

    .hero-countdown {
      margin-top: 0.85rem;
      padding: 0.75rem;
      border-radius: 0.9rem;
      border: 1px solid color-mix(in srgb, var(--ui-border, #dbe3ef) 85%, transparent);
      background: color-mix(in srgb, var(--ui-bg-secondary, #f8fafc) 88%, transparent);
    }

    .hero-countdown__title {
      margin: 0 0 0.55rem;
      color: var(--ui-text-primary, #0f172a);
      font-size: 0.9rem;
      font-weight: 800;
      line-height: 1.2;
    }

    .hero-countdown__grid {
      --bs-gutter-x: 0.5rem;
      --bs-gutter-y: 0.5rem;
    }

    .hero-countdown__item {
      border: 1px solid color-mix(in srgb, var(--ui-border, #dbe3ef) 80%, transparent);
      border-radius: 0.75rem;
      background: color-mix(in srgb, var(--ui-bg-primary, #fff) 92%, transparent);
      padding: 0.45rem 0.3rem;
      text-align: center;
      box-shadow: 0 6px 16px rgba(2, 6, 23, 0.06);
    }

    .hero-countdown__value {
      display: block;
      font-size: clamp(1rem, 1.8vw, 1.22rem);
      font-weight: 900;
      line-height: 1;
      color: var(--ui-text-primary, #0f172a);
    }

    .hero-countdown__label {
      display: block;
      margin-top: 0.2rem;
      font-size: 0.72rem;
      color: var(--ui-text-secondary, #475569);
      font-weight: 700;
    }

    .hero-countdown__date {
      margin-top: 0.5rem;
    }

    .hero-countdown__date p {
      margin: 0;
      font-size: 0.78rem;
      line-height: 1.35;
    }

    .voter-action-toggle {
      transition: background-color 220ms ease, border-color 220ms ease, color 220ms ease, transform 220ms ease, box-shadow 220ms ease;
      will-change: transform, background-color;
    }

    .voter-action-toggle:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 18px rgba(2, 6, 23, 0.15);
    }

    .voter-action-toggle.is-switching {
      animation: voterActionSwitch 320ms ease;
    }

    .contractor-tab-nav {
      display: flex;
      justify-content: center;
      gap: 0.55rem;
      margin: 0.4rem auto 0.85rem;
      flex-wrap: wrap;
    }

    .contractor-tab-btn {
      min-width: 130px;
    }

    .contractor-tab-pane {
      width: 100%;
    }

    .tab-content > .contractor-tab-pane {
      display: none;
    }

    .tab-content > .contractor-tab-pane.active,
    .tab-content > .contractor-tab-pane.show.active {
      display: block;
    }

    .contractor-tab-pane[hidden] {
      display: none !important;
    }

    .voter-name-details {
      background: transparent;
      border: 0;
      padding: 0;
      margin: 0;
      color: var(--ui-text-primary, #0f172a);
      font-size: clamp(1rem, 1.45vw, 1.12rem);
      font-weight: 900;
      line-height: 1.45;
      text-align: right;
      cursor: pointer;
      text-decoration: none;
      display: block;
      width: fit-content;
      max-width: 100%;
    }

    .voter-name-details:hover {
      color: var(--ui-btn-primary, #0ea5e9);
      text-decoration: underline;
      text-decoration-style: dotted;
      text-underline-offset: 3px;
    }

    .voter-inactive-flag {
      display: block;
      margin-top: 0.2rem;
      color: color-mix(in srgb, var(--ui-btn-quaternary, #f59e0b) 74%, #991b1b);
      font-size: 0.78rem;
      font-weight: 700;
    }

    .voter-inactive-flag:empty {
      display: none;
    }

    .search-relatives-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      margin-top: 0.48rem;
      padding: 0.2rem 0.55rem;
      border-radius: 999px;
      border: 1px solid color-mix(in srgb, var(--ui-border, #dbe3ef) 90%, transparent);
      background: color-mix(in srgb, var(--ui-bg-secondary, #f8fafc) 80%, transparent);
      color: var(--ui-text-secondary, #475569);
      font-size: 0.76rem;
      font-weight: 700;
      transition: all 180ms ease;
    }

    .search-relatives-btn:hover {
      color: var(--ui-btn-primary, #0ea5e9);
      border-color: color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 42%, transparent);
      background: color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 10%, transparent);
      transform: translateY(-1px);
    }

    .madameenTable {
      border: 1px solid color-mix(in srgb, var(--ui-border, #dbe3ef) 92%, transparent);
      border-radius: calc(var(--ui-radius-card, 1rem) - 0.05rem);
      background: color-mix(in srgb, var(--ui-bg-primary, #fff) 96%, transparent);
      padding: 0.4rem;
      box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--ui-border, #dbe3ef) 50%, transparent);
    }

    .contractor-voters-table {
      margin-bottom: 0;
      border-collapse: separate;
      border-spacing: 0 0.42rem;
      min-width: 760px;
    }

    .contractor-voters-table thead th {
      position: sticky;
      top: 0;
      z-index: 2;
      border: 0;
      background: linear-gradient(145deg,
        color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 14%, #ffffff),
        color-mix(in srgb, var(--ui-bg-secondary, #f8fafc) 96%, transparent));
      color: var(--ui-text-primary, #0f172a);
      font-size: 0.83rem;
      font-weight: 900;
      letter-spacing: -0.01em;
      padding: 0.78rem 0.75rem;
    }

    .contractor-voters-table tbody tr {
      background: color-mix(in srgb, var(--ui-bg-primary, #fff) 98%, transparent);
      box-shadow: 0 6px 16px rgba(2, 6, 23, 0.06);
      transition: transform 180ms ease, box-shadow 180ms ease;
    }

    .contractor-voters-table tbody tr:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 22px rgba(2, 6, 23, 0.1);
    }

    .contractor-voters-table tbody td {
      border: 0;
      padding: 0.78rem 0.7rem;
      vertical-align: middle;
      background: transparent;
    }

    .contractor-voters-table tbody tr td:first-child {
      border-top-right-radius: 0.72rem;
      border-bottom-right-radius: 0.72rem;
    }

    .contractor-voters-table tbody tr td:last-child {
      border-top-left-radius: 0.72rem;
      border-bottom-left-radius: 0.72rem;
    }

    .contractor-confirm-modal .modal-dialog {
      transform: translateY(14px) scale(0.98);
      transition: transform 220ms ease;
    }

    .contractor-confirm-modal.show .modal-dialog {
      transform: translateY(0) scale(1);
    }

    .contractor-confirm-modal .modal-content {
      border-radius: calc(var(--ui-radius-card, 1rem) + 0.2rem);
      border: 1px solid var(--ui-border, #dbe3ef);
      box-shadow: var(--ui-shadow, 0 20px 45px rgba(2, 6, 23, 0.14));
      background: var(--ui-bg-primary, #fff);
      color: var(--ui-text-primary, #0f172a);
    }

    .contractor-confirm-modal__icon {
      width: 52px;
      height: 52px;
      margin: 0 auto 0.8rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      color: var(--ui-btn-primary, #0ea5e9);
      background: color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 14%, transparent);
      border: 1px solid color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 36%, transparent);
      animation: confirmIconPulse 480ms ease;
    }

    .contractor-confirm-modal__icon.is-danger {
      color: var(--ui-btn-quaternary, #f59e0b);
      background: color-mix(in srgb, var(--ui-btn-quaternary, #f59e0b) 16%, transparent);
      border-color: color-mix(in srgb, var(--ui-btn-quaternary, #f59e0b) 38%, transparent);
    }

    .contractor-confirm-modal__title {
      margin: 0 0 0.35rem;
      font-size: 1.05rem;
      font-weight: 900;
      text-align: center;
    }

    .contractor-confirm-modal__message {
      margin: 0;
      text-align: center;
      color: var(--ui-text-secondary, #475569);
      font-weight: 600;
      line-height: 1.7;
    }

    .contractor-confirm-modal__actions {
      display: flex;
      justify-content: center;
      gap: 0.6rem;
      margin-top: 1rem;
      flex-wrap: wrap;
    }

    @keyframes confirmIconPulse {
      0% {
        transform: scale(0.86);
        opacity: 0;
      }
      60% {
        transform: scale(1.06);
        opacity: 1;
      }
      100% {
        transform: scale(1);
        opacity: 1;
      }
    }

    @keyframes voterActionSwitch {
      0% {
        transform: scale(1);
        filter: saturate(100%);
      }
      45% {
        transform: scale(0.92);
        filter: saturate(120%);
      }
      100% {
        transform: scale(1);
        filter: saturate(100%);
      }
    }

    .committeDetails {
      max-width: min(1080px, 92vw);
      margin: 1rem auto 1.3rem;
      background: linear-gradient(140deg,
        color-mix(in srgb, var(--ui-bg-primary, #fff) 96%, transparent),
        color-mix(in srgb, var(--ui-bg-secondary, #f8fafc) 94%, transparent));
      border: 1px solid color-mix(in srgb, var(--ui-border, #dbe3ef) 85%, transparent);
      border-radius: calc(var(--ui-radius-card, 1rem) + 0.2rem);
      padding: clamp(0.95rem, 1.8vw, 1.35rem);
      box-shadow: 0 14px 30px rgba(2, 6, 23, 0.1);
      color: var(--ui-text-primary, #0f172a);
      position: relative;
      overflow: hidden;
    }

    .committeDetails::before {
      content: "";
      position: absolute;
      inset-inline-start: -18%;
      top: -55%;
      width: 55%;
      height: 180%;
      background: radial-gradient(circle,
        color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 18%, transparent) 0%,
        transparent 68%);
      pointer-events: none;
    }

    .committeDetails p {
      margin-bottom: 0;
    }

    .committeDetails__badge {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.22rem 0.65rem;
      border-radius: 999px;
      font-size: 0.72rem;
      font-weight: 800;
      color: var(--ui-btn-primary, #0ea5e9);
      border: 1px solid color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 38%, transparent);
      background: color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 12%, transparent);
      margin-bottom: 0.45rem;
      position: relative;
      z-index: 1;
    }

    .committeDetails__title {
      margin: 0;
      font-size: clamp(1.02rem, 2.1vw, 1.35rem);
      font-weight: 900;
      letter-spacing: -0.01em;
      color: var(--ui-text-primary, #0f172a);
      position: relative;
      z-index: 1;
    }

    .committeDetails__title strong {
      color: var(--ui-btn-primary, #0ea5e9);
      font-weight: 900;
    }

    .committeDetails__desc {
      margin-top: 0.45rem;
      font-size: clamp(0.88rem, 1.5vw, 0.98rem);
      font-weight: 600;
      color: var(--ui-text-secondary, #475569);
      line-height: 1.75;
      position: relative;
      z-index: 1;
    }

    @media (max-width: 768px) {
      .contractor-layout-block {
        width: 95vw;
        padding: 0.8rem;
        margin: 0.8rem auto;
      }

      .contractor-page-container {
        width: 95vw;
      }

      .contractor-top-cta__inner {
        justify-content: center;
        text-align: center;
      }

      .contractor-top-cta__text {
        justify-content: center;
      }

      .contractor-hero {
        grid-template-columns: 1fr;
        text-align: center;
      }

      .contractor-hero-media {
        margin: 0 auto;
      }

      .hero-countdown {
        padding: 0.62rem;
      }

      .hero-countdown__value {
        font-size: 1rem;
      }

      .hero-countdown__label {
        font-size: 0.68rem;
      }

      .committeDetails {
        padding: 0.85rem;
      }

      .committeDetails__desc {
        line-height: 1.65;
      }
    }
  </style>
  <body class="ui-modern ui-light contractor-profile-page" dir="rtl" data-ui-mode="modern" data-ui-color-mode="light" data-bs-theme="light">

    <div class="contractor-top-cta">
      <div class="contractor-top-cta__inner">
        <div>
          <p class="contractor-top-cta__offer"><i class="fa fa-coins"></i> كن وسيط بين البرنامج والمرشح واربح عمولة مباشرة <span class="ltr-fragment">20%</span></p>
          <p class="contractor-top-cta__text"><i class="fa fa-bullhorn"></i> عشان تعرف اكتر عن البرنامج اضغط هنا</p>
          <p class="contractor-top-cta__hint">اكتشف مميزات كنترول وكيف يساعدك في إدارة الكشوف والمتابعة بشكل احترافي.</p>
        </div>
        <a class="contractor-top-cta__link" href="https://kw-control.com/about-control" target="_blank" rel="noopener noreferrer"><i class="fa fa-arrow-up-left-from-circle"></i> اضغط هنا</a>
      </div>
    </div>

    <section class="rtl contractor-hero-wrap">
      <div class="container-fluid contractor-page-container">
        <div class="contractor-hero">
          <figure class="contractor-hero-media mb-0">
            <img
              src="{{$contractor->creator ? $contractor->creator->image : ''}}"
              alt="{{$contractor->creator?->name}}"
            />
          </figure>
          <div>
            <span class="contractor-hero-badge"><i class="fa fa-circle-check"></i> صفحة المتعهد الرسمية</span>
            <h1 class="contractor-hero-name">{{$contractor->creator?->name}}</h1>
            <p class="contractor-hero-sub">مرشح انتخابات جمعية {{ $contractor->creator?->election->name }}</p>

            <div class="hero-countdown">
              <h6 class="hero-countdown__title">باقي على موعد الانتخابات</h6>

              <div class="row hero-countdown__grid align-items-center">
                <div class="col-3">
                  <div class="hero-countdown__item">
                    <span class="hero-countdown__value" id="seconds"></span>
                    <span class="hero-countdown__label">ثانيه</span>
                  </div>
                </div>

                <div class="col-3">
                  <div class="hero-countdown__item">
                    <span class="hero-countdown__value" id="minutes"></span>
                    <span class="hero-countdown__label">دقيقه</span>
                  </div>
                </div>

                <div class="col-3">
                  <div class="hero-countdown__item">
                    <span class="hero-countdown__value" id="hours"></span>
                    <span class="hero-countdown__label">ساعه</span>
                  </div>
                </div>

                <div class="col-3">
                  <div class="hero-countdown__item">
                    <span class="hero-countdown__value" id="days"></span>
                    <span class="hero-countdown__label">يوم</span>
                  </div>
                </div>
              </div>

              <input type="hidden" id="startDate" value="{{ \Carbon\Carbon::parse($contractor->creator?->election->start_date)->format('Y-m-d') }}">
              <input type="hidden" id="startTime" value="{{ \Carbon\Carbon::parse($contractor->creator?->election->start_time)->format('H:i:s') }}">
              <input type="hidden" id="endDate" value="{{ \Carbon\Carbon::parse($contractor->creator?->election->end_date)->format('Y-m-d') }}">
              <input type="hidden" id="endTime" value="{{ \Carbon\Carbon::parse($contractor->creator?->election->end_time)->format('H:i:s') }}">

              <div id="election_start" class="hero-countdown__date">
                <p class="text-danger">
                  حتى تاريخ {{ \Carbon\Carbon::parse($contractor->creator?->election->start_date)->format('d/m/Y') }} , الساعة
                  {{ \Carbon\Carbon::parse($contractor->creator?->election->start_time)->format('h:i') }}
                  {{ \Carbon\Carbon::parse($contractor->creator?->election->start_time)->format('A') === 'AM' ? 'ص' : 'م' }}
                </p>
              </div>
              <div id="election_end" class="d-none hero-countdown__date">
                <p class="text-danger">
                  حتى تاريخ {{ \Carbon\Carbon::parse($contractor->creator?->election->end_date)->format('d/m/Y') }} , الساعة
                  {{ \Carbon\Carbon::parse($contractor->creator?->election->end_time)->format('h:i') }}
                  {{ \Carbon\Carbon::parse($contractor->creator?->election->end_time)->format('A') === 'AM' ? 'ص' : 'م' }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="committeDetails mx-auto my-4 text-center">
        <span class="committeDetails__badge"><i class="fa fa-heart"></i> رسالة ترحيب</span>
        <p class="committeDetails__title">مرحبا <strong>{{$contractor->name}}</strong></p>
        <p class="committeDetails__desc">
          هذه الصفحة تمكنكم من متابعة المتعهدين<br />
          ولكم كل الشكر والتقدير على دعمكم لنا 🌹
        </p>
      </div>

      <div class="container contractor-page-container">
        <div class="contractor-tab-nav nav" id="contractorTabNav" role="tablist">
          <button
            type="button"
            class="btn btn-secondary contractor-tab-btn active"
            id="contractor-tab-search"
            data-bs-toggle="tab"
            data-bs-target="#contractorTabSearch"
            role="tab"
            aria-controls="contractorTabSearch"
            aria-selected="true"
          >البحث</button>
          <button
            type="button"
            class="btn btn-outline-secondary contractor-tab-btn"
            id="contractor-tab-lists"
            data-bs-toggle="tab"
            data-bs-target="#contractorTabLists"
            role="tab"
            aria-controls="contractorTabLists"
            aria-selected="false"
          >القوائم</button>
        </div>
      </div>

      <div class="tab-content">
      <div id="contractorTabSearch" class="contractor-tab-pane tab-pane fade show active" data-tab-pane="search" role="tabpanel" aria-labelledby="contractor-tab-search">
      <div class="container contractor-page-container">
        <div class="mx-auto my-3">
            <x-dashboard.partials.message-alert />
         @if ($contractor->hasPermissionTo('search-stat-con'))
         <div class="moreSearch ">
            <form id="SearchForm"  class="description my-1">
              <div class="d-flex align-items-center mb-2">
                <label for="searchByNameOrNum">البحث عن</label>
                <input type="hidden" name="id" value="{{$contractor->id}}">
                <input
                  type="text"
                  class="form-control"
                  name="name"
                  id="searchByNameOrNum"
                  placeholder=" البحث عن الأسم او الرقم"
                  value=""
                />
              </div>
              <div class="d-flex align-items-center mb-2">
                <label for="searchByFamily"> العائلة</label>
                <select
                  name="family"
                  id="searchByFamily"
                  class="form-control js-example-basic-single"
                >
                  <option value="" hidden>حصر نتائج البحث حسب العائلة</option>
                    @foreach ($families as $fam )
                        <option value="{{$fam->id}}"> {{$fam->name}} </option>
                    @endforeach
                </select>
              </div>

            </form>
          </div>
         @endif
        </div>
      </div>

    @php
    $id=$contractor->id;
    $voters = $contractor->voters()
    ->whereDoesntHave('groups', function ($query) use ($id) {
        $query->where('contractor_id', $id);
    })
    ->orderByRaw('status = true ASC')  // This puts the voters with `status = true` at the end
    ->orderBy('name', 'asc')     // This orders the rest by `created_at` ascending
    ->get();
    @endphp
    <form action="{{route("modify")}}" method="POST" id="form-transfer">
        @csrf
        <input type="hidden" name="id" value="{{$contractor->id}}" id="con_id">
      <input type="hidden" name="select" value="" id="bulk_action">
    <section class="py-3 rtl">
      <div class="container contractor-page-container">
        <div class="d-flex justify-content-start align-items-center gap-2 mb-2">
          <button type="button" class="btn btn-dark allBtn mb-0" id="toggle_select_all_search" data-select-all="off">تحديد الكل</button>
          <button type="button" class="btn btn-primary" id="all_voters">اضافة المحدد</button>
          <button type="button" class="btn btn-danger" id="delete_selected_top">حذف</button>
        </div>
      </div>
      <div class="container-fluid contractor-layout-block">
        <h5>
          قائمة الأسماء
          <span
            id="search_count"
            class="bg-dark text-white rounded-2 p-1 px-3 me-2"
            >{{$voters->count()}}</span
          >
        </h5>

        <div class="d-flex justify-content-center align-items-center gap-2 mb-3" id="membershipFilterButtons">
          <button type="button" class="btn btn-secondary membership-filter-btn active" data-membership-scope="all">الكل</button>
          <button type="button" class="btn btn-outline-secondary membership-filter-btn" data-membership-scope="attached">تمت اضافتهم</button>
          <button type="button" class="btn btn-outline-secondary membership-filter-btn" data-membership-scope="available">لم تتم اضافتهم</button>
        </div>

        <div class="madameenTable table-responsive mt-4">
          <table
            class="table contractor-voters-table rtl overflow-hidden rounded-3 text-center align-middle"
          >
            <thead
              class="table-primary border-0 border-secondary border-bottom border-2"
            >
              <tr>
                <th>#</th>
                <th class="w150">الأسم</th>
                <th>نسبة الصدق</th>
                <th>أدوات</th>
              </tr>
            </thead>
            <tbody id="resultSearchData">
              @foreach ( $voters as $voter )
              <tr
                class="
                 @if ($voter->status == 1)
                    table-success
                 @endif
                "
              >
                <td id="voter_td">
                  <input type="checkbox" id="voter_id" class="check" name="voters[]" value="{{$voter->id}}" />
                </td>

                <td>
                  <button
                    type="button"
                    class="voter-name-details @if ($voter->restricted != 'فعال') line @endif"
                    data-voter-id="{{$voter->id}}"
                    data-bs-toggle="modal"
                    data-bs-target="#nameChechedDetails"
                  >{{$voter->name}}</button>

                    <button class="search-relatives-btn" data-voter-name="{{$voter->name}}" data-voter-id="{{$voter->id}}" type="button">البحث عن أقارب</button>

                    @if ($voter->status == 1)
                <p class=" my-1">
                    <span><i class="fa fa-check-square text-success ms-1"></i>تم التصويت </span>
                    <span>{{ $voter->updated_at->format('Y/m/d') }}</span>
                </p>
            @endif
                </td>
                <td>%  {{$voter->pivot->percentage}} </td>

                <td>
                  <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-danger voter-action-toggle" onclick="toggleVoterStatus(this, '{{$voter->id}}', true)">حذف</button>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

      </div>
    </section>
</form>
    </div>

    <!-- Modal nameChechedDetails-->
    <!-- لسه هيتعمل -->
    <div
      class="modal modal-lg rtl"
      id="nameChechedDetails"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-1">
                <button
                type="button"
                class="btn-close "
                data-bs-dismiss="modal"
                aria-label="Close"
                     ></button>
          </div>
          <form class="modal-body">
            <div>
              <img src="{{ asset('assets/admin/images/image4.jpg') }}" class="w-100" alt="">
            </div>
            <h5
              class=" bg-dark text-center py-2 pe-2 text-white"
            >عرض وتحرير البيانات</h5>
            <div class="d-flex align-items-center mb-3">
              <label for="mota3ahedDetailsName"> الأسم</label>
               <input
              type="text"
              name="name"
              id="mota3ahedDetailsName"
                              value=""
                class="form-control fw-semibold"
                readonly="true"
              />
            </div>
            <!-- ================================================================================ -->
            <!-- <form action="#" method="POST" class="page-body">
              @csrf -->
              
              <div class="d-flex align-items-center mb-3">
              
                <input type="hidden" id="mota3ahedDetailsVoterId" value="">
                <label class="" for="mota3ahedDetailsPhone"> الهاتف </label>
                <input
                    type="text"
                    class="form-control"
                    name="mota3ahedDetailsPhone"
                    id="mota3ahedDetailsPhone"
                    value=""
                    min="0"
                    
                />
                
                <button class="btn btn-danger rounded-circle" id="update_voter_phone_btn" title="update phone">
                  <i class="pt-1 fs-5 fa fa-pen"></i>
                </button>
                
                <a href="" class="d-inline-block px-2 py-1 mx-1 text-white bg-primary rounded-circle"><i class="pt-1 fs-5 fa fa-phone"></i></a>
                <a href="" class="d-inline-block px-2 py-1 mx-1 text-white bg-success rounded-circle"><i class="pt-1 fs-5 fa-brands fa-whatsapp"></i></a>
              </div>
            <!-- </form> -->
            <!-- ================================================================================ -->
        
        
            
      <div class="d-flex align-items-center mb-3">
          <label class="" for="mota3ahedDetailsNote">ملاحظات </label>
          <textarea
              class="form-control"
              name="mota3ahedDetailsNote"
              id="mota3ahedDetailsNote"
              value=""
              min="">
          </textarea>
      </div>
      <div class="d-flex align-items-center mt-3 border">
        <label for="mota3ahedDetailsTrustingRate">نسبة الألتزام</label>
        <input type="range" name="mota3ahedDetailsTrustingRate" id="mota3ahedDetailsTrustingRate" value="0" min="0" class="w-100">
        <span class="bg-body-secondary p-2 px-3 d-flex">% <span class="fw-semibold" id="percent">0</span></span>
      </div>

      <hr>

      <div class="d-flex align-items-center mt-3">
        <label for="mota3ahedDetailsRegiterNumber"> رقم القيد</label>
        <input class="form-control" type="number" name="mota3ahedDetailsRegiterNumber" id="mota3ahedDetailsRegiterNumber" value="9996"  class="w-100">
      </div>
      <div class="d-flex align-items-center mt-3">
        <label for="mota3ahedDetailsCommitte">  اللجنة</label>
        <input class="form-control" type="text" name="mota3ahedDetailsCommitte" id="mota3ahedDetailsCommitte" value=""  class="w-100" readonly>
      </div>
      <div class="d-flex align-items-center mt-3">
        <label for="mota3ahedDetailsSchool">  المدرسة</label>
        <input class="form-control" type="text" name="mota3ahedDetailsSchool" id="mota3ahedDetailsSchool" value=""  class="w-100" readonly>
      </div>

      <hr>

      <div class="d-flex align-items-center">
        <label for="getNearly">عرض أقرب المحتملين</label>
        <form id="Form-Siblings">
            <select class="form-control" name="getNearly" id="siblings">
                <option value="من الدرجة الاولى " id="father">من الدرجة الاولى</option>
                <option value="من الدرجة التانية ">من الدرجة التانية</option>
                <option value="بحث موسع">بحث موسع</option>
              </select>

              <button type="submit" class="btn btn-secondary" id="search">
                بحث
              </button>
        </form>
      </div>



    </form>
        </div>
      </div>
    </div>

    <div
      class="modal rtl"
      id="createGroupModal"
      tabindex="-1"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header py-2">
            <h6 class="modal-title">انشاء قائمة جديدة للاسماء</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{route('group')}}" method="POST" class="modal-body" id="createGroupForm">
            @csrf
            <input type="hidden" name="contractor_id" value="{{$contractor->id}}">

            <div id="createGroupFeedback" class="alert d-none py-2" role="alert"></div>

            <div class="mb-2">
              <label for="listNameModal" class="form-label">اسم القائمة</label>
              <input
               type="text"
               class="form-control"
               name="name"
               id="listNameModal"
               placeholder="اسم القائمة"
               value=""
               required
             />
            </div>

            <div class="mb-3">
              <label for="listTypeModal" class="form-label">نوع القائمة</label>
              <select name="type" id="listTypeModal" class="form-control" required>
                <option value="" hidden>نوع القائمة</option>
                <option value="مضمون">مضمون</option>
                <option value="تحت المراجعة">تحت المراجعة</option>
              </select>
            </div>

            <button type="submit" class="btn btn-secondary w-100" id="createGroupSubmitBtn">انشاء</button>
          </form>
        </div>
      </div>
    </div>

    <div id="contractorTabLists" class="contractor-tab-pane tab-pane fade" data-tab-pane="lists" role="tabpanel" aria-labelledby="contractor-tab-lists">
    <section class="pt-4 pb-2">
        <!-- <div class="container-fluid px-0"> -->
      <div class="container contractor-page-container mb-2">
        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#createGroupModal">
            <i class="fa fa-plus ms-1"></i>
            انشاء قائمة جديدة للاسماء
          </button>
        </div>
      </div>
      <div class="container contractor-layout-block">
            @forelse ($contractor->groups as $g )
            <div
         class="ta7reerContent @if ($g->type == 'مضمون')
            bg-success
            @else
            bg-warning @endif d-flex px-3 py-2 mb-4 rounded-3 flex-column rtl"
       >
         <div
           class="ta7reerList d-flex justify-content-between align-items-center"
         >
           <div>
             <span class="listName">{{$g->name}}</span>
             <span class="bg-dark px-2 text-white rounded-1">{{$g->voters->count()}}</span>
             <span class="listType">{{$g->type}}</span>
           </div>
           <div>
            <input type="hidden" id="group_id" value="{{$g->id}}">
             <button
               data-bs-toggle="modal"
               data-bs-target="#ta7reerData"
               class="btn btn-dark"
             >
               تحرير
             </button>
           </div>
         </div>

         @if ($g->voters->isNotEmpty())

             <div class="table-responsive mt-1 d-none">
        <form action="{{route('modify_g')}}" method="POST" id="modify-g">
            @csrf
            <input type="hidden" name="id" value="{{$contractor->id}}" id="con_id">

            <table
              class="table rtl overflow-hidden rounded-3 text-center"
            >
              <thead
                class="table-primary border-0 border-secondary border-bottom border-2"
              >
                <tr>
                  <th>#</th>
                  <th class="w150">الأسم</th>
                  <th>نسبة الصدق</th>
                  <th>أدوات</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($g->voters as  $voter)

                <tr
                class="
                 @if ($voter->status == 1)
                    table-success
                 @endif
                "
                >
                    <td id="voter_td">
                        <input type="checkbox" id="voter_id" class="check" name="voters[]" value="{{$voter->id}}" />
                      </td>

                    <td>
                    <button
                      type="button"
                      class="voter-name-details"
                      data-voter-id="{{$voter->id}}"
                      data-bs-toggle="modal"
                      data-bs-target="#nameChechedDetails"
                    >{{$voter->name}}</button>

                    @if ($voter->status == 1)
                <p class=" my-1">
                    <span><i class="fa fa-check-square text-success ms-1"></i>تم التصويت </span>
                    <span>{{ $voter->updated_at->format('Y/m/d') }}</span>
                </p>
            @endif
                    </td>
                    <td>{{$voter->contractors()->where('contractor_id' , $contractor->id)->first()?->pivot->percentage}} % </td>
                    <td>-</td>
                  </tr>
                @endforeach
              </tbody>
            </table>

            <div class="d-flex align-items-center">
                <label class="btn btn-dark allBtn">تحديد الكل</label>
                <input type="hidden" name="group_id" value="{{$g->id}}">
                <select name="select" id="allSelected-{{$g->id}}" class="form-control mx-2 select-group">
                    <option value=""></option>
                    <option value="" hidden>التطبيق على المحدد</option>
                    @forelse ($contractor->groups as $group )
                        @if ($g->id != $group->id)
                        <option data-message="نقل الي  {{$g->name}} ({{$g->type}} " value="{{$group->id}}"> نقل الي  {{$group->name}} ({{$group->type}})  </option>
                        @endif
                    @empty
                  @endforelse
                  @if ($contractor->hasPermissionTo('delete-stat-con'))
                  <option value="delete-g" class="btn btn-danger" data-message="حذف الاسماء من المجموعه">حذف الاسماء من المجموعه</option>
                  <option value="delete" class="btn btn-danger" data-message="حذف الاسماء من المضامين">حذف الاسماء من المضامين</option>
                    @endif
                </select>
                <button class="btn btn-primary" id="sub-btn-g" disabled>تنفيذ</button>
              </div>
            </form>
          </div>


          
         @else
         <div class="fs-5 bg-white px-3 d-none">لا يوجد</div>

         @endif


       </div>
         @empty

         @endforelse



          <!-- Modal ta7reerData-->
          <div
            class="modal modal-lg rtl"
            id="ta7reerData"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
          >
            <div class="modal-dialog modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                  ></button>
                </div>
                <div class="modal-body">
                    <form action="" id="edit-form" method="POST">
                        @csrf
                  <div class="d-flex align-items-center mt-3">
                    <label for="listNamemodal">اسم القائمة</label>
                    <input
                      type="text"
                      name="name"
                      id="listNamemodal"
                      value=""
                      class="form-control fw-semibold"
                    />
                  </div>
                  <div class="d-flex align-items-center mt-3">
                    <label for="listTypemodal">نوع القائمة</label>
                    <select
                      name="type"
                      id="listTypemodal"
                      class="form-control mx-2"
                    >
                      <option value="" hidden></option>
                      <option value="مضمون">مضمون</option>
                      <option value="تحت المراجعة">تحت المراجعة</option>
                    </select>
                  </div>
                  <button type="submit" id="button-g" class="btn btn-primary w-100 mt-3 doBtn">تنفيذ</button>
                </form>
                  <a href="" id="delete-g">
                    <button class="btn btn-danger w-100 mt-3 deleteBtn">
                        حذف المجموعة
                      </button>
                  </a>
                </div>
              </div>
            </div>
          </div>


        </div>
      </section>
    </div>
    </div>
<!--
        <div class="banner">
          <img
            src="{{$contractor->creator?->candidate ? ($contractor->creator->candidate ?? $contractor->creator->candidate[0]->banner) : "" }}"
            class="w-100 h-100"
            alt="description banner image project "
          />
        </div> -->
    </section>

        <!-- ================================================================================================================= -->
        <!-- this form for update voter phone -->
        @include('dashboard.contractors.update_phone_pop_up')
        <!-- ================================================================================================================= -->

    <div class="modal fade contractor-confirm-modal" id="bulkActionConfirmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body p-4">
            <div class="contractor-confirm-modal__icon" id="bulkActionConfirmIcon">
              <i class="fa fa-circle-check"></i>
            </div>
            <h6 class="contractor-confirm-modal__title" id="bulkActionConfirmTitle">تأكيد العملية</h6>
            <p class="contractor-confirm-modal__message" id="bulkActionConfirmMessage">هل تريد الاستمرار؟</p>
            <div class="contractor-confirm-modal__actions">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
              <button type="button" class="btn btn-primary" id="bulkActionConfirmApproveBtn">تأكيد</button>
            </div>
          </div>
        </div>
      </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.easing.1.3.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/admin/js/main.js') }}"></script>
    <script>
        $("#allSelected").on('change',function(){

            if($("#allSelected").val()){
                $("#sub-btn").prop('disabled',false)
            }else{
                $("#sub-btn").prop('disabled',true)
            }
        })
        $("#sub-btn").on('click',function(){
            if($("#allSelected").val()){
                event.preventDefault(); // Prevent the form from submitting immediately

var selectedOption = $('#allSelected option:selected'); // Get the selected option
var message = selectedOption.data('message'); // Get the data-message attribute

                if (confirm(message)) {
    $("#form-transfer").submit();
} else {
    return false;
            }
        }
        })
            $(document).ready(function() {
    $('.select-group').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var message = selectedOption.data('message');
        console.log($(this));


        let btn=$(this).closest('div').find('#sub-btn-g');
        if (selectedOption.val()) {
            btn.prop('disabled', false);
        } else {
            btn.prop('disabled', true);
        }

        btn.off('click').on('click', function(event) {
            event.preventDefault();
            if (confirm(message)) {
                $(this).closest('form').submit();
            } else {
                return false;
            }
        });
    });
});
    </script>
    <script>
      $(document).ready(function () {
      
        //========================================================================
          //load voter details by clicking voter name
          $(document).on('click', '.voter-name-details', function(event) {
            event.preventDefault();

            const voterId = $(this).data('voterId') || $(this).attr('data-voter-id');
            if (!voterId) return;

            const detailsUrl = '/voter/' + voterId + '/' + contractorId;

            axios.get(detailsUrl)
              .then(function (response) {
                $('#mota3ahedDetailsVoterId').val(response?.data?.voter?.id || '');
                $('#mota3ahedDetailsName').val(response?.data?.voter?.name || '');
                $('#mota3ahedDetailsPhone').val(response?.data?.voter?.phone1 || '');
                $('#mota3ahedDetailsCommitte').val(response?.data?.committee_name || '');
                $('#mota3ahedDetailsRegiterNumber').val(response?.data?.voter?.alsndok || '');
                $('#mota3ahedDetailsSchool').val(response?.data?.school || '');
                $('#mota3ahedDetailsTrustingRate').val(response?.data?.percent || 0);
                $('#percent').text(response?.data?.percent || 0);
                $('#father').val(response?.data?.voter?.father || '');
              })
              .catch(function () {
                alert('تعذر تحميل تفاصيل الناخب');
              });
          });
        //========================================================================
      
          $(".js-example-basic-single").select2();
          //========================================================================
          //update voter phone
          $('#update_voter_phone_btn').on('click', function(event) {
            event.preventDefault(); // Prevent the default button action
            var voter_phone = $('#mota3ahedDetailsPhone').val();
            var voter_id    = $('#mota3ahedDetailsVoterId').val();
            updateVoterPhone(voter_phone,voter_id);
          });
          //========================================================================
          function updateVoterPhone(voter_phone,voter_id) {
            $.ajax({
              url: '/update/voter/phone',
              type: 'POST',
              data: JSON.stringify({
                _token: $('meta[name="csrf-token"]').attr('content'),
                voter_phone: voter_phone,
    				    voter_id: voter_id,
              }),
              contentType: 'application/json',
              success: function(data) {
                // console.log(data);
                sucessMessageInModel(data.message);
              },
              error: function(xhr, status, error) {
                // console.log(xhr);
                // Show error in success modal
                errorMessageInModel('حدث خطأ');
              }
            });
          }
          //========================================================================
          function errorMessageInModel(msg) {
            $('#successMessage').text(msg);
            $('#successModal .modal-header').removeClass('bg-success').addClass('bg-danger');
            $('#successModal .fa-check-circle').removeClass('text-success').addClass('text-danger');
            $('#successModal').modal('show');
          }
          //========================================================================
          function sucessMessageInModel(msg) {
            $('#successMessage').text(msg);
            $('#successModal .modal-header').removeClass('bg-danger').addClass('bg-success');
            $('#successModal .fa-check-circle').removeClass('text-danger').addClass('text-success');
            $('#successModal').modal('show');
          }
          //========================================================================
      });
let users = [];
const attachRoute = "{{ route('ass', $contractor->id) }}";
const modifyRoute = "{{ route('modify') }}";
const csrfToken = $('meta[name="csrf-token"]').attr('content');
const contractorId = "{{ $contractor->id }}";
const searchEndpoint = '/search';
const pageSize = 20;

let isLoadingRows = false;
let hasMoreRows = true;
let currentPage = 1;
let searchDebounceTimer = null;
let currentRequestId = 0;
let silentFilterUpdate = false;
let windowLoadMoreThrottle = null;
let isSelectAllLoading = false;
let bulkSelectAllActive = false;
let bulkSelectedVoterIds = [];
let bulkActionConfirmModalInstance = null;
let bulkActionConfirmResolver = null;
let activeFilters = {
  name: '',
  family: '',
  sibling: '',
  siblingExcludeId: '',
  membershipScope: 'all'
};

function ensureBulkActionConfirmModal() {
  const modalEl = document.getElementById('bulkActionConfirmModal');
  if (!modalEl || typeof bootstrap === 'undefined') return null;

  if (!bulkActionConfirmModalInstance) {
    bulkActionConfirmModalInstance = new bootstrap.Modal(modalEl, {
      backdrop: true,
      keyboard: true
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
      if (bulkActionConfirmResolver) {
        bulkActionConfirmResolver(false);
        bulkActionConfirmResolver = null;
      }
    });
  }

  return bulkActionConfirmModalInstance;
}

function openBulkActionConfirm(options) {
  const modal = ensureBulkActionConfirmModal();
  if (!modal) {
    return Promise.resolve(window.confirm(options?.message || 'هل تريد الاستمرار؟'));
  }

  const title = options?.title || 'تأكيد العملية';
  const message = options?.message || 'هل تريد الاستمرار؟';
  const approveText = options?.approveText || 'تأكيد';
  const isDanger = options?.isDanger === true;

  $('#bulkActionConfirmTitle').text(title);
  $('#bulkActionConfirmMessage').text(message);
  $('#bulkActionConfirmApproveBtn')
    .text(approveText)
    .removeClass('btn-primary btn-danger')
    .addClass(isDanger ? 'btn-danger' : 'btn-primary');

  const iconWrap = $('#bulkActionConfirmIcon');
  iconWrap
    .toggleClass('is-danger', isDanger)
    .html(`<i class="fa ${isDanger ? 'fa-trash' : 'fa-plus-circle'}"></i>`);

  return new Promise(function (resolve) {
    bulkActionConfirmResolver = resolve;

    $('#bulkActionConfirmApproveBtn').off('click').on('click', function () {
      if (bulkActionConfirmResolver) {
        bulkActionConfirmResolver(true);
        bulkActionConfirmResolver = null;
      }
      modal.hide();
    });

    modal.show();
  });
}

function getCurrentlyCheckedVoterIds() {
  return Array.from(document.querySelectorAll('#resultSearchData .check:checked')).map(function (checkbox) {
    return checkbox.value;
  });
}

function getBulkActionVoterIds() {
  if (bulkSelectAllActive && Array.isArray(bulkSelectedVoterIds) && bulkSelectedVoterIds.length > 0) {
    return bulkSelectedVoterIds.slice();
  }

  return getCurrentlyCheckedVoterIds();
}

function resetBulkSelectAllState(uncheckLoadedRows) {
  bulkSelectAllActive = false;
  bulkSelectedVoterIds = [];

  $('#toggle_select_all_search')
    .attr('data-select-all', 'off')
    .text('تحديد الكل');

  if (uncheckLoadedRows) {
    document.querySelectorAll('#resultSearchData .check').forEach(function (checkbox) {
      checkbox.checked = false;
    });
  }
}

function collectAllFilteredVoterIds() {
  return axios.get(searchEndpoint, {
    params: {
      id: contractorId,
      scope: activeFilters.membershipScope || 'all',
      exclude_grouped: '1',
      ids_only: '1',
      name: activeFilters.name || '',
      family: activeFilters.family || '',
      sibling: activeFilters.sibling || '',
      sibling_exclude_id: activeFilters.siblingExcludeId || ''
    }
  }).then(function (response) {
    const ids = Array.isArray(response?.data?.ids) ? response.data.ids : [];
    return ids.map(function (id) { return String(id); });
  });
}

function submitAttachVoters(voterIds) {
  if (!Array.isArray(voterIds) || voterIds.length === 0) {
    alert('يرجى تحديد ناخب واحد على الأقل');
    return Promise.reject(new Error('no-voters-selected'));
  }

  return axios.post(attachRoute, {
    _token: csrfToken,
    voter: voterIds
  }, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  });
}

function submitDeleteVoters(voterIds) {
  if (!Array.isArray(voterIds) || voterIds.length === 0) {
    alert('لم يتم اختيار اي ناخب');
    return Promise.reject(new Error('no-voters-selected'));
  }

  return axios.post(modifyRoute, {
    _token: csrfToken,
    id: contractorId,
    select: 'delete',
    voters: voterIds
  }, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  });
}

function addSingleVoter(voterId) {
  submitAttachVoters([voterId]);
}

function toggleVoterStatus(buttonEl, voterId, isCurrentlyAdded) {
  const targetUrl = isCurrentlyAdded ? modifyRoute : attachRoute;
  const payload = {
    _token: csrfToken,
    voter: [voterId]
  };

  if (isCurrentlyAdded) {
    payload.id = contractorId;
    payload.voters = [voterId];
    payload.select = 'delete';
  }

  axios.post(targetUrl, payload, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
    .then(function (response) {
      const msg = response?.data?.message || (isCurrentlyAdded ? 'تم الحذف بنجاح' : 'تمت الاضافة بنجاح');
      showCreateGroupFeedback('success', msg);

      const nextIsAdded = !isCurrentlyAdded;
      if (buttonEl) {
        buttonEl.classList.add('is-switching');
        buttonEl.disabled = true;

        setTimeout(function () {
          buttonEl.classList.remove('btn-success', 'btn-danger');
          buttonEl.classList.add(nextIsAdded ? 'btn-danger' : 'btn-success');
          buttonEl.textContent = nextIsAdded ? 'حذف' : 'اضافة';
          buttonEl.setAttribute('onclick', `toggleVoterStatus(this, '${voterId}', ${nextIsAdded})`);
        }, 120);

        setTimeout(function () {
          buttonEl.classList.remove('is-switching');
          buttonEl.disabled = false;
        }, 340);
      }

      setTimeout(function () {
        runLiveSearch({
          name: ($('#searchByNameOrNum').val() || '').trim(),
          family: ($('#searchByFamily').val() || '').trim(),
          sibling: '',
          siblingExcludeId: '',
          membershipScope: activeFilters.membershipScope || 'all'
        });
      }, 260);
    })
    .catch(function (error) {
      const msg = error?.response?.data?.message || 'حدث خطأ أثناء تنفيذ العملية';
      showCreateGroupFeedback('error', msg);
      alert(msg);
    });
}

function bindRelativeButtons() {
  document.querySelectorAll('.search-relatives-btn').forEach(function (button) {
    button.onclick = function () {
      searchRelatives(this.dataset.voterName || '', this.dataset.voterId || '');
    };
  });
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function buildVoterRow(voter) {
  const voterId = voter?.id ?? '';
  const voterName = escapeHtml(voter?.name ?? '');
  const voterFullName = escapeHtml(voter?.name ?? '');
  const isActive = (voter?.restricted ?? '') === 'فعال';
  const statusRowClass = Number(voter?.status) === 1 ? 'table-success' : '';
  const trustRate = voter?.pivot?.percentage ?? '-';
  const isAdded = Boolean(voter?.is_added);
  const actionBtnClass = isAdded ? 'btn btn-danger voter-action-toggle' : 'btn btn-success voter-action-toggle';
  const actionBtnText = isAdded ? 'حذف' : 'اضافة';

  return `<tr class="${statusRowClass}">
    <td><input type="checkbox" class="check" name="voters[]" value="${voterId}" /></td>
    <td>
      <button type="button" class="voter-name-details ${isActive ? '' : 'line'}" data-voter-id="${voterId}" data-bs-toggle="modal" data-bs-target="#nameChechedDetails">${voterName}</button>
      <span class="voter-inactive-flag">${isActive ? '' : 'غير فعال'}</span>
      <button class="search-relatives-btn" data-voter-name="${voterFullName}" data-voter-id="${voterId}" type="button">البحث عن أقارب</button>
    </td>
    <td>% ${trustRate}</td>
    <td>
      <div class="d-flex justify-content-center gap-2 flex-wrap">
        <button type="button" class="${actionBtnClass}" onclick="toggleVoterStatus(this, '${voterId}', ${isAdded})">${actionBtnText}</button>
      </div>
    </td>
  </tr>`;
}

function renderVoters(votersList, appendMode) {
  const tbody = document.getElementById('resultSearchData');
  if (!tbody) return;

  if (!appendMode) {
    resetBulkSelectAllState(true);
  }

  if (!Array.isArray(votersList) || votersList.length === 0) {
    if (!appendMode) {
      tbody.innerHTML = '<tr><td colspan="4" class="text-center py-3">لا توجد نتائج</td></tr>';
    }
    return;
  }

  const rowsHtml = votersList.map(buildVoterRow).join('');
  if (appendMode) {
    tbody.insertAdjacentHTML('beforeend', rowsHtml);
  } else {
    tbody.innerHTML = rowsHtml;
  }

  if (bulkSelectAllActive) {
    document.querySelectorAll('#resultSearchData .check').forEach(function (checkbox) {
      checkbox.checked = true;
    });
  }

  bindRelativeButtons();
}

function currentFiltersFromUI() {
  return {
    name: ($('#searchByNameOrNum').val() || '').trim(),
    family: ($('#searchByFamily').val() || '').trim(),
    sibling: '',
    siblingExcludeId: '',
    membershipScope: activeFilters.membershipScope || 'all'
  };
}

function fetchVotersPage(appendMode) {
  if (isLoadingRows) return;
  if (appendMode && !hasMoreRows) return;

  isLoadingRows = true;
  const requestId = ++currentRequestId;
  const params = new URLSearchParams();
  params.append('id', contractorId);
  params.append('scope', activeFilters.membershipScope || 'all');
  params.append('exclude_grouped', '1');
  params.append('page', String(currentPage));
  params.append('per_page', String(pageSize));

  if (activeFilters.name) params.append('name', activeFilters.name);
  if (activeFilters.family) params.append('family', activeFilters.family);
  if (activeFilters.sibling) params.append('sibling', activeFilters.sibling);
  if (activeFilters.siblingExcludeId) params.append('sibling_exclude_id', activeFilters.siblingExcludeId);

  axios.get(searchEndpoint, { params: params })
    .then(function (response) {
      if (requestId !== currentRequestId) return;

      const votersList = Array.isArray(response?.data?.voters) ? response.data.voters : [];
      const pagination = response?.data?.pagination || null;

      renderVoters(votersList, appendMode);

      if (pagination) {
        const hasMoreValue = pagination.has_more;
        hasMoreRows = hasMoreValue === true || hasMoreValue === 1 || hasMoreValue === '1';
        $('#search_count').text(pagination.total ?? votersList.length);
      } else {
        hasMoreRows = votersList.length >= pageSize;
        if (!appendMode) {
          $('#search_count').text(votersList.length);
        }
      }
    })
    .catch(function (error) {
      console.error('Search Error:', error);
    })
    .finally(function () {
      isLoadingRows = false;
    });
}

function runLiveSearch(filters) {
  activeFilters = {
    name: filters?.name ?? '',
    family: filters?.family ?? '',
    sibling: filters?.sibling ?? '',
    siblingExcludeId: filters?.siblingExcludeId ?? '',
    membershipScope: filters?.membershipScope ?? activeFilters.membershipScope ?? 'all'
  };

  currentPage = 1;
  hasMoreRows = true;
  fetchVotersPage(false);
}

function loadNextPageIfAvailable() {
  if (isLoadingRows || !hasMoreRows) return;
  currentPage += 1;
  fetchVotersPage(true);
}

function searchRelatives(voterName, voterId) {
  silentFilterUpdate = true;
  $('#searchByNameOrNum').val('');
  $('#searchByFamily').val('').trigger('change.select2');
  silentFilterUpdate = false;

  runLiveSearch({
    name: '',
    family: '',
    sibling: voterName || '',
    siblingExcludeId: voterId || '',
    membershipScope: activeFilters.membershipScope || 'all'
  });
}

$('#membershipFilterButtons .membership-filter-btn').on('click', function () {
  const scope = $(this).data('membershipScope') || 'all';

  $('#membershipFilterButtons .membership-filter-btn')
    .removeClass('btn-secondary active')
    .addClass('btn-outline-secondary');

  $(this)
    .removeClass('btn-outline-secondary')
    .addClass('btn-secondary active');

  runLiveSearch({
    name: ($('#searchByNameOrNum').val() || '').trim(),
    family: ($('#searchByFamily').val() || '').trim(),
    sibling: '',
    siblingExcludeId: '',
    membershipScope: scope
  });
});

$('#contractorTabNav').on('shown.bs.tab', '.contractor-tab-btn', function () {
  $('#contractorTabNav .contractor-tab-btn')
    .removeClass('btn-secondary active')
    .addClass('btn-outline-secondary')
    .attr('aria-selected', 'false');

  $(this)
    .removeClass('btn-outline-secondary')
    .addClass('btn-secondary active')
    .attr('aria-selected', 'true');
});

$('#all_voters').off('click');

$('#all_voters').on('click', function (event) {
  event.preventDefault();
  const selectedVoters = getBulkActionVoterIds();
  const actionBtn = $(this);

  if (!selectedVoters.length) {
    alert('يرجى تحديد ناخب واحد على الأقل');
    return;
  }

  openBulkActionConfirm({
    title: 'تأكيد الإضافة',
    message: `سيتم إضافة ${selectedVoters.length} اسم إلى قائمتك. هل تريد المتابعة؟`,
    approveText: 'تأكيد الإضافة',
    isDanger: false
  }).then(function (confirmed) {
    if (!confirmed) return;

    actionBtn.prop('disabled', true).text('جاري الإضافة...');

    submitAttachVoters(selectedVoters)
      .then(function (response) {
        const msg = response?.data?.message || 'تمت الاضافه بنجاح';
        showCreateGroupFeedback('success', msg);
        resetBulkSelectAllState(true);
        runLiveSearch({ ...activeFilters });
      })
      .catch(function (error) {
        if (error?.message === 'no-voters-selected') return;
        const msg = error?.response?.data?.message || 'حدث خطأ أثناء الاضافة';
        showCreateGroupFeedback('error', msg);
        alert(msg);
      })
      .finally(function () {
        actionBtn.prop('disabled', false).text('اضافة المحدد');
      });
  });
});

$('#toggle_select_all_search').on('click', function (event) {
  event.preventDefault();

  if (isSelectAllLoading) return;

  if (bulkSelectAllActive) {
    resetBulkSelectAllState(true);
    return;
  }

  isSelectAllLoading = true;
  const triggerBtn = $(this);
  triggerBtn.prop('disabled', true).text('جاري تحديد الكل...');

  collectAllFilteredVoterIds()
    .then(function (allIds) {
      bulkSelectedVoterIds = allIds;
      bulkSelectAllActive = allIds.length > 0;

      document.querySelectorAll('#resultSearchData .check').forEach(function (checkbox) {
        checkbox.checked = bulkSelectAllActive;
      });

      triggerBtn
        .attr('data-select-all', bulkSelectAllActive ? 'on' : 'off')
        .text(bulkSelectAllActive ? `إلغاء تحديد الكل (${allIds.length})` : 'تحديد الكل');

      if (!bulkSelectAllActive) {
        alert('لا توجد نتائج لتحديدها');
      }
    })
    .catch(function () {
      alert('تعذر تحديد كل النتائج، حاول مرة أخرى');
      resetBulkSelectAllState(true);
    })
    .finally(function () {
      isSelectAllLoading = false;
      triggerBtn.prop('disabled', false);
    });
});

$('#delete_selected_top').on('click', function (event) {
  event.preventDefault();

  const selectedVoters = getBulkActionVoterIds();

  if (!selectedVoters.length) {
    alert('لم يتم اختيار اي ناخب');
    return;
  }

  const actionBtn = $(this);
  openBulkActionConfirm({
    title: 'تأكيد الحذف',
    message: `سيتم حذف ${selectedVoters.length} اسم من قائمتك الحالية. هل تريد المتابعة؟`,
    approveText: 'تأكيد الحذف',
    isDanger: true
  }).then(function (confirmed) {
    if (!confirmed) return;

    actionBtn.prop('disabled', true).text('جاري الحذف...');

    submitDeleteVoters(selectedVoters)
      .then(function (response) {
        const msg = response?.data?.message || 'تم الحذف بنجاح';
        showCreateGroupFeedback('success', msg);
        resetBulkSelectAllState(true);
        runLiveSearch({ ...activeFilters });
      })
      .catch(function (error) {
        if (error?.message === 'no-voters-selected') return;
        const msg = error?.response?.data?.message || 'حدث خطأ أثناء الحذف';
        showCreateGroupFeedback('error', msg);
        alert(msg);
      })
      .finally(function () {
        actionBtn.prop('disabled', false).text('حذف');
      });
  });
});

function showCreateGroupFeedback(type, message) {
  const feedback = $('#createGroupFeedback');
  feedback.removeClass('d-none alert-success alert-danger')
    .addClass(type === 'success' ? 'alert-success' : 'alert-danger')
    .text(message);
}

$('#createGroupForm').on('submit', function (event) {
  event.preventDefault();

  const form = this;
  const submitBtn = $('#createGroupSubmitBtn');
  const formData = new FormData(form);

  submitBtn.prop('disabled', true).text('جاري الإنشاء...');
  $('#createGroupFeedback').addClass('d-none').text('');

  axios.post(form.action, formData, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
    .then(function (response) {
      const msg = response?.data?.message || 'تم إنشاء القائمة بنجاح';
      showCreateGroupFeedback('success', msg);
      alert(msg);
      form.reset();
    })
    .catch(function (error) {
      const msg = error?.response?.data?.message || error?.response?.data?.errors?.name?.[0] || 'حدث خطأ أثناء إنشاء القائمة';
      showCreateGroupFeedback('error', msg);
      alert(msg);
    })
    .finally(function () {
      submitBtn.prop('disabled', false).text('انشاء');
    });
});

$('#SearchForm').on('submit', function (event) {
  event.preventDefault();
});

$('#searchByNameOrNum').on('input', function () {
  clearTimeout(searchDebounceTimer);
  searchDebounceTimer = setTimeout(function () {
    runLiveSearch(currentFiltersFromUI());
  }, 250);
});

$('#searchByFamily').on('change', function () {
  if (silentFilterUpdate) return;
  runLiveSearch(currentFiltersFromUI());
});

$('.madameenTable').on('scroll', function () {
  const element = this;
  const nearBottom = element.scrollTop + element.clientHeight >= element.scrollHeight - 80;
  if (!nearBottom) return;
  loadNextPageIfAvailable();
});

$(window).on('scroll', function () {
  if ($('#contractorTabSearch').length && !$('#contractorTabSearch').hasClass('active')) return;

  if (windowLoadMoreThrottle) {
    clearTimeout(windowLoadMoreThrottle);
  }

  windowLoadMoreThrottle = setTimeout(function () {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
    const viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
    const fullHeight = Math.max(
      document.body.scrollHeight,
      document.documentElement.scrollHeight,
      document.body.offsetHeight,
      document.documentElement.offsetHeight
    );

    const nearPageBottom = scrollTop + viewportHeight >= fullHeight - 180;
    if (nearPageBottom) {
      loadNextPageIfAvailable();
    }
  }, 120);
});

runLiveSearch(currentFiltersFromUI());
$('button[data-bs-target="#ta7reerData"]').on("click", function () {

    let group_id=$(this).siblings('#group_id').val();
    url = "/group/"+ group_id;
    console.log(url);

    axios.get(url)
    .then(function (response) {
        console.log(response);
        $("#listNamemodal").val(response.data.group.name)
        $("#listTypemodal").val(response.data.group.type)
        let editUrl="/group-e/"+group_id;
        let deleteUrl="/group-d/"+group_id;
        $("#edit-form").attr("action",editUrl)
        $("#delete-g").attr("href", deleteUrl)
        console.log($("#delete-g").attr("href"));
    })
    .catch(function (error) {
        console.error('Error:', error);
    });
    $("#button-g").on('click',function(){
    $("#edit-form").submit();

    })



})






    </script>
  </body>
</html>

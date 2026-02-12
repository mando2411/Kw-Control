<!DOCTYPE html>
@php
    $uiModeServer = auth()->check() ? (auth()->user()->ui_mode ?? 'classic') : null;
    $uiModeServer = in_array($uiModeServer, ['classic', 'modern'], true) ? $uiModeServer : 'classic';
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
            var IS_AUTH = {{ auth()->check() ? 'true' : 'false' }};
            var IS_HOMEPAGE = {{ $isHomepage ? 'true' : 'false' }};
            var STORAGE_KEY = 'ui_mode';
            var PENDING_KEY = 'ui_mode_pending';
            var mode = SERVER_MODE || 'classic';
            var pending = null;

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
            border: 1px solid rgba(15, 23, 42, 0.16);
            background: rgba(255, 255, 255, 0.92);
            color: rgba(15, 23, 42, 0.92);
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.10);
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
            will-change: transform;
        }

        .hm-sidebar-toggle i {
            font-size: 1.05rem;
            color: rgba(14, 165, 233, 0.95);
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

                    var classicIcon = document.getElementById('sidebar-toggle');
                    if (!classicIcon) return;

                    var classicTrigger = classicIcon.closest('a,button');
                    if (classicTrigger) {
                        classicTrigger.click();
                    } else {
                        classicIcon.click();
                    }
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

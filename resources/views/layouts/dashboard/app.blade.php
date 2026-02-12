<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="New vision Dashboard">
    <meta name="keywords" content="admin dashboard, New vision, web app">
    <meta name="author" content="Ahmed Nasr">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    </style>
</head>

<body class="">
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
        (function () {
            document.addEventListener('click', function (event) {
                var toggle = event.target.closest('#sidebar-toggle');
                if (!toggle) {
                    return;
                }

                event.preventDefault();

                var sidebar = document.querySelector('.page-sidebar');
                var header = document.querySelector('.page-main-header');

                if (sidebar) {
                    sidebar.classList.toggle('open');
                }

                if (header) {
                    header.classList.toggle('open');
                }
            });
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

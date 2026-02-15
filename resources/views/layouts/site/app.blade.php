<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

@include('layouts.site.header')

<body>
    <!-- page-wrapper Start-->
    <div class="page-wrapper">

        @include('layouts.site.navbar')

        <!-- Page Body Start -->

        <div class="page-body">

            @yield('content')

        </div>

        @include('layouts.site.footer')

    </div>
    <!-- page-wrapper end-->

    <!-- Additional plugin js -->

    <script src="{{ asset('assets/site/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/site/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/site/js/jquery.easing.1.3.js') }}"></script>
    <script src="{{ asset('assets/site/js/slick.min.js') }}"></script>
    <script src="{{ asset('assets/site/js/jquery.countTo.js') }}"></script>
    <script src="{{ asset('assets/site/js/jquery.skitter.min.js') }}"></script>
    <script src="{{ asset('assets/site/js/mixitup.min.js') }}"></script>
    <script src="{{ asset('assets/site/js/axios.min.js') }}"></script>
    <script src="{{ asset('assets/site/js/toastr.min.js') }}"></script>

    <!-- main js -->
    <script src="{{ asset('assets/site/js/main.js') }}"></script>


    <script src="./js/main.js"></script>

    @stack('js')
</body>

</html>

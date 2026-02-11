<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ setting(\App\Enums\SettingKey::SITE_TITLE->value, true) }}</title>    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- favicon -->
    <link rel=icon href="{{logo()}}" sizes="20x20" type="image/png">

    <!-- Additional plugin css -->
    <link rel="stylesheet" href="{{ asset('assets/site/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/site/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/site/css/slick.css') }}">
    <!-- icons -->
    <link rel="stylesheet" href="{{ asset('assets/site/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/site/css/skitter.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/site/css/line-awesome.min.css') }}">
    <!-- main css -->
    <link rel="stylesheet" href="{{ asset('assets/site/css/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/site/css/style.css') }}">
    <!-- responsive css -->
    <link rel="stylesheet" href="{{ asset('assets/site/css/responsive.css') }}">
    {{-- toster --}}
    <link rel="stylesheet" href="{{ asset('assets/site/css/toastr.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/site/css/timepicker.css') }}">


    <style>
        .featured-package-tour .thumb img {
            width: 100%;
            height: 190px;
            border-radius: 15px;
        }
        .featured-package-tour h3 {
            height:100px;

        }
        .single-package-included {
            width: 245px !important;
            height: 160px;
        }
        .nav-right-content ul li {
            padding: 0px 19px !important;
        }
        .single-destination-grid .thumb img{
            width: 100%;
            height: 300px;
        }
        @media (min-width: 992px) {
            .navbar-area .nav-container .navbar-collapse .navbar-nav li.menu-item-has-children .sub-menu .menu-item-has-children a::after {
                top: 9px;
            }
        }
        .single-input-wrap.style-two select {
            background: #F8F8F8;
            border: 1px solid #F8F8F8;
            height: 52px;
            width: 100%;
            padding: 0 18px;
            color: var(--paragraph-color);
        }
        .checkbox-wrap.checkbox-small .checkbox-label {
            color: var(--paragraph-color);
        }
        .day-title {
            color: var(--heading-color);
            font-family: var(--heading-font);
            font-size: 20px;
            font-weight: 700;
        }
        .featured-package-tour h3 {
            height: 75px;
            font-size: 19px;
        }
        .single-package-included {
            height: 100px !important;
        }
        .single-package-included {
            padding: 0px 0px 0px 0px !important;
            border: 0px solid #CFD3DE !important;
            margin-bottom: 0px;
        }
        .single-input-wrap.style-two input::placeholder {
            color: #f8f8f8 !important;
        }
    </style>
</head>

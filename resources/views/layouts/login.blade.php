<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    <title>J.Sons Communication</title>

    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />

    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <link href="https://kit.fontawesome.com/42d5adcbca.js" rel="stylesheet" crossorigin="anonymous">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/argon-dashboard.css') }}">
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />

    <!-- Optional: If you don't need nucleo-svg.css separately, you can remove the following line -->
    <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.css?v=2.0.4') }}" rel="stylesheet" />
</head>

@yield('content')

<!-- General JS Scripts -->
<script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/nicescroll/jquery.nicescroll.min.js')}} "></script>
@stack('custom-scripts')
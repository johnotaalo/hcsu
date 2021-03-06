<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('themes/nprogress/nprogress.css') }}">
    <script type="text/javascript" src="{{ asset('themes/nprogress/nprogress.js') }}"></script>
    <script type="text/javascript">
        window.Laravel = {
            baseUrl: "<?php echo URL::to('/');; ?>"
        };
    </script>
    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <app {!! $iframe ? ':iframe = "true"' : '' !!} case = "{{ $case_no }}"></app>
    </div>
</body>
</html>

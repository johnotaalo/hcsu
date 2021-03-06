<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'Laravel') }} - Focal Points</title>

	<link rel="stylesheet" type="text/css" href="{{ asset('themes/nprogress/nprogress.css') }}">
	<script type="text/javascript" src="{{ asset('themes/nprogress/nprogress.js') }}"></script>
	<!-- Scripts -->
	<script src="{{ asset('js/fp.js') }}" defer></script>

	<!-- Styles -->
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	
	<link href="{{ asset('css/fp.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <app></app>
    </div>
</body>
</html>

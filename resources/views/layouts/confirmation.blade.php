<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'Laravel') }}</title>

	<link rel="stylesheet" type="text/css" href="{{ asset('themes/nprogress/nprogress.css') }}">
	<script type="text/javascript" src="{{ asset('themes/nprogress/nprogress.js') }}"></script>

	<link rel="stylesheet" type="text/css" href="{{ asset('css/theme.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('fonts/feather/feather.min.css') }}">

	<script type="text/javascript" src="{{ asset('js/vue.min.js') }}"></script>
	<style>body { display: none; }</style>
</head>
<body class="d-flex align-items-center bg-auth border-top border-top-2 border-primary">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 col-md-6 col-xl-5 my-5">
				@yield('content')
			</div>
		</div>
	</div>
	<script type="text/javascript" src="{{ asset('plugins/axios.min.js') }}"></script>
	<script type="text/javascript">
		// const axios = require('axios').default
	</script>
	@yield('js')
</body>
</html>
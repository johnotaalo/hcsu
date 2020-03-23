<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ config('app.name', 'Laravel') }}</title>

	<!-- Libs CSS -->
    <link rel="stylesheet" href="{{ asset('fonts/feather/feather.min.css') }}">

    <!-- Theme CSS -->
      
    <link rel="stylesheet" href="{{ asset('css/theme.min.css') }}">
</head>
<body class="d-flex align-items-center bg-auth border-top border-top-2 border-primary">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 col-md-5 col-xl-4 my-5">
				<center><img src="{{ asset('images/UNLOGOBW.jpg') }}" style="width: 30%;"></center>
				@if ($errors->any())
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<h1 class="display-4 text-center mb-3">
					@yield('title')
				</h1>

				<!-- Subheading -->
				<p class="text-muted text-center mb-5">
					@yield('subheading')
				</p>
				<div id="app">
					@yield('content')
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="{{ asset('js/hcsu-auth.js') }}"></script>
	@yield('js')
</body>
</html>
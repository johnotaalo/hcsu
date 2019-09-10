@extends('auth.focalpoints.layouts.main')

@section('content')
{{ $errors }}
<form action="{{ route('login') }}" method="POST">

	{{ csrf_field() }}
	<h1 class="display-4 text-center mb-3">Sign in</h1>
	<p class="text-muted text-center mb-5">Login to access your dashboard.</p>

	<div class="form-group">
		<label>Email Address</label>
		<input type="email" class="form-control" placeholder="name@address.com" name="email">
	</div>

	<div class="form-group">
		<label>Password</label>
		<input type="password" class="form-control" name="password">
	</div>

	<button class="btn btn-lg btn-block btn-primary mb-3">Sign in</button>
</form>
@endsection

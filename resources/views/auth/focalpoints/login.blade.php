@extends('auth.focalpoints.layouts.main')

@section('title', 'Log In')

@section('subheading', "Use your credentials to login")

@section('content')
	{{ Form::open() }}
		<div class="form-group">
			{{ Form::label('email', 'E-Mail Address') }}
			{{ Form::email('email', null, ['class' => 'form-control']) }}
		</div>

		<div class="form-group">
			{{ Form::label('password', 'Password') }}
			{{ Form::password('password', ['class' => 'form-control']) }}
		</div>

		{{ Form::submit('Reset Password', ['class' => 'btn btn-lg btn-block btn-primary mb-3']) }}
	{{ Form::close() }}
@endsection
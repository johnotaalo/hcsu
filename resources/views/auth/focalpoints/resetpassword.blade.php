@extends('auth.focalpoints.layouts.main')

@section('title', 'Reset Password')

@section('subheading', "Enter your new password")

@section('content')
	{{ Form::open() }}

		<input type="hidden" name="token" value="{{ $token }}">
		<div class="form-group">
			{{ Form::label('email', 'E-Mail Address') }}
			{{ Form::email('email', null, ['class' => 'form-control']) }}
		</div>

		<div class="form-group">
			{{ Form::label('new_password', 'New Password') }}
			{{ Form::password('new_password', ['class' => 'form-control']) }}
		</div>

		<div class="form-group">
			{{ Form::label('new_password_confirmation', 'Confirm Password') }}
			{{ Form::password('new_password_confirmation', ['class' => 'form-control']) }}
		</div>

		{{ Form::submit('Reset Password', ['class' => 'btn btn-lg btn-block btn-primary mb-3']) }}
	{{ Form::close() }}
@endsection
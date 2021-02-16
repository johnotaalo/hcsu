@extends('layouts.confirmation')
@section('content')
@if(!$dispute)
<div class="card">
	<div class="card-body">
		<h3>Fill in the form below</h3>
		{{ Form::open(['route' => 'confirmation-dispute']) }}
		<div class="form-group">
			{!! Form::label('lastname', "Last Name") !!}
			{!! Form::text('lastname', old('lastname'), ['class' => ($errors->has('lastname')) ? 'form-control is-invalid' :'form-control']) !!}
			@error('lastname')
			<div class="invalid-feedback">{{ $message }}</div>
			@enderror
		</div>
		<div class="form-group">
			{!! Form::label('othernames', "Other Names") !!}
			{!! Form::text('othernames', old('othernames'), ['class' =>  ($errors->has('othernames')) ? 'form-control is-invalid' :'form-control']) !!}
			@error('othernames')
			<div class="invalid-feedback">{{ $message }}</div>
			@enderror
		</div>
		<div class="form-group">
			{!! Form::label('email', "Email Address") !!}
			{!! Form::email('email', old('email'), ['class' =>  ($errors->has('email')) ? 'form-control is-invalid' :'form-control']) !!}
			@error('email')
			<div class="invalid-feedback">{{ $message }}</div>
			@enderror
		</div>
		<div class="form-group">
			{!! Form::label('agency', "Agency") !!}
			{!! Form::select('agency', [null => '-- Select an Agency --'] + $agencies, old('agency'), ['class' =>  ($errors->has('agency')) ? 'form-control is-invalid' :'form-control']) !!}
			@error('agency')
			<div class="invalid-feedback">{{ $message }}</div>
			@enderror
		</div>

		<div class="form-group">
			{!! Form::label('index_no', "Index No") !!}
			{!! Form::text('index_no', old('index_no'), ['class' =>  ($errors->has('index_no')) ? 'form-control is-invalid' :'form-control']) !!}
			@error('index_no')
			<div class="invalid-feedback">{{ $message }}</div>
			@enderror
		</div>

		<button class="btn btn-primary btn-block">Submit Data</button>
		{{ Form::close() }}
	</div>
</div>
@else
<div class="card">
	<div class="card-body">
		<center>
			<i class="fe fe-check-circle" style="font-size: 6em"></i>

			<h3 class="mt-4">A dispute of information has already been launched. We shall update your information accordingly.</h3>

			<a class="btn btn-primary btn-block" href="{{ route('clients-home') }}">Refresh page</a>
		</center>
	</div>
</div>
@endif
@endsection
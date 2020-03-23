@extends('auth.focalpoints.layouts.main')

@section('content')
<!-- {{ $errors }} -->
<form action="{{ route('login') }}" method="POST">

	{{ csrf_field() }}
	<h1 class="display-4 text-center mb-3">Sign in</h1>
	<p class="text-muted text-center mb-5">Login to access your dashboard.</p>

	<div class="form-group">
		<label>Log In To:</label>
		<select class="form-control" name = "location" value = "{{ old('location') }}">
			<option value="data-manager">Data Manager (HCSU Staff Members)</option>
			<option value="client-portal">Client Portal (UNON, UNEP, UN-HABITAT)</option>
		</select>
	</div>

	<div class="form-group">
		<label id="username-label">Email Address</label>
		<input type="email" class="form-control" placeholder="name@address.com" name="email" value="{{ old('email') }}">
		<small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
	</div>

	<div class="form-group">
		<label id="password-label">Password</label>
		<input type="password" class="form-control" name="password">
		<small id="passwordHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
	</div>

	<button class="btn btn-lg btn-block btn-primary mb-3">Sign in</button>
</form>
@endsection

@section('js')
<script type="text/javascript">
	$(document).ready(function(){
		toggleLabels()
	})

	$("select[name='location']").change(function(){
		toggleLabels()
	});

	function toggleLabels(){
		var location = $("select[name='location']").val()

		if (location == "data-manager") {
			$('#username-label').text("Email Address");
			$('#password-label').text("Password");
			$('input[name="email"]').attr('type', 'email');

			$('#emailHelp').text("Email Address issued in HCSU");
			$('#passwordHelp').text("Password issued in HCSU");
		}else{
			$('#username-label').text("AD Username");
			$('#password-label').text("AD Password");
			$('input[name="email"]').attr('type', 'text');

			$('#emailHelp').text("The username you use on your office computer");
			$('#passwordHelp').text("The password you use on your office computer");
		}
	}
</script>
@endsection
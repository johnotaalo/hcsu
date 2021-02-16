@extends('layouts.confirmation')

@section('content')
<div class="card" id = "card-app">
	<form method="POST" action="{{ route('confirm-user') }}">
		@csrf
		<input type="hidden" name="principal_id" value="{{ $principal->ID }}">
		<div class="card-body text-center">
			<div class="avatar avatar-xl card-avatar card-avatar-top">
				@if($principal->image_link != "/storage/")
				<img src="{{ route('principal-photo', ['host_country_id' => $principal->HOST_COUNTRY_ID]) }}" class="avatar-img rounded-circle border border-4 border-card" style="background: #FCE4EC;">
				@else
				<img class="avatar-img rounded-circle border border-4 border-card" src="{{ asset('images/no_avatar.svg') }}">
				@endif
			</div>

			<h2 class="card-title">{{ $principal->fullname }}</h2>
			<h4>{{ $principal->latest_contract->ACRONYM }}</h4>
			<h4>Host Country ID: {{ $principal->HOST_COUNTRY_ID }}</h4>
		</div>

		<div class="card-footer card-footer-boxed">
			<div class="row align-items-center justify-content-between">
				<div class="col-auto">
					<p>Is this you?</p>
				</div>
				<div class="col-auto">
					<button class="btn btn-sm btn-success"><i class="fe fe-check"></i>&nbsp;Yes. This is me</button>
					<a class="btn btn-sm btn-danger" href="{{ route('confirmation-cancel', ['id' => $principal->ID]) }}"><i class="fe fe-x"></i>&nbsp;No. This is not me</a>
				</div>
			</div>
		</div>
	</form>
</div>
@endsection

@section('js')
<script type="text/javascript">
	var app = new Vue({
		el: "#card-app",
		data: {
			message: "Testing",
			principalID: "{{ $principal->ID }}"
		},
		created(){

		},
		methods: {
			confirm(principalID){
				axios.get(`/api/users/confirm`)
				.then(res => {
					alert(res.data);
				})
				.catch(error => {
					alert(error.message);
				});
			}
		}
	})
</script>
@endsection
@extends('layouts.status')

@section('content')
<div class="text-center">
	@if($status == "completed")
	<img src="{{ asset('img/tick.svg') }}" style="width: 200px;height: 200px;">

	<!-- Preheading -->
	<h6 class="text-uppercase text-success mb-4">
		SUCCESS
	</h6>

	<!-- Heading -->
	<h1 class="display-4 mb-3">
		SUCCESSFULLY SIGNED DOCUMENT.
	</h1>

	<!-- Subheading -->
	<p class="text-muted mb-4">
		The document can be downloaded from the link below 
	</p>

	<!-- Button -->
	<a href="{{ route('download-docusigned-doc', ['envelope_id'=> $envelope_id]) }}" class="btn btn-lg btn-primary">
		Download Document
	</a>
	@else
	<img src="{{ asset('img/error.svg') }}" style="width: 200px;height: 200px;">

	<!-- Preheading -->
	<h6 class="text-uppercase text-danger mb-4">
		ERROR
	</h6>

	<!-- Heading -->
	<h1 class="display-4 mb-3">
		THERE WAS AN ERROR SIGNING THE DOCUMENT.
	</h1>
	@endif

</div>
@endsection
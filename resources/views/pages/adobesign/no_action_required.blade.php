@extends('layouts.status')

@section('content')
<div class="text-center">
	<center><img src="{{ asset('images/thumbs-up.png') }}" style="width: 100%;"></center>
	<h1>No Action is required from you</h1>
	<p>Looks like the document has already been signed or Adobe has not sent back an agreement id.</p>
	<p>Contact the Administrator in case you have not signed the document yet.</p>
</div>
@endsection
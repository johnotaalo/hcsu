<style type="text/css">
</style>
<p>Dear {{ strtoupper($application->user->focal_point->LAST_NAME) }}, {{ ucwords(strtolower($application->user->focal_point->OTHER_NAMES)) }}, <p>

<p>We are in receipt of your VAT Exemtion Application.</p>
<p>Your reference is: <b>Case No. {{ $application->CASE_NO }}</b></p>

<table class="email-table">
	<tr>
		<th style="text-align: left;">Supplier:</th>
		<td>{{ $application->supplier->SUPPLIER_NAME }}</td>
	</tr>
	<tr>
		<th style="text-align: left;">Invoice No:</th>
		<td>{{ $application->data->vat->pfNo }}</td>
	</tr>

	<tr>
		<th style="text-align: left;">Total VAT Amount</th>
		<td>{{ $application->data->vat->vatAmount }}</td>
	</tr>
</table>

<p>Regards</p>
<p>Admin,<p>
<p>Host Country Services Unit</p>
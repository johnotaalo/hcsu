<p>Dear {{ strtoupper($application->user->focal_point->LAST_NAME) }}, {{ ucwords(strtolower($application->user->focal_point->OTHER_NAMES)) }}, <p>

<p>We are in receipt of your VAT Exemtion Application.</p>
<p><b>Case No. {{ $application->CASE_NO }}</b></p>

<table>
	<tr>
		<th>Supplier:</th>
		<td>{{ $application->supplier->SUPPLIER_NAME }}</td>
	</tr>
</table>

<p>Regards</p>
<p>Admin,<p>
<p>Host Country Services Unit</p>
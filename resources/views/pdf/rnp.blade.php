<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Returned Red Number Plate</title>
	<style type="text/css">
		body {
			font-family: 'arial';
			font-size: 17px;
		}

		.address{
			margin: 0 !important;
		}

		@page {
			header: page-header;
			footer: page-footer;
		}

		table.plates{
			font-size: 13px;
		}

		table.plates th{
			font-size: 12px;
		}

		table.plates, table.plates th, table.plates td{
			border: 1px solid #000000;
		}

		table.plates th, table.plates td{
			padding: 6px;
		}
	</style>
</head>
<body>
	<table width="100%">
		<tr>
			<td align="center">
				<img src="{{ public_path('img/HEADER.png') }}" style="width: 70%;" />
			</td>
		</tr>
	</table>
		
	<table width="100%">
		<tbody>
			<tr>
				<td width="50%"></td>
				<td width="50%" align="right">{{ date('d F Y', strtotime($data->created_at)) }}</td>
			</tr>
		</tbody>
	</table>
	<!-- <div>
		<p style="float: right;"></p>
	</div> -->
	<div style="">
		<p style="margin: 0 !important; margin-bottom: 2px;">The Director General - NTSA</p>
		<p style="margin: 0 !important; margin-bottom: 2px;">P.O. Box 52692-00200</p>
		<p style="margin: 0 !important; margin-bottom: 2px;">Nairobi, Kenya.</p>
	</div>

	<p>Dear Sir,</p>
	<table width="100%">
		<tr>
			<td align="center"><strong style="text-decoration: underline !important;">RE: Surrender of used United Nations Reflective Number Plates</strong></td>
		</tr>
	</table>
	<div style="margin-bottom: 130px;">
		<p>The United Nations Office at Nairobi (UNON) is hereby returning to National Transport and Safety Authority (NTSA) used red reflective number plates for disposal.</p>
		<p>The used reflective number plates have been retrieved from personal and official UN diplomatic vehicles that have been disposed. A list is attached herewith for ease of reference.</p>
		<p>UNON would be grateful for NTSA's acknowledgement for receiving the number plates.</p>
		<p>Thank you for your usual cooperation.</p>
	</div>

	<table width="100%">
		<tr>
			<td align="center">
				<p>Samuel Olago</p>
				<p>Manager</p>
				<p>Host Country Services Unit</p>
			</td>
		</tr>
	</table>

	<p style="margin-top: 150px;">Cc: Ministry of Foreign Affairs of the Republic of Kenya</p>

	<htmlpagefooter name="page-footer">
		<hr style="width: 100%; color: black;" />
		<table width="100%">
			<tr>
				<td align="center" style="font-size: 13px; font-family: 'Courier New', Courier; padding: 2px; color: rgb(21,21,21);">SUPPORT SERVICES SERVICE.TEL: 254 (0)20 7623628. FAX: 254 (0)20 7623931</td>
			</tr>
		</table>
	</htmlpagefooter>

	<pagebreak />

	<table style="width: 100%;">
		<tr>
			<td align="center" style="font-weight: bold !important">
				<strong style="text-transform: uppercase;">
					<p>LIST OF USED REFLECTIVE NUMBER PLATES</p>
					<p>RETURNED TO NTSA BY UNITED NATIONS OFFICE AT NAIROBI (UNON)</p>
					<p>{{ date("dS F Y", strtotime($data->RNP_DATE)) }}</p>
				</strong>
			</td>
		</tr>
	</table>

	<table class="plates" width="100%" style="border-collapse: collapse;">
		<tr>
			<th>#</th>
			<th>Organisation</th>
			<th>Plate No</th>
			<th>Name of Staff</th>
			<th>Measurements</th>
			<th width="25%">Remarks</th>
		</tr>
		<tbody>
			<?php $count = 1; ?>
			@foreach($data->plates as $plate)
			<tr>
				<td>{{ $count }}</td>
				<td>
					@if($plate->clientType == "agency")
						Official
					@elseif($plate->clientType == "staff")
						{{ $plate->client->latest_contract->ACRONYM }}
					@elseif($plate->clientType == "dependent")
						{{ $plate->client->principal->latest_contract->ACRONYM }}
					@endif
				</td>
				<td>{{ $plate->PLATE_NO }}</td>
				<td>
					@if($plate->clientType == "agency")
						{{ $plate->client_details->ACRONYM }}
					@elseif($plate->clientType == "staff")
						{{ strtoupper($plate->client->LAST_NAME) }}, {{ format_other_names($plate->client->OTHER_NAMES) }}
					@elseif($plate->clientType == "dependent")
						{{ strtoupper($plate->client->LAST_NAME) }}, {{ format_other_names($plate->client->OTHER_NAMES) }}
					@endif
				</td>

				<td>{{ $plate->MEASUREMENTS }}</td>
				<td></td>
				<?php $count++; ?>
			</tr>
			@endforeach
		</tbody>
	</table>
</body>
</html>
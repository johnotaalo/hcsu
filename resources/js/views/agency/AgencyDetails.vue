<template>
	<div>
		<div class="row">
			<div class="col-md">
				<legend>Agency Details</legend>
				<div class="form-group">
					<label for="agency_name">Agency Name</label>
					<b-form-input placeholder="Enter Agency Name"></b-form-input>
				</div>
				
			</div>
		</div>

		<div class="row">
			<div class="col-md">
				<div class="form-group">
					<label for="agency_acronym">Agency Acronym</label>
					<b-form-input placeholder="Enter Agency Acronym"></b-form-input>
				</div>
			</div>

			<div class="col-md">
				<div class="form-group">
					<label for="agency_acronym">Agency PIN</label>
					<b-form-input placeholder="Enter Agency PIN"></b-form-input>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md">
				<legend>Contact and Location Information</legend>
			</div>
		</div>

		<div class="row">
			<div class="col-md">
				<div class="form-group">
					<label for="agency_acronym">P.O. BOX</label>
					<b-form-input placeholder="Enter Agency P.O. BOX"></b-form-input>
				</div>
			</div>

			<div class="col-md">
				<div class="form-group">
					<label for="agency_acronym">Postal Code</label>
					<b-form-input placeholder="Enter Postal Code"></b-form-input>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md">
				<div class="form-group">
					<label for="agency_acronym">Location</label>
					<b-form-textarea placeholder="Enter Location" rows="3"></b-form-textarea>
				</div>
			</div>

			<div class="col-md">
				<div class="form-group">
					<label for="agency_acronym">Physical Address</label>
					<b-form-textarea placeholder="Enter Physical Address" rows="3"></b-form-textarea>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md">
				<legend>Documents</legend>
			</div>
		</div>

		<div class="row">
			<div class="col-md">
				<legend>Host Country Agreement</legend>
				<b-form-file placeholder="Choose a file..." drop-placeholder="Drop file here..."></b-form-file>
			</div>
		</div>
	</div>
	
</template>

<script type="text/javascript">
	export default {
		name: "AgencyDetails",
		data(){
			return {

			}
		}
	}
</script>
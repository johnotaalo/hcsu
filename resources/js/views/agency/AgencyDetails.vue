<template>
	<div>
		<div class="row">
			<div class="col-md">
				<legend>Agency Details</legend>
				<div class="form-group">
					<label for="agency_name">Agency Name</label>
					<b-form-input placeholder="Enter Agency Name" v-model="value.agency_name"></b-form-input>
				</div>
				
			</div>
		</div>

		<div class="row">
			<div class="col-md">
				<div class="form-group">
					<label for="agency_acronym">Agency Acronym</label>
					<b-form-input placeholder="Enter Agency Acronym" v-model="value.agency_acronym"></b-form-input>
				</div>
			</div>

			<div class="col-md">
				<div class="form-group">
					<label for="agency_pin">Agency PIN</label>
					<b-form-input placeholder="Enter Agency PIN" v-model="value.agency_pin"></b-form-input>
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
					<label for="agency_pobox">P.O. BOX</label>
					<b-form-input placeholder="Enter Agency P.O. BOX" v-model="value.agency_pobox"></b-form-input>
				</div>
			</div>

			<div class="col-md">
				<div class="form-group">
					<label for="agency_postal_code">Postal Code</label>
					<b-form-input placeholder="Enter Postal Code" v-model="value.agency_postal_code"></b-form-input>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md">
				<div class="form-group">
					<label for="agency_location">Location</label>
					<b-form-textarea placeholder="Enter Location" rows="3" v-model="value.agency_location"></b-form-textarea>
				</div>
			</div>

			<div class="col-md">
				<div class="form-group">
					<label for="agency_physical_address">Physical Address</label>
					<b-form-textarea placeholder="Enter Physical Address" rows="3" v-model="value.agency_physical_address"></b-form-textarea>
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
				<b-form-file placeholder="Choose a file..." drop-placeholder="Drop file here..." v-model="value.agency_hca"></b-form-file>
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
		},
		props: {
			'value': { type: null, default: null },
		}
	}
</script>
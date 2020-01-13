<template>
	<div>
		<div class="row">
			<div class="col-md">
				<legend>Agency Details</legend>

				<div class="form-group">
					<b-img class ="avatar-img border border-4 border-body" style="width: 100px;height: 100px;background: #FCE4EC;" v-bind="options.imageProps" :src="agencyLogoURL"/>
					<input type="file" ref="agencyFile" name="agencyFile" @change="onAgencyFileChange($event.target.name, $event.target.files)" style="display:none">
					<b-button variant="primary" size="sm" @click="$refs.agencyFile.click()">Upload Logo</b-button>
				</div>
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
				options: {
					imageProps: { blank: true, blankColor: '#777', width: 100, height: 100, class: 'm1' }
				}
			}
		},
		created(){
			this.value.image = {}
			if (!this.value.image) {
				this.value.image.url = ""
			}
		},
		methods: {
			onAgencyFileChange(fieldName, file){
				const { maxSize } = this
				let imageFile = file[0]

				//check if user actually selected a file
				if (file.length>0) {
					let size = imageFile.size / maxSize / maxSize
					if (!imageFile.type.match('image.*')) {
						alert("Please choose an image file");
					} else if (size>1) {
						// check whether the size is greater than the size limit
						alert('Your file is too big! Please select an image under 1MB')
					} else {
						// Append file into FormData & turn file into image URL
						let formData = new FormData()
						let imageURL = URL.createObjectURL(imageFile)
						this.value.image = {}
						this.value.image.url = imageURL
						this.value.image.file = imageFile
						this.value.image_link = imageURL
						this.options.imageProps.blank = false
					}
				}
			}
		},
		computed: {
			agencyLogoURL(){
				if(this.value.image.url){
					return this.value.image.url
				}
			}
		},
		props: {
			'value': { type: Object, default: () => { return {} } },
		}
	}
</script>
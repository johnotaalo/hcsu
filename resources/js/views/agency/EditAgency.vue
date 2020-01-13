<template>
	<div>
		<b-card>
			<form-wizard title="Add Agency" finishButtonText="Update Agency Details" @on-complete="onComplete">
				<tab-content title = "Agency Details" icon="fe fe-briefcase">
					<agency-details v-model="form.agencyDetails"></agency-details>
				</tab-content>
				<tab-content title = "Focal Point(s)" icon="fe fe-users">
					<agency-focal-points :agency = "$route.params.id" v-model="form.focalPoints" @focalPointsListener="focalPointsChange"></agency-focal-points>
				</tab-content>
			</form-wizard>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../core/Form'
	import {FormWizard, TabContent} from 'vue-form-wizard'
	import AgencyDetails from "./AgencyDetails"
	import AgencyFocalPoints from "./AgencyFocalPoints"
	export default {
		components: { FormWizard, TabContent, AgencyDetails, AgencyFocalPoints },
		data(){
			return {
				form: new Form({
					agencyDetails: {
						host_country_id: this.$route.params.id,
						agency_name: "",
						agency_acronym: "",
						agency_pin: "",
						agency_pobox: "",
						agency_postal_code: "",
						agency_location: "",
						agency_physical_address: "",
						agency_hca: "",
						image: {}
					},
					focalPoints: []
				})
			}
		},
		created(){
			this.getAgencyData(this.form.agencyDetails.host_country_id)
		},
		methods: {
			getAgencyData: function(host_country_id){
				axios.get(`/api/agencies/get/${host_country_id}`)
				.then(res => {
					if (res.data) {
						var agency = res.data
						this.form.agencyDetails.agency_name = agency.AGENCYNAME
						this.form.agencyDetails.agency_acronym = agency.ACRONYM
						this.form.agencyDetails.agency_pin = agency.PIN_NO
						this.form.agencyDetails.agency_pobox = agency.POBOX
						this.form.agencyDetails.agency_postal_code = agency.POSTCODE
						this.form.agencyDetails.agency_location = agency.LOCATION
						this.form.agencyDetails.agency_physical_address = agency.PHY_ADDRESS

						this.form.focalPoints = agency.focal_points
					}
				})
			},
			onComplete: function(){
				// console.log(this.form.data())
				this.form.put(`agencies/update/${this.form.agencyDetails.host_country_id}`)
				.then(data => {
					// console.log(data)
				})
			},
			focalPointsChange(value){
				this.form.focalPoints = value
			}
		}
	}
</script>
<template>
	<div>
		<b-card>
			<form-wizard title="Add Agency" finishButtonText="Register Agency" @on-complete="onComplete">
				<tab-content title = "Agency Details" icon="fe fe-briefcase">
					<agency-details v-model="form.agencyDetails"></agency-details>
				</tab-content>
				<tab-content title = "Focal Point(s)" icon="fe fe-users">
					<agency-focal-points v-model="form.focalPoints" @focalPointsListener="focalPointsChange"></agency-focal-points>
				</tab-content>
			</form-wizard>
		</b-card>
	</div>
</template>
<script type="text/javascript">
	import {FormWizard, TabContent} from 'vue-form-wizard'
	import AgencyDetails from "./agency/AgencyDetails"
	import AgencyFocalPoints from "./agency/AgencyFocalPoints"
	import Form from '../core/Form'

	export default {
		components: { FormWizard, TabContent, AgencyDetails, AgencyFocalPoints },
		data() {
			return {
				form: new Form({
					agencyDetails: {
						agency_name: "",
						agency_acronym: "",
						agency_pin: "",
						agency_pobox: "",
						agency_postal_code: "",
						agency_location: "",
						agency_physical_address: "",
						agency_hca: "",
						image: {
							url: "",
							link: "",
							file: ""
						}
					},
					focalPoints: []
				})
			}
		},
		methods: {
			focalPointsChange(value){
				this.form.focalPoints = value
			},
			onComplete: function(){
				var em = this
				em.form.post('/agencies/add')
				.then((res) => {
					console.log(res)
				})
			}
		}
	}
</script>
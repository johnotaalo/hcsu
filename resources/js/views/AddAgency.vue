<template>
	<div>
		<b-card>
			<form-wizard title="Add Agency" finishButtonText="Register Agency">
				<tab-content title = "Agency Details" icon="fe fe-briefcase">
					<agency-details></agency-details>
				</tab-content>
				<tab-content title = "Focal Point(s)" icon="fe fe-users">
					<agency-focal-points></agency-focal-points>
				</tab-content>
			</form-wizard>
		</b-card>
	</div>
</template>
<script type="text/javascript">
	import {FormWizard, TabContent} from 'vue-form-wizard'
	import AgencyDetails from "./agency/AgencyDetails"
	import AgencyFocalPoints from "./agency/AgencyFocalPoints"
	export default {
		components: { FormWizard, TabContent, AgencyDetails, AgencyFocalPoints },
		data() {
			return {

			}
		}
	}
</script>
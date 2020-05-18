<template>
	<div>
		<b-card title="Edit Client">
			<loading :active.sync="loading" :can-cancel="false" :is-full-page="true"></loading>
			<other-client-form v-model = "form"></other-client-form>

			<b-button variant="primary" @click="editClient">Update Data</b-button>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../core/Form'
	import OtherClientForm from './components/OtherClientForm'

	export default{
		components: { OtherClientForm },
		data(){
			return {
				loading: false,
				id: this.$route.params.id,
				form: new Form({
					host_country_id: this.$route.params.id,
					lastName: '',
					otherNames: '',
					nationality: '',
					dob: '',
					clientType: '',
					agency: '',
					description: '',
					passport: {}
				})
			}
		},
		mounted(){
			var _this = this
			_this.loading = true

			axios.all([_this.getClientDetails()])
			.then(axios.spread((client) => {
				_this.loading = false
				var data = client.data
				_this.form.lastName = data.LAST_NAME
				_this.form.otherNames = data.OTHER_NAMES
				_this.form.nationality = { id: data.nationality.id, label: data.nationality.name }
				_this.form.dob = data.DATE_OF_BIRTH
				_this.form.clientType = data.TYPE
				_this.form.agency = { id: data.agency.AGENCY_ID, label: data.agency.AGENCYNAME }
				_this.form.description = data.DESCRIPTION
				_this.form.passport = {
					no: data.PASSPORT_NO,
					issue_date: data.ISSUE_DATE,
					expiry_date: data.EXPIRY_DATE,
					country: { id: data.passport_country.id, label: data.passport_country.name }
				}
				// _this.form.nationality
			}))
		},
		methods: {
			getClientDetails(){
				return axios.get(`api/other/clients/get/${this.id}`);
			},
			editClient(){
				this.loading = true
				this.form.put('/other/clients')
				.then(res => {
					this.loading = false
					this.$notify({
						group: 'foo',
						title: 'Success',
						text: "Successfully updated client details",
						type: "success"
					})
					// this.$router.push({ name: 'clients.other'})
				})
				.catch(error => {
					this.loading = false
					this.$notify({
						group: 'foo',
						title: 'Error',
						text: "There was an error adding client",
						type: "error"
					})	
				})
			}
		}
	}
</script>
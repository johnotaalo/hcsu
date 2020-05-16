<template>
	<div>
		<b-card title="Add Client">
			<loading :active.sync="loading" :can-cancel="false" :is-full-page="true"></loading>
			<other-client-form v-model = "form"></other-client-form>

			<b-button variant="primary" @click="addClient">Add Client</b-button>
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
				form: new Form({
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
		methods: {
			addClient(){
				this.loading = true
				this.form.post('/other/clients')
				.then(res => {
					this.loading = false
					this.$notify({
						group: 'foo',
						title: 'Success',
						text: "Successfully added client",
						type: "success"
					})
					this.$router.push({ name: 'clients.other'})
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
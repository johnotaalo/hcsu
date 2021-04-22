<template>
	<div>
		<b-card>
			<b-row>
				<b-col>
					<b-form-group>
						<label>System</label>
						<b-select v-model="form.system" :options="systemOptions"></b-select>
					</b-form-group>
				</b-col>
				<b-col>
					<b-form-group>
						<label>Form A Case</label>
						<b-input v-model="formACase" />
					</b-form-group>
				</b-col>
			</b-row>
			
			<b-button variant="primary" size="sm" @click="searchFormA">Search</b-button>

			<hr>

			<div v-if="formADetails">
				<b-alert :show="formADetails.LOGBOOK_CASE_NO" variant="warning" dismissible>
					<h4 class="alert-heading">Warning</h4>
					<p>This case may already have a logbook case no. Kindly confirm first using this case no: {{ formADetails.LOGBOOK_CASE_NO }}. If the case cannot be opened, kindly ignore this notification</p>
				</b-alert>

				<h2>Required Variables</h2>
				<b-row>
					<b-col cols="4">
						<b-form-group label="Client Name">
							<b-input v-model = "form.client_name" readonly/>
						</b-form-group>
					</b-col>

					<b-col cols="4">
						<b-form-group label="Assigned Plate">
							<b-input v-model = "form.assignedPlate" />
						</b-form-group>
					</b-col>

					<b-col cols="4">
						<b-form-group label="Pro 1B Case No">
							<b-input v-model = "form.pro_1b_case_no" readonly/>
						</b-form-group>
					</b-col>
				</b-row>
				<b-row>
					<b-col>
						<b-button variant="primary" @click="submitDetails">Submit Details</b-button>
					</b-col>
				</b-row>
			</div>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	import Form from "../../core/Form"
	export default {
		data(){
			return {
				formACase: null,
				formASystem: null,
				formADetails: null,
				form: new Form({
					system: "",
					assignedPlate: "",
					client_name: "",
					pro_1b_case_no: ""
				}),
				systemOptions: [
					{ value: "old", text: "Old Processmaker" },
					{ value: "new", text: "New Processmaker" }
				]
			}
		},
		mounted(){
			console.log("Component mounted")
		},
		methods: {
			searchFormA: function(){
				axios.post("/api/vehicle/form-a/search", { case: this.formACase, system: this.form.system })
				.then(res => {
					this.formADetails = res.data

					this.form.assignedPlate = res.data.PLATE_NO
					this.form.client_name = res.data.client_details.name
					this.form.pro_1b_case_no = res.data.PRO_1B_CASE_NO
				})
			},
			submitDetails: function(){
				this.form.post(`vehicle/form-a/create-logbook-case/${this.formACase}`)
				.then(res => {
					this.$swal("Success", `Successfully created logbook case. Case No: ${res.logbook_case_no}`, 'success')
					.then(() => {
						this.form = new Form({
							assignedPlate: "",
							client_name: "",
							pro_1b_case_no: ""
						})

						this.formADetails = null
						this.formACase = null
					})
				})
				.catch(error => {
					this.$swal("Oops!", `${error.message}`, "error")
				})
			}
		}
	}
</script>
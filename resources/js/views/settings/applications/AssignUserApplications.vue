<template>
	<div>
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">User Application</h3>
			</div>
			<div class="card-body">
				<div class="row mb-2">
					<label class="col-sm-2">
						Applicant Details
					</label>
					<div class="col">
						<ul>
							<p><b>Name:</b>
								<span v-if="application.applicant_type == 'staff'">{{ application.applicant_details.LAST_NAME }}, {{ application.applicant_details.OTHER_NAMES }}</span>
								<span v-if="application.applicant_type == 'agency'">{{ application.applicant_details.ACRONYM }}</span>
								<span v-if="application.applicant_type == 'dependent'">{{ application.applicant_details.LAST_NAME }}, {{ application.applicant_details.OTHER_NAMES }}<br/><small><i>Dependent of {{ application.applicant_details.principal.principal_name }} ({{ application.applicant_details.principal.latest_contract.ACRONYM }})</i></small></span>
							</p>
							<p  v-if="application.applicant_type == 'staff'"><b>Email:</b>
								<span>{{ application.applicant_details.EMAIL }}</span>
							</p>
						</ul>
					</div>
				</div>
				<div class="row mb-2">
					<label class="col-sm-2">
						Process
					</label>
					<div class="col">
						<b-input class = "form-control" v-model = "application.process.PRO_TITLE" disabled></b-input>
					</div>
				</div>

				<div class="row mb-2">
					<label class="col-sm-2">
						Files
					</label>
					<div class="col">
						<ul v-if="application.files.length > 0">
							<li v-for="(file, key) in application.files"><a target="_blank" :href="`uploads/${file.id}`">{{ file.FILE_DESCRIPTION }}</a></li>
						</ul>
						<h3 v-else>No files were uploaded</h3>
					</div>
				</div>

				<div class="row mb-2">
					<label class="col-sm-2">
						Comments
					</label>
					<div class="col">
						<b-textarea v-model ="application.COMMENT" disabled></b-textarea>
					</div>
				</div>

				<legend>Assignment Details</legend>

				<div v-if="application.ASSIGNED_TO == null && application.STATUS == 'SUBMITTED' && application.CURRENT_USER == 'SUPERVISOR'">
					<div class="form-group">
						<label>Approve Application</label>
						<b-form-radio-group :options="approveOptions" value-field="item" text-field="name" v-model ="form.submitApplication"></b-form-radio-group>
					</div>

					<div v-if="form.submitApplication == true">
						<div class="form-group">
							<label>Assign Case To:</label>
							<v-select :options="users" v-model="form.assignedTo">
								<template slot="no-options">
									Type to search for a user
								</template>
								<template slot="option" slot-scope="option">
									{{ option.USR_LASTNAME }}, {{ option.USR_FIRSTNAME }}
								</template>

								<template slot="selected-option" slot-scope="option">
									{{ option.USR_LASTNAME }}, {{ option.USR_FIRSTNAME }}
								</template>
							</v-select>
						</div>
					</div>

					<div class="form-group">
						<label>Supervisor Comments</label>
						<b-textarea v-model="form.supervisorComments"></b-textarea>
					</div>

					<b-button size="sm" variant="primary" @click="proceed">Proceed</b-button>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../../core/Form'
	export default {
		data(){
			return {
				id: 0,
				application: {},
				users: [],
				approveOptions: [{
					item: true, name: "Approve"
				},{
					item: false, name: "Reject"
				}],
				form: new Form({
					host_country_id: 0,
					submitApplication: false,
					assignedTo: "",
					supervisorComments: ""
				})
			}
		},
		created(){
			this.id = this.$route.params.id
			this.getData(this.$route.params.id)
			this.getUsers()
		},
		methods: {
			getData(paramId){
				var vm = this
				axios.get(`/api/focal-points/applications/get/${paramId}`)
				.then(res => {
					vm.application = res.data
				});
			},
			viewuploads: function(id){
				window.location = "/uploads/" + id
			},
			getUsers: function(){
				axios.get('/api/focal-points/applications/users')
				.then(res => {
					this.users = res.data
				});
			},
			proceed(){
				this.form.host_country_id = this.application.HOST_COUNTRY_ID
				var submitApplication = this.form.submitApplication

				this.form.post(`focal-points/applications/assign/${this.id}`)
				.then(res => {
					var text = (submitApplication) ? "Successfully assigned case" : "The case has been rejected and returned to the client for updates";
					this.$swal("Success", text, "success")
					.then(() => {
						this.$router.push({ name: 'user-applications.all' })
					})
				})
				.catch(error => {
					this.$swal('Error', error.message, "error")
				})
			}
		}
	}
</script>
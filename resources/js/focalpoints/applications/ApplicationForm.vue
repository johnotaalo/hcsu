<template>
	<div>
		<div class="card"> 
			<div class="card-body">
				<div class="form-group" v-if="id != 0">
					<label class = "control-label">Supervisor Comments</label>
					<b-textarea disabled v-model="application.data.SUPERVISOR_COMMENTS"></b-textarea>
				</div>
				<div class="form-group">
					<label class = "control-label">Process</label>
					<v-select v-model="form.process" :options="processes" label="PRO_TITLE">
						<template slot="no-options">
							Type to search for a process
						</template>

						<template slot="option" slot-scope="option">
							{{ option.PRO_TITLE }}
						</template>
					</v-select>
				</div>
				<div class="form-group">
					<label class = "control-label">Client</label>
					<div v-if="showClientForm">
						<search-client v-model="form"></search-client>
						<button v-if="id != 0" class="btn btn-sm btn-warning" @click="showClientForm = false">Revert</button>
					</div>
					<div v-else>
						<p>Picked Details: <button class="btn btn-link" @click="showClientForm = true">Change</button></p>
						<b>Type: </b>{{ application.data.applicant_type | capitalize }}<br/>
						<b>Name: </b><span v-if="application.data.applicant_type == 'staff'">{{ application.data.applicant_details.LAST_NAME }}, {{ application.data.applicant_details.OTHER_NAMES }}</span>
									<span v-if="application.data.applicant_type == 'agency'">{{ application.data.applicant_details.ACRONYM }}</span>
					</div>
				</div>
				<!-- <div class="form-group">
					<label>Upload the required documents</label>
					<b-form-file v-model="form.uploads"></b-form-file>
				</div> -->
				<div class="form-group">
					<label class = "control-label">Upload the required documents</label>
					<div class="uploads">
						<button class="btn btn-sm btn-primary mb-5" @click="addRow(form.uploads)"><i class = "fe fe-file-plus"></i>&nbsp;Add File (<span>{{ form.uploads.length }} Files Added</span>)</button>

						<document-row  v-for="(row, index) in form.uploads" :key="row.id" v-model="form.uploads[index]" @remove="removeRow($event, form.uploads)" v-if="form.uploads.length > 0"></document-row>
						<b-card v-if="form.uploads.length == 0" class="text-center">
							<h2>No Documents Added Yet</h2>
						</b-card>
					</div>
				</div>

				<div class="form-group">
					<label class = "control-label">Comments</label>
					<b-textarea rows="8" v-model="form.comment"></b-textarea>
				</div>

				<b-button @click="submitApplication" variant="primary">Submit Application</b-button>
			</div>
		</div>
		<!-- <div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card card-inactive">
						<div class="card-body text-center">
							<img src="/img/illustrations/scale.svg" alt="..." class="img-fluid" style="max-width: 182px;">
							<h1>
								Action Stopped
							</h1>
							<p class="text-muted">
								You are not allowed to view this page
							</p>
						</div>
					</div>

				</div>
			</div> 
		</div> -->
	</div>
</template>
<script type="text/javascript">
	import Form from '../../core/Form'
	import rowForm from '../../mixins/rowForm'
	import SearchClientComponent from '../../components/SearchClientComponent'
	import FileUploadRow from './components/FileUploadRow'

	export default {
		name: 'ApplicationForm',
		props: {
			id: { type: Number,  default: 0 },
		},
		components: {'search-client': SearchClientComponent, 'document-row': FileUploadRow},
		mixins: [rowForm],
		data(){
			return {
				showClientForm: true,
				processes: [],
				application: {
					data: {}
				},
				form: new Form({
					id: 0,
					client: "",
					process: "",
					uploads: [],
					comment: ""
				}),
			}
		},
		created(){
			this.getProcesses()
			if (this.id != 0) {
				this.getApplicationData(this.id)
			}
		},
		methods: {
			submitApplication: function(){
				// this.form.outputToConsole()
				if (!this.form.client || !this.form.process || this.form.uploads.length == 0) {
					this.$swal("Error", 'Please fill in all details before proceeding', "error")
				}else{
					if(this.id != 0){
						this.form.put('focal-points/applications/edit')
						.then((res) => {
							this.$swal("Success", 'Successfully updated application', "success")
							.then(() => {
								// this.$router.push({ name: 'applications.all' })
							})
						})
						.catch(error => {
							this.$swal("Error", "There was an error processing your request", "error")
						})
					}else{
						this.form.post('focal-points/applications/new')
						.then(res => {
							this.$swal("Success", 'Successfully submitted application', "success")
							.then(() => {
								this.$router.push({ name: 'applications.all' })
							})
						})
						.catch(error => {
							this.$swal("Error", "There was an error processing your request", "error")
						})
					}
					
				}
				
			},
			getProcesses: function(){
				axios.get('/api/data/processes/local')
				.then((res) => {
					this.processes = res.data
				});
			},
			getApplicationData(id){
				// this.$store.commit('loadingOn');
				axios.get(`/api/focal-points/applications/get/${id}`)
				.then((res) => {
					// this.$store.commit('loadingOff');
					this.showClientForm = false
					this.form.id = res.data.id
					this.form.client = res.data.applicant_details
					this.form.process = res.data.process
					this.form.comment = res.data.COMMENT
					this.form.uploads = _.map(res.data.files, (file, key) => {
						return {
							id: file.id,
							description: file.FILE_DESCRIPTION,
							edit: true
						}
					})
					this.application = res
				}).catch((error) => {
					// this.$store.commit('loadingOff');
					alert(error.message)
				})
			}
		}
	}
</script>
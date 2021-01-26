<template> 
	<div>
		<div class="card"> 
			<div class="card-body">
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
						<b>Type: </b>{{ data.data.client.type | capitalize }}<br/>
						<b>Name: </b>{{ data.data.client.name }}
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
	</div>
</template>

<script type="text/javascript">
	import Form from '../../core/Form'
	import rowForm from '../../mixins/rowForm'
	import SearchClientComponent from '../../components/SearchClientComponent'
	import FileUploadRow from './components/FileUploadRow'

	export default {
		components: {'search-client': SearchClientComponent, 'document-row': FileUploadRow},
		mixins: [rowForm],
		data(){
			return {
				showClientForm: true,
				processes: [],
				id: 0,
				form: new Form({
					client: "",
					process: "",
					uploads: [],
					comment: ""
				}),
			}
		},
		created(){
			this.getProcesses()
		},
		methods: {
			submitApplication: function(){
				// this.form.outputToConsole()
				this.form.post('focal-points/applications/new')
				.then(res => {
					alert('Successfully submitted application');
					this.$router.push({ name: 'applications.all' })
				})
				.catch(error => {
					alert("There was an error processing your request");
				})
			},
			getProcesses: function(){
				axios.get('/api/data/processes/local')
				.then((res) => {
					this.processes = res.data
				});
			}
		}
	}
</script>
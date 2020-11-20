<template> 
	<div>
		<div class="card"> 
			<div class="card-body">
				<div class="form-group">
					<label>Process</label>
					<v-select v-model="form.process" :options="processes" label="prj_name">
						<template slot="no-options">
							Type to search for a process
						</template>

						<template slot="option" slot-scope="option">
							{{ option.prj_name }}
						</template>
					</v-select>
				</div>

				 <div class="form-group">
				 	<label>Upload the required documents</label>
				 	<b-form-file v-model="form.uploads"></b-form-file>
				 </div>

				 <div class="form-group">
				 	<label>Comments</label>
				 	<b-textarea rows="8" v-model="form.comment"></b-textarea>
				 </div>

				 <b-button @click="submitApplication" variant="primary">Submit Application</b-button>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../core/Form'
	export default {
		data(){
			return {
				processes: [],
				form: new Form({
					process: "",
					uploads: [],
					comment: ""
				}),
			}
		},
		created(){
			// this.getProcesses()
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
				axios.get('/api/data/processes')
				.then((res) => {
					this.processes = res.data
				});
			}
		}
	}
</script>
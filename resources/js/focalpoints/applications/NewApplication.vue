<template> 
	<div>
		<div class="card"> 
			<div class="card-body">
				<div class="form-group">
					<label>Process</label>
					<v-select v-model="form.process"></v-select>
				</div>

				 <div class="form-group">
				 	<label>Upload the required documents</label>
				 	<b-form-file v-model="form.uploads" multiple></b-form-file>
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
			this.getProcesses()
		},
		methods: {
			submitApplication: function(){
				form.post('focal-points/applications/new')
				.then(res => {
					console.log(res)
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
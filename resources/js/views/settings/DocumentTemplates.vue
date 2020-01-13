<template>
	<div>
		<b-card>
			<div class="form-group row">
				<div class="col-md">
					<label>Process</label>
					<v-select v-model = "form.process" :options="processes" label="prj_name">
						<template slot="no-options">
							Type to search for a process
						</template>

						<template slot="option" slot-scope="option">
							{{ option.prj_name }}
						</template>
					</v-select>
				</div>

				<div class="col-md">
					<label>Task</label>
					<v-select v-model = "form.task" :options="processTasks" label="act_name"></v-select>
				</div>
			</div>

			<div class="form-group">
				<div class="row">
					<div class="col-md">
						<label>Step</label>
						<v-select v-model = "form.step" :options="taskSteps" label="obj_title"></v-select>
					</div>

					<div class="col-md">
						<label>Type</label>
						<b-input placeholder="Enter Type" label="Type" v-model="form.type"/>
					</div>
				</div>
				
			</div>
			<div class="form-group row">
				<div class="col-md">
					<b-input placeholder="Enter Input Document UID" label="Input Document UID" v-model = "form.input_document"></b-input>
				</div>
				<div class="col-md">
					<b-input placeholder="Enter Template name" label="Template Name" v-model = "form.name"></b-input>
				</div>
			</div>
			<div class="form-group">
				<label>Template</label>
				<b-form-file v-model="form.file"></b-form-file>
			</div>
			<b-button @click="uploadTemplateFile">Upload Template</b-button>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	import vSelect from 'vue-select'
	import Form from '../../core/Form'
	export default {
		components: { 'v-select': vSelect, Form },
		data(){
			return {
				processes: [],
				processTasks: [],
				taskSteps: [],
				form: new Form({
					name: "",
					process: "",
					task: "",
					step: "",
					type: "",
					input_document: "",
					file: ""
				})
			}
		},
		created(){
			this.getProcesses()
		},
		methods: {
			getProcesses(){
				axios.get('/api/data/processes')
				.then((res) => {
					this.processes = res.data
				});
			},

			getTasks(uid){
				axios.get(`/api/data/processes/${uid}/tasks`)
				.then((res) => {
					this.processTasks = res.data
				});
			},

			getSteps(process_uid, task_uid){
				axios.get(`/api/data/processes/${process_uid}/task/${task_uid}/steps`)
				.then((res) => {
					this.taskSteps = res.data
				})
			},
			uploadTemplateFile: function(){
				var em = this
				this.form.post('/template/add')
				.then((res) => {
					em.$toastr.success('Successfully added template')
				});
			}
		},
		watch: {
			'form.process': function(val) {
				var uid = val.prj_uid
				this.getTasks(uid)
				this.form.task = ""
			},
			'form.task': function(val){
				var task_uid = val.act_uid
				var project_uid = this.form.process.prj_uid
				this.getSteps(project_uid, task_uid)
			},
		}
	}
</script>
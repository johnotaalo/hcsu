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
				<b-input placeholder="Enter Input Document UID" label="Input Document UID" v-model = "form.input_document"></b-input>
			</div>
			<div class="form-group">
				<b-input placeholder="Enter Template name" label="Template Name" v-model = "form.name"></b-input>
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
				form: new Form({
					name: "",
					process: "",
					task: "",
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
			}
		}
	}
</script>
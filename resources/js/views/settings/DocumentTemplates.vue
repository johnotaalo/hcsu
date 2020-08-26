<template>
	<div>
		<b-card>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
                  <li class="breadcrumb-item"><b-link :to="{ name: 'settings' }">Settings</b-link></li>
                  <li class="breadcrumb-item"><b-link :to="{ name: 'settings-templates' }">Templates List</b-link></li>
                  <li class="breadcrumb-item active" aria-current="page">Add Template</li>
                </ol>
			</nav>
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
				<label>Adobe Sign Template</label>
				<v-select :options="adobeTemplates" v-model = "form.adobe_sign_template"></v-select>
			</div>
			<div class="form-group">
				<label>Template</label>
				<b-form-file v-model="form.file"></b-form-file>
				<div v-if="id != 0">
					<small><b-link :href="`/templates/file/${receivedData.id}`" target="_blank">Download previously uploaded file</b-link></small>
				</div>
			</div>
			<b-button @click="uploadTemplateFile" class = "btn btn-sm btn-primary"><i class="fe fe-upload-cloud"></i>&nbsp;Upload Template</b-button>
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
				adobeTemplates: [],
				taskSteps: [],
				id: 0,
				receivedData: {},
				waitTask: false,
				waitStep: false,
				waitTemplates: false,
				form: new Form({
					id: 0,
					name: "",
					process: "",
					task: "",
					step: "",
					type: "",
					input_document: "",
					file: "",
					adobe_sign_template: ""
				})
			}
		},
		created(){
			this.getProcesses()
			this.getAdobeSignTemplates()
			if (this.$route.params.id) {
				this.id = this.$route.params.id
			}
		},
		methods: {
			getTemplateData(id){
				axios.get(`/api/template/get/${id}`)
				.then((res) => {
					this.receivedData = res.data
					this.form.id = res.data.id
					this.form.name = res.data.form_name
					this.form.type = res.data.type
					this.form.input_document = res.data.input_document
					this.form.process = _.find(this.processes, ['prj_uid', res.data.process]);
					this.waitTask = true
					this.waitStep = true
					this.waitTemplates = true
				})
			},
			getProcesses(){
				axios.get('/api/data/processes')
				.then((res) => {
					this.processes = res.data
					if (this.id != 0) {
						this.getTemplateData(this.id)
					}
				});
			},

			getTasks(uid){
				axios.get(`/api/data/processes/${uid}/tasks`)
				.then((res) => {
					this.processTasks = res.data

					if (this.waitTask) {
						this.form.task = _.find(res.data, ['act_uid', this.receivedData.task])
						this.waitTask = false
					}
				});
			},

			getSteps(process_uid, task_uid){
				axios.get(`/api/data/processes/${process_uid}/task/${task_uid}/steps`)
				.then((res) => {
					this.taskSteps = res.data

					if (this.waitStep) {
						this.form.step = _.find(res.data, ['step_uid', this.receivedData.step])
						this.waitStep = false
					}
				})
			},
			uploadTemplateFile: function(){
				var em = this
				if(em.id == 0){
					this.form.post('/template/add')
					.then((res) => {
						this.$notify({
							group: 'foo',
							title: 'Success',
							text: 'Successfully added template',
							type: "success"
						});
						em.$router.push({ name: 'settings-templates' })
					})
					.catch((error) => {
						this.$notify({
							group: 'foo',
							title: 'Error',
							text: 'There was an error adding the template',
							type: "error"
						});
						console.log(error)
					});
				}else{
					this.form.put('/template/edit')
					.then((res) => {
						this.$notify({
							group: 'foo',
							title: 'Success',
							text: 'Successfully updated template',
							type: "success"
						});
						em.$router.push({ name: 'settings-templates' })
					})
					.catch((error) => {
						this.$notify({
							group: 'foo',
							title: 'Error',
							text: 'There was an error updating the template',
							type: "error"
						});
						console.log(error)
					});
				}
			},
			getAdobeSignTemplates: function(){
				axios.get("/api/documents/adobe-sign/library-documents")
				.then(res => {
					this.adobeTemplates = _.map(res.data.documents.libraryDocumentList, (o) => {
						return {
							label: o.name,
							value: o.libraryDocumentId
						}
					})

					if (this.waitTemplates) {
						this.form.adobe_sign_template = _.find(this.adobeTemplates, ['value', this.receivedData.ADOBE_SIGN_TEMPLATE])
					}
				});
			}
		},
		watch: {
			'form.process': function(val) {
				var uid = val.prj_uid
				this.getTasks(uid)
				if (this.id != 0) {
					this.form.task = ""
				}
				
			},
			'form.task': function(val){
				var task_uid = val.act_uid
				var project_uid = this.form.process.prj_uid
				this.getSteps(project_uid, task_uid)
			},
		}
	}
</script>
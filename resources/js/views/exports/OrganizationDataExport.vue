<template>
	<div>
		<loading
		:active.sync="loading"
        :can-cancel="false"
        :is-full-page="true"></loading>
		<b-card title="Organization Data Export">
			<h4>Filters</h4>
			<div class="row">
				<div class="col-md">
					<b-form-group label="Processes" description="Leave empty if you want all processes">
						<v-select v-model="form.processes" multiple :options="processes"></v-select>
					</b-form-group>
				</div>

				<div class="col-md">
					<b-form-group label="Organization(s)" description="Leave empty if you want all agencies">
						<v-select v-model="form.agencies" multiple :options="agencies"></v-select>
					</b-form-group>
				</div>

				<div class="col-md">
					<b-form-group label="Status" description="Leave empty if you want all statuses">
						<v-select v-model="form.status" multiple :options="status"></v-select>
					</b-form-group>
				</div>
			</div>
			<div class="row">
				<div class="col-md">
					<b-form-group label="Years">
						<b-form-checkbox-group
						id="years"
						v-model = "form.years"
						:options = "years"
						name="years"></b-form-checkbox-group>
					</b-form-group>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<b-button size="sm" variant="primary" @click="downloadList" :disabled="loading"><i class="fe fe-download"></i> Download List</b-button>
					<b-button size="sm" variant="danger" @click="clearFilters"><i class="fe fe-x"></i> Clear Filters</b-button>
				</div>
			</div>
		</b-card>
	</div> 
</template>

<script type="text/javascript">
	import Form from '../../core/Form'
	export default{
		data(){
			return {
				processes: [],
				agencies: [],
				oldestdate: "",
				years: [],
				status: [{
					id: "TO_DO",
					label: "TO_DO"
				},{
					id: "COMPLETED",
					label: "COMPLETED"
				},{
					id: "DRAFT",
					label: "DRAFT"
				},{
					id: "CANCELLED",
					label: "CANCELLED"
				}],
				loading: false,
				form: {
					processes: [],
					agencies: [],
					status: [],
					years: []
				}
			}
		},
		mounted() {
			this.loading = true
			var em = this
			axios.all([this.getProcesses(), this.getAgencies(), this.getStartDatesPM()])
			.then(axios.spread(function(processes, agencies, date){
				em.loading = false
			}))
		},
		methods: {
			downloadList(){
				this.loading = true
				this.$notify({
					group: 'foo',
					title: 'Download started',
					text: "File download has started",
					type: "information"
				});
				const url = "api/documents/export/processes/organization"
				axios({
					method: "POST",
					url: url, 
					data: this.form,
					responseType: 'blob'
				})
				.then(res => {
					this.loading = false
					var blobData = new Blob([res.data]);
					console.log(blobData.size)

					var fileURL = window.URL.createObjectURL(blobData)
					var fileLink = document.createElement('a')

					fileLink.href = fileURL
					var organizations = _.map(this.form.agencies, (agency) => {
						return agency.id
					})
					var organization_list = (organizations.length > 0) ? organizations.join() : "All"
					fileLink.setAttribute('download', `Organization Data (${organization_list}).xlsx`);
					document.body.appendChild(fileLink)

					fileLink.click()

					this.$notify({
						group: 'foo',
						title: 'Success',
						text: "Successfully downloaded file",
						type: "success"
					});
				})
				.catch(error => {
					this.loading = false
					this.$notify({
						group: 'foo',
						title: 'Error',
						text: "There was an error downloading the file",
						type: "error"
					});
				});
			},

			getProcesses(){
				var request = axios.get('/api/data/processes')
				.then(res => {
					this.processes = _.map(res.data, (process) => {
						return {
							id: process.prj_uid,
							label: process.prj_name
						}
					})
				});

				return request;
			},
			getAgencies(){
				var request = axios.get('/api/agencies')
				.then(res => {
					this.agencies = _.map(res.data, (agency) => {
						return {
							id: agency.ACRONYM,
							label: agency.ACRONYM
						}
					})
				});

				return request;
			},
			getStartDatesPM(){
				var request = axios.get('/api/data/applications/oldestdate')
				.then(res => {
					this.oldestdate = res.data

					if (this.oldestdate) {
						var oldestYear = this.$moment(this.oldestdate).year()
						var currentYear = this.$moment().year()
						for (var i = oldestYear; i <= currentYear; i++) {
							this.years.push(i)
						}
						this.form.years = this.years
					}
				});

				return request;
			},
			clearFilters(){
				this.form.processes = []
				this.form.agencies = []
				this.form.status = []
				this.form.years = []
			}
		},
		computed:{
			yearsToSelect(){
				var years = []
				if (this.oldestdate) {
					var oldestYear = this.$moment(this.oldestdate).year()
					var currentYear = this.$moment().year()
					for (var i = oldestYear; i <= currentYear; i++) {
						years.push(i)
					}
				}
				
				return years;
			}
		}
	}
</script>
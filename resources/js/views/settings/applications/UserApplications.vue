<template>
	<div>
		<div class="row mt-4">
				<div class="col-12 col-lg-6 col-xl">
					<div class="card">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col">
									<h6 class="text-uppercase text-primary mb-2">
										All Applications
									</h6>
									<span class="h2 mb-0">
										{{ cleanedData.counts.applications.all }}
									</span>
								</div>
								<div class="col-auto">
									<span class="h2 fe fe-file-text text-primary mb-0"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6 col-xl">
					<div class="card">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col">
									<h6 class="text-uppercase text-success mb-2">
										Assigned
									</h6>
									<span class="h2 mb-0">
										{{ cleanedData.statuses.ASSIGNED.length }}
									</span>
								</div>
								<div class="col-auto">
									<span class="h2 fe fe-check text-success mb-0"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6 col-xl">
					<div class="card">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col">
									<h6 class="text-uppercase text-warning mb-2">
										Queried
									</h6>
									<span class="h2 mb-0">
										{{ cleanedData.statuses.QUERIED.length }}
									</span>
								</div>
								<div class="col-auto">
									<span class="h2 fe fe-corner-down-left text-warning mb-0"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6 col-xl">
					<div class="card">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col">
									<h6 class="text-uppercase text-danger mb-2">
										Cancelled
									</h6>
									<span class="h2 mb-0">
										{{ cleanedData.statuses.CANCELED.length }}
									</span>
								</div>
								<div class="col-auto">
									<span class="h2 fe fe-x-circle text-danger mb-0"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<div class="card">
			<div class="card-header">
				<div class="row align-items-center">
					<div class="col">
					</div>
					<div class="col-auto">
						<b-select :options="statusOptions" v-model="status" v-on:change="applyStatusFilter(status)"></b-select>
					</div>
				</div>
			</div>
			<!-- <div class="card-body"> -->
				<v-server-table
				ref="principalTable"
				class="table-sm table-nowrap card-table"
				:columns="columns"
				:options="options"
				size="sm">

				<template slot="CLIENT" slot-scope="data">
					<span v-if="data.row.applicant_type == 'staff'">{{ data.row.applicant_details.LAST_NAME }}, {{ data.row.applicant_details.OTHER_NAMES }} ({{ data.row.applicant_details.latest_contract.ACRONYM }})</span>
					<span v-if="data.row.applicant_type == 'agency'">{{ data.row.applicant_details.ACRONYM }}</span>
					<span v-if="data.row.applicant_type == 'dependent'">{{ data.row.applicant_details.LAST_NAME }}, {{ data.row.applicant_details.OTHER_NAMES }}<br/><small><i>Dependent of {{ data.row.applicant_details.principal.principal_name }} ({{ data.row.applicant_details.principal.latest_contract.ACRONYM }})</i></small></span>
				</template>

				<template slot="CASE_NO" slot-scope="data">
					<span v-if="data.row.APP_NUMBER"> {{ data.row.APP_NUMBER }}</span>
					<span v-else>
						N/A
					</span>
				</template>

				<template slot="PROCESS_NAME" slot-scope="data">
					{{ data.row.process.PRO_TITLE }}
				</template>

				<template slot="CURRENT_USER" slot-scope="data">
					<span v-if="data.row.CURRENT_USER == 'ADMINISTRATIVE_ASSISTANT'">
						{{ data.row.assigned.USR_LASTNAME }}, {{ data.row.assigned.USR_FIRSTNAME }}
					</span>
					<span v-else-if="data.row.CURRENT_USER == 'SELF'">
						CLIENT
					</span>
					<span v-else>{{ data.row.CURRENT_USER }}</span>
				</template>
				<template slot="CREATED_AT" slot-scope="data">
					{{ data.row.created_at }}
				</template>

				<template slot="ACTIONS" slot-scope="data">
					<b-dropdown text="Actions" variant="info" size="sm">
						<b-dropdown-item v-if="data.row.CURRENT_USER == 'SUPERVISOR' && data.row.STATUS == 'SUBMITTED'" :to="{ name: 'user-applications.assign', params: { id: data.row.id } }">Assign Case</b-dropdown-item>
						<b-dropdown-item @click="viewuploads(data.row.id)">View Uploads</b-dropdown-item>
					</b-dropdown>
					<!-- <b-button  :to="{ name: 'user-applications.assign', params: { id: data.row.id } }" variant="sm" class = "btn btn-white">Assign Case</b-button> -->
				</template>
			</v-server-table>
			<!-- </div> -->
		</div>
	</div>
</template>

<script type="text/javascript">
	import VueTables from 'vue-tables-2'
	const Event = VueTables.Event
	export default{
		data(){
			return {
				searchTerm: "",
				status: "SUBMITTED",
				appData: [],
				statuses: [
					{
						name: 'ASSIGNED',
						color: "#1b5e20"
					},
					{
						name: 'CANCELED',
						color: "#B71C1C"
					}, 
					{
						name: 'SUBMITTED',
						color: "#0d47a1"
					},
					{
						name: 'QUERIED',
						color: "#ff6f00"
					}
				],
				statusOptions: [
					{ value: null, text: 'All Statuses' },
					{ value: 'SUBMITTED', text: 'SUBMITTED' },
					{ value: 'QUERIED', text: 'QUERIED' },
					{ value: 'ASSIGNED', text: 'ASSIGNED' },
					{ value: 'CANCELED', text: 'CANCELED' },
				],
				columns: ['CASE_NO', 'CLIENT', 'PROCESS_NAME', 'STATUS', 'CURRENT_USER', 'CREATED_AT', 'ACTIONS'],
				options: {
					perPage: 50,
					perPageValues: [],
					filterable: false,
					customFilters: ['normalSerch', 'statusSearch'],
					sortable: ["CASE_NO", "PROCESS_NAME", "CREATED"],
					sortIcon: {
						base: 'fe',
						up: 'fe-arrow-up',
						down: 'fe-arrow-down',
						is: 'fe-minus'
					},
					orderBy: {
						column: "CREATED",
						ascending: true
					},
					requestFunction: (data) => {
						return axios.get(`/api/focal-points/applications`, {
							params: data
						})
						.catch(function (e) {
							console.log('error', e);
						}.bind(this));
					}
				}
			}
		},
		created(){
			this.getData()
		},
		mounted(){
			this.$parent.isContainer = false
			this.applyStatusFilter(this.status)
		},
		methods: {
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::normalSearch', term);
			},

			applyStatusFilter: function(status){
				Event.$emit('vue-tables.filter::statusSearch', status);
			},

			assignCase: function(id){
				axios.get('/api/focal-points/applications/assign/' + id)
				.then(res => {
					alert('Successfully assigned to AA');
					location.reload()
				});
			},

			viewuploads: function(id){
				window.location = "/uploads/" + id
			},

			getData(){
				axios.get('/api/focal-points/applications/all')
				.then(res => {
					this.appData = res.data
				})
				.catch(error => {
					console.log(error)
				});
			}
		},
		computed: {
			cleanedData(){
				var data = {};

				data['counts'] = {}
				data['counts']['applications'] = {}

				data['counts']['applications']['all'] = this.appData.length

				var statusData = {};
				var monthlyCount = {};
				data['statusColor'] = {};
				_.each(this.statuses, (status) => {
					statusData[status.name] = []
					data['statusColor'][status.name] = status.color
				})

				var year = this.$moment().format("YYYY");
				var previousMonths = [];

				for (var i = 11; i >= 0; i--) {
					var currDate = this.$moment().subtract(i, 'months')
					previousMonths.push(currDate.format("MMM YYYY"))
				}

				data['months'] = previousMonths
				data['counts']['applications']['monthly'] = {}

				var monthlyInfo = {};

				_.each(this.appData, (d) => {
					statusData[d.STATUS].push(d.id)
					var month = this.$moment(d.created_at).format("MMM YYYY");
					if (!(month in monthlyInfo)) {
						monthlyInfo[month] = [];
					}

					monthlyInfo[month].push(d)
				})

				_.each(previousMonths, (month) => {
					if (!(month in monthlyInfo)) {
						monthlyInfo[month] = [];
					}else{
						monthlyInfo[month] = monthlyInfo[month]
					}
				})

				data['counts']['applications']['monthly'] = monthlyInfo
				data['statuses'] = statusData

				return data;
			}
		}

	}
</script>
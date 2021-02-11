<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="row align-items-center">
					<div class="col">
						<form class="row align-items-center">
							<div class="col-auto pr-0">
								<span class="fe fe-search text-muted"></span>
							</div>

							<div class="col">
								<b-input type="search" class="form-control form-control-flush search" v-model = "searchTerm" placeholder="Search" v-on:keyup="applySearchFilter(searchTerm)"/>
							</div>

							<div class="col-auto">
								<b-select size="sm" v-model="processSearch" :options="processes" @change="applyProcessFilter(processSearch)"></b-select>
							</div>

							<div class="col-auto">
								<b-select size="sm" v-model="statusSearch" :options="statusOptions" @change="applyStatusFilter(statusSearch)"></b-select>
							</div>
							<!-- <div class="col-auto">
								<b-button class="btn btn-sm btn-white" v-b-modal.filter-modal><i class="fe fe-filter"></i>&nbsp;Filter</b-button>
							</div> -->
							<div class="col-auto">
								<b-button class="btn-sm btn-white" :to="{ name: 'applications.new' }"><i class="fe fe-plus-circle"></i>&nbsp;Start a New Application</b-button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<v-server-table
			ref="applicationsTable"
			class="table-sm table-nowrap card-table"
			:columns="columns"
			:options="options"
			size="sm">
			<!-- <template slot="#" slot-scope="data">
				{{ data.index }}
			</template> -->
			<template slot="CLIENT" slot-scope="data">
				<span v-if="data.row.applicant_type == 'staff'">{{ data.row.applicant_details.LAST_NAME }}, {{ data.row.applicant_details.OTHER_NAMES }}</span>
				<span v-if="data.row.applicant_type == 'agency'">{{ data.row.applicant_details.ACRONYM }}</span>
				<span v-if="data.row.applicant_type == 'dependent'">{{ data.row.applicant_details.LAST_NAME }}, {{ data.row.applicant_details.OTHER_NAMES }}<br/><small>Dependent</small></span>
			</template>

			<template slot="CASE_NO" slot-scope="data">
				<span v-if="data.row.APP_NUMBER"> {{ data.row.APP_NUMBER }}</span>
				<span v-else>
					N/A
				</span>
			</template>

			<template slot="ASSIGNED_TO" slot-scope="data">
				<span v-if="data.row.CURRENT_USER == 'ADMINISTRATIVE_ASSISTANT'">
					{{ data.row.assigned.USR_LASTNAME }}, {{ data.row.assigned.USR_FIRSTNAME }}
				</span>
				<span v-else>
					{{ data.row.CURRENT_USER }}
				</span>
			</template>

			<template slot="PROCESS_NAME" slot-scope="data">
				{{ data.row.process.PRO_TITLE }}
			</template>
			<template slot="CREATED" slot-scope="data">
				{{ data.row.created_at | moment("from") }}
			</template>

			<template slot="ACTIONS" slot-scope="data">
				<div class="text-right">
					<div class="dropdown">
						<a href="#" class="dropdown-ellipses dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fe fe-more-vertical"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right" style="">
							<router-link :to="{ name: 'applications.edit', params: {id: data.row.id} }" class="dropdown-item" v-if="data.row.STATUS == 'QUERIED'">
								<i class="fe fe-edit-3"></i>&nbsp;Edit Application
							</router-link>
							<router-link :to="{ name: 'applications.view', params: {id: data.row.id} }" class="dropdown-item">
								<i class="fe fe-eye"></i>&nbsp;View Application
							</router-link>
							<a @click="cancelApplication(data.row.id)" class="dropdown-item" v-if="data.row.STATUS == 'SUBMITTED'">
								<i class="fe fe-x"></i>&nbsp;Cancel Application
							</a>
							<router-link :to="{ name: '', params: {} }" class="dropdown-item" v-if="data.row.STATUS == 'ASSIGNED'">
								<i class="fe fe-navigation-2"></i>&nbsp;Track Application
							</router-link>
						</div>
					</div>
				</div>
			</template>
			</v-server-table>
		</div>
		<b-modal id="filter-modal" title="Filter">
			Hello From My Modal!
		</b-modal>
	</div>
</template>

<script type="text/javascript">
	import VueTables from 'vue-tables-2'
	const Event = VueTables.Event
	export default{
		data(){
			return {
				searchTerm: "",
				statusSearch: null,
				processSearch: null,
				statusOptions: [
					{ value: null, text: 'All Statuses' },
					{ value: 'SUBMITTED', text: 'SUBMITTED' },
					{ value: 'QUERIED', text: 'QUERIED' },
					{ value: 'ASSIGNED', text: 'ASSIGNED' },
					{ value: 'CANCELED', text: 'CANCELED' },
				],
				processes: [],
				columns: ['CASE_NO', 'CLIENT', 'PROCESS_NAME', 'STATUS', 'ASSIGNED_TO', 'CREATED', 'ACTIONS'],
				options: {
					perPage: 50,
					perPageValues: [],
					filterable: false,
					customFilters: ['normalSearch', 'statusSearch', 'processSearch'],
					sortable: ["CASE_NO", "PROCESS_NAME", "CREATED"],
					sortIcon: {
						base: 'fe',
						up: 'fe-arrow-up',
						down: 'fe-arrow-down',
						is: 'fe-minus'
					},
					orderBy: {
						column: "CREATED",
						ascending: false
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
			this.getProcesses()
		},
		methods: {
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::normalSearch', term);
			},
			applyStatusFilter: function(term){
				Event.$emit('vue-tables.filter::statusSearch', term);
			},
			applyProcessFilter: function(term){
				Event.$emit('vue-tables.filter::processSearch', term);
			},
			getProcesses: function(){
				axios.get('/api/data/processes/local')
				.then((res) => {
					this.processes = _.map(res.data, (process) => {
						return {
							value: process.PRO_UID,
							text: process.PRO_TITLE
						}
					})

					this.processes.unshift({ value: null, text: 'All processes' })
				});
			},
			cancelApplication: function(id){
				this.$swal({
					title: "Do you want to cancel this case?",
					text: "Once cancelled, you will not be able to edit the case!",
					icon: "warning",
					buttons: ["Do not Cancel", "Proceed"],
					dangerMode: true,
				})
				.then((result) => {
					if (result) {
						axios.get(`/api/focal-points/applications/cancel/${id}`)
						.then(res => {
							this.$swal("Successfully cancelled case.", "", "success")
							this.$refs.applicationsTable.refresh()
						});
					} else if (result) {
				
					}
				})
			}
		}
	}
</script>
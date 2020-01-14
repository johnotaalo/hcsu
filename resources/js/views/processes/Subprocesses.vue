<template>
	<div>
		<b-card>
			<div class="row align-items-center">
				<div class="col">
					<h3>Filters</h3>
				</div>
				<div class="col-auto">
					<b-button @click="visibleFilter = !visibleFilter" class= "float-right" variant="outline-primary" size ="sm"><i :class = "visibleFilter ? 'fe fe-minus' : 'fe fe-plus'"></i></b-button>
				</div>
			</div>
			<b-collapse v-model="visibleFilter" id="filter-collapse">
				<div class="row">
					<div class="col-md">
						<b-form-group label="Process">
							<v-select v-model="filter.process"  :options="filterData.processes">
								<template slot="no-options">
									Type to search for a process
								</template>
							</v-select>
						</b-form-group>
					</div>
					<div class="col-md">
						<b-form-group label="Creator">
							<v-select v-model="filter.creator" :options="filterData.users">
							</v-select>
						</b-form-group>
					</div>
				</div>
				<b-button variant="primary" size="sm" @click="applySearchFilter"><span class="fe fe-search"></span> Filter Data</b-button>
				<b-button variant="danger" size="sm" @click="clearFilters"><span class="fe fe-x"></span> Clear Filters</b-button>
			</b-collapse>
		</b-card>
		<b-card title="IPMIS Subprocesses">
			<v-server-table
			url="/api/data/processes/ipmis"
			:options="options"
			:columns="columns"
			></v-server-table>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	import VueTables from 'vue-tables-2'
	const Event = VueTables.Event

	export default {
		data(){
			return {
				selectedFilter: "",
				visibleFilter: true,
				filterOptions: [
					{ text: 'Process', value: 'process' },
					{ text: 'Creator', value: 'creator' }
				],
				options: {
					filterable: false,
					perPageValues: [],
					customFilters: ['filterSearch']
				},
				columns: ['parent_case', 'subprocess_case', 'case_title', 'process', 'creator'],
				filter: {
					process: "",
					creator: ""
				},
				filterData: {
					users: [],
					processes: []
				}
			}
		},
		mounted(){
			this.getUsers()
			this.getProcesses()
		},
		methods: {
			getUsers(){
				axios.get('/api/data/users')
				.then(res => {
					this.filterData.users = _.map(res.data, (user) => {
						return {
							value: user.USR_UID,
							label: `${user.USR_LASTNAME}, ${user.USR_FIRSTNAME} (${user.USR_USERNAME})`
						}
					})
				});
			},
			getProcesses(){
				axios.get('/api/data/processes')
				.then((res) => {
					this.filterData.processes = _.map(res.data, (process) => {
						return {
							label: process.prj_name,
							uid: process.prj_uid
						}
					})
				});
			},
			clearFilters(){
				this.filter.process = ""
				this.filter.creator = ""

				this.applySearchFilter()
			},
			applySearchFilter: function(){
				Event.$emit('vue-tables.filter::filterSearch', this.filter);
			}
		},
		watch: {
			'filter.type': function(newval, oldval){
				this.filter.value = ""
			}
		}
	}
</script>
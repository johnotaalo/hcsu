<template>
	<div>
		<div class="header">
			<div class="header-body">
				<div class="row align-items-center">
					<div class="col">
						<!-- Pretitle -->
						<h6 class="header-pretitle">
							Vehicle Plates
						</h6>
						<!-- Title -->
						<h1 class="header-title">
							Plates List
						</h1>
					</div>
				</div> <!-- / .row -->
			</div>
		</div>

		<div class="row">
			<div class="col-md">
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
								</form>

							</div>
							<div class="col-auto">
								<b-button class = "btn-white" size="sm"><i class="fe fe-filter"></i>&nbsp;Advanced Filters</b-button>
							</div>
							<div class="col-auto">
								<b-button class= "btn-white" :to="{ name: 'vehicle-plates-orders' }" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Order Plates</b-button>
							</div>
						</div>
					</div>

					<v-server-table class="table-sm card-table" :columns="table.columns" :options="table.options" size="sm">
						<template slot="#" slot-scope="data">
							{{ data.index }}
						</template>
						<template slot="Plate Number" slot-scope="data">
							{{ data.row.prefix.prefix }}{{ data.row.plate_number }}
						</template>

						<template slot="Allocated To" slot-scope="data">
							<span v-if="data.row.client != null && data.row.client.principal != null">{{ data.row.client.principal.LAST_NAME }} {{ data.row.client.principal.OTHER_NAMES }} - {{ data.row.client.principal.latest_contract.ACRONYM }}</span>
							<span v-else-if="data.row.client != null && data.row.client.dependent != null">{{ data.row.client.dependent.LAST_NAME }} {{ data.row.client.dependent.OTHER_NAMES }} <br/><span class="text-muted">{{ data.row.client.dependent.relationship.RELATIONSHIP }} of {{ data.row.client.dependent.principal.fullname }} - {{ data.row.client.dependent.principal.latest_contract.ACRONYM }}</span></span>
							<span v-else-if="data.row.client != null && data.row.client.agency != null">{{ data.row.client.agency.ACRONYM }}</span>
							<span v-else>
								<b-link>Assign to Client</b-link>
							</span>
						</template>

						<template slot="Status" slot-scope="data">
							<span v-if="data.row.status == 0">
								<span class="text-success">●</span> Available
							</span>
							<span v-else-if="data.row.status == 1">
								<span class="text-warning">●</span> Pending Issuance
							</span>
							<span v-else-if="data.row.status == 2">
								<span class="text-info">●</span> Issued
							</span>

							<span v-else-if="data.row.status == 3">
								<span class="text-info">●</span> Lost/Stolen
							</span>
						</template>

						<template slot="Actions">
							<b-button variant = "sm" class = "btn-white">Change Status</b-button>
						</template>
					</v-server-table>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	import VueTables from 'vue-tables-2'
	const Event = VueTables.Event
	export default {
		data(){
			return {
				searchTerm: "",
				table: {
					columns: ["#", "Plate Number", "Status", "Allocated To", "Actions"],
					options: {
						perPage: 50,
						perPageValues: [],
						filterable: false,
						customFilters: ["normalSearch"],
						requestFunction: (data) => {
							return axios.get(`/api/vehicle/plates/list`, {
								params: data
							})
							.catch(function (e) {
								console.log('error', e);
							}.bind(this));
						},
						columnsDropdown: false,
						sortIcon: {
							base: 'fe',
							up: 'fe-arrow-up',
							down: 'fe-arrow-down',
							is: 'icon-sort'
						},
						pagination: {
							nav: "fixed",
							dropdown: false,
							edge: true,
						},
					}
				}
			}
		},
		methods: {
			applySearchFilter: function(searchTerm){
				Event.$emit('vue-tables.filter::normalSearch', searchTerm);
			}
		}
	}
</script>
<template>
	<div>
		<div class="header">
			<div class="header-body">
				<div class="row align-items-center">
					<div class="col">
						<!-- Pretitle -->
						<h6 class="header-pretitle">
							Tims Registration
						</h6>
						<!-- Title -->
						<h1 class="header-title">
							TIMS Registration List
						</h1>
					</div>
				</div> <!-- / .row -->
			</div>
		</div>

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
								<label class="float-right" for="agencies">Filter by Agency:</label>
							</div>
							<div class="col-auto">
								<b-select @change="applyAgencyFilter(agencyFilter)" v-model="agencyFilter" :options="agencies" id="agencies"></b-select>
							</div>
						</form>
					</div>
					<!-- <div class="col-auto">
						<label>Filter By Agency:</label>
					</div>
					<div class="col-auto">
						<b-form-select @change="applyActiveStaffFilter(activeStaff)"
						v-model="activeStaff" :options="staffOptions"></b-form-select>
					</div> -->
				</div>
			</div>
			<v-server-table
				class="table-sm table-nowrap card-table"
				:columns="serverColumns"
				:options="options"
				size="sm">
				<template slot="DIPLOMATIC ID" slot-scope="data">
					{{ data.row.DIP_ID_NO }}
				</template>

				<template slot="KRA_PIN_NO" slot-scope="data">
					{{ data.row.KRA_PIN_NO }}
				</template>

				<template slot="AGENCY" slot-scope="data">
					{{ data.row.ACRONYM }}
				</template>
			</v-server-table>
		</div>
	</div>
</template>

<script type="text/javascript">
	import VueTables from 'vue-tables-2'
	const Event = VueTables.Event
	export default{
		data(){
			return {
				serverColumns: ["HOST_COUNTRY_ID", "CLIENT", "AGENCY", "DIPLOMATIC ID", "USERNAME", "KRA_PIN_NO", "REGISTRATION_DATE"],
				agencies: [],
				searchTerm: "",
				agencyFilter: "",
				options: {
					perPage: 50,
					perPageValues: [],
					filterable: false,
					customFilters: ['normalSearch', 'agencySearch'],
					sortable: ["HOST_COUNTRY_ID", "CLIENT", "AGENCY", "DIPLOMATIC ID", "USERNAME", "KRA_PIN_NO", "REGISTRATION_DATE"],
					sortIcon: {
						base: 'fe',
						up: 'fe-arrow-up',
						down: 'fe-arrow-down',
						is: 'fe-minus'
					},
					requestFunction: (data) => {
						return axios.get(`/api/data/tims/list`, {
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
			this.getAgencies()
		},
		methods: {
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::normalSearch', term);
			},
			applyAgencyFilter: function(term){
				Event.$emit('vue-tables.filter::agencySearch', term);
			},
			getAgencies(){
				axios.get('/api/agencies')
				.then(res => {
					var data = _.map(res.data, (agency) => {
						return {
							value: agency.ACRONYM,
							text: agency.ACRONYM
						}
					});

					data.unshift({ value: "", text: "All Agencies" });

					this.agencies = data
				});
			}
		}
	}
</script>
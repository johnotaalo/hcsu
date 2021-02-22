<template>
	<div>
		<b-button-group></b-button-group>
		<div id="table">
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
							<router-link class = "btn btn-sm btn-primary float-right" size="sm" :to="{ name: 'agencies.add' }"><i class="fe fe-plus-circle"></i>&nbsp;Add Agency</router-link>
						</div>
						<!-- <div class="col-auto">
							<b-select :options="statusOptions" v-model="status" v-on:change="applyStatusFilter(status)"></b-select>
						</div> -->
					</div>
				</div>
				<v-server-table
					ref="agenciesTable"
					class="table-sm table-nowrap card-table"
					:columns="columns"
					:options="options"
					size="sm">
					<template slot="IMAGE" slot-scope="data">
						<center>
							<b-img v-if="data.row.logo_link" width="40" height="40" rounded="circle" alt="Circle image" :src="`/agency/logo/${data.row.AGENCY_ID}`" />
							<b-img v-else width="40" height="40" rounded="circle" alt="Circle image" src="/images/unlogo.jpg" />
						</center>
					</template>
					<template slot="STATUS" slot-scope="data">
						<span v-if="data.row.IS_ACTIVE == 1"><span class="text-success">●</span> Active</span>
						<span v-if="data.row.IS_ACTIVE == 0"><span class="text-danger">●</span> Inactive</span>
					</template>

					<template slot="ACTIONS" slot-scope="data">
						<b-dropdown text="Actions" variant="info" size="sm">
							<b-dropdown-item :to="{ name: 'agencies.edit', params: { id: data.row.HOST_COUNTRY_ID } }">Edit</b-dropdown-item>
							<b-dropdown-item :to="{ name: 'agencies.view', params: { id: data.row.HOST_COUNTRY_ID } }">View</b-dropdown-item>
							<b-dropdown-item :to="{ name: 'agencies.view', params: { id: data.row.HOST_COUNTRY_ID } }">Report</b-dropdown-item>
						</b-dropdown>
						<!-- <b-button  :to="{ name: 'user-applications.assign', params: { id: data.row.id } }" variant="sm" class = "btn btn-white">Assign Case</b-button> -->
					</template>
				</v-server-table>
			</div>
		</div>
		<div id = grid v-if="grids">
			<div class="row mb-2">
				<div class="col-md-10">
					<b-form-input v-model="search" type="text" class="float-left form-control-rounded" placeholder = "Search for Agency" size="sm" />
				</div>
				<div class="col-md-2">
					<router-link class = "btn btn-sm btn-primary float-right" size="sm" :to="{ name: 'agencies.add' }"><i class="fa fa-plus"></i>Add Agency</router-link>
				</div>
			</div>

			<div class="row">
				<div class="col-md">
					<div>
						<center><b-spinner type="grow" label="Spinning" v-if="loading" /></center>
						
						<div v-if="totalRows && !loading" class="row" id="agencyList" :per-page="pagination.perPage">
							<!-- <paginate name="agencies" :list="agencies" :per="pagination.perPage"> -->
								<div class="col-md-4 mb-3" v-for="agency in filteredAgencies">
									<b-card style="height: 280px;">
										<div class="row" style="height: 40%;">
											<div class="col-sm-2" style="padding: 6px;" >
												<b-img v-bind="mainProps" src="/img/unep.png" center align-middle rounded alt="Rounded image" class="w-100"/>
											</div>
											<div class="col-sm-10">
												<p style="margin-bottom: 2px;">{{ agency.ACRONYM }}</p>
												<small style="line-height: 1 !important;">{{ agency.AGENCYNAME }}</small>
											</div>
										</div>
										<hr>
										<div class="row">
											<div class="col-md">
												<center class="text-muted">
													<p class="mb-0"><font-awesome-icon icon="user-friends" /></p>
													<small>3,000</small>
												</center>
											</div>
											<div class="col-md">
												<center class = "text-muted">
													<p class="mb-0"><font-awesome-icon icon="cogs" /></p>
													<small>3,000</small>
												</center>
											</div>

											<div class="col-md">
												<center class = "text-muted">
													<p class="mb-0"><font-awesome-icon icon="money-bill" /></p>
													<small>3,000</small>
												</center>
											</div>
										</div>
										<hr>
										<div class="row">
											<div class="col-md">
												<!-- <b-button block variant="primary" size="sm">
													
												</b-button> -->

												<router-link class="btn btn-primary btn-block btn-sm" :to="{ name: 'agencies.edit', params: { id: agency.HOST_COUNTRY_ID }} "><font-awesome-icon icon="pen"/>Edit</router-link>
											</div>
											<div class="col-md">
												<b-button block variant="success" size="sm" @click="viewAgency(agency.HOST_COUNTRY_ID)">
													<font-awesome-icon icon="eye"/>
													View
												</b-button>
											</div>
											<div class="col-md">
												<b-button block variant="warning" size="sm">
													<font-awesome-icon icon="eye"/>
													Report
												</b-button>
											</div>
										</div>
									</b-card>
								</div>
							<!-- </paginate> -->
						</div>

						<div v-else-if="!totalRows && !loading">
							<center>
								<h3>Sorry. There are no agencies to display</h3>
							</center>
						</div>
					</div>
					

					<!-- <b-pagination v-model="pagination.currentPage" :total-rows="totalRows" :per-page= "pagination.perPage" aria-controls="agencyList"></b-pagination> -->
				</div>
			</div>
		</div>
	</div>
</template>
<style type="text/css">
	small{
		line-height: 1;
	}
</style>
<script type="text/javascript">
	import VueTables from 'vue-tables-2'
	const Event = VueTables.Event
	export default {
		data() {
			return {
				searchTerm: "",
				grids: false,
				loading: true,
				search: "",
				pagination: {
					currentPage: 1,
					perPage: 10
				},
				paginate: ['agencies'],
				agencies: [],
				mainProps: { blank: false, blankColor: '#777'},
				columns: ["IMAGE", "HOST_COUNTRY_ID", "ACRONYM", "AGENCYNAME", "STATUS", "ACTIONS"],
				options: {
					perPage: 10,
					perPageValues: [],
					filterable: false,
					customFilters: ['normalSerch'],
					sortable: ["HOST_COUNTRY_ID", "AGENCYNAME", "ACRONYM"],
					sortIcon: {
						base: 'fe',
						up: 'fe-arrow-up',
						down: 'fe-arrow-down',
						is: 'fe-minus'
					},
					orderBy: {
						column: "HOST_COUNTRY_ID",
						ascending: true
					},
					requestFunction: (data) => {
						return axios.get(`/api/agencies/all`, {
							params: data
						})
						.catch(function (e) {
							console.log('error', e);
						}.bind(this));
					}
				}
			}
		},
		mounted(){
			// axios.get('/api/agencies')
			// .then((res) => {
			// 	this.agencies = res.data;
			// 	this.loading = false;
			// });
			this.$parent.isContainer = false
		},

		computed: {
			filteredAgencies() {
				return this.agencies.filter(agency => {
					return agency.ACRONYM.toLowerCase().includes(this.search.toLowerCase()) || agency.AGENCYNAME.toLowerCase().includes(this.search.toLowerCase());
				});
			},
			totalRows() {
				return this.filteredAgencies.length;
			}
		},
		methods: {
			applySearchFilter: function(term){
				console.log(term)
				Event.$emit('vue-tables.filter::normalSearch', term);
			},
			chuckAgencies(){
				console.log(this.pagination.perPage)
			},
			viewAgency(id){
				this.$router.push({ name: 'agencies.view', params: { id: id } })
			}
		}
	}
</script>
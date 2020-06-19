<template>
	<div>
		<div class="header">
			<div class="header-body">
				<div class="row align-items-center">
					<div class="col">
						<!-- Pretitle -->
						<h6 class="header-pretitle">
							RETURNED PLATES
						</h6>
						<!-- Title -->
						<h1 class="header-title">
							RNP LIST
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
						</form>

					</div>
					<div class="col-auto">
						<b-input type="date" />
					</div>
					<div class="col-auto">
						<b-button class= "float-right" :to="{ name: 'rnp-create' }" variant="outline-primary" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Create RNP List</b-button>
					</div>
				</div>
			</div>

			<v-server-table
				class="table-sm table-nowrap card-table"
				:columns="serverColumns"
				:options="options"
				size="sm"
			></v-server-table>
		</div>
	</div>
</template>

<script type="text/javascript">
	import VueTables from 'vue-tables-2'
	const Event = VueTables.Event
	export default {
		name: 'RNPList',
		data(){
			return {
				serverColumns: ["HOST_COUNTRY_ID", "CLIENT", "DIPLOMATIC ID", "USERNAME", "KRA_PIN_NO", "REGISTRATION_DATE"],
				options: {
					perPage: 50,
					perPageValues: [],
					filterable: false,
					customFilters: ['normalSearch'],
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
		methods: {
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::normalSearch', term);
			}
		}
	}
</script>
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
								<b-input type="search" class="form-control form-control-flush search" v-model = "table.searchTerm" placeholder="Search" v-on:keyup="applySearchFilter(table.searchTerm)"/>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="card-body">
				<v-server-table
					class="table-sm table-nowrap card-table"
					:columns="table.columns"
					:options="table.options"
					size="sm">
						<!-- <template slot="IMAGE" slot-scope="data">
							<center>
								<b-img v-if="data.row.image !='' && data.row.image != '/storage/'" v-bind="options.principalImageProps" rounded="circle" :src="data.row.image"></b-img>
								<b-img v-else width="40" height="40" rounded="circle" alt="Circle image" src="/images/no_avatar.svg"></b-img>
							</center>
						</template>
						<template slot="actions" slot-scope="data">
							<router-link class="btn btn-sm btn-primary" :to="{ name: 'principal.view', params: { id: data.row.host_country_id } }">View</router-link> 
						</template> -->
					</v-server-table>
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
				table: {
					options: {
						perPage: 50,
						perPageValues: [],
						filterable: false,
						customFilters: ['normalSearch'],
						requestFunction: (data) => {
							return axios.get(`/api/principal/dependent/list`, {
								params: data
							})
							.catch(function (e) {
								console.log('error', e);
							}.bind(this));
						}
					},
					columns: ["HOST_COUNTRY_ID", "DEPENDENT_NAME", "PRINCIPAL", "RELATIONSHIP"],
					searchTerm: ""
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
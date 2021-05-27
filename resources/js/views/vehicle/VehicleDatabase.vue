<template>
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
							<b-button class= "float-right" :to="{ name: 'principal.add.pre-flight' }" variant="outline-success" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Add Missing Vehicle</b-button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<v-server-table
		ref="vehicleTable"
		:columns="table.columns"
		:options="table.options"
		class="table-sm table-nowrap card-table">
			<template slot="Owner" slot-scope="data">
				<span v-if="data.row.staff">
					{{ data.row.staff.last_name }}, {{ data.row.staff.other_names }}
				</span>
			</template>

			<template slot="Registration No" slot-scope="data">
				{{ data.row.regt_no }}
			</template>

			<template slot="Disposed?" slot-scope="data">
				<center>
					<span v-if="data.row.disposed">Yes</span>
					<span v-else>No</span>
				</center>
			</template>

			<template slot="Actions">
				TBA
			</template>
		</v-server-table>
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
					options: {
						perPage: 100,
						perPageValues: [],
						filterable: false,
						customFilters: ['normalSearch'],
						requestFunction: (data) => {
							return axios.get(`/api/vehicle/database/list`, {
								params: data
							})
							.catch(function (e) {
								console.log('error', e);
							}.bind(this));
						}
					},
					columns: ['Owner', 'make_model', 'engine_no', 'chassis_no', 'Registration No', 'status', 'Disposed?', 'Actions']
				}
			}
		},
		methods: {
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::normalSearch', term);
			},
		}
	}
</script>
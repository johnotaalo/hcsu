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
						</form>

					</div>
					<div class="col-auto">
						<b-button class= "btn-white float-right" :to="{ name: 'settings-user-add' }" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Add User</b-button>
					</div>
				</div>
			</div>
			<!-- <div class="card-body"> -->
				<v-server-table
					ref="usersTable"
					class="table-sm table-nowrap card-table"
					:columns="table.columns"
					:options="table.options"
					size="sm">
				</v-server-table>
			<!-- </div> -->
		</div>
	</div>
</template>

<script type="text/javascript">
	import VueTables from 'vue-tables-2'
	const Event = VueTables.Event

	export default {
		name: "UsersIndex",
		data(){
			return {
				searchTerm: "",
				table: {
					columns: ["name", "email", "type", "created_at", "actions"],
					options: {
						perPage: 50,
						perPageValues: [],
						filterable: false,
						customFilters: ['normalSearch'],
						columnsDropdown: false,
						sortIcon: {
							base: 'icon',
							up: 'icon-sort-up',
							down: 'icon-sort-down',
							is: 'icon-sort'
						},
						pagination: {
							nav: "fixed",
							dropdown: false,
							edge: true,
						},
						requestFunction: (data) => {
							return axios.get(`/api/users/all`, {
								params: data
							})
							.catch(function (e) {
								console.log('error', e);
							}.bind(this));
						}
					}
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
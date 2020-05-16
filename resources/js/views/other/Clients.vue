<template>
	<div>
		<div class="card no-body">
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
						<b-button class= "float-right" :to="{ name: 'clients.other.add' }" variant="outline-primary" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Add Client</b-button>
					</div>
				</div>
			</div>

			<v-server-table
			class="table-sm table-nowrap card-table"
			url = "/api/other/clients"
			:columns="tableOptions.columns"
			:options="tableOptions.options"
			size="sm"></v-server-table>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default{
		data(){
			return {
				searchTerm: '',
				tableOptions:{
					columns: [
						{
							label: "HOST COUNTRY ID",
							name: "HOST_COUNTRY_ID",
							filterable: true,
							orderable: true
						},
						// {
						// 	label: "NAME",
						// 	// name: "fullName",
						// 	filterable: true,
						// 	orderable: true
						// },
						{
							label: "PASSPORT NO",
							name: "PASSPORT_NO",
							filterable: true,
							orderable: true
						},
						{
							label: "TYPE",
							name: "TYPE",
							filterable: true,
							orderable: true
						},
						{
							label: "AGENCY",
							name: "AGENCYNAME",
							filterable: true,
							orderable: true
						}, 
						{
							label: "",
							name: "ACTIONS",
							filterable: false,
							orderable: false,
							// event: "click",
							// component: PrincipalActionsComponent,
							// handler: this.viewPrincipal
						}
					],
					options: {
						perPage: 50,
						perPageValues: [],
						filterable: false,
						customFilters: ['normalSearch'],
					}
				}
			}
		}
	}
</script>
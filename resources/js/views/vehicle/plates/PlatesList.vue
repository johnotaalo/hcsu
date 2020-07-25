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

					<v-server-table class="table-sm table-nowrap card-table" :columns="table.columns" :options="table.options" size="sm" url = "/api/vehicle/plates/list"></v-server-table>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
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

			}
		}
	}
</script>
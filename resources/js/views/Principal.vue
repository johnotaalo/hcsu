<template>
	<div>
		<div class = "card">
			<!-- <div class="row">
				<div class="col-sm-12">
					<b-button class= "float-right" variant="outline-primary" size ="sm">Add Client</b-button>
				</div>
				
			</div> -->
			<div class="card-header">
				<div class="row align-items-center">
					<div class="col">
						<!-- Title -->
						<h4 class="card-header-title">
						Principal
						</h4>

					</div>
					<div class="col-auto">
						<b-button class= "float-right" :to="{ name: 'principal.add' }" variant="outline-primary" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Add Client</b-button>
					</div>
				</div>
			</div>
			<div class="card-body">
				<!-- <div class="col-md-12"> -->
					<v-server-table
					url="/api/principal/"
					:columns="principal.columns"
					:options="principal.options"
					size="sm">
						<template slot="IMAGE" slot-scope="data">
							<center>
								<b-img v-if="data.row.image !='' && data.row.image != '/storage/'" v-bind="options.principalImageProps" rounded="circle" :src="data.row.image"></b-img>
								<b-img v-else width="50" height="50" rounded="circle" alt="Circle image" src="/images/no_avatar.svg"></b-img>
							</center>
						</template>
						<template slot="actions" slot-scope="data">
							<router-link class="btn btn-sm btn-primary" :to="{ name: 'principal.view', params: { id: data.row.host_country_id } }">View</router-link> 
						</template>
					</v-server-table>
				<!-- </div> -->
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				options: {
					principalImageProps: { blank: false, blankColor: '#777', width: 40, height: 40, class: 'm1' }
				},
				principal: {
					columns: ["IMAGE", "host_country_id", "last_name", "other_names", "email_address", "actions"],
					options: {
						perPage: 20,
						perPageValues: [10, 20, 50, 100],
						columnsDisplay: {
							last_name: 'not_mobile',
							other_names: 'not_mobile',
							email_address: 'desktop',
							agency: 'not_mobile',
							actions: 'not_mobile'
						},
						columnsDropdown: true,
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

						}
					}
				},
				columnsDropdown: true,
				sortIcon: {
					base: 'icon',
					up: 'icon-sort-up',
					down: 'icon-sort-down',
					is: 'icon-sort'
				},
				fields: [
					{
						key: "host_country_id",
						sortable: false,
						label: "HOST COUNTRY NO"
					},
					{
						key: "last_name",
						sortable: true
					},
					{
						key: "other_names",
						sortable: true
					},
					{
						key: "email_address",
						sortable: false
					},
					{
						key: "actions",
						sortable: false
					}
				],
				items: []
			}
		},
		mounted(){			
		},
		computed: {
			rows() {
				return this.items.length;
			}
		}
	}
</script>
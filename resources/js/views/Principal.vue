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
					<!-- :url="`${baseUrl}/api/principal/`" -->
					<v-server-table
					
					:columns="serverColumns"
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

					<!-- <data-table :url="`${baseUrl}/api/principal/`" :per-page="principal.options.perPageValues" :columns="principal.columns" /> -->
				<!-- </div> -->
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	import PrincipalImageComponent from '../components/principal/PrincipalImageComponent'
	import PrincipalActionsComponent from '../components/principal/PrincipalActionsComponent'
	export default {
		components: { PrincipalImageComponent, PrincipalActionsComponent },
		data(){
			return {
				baseUrl: window.Laravel.baseUrl,
				options: {
					principalImageProps: { blank: false, blankColor: '#777', width: 40, height: 40, class: 'm1' }
				},
				principal: {
					columns: [
						{
							label: "",
							name: "IMAGE",
							filterable: false,
							component: PrincipalImageComponent
						}, 
						{
							label: "HOST COUNTRY ID",
							name: "HOST_COUNTRY_ID",
							filterable: true,
							orderable: true
						}, 
						{
							label: "LAST NAME",
							name: "LAST_NAME",
							filterable: true,
							orderable: true
						}, 
						{
							label: "OTHER NAMES",
							name: "OTHER_NAMES",
							filterable: true,
							orderable: true
						}, 
						{
							label: "EMAIL",
							name: "EMAIL",
							filterable: true,
							orderable: true
						}, 
						{
							label: "",
							name: "View",
							filterable: false,
							orderable: false,
							event: "click",
							component: PrincipalActionsComponent,
							handler: this.viewPrincipal
						}
					],
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
							return axios.get(`${this.baseUrl}/api/principal`, {
								params: data
							})
							.catch(function (e) {
								console.log('error', e);
							}.bind(this));
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
			this.$parent.isContainer = true		
		},
		computed: {
			rows() {
				return this.items.length;
			},
			serverColumns(){
				return _.map(this.principal.columns, (o) => {
					if (o.name == "IMAGE") {
						return o.name
					}else if(o.name == "EMAIL"){
						return "email_address"
					}else if(o.name == "View"){
						return "actions"
					}
					return o.name.toLowerCase()
				})
			}
		},
		methods: {
			viewPrincipal(data){
				console.log(data)
			}
		}
	}
</script>
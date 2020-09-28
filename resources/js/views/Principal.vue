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
						<label>Show:</label>
					</div>
					<div class="col-auto">
						<b-form-select @change="applyActiveStaffFilter(activeStaff)"
						v-model="activeStaff" :options="staffOptions"></b-form-select>
					</div>
					<div class="col-auto">
						<b-button class= "float-right" :to="{ name: 'principal.add' }" variant="outline-primary" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Add Client</b-button>
					</div>
				</div>
			</div>
			<!-- <div class="card-body"> -->
				<!-- <div class="col-md-12"> -->
					<!-- :url="`${baseUrl}/api/principal/`" -->
					<v-server-table
					ref="principalTable"
					class="table-sm table-nowrap card-table"
					:columns="serverColumns"
					:options="principal.options"
					size="sm">
						<template slot="IMAGE" slot-scope="data">
							<center>
								<b-img v-if="data.row.image !='' && data.row.image != '/storage/'" v-bind="options.principalImageProps" rounded="circle" :src="data.row.image"></b-img>
								<b-img v-else width="40" height="40" rounded="circle" alt="Circle image" src="/images/no_avatar.svg"></b-img>
							</center>
						</template>
						<template slot="status" slot-scope="data">
							<span v-if="data.row.status == 1"><span class="text-success">●</span> Active</span>
							<span v-if="data.row.status == 0"><span class="text-danger">●</span> Inactive</span>
						</template>
						<template slot="name" slot-scope="data">
							{{ data.row.last_name }}, {{ data.row.other_names }}
						</template>
						<template slot="organization" slot-scope="data">
							{{ data.row.contract.ACRONYM }}
						</template>
						<template slot="actions" slot-scope="data">
							<router-link class="btn btn-sm btn-primary" :to="{ name: 'principal.view', params: { id: data.row.host_country_id } }" v-if="data.row.status == 1"><i class="fe fe-eye"></i>&nbsp;&nbsp;View Client</router-link>
							<b-button class="btn btn-sm btn-warning text-white" v-if="data.row.status == 0" @click="activateClient(data.row)"><i class="fe fe-check"></i>&nbsp;&nbsp;Activate Client</b-button>
							<b-button @click="downloadNOA(data.row.host_country_id)" class = "btn btn-sm btn-danger"><i class="fe fe-download"></i>&nbsp;&nbsp;Download NOA</b-button> 

							
						</template>
					</v-server-table>

					<!-- <data-table :url="`${baseUrl}/api/principal/`" :per-page="principal.options.perPageValues" :columns="principal.columns" /> -->
				<!-- </div> -->
			<!-- </div> -->
		</div>
	</div>
</template>
<script type="text/javascript">
	import PrincipalImageComponent from '../components/principal/PrincipalImageComponent'
	import PrincipalActionsComponent from '../components/principal/PrincipalActionsComponent'

	import VueTables from 'vue-tables-2'
	const Event = VueTables.Event

	export default {
		components: { PrincipalImageComponent, PrincipalActionsComponent },
		data(){
			return {
				baseUrl: window.Laravel.baseUrl,
				searchTerm: "",
				activeStaff: "all",
				options: {
					principalImageProps: { blank: false, blankColor: '#777', width: 40, height: 40, class: 'm1' }
				},
				staffOptions: [
					{ text: 'All Staff', value: "all" },
					{ text: 'Active Staff', value: "active" },
					{ text: 'Inactive Staff', value: "inactive" }
				],
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
							label: "Name",
							name: "NAME",
							filterable: true,
							orderable: true
						},
						{
							label: "ORGANIZATION",
							name: "ORGANIZATION",
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
							label: "Status",
							name: "STATUS",
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
						perPage: 50,
						perPageValues: [],
						filterable: false,
						customFilters: ['normalSearch', 'activeStaffSearch'],
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
			this.$parent.isContainer = false		
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
				// console.log(data)
			},
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::normalSearch', term);
			},
			applyActiveStaffFilter: function(active){
				console.log(active)
				Event.$emit('vue-tables.filter::activeStaffSearch', active);
			},
			downloadNOA(host_country_id){
				// console.log(host_country_id)
				window.open(`/api/documents/generate/NOA/${host_country_id}`);
			},
			activateClient: function(data, index){
				this.$swal({
					title: "Confirmation",
					text: `You are going to active ${data.last_name}, ${data.other_names}. Are you sure?`,
					icon: 'warning',
					buttons: true,
					dangerMode: true
				})
				.then((willActivate) => {
					if (willActivate) {
						axios.put(`api/principal/${data.id}/activate`, { "_METHOD": "PUT", id: data.id })
						.then(res => {
							this.$swal("Success", "Successfully activated client", "success")
							this.$refs.principalTable.refresh()
						})
						.catch((error) => {
							this.$swal("Error", "There was an error activating the client. Please contact the administrator", "error")
						})
					}
				})
			}
		}
	}
</script>
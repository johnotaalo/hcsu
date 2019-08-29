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
							Manage Prefixes
						</h1>
					</div>
				</div> <!-- / .row -->
			</div>
		</div>

		<div class="row">
			<div class="col-md-7">
				<div class="card">
					<div class="card-header">
						<div class="row align-items-center">
							<div class="col">

								<!-- Title -->
								<h4 class="card-header-title">
									Prefixes
								</h4>

							</div>
							<div class="col-auto">

								<!-- Button -->
								<a @click="openComponent({}, 'add')" class="btn btn-sm btn-white">
									Add Prefix
								</a>

							</div>
						</div>
					</div>
					<div class="table-responsive mb-0">
						<b-table class="table table-sm table-nowrap card-table" :fields="fields" :items="items" show-empty>
							<template slot='#' slot-scope="data">
								{{ data.index + 1 }}
							</template>
							<template slot = "organizations" slot-scope="data">
								<ul>
									<li v-for = "agency in data.item.agencies">{{ agency.agency_data.ACRONYM }}</li>
								</ul>
								<!-- {{ data.item.agencies | combineArray('agency_data["ACRONYM"]') }} -->
							</template>
							<template slot="empty" slot-scope="scope">
								<h4>{{ scope.emptyText }}</h4>
							</template>
							<template slot="actions" slot-scope="scope" class="align-middle">
								<b-button-group size="sm">
									<b-button variant="success" @click="openComponent(scope.item, 'edit')"><i class="fe fe-edit"></i>&nbsp;Edit</b-button>
									<b-button variant="info" @click="openComponent(scope.item, 'organizations')"><i class="fe fe-grid"></i>&nbsp;Organizations</b-button>
									<b-button variant="danger"><i class="fe fe-trash-2"></i>&nbsp;Delete</b-button>
								</b-button-group>
							</template>
						</b-table>
					</div>
				</div>
			</div>

			<div class="col-md-5">
				<div class="card" v-if="action.selected != null">
					<div class="card-header">
						<div class="row align-items-center">
							<div class="col">

								<!-- Title -->
								<h4 class="card-header-title">
									{{ action.title }}
								</h4>

							</div>
							<div class="col-auto">

								<!-- Button -->
								<a class="btn btn-sm btn-white" @click="closeAction">
									<i class="fe fe-x"></i>
								</a>

							</div>
						</div>
					</div>

					<div class="card-body">
						<div :is="action.component" v-bind="action.componentProps" @success="successSubmit" @error="errorSubmit"></div>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</template>

<script type="text/javascript">
	import ManagePrefix from './components/ManagePrefix'
	import PrefixOrganizations from './components/PrefixOrganizations'
	export default {
		components: { ManagePrefix, PrefixOrganizations },
		data(){
			return {
				fields: ['#', 'organizations','prefix', 'actions'],
				items: [],
				action: {
					selected: null,
					title: '',
					component: {},
					componentProps: {},
					events: []
				}
			}
		},
		created(){
			this.getAgencyPrefixes()
		},
		methods: {
			getAgencyPrefixes(){
				axios.get('/api/vehicle/plates/agency/prefixes')
				.then((res) => {
					this.items = res.data
				});
			},
			openComponent(data, action){
				this.action.selected = action
				switch(action){
					case 'edit':
						this.action.title = `Edit Prefix - ${data.prefix}`
						this.action.component = ManagePrefix
						this.action.componentProps = {
							data: data
						}
						// this.action.events = ['submitInformation'];
						break;
					case 'organizations':
						this.action.title = `${data.prefix} Organizations`
						this.action.component = PrefixOrganizations
						this.action.componentProps = {
							data: data.agencies,
							prefix_id: data.id
						}
						break;
					case 'add':
						this.action.title = `Add Prefix`
						this.action.component = ManagePrefix
						this.action.componentProps = {
							data: data
						}
						break;
				}
			},
			closeAction(){
				this.action.selected = null
				this.action.title = ''
				this.action.component = {}
				this.action.componentProps = {}
			},
			successSubmit(){
				this.getAgencyPrefixes()
				this.$swal("Success", "Successfully processed your request", "success");
				this.closeAction()
			},
			errorSubmit(error){
				this.$swal("Error", `There was an error submiting your request (${error.message})`, "error");
			}
		}
	}
</script>
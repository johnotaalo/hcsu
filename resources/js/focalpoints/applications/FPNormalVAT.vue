<template>
	<div>
		<div class="header">
			<div class="container-fluid">
				<div class="header-body">
					<div class="row align-items-end">
						<div class="col">
							<h6 class="header-pretitle">
								Normal VAT
							</h6>

							<h1 class="header-title">
								VAT Application
							</h1>
						</div>
						<div class="col-auto">
							<router-link :to="{ name: 'applications.normal-vat.add' }" class="btn btn-primary btn-lg">Add VAT</router-link>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				<div class="row align-items-center">
					<div class="col">

						<!-- Search -->
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

						<!-- Button -->

						<div class="dropdown">
							<button class="btn btn-sm btn-white dropdown-toggle" type="button" id="bulkActionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Bulk action
							</button>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="bulkActionDropdown">
								<a class="dropdown-item">Action</a>
								<a class="dropdown-item">Another action</a>
								<a class="dropdown-item">Something else here</a>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="table-responsive">
				<v-server-table url="/api/focal-points/vat/search"
				ref="uploadedVATTable"
				:columns="table.columns"
				:options="table.options"
				size="sm"
				class="table-sm table-nowrap card-table">
					<template slot="#" slot-scope="data">
						{{ data.index }}
					</template>

					<template slot="Case No" slot-scope="data">
						{{ data.row.CASE_NO }}
					</template>

					<template slot="Supplier" slot-scope="data">
						{{ data.row.supplier.SUPPLIER_NAME }}
					</template>

					<template slot="Date" slot-scope = "data">
						{{ data.row.data.date | moment('DD-MM-YYYY') }}
					</template>

					<template slot="Applied For" slot-scope = "data">
						{{ data.row.data.client.name }}
					</template>

					<template slot="Amount" slot-scope="data">
						KES {{ data.row.data.vat.vatAmount }}
					</template>

					<template slot="Status" slot-scope="data">
						<span v-if="data.row.STATUS == 'Pending'">
							<span class="text-warning"><i class="fe fe-clock"></i>&nbsp;Pending</span>
						</span>
						<span v-else-if="data.row.STATUS == 'Claimed'">
							<span class="text-primary"><i class="fe fe-user-check"></i>&nbsp;Claimed</span>
						</span>
						<span v-else-if="data.row.STATUS == 'Not Approved'">
							<span class="text-danger"><i class="fe fe-x"></i>&nbsp;Not Approved</span>
						</span>

						<span v-else-if="data.row.APPROVED == 1">
							<span v-if="data.row.STATUS != 'Cancelled' && data.row.STATUS != 'Canceled'">
								<span class="text-success"><pen-tool-icon size="1x"></pen-tool-icon>&nbsp;{{ data.row.STATUS }}</span>
							</span>

							<span class = "text-danger" v-if="data.row.STATUS == 'Cancelled' || data.row.STATUS == 'Canceled'"><x-octagon-icon size="1x"></x-octagon-icon>&nbsp;Canceled</span>
						</span>
					</template>

					<template slot="Action" slot-scope="data">
						<!-- <a class="btn btn-success btn-sm">View Status</a> -->

						<div class="dropdown">
							<a href="#" class="btn btn-primary btn-sm dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Actions
							</a>
							<div class="dropdown-menu dropdown-menu-right">
								<router-link :to="{name: 'applications.normal-vat.edit', params: { id: data.row.id }}" class="dropdown-item" v-if="data.row.STATUS == 'Not Approved'"><i class="fe fe-edit"></i>&nbsp;&nbsp;Edit</router-link>
								<router-link :to="{name: 'applications.normal-vat.view', params: { id: data.row.id }}" class="dropdown-item"><i class="fe fe-eye"></i>&nbsp;&nbsp;View Application</router-link>
								<a></a>
							</div>
						</div>
					</template>
				</v-server-table>
			</div>
			
		</div>
	</div>
</template>

<script type="text/javascript">
	import { PenToolIcon, XOctagonIcon } from 'vue-feather-icons'
	import VueTables from 'vue-tables-2'
	// const ClientTable = VueTables.ClientTable
	const Event = VueTables.Event
	// console.log(Event)

	export default {
		name: "FPNormalVAT",
		components: { PenToolIcon, XOctagonIcon },
		data(){
			return {
				tabIndex: 0,
				searchTerm: "",
				table: {
					columns: ['Case No', 'Date', 'Applied For', 'Supplier', 'Amount', 'Status', 'Action'],
					options: {
						sortable: ['Case No', 'Date'],
						filterable: false,
						perPageValues: [],
						customFilters: ['normalSearch']
					}
				}
			}
		},
		mounted(){
			// this.$parent.dashboard = false
		},
		methods: {
			resetCard() {
				this.tabIndex = 0
				location.reload()
			},
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::normalSearch', term);
			}
		}
	}
</script>
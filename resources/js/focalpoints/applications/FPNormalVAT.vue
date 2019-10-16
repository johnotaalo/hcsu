<template>
	<div>
		<b-card no-body>
			<b-tabs v-model="tabIndex" pills card>
				<b-tab>
					<template slot="title">VAT List</template>

					<v-server-table url="/api/focal-points/vat/search/"
					:columns="table.columns"
					:options="table.options"
					size="sm">
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
							<span v-if="data.row.STATUS == 'Claimed'">
								<span class="text-primary"><i class="fe fe-user-check"></i>&nbsp;Claimed</span>
							</span>
							<span v-if="data.row.STATUS == 'Not Approved'">
								<span class="text-danger"><i class="fe fe-x"></i>&nbsp;Not Approved</span>
							</span>

							<span v-if="data.row.APPROVED == 1">
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
				</b-tab>

				<b-tab title="Add VAT">
					<normal-vat applier="fp" @update-tabs="resetCard"></normal-vat>
				</b-tab>
			</b-tabs>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	import NormalVAT from '../../views/applications/NormalVAT'
	import { PenToolIcon, XOctagonIcon } from 'vue-feather-icons'

	export default {
		name: "FPNormalVAT",
		components: { 'normal-vat': NormalVAT, PenToolIcon, XOctagonIcon },
		data(){
			return {
				tabIndex: 0,
				table: {
					columns: ['#', 'Case No', 'Date', 'Applied For', 'Supplier', 'Amount', 'Status', 'Action'],
					options: {
						sortable: ['Case No']
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
			}
		}
	}
</script>
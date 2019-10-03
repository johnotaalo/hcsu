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
								<span class="text-warning">●</span>&nbsp;Pending
							</span>
						</template>

						<template slot="Action" slot-scope="data">
							<!-- <a class="btn btn-success btn-sm">View Status</a> -->
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

	export default {
		name: "FPNormalVAT",
		components: { 'normal-vat': NormalVAT },
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
		},
		methods: {
			resetCard() {
				this.tabIndex = 0
				location.reload()
			}
		}
	}
</script>
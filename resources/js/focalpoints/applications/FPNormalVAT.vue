<template>
	<div>
		<b-card no-body>
			<b-tabs pills card>
				<b-tab active>
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

						<template slot="Application Date" slot-scope = "data">
							{{ data.row.data.date | moment('DD-MM-YYYY') }}
						</template>

						<template slot="Applied For" slot-scope = "data">
							<!-- {{ data.row.client.name }} -->
						</template>

						<template slot="Status" slot-scope="data">
							<span v-if="data.row.STATUS == 'Pending'">
								<span class="text-warning">●</span>&nbsp;Pending
							</span>
						</template>
					</v-server-table>
				</b-tab>

				<b-tab title="Add VAT">
					<normal-vat applier="fp"></normal-vat>
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
				table: {
					columns: ['#', 'Case No', 'Date', 'Applied For', 'Supplier', 'Status', 'Action'],
					options: {}
				}
			}
		}
	}
</script>
<template>
	<div>
		<div v-if="dates.length > 0">
			<div class="row">
				<v-client-table
				:columns="columns",
				 striped
				 hover
				 :data="rowData"
				></v-client-table>
			</div>
		</div>
		<div v-else>
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card bg-light border">
						<div class="card-body">
							<center>
								<!-- Heading -->
								<h1 class="mb-2">
									<i class="fe fe-alert-circle"></i>
									<p>NO RNUMBER TRACKING DETAILS</p>
								</h1>

								<!-- Text -->
								<p class="small text-muted mb-0">
									There seems to be no Residence Number Tracking Information for the staff member. If you have the details, kindly add them by clicking on the button below
								</p>

								<p class="mt-3">
									<b-button variant="primary"><i class="fe fe-plus"></i>&nbsp;Add Residence Number Info</b-button>
								</p>
							</center>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default{
		name: 'RNumberTracking',
		props: {
			'value': { type: null, default: null },
			dates: {
				type: Array, default: () => { return [] }
			}
		},
		data:function(){
			return {
				columns: ["#", "Residence Number", "Issue Date", "Expiry Date", "Status"]
			}			
		},
		computed: {
			rowData: function(){
				let rowData = this.dates

				return _.map(rowData, (row) => {
					return {
						residenceNo				: row.RNO,
						issue_date				: row.ISSUE_DATE,
						expiry_date				: row.EXPIRY_DATE
					}
				})
			}
		}
	}
</script> 
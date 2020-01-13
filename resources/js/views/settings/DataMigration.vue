<template>
	<div>
		<b-card no-body>
			<b-tabs small card pills>
				<b-tab  active>
					<template v-slot:title>
						Principal&nbsp;&nbsp;<span class="badge badge-success">0</span>
					</template>

					<b-button variant="primary" size="sm" @click="importAllStaff()"><i class="fe fe-user-plus"></i>&nbsp;Import All</b-button>

					<v-server-table class="table-sm table-nowrap card-table"
					ref="pendingStaff"
					url="/api/data/management/pending/principals"
					:columns="principal.options.columns"
					:options="principal.options"
					size="sm">
						<template slot="index_no" slot-scope="data">
							{{ data.row.index_no }}
						</template>

						<template slot="staff_name" slot-scope="data">
							{{ data.row.last_name }}, {{ data.row.other_names }}
						</template>

						<template slot="actions" slot-scope="data">
							<b-button variant="danger" size="sm" @click="importIndividualStaff(data.row.record_id)"><i class="fe fe-user-plus"></i>&nbsp;Import</b-button>
						</template>
					</v-server-table>
				</b-tab>
			</b-tabs>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	export default{
		data(){
			return {
				principal: {
					options: {
						columns: ["index_no", "staff_name", "actions"]
					}
				}
			}
		},
		methods: {
			importIndividualStaff(record_id){
				axios.get(`/api/data/management/import/pending/staff/${record_id}`)
				.then(res => {
					if(res.data.status){
						this.$notify({
							group: 'foo',
							title: 'Success',
							text: res.data.message,
							type: "success"
						});

						this.$refs.pendingStaff.refresh();
					}else{
						this.$notify({
							group: 'foo',
							title: 'Error',
							text: res.data.message,
							type: "error"
						});
					}
				})
				.catch(function(error){
					this.$notify({
						group: 'foo',
						title: 'Error',
						text: 'There was an error importing data',
						type: "error"
					});
				});
			},

			importAllStaff(){
				axios.get('/api/data/management/import')
				.then(res => {
					this.$notify({
						group: 'foo',
						title: 'Success',
						text: 'Successfully imported staff data',
						type: "success"
					});

					this.$refs.pendingStaff.refresh();
				})
				.catch(function(error){
					this.$notify({
						group: 'foo',
						title: 'Error',
						text: 'There was an error importing data',
						type: "error"
					});
				});
			}
		}
	}
</script>
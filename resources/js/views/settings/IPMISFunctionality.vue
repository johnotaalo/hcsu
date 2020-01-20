<template>
	<div>
		<b-card title="IPMIS Functionality Matrix">
			<b-table :fields="table.fields" :items="table.data" small>
				<template v-slot:cell(IPMIS_FUNCTIONAL)="data">
					<b-form-checkbox :checked="(data.item.IPMIS_FUNCTIONAL) ? true : false" @change = "toggleIPMISFunctionality(data.item)"></b-form-checkbox>
				</template>
			</b-table>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../core/Form'
	export default {
		data(){
			return {
				table: {
					fields: ['PROCESS_NAME', 'IPMIS_FUNCTIONAL'],
					data: []
				}
			}
		},
		mounted(){
			this.getIPMISFunctionalityData()
		},
		methods: {
			getIPMISFunctionalityData(){
				axios.get('/api/data/processes/ipmis/functionality')
				.then(res => {
					this.table.data = res.data
				});
			},

			toggleIPMISFunctionality(row){
				var functional = (row.IPMIS_FUNCTIONAL) ? true : false;
				var form = new Form({
					functionality: !functional,
					id: row.id
				})

				form.post(`/api/data/processes/ipmis/functionality/${row.id}`)
				.then(res => {
					console.log(res)
				});
			}
		}
	}
</script>
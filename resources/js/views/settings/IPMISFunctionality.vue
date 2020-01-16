<template>
	<div>
		<b-card title="IPMIS Functionality Matrix">
			<b-table :fields="table.fields" :items="table.data"></b-table>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	export default {
		data(){
			return {
				table: {
					fields: ['PROCESS_NAME', 'FUNCTIONAL'],
					data: []
				}
			}
		},
		mounted(){
			this.getIPMISFunctionalityData()
		},
		methods: {
			getIPMISFunctionalityData(){
				axios.get('/api/data/');
			}
		}
	}
</script>
<template>
	<div>
		<b-card title="IPMIS Subprocesses">
			<v-server-table
			url="/api/data/processes/ipmis"
			:options="options"
			:columns="columns"
			></v-server-table>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	export default {
		data(){
			return {
				options: {
					filterable: false,
				},
				columns: ['parent_case', 'subprocess_case', 'case_title', 'creator']
			}
		}
	}
</script>
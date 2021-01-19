<template>
	<div>
		<div class="card">
			<div class="card-header">
				
			</div>
			<v-server-table
			responsive
			ref="documentsTable"
			class="table-sm table-nowrap card-table"
			:columns="serverColumns"
			:options="tableOptions"
			size="sm">
				<template slot="NAME" slot-scope="data">
					<p>{{ data.row.name }}</p>
				</template>
			</v-server-table>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default{
		data(){
			return {
				serverColumns: ["CASE_NO", "AGREEMENT_STATUS", "NAME", "NEXT_TO_SIGN", "CREATED_AT", "ACTION"],
				tableOptions: {
					perPage: 50,
					perPageValues: [],
					filterable: false,
					customFilters: ['documentSearch'],
					sortable: ["CASE_NO", "NAME", "CREATED_AT"],
					sortIcon: {
						base: 'fe',
						up: 'fe-arrow-up',
						down: 'fe-arrow-down',
						is: 'fe-minus'
					},
					requestFunction: (data) => {
						return axios.get(`/api/data/adobe-sign/documents`, {
							params: data
						})
						.catch(function (e) {
							console.log('error', e);
						}.bind(this));
					}
				}
			}
		},
		methods: {
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::documentSearch', term);
			},
		}
	}
</script>
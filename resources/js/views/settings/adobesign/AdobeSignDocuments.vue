<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="row align-items-center">
					<div class="col">
						<form class="row align-items-center">
							<div class="col-auto pr-0">
								<span class="fe fe-search text-muted"></span>
							</div>

							<div class="col">
								<b-input type="search" class="form-control form-control-flush search" v-model = "searchTerm" placeholder="Search" v-on:keyup="applySearchFilter(searchTerm)"/>
							</div>
						</form>
					</div>
				</div>
			</div>
			<v-server-table
			responsive
			ref="documentsTable"
			class="table-sm table-nowrap card-table"
			:columns="serverColumns"
			:options="tableOptions"
			size="sm">
			<template slot="CREATED_AT" slot-scope="data">
				{{ data.row.created_at }}
			</template>

			<template slot="ACTION" slot-scope="data">
				<a class="btn btn-sm btn-white" v-if="data.row.AGREEMENT_STATUS == 'SIGNED'" @click="downloadSignedApplication(data.row.id)">Download Signed</a>
			</template>
			</v-server-table>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default{
		data(){
			return {
				searchTerm: "",
				serverColumns: ["CASE_NO", "AGREEMENT_STATUS", "NEXT_TO_SIGN", "CREATED_AT", "ACTION"],
				tableOptions: {
					perPage: 50,
					perPageValues: [],
					filterable: false,
					customFilters: ['documentSearch'],
					sortable: ["CASE_NO", "CREATED_AT"],
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
			downloadSignedApplication: function(id){
				window.location = `/adobe-sign/download/${id}`;
			}
		}
	}
</script>
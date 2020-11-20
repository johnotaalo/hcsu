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

							<div class="col-auto">
								<b-button class="btn-sm btn-white" :to="{ name: 'applications.new' }">Start a New Application</b-button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<v-server-table
			ref="principalTable"
			class="table-sm table-nowrap card-table"
			:columns="columns"
			:options="options"
			size="sm">
			<template slot="#" slot-scope="data">
				{{ data.index }}
			</template>

			<template slot="CASE_NO" slot-scope="data">
				<span v-if="data.row.APP_NUMBER"> {{ data.row.APP_NUMBER }}</span>
				<span v-else>
					Not Assigned
				</span>
			</template>
			</v-server-table>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default{
		data(){
			return {
				columns: ['#', 'CASE_NO', 'PROCESS NAME', 'STATUS', 'CURRENT_USER', 'CREATED_AT', 'ACTIONS'],
				options: {
					perPage: 50,
					perPageValues: [],
					filterable: false,
					customFilters: ['normalSerch'],
					requestFunction: (data) => {
						return axios.get(`/api/focal-points/applications`, {
							params: data
						})
						.catch(function (e) {
							console.log('error', e);
						}.bind(this));
					}
				}
			}
		}
	}
</script>
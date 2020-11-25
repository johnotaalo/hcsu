<template>
	<div>
		<div class="card">
			<div class="card-body">
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

			<template slot="PROCESS_NAME" slot-scope="data">
				TBA
			</template>

			<template slot="UPLOADS" slot-scope="data">
				<a class="btn btn-link btn-sm" @click="viewuploads(data.row.id)">View Uploads</a>
			</template>
			<template slot="CREATED_AT" slot-scope="data">
				{{ data.row.created_at }}
			</template>

			<template slot="ACTIONS" slot-scope="data">
				<b-button v-if="data.row.CURRENT_USER == 'SUPERVISOR'" @click="assignCase(data.row.id)" variant="sm" class = "btn btn-white">Assign To AA</b-button>
			</template>
			</v-server-table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default{
		data(){
			return {
				searchTerm: "",
				columns: ['#', 'CASE_NO', 'PROCESS_NAME', 'UPLOADS', 'STATUS', 'CURRENT_USER', 'CREATED_AT', 'ACTIONS'],
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
		},
		created(){
		},
		methods: {
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::normalSearch', term);
			},

			assignCase: function(id){
				axios.get('/api/focal-points/applications/assign/' + id)
				.then(res => {
					alert('Successfully assigned to AA');
					location.reload()
				});
			},

			viewuploads: function(id){
				window.location = "/uploads/" + id
			}
		}
	}
</script>
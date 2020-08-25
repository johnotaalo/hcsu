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
					<div class="col-auto">
						<b-button class= "float-right" :to="{ name: 'settings-templates-add' }" variant="primary" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Add Template</b-button>
					</div>
				</div>
			</div>
			<v-server-table
			class="table-sm table-nowrap card-table"
			:columns="serverColumns"
			:options="options"
			size="sm">
				<template slot="FORM_NAME" slot-scope="data">
					{{ data.row.form_name }}
				</template>

				<template slot="INPUT_DOCUMENT" slot-scope="data">
					<div v-if="data.row.input_document">
						<center><i class="fe fe-check text-success"></i></center>
					</div>
					<div v-else>
						<center><i class="fe fe-x text-danger"></i></center>
					</div>
				</template>

				<template slot="ADOBE_SIGN" slot-scope="data">
					<div v-if="data.row.ADOBE_SIGN_TEMPLATE">
						<center><i class="fe fe-check text-success"></i></center>
					</div>
					<div v-else>
						<center><i class="fe fe-x text-danger"></i></center>
					</div>
				</template>

				<template slot="ACTION" slot-scope="data">
					<b-button :to="{ name: 'settings-templates-edit', params: { 'id': data.row.id  } }" class = "btn btn-sm btn-white"><i class="fe fe-edit"></i>&nbsp;Edit</b-button>
				</template>
			</v-server-table>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default{
		data(){
			return {
				serverColumns: ["FORM_NAME", "INPUT_DOCUMENT", "type", "ADOBE_SIGN", "ACTION"],
				options: {
					perPage: 50,
					perPageValues: [],
					filterable: false,
					customFilters: ['normalSearch'],
					requestFunction: (data) => {
						return axios.get(`/api/template/list`, {
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
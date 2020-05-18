<template>
	<div>
		<div class="card no-body">
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
						<b-button class= "float-right" :to="{ name: 'clients.other.add' }" variant="outline-primary" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Add Client</b-button>
					</div>
				</div>
			</div>

			<v-server-table
			class="table-sm table-nowrap card-table"
			:columns="tableOptions.columnsx"
			:options="tableOptions.options"
			size="sm">
				<div slot="NAME" slot-scope="props">
					{{ props.row.LAST_NAME }}, {{ props.row.OTHER_NAMES }}
				</div>

				<div slot="AGENCY" slot-scope="props">
					{{ props.row.ACRONYM }}
				</div>

				<div slot="ACTIONS" slot-scope="props">
					<b-button :to="{name: 'clients.other.edit', params: {id: props.row.HOST_COUNTRY_ID}}" href="#" class="btn btn-success btn-sm">Edit</b-button>
				</div>
			</v-server-table>
		</div>
	</div>
</template>

<script type="text/javascript">
	import VueTables from 'vue-tables-2'
	const Event = VueTables.Event
	export default{
		data(){
			return {
				searchTerm: '',
				tableOptions:{
					columnsx: ["HOST_COUNTRY_ID", "NAME", "TYPE", "NATIONALITY", "PASSPORT_NO", "AGENCY", "ACTIONS"],
					options: {
						perPage: 50,
						perPageValues: [],
						filterable: false,
						customFilters: ['normalSearch'],
						requestFunction: (data) => {
							return axios.get(`/api/other/clients`, {
								params: data
							})
							.catch(function (e) {
								console.log('error', e);
							}.bind(this));
						}
					}
				}
			}
		},
		methods: {
			editClient(id){
				alert(id)
			},
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::normalSearch', term);
			}
		}
	}
</script>
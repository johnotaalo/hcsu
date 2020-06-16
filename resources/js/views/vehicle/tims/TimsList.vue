<template>
	<div>
		<div class="header">
			<div class="header-body">
				<div class="row align-items-center">
					<div class="col">
						<!-- Pretitle -->
						<h6 class="header-pretitle">
							Tims Registration
						</h6>
						<!-- Title -->
						<h1 class="header-title">
							TIMS Registration List
						</h1>
					</div>
				</div> <!-- / .row -->
			</div>
		</div>

		<div class="card">
			<v-server-table
				class="table-sm table-nowrap card-table"
				:columns="serverColumns"
				:options="options"
				size="sm">
				<template slot="DIPLOMATIC ID" slot-scope="data">
					{{ data.row.DIP_ID_NO }}
				</template>

				<template slot="KRA_PIN_NO" slot-scope="data">
					{{ data.row.KRA_PIN_NO }}
				</template>
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
				serverColumns: ["HOST_COUNTRY_ID", "CLIENT", "DIPLOMATIC ID", "USERNAME", "KRA_PIN_NO", "REGISTRATION_DATE"],
				options: {
					perPage: 50,
					perPageValues: [],
					filterable: false,
					customFilters: ['normalSearch'],
					requestFunction: (data) => {
						return axios.get(`/api/data/tims/list`, {
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
				Event.$emit('vue-tables.filter::normalSearch', term);
			}
		}
	}
</script>
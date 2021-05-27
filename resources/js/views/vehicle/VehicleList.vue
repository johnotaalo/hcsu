<template>
	<div class="card">
		<v-server-table
		:url="apiUri"
		:columns="columns"
		:options="options"
		class="table-sm table-nowrap card-table"
		size="sm">
			<template slot="MAKE_MODEL" slot-scope="data">
				<p v-if="data.row.make_model">{{ data.row.make_model.MAKE_MODEL }}</p>
			</template>

			<template slot="OWNER(S)" slot-scope="data">
				<b-link>{{ data.row.owners.length }} {{ "Owner" | pluralize(data.row.owners.length) }} </b-link>
			</template>
		</v-server-table>
	</div>
</template>

<script type="text/javascript">
	export default {
		name: "VehicleList",
		props: {
			value: { type: null, default: null },
			host_country_id: {
				type: Number,
				required: false
			}
		},
		data() {
			return {
				columns: ["MAKE_MODEL", "COLOR", "ENGINE_NO", "CHASSIS_NO", "OWNER(S)"],
				options: {
					perPage: 20,
					perPageValues: [10, 20, 50, 100],
					filterable: false
				}
			}
		},
		mounted() {
			if (typeof this.host_country_id != "undefined") {
				this.options.filterable = false
			}
		},
		computed: {
			apiUri: function(){
				var host_country_id = (typeof this.host_country_id != "undefined") ? this.host_country_id : ""
				return "/api/vehicle/" + host_country_id
			}
		}
	}
</script>
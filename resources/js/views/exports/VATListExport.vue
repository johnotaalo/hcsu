<template>
	<div>
		<b-card title="VAT List Exports">
			<h4>Filters</h4>
			<div class="row">
				<div class="col-md">
					<b-form-group label="Purpose">
						<b-select v-model="filters.location" :options="options.locations" size="sm"></b-select>
					</b-form-group>
				</div>

				<div class="col-md">
					<b-form-group label="Limit">
						<b-select v-model="filters.limit" :options="options.limits" size="sm"></b-select>
					</b-form-group>
				</div>

				<div class="col-md">
					<b-form-group label="Order">
						<b-select v-model="filters.order" :options="options.orders" size="sm"></b-select>
					</b-form-group>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<b-button size="sm" variant="primary" @click="downloadList"><i class="fe fe-download"></i> Download List</b-button>
				</div>
			</div>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	export default {
		data(){
			return {
				filters: {
					location: 'all',
					limit: 500,
					order: 'asc'
				},
				options: {
					locations: [{text: 'All', value: 'all'}, {text: 'To MoFA', value: 'to-mofa'}, {text: 'To KRA', value: 'to-kra'}],
					limits: [500, 1000, 1500, 2000, 2500, 3000],
					orders: [{text: 'Ascending', value: 'asc'}, {text: 'Descending', value: 'desc'}]
				}
			}
		},
		methods:{
			downloadList(){
				window.open(`/api/documents/export/vat/normal/list?location=${this.filters.location}&limit=${this.filters.limit}&order=${this.filters.order}`, '_blank')
			}
		}
	}
</script>
<template>
	<div>
		<b-input-group prepend="New Organization" slass="mb-3">
			<b-form-select v-model="selected" :options="options"></b-form-select>
			<b-input-group-append>
				<b-button size="sm" @click="addOrganization"><i class="fe fe-plus"></i>&nbsp;Add</b-button>
			</b-input-group-append>
		</b-input-group>

		<table class="table">
			<tbody>
				<tr v-for="item in data">
					<td>{{ item.agency_data.ACRONYM }}</td>
					<td><b-button variant="danger" size="sm" @click="removeAgency(item.id)">Remove</b-button></td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../../../core/Form'
	export default {
		props: {
			data: { type: Array, default: [] },
			prefix_id: { type: Number, default: 0 }
		},
		data(){
			return {
				options: [],
				selected: ""
			}
		},
		created(){
			this.getOrganizations()
		},
		methods: {
			getOrganizations(){
				axios.get('/api/agencies')
				.then((res) => {
					var sortedData = _.orderBy(res.data, ['ACRONYM'], ['asc'])
					var availableAgencies = _.map(this.data, (o) => {
						return o.agency_data.ACRONYM
					})


					var data = _.map(sortedData, (agency) => {
						if(!_.includes(availableAgencies, agency.ACRONYM)){
							return {
								value: agency.HOST_COUNTRY_ID,
								text: agency.ACRONYM
							}
						}
					})

					data = _.without(data, undefined)

					this.options = data
				})
			},
			addOrganization(){
				var form = new Form({ prefix_id: this.prefix_id, host_country_id: this.selected })
				form.post('vehicle/plates/organization/prefix')
				.then(res => {
					this.$emit('success')
				})
				.catch(error => {
					this.$emit('error', error)
				})
			},
			removeAgency(id){
				var form = new Form({ id: id })
				form.delete(`vehicle/plates/organization/prefix/${id}`)
				.then(res => {
					this.$emit('success')
				})
				.catch(error => {
					this.$emit('error', error)
				})
			}
		}
	}
</script>
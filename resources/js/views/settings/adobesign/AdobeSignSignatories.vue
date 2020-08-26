<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="row align-items-center">
					<div class="col">
						<h2>Adobe Sign Signatories</h2>
					</div>
					<div class="col-auto">
						<b-button class="btn btn-white btn-sm" :to="{ name: 'setting-signatories-add' }"><i class = "fe fe-plus"></i>&nbsp;Add Signatory</b-button>
					</div>
				</div>
			</div>

			<b-table class="table-sm" :fields="table.fields" :items="table.items" show-empty>
				<template v-slot:cell(status)="data">
					<span v-if="data.item.status == true"><span class="text-success">●</span> Active</span>
					<span v-if="data.item.status == false"><span class="text-danger">●</span> Inactive</span>
				</template>

				<template v-slot:cell(actions)="data">
					<b-button class = "btn btn-sm btn-white" :to="{ name: 'setting-signatories-edit', params: { id: data.item.id } }"><i class = "fe fe-edit"></i>&nbsp;Edit</b-button>
				</template>
			</b-table>

		</div>
	</div>
</template>

<script type="text/javascript">
	export default {
		data(){
			return {
				table: {
					fields: ["last_name", "other_names", "email", "status", "actions"],
					items: []
				}
			}
		},
		mounted(){
			this.getSignatories()
		},
		methods: {
			getSignatories(){
				axios.get('/api/data/adobe-sign/signatories')
				.then(res => {
					this.table.items = res.data
				});
			}
		}
	}
</script>
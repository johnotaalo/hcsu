<template>
	<div>
		<div class="row mb-2">
			<div class="col">
				<b-button class = "float-right" size="sm" v-b-modal.add-focal-point><i class="fe fe-user-plus mr-2"></i>Add Focal Point</b-button>
			</div>
		</div>

		<div class="row">
			<div class="col">
				<b-table :fields="table.fields"></b-table>
			</div>
		</div>

		<b-modal size="lg" id="add-focal-point" title="Add Focal Point" @ok="mergeFocalPointData">
			<focal-point-details v-model="form.focalPoints" ref="fp"></focal-point-details>
		</b-modal>
	</div>
</template>

<script type="text/javascript">
	import FocalPointDetails from './FocalPointDetails'
	export default {
		name: "AgencyFocalPoints",
		components: { FocalPointDetails },
		data(){
			return {
				table: { fields: ["index_no", "focal_point", "extension", "mobile_no", "email", "actions"] },
				form: {
					focalPoints: []
				}
			}
		},
		methods: {
			mergeFocalPointData(model){
				console.log(this.form.focalPoints.length)
				var current = 0;
				// if(this.form.focalPoints.length != 0){
					current = this.form.focalPoints.length;
				// }

				this.form.focalPoints[current] = this.$refs.fp.data
			}
		}
	}
</script>
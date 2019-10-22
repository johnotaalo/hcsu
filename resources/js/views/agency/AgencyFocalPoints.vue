<template>
	<div>
		<div class="row mb-2">
			<div class="col">
				<b-button class = "float-right" size="sm" v-b-modal.add-focal-point><i class="fe fe-user-plus mr-2"></i>Add Focal Point</b-button>
			</div>
		</div>

		<div class="row">
			<div class="col">
				<b-table :fields="table.fields" :items="focalPoints" show-empty>
					<template slot="focal_point" slot-scope="data">
						{{ data.item.last_name }}, {{ data.item.other_names }}
					</template>

					<template slot="email" slot-scope="data">
						{{ data.item.email_address }}
					</template>

					<template slot="actions" slot-scope="data">
						<b-button variant="danger" size="sm" @click="removeFocalPoint(data.index)">Remove</b-button>&nbsp;&nbsp;<b-button variant="primary" size="sm" @click="editFocalPoint(data.index)">Edit</b-button>
					</template>

					<template v-slot:empty="scope">
						<h4 class="text-center">There are no focal points registered in this organization</h4>
					</template>
				</b-table>
			</div>
		</div>

		<b-modal size="lg" id="add-focal-point" ref="add-focal-point" title="Add Focal Point" @ok="mergeFocalPointData">
			<focal-point-details v-model="focalPoint" ref="fp"></focal-point-details>
		</b-modal>
	</div>
</template>

<script type="text/javascript">
	import FocalPointDetails from './FocalPointDetails'
	export default {
		name: "AgencyFocalPoints",
		components: { FocalPointDetails },
		props: {
			value: { type: null, default: null },
			agency: { type: null, default: null }
		},
		created(){
			if (this.agency != null) {
				this.getFocalPoints(this.agency)
			}
		},
		data(){
			return {
				table: { fields: ["index_no", "focal_point", "extension", "mobile_no", "email", "actions"] },
				focalPoint: {},
				focalPoints: [],
				editIndex: null
			}
		},
		watch: {
			focalPoints: function(val){
				this.$emit('focalPointsListener', val)
			}
		},
		methods: {
			getFocalPoints: function(host_country_id){
				axios.get(`api/agency/focal-point/${host_country_id}`)
				.then(res => {
					this.focalPoints = _.map(res.data, function(fp) {
						return {
							id 				: fp.ID,
							last_name		: fp.LAST_NAME,
							other_names		: fp.OTHER_NAMES,
							email_address	: fp.EMAIL,
							extension		: fp.EXTENSION,
							index_no		: fp.INDEX_NO,
							mobile_no		: fp.MOBILE_NO
						}
					})
				})
			},
			mergeFocalPointData(){
				var dataObj = {};
				var em = this;

				_.forOwn(this.focalPoint, (value, key) => {
					dataObj[key] = value;
					em.focalPoint[key] = "";
				});

				if(this.editIndex == null){
					this.focalPoints.push(dataObj);
				}else{
					this.focalPoints.splice(this.editIndex, 1, dataObj)
				}
				this.focalPoint = {};
				this.editIndex = null;
			},
			removeFocalPoint(index){
				this.$swal({
					title: "Delete Focal Point?",
					text: "This action cannot be undone",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						this.focalPoints.splice(index, 1);
					}
				});
			},
			editFocalPoint(index){
				this.editIndex = index
				this.focalPoint = this.focalPoints[index]
				this.$refs['add-focal-point'].show()
			}
		}
	}
</script>
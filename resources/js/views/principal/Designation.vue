<template>
	<div>
		<table>
			<tr>
				<td style="width: 10%;">
					<b-input size="sm" v-model="form.grade"></b-input>
				</td>
				<td style="width: 70%;">
					<b-input v-model = "form.designation" size="sm"></b-input>
				</td>
				<td style="width: 20%;padding: 4px;cursor: pointer;">
					<a class="btn-link" style="" @click="saveDesignation">Save</a>&nbsp;&nbsp;<a class="text-danger" @click="cancelDesignation">Cancel</a>
				</td>
			</tr>
		</table>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../core/Form'
	export default {
		props: {
			grade: { type: Object, default: () => { return {} } }
		},
		data(){
			return {
				form: new Form({
					designation: "",
					grade: this.grade.label,
					grade_id: this.grade.id,
					category: ""
				})
			}
		},
		methods: {
			saveDesignation(){
				this.form.post('/data/designations')
				.then(res => {
					var designationData =  {
						id: res.ID,
						label: `${res.DESIGNATION}`
					}
					this.$emit('saved', designationData)
				})
			},
			cancelDesignation(){
				this.form.designation = ""
				this.form.grade = ""
				this.$emit('cancel')
			}
		}
	}
</script>
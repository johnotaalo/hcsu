<template>
	<div>
		<table>
			<tr>
				<td style="width: 10%;">
					<b-input size="sm"></b-input>
				</td>
				<td style="width: 70%;">
					<b-input v-model = "form.designation" size="sm"></b-input>
				</td>
				<td style="width: 20%;padding: 4px;">
					<a class="btn-link" @click="saveDesignation">Save</a>&nbsp;&nbsp;<a class="text-danger" @click="cancelDesignation">Cancel</a>
				</td>
			</tr>
		</table>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../core/Form'
	export default {
		data(){
			return {
				form: new Form({
					designation: "",
					grade: "",
					category: ""
				})
			}
		},
		methods: {
			saveDesignation(){
				alert("Clicked Save");
			},
			cancelDesignation(){
				this.form.designation = ""
				this.form.grade = ""
				this.$emit('cancel')
			}
		}
	}
</script>
<template>
	<div>
		<div class="row">
			<div class="col">
				<div class="form-group">
					<label>Last Name<span class="text-danger">*</span></label>
					<b-input placeholder="Enter Last Name" v-model="value.last_name" />
				</div>
			</div>
			<div class="col">
				<div class="form-group">
					<label>Other Names<span class="text-danger">*</span></label>
					<b-input placeholder="Enter First Name" v-model="value.other_names" />
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label>Index No</label>
					<b-input placeholder="Enter Index No" v-model="value.index_no" />
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col">
				<div class="form-group">
					<label>Extension/Office No</label>
					<b-input placeholder="Enter Extension/Office No" v-model="value.extension" />
				</div>
			</div>
			<div class="col">
				<div class="form-group">
					<label>Mobile No<span class="text-danger">*</span></label>
					<b-input placeholder="Enter Mobile No" v-model="value.mobile_no" />
				</div>
			</div>
		</div>
		

		<div class="form-group">
			<label>Email Address<span class="text-danger">*</span></label>
			<b-input placeholder="Enter Email Address" v-model="value.email_address" />
		</div>
	</div>
</template>

<script type="text/javascript">
	export default {
		name: "FocalPointDetails",
		props: {
			'value': { type: null, default: null },
		},
		data() {
			return {
				data: {
					last_name: "",
					other_names: "",
					index_no: "",
					extension: "",
					email_address: "",
					mobile_no: ""
				}
			}
		},
		created(){
			
		}
	}
</script>
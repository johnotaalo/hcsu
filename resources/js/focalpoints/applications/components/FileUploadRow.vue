<template>
	<transition name="fade">
		<div class="row align-items-center justify-content-between">
			<div class="col-md-4">
				<div class="form-group">
					<label class = "control-label">File</label>
					<b-file v-model = "value.file" size="sm"></b-file>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class = "control-label">Description</label>
					<b-input v-model = "value.description" size="sm"></b-input>
				</div>
			</div>
			<div class="col-md-2">
				<b-button variant="danger" size="sm">Remove File</b-button>
			</div>
		</div>
	</transition>
</template>

<script type="text/javascript">
	export default {
		name: "FileUploadRow",
		props: {
			'value': { type: null, default: null },
		},
		data(){
			return {

			}
		},
		methods: {
			removeRow () {
				this.$emit('remove', this.value.id)
			},
		}
	}
</script>
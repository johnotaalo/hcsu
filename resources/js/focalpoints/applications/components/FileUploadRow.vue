<template>
	<transition name="fade">
		<div class="row align-items-center justify-content-between">
			<div class="col-md-4">
				<div class="form-group">
					<label class = "control-label">File</label>
					<div v-if="(typeof (value.edit == 'undefined') && showDocumentUploader == true)">
						<b-file v-model = "value.file" size="sm"></b-file>
					</div>
					<div v-else>
						<div class="media">
							<img style="width: 32px;" src="/images/pdf.svg" class="mr-3" alt="PDF Logo">
							<div class="media-body">
								<h4 class="mt-0">Uploaded Document&nbsp;&nbsp;<br/><button class="btn btn-link text-danger" @click="showDocumentUploader = true"><i class="fe fe-refresh-cw"></i>&nbsp;Change</button>&nbsp;|&nbsp;<button class="btn btn-link text-success" @click="viewdocument"><i class="fe fe-eye"></i>&nbsp;View</button></h4>
								<!-- <p></p> -->
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class = "control-label">Description</label>
					<b-input v-model = "value.description" size="sm"></b-input>
				</div>
			</div>
			<div class="col-md-2">
				<b-button variant="danger" size="sm" @click="removeRow()">Remove File</b-button>
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
				showDocumentUploader: true
			}
		},
		created(){
			if (this.value.edit == true) {
				this.showDocumentUploader = false
			}
		},
		methods: {
			removeRow () {
				this.$emit('remove', this.value.id)
			},
			viewdocument(){
				// console.log(this.value.link)

				window.open(`/uploads/${this.value.id}`, '_blank');
			}
		}
	}
</script>
<template>
	<div>
		<div class="header">
			<div class="header-body">
				<div class="row align-items-center">
					<div class="col">
						<!-- Pretitle -->
						<h6 class="header-pretitle">
							RETURNED PLATES
						</h6>
						<!-- Title -->
						<h1 class="header-title">
							RNP LIST
						</h1>
					</div>
				</div> <!-- / .row -->
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<div class="row align-items-center">
					<div class="col">
						<form class="row align-items-center">
							<div class="col-auto pr-0">
								<span class="fe fe-search text-muted"></span>
							</div>

							<div class="col">
								<b-input type="search" class="form-control form-control-flush search" v-model = "searchTerm" placeholder="Search" v-on:keyup="applySearchFilter(searchTerm)"/>
							</div>
						</form>

					</div>
					<div class="col-auto">
						<b-input type="date" v-model="searchDate" v-on:change="applyDateFilter(searchDate)" />
					</div>
					<div class="col-auto">
						<b-button class= "float-right" :to="{ name: 'rnp-create' }" variant="outline-primary" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Create RNP List</b-button>
					</div>
				</div>
			</div>

			<v-server-table
				class="table-sm table-nowrap card-table"
				:columns="serverColumns"
				:options="options"
				size="sm">
				<template slot="PLATES" slot-scope="data">
					<b-link href="#" @click="showPlates(data.row.plates)"><i class = 'fe fe-eye'></i>&nbsp;Show Returned Plates</b-link>
				</template>
				<template slot="UNSIGNED_LIST" slot-scope="data">
					<b-link :href="`/api/vehicle/plates/returned/download/list/unsigned/${data.row.id}`" target="_blank"><i class = 'fe fe-download'></i>&nbsp;Download Unsigned List</b-link>
				</template>
				<template slot="SIGNED_LIST" slot-scope="data">
					<b-link :href="`vehicle/plates/returned/download/list/signed/${data.row.id}`" v-if="data.row.SIGNED_DOCUMENT"><i class = 'fe fe-download'></i>&nbsp;Download RNP</b-link>
					<span v-else>No Document Uploaded&nbsp;<b-link href='#' @click="showUploadModal(data.row.id)"><i class = 'fe fe-upload'></i>&nbsp;Upload</b-link></span>
				</template>
				<template slot="ACTIONS" slot-scope="data">
					<!-- <b-link :href="data.SIGNED_DOCUMENT" v-if="data.SIGNED_DOCUMENT">Disabled Link</b-link>
					<span v-else>No Document Uploaded</span> -->
					<b-button v-if="!data.row.SIGNED_DOCUMENT" class="btn btn-success btn-sm" :to="{ name: 'rnp-edit', params: { id: data.row.id } }"><i class="fe fe-edit"></i>&nbsp;Edit</b-button>
					<span v-else>No action allowed</span>
				</template>
			</v-server-table>

			<b-modal id="returned_plates" title="Returned Plate Numbers" modal-ok="false">
				<b-table striped bordered :fields="modal.returned_plates.fields" :items="modal.returned_plates.data">
					<template v-slot:cell(Client)="row">
						<span v-if="row.item.client_type == 'staff'">{{ row.item.client.LAST_NAME }}, {{ row.item.client.OTHER_NAMES }}</span>
						<span v-else-if="row.item.client_type == 'dependent'">{{ row.item.client_details.LAST_NAME }}, {{ row.item.client_details.OTHER_NAMES }}</span>
						<span v-else-if="row.item.client_type == 'agency'">{{ row.item.client_details.ACRONYM }}</span>
					</template>
					<template v-slot:cell(Number)="row">
						<span>{{ row.item.PLATE_NO }}</span>
					</template>
				</b-table>
			</b-modal>

			<b-modal id="upload-signed-list" title="Upload Signed List" @ok="uploadData">
				<b-form-file v-model="form.uploadedList" ref="file-input" class="mb-2"></b-form-file>
			</b-modal>
		</div>
	</div>
</template>

<script type="text/javascript">
	import VueTables from 'vue-tables-2'
	import Form from '../../../../core/Form'
	const Event = VueTables.Event
	export default {
		name: 'RNPList',
		data(){
			return {
				searchTerm: "",
				searchDate: "",
				serverColumns: ["RNP_DATE", "PLATES", "UNSIGNED_LIST", "SIGNED_LIST", "ACTIONS"],
				form: new Form({
					'uploadedList': null,
					id: 0
				}),
				options: {
					perPage: 50,
					perPageValues: [],
					filterable: false,
					customFilters: ['normalSearch', 'dateSearch'],
					requestFunction: (data) => {
						return axios.get(`/api/vehicle/plates/returned/list`, {
							params: data
						})
						.catch(function (e) {
							console.log('error', e);
						}.bind(this));
					}
				},
				modal: {
					returned_plates: {
						data: [],
						fields: ['Client', 'Number']
					},
					upload: {
						id: 0
					}
				}
			}
		},
		methods: {
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::normalSearch', term);
			},
			applyDateFilter: function(term){
				Event.$emit('vue-tables.filter::dateSearch', term)
			},
			showPlates(plates){
				this.modal.returned_plates.data = plates
				this.$bvModal.show('returned_plates')
			},
			showUploadModal(id){
				this.modal.upload.id = id
				this.$bvModal.show('upload-signed-list');
			},
			uploadData(){
				this.form.id = this.modal.upload.id
				this.form.post('/vehicle/plates/returned/upload/list/signed')
				.then(res => {
					this.$swal(`Success`, "Successfully uploaded list", "success")
				})
				.catch(error => {
					this.$notify({
							group: 'foo',
							title: 'Error',
							text: "There was an error uploading the document. Please try again later",
							duration: -1,
							type: "error"
						});
				})
			}
		}
	}
</script>
<template>
	<div>
		<div class="row">
			<div class="col-md">
				<button class="btn btn-rounded btn-primary" v-b-modal.instructionsModal><i class="fe fe-info"></i>&nbsp;Click here to view instructions</button>
				<!-- &nbsp;|&nbsp;
				<a class="btn btn-rounded btn-danger" target = "_blank" href="/documents/Guidelines for the submission of VAT claims.pdf"><i class="fe fe-download"></i>&nbsp;Download VAT Guidelines</a> -->
			</div>
		</div>
		<div class="row">
			<div class="col-md">
				<fieldset>
					<legend>Client</legend>
					<div v-if="showClientForm">
						<search-client v-model="form"></search-client>
						<button v-if="id != 0" class="btn btn-sm btn-warning" @click="showClientForm = false">Revert</button>
					</div>
					<div v-else>
						<p>Picked Details: <button class="btn btn-link" @click="showClientForm = true">Change</button></p>
						<b>Type: </b>{{ data.data.client.type | capitalize }}<br/>
						<b>Name: </b>{{ data.data.client.name }}
					</div>
				</fieldset>
				<hr>
				<fieldset>
					<legend>Supplier</legend>
					<!-- <div class="row">
						<div class="col-md">
							<div class="form-group">
								
							</div>
						</div>
					</div> -->
					<div class="row">
						<div class="col-md">
							<label class="control-label">Pick a Supplier</label>
							<v-select label = "SUPPLIER_NAME" :options="suppliers" @search="onSupplierSearch" v-model="form.supplier"></v-select>
						</div>

						<div class="col-md">
							<label class="control-label">Supplier PIN/VAT No.</label>
							<b-input :value="form.supplier.PIN" disabled></b-input>
						</div>

						<div class="col-md">
							<label class="control-label">Supplier Address</label>
							<b-input :value="form.supplier.SUPPLIER_ADDRESS" disabled></b-input>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md">
				<legend>Documents</legend>
				<b-button variant = "success" size="sm" @click="addRow(form.documents)" class = "mb-3"><i class="fe fe-plus"></i>&nbsp;Add Document</b-button>

				<document-row v-for="(row, index) in form.documents" :key="row.id" v-model="form.documents[index]" @remove="removeRow($event, form.documents)" v-if="form.documents.length > 0"></document-row>
				<b-card v-if="form.documents.length == 0" class="text-center">
					<h2>No Documents Added Yet</h2>
				</b-card>
			</div>
		</div>

		<div class="row">
			<div class="col-md">
				<b-button variant="primary" @click="submitApplication">Submit</b-button>
			</div>
		</div>

		<b-modal ref="successModal" hide-footer title="Success!">
			<div class="d-block text-center">
				<h5>Your case has been created!</h5>
				<h3>Case No: {{ case_no }}</h3>
			</div>
			<!-- <b-button class="mt-3" variant="outline-success" block @click="openOutputLink">Download Document Control Form</b-button> -->
		</b-modal>

		<b-modal ref="errorModal" title="Error!">
			<div class="d-block">
				<h5>Rectify the errors before proceeding</h5>
				<p v-for = "(error, key) in errors">
					{{ key | capitalize }} Field
					<ul>
						<li v-for = "e in error">{{ e }}</li>
					</ul>
				</p>
			</div>
		</b-modal>

		<b-modal ref="instructionsModal" title = "Instructions" hide-footer hide-header id="instructionsModal">
			<!-- <div class="col-md"> -->
				<!-- <b-alert variant="danger" dismissible show> -->
					<instructions-row></instructions-row>
					<b-form-checkbox class="mb-2 mr-sm-2 mb-sm-0 mt-3" v-model = "hideinstructions">Do not show this pop up again</b-form-checkbox>
					<b-button class="mt-3" variant="outline-success" block @click="closeInstructionForm">Got It</b-button>
					<!-- <b-button></b-button> -->
				<!-- </b-alert> -->
			<!-- </div> -->
		</b-modal>
	</div>
</template>

<script type="text/javascript">
	import vSelect from 'vue-select'
	import Form from '../../core/Form'
	import DocumentRow from './components/DocumentRow'
	import rowForm from '../../mixins/rowForm'
	import SearchClientComponent from '../../components/SearchClientComponent'
	import InstructionsRow from './components/InstructionsRow'
	import _ from 'lodash'

	export default {
		name: "NormalVAT",
		components: { 'v-select': vSelect, 'search-client': SearchClientComponent, Form, DocumentRow, InstructionsRow },
		mixins: [rowForm],
		props: {
			applier: { type: String, default: 'fp' },
			id: { type: Number,  default: 0 },
			viewtype: { type: String, default: 'new' }
		},
		data(){
			return {
				hide_instruction_label: "hide-instruction",
				hideinstructions: false,
				showClientForm: true,
				case_no: "",
				output_document_link: "",
				suppliers: [],
				form: new Form({
					supplier: "",
					client: {},
					documents: []
				}),
				errors: [],
				data: {}
			}
		},
		created() {
			if (this.id != 0) {
				this.getVATDetails()
			}
		},

		mounted(){
			if(localStorage.getItem(this.hide_instruction_label) == "false"){
				this.hideinstructions = false
				this.$refs['instructionsModal'].show()
			}else{
				this.hideinstructions = true
			}

			this.$root.$on('bv::modal::hide', (bvEvent, modalId) => {
				if (modalId == "instructionsModal") {
					this.closeInstructionForm()
				}
			})
		},
		methods: {
			downloadGuidelines(){

			},
			getVATDetails(){
				axios(`/api/focal-points/vat/user-application/${this.id}`)
				.then(res => {
					this.case_no = res.data.CASE_NO
					this.showClientForm = false
					// this.form.clientType = (res.data.data.client.type == "staff") ? "staff-member" : res.data.data.client.type
					var invoices = res.data.invoices

					this.data = res.data

					this.form.supplier = res.data.supplier
					this.form.client = res.data.client
					this.form.documents = _.map(res.data.documents, (doc, key) => {
						return {
							id: doc.id,
							documentType: invoices[0].DOCUMENT_TYPE,
							documentNo: invoices[0].DOCUMENT_NO,
							documentDate: invoices[0].DOCUMENT_DATE,
							goodsDescription: invoices[0].GOODS_DESCRIPTION,
							vatAmount: invoices[0].VAT_AMOUNT,
							link: doc.document_link,
							edit: true
						}
					})
				})
			},
			onSupplierSearch(search, loading){
				loading(true)
				this.supplierSearch(loading, search, this)
			},
			supplierSearch: _.debounce((loading, search, vm) => {
				axios(`/api/data/suppliers/search?q=${escape(search)}`)
				.then((res) => {
					vm.suppliers = res.data
					loading(false)
				})
			}, 350),
			submitApplication: function(){
				this.errors = [];
				this.$store.commit('loadingOn');
				this.form.post('/focal-points/vat').then(res => {
					this.$store.commit('loadingOff');
					// this.$refs['successModal'].show()
					this.$swal("All Good!", `Successfully submitted VAT Application. Your Case No is: ${res.case_no}. We have also sent an email to your address with the details`, "success")
					.then((value) => {
						if (value) {
							this.$emit('update-tabs')
						}
					})

					this.form.supplier = ""
					this.form.client = {}
					this.form.documents = []
				}).catch((error) => {
					this.$store.commit('loadingOff');
					this.$swal("Error", `There was an error while applying for your application. ${error.message}`, "error")
					if (error.errors) {
						this.errors = error.errors
						this.$refs['errorModal'].show()
					}
				});
			},
			closeInstructionForm: function(){
				localStorage.setItem(this.hide_instruction_label, this.hideinstructions)
				this.$refs['instructionsModal'].hide()
			},
			openOutputLink: function(){
				window.location.href = this.output_document_link
			}
			// supplierSearch: (loading, search, vm) => {}
		},
		computed: {
			// hideinstructionsx: function(){
			// 	return (localStorage.getItem(this.hide_instruction_label) === "true") ? true : false
			// }
		},
		watch: {
			showClientForm: function(newVal, oldVal){
				if (newVal == false) {
					this.form.client = this.data.client
				}
			}
		}
	}
</script>
<template>
	<div>
		<div class="row">
			<div class="col-md">
				<fieldset>
					<legend>Client</legend>
					<search-client v-model="form"></search-client>
				</fieldset>
				<fieldset>
					<legend>Supplier</legend>
					<div class="row">
						<div class="col-md">
							<div class="form-group">
								
							</div>
						</div>
					</div>
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
			<div class="col-md">
				<b-alert variant="danger" dismissible show>
					<h3>Instructions</h3>
					<ul>
						<li>Please scan all documents as one pdf document</li>
						<li>If you are claiming for anything pertaining to a car, please include the <b>log book of the car</b></li>
						<li>If you are claiming for a meeting, please include the <b>list of participants</b></li>
						<li>All official VAT claims <b>must</b> include an <b>LPO</b></li>
					</ul>
					<!-- <b-button></b-button> -->
				</b-alert>
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
	</div>
</template>

<script type="text/javascript">
	import vSelect from 'vue-select'
	import Form from '../../core/Form'
	import DocumentRow from './components/DocumentRow'
	import rowForm from '../../mixins/rowForm'
	import SearchClientComponent from '../../components/SearchClientComponent'
	import _ from 'lodash'

	export default {
		name: "NormalVAT",
		components: { 'v-select': vSelect, 'search-client': SearchClientComponent, Form, DocumentRow },
		mixins: [rowForm],
		props: {
			applier: { type: String, default: 'fp' },
			id: { type: Number,  default: 0 },
			viewtype: { type: String, default: 'new' }
		},
		data(){
			return {
				case_no: "",
				output_document_link: "",
				suppliers: [],
				form: new Form({
					supplier: "",
					client: {},
					documents: []
				})
			}
		},
		created() {
			if (this.id != 0) {
				this.getVATDetails()
			}
		},
		methods: {
			getVATDetails(){
				axios(`/api/focal-points/vat/user-application/${this.id}`)
				.then(res => {
					this.case_no = res.data.CASE_NO
					this.form.clientType = (res.data.data.client.type == "staff") ? "staff-member" : res.data.data.client.type
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
				this.$store.commit('loadingOn');
				this.form.post('/focal-points/vat').then(res => {
					this.$store.commit('loadingOff');
					// this.$refs['successModal'].show()
					this.$swal("All Good!", `Successfully submitted VAT Application. Your Case No is: ${res.case_no}. We have also sent an email to your address with the details`, "success")
					.then((value) => {
						if (value) {
							// this.$emit('update-tabs')
						}
					})

					// this.form.supplier = ""
					// this.form.client = {}
					// this.form.documents = []
				}).catch((error) => {
					// console.log(error);
					this.$store.commit('loadingOff');
					this.$swal("Error", "There was an error while applying for your application. Please try again later", "error")
				});
			},
			openOutputLink: function(){
				window.location.href = this.output_document_link
			}
			// supplierSearch: (loading, search, vm) => {}
		}
	}
</script>
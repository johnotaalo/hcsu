<template>
	<div>
		<fieldset>
			<legend>Client</legend>
			<search-client v-model="form.client"></search-client>
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
					<label class="control-label">Supplier PIN</label>
					<b-input :value="form.supplier.PIN" disabled></b-input>
				</div>

				<div class="col-md">
					<label class="control-label">Supplier Address</label>
					<b-input :value="form.supplier.SUPPLIER_ADDRESS" disabled></b-input>
				</div>
			</div>
		</fieldset>
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
				<b-button variant="primary">Submit</b-button>
			</div>
		</div>
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
			applier: { type: String, dafault: 'fp' }
		},
		data(){
			return {
				suppliers: [],
				form: new Form({
					supplier: "",
					client: {},
					documents: []
				})
			}
		},
		created() {
			
		},
		methods: {
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
			// supplierSearch: (loading, search, vm) => {}
		}
	}
</script>
<template>
	<transition name="fade">
		<div>
			<b-card>
				<div class="row">
					<div class="col-sm">
						<label for="document_type">Document Type</label>
						<b-select :size="formSize" :options="documentTypes" v-model="value.documentType"></b-select>
					</div>

					<div class="col-sm">
						<label for="document_type">Document No</label>
						<b-input :size="formSize" v-model="value.documentNo"></b-input>
					</div>

					<div class="col-sm">
						<label for="document_type">Document Date</label>
						<!-- <b-input :size="formSize"></b-input> -->
						<datetime v-model="value.documentDate" input-class="form-control" :max-datetime="maxInvoiceDate" :min-datetime="minInvoiceDate"></datetime>
					</div>

					<!-- <div class="col-sm">
						<label for="document_type">Days to Expiry</label>
						<b-input :size="formSize" disabled></b-input>
					</div> -->

					<div class="col-sm">
						<label for="document_type">Goods/Services Description</label>
						<b-input :size="formSize" v-model="value.goodsDescription"></b-input>
					</div>

				</div>

				<b-row class="mt-3" align-v="center">
					<div class="col-sm-3">
						<label for="document_type">VAT Amount</label>
						<b-input-group :size="formSize" prepend="KSH">
							<b-input v-model="value.vatAmount"></b-input>
						</b-input-group>
					</div>

					<div class="col-sm-3" v-if="value.documentType == 'invoice'">
						<label for="document_type">ETR No./ESD</label>
						<b-input v-model="value.etr"></b-input>
					</div>

					<div class="col-sm-6">
						<label for="document_type">Invoice File</label>
						<b-file placeholder="Choose a file..." drop-placeholder="Drop file here..." v-model="value.invoiceFile" accept="application/pdf"></b-file>
					</div>
				</b-row>

				<div slot="footer">
					<b-button variant="danger" size="sm" @click="removeRow"><i class="fe fe-trash"></i>&nbsp;Remove</b-button>
				</div>	
			</b-card>
			
		</div>
	</transition>
	
</template>

<script type="text/javascript">
	import { Datetime } from 'vue-datetime'
	import 'vue-datetime/dist/vue-datetime.css'
	export default {
		name: "DocumentRow",
		props: {
			'value': { type: null, default: null },
		},
		components: { Datetime },
		data(){
			return {
				formSize: "",
				documentTypes: [
					{ value: "profoma", text: "Profoma" },
					{ value: "invoice", text: "Invoice" }
				]
			}
		},
		created(){

		},
		methods: {
			removeRow () {
				this.$emit('remove', this.value.id)
			}
		},
		computed: {
			maxInvoiceDate(){
				return this.$moment().format();
			},
			minInvoiceDate(){
				return this.$moment().subtract("4", "months").format();
			}
		}
	}
</script>
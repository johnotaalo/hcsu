<template>
	<div>
		<div class="row align-items-center mb-2">
			<div class="col">
				<form class="row align-items-center">
					<div class="col-auto pr-0">
						<span class="fe fe-search text-muted"></span>
					</div>

					<div class="col">
						<b-input type="search" class="form-control " v-model = "searchTerm" placeholder="Search" v-on:keyup="applySearchFilter(searchTerm)"/>
					</div>
				</form>

			</div>
			<div class="col-auto">
				<b-button v-b-modal.manage-supplier variant="primary" size="sm">Add Supplier</b-button>
			</div>
		</div>
		<v-server-table :columns="columns" :data="suppliers" :options="options" class="table-sm">
			<template slot="#" slot-scope="props">
				{{ props.index }}
			</template>

			<!-- <template slot="Supplier" slot-scope="props">
				{{ props.row.SUPPLIER_NAME }}
			</template> -->
			<template slot="Address" slot-scope="props">
				{{ props.row.SUPPLIER_ADDRESS }}
			</template>
			<template slot="VAT/PIN No" slot-scope="props">
				{{ props.row.PIN }}
			</template>

			<template slot="Actions" slot-scope="props">
				<b-button size="sm" @click="editSupplier(props.row)">Edit</b-button>
			</template>
		</v-server-table>

		<b-modal id="manage-supplier" title="Add Supplier" @ok="submitModal" @hidden="resetModal">
			<b-alert v-model="modalLoader.error" variant="danger" dismissible>
				<p>See the errors below:</p>
				<p>{{ modalLoader.errorMessage }}</p>
				<ul v-if="modalLoader.validationErrors">
					<li v-for="(error, field) in modalLoader.validationErrors">
						<span v-if="Array.isArray(error)">
							{{ field }}
							<ul>
								<li v-for="e in error">{{ e }}</li>
							</ul>
						</span>
						<span v-else>{{ error }}</span>
					</li>
				</ul>
			</b-alert>
			<loading
			:active.sync="modalLoader.isLoading"
	        :can-cancel="false"
	        :is-full-page="false"></loading>
			<b-form-group>
				<label>Supplier Name</label>
				<b-input v-model = "modal.SUPPLIER_NAME"></b-input>
			</b-form-group>

			<b-form-group>
				<label>Supplier Short Name</label>
				<b-input v-model = "modal.SUPPLIER_SHORT_NAME"></b-input>
			</b-form-group>

			<b-form-group>
				<label>Supplier Address</label>
				<b-textarea v-model = "modal.SUPPLIER_ADDRESS"></b-textarea>
			</b-form-group>

			<b-form-group>
				<label>Supplier PIN</label>
				<b-input v-model = "modal.PIN"></b-input>
			</b-form-group>
			
		</b-modal>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../../core/Form'
	import VueTables from 'vue-tables-2'
	const Event = VueTables.Event

	export default {
		data(){
			return {
				baseUrl: window.Laravel.baseUrl,
				searchTerm: "",
				suppliers: [],
				columns: ['#', 'SUPPLIER_NAME', 'SUPPLIER_ADDRESS', 'PIN', 'Actions'],
				options: {
					filterable: false,
					perPage: 50,
					perPageValues: [],
					customFilters: ['vatSearch'],
					sortable: ['SUPPLIER_NAME', 'SUPPLIER_ADDRESS', 'PIN'],
					sortIcon: {
						base: 'fe',
						up: 'fe-arrow-up',
						down: 'fe-arrow-down',
						is: 'fe-minus'
					},
					requestFunction: (data) => {
						return axios.get(`${this.baseUrl}/api/data/suppliers/all`, {
							params: data
						})
						.catch(function (e) {
							console.log('error', e);
						}.bind(this));
					}
				},
				modal: new Form({
					ID: "",
					'SUPPLIER_NAME': '',
					'SUPPLIER_SHORT_NAME': '',
					'SUPPLIER_ADDRESS': '',
					'PIN': ''
				}),
				modalLoader: {
					isLoading: false,
					error: false,
					validationErrors: [],
					errorMessage: ""
				}
			}
		},
		created(){
			// this.getSuppliers()
		},
		methods: {
			getSuppliers(){
				axios.get('/api/data/suppliers/all')
				.then((res) => {
					this.suppliers = res.data
				})
				.catch((error) => {
					console.log(error)
					// this.$toastr.error('There was an error fetching suppliers');
				});
			},
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::vatSearch', term);
			},
			editSupplier(data){
				console.log(data)
				this.modal.ID = data.ID
				this.modal.SUPPLIER_NAME = data.SUPPLIER_NAME
				this.modal.SUPPLIER_SHORT_NAME = data.SUPPLIER_SHORT_NAME
				this.modal.SUPPLIER_ADDRESS = data.SUPPLIER_ADDRESS
				this.modal.PIN = data.PIN

				this.$bvModal.show('manage-supplier')
			},
			submitModal(event){
				event.preventDefault()
				this.modalLoader.isLoading = true
				if(this.modal.ID == ""){
					this.modal.post('data/suppliers')
					.then(res => {
						this.modalLoader.isLoading = false
						this.$swal('Success!', "Successfully added supplier", "success")
						this.$bvModal.hide('manage-supplier')
					})
					.catch(error => {
						this.modalLoader.isLoading = false
						this.modalLoader.error = true
						this.modalLoader.errorMessage = error.message
						if(error.errors){
							this.modalLoader.validationErrors = error.errors
						}
						this.$swal({
							title: 'ERROR', 
							text: `Sorry there was an error while performing this request<br/>${error.message}`, 
							icon: "error"
						})
					});
				}else{
					this.modal.put('data/suppliers')
					.then(res => {
						this.modalLoader.isLoading = false
						this.$swal('Success!', "Successfully updated supplier", "success")
						this.$bvModal.hide('manage-supplier')
					})
					.catch(error => {
						this.modalLoader.isLoading = false
						this.modalLoader.error = true
						this.modalLoader.errorMessage = error.message
						if(error.errors){
							this.modalLoader.validationErrors = error.errors
						}
						this.$swal({
							title: 'ERROR', 
							text: `Sorry there was an error while performing this request<br/>${error.message}`, 
							icon: "error"
						})
					});
				}
			},
			resetModal(){
				this.modal.ID = ''
				this.modal.SUPPLIER_NAME = ''
				this.modal.SUPPLIER_ADDRESS = ''
				this.modal.SUPPLIER_SHORT_NAME = ''
				this.modal.PIN = ''

				this.modalLoader.isLoading = false
				this.modalLoader.error = false
				this.modalLoader.validationErrors = []
				this.modalLoader.errorMessage = ""
			}
		}
	}
</script>
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
				<b-button v-b-modal.manage-vehicle-model variant="primary" size="sm">Add Vehicle Model</b-button>
			</div>
		</div>
		<v-server-table :columns="columns" :data="vehicleModels" :options="options" class="table-sm">
			<template slot="#" slot-scope="props">
				{{ props.index }}
			</template>

			<template slot="Actions" slot-scope="props">
				<b-button size="sm" @click="editModel(props.row)">Edit</b-button>
			</template>
		</v-server-table>

		<b-modal id="manage-vehicle-model" title="Add Vehicle Make & Model" @ok="submitModal" @hidden="resetModal">
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
				<label>Make Model</label>
				<b-input v-model = "modal.MAKE_MODEL"></b-input>
			</b-form-group>			
		</b-modal>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../../core/Form'
	import VueTables from 'vue-tables-2'
	const Event = VueTables.Event

	export default {
		name: 'VehicleModels',
		data(){
			return{
				columns: ["#", "MAKE_MODEL", "Actions"],
				baseUrl: window.Laravel.baseUrl,
				searchTerm: "",
				vehicleModels: [],
				options: {
					filterable: false,
					perPage: 100,
					perPageValues: [],
					customFilters: ['modelSearch'],
					sortable: ['MAKE_MODEL'],
					sortIcon: {
						base: 'fe',
						up: 'fe-arrow-up',
						down: 'fe-arrow-down',
						is: 'fe-minus'
					},
					requestFunction: (data) => {
						return axios.get(`${this.baseUrl}/api/data/vehicles/models`, {
							params: data
						})
						.catch(function (e) {
							console.log('error', e);
						}.bind(this));
					}
				},
				modal: new Form({
					ID: "",
					'MAKE_MODEL': '',
				}),
				modalLoader: {
					isLoading: false,
					error: false,
					validationErrors: [],
					errorMessage: ""
				}
			}
		},
		methods: {
			editModel: function(data){
				this.modal.ID = data.MAKE_MODEL_ID
				this.modal.MAKE_MODEL = data.MAKE_MODEL
				this.$bvModal.show('manage-vehicle-model')
			},
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::modelSearch', term);
			},
			submitModal(event){
				event.preventDefault()
				this.modalLoader.isLoading = true
				if(this.modal.ID == ""){
					this.modal.post('data/vehicles/models')
					.then(res => {
						this.modalLoader.isLoading = false
						this.$swal('Success!', "Successfully added vehicle model", "success")
						this.$bvModal.hide('manage-vehicle-model')
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
					this.modal.put('data/vehicles/models')
					.then(res => {
						this.modalLoader.isLoading = false
						this.$swal('Success!', "Successfully updated vehicle model", "success")
						this.$bvModal.hide('manage-vehicle-model')
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
				this.modal.MAKE_MODEL = ''

				this.modalLoader.isLoading = false
				this.modalLoader.error = false
				this.modalLoader.validationErrors = []
				this.modalLoader.errorMessage = ""
			}
		}
	}
</script>
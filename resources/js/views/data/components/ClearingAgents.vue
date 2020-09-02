<template>
	<div>
		<!-- <b-input-group>
			<b-form-input></b-form-input>
		</b-input-group> -->
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
				<b-button v-b-modal.manage-agent class= "float-right" variant="outline-primary" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Add Clearing Agent</b-button>
			</div>
		</div>
		<v-server-table :columns="columns" :options="options" class="table-sm">
			<template slot="#" slot-scope="props">
				{{ props.index }}
			</template>
			<template slot="Actions" slot-scope="props">
				<b-button size="sm" @click="editAgent(props.row)">Edit</b-button>
			</template>
		</v-server-table>

		<b-modal id="manage-agent" title="Add Clearing Agent" @ok="submitModal" @hidden="resetModal">
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
				<label>Clearing Agent Name</label>
				<b-input v-model = "modal.CLEARING_AGENT_NAME"></b-input>
			</b-form-group>

			<b-form-group>
				<label>Clearing Agent Address</label>
				<b-textarea v-model = "modal.CLEARING_AGENT_ADDRESS"></b-textarea>
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
				clearingAgents: [],
				baseUrl: window.Laravel.baseUrl,
				searchTerm: "",
				options: {
					filterable: false,
					perPage: 50,
					perPageValues: [],
					customFilters: ['normalSearch'],
					sortable: ['CLEARING_AGENT_NAME', 'CLEARING_AGENT_ADDRESS'],
					sortIcon: {
						base: 'fe',
						up: 'fe-arrow-up',
						down: 'fe-arrow-down',
						is: 'fe-minus'
					},
					requestFunction: (data) => {
						return axios.get(`${this.baseUrl}/api/data/clearing-agents/all`, {
							params: data
						})
						.catch(function (e) {
							console.log('error', e);
						}.bind(this));
					}
				},
				columns: [
					'#',
					"CLEARING_AGENT_NAME",
					"CLEARING_AGENT_ADDRESS",
					"Actions"
				],
				modalLoader: {
					isLoading: false,
					error: false,
					validationErrors: [],
					errorMessage: ""
				},
				modal: new Form({
					ID: "",
					CLEARING_AGENT_NAME: "",
					CLEARING_AGENT_ADDRESS: ""
				})
			}
		},
		created(){
			// this.getClearingAgents()
		},
		methods: {
			applySearchFilter: function(term){
				Event.$emit('vue-tables.filter::normalSearch', term);
			},
			editAgent: function(data){
				this.modal.ID = data.ID
				this.modal.CLEARING_AGENT_NAME = data.CLEARING_AGENT_NAME
				this.modal.CLEARING_AGENT_ADDRESS = data.CLEARING_AGENT_ADDRESS

				this.$bvModal.show('manage-agent')
			},
			getClearingAgents: function(){
				axios.get('/api/data/clearing-agents/all')
				.then((res) => {
					this.clearingAgents = res.data
				})
				.catch((error) => {
					console.log(error)
					// this.$toastr.error('There was an error fetching suppliers');
				});
			},
			resetModal(){
				this.modal.ID = ''
				this.modal.CLEARING_AGENT_NAME = ''
				this.modal.CLEARING_AGENT_ADDRESS = ''
				
				this.modalLoader.isLoading = false
				this.modalLoader.error = false
				this.modalLoader.validationErrors = []
				this.modalLoader.errorMessage = ""
			},
			submitModal(event){
				event.preventDefault()
				this.modalLoader.isLoading = true

				if(this.modal.ID == ""){
					this.modal.post('data/clearing-agents')
					.then(res => {
						this.modalLoader.isLoading = false
						this.$swal('Success!', "Successfully added clearing agent", "success")
						this.$bvModal.hide('manage-agent')
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
							text: `${error.message}`, 
							icon: "error"
						})
					});
				}else{
					this.modal.put('data/clearing-agents')
					.then(res => {
						this.modalLoader.isLoading = false
						this.$swal('Success!', "Successfully updated clearing agent", "success")
						this.$bvModal.hide('manage-agent')
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
							text: `${error.message}`, 
							icon: "error"
						})
					});
				}
			}
		}
	}
</script>
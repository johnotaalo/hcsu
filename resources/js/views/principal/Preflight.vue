<template>
	<div>
		<div class="row justify-content-center">
			<div class="col-12 col-md-9 col-xl-8 my-5">
				<div class="card" v-if="!loading && !showResultSection">
					<div class="card-body">
						<h3>Search Staff</h3>
						<div class="row">
							<div class="col-md">
								<b-form-group
								label="Last Name">
									<b-input v-model="form.lastName" size="sm"></b-input>
								</b-form-group>
							</div>
							<div class="col-md">
								<b-form-group
								label="Other Names">
									<b-input v-model="form.otherNames" size="sm"></b-input>
								</b-form-group>
							</div>
						</div>
						<div class="row">
							<div class="col-md">
								<b-form-group
								label="Passport No">
									<b-input v-model="form.passportNo" size="sm"></b-input>
								</b-form-group>
							</div>
							<div class="col-md">
								<b-form-group
								label="Index No">
									<b-input v-model="form.indexNo" size="sm"></b-input>
								</b-form-group>
							</div>
						</div>

						<div class="row">
							<div class="col-md">
								<b-button class="btn btn-sm btn-success" @click="search"><i class="fe fe-search"></i>&nbsp;Search</b-button>
							</div>

							<div class="col-md">
								<b-button class="btn btn-sm btn-danger float-right" @click="clearSearch"><i class="fe fe-x"></i>&nbsp;Clear</b-button>
							</div>
						</div>
					</div>
				</div>
				<div id="result" v-else-if="!loading && showResultSection">
					<center><h3>Results</h3></center>

					<div class="row">
						<div class="col-md">
							<a class="btn btn-link" @click="back">Back</a>
						</div>
						<div class="col-md">
							<span class="float-right">({{ principals.length }}) results</span>
						</div>
					</div>
					
					<div v-if="principals.length > 0">
						<div class="card mb-3" v-for="(principal, key) in principals">
							<div class="card-body">
								<div class="row align-items-center">
									<div class="col-auto">
										<a href="#" class="avatar avatar-lg">
											<img :src="`/photos/principal/${principal.HOST_COUNTRY_ID}`" :alt="principal.fullname" class="avatar-img rounded-circle">
										</a>
									</div>

									<div class="col ml-n2">
										<h4 class="mb-1">
											<a :href="`/principal/view/${principal.HOST_COUNTRY_ID}`">{{ principal.fullname }}</a>
										</h4>
										<p class="card-text small text-muted mb-1">
											<span v-if="principal.latest_passport">Passport No: {{ principal.latest_passport.PASSPORT_NO }};</span><span v-else>No Passport Information Found</span><br/>
											<span>Index No: {{ principal.latest_contract.INDEX_NO }} {{ principal.latest_contract.ACRONYM }}</span>
										</p>
										<p class="card-text small">
											<span v-if="principal.STATUS == 1"><span class="text-success">●</span> Active</span>
											<span v-if="principal.STATUS == 0"><span class="text-danger">●</span> Inactive</span>
										</p>
									</div>

									<div class="col-auto">
										<b-button class="btn btn-sm btn-success d-none d-md-inline-block" @click="useClient(principal)">Use Client</b-button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div v-else>
						<center><p>No results to be displayed</p></center>
					</div>
					<center><router-link class="btn btn-link" :to="{ name: 'principal.add' }">Can't find Client. Proceed to add a new entry</router-link></center>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../core/Form'
	export default {
		data(){
			return {
				loading: false,
				showResultSection: false,
				principals: [],
				form: new Form({
					lastName: "",
					otherNames: "",
					passportNo: "",
					indexNo: ""
				})
			}
		},
		mounted(){
			this.$parent.isContainer = true
		},
		methods: {
			search: function(){
				this.loading = true
				this.form.post('principal/preflight/search')
				.then(res => {
					this.loading = false
					this.showResultSection = true
					this.principals = res
				})
				.catch(error => {
					this.loading = false
					this.showResultSection = false
					this.$swal('Error', error.message, 'error')
				});
			},
			back(){
				this.loading = false
				this.showResultSection = false
			},
			clearSearch(){
				this.back()
				this.form.lastName = ""
				this.form.otherNames = ""
				this.form.passportNo = ""
				this.form.indexNo = ""
			},
			useClient(principal){
				var host_country_id = principal.HOST_COUNTRY_ID

				if (principal.STATUS) {
					this.confirmation(host_country_id)
				}else{
					this.$swal({
						title: "Confirmation",
						text: `You are going to active ${principal.LAST_NAME}, ${principal.OTHER_NAMES}. Are you sure?`,
						icon: 'warning',
						buttons: true,
						dangerMode: true
					})
					.then((willActivate) => {
						if (willActivate) {
							axios.put(`api/principal/${principal.ID}/activate`, { "_METHOD": "PUT", id: principal.ID })
							.then(res => {
								this.$swal("Success", "Successfully activated client", "success")
								.then(() => {
									this.$router.push({ name: 'principal.view', params: {id: host_country_id} })
								})
							})
							.catch((error) => {
								this.$swal("Error", "There was an error activating the client. Please contact the administrator", "error")
							})
						}
					})
				}
			},
			confirmation(host_country_id){
				this.$swal({
					title: "Continue?",
					text:  "This will take you to the client's page where you can edit information",
					icon: "info",
					buttons: true,
					dangerMode: true
				})
				.then((willContinue) => {
					if (willContinue) {
						this.$router.push({ name: 'principal.view', params: {id: host_country_id} })
					}
				})
			}
		}
	}
</script>
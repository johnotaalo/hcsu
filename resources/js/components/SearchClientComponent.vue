<template>
	<div>
		<!-- <b-form-group label="Type of Client">
			<b-form-radio-group
				id="type-of-client"
				v-model="value.clientType"
				:options="clientTypes"
				name="client-types-option"
			></b-form-radio-group>
		</b-form-group>
		<div v-if="value.clientType == ''" >
			Please select a client type above
		</div>
		<div v-else>
			<div v-if="value.clientType == 'agency'">
				<div v-if="applier != 'fp'">
					<label class="control-label">Please type to search for an agency</label>
					<v-select label="AGENCYNAME" :filterable="false" :options="agencies" @search="onAgencySearch" v-model="value.client">
						<template slot="no-options">
							Type to search for an Agency
						</template>

						<template slot="option" slot-scope="option">
							<img style="width: 50px;height: 50px;" /> {{ option.AGENCYNAME }} [{{ option.ACRONYM }}]
						</template>

						<template slot="selected-option" slot-scope="option">
							<span v-if="option.AGENCYNAME"><img style="width: 50px;height: 50px;" /> {{ option.AGENCYNAME }} [{{ option.ACRONYM }}]</span>
						</template>
					</v-select>
				</div>
				<div v-else>
					<p>VAT Application for: {{ agency.AGENCYNAME }}</p>
				</div>
			</div>
			<div v-if="value.clientType == 'staff-member'">
				<label class="control-label">Please type to search for a staff member</label>
				<v-select label = "LAST_NAME" :filterable="false" :options="staffMembers" @search="onStaffSearch" v-model="value.client">
					<template slot="no-options">
						Type to search for a staff member
					</template>

					<template slot="option" slot-scope="option">
						<img style="width: 50px;height: 50px;" v-if="option.image_link != '' && option.image_link != '/storage/'" :src="option.image_link"/><img v-else style="width: 50px;height: 50px;" :src="no_avatar_img"/>&nbsp;{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}
					</template>

					<template slot="selected-option" slot-scope="option">
						<span v-if="option.LAST_NAME">
							<img style="width: 50px;height: 50px;" v-if="option.image_link != '' && option.image_link != '/storage/'" :src="option.image_link"/><img v-else style="width: 50px;height: 50px;" :src="no_avatar_img"/>&nbsp;{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}
						</span>
					</template>
				</v-select>
			</div>
		</div> -->
		<b-form-group label="Type of Client">
			<b-form-radio-group
				id="type-of-client"
				v-model="value.clientType"
				:options="clientTypes"
				name="client-types-option"
			></b-form-radio-group>
		</b-form-group>

		<div v-if="value.clientType == ''" >
			Please select a client type above
		</div>
		<div v-else>
			<div v-if="value.clientType == 'agency'">
				<div v-if="applier != 'fp'">
					<label class="control-label">Please type to search for an agency</label>
					<v-select label="AGENCYNAME" :filterable="false" :options="agencies" @search="onAgencySearch" v-model="value.client">
						<template slot="no-options">
							Type to search for an Agency
						</template>

						<template slot="option" slot-scope="option">
							<img style="width: 50px;height: 50px;" /> {{ option.AGENCYNAME }} [{{ option.ACRONYM }}]
						</template>

						<template slot="selected-option" slot-scope="option">
							<span v-if="option.AGENCYNAME"><img style="width: 50px;height: 50px;" /> {{ option.AGENCYNAME }} [{{ option.ACRONYM }}]</span>
						</template>
					</v-select>
				</div>
				<div v-else>
					<p>Making Application for: {{ agency.AGENCYNAME }}</p>
				</div>
			</div>
			<div v-if="value.clientType == 'staff-member'">
				<label class="control-label">Please type to search for a staff member</label>
				<v-select label = "LAST_NAME" :filterable="false" :options="staffMembers" @search="onStaffSearch" v-model="value.client">
					<template slot="no-options">
						Type to search for a staff member
					</template>

					<template slot="option" slot-scope="option">
						<div class = "row align-items-center">
							<div class="col-auto">
								<img class = "rounded-circle m1" style="width: 70px;height: 70px;" v-if="option.image_link != '' && option.image_link != '/storage/'" :src="`/photos/principal/${option.HOST_COUNTRY_ID}`"/><img v-else style="width: 70px;height: 70px;" :src="no_avatar_img"/>
							</div>
							<div class="col ml-n2">
								<h4 class = "card-title mb-1">{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}</h4>
							</div>
						</div>
					</template>

					<template slot="selected-option" slot-scope="option">
						<span v-if="option.LAST_NAME">
							<div class = "row align-items-center">
							<div class="col-auto">
								<img class = "rounded-circle m1" style="width: 70px;height: 70px;" v-if="option.image_link != '' && option.image_link != '/storage/'" :src="`/photos/principal/${option.HOST_COUNTRY_ID}`"/><img v-else style="width: 70px;height: 70px;" :src="no_avatar_img"/>
							</div>
							<div class="col ml-n2">
								<h4 class = "card-title mb-1">{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}</h4>
								<span class="card-text">
									<span>Organization (<span v-if="option.latest_contract">{{ option.latest_contract.ACRONYM }}</span><span v-else>N/A</span>)</span>, 
									<span>Latest Passport (<span v-if="option.latest_passport">{{ option.latest_passport.PASSPORT_NO }}</span><span v-else>N/A</span>)</span>, 
									<span>Latest DIP ID (<span v-if="option.latest_diplomatic_card">{{ option.latest_diplomatic_card.DIP_ID_NO }}</span><span v-else>N/A</span>)</span>
								</span>
							</div>
						</div>
						</span>
					</template>
				</v-select>
				<div v-if="agency" class="text-danger">This will only show clients from your agency</div>
			</div>

			<div v-if="value.clientType == 'dependent'">
				<label class="control-label">Please type to search for a dependent</label>
				<v-select label = "LAST_NAME" :filterable="false" :options="dependents" @search="onDependentSearch" v-model="value.client">
					<template slot="no-options">
						Type to search for a dependent
					</template>

					<template slot="option" slot-scope="option">
						<div class = "row align-items-center">
							<div class = "col-auto">
								<img style="width: 50px;height: 50px;" v-if="option.image_link != '' && option.image_link != '/storage/'" :src="option.image_link"/><img v-else style="width: 50px;height: 50px;" :src="no_avatar_img"/>
							</div>
							<div class="col ml-n2">
								<h4 class = "card-title mb-1">{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}</h4>
								<p class="card-text text-muted">{{ option.relationship_x.RELATIONSHIP }} of {{ option.principal.LAST_NAME }}, {{ option.principal.OTHER_NAMES }} ({{ option.principal.latest_contract.ACRONYM }})</p>
							</div>
						</div>
					</template>

					<template slot="selected-option" slot-scope="option">
						<span v-if="option.LAST_NAME">
							<div class = "row align-items-center">
								<div class = "col-auto">
									<img style="width: 50px;height: 50px;" v-if="option.image_link != '' && option.image_link != '/storage/'" :src="option.image_link"/><img v-else style="width: 50px;height: 50px;" :src="no_avatar_img"/>
								</div>
								<div class="col ml-n2">
									<h4 class = "card-title mb-1">{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}</h4>
									<p class="card-text text-muted">{{ option.relationship_x.RELATIONSHIP }} of {{ option.principal.LAST_NAME }}, {{ option.principal.OTHER_NAMES }} ({{ option.principal.latest_contract.ACRONYM }})</p>
								</div>
							</div>
						</span>
					</template>
				</v-select>
			</div>

			<div v-if="value.clientType == 'domestic-worker'">
				<label class="control-label">Please type to search for a Domestic Worker</label>
				<v-select label = "LAST_NAME" :filterable="false" :options="domesticWorkers" @search="onDomesticWorkerSearch" v-model="value.client">
					<template slot="no-options">
						Type to search for a Domestic Worker
					</template>

					<template slot="option" slot-scope="option">
						<div class = "row align-items-center">
							<div class = "col-auto">
								<img style="width: 50px;height: 50px;" :src="no_avatar_img"/>
							</div>
							<div class="col ml-n2">
								<h4 class = "card-title mb-1">{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}</h4>
								<p class="card-text text-muted">Domestic Worker of {{ option.principal.LAST_NAME }}, {{ option.principal.OTHER_NAMES }} ({{ option.principal.latest_contract.ACRONYM }})</p>
							</div>
						</div>
					</template>

					<template slot="selected-option" slot-scope="option">
						<span v-if="option.LAST_NAME">
							<div class = "row align-items-center">
								<div class = "col-auto">
									<img style="width: 50px;height: 50px;" :src="no_avatar_img"/>
								</div>
								<div class="col ml-n2">
									<h4 class = "card-title mb-1">{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}</h4>
									<p class="card-text text-muted">Domestic Worker of {{ option.principal.LAST_NAME }}, {{ option.principal.OTHER_NAMES }} ({{ option.principal.latest_contract.ACRONYM }})</p>
								</div>
							</div>
						</span>
					</template>
				</v-select>
			</div>

			<div v-if="value.clientType == 'other-clients'">
				<label class="control-label">Please type to search for client</label>
				<v-select label = "LAST_NAME" :filterable="false" :options="otherClients" @search="onOtherClientsSearch" v-model="value.client">
					<template slot="no-options">
						Type to search for a Client
					</template>

					<template slot="selected-option" slot-scope="option">
						<span v-if="option.LAST_NAME">
							<div class = "row align-items-center">
								<div class = "col-auto">
									<img style="width: 50px;height: 50px;" :src="no_avatar_img"/>
								</div>
								<div class="col ml-n2">
									<h4 class = "card-title mb-1">{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}</h4>
									<p class="card-text text-muted">Passport No: {{ option.PASSPORT_NO }}, Affiliated with {{ option.agency.ACRONYM }}, Nationality: {{ option.nationality.name }}</p>
								</div>
							</div>
						</span>
					</template>

					<template slot="option" slot-scope="option">
						<span v-if="option.LAST_NAME">
							<div class = "row align-items-center">
								<div class = "col-auto">
									<img style="width: 50px;height: 50px;" :src="no_avatar_img"/>
								</div>
								<div class="col ml-n2">
									<h4 class = "card-title mb-1">{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}</h4>
									<p class="card-text">Passport No: {{ option.PASSPORT_NO }}, Affiliated with {{ option.agency.ACRONYM }}, Nationality: {{ option.nationality.name }}</p>
								</div>
							</div>
						</span>
					</template>
				</v-select>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	import vSelect from 'vue-select'
	import _ from 'lodash'
	export default {
		components: { 'v-select': vSelect },
		props: {
			'value': { type: null, default: null },
			applier: { type: String, default: 'fp' },
			type: { type: String, default: '' },
			// organization: { type: String, default: '' }
		},
		mounted() {
			// this.clientType = this.type
			// console.log("Client", this.client)
		},
		created(){
		},
		data(){
			return {
				// clientType: "",
				clientTypes: [
					{ text: "Agency", value: 'agency' },
					{ text: "Staff Member", value: 'staff-member' },
					{ text: "Dependent", value: 'dependent' },
					{ text: "Domestic Worker", value: 'domestic-worker' },
					{ text: "Other Clients", value: 'other-clients' }
				],
				agencies: [],
				staffMembers: [],
				dependents: [],
				domesticWorkers: [],
				otherClients: [],
				selectedAgency: {},
				selectedStaff: {},
				selectedDependent: {},
				selectedDomesticWorker: {},
				selectedOtherClient: {},
				no_avatar_img: "/images/no_avatar.svg",
				host_country_id: "",
				organization: ""
			}
		},
		methods: {
			onAgencySearch(search, loading){
				loading(true)
				this.search(loading, search, this)
			},
			onStaffSearch(search, loading){
				loading(true)
				this.staffSearch(loading, search, this)
			},
			onDependentSearch(search, loading){
				loading(true)
				this.dependentSearch(loading, search, this)
			},
			onDomesticWorkerSearch(search, loading){
				loading(true)
				this.domesticWorkerSearch(loading, search, this)
			},
			onOtherClientsSearch(search, loading){
				loading(true)
				this.otherclientsSearch(loading, search, this)
			},
			getSelectedClient(host_country_id){
				// this.isLoading = true;
				alert(host_country_id)
			},
			search: _.debounce( (loading, search, vm) => {
				axios(`/api/agencies/search?q=${escape(search)}`)
				.then((res) => {
					vm.agencies = res.data
					loading(false)
				})
			}, 350),
			staffSearch: _.debounce( (loading, search, vm) => {
				// if (vm.applier = "fp") {
				// 	vm.organization = vm.$store.state.loggedInUser.focal_point.agency.ACRONYM;
				// }
				// console.log(vm.organization)
				var organizationString = (vm.agency) ? `&organization=${escape(vm.agency.ACRONYM)}` : "";

				axios(`/api/principal/search?q=${escape(search)}${organizationString}`)
				.then((res) => {
					vm.staffMembers = res.data
					loading(false)
				})
			}, 350),
			dependentSearch: _.debounce( (loading, search, vm) => {
				var organizationString = (vm.agency) ? `&organization=${escape(vm.agency.ACRONYM)}` : "";
				axios(`/api/dependent/search?q=${escape(search)}${organizationString}`)
				.then( (res) => {
					vm.dependents = res.data
					loading(false)
				} )
			}, 350 ),
			domesticWorkerSearch: _.debounce((loading, search, vm) => {
				axios(`api/principal/domesticworker/search?q=${escape(search)}`)
				.then(res=>{
					vm.domesticWorkers = res.data
					loading(false)
				})
			}, 200),
			otherclientsSearch: _.debounce((loading, search, vm) => {
				axios(`api/other/clients/search?q=${escape(search)}`)
				.then(res=>{
					vm.otherClients = res.data
					loading(false)
				})
			}, 200),
		},
		computed: {
			agency: function(){
				if(this.$store.state.loggedInUser.focal_point){
					return this.$store.state.loggedInUser.focal_point.agency
				}

				return null;
			}
		},
		watch: {
			// selectedAgency: function(newVal, oldVal){
			// 	if(newVal){
			// 		this.host_country_id = newVal.HOST_COUNTRY_ID
			// 	}else{
			// 		this.host_country_id = 0
			// 	}
			// 	// this.selectedStaff = {}
			// },
			// selectedStaff: function(newVal, oldVal){
			// 	if(newVal){
			// 		this.host_country_id = newVal.HOST_COUNTRY_ID
			// 	}else{
			// 		this.host_country_id = 0
			// 	}
			// 	// this.selectedAgency= {}
			// },
			selectedAgency: function(newVal, oldVal){
				var em = this
				if(newVal.HOST_COUNTRY_ID){
					if(this.application == 'pin'){
						if (newVal.PIN_NO != null && newVal.PIN_NO != "null") {
							this.$swal({
								title: "PIN Exists", 
								text: `This Organization already has a PIN (${newVal.PIN_NO}). If you proceed, the organization's PIN shall be cleared from the system. Proceed?`, 
								icon: "warning",
								buttons: true,
								dangerMode: true
							})
							.then((proceed) => {
								if (proceed) {
									// console.log(newVal)
									em.host_country_id = newVal.HOST_COUNTRY_ID
								}else{
									// alert("Clicked on cancel");
									em.host_country_id = 0
									em.selectedAgency = {}
								}
							})
						}else{
							this.host_country_id = newVal.HOST_COUNTRY_ID
						}
					}else{
						this.host_country_id = newVal.HOST_COUNTRY_ID
					}
				}else{
					this.host_country_id = 0
				}
				// this.selectedStaff = {}
			},
			selectedStaff: function(newVal, oldVal){
				var em = this
				if(newVal.HOST_COUNTRY_ID){
					if(this.application == 'pin'){
						if (newVal.PIN_NO != null && newVal.PIN_NO != "null" && newVal.PIN_NO != "NULL") {
							this.$swal({
								title: "PIN Exists", 
								text: `The staff member already has a PIN (${newVal.PIN_NO}). If you proceed, the staff member's PIN shall be cleared from the system. Proceed?`, 
								icon: "warning",
								buttons: true,
								dangerMode: true
							})
							.then((proceed) => {
								if (proceed) {
									// console.log(newVal)
									em.host_country_id = newVal.HOST_COUNTRY_ID
								}else{
									// alert("Clicked on cancel");
									em.host_country_id = 0
									em.selectedStaff = {}
								}
							})
						}else{
							this.host_country_id = newVal.HOST_COUNTRY_ID
						}
					}else{
						this.host_country_id = newVal.HOST_COUNTRY_ID
					}
				}else{
					this.host_country_id = 0
				}
			},
			selectedDependent: function(newVal, oldVal){
				var em = this
				if(newVal.HOST_COUNTRY_ID){
					if(this.application == 'pin'){
						if (newVal.PIN != null && newVal.PIN != "null") {
							this.$swal({
								title: "PIN Exists", 
								text: `The client already has a PIN (${newVal.PIN}). If you proceed, the client's PIN shall be cleared from the system. Proceed?`, 
								icon: "warning",
								buttons: true,
								dangerMode: true
							})
							.then((proceed) => {
								if (proceed) {
									// console.log(newVal)
									em.host_country_id = newVal.HOST_COUNTRY_ID
								}else{
									// alert("Clicked on cancel");
									em.host_country_id = 0
									em.selectedDependent = {}
								}
							})
						}else{
							this.host_country_id = newVal.HOST_COUNTRY_ID
						}
					}else{
						this.host_country_id = newVal.HOST_COUNTRY_ID
					}
				}else{
					this.host_country_id = 0
				}
			},
			selectedDomesticWorker: function(newVal, oldVal){
				var em = this
				if(newVal.HOST_COUNTRY_ID){
					this.host_country_id = newVal.HOST_COUNTRY_ID
				}else{
					this.host_country_id = 0
				}
			},
			selectedOtherClient: function(newVal, oldVal) {
				var em = this
				if (newVal.HOST_COUNTRY_ID) {
					this.host_country_id = newVal.HOST_COUNTRY_ID
				}else{
					this.host_country_id = 0
				}
			},
			'value.clientType': function(newVal){
				this.value.client = {}
				if (newVal == "agency") {
					this.value.client = this.agency
				}
			}
		}
	}
</script>
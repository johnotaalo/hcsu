<template>
	<div>
		<loading
		:active.sync="isLoading"
        :can-cancel="false"
        :is-full-page="fullPage"></loading>
		<b-card>
			<p v-if="$store.state.isUserRetrieved"><i>Logged in as: {{ $store.state.loggedInUser.USR_FIRSTNAME }}, {{ $store.state.loggedInUser.USR_LASTNAME }} ({{ $store.state.loggedInUser.USR_USERNAME }})</i></p>
			<b-form-group label="Type of Client">
				<b-form-radio-group
					id="type-of-client"
					v-model="clientType"
					:options="clientTypes"
					name="client-types-option"
				></b-form-radio-group>
			</b-form-group>

			<div v-if="clientType == ''" >
				Please select a client type above
			</div>
			<div v-else>
				<div v-if="clientType == 'agency'">
					<label class="control-label">Please type to search for an agency</label>
					<v-select label="AGENCYNAME" :filterable="false" :options="agencies" @search="onAgencySearch" v-model="selectedAgency">
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
				<div v-if="clientType == 'staff-member'">
					<label class="control-label">Please type to search for a staff member</label>
					<v-select label = "LAST_NAME" :filterable="false" :options="staffMembers" @search="onStaffSearch" v-model="selectedStaff">
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
				</div>

				<div v-if="clientType == 'dependent'">
					<label class="control-label">Please type to search for a dependent</label>
					<v-select label = "LAST_NAME" :filterable="false" :options="dependents" @search="onDependentSearch" v-model="selectedDependent">
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

				<div v-if="clientType == 'domestic-worker'">
					<label class="control-label">Please type to search for a Domestic Worker</label>
					<v-select label = "LAST_NAME" :filterable="false" :options="domesticWorkers" @search="onDomesticWorkerSearch" v-model="selectedDomesticWorker">
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
			</div>

			<div v-if="host_country_id != 0" class="mt-5">
				<center><button class="btn btn-primary" v-on:click="proceedClient" id = "proceed-button">Proceed with selected client</button></center>

				<div v-if="clientType == 'dependent' || clientType == 'staff-member'">
					
				</div>
			</div>
		</b-card>
	</div>
</template>

<script type="text/javascript">

	export default {
		props: {
			case: { type: String, default: null, required: false },
		},
		data: function(){
			return {
				isLoading: false,
      			fullPage: true,
				clientType: '',
				searchType: '',
				application: '',
				clientTypeList: [
					{ text: "Agency", value: 'agency' },
					{ text: "Staff Member", value: 'staff-member' },
					{ text: "Dependent", value: 'dependent' },
					{ text: "Domestic Worker", value: 'domestic-worker' }
				],
				agencies: [],
				staffMembers: [],
				dependents: [],
				domesticWorkers: [],
				selectedAgency: {},
				selectedStaff: {},
				selectedDependent: {},
				selectedDomesticWorker: {},
				host_country_id: 0,
				finalisedHCSUID: 0,
				no_avatar_img: "/images/no_avatar.svg"
			}
		},
		mounted(){
			var query = this.$route.query

			var type = query.type
			var case_no = query.case_no
			var user = query.user
			var selectedClient = query.host_country_id;
			if (selectedClient) {
				getSelectedClient(selectedClient)
			}
			this.searchType = query.searchfor
			this.application = query.application
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
				axios(`/api/principal/search?q=${escape(search)}`)
				.then((res) => {
					vm.staffMembers = res.data
					loading(false)
				})
			}, 350),
			dependentSearch: _.debounce( (loading, search, vm) => {
				axios(`/api/dependent/search?q=${escape(search)}`)
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
			proceedClient: function(){
				this.isLoading = true;
				axios.post('/api/client/update', {
					case_no: this.$parent.case,
					host_country_id: this.host_country_id,
					application: this.application
				})
				.then( (res) => {
					this.isLoading = false
					this.$swal("Success!", "The client's details have been sent to processmaker!", "success")
				})
				.catch((error) => {
					this.isLoading = false
					this.$swal("Error", error.message, "error")
				})
			}
		},
		watch: {
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
						if (newVal.PIN_NO != null && newVal.PIN_NO != "null") {
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
			}
		},
		computed: {
			clientTypes: function(){
				var list = this.clientTypeList
				var arr = [];
				if (this.searchType == 'staff') {
					var result = _.find(list, (obj) => {
						return obj.value == 'staff-member'
					})
					this.clientType = 'staff-member'
					arr.push(result)
				}
				else if (this.searchType == 'agency') {
					var result = _.find(list, (obj) => {
						return obj.value == 'agency'
					})
					this.clientType = 'agency'
					arr.push(result)
				}
				else if (this.searchType == 'dependent') {
					var result = _.find(list, (obj) => {
						return obj.value == 'dependent'
					})
					this.clientType = 'dependent'
					arr.push(result)
				}
				else{

					if (this.application == 'diplomatic_id' || this.application == 'visa_extension') {
						if(this.application == 'diplomatic_id'){
							var result = _.filter(list, (obj) => {
								return obj.value == 'staff-member' || obj.value == 'dependent'
							})
						}else{
							var result = _.filter(list, (obj) => {
								return obj.value == 'staff-member' || obj.value == 'dependent' || obj.value == 'domestic-worker'
							})
						}

						arr = result
					}
					else{
						arr = list
					}
				}

				return arr				
			}
		}
	}
</script>

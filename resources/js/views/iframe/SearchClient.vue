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
							<img style="width: 50px;height: 50px;" v-if="option.image_link != '' && option.image_link != '/storage/'" :src="option.image_link"/><img v-else style="width: 50px;height: 50px;" :src="no_avatar_img"/>&nbsp;{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}
						</template>

						<template slot="selected-option" slot-scope="option">
							<span v-if="option.LAST_NAME">
								<img style="width: 50px;height: 50px;" v-if="option.image_link != '' && option.image_link != '/storage/'" :src="option.image_link"/><img v-else style="width: 50px;height: 50px;" :src="no_avatar_img"/>&nbsp;{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}
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
									<p class="card-text text-muted">{{ option.relationship_x.RELATIONSHIP }} of {{ option.principal.LAST_NAME }}, {{ option.principal.OTHER_NAMES }} ({{ option.principal.latest_contract.contract_agency.ACRONYM }})</p>
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
										<p class="card-text text-muted">{{ option.relationship_x.RELATIONSHIP }} of {{ option.principal.LAST_NAME }}, {{ option.principal.OTHER_NAMES }} ({{ option.principal.latest_contract.contract_agency.ACRONYM }})</p>
									</div>
								</div>
							</span>
						</template>
					</v-select>
				</div>
			</div>

			<div v-if="host_country_id != 0" class="mt-5">
				<center><button class="btn btn-primary" v-on:click="proceedClient" id = "proceed-button">Proceed with selected client</button></center>
			</div>
		</b-card>
	</div>
</template>

<script type="text/javascript">

	export default {
		props: {
			case: { type: String, default: null, required: false }
		},
		data: function(){
			return {
				isLoading: false,
      			fullPage: true,
				clientType: '',
				clientTypes: [
					{ text: "Agency", value: 'agency' },
					{ text: "Staff Member", value: 'staff-member' },
					{ text: "Dependent", value: 'dependent' }
				],
				agencies: [],
				staffMembers: [],
				dependents: [],
				selectedAgency: {},
				selectedStaff: {},
				selectedDependent: {},
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
			proceedClient: function(){
				this.isLoading = true;
				axios.post('/api/client/update', {
					case_no: this.$parent.case,
					host_country_id: this.host_country_id
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
				if(newVal){
					this.host_country_id = newVal.HOST_COUNTRY_ID
				}else{
					this.host_country_id = 0
				}
				// this.selectedStaff = {}
			},
			selectedStaff: function(newVal, oldVal){
				if(newVal){
					this.host_country_id = newVal.HOST_COUNTRY_ID
				}else{
					this.host_country_id = 0
				}
				// this.selectedAgency= {}
			},
			selectedDependent: function(newVal, oldVal){
				if(newVal){
					this.host_country_id = newVal.HOST_COUNTRY_ID
				}else{
					this.host_country_id = 0
				}
			}
		}
	}
</script>

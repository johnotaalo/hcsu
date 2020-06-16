<template>

	<div>
		<div class="header">
			<div class="header-body">
				<div class="row align-items-center">
					<div class="col">
						<!-- Pretitle -->
						<h6 class="header-pretitle">
							Tims Registration
						</h6>
						<!-- Title -->
						<h1 class="header-title">
							New TIMS Registration
						</h1>
					</div>
				</div> <!-- / .row -->
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<h3>New Tims Registration</h3>
			</div>
			<div class="card-body">
				<b-form-group label="Client Type">
					<b-form-radio-group
						id="type-of-client"
						v-model="clientType"
						:options="clientTypes"
						name="client-types-option"
					></b-form-radio-group>
				</b-form-group>
				<div v-if="clientType == ''"  class = "mb-5" >
					Please select a client type above
				</div>
				<div v-else  class = "mb-5" >
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
					<div v-else-if="clientType == 'dependant'">
						<label class="control-label">Please type to search for a dependant</label>
						<v-select label = "LAST_NAME" :filterable="false" :options="dependants" @search="onDependantSearch" v-model="selectedDependant">
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
				</div>
				<div v-if="form.host_country_id != 0">
					<div class="row">
						<div class="col-md">
							<b-form-group label="Registation Date">
								<datetime v-model= "form.registrationDate" input-class="form-control" :max-datetime="$moment().format()"/>
							</b-form-group>
						</div>

						<div class="col-md">
							<b-form-group label="Diplomatic ID Card No">
								<div v-if ="diplomaticSelect">
									<b-select v-model = "form.diplomatic_id" :options="diplomaticCards"/>
									<small> <b-link @click="toggleDiplomaticCardSelect">Can't see diplomatic card?</b-link> </small>
								</div>
								<div v-else>
									<b-input v-model = "form.diplomatic_id" placeholder="Type in Diplomatic Card No"/>
									<small> <b-link @click="toggleDiplomaticCardSelect">Show Diplomatic Card List</b-link> </small>
								</div>
							</b-form-group>
						</div>

						<div class="col-md">
							<b-form-group label="KRA PIN">
								<b-input v-model = "form.pin" />
							</b-form-group>
						</div>
					</div>
					<div class="row">
						<div class="col-md">
							<b-form-group label="Phone Number">
								<b-input v-model = "form.mobile_no"/>
							</b-form-group>
						</div>

						<div class="col-md">
							<b-form-group label="Username">
								<b-input v-model = "form.username"/>
							</b-form-group>
						</div>

						<div class="col-md">
							<b-form-group label="Password">
								<b-input v-model = "form.password"/>
							</b-form-group>
						</div>
					</div>

					<b-button variant="primary" size="sm" @click="savedata">Save Data</b-button>
				</div>
			</div>
		</div>
	</div>
	
</template>

<script type="text/javascript">
	import { Datetime } from 'vue-datetime'
	import Form from '../../../core/Form'
	export default{
		components: { Datetime },
		data(){
			return {
				clientType: "",
				clientTypes: [
					{ text: "Staff Member", value: 'staff-member' },
					{ text: "Dependant", value: 'dependant' },
				],
				no_avatar_img: "/images/no_avatar.svg",
				staffMembers: [],
				dependants: [],
				selectedStaff: {},
				selectedDependant: {},
				diplomaticSelect: true,
				form: new Form({
					registrationDate: this.$moment().format(),
					host_country_id: 0,
					client: {},
					diplomatic_id: null,
					pin: "",
					username: "",
					password: "",
					mobile_no: ""
				})
			}
		},
		methods: {
			onStaffSearch: function(search, loading){
				loading(true)
				this.staffSearch(loading, search, this)
			},
			onDependantSearch: function(search, loading){
				loading(true)
				this.dependentSearch(loading, search, this)
			},
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
					vm.dependants = res.data
					loading(false)
				} )
			}, 350 ),

			toggleDiplomaticCardSelect: function(){
				this.diplomaticSelect = !this.diplomaticSelect
			},

			savedata(){
				this.form.post('data/tims/registration')
				.then(res => {
					if(res.id){
						this.$swal("Success!", "Successfully tied client to TIMS Account", "success")
						this.$router.push({ name: 'tims-list'})
					}
				})
			}
		},
		watch: {
			selectedStaff: function(newVal, oldVal){
				if(newVal){
					if (newVal.HOST_COUNTRY_ID) {
						this.form.host_country_id = newVal.HOST_COUNTRY_ID
						this.form.pin = newVal.PIN_NO
					}else{
						this.form.host_country_id = 0
						this.form.pin = ""
					}
				}else{
					this.form.host_country_id = 0
					this.form.pin = ""
				}
			},
			selectedDependant: function(newVal, oldVal){
				if(newVal){
					if (newVal.HOST_COUNTRY_ID) {
						this.form.host_country_id = newVal.HOST_COUNTRY_ID
						this.form.pin = newVal.PIN
					}else{
						this.form.host_country_id = 0
						this.form.pin = ""
					}
				}else{
					this.form.host_country_id = 0
					this.form.pin = ""
				}
			}
		},
		computed: {
			diplomaticCards: function(){
				if (this.clientType == "staff-member") {
					if (this.selectedStaff) {
						if(this.selectedStaff){
							return _.map(this.selectedStaff.diplomatic_cards, (o) => {
								return {
									value: o.DIP_ID_NO,
									text: `${o.DIP_ID_NO} - Expiry (${o.EXPIRY_DATE})`
								}
							})
						}
					}
				}else if(this.clientType == "dependant"){
					if(this.selectedDependant){
						return _.map(this.selectedDependant.diplomatic_cards, (o) => {
							return {
								value: o.DIP_ID_NO,
								text: `${o.DIP_ID_NO} - Expiry (${o.EXPIRY_DATE})`
							}
						})
					}
				}

				return [];
			}
		}
	}
</script>
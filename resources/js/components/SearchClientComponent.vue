<template>
	<div>
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
			type: { type: String, default: '' }
		},
		mounted() {
			// this.clientType = this.type
			// console.log("Client", this.client)
		},
		data(){
			return {
				// clientType: "",
				clientTypes: [
					{ text: "Agency", value: 'agency' },
					{ text: "Staff Member", value: 'staff-member' }
				],
				agencies: [],
				staffMembers: [],
				no_avatar_img: "/images/no_avatar.svg",
				selectedAgency: {},
				selectedStaff: {},
				host_country_id: ""
			}
		},
		methods: {
			onAgencySearch: function(search, loading){
				loading(true)
				this.agencySearch(loading, search, this)
			},
			onStaffSearch: function(search, loading){
				loading(true)
				this.staffSearch(loading, search, this)
			},
			agencySearch: _.debounce( (loading, search, vm) => { 
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
			}, 350)
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
			'value.clientType': function(newVal){
				this.value.client = {}
				if (newVal == "agency") {
					this.value.client = this.agency
				}
			}
		}
	}
</script>
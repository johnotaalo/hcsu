<template>
	<div class="row align-items-center">
		<div class="col-auto">
			<b-form-group label="Client Type">
				<b-form-radio-group
					v-model="value.clientType"
					:options="clientTypes"
				></b-form-radio-group>
			</b-form-group>
		</div>
		<div class="col-md">
			<b-form-group label = "Client" v-if="value.clientType">
				<v-select v-if="value.clientType == 'agency'"  label="AGENCYNAME" :filterable="false" :options="agencies" @search="onAgencySearch" v-model="value.selectedAgency">
					<template slot="no-options">
						Type to search for an agency
					</template>
					<template slot="selected-option" slot-scope="option">
						<span v-if="option.AGENCYNAME">{{ option.AGENCYNAME }} [{{ option.ACRONYM }}]</span>
					</template>
				</v-select>

				<v-select v-if="value.clientType == 'staff'" label = "LAST_NAME" :filterable="false" :options="staffMembers" @search="onStaffSearch" v-model="value.selectedStaff">
					<template slot="no-options">
						Type to search for a staff member
					</template>
					<template slot="option" slot-scope="option">
						<div class = "row align-items-center">
							<div class="col ml-n2">
								<h4 class = "card-title mb-1">{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}</h4>
							</div>
						</div>
					</template>

					<template slot="selected-option" slot-scope="option">
						<span v-if="option.LAST_NAME">
							{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }} (<span v-if="option.latest_contract">{{ option.latest_contract.ACRONYM }}</span><span v-else>N/A</span>)
						</span>
					</template>
				</v-select>

				<v-select v-if="value.clientType == 'dependant'" label = "LAST_NAME" :filterable="false" :options="dependents" @search="onDependentSearch" v-model="value.selectedDependent">
					<template slot="no-options">
						Type to search for a dependant
					</template>

					<template slot="option" slot-scope="option">
						<div class = "row align-items-center">
							<div class="col ml-n2">
								<h4 class = "card-title mb-1">{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}</h4>
								<p class="card-text text-muted">{{ option.relationship_x.RELATIONSHIP }} of {{ option.principal.LAST_NAME }}, {{ option.principal.OTHER_NAMES }} ({{ option.principal.latest_contract.ACRONYM }})</p>
							</div>
						</div>
					</template>

					<template slot="selected-option" slot-scope="option">
						<span v-if="option.LAST_NAME">
							<div class = "row align-items-center p-3">
								<div class="col ml-n2">
									<h4 class = "card-title mb-1">{{ option.LAST_NAME }}, {{ option.OTHER_NAMES }}</h4>
									<p class="card-text text-muted">{{ option.relationship_x.RELATIONSHIP }} of {{ option.principal.LAST_NAME }}, {{ option.principal.OTHER_NAMES }} ({{ option.principal.latest_contract.ACRONYM }})</p>
								</div>
							</div>
						</span>
					</template>
				</v-select>
			</b-form-group>
			<small v-else>Please select a client</small>
		</div>

		<div class="col-md">
			<b-form-group label="Plate No.">
				<b-input v-model = "value.plateNo" />
			</b-form-group>
		</div>

		<div class="col-md">
			<b-form-group>
				<b-form-radio-group
					v-model="value.measurements"
					:options="measurements"
				></b-form-radio-group>
			</b-form-group>
		</div>

		<div class="col-auto">
			<b-button variant="danger" size="sm" @click="removeRow"><i class = "fe fe-x"></i>Remove</b-button>
			<b-button variant="primary" size="sm" @click="duplicate"><i class = "fe fe-copy"></i>Duplicate</b-button>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default{
		props: {
			'value': { type: null, default: null },
			'errors': { type: Object, default: null },
			'index': { type: null, default: null }
		},
		data(){
			return {
				clientType: "",
				clientTypes: [
					{ value: 'agency', text: 'Agency' },
					{ value: 'staff', text: 'Staff Member' },
					{ value: 'dependant', text: 'Dependant' }
				],
				measurements: [
					{ value: 'Single Plate', text: 'Single Plate' },
					{ value: 'Pair', text: 'Pair' },
				],
				agencies: [],
				staffMembers: [],
				dependents: [],
				client: {
					host_country_id: 0
				},
				selectedAgency: {},
				selectedStaff: {},
				selectedDependent: {}
			}
		},
		mounted(){
		},
		methods: {
			onAgencySearch: function(search, loading){
				loading(true)
				this.agencySearch(loading, search, this)
			},
			agencySearch: _.debounce( (loading, search, vm) => {
				axios(`/api/agencies/search?q=${escape(search)}`)
				.then((res) => {
					vm.agencies = res.data
					loading(false)
				})
			}, 350),
			onStaffSearch(search, loading){
				loading(true)
				this.staffSearch(loading, search, this)
			},
			staffSearch: _.debounce( (loading, search, vm) => {
				axios(`/api/principal/search?q=${escape(search)}`)
				.then((res) => {
					vm.staffMembers = res.data
					loading(false)
				})
			}, 350),
			onDependentSearch(search, loading){
				loading(true)
				this.dependentSearch(loading, search, this)
			},
			dependentSearch: _.debounce( (loading, search, vm) => {
				axios(`/api/dependent/search?q=${escape(search)}`)
				.then( (res) => {
					vm.dependents = res.data
					loading(false)
				} )
			}, 350 ),

			duplicate () {
				this.$emit('duplicate', this.value.id)
			},
			removeRow () {
				this.$emit('remove', this.value.id)
			},
		},
		watch: {
			selectedAgency: function(newVal, oldVal){

			}
		}
	}
</script>
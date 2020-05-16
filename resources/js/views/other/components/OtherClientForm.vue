<template>
	<div>
		<loading :active.sync="loading" :can-cancel="false" :is-full-page="true"></loading>
		<legend>Personal Information</legend>
		<div class="row">
			<div class="col-md">
				<b-form-group label = "Last Name">
					<b-input v-model = "value.lastName" />
				</b-form-group>
			</div>

			<div class="col-md">
				<b-form-group label = "Other Names">
					<b-input v-model = "value.otherNames" />
				</b-form-group>
			</div>
		</div>

		<div class="row">
			<div class="col-md">
				<b-form-group label = "Nationality">
					<v-select :options="options.nationalities" v-model = "value.nationality"></v-select>
				</b-form-group>
			</div>

			<div class="col-md">
				<b-form-group label = "Date of Birth">
					<datetime input-id = "dob" v-model = "value.dob" input-class="form-control" :max-datetime="$moment().format()"/>
				</b-form-group>
			</div>
		</div>

		<div class="row">
			<div class="col-md">
				<b-form-group label = "Type of Client">
					<b-select :options="options.clientTypes" v-model="value.clientType"></b-select>
				</b-form-group>
			</div>

			<div class="col-md">
				<b-form-group label = "Affiliated Agency">
					<v-select :options="options.agencies" v-model = "value.agency"></v-select>
				</b-form-group>
			</div>
		</div>

		<div class="row">
			<div class="col-md">
				<b-form-group label = "Description">
					<b-input v-model = "value.description" />
				</b-form-group>
			</div>
		</div>

		<legend>Passport Details</legend>
		<div class="row">
			<div class="col-md">
				<b-form-group label = "Passport No.">
					<b-input v-model = "value.passport.no" />
				</b-form-group>
			</div>

			<div class="col-md">
				<b-form-group label = "Issue Date">
					<datetime v-model = "value.passport.issue_date" input-id="passport_issue_date" input-class="form-control" :max-datetime="$moment().format()"/>
				</b-form-group>
			</div>

			<div class="col-md">
				<b-form-group label = "Expiry Date">
					<datetime v-model = "value.passport.expiry_date" input-id = "passport_expiry_date" input-class="form-control" :min-datetime="value.passport.issue_date" :max-datetime="$moment(value.passport.expiry_date).add(10, 'years').format()"/>
				</b-form-group>
			</div>

			<div class="col-md">
				<b-form-group label = "Country of Issue">
					<v-select :options="options.nationalities" v-model = "value.passport.country" />
				</b-form-group>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	import { Datetime } from 'vue-datetime'
	export default {
		components: { Datetime },
		props: {
			'value': { type: null, default: null },
		},
		data(){
			return {
				options: {
					nationalities: [],
					loading: false,
					agencies: [],
					clientTypes: [
						'Meeting Participant',
						'Relative to Staff Member',
						'Relative to Staff Member in Other Duty Station'
					]
				}
				
			}
		},
		created(){
			var _this = this
			_this.loading = true
			axios.all([this.getNationality(), this.getAgencies()])
			.then(axios.spread(function(nationalities, agencies){
				_this.loading = false
				_this.options.nationalities = _.map(nationalities.data, (o) => {
					return {
						id: o.id,
						label: o.name
					}
				})

				_this.options.agencies = _.map(agencies.data, (o) => {
					return {
						id: o.AGENCY_ID,
						label: `${o.ACRONYM} - ${o.AGENCYNAME}`
					}
				})
			}))
		},
		methods: {
			getNationality(){
				return axios.get('/api/data/nationalities');
			},
			getAgencies(){
				return axios.get('/api/agencies');
			}
		}
	}
</script>
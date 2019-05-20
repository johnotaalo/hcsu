<template>
	<transition name="fade">
		<div>
			<div class="row">
				<div class="col-sm">
					<label for="passport_type" >Passport Type</label>
					<b-form-select v-model="value.passportType" :options="passportTypes">
						<template slot="first">
							<option :value="null" disabled>-- Please select an option --</option>
						</template>
					</b-form-select>
				</div>
				<div class="col-sm">
					<label for="passport_no">Passport No.</label>
					<b-input v-model="value.passportNo" id="passport_no" />
				</div>
				<div class="col-sm" v-if="value.passportType != 3">
					<label for="place_issue">Place of Issue</label>
					<b-input v-model="value.place_issue" id="place_issue" />
				</div>
				<div class="col-sm">
					<label v-if="value.passportType != 3" for="country_issue">Country of Issue</label>
					<label v-else for="country_issue">Issuing Organization</label>
					<v-select v-model="value.country_issue" :options="selectCountries">
						<!-- <template slot="first">
							<option :value="null" disabled>-- Please select an option --</option>
						</template> -->
					</v-select>
				</div>
				<div class="col-sm">
					<label for="date_issue">Date of Issue</label>
					<!-- <b-input v-model="value.date_issue" id="date_issue" type="date" /> -->
					<datetime v-model="value.date_issue" input-id="date_issue" input-class="form-control" :max-datetime="maxFromDate"></datetime>
				</div>
				<div class="col-sm">
					<label for="expiry_date">Expiry Date</label>
					<!-- <b-input v-model="value.expiry_date" id="expiry_date" type="date" /> -->
					<datetime v-model="value.expiry_date" input-id="expiry_date" input-class="form-control"></datetime>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-sm">
					<b-button variant="danger" size="sm" @click="removeRow"><i class="fe fe-trash"></i>&nbsp;Remove</b-button> | <b-button variant="info" size="sm" @click="duplicate"><i class="fe fe-copy"></i>&nbsp;Duplicate</b-button>
				</div>
			</div>
			<hr>
		</div>
	</transition>
</template>
<script type="text/javascript">
	import { Datetime } from 'vue-datetime'
	
	export default {
		name: 'PassportRow',
		components: {datetime: Datetime},
		props: {
			'value': { type: null, default: null },
			'errors': { type: Object, default: null },
			'index': { type: null, default: null },
			passportTypes: {
				type: Array, default: () => { return [] }
			},
			countries: {
				type: Array, default: () => { return [] }
			}
		},
		data() {
			return {
				selectCountries: []
			}
		},
		computed: {
			maxFromDate: function() {
				return this.$moment().format()
			}
		},
		methods: {
			duplicate () {
				this.$emit('duplicate', this.value.id)
			},
			removeRow () {
				this.$emit('remove', this.value.id)
			}
		},
		watch: {
			'value.date_issue': function(newVal, oldVal){
				if (this.value.passportType == 3) {
					this.value.expiry_date = this.$moment(newVal).add(5, "Years").format()
				}
			},
			'value.passportType': function(newVal, oldVal){
				if (this.value.passportType == 3 && this.value.date_issue != "") {
					this.value.expiry_date = this.$moment(this.value.date_issue).add(5, "Years").format()
				}

				if(this.value.passportType == 3){
					this.selectCountries = _.map(this.countries, function(o){
						if( o.id == "UNNY" || o.id == "UNOG" ) { return o }
					})

					this.selectCountries = _.without(this.selectCountries, undefined)
				}else{
					this.selectCountries = this.countries
				}
			}
		}
	}
</script>
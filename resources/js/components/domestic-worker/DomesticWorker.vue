<template>
	<div>
		<b-form>
			<b-form-group v-if="searchPrincipal" label="Principal">
				<v-select></v-select>
			</b-form-group>
			<b-form-group>
				<div class="row">
					<div class="col-md-6">
						<label>Last Name</label>
						<b-input v-model="value.lastName" size="sm"/>
					</div>
					<div class="col-md-6">
						<label>Other Names</label>
						<b-input v-model="value.otherNames" size="sm"/>
					</div>
				</div>
			</b-form-group>

			<b-form-group>
				<div class="row">
					<div class="col-md-6">
						<label>Email</label>
						<b-input v-model="value.email" :value = "principaldata.email" size="sm"/>
					</div>
					<div class="col-md-6">
						<label>Phone Number</label>
						<b-input v-model="value.phone" size="sm"/>
					</div>
				</div>
			</b-form-group>

			<!-- <b-form-group>
				<div class="row">
					<div class="col-md-6">
						<label>Email</label>
						<b-input size="sm"/>
					</div>
					<div class="col-md-6">
						<label>Phone Number</label>
						<b-input size="sm"/>
					</div>
				</div>
			</b-form-group> -->

			<b-form-group>
				<div class="row">
					<div class="col-md-6">
						<label>Place of Birth</label>
						<b-input v-model="value.placeOfBirth" size="sm"/>
					</div>
					<div class="col-md-6">
						<label>Date of Birth</label>
						<b-input v-model="value.dateOfBirth" size="sm" type="date"/>
					</div>
				</div>
			</b-form-group>

			<b-form-group>
				<div class="row">
					<div class="col-md-6">
						<label>Employment Date</label>
						<b-input v-model="value.employmentDate" size="sm" type="date"/>
					</div>

					<div class="col-md-6">
						<label>Nationality</label>
						<b-select v-model="value.nationality" size="sm" :options="options.nationalities"></b-select>
					</div>
				</div>
			</b-form-group>

			<b-form-group>
				<div class="row">
					<div class="col-md-6">
						<label>Residence No (If exists)</label>
						<b-input v-model="value.rno" size="sm"></b-input>
					</div>

					<div class="col-md-6">
						<label>Address</label>
						<b-textarea v-model="value.address" size="sm"></b-textarea>
					</div>
				</div>
			</b-form-group>

			<legend>Passports</legend>
			<b-button variant = "success" size="sm" @click="addRow(value.passports)"><i class="fe fe-plus"></i>&nbsp;Add Passport</b-button>
			<passport-row v-for="(row, index) in value.passports" :passportTypes="options.passportTypes" :countries="options.countries" :key="row.id" v-model="value.passports[index]" @duplicate="duplicateRow($event, value.passports)"
              @remove="removeRow($event, value.passports)" />
		</b-form>
	</div>
</template>

<script type="text/javascript">
	import rowForm from '../..//mixins/rowForm'
	import PassportRow from '../../views/principal/PassportRow'
	export default {
		components: { PassportRow },
		mixins: [rowForm],
		props: {
			'value': { type: null, default: null },
			searchPrincipal: { type: Boolean, default: true },
			principaldata: { type: null, default: null }
		},
		data(){
			return {
				passports: [],
				options: {
					passportTypes: [],
					countries: [],
					nationalities: []
				}
			}
		},

		mounted() {
			this.getDomesticWorkerOptions()
			if (this.principaldata) {
				if(this.value.email == ""){
					this.value.email = this.principaldata.email
				}

				if(this.value.phone == ""){
					this.value.phone = this.principaldata.mobileNo
				}

				if(this.value.address == ""){
					this.value.address = this.principaldata.Address
				}
			}
		},

		methods: {
			getDomesticWorkerOptions(){
				axios.get('/api/data/options/domestic-worker')
				.then(res => {
					this.options.countries = _.map(res.data.countries, country => ({
						id: country.iso_3,
						label: country.official_name
					}));

					this.options.nationalities = _.map(res.data.countries, country => ({
						value: country.id,
						text: country.official_name
					}));

					this.options.passportTypes = _.map(res.data.passportTypes, type => ({
						value: type.ID, 
						text: type.PPT_TYPE
					}));
				})
				.catch(error => {
					alert("There was an error fetching data " + error.message);
				});
			}
		}
	}
</script>
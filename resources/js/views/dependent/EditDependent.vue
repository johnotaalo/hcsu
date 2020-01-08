<template>
	<div>
		<div class="row align-items-end">
			<div class="col mb-3 ml-n3 ml-md-n2">
				<h6 class="header-pretitle">Dependent</h6>
				<h1 class="header-title">{{ form.dependent.LAST_NAME }}, {{ form.dependent.OTHER_NAMES }}</h1>
			</div>

			<div class="col-12 col-md-auto mt-2 mt-md-0 mb-md-3">
				<h6 class="header-pretitle">HOST COUNTRY ID</h6>
				<h1 class="header-title">{{ form.dependent.HOST_COUNTRY_ID }}</h1>
			</div>
		</div>

		<div class="row align-items-center">
			<div class="col-md">
				<b-card no-body>
					<b-tabs card>
						<b-tab title="Dependent Details" active>
							<div class="form-group row">
								<div class="col-md">
									<label>Last Name</label>
									<b-input v-model = "form.dependent.LAST_NAME"/>
								</div>

								<div class="col-md">
									<label>Other Names</label>
									<b-input v-model = "form.dependent.OTHER_NAMES"/>
								</div>
							</div>

							<div class="form-group row">
								<div class="col-md-6">
									<label>Relationship to Principal</label>
									<b-select v-model = "form.dependent.RELATIONSHIP_ID" :options="options.relationships" />
								</div>

								<div class="col-md-6">
									<label>Nationality</label>
									<b-select v-model="form.dependent.COUNTRY" :options="options.nationalities"/>
								</div>
							</div>

							<div class="form-group row">
								<div class="col-md-6">
									<label>Gender</label>
									<b-select v-model="form.dependent.GENDER" :options="options.gender"/>
								</div>
							</div>

							<div class="form-group row">
								<div class="col-md">
									<label>KRA PIN</label>
									<b-input v-model = "form.dependent.PIN"/>
								</div>

								<div class="col-md">
									<label>Residence No. (If not same as principal)</label>
									<b-input v-model = "form.dependent.RNO"/>
								</div>
							</div>

							<div class="form-group row">
								<div class="col-md">
									<label>Date of Birth</label>
									<datetime v-model="form.dependent.DATE_OF_BIRTH" type="date" input-id="dob" input-class="form-control" :auto="dateAutoProp"></datetime>
								</div>

								<div class="col-md">
									<label>Place of Birth</label>
									<b-input v-model = "form.dependent.PLACE_OF_BIRTH"/>
								</div>
							</div>

							<b-button variant="primary" @click="editDependentDetails">Update Data</b-button>
						</b-tab>

						<b-tab title="Passports">
							<b-button variant="primary" class="mb-2" size="sm" v-b-modal.add-passport-modal>Add Passport</b-button>
							<b-table :fields="table.fields" :items="dependent.passports" show-empty>
								<template v-slot:cell(actions)="data">
									<b-button size="sm" variant="primary" @click="openEditPassportModal(data.index)" v-b-modal.add-passport-modal>Edit</b-button>
									<b-button size="sm" variant="danger" @click="removePassport(data.index)">Remove</b-button>
								</template>
							</b-table>
						</b-tab>
					</b-tabs>
				</b-card>
			</div>
		</div>

		<b-modal id="add-passport-modal" ref="add-passport-modal" title="Add Passport" @ok="addPassport" @cancel="cancelPassportModal" @hidden="clearModal">
			<b-form-group label= "Passport No">
				<b-form-input v-model="modal.passport.PASSPORT_NO" />
			</b-form-group>

			<b-form-group label= "Issue Date">
				<datetime v-model="modal.passport.ISSUE_DATE" type="date" input-id="modal-passport-issue-date" input-class="form-control" :auto="dateAutoProp"/>
			</b-form-group>

			<b-form-group label= "Expiry Date">
				<datetime v-model="modal.passport.EXPIRY_DATE" type="date" input-id="modal-passport-expiry-date" input-class="form-control" :auto="dateAutoProp"/>
			</b-form-group>

			<b-form-group label= "Place of Issue">
				<b-form-input v-model="modal.passport.PLACE_OF_ISSUE" />
			</b-form-group>

			<b-form-group label= "Country of Issue">
				<b-select v-model = "modal.passport.COUNTRY_OF_ISSUE" :options= "options.nationalities"/>
			</b-form-group>
		</b-modal>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../core/Form'
	import { Datetime } from 'vue-datetime'
	export default {
		components: { datetime: Datetime },
		data(){
			return {
				dependent_id: this.$route.params.id,
				dateAutoProp: true,
				form: new Form({
					dependent: {

					}
				}),

				editPassportIndex: -1,

				modal: {
					passport: new Form({
						PASSPORT_NO: "",
						PASSPORT_TYPE: 2,
						ISSUE_DATE: "",
						EXPIRY_DATE: "",
						PLACE_OF_ISSUE: "",
						COUNTRY_OF_ISSUE: ""
					})
				},
				dependent: {},
				options: {
					gender: [
						{
							value: "Female",
							text: "Female"
						},
						{
							value: "Male",
							text: "Male"
						}
					]
				},
				table: {
					fields: [
						'PASSPORT_NO',
						'ISSUE_DATE',
						'EXPIRY_DATE',
						'PLACE_OF_ISSUE',
						'COUNTRY_OF_ISSUE',
						{ key: 'actions', label: 'ACTIONS' }
					]
				}
			}
		},
		mounted(){
			this.getData()
		},
		methods: {
			getData(){
				axios.get(`/api/data/dependent/get/${this.dependent_id}`)
				.then(res => {
					this.dependent = res.data

					this.form.dependent.LAST_NAME = this.dependent.LAST_NAME
					this.form.dependent.OTHER_NAMES = this.dependent.OTHER_NAMES
					this.form.dependent.RELATIONSHIP_ID = this.dependent.RELATIONSHIP_ID
					this.form.dependent.COUNTRY = this.dependent.COUNTRY
					this.form.dependent.PIN = this.dependent.PIN
					this.form.dependent.RNO = this.dependent.RNO
					this.form.dependent.DATE_OF_BIRTH = this.dependent.DATE_OF_BIRTH
					this.form.dependent.PLACE_OF_BIRTH = this.dependent.PLACE_OF_BIRTH
					this.form.dependent.GENDER = this.dependent.GENDER
				})

				axios.get(`/api/data/options/dependent`)
				.then(res => {
					this.options.relationships = _.map(res.data.relationships, (o) => {
						return {
							value: o.REL_ID,
							text: o.RELATIONSHIP
						}
					})

					this.options.nationalities = _.map(res.data.nationalities, (o) => {
						return {
							value: o.pm_abbrev,
							text: o.official_name
						}
					})
				})
			},

			editDependentDetails(){
				this.form.post(`/data/dependent/update/${this.dependent_id}`)
				.then(res => {

				})
			},

			addPassport(){
				if(this.editPassportIndex == -1){
					this.modal.passport.post(`/data/dependent/passport/add/${this.dependent_id}`)
					.then(res => {
						this.getData()
					})
				}else{
					var passport = this.dependent.passports[this.editPassportIndex]

					this.modal.passport.post(`/data/dependent/passport/edit/${passport.ID}`)
					.then(res => {
						this.getData()
					})
				}
			},

			openEditPassportModal(idx){
				var passport = this.dependent.passports[idx]
				this.modal.passport.PASSPORT_NO = passport.PASSPORT_NO
				this.modal.passport.ISSUE_DATE = passport.ISSUE_DATE
				this.modal.passport.EXPIRY_DATE = passport.EXPIRY_DATE
				this.modal.passport.PLACE_OF_ISSUE = passport.PLACE_OF_ISSUE
				this.modal.passport.COUNTRY_OF_ISSUE = passport.COUNTRY_OF_ISSUE
				this.editPassportIndex = idx
			},

			removePassport(idx){
				this.$swal({
					title: "Delete Passport?",
					text: "This action cannot be undone",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						this.proceedDelete(idx)
					}
				});
			},

			proceedDelete(idx){
				var passport = this.dependent.passports[idx]
				axios.delete(`/api/data/dependent/passport/delete/${passport.ID}`)
				.then(res => {
					this.getData()
				})
			},

			clearModal(){
				this.modal.passport.PASSPORT_NO = ""
				this.modal.passport.ISSUE_DATE = ""
				this.modal.passport.EXPIRY_DATE = ""
				this.modal.passport.PLACE_OF_ISSUE = ""
				this.modal.passport.COUNTRY_OF_ISSUE = ""
				this.editPassportIndex = -1
			},

			cancelPassportModal(){
				this.clearModal()
			}
		}
	}
</script>
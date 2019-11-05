<template>
	<div>
		<b-card>
			<form-wizard :title="fullName" :rules="rules" validate-on-back subtitle= "" finishButtonText="Submit Data" @on-complete="onComplete">
				<tab-content title= "Personal Details" icon="fe fe-user" :before-change="()=>validateStep('personal')">
					<div ref="personal">
						<div class="row">
							<div class="col-md">
								<center>
									<b-img v-bind="options.principalImageProps" rounded="circle" alt="Circle image" :src="form.principalPhoto"></b-img>
									<b-button @click="launchPrincipalFilePicker">Upload Avatar</b-button>
									<input type="file"
									ref="principalFile"
									:name="principalFileName"
									@change="onPrincipalFileChange(
									$event.target.name, $event.target.files)"
									style="display:none">
								</center>
							</div>
						</div>
						<div class="row">
							<div class="col-md">
								<div class="form-group" >
									<label for="lastname">Last Name</label>
									<b-input @input="$v.form.lastName.$touch()" :state="validationStates.personal.lastName" v-model="form.lastName" id="lastname" :size="formSize" required />
									<div class="invalid-feedback">Please provide the last name.</div>
								</div>

								<div class="form-group">
									<label for="marital_status">Marital Status</label>
									<b-form-select :state="validationStates.personal.maritalStatus" v-model="form.maritalStatus" :options="options.marital_status" :size="formSize"></b-form-select>
									<div class="invalid-feedback">Please provide a marital status.</div>
								</div>

								<div class="form-group">
									<label for="gender">Gender</label>
									<b-form-select :state="validationStates.personal.gender" v-model="form.gender" :options="options.gender" id="gender" :size="formSize"></b-form-select>
									<div class="invalid-feedback">Please provide a gender.</div>
								</div>
							</div>
							<div class="col-md">
								<div class="form-group">
									<label for="othernames">Other Names</label>
									<b-input :state="validationStates.personal.otherNames" v-model="form.otherNames" id="othernames" :size="formSize" />
									<div class="invalid-feedback">Please provide other names.</div>
								</div>
								<div class="form-group">
									<label for="dob">Date of Birth</label>
									<datetime :state="validationStates.personal.dob" v-model="form.dob" type="date" input-id="dob" input-class="form-control" :auto="dateAutoProp" :max-datetime="maxDobPrincipal"></datetime>
									<div class="invalid-feedback">Please provide a date of birth.</div>
								</div>						
							</div>
						</div>

						<div class="row">
							<legend class="col-md-12">Kenyan Identification</legend>
							<div class="col-md">
								<div class="form-group">
									<label for="pin">PIN No.</label>
									<b-input v-model="form.pin" :size="formSize"></b-input>
								</div>
							</div>

							<div class="col-md">
								<div class="form-group">
									<label for="pin">Residence No. (RNo)</label>
									<b-input v-model="form.residenceNo" :size="formSize"></b-input>
								</div>
							</div>

							<div class="col-md">
								<div class="form-group">
									<label for="pin">Driving License No.</label>
									<b-input v-model="form.drivingLicense" :size="formSize"></b-input>
								</div>
							</div>
						</div>

						<div class="row">
							<legend class="col-md-12">Contact Information</legend>
							<div class="col-md">
								<div class="form-group">
									<label for="mobile_no">Mobile No.</label>
									<b-input :state="validationStates.personal.mobileNo" placeholder="+xx xxx xxx xxx" v-model="form.mobileNo" id="mobile_no" :size="formSize" type="tel" required />
									<div class="invalid-feedback">Please provide a mobile no.</div>
								</div>

								<div class="form-group">
									<label for="email">Email Address</label>
									<b-input v-model="form.email" :state="validationStates.personal.email" id="email" :size="formSize" type="email" required placeholder="someone@example.com"/>
									<div class="invalid-feedback">Please provide an email address.</div>
								</div>
							</div>
							<div class="col-md">
								<div class="form-group">
									<label for="mobile_no">Office No.</label>
									<b-input placeholder="+xx xxx xxx xxx" v-model="form.officeNo" id="mobile_no" :size="formSize" type="tel" required />
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md">
								<div class="form-group">
									<label for="address">Address</label>
									<b-form-textarea id="address" v-model="form.Address" placeholder="Please enter the address" rows="3" max-rows="6" :state="validationStates.personal.Address"></b-form-textarea>
									<div class="invalid-feedback">Please provide an address.</div>
								</div>
							</div>
							<div class="col-md">
								<div class="form-group">
									<label for="residence">Residence</label>
									<b-form-textarea id="residence" v-model="form.residence" placeholder="Please enter the residence" rows="3" max-rows="6" :state="validationStates.personal.residence"></b-form-textarea>
									<div class="invalid-feedback">Please provide a residence.</div>
								</div>
							</div>
						</div>
					</div>
					
				</tab-content>
				<tab-content title= "Passport Details" icon ="fe fe-book" :before-change="()=>validateStep('passports')">
					<div class="row">
						<div class="col-sm">
							<b-button variant = "success" size="sm" @click="addRow(form.passports)"><i class="fe fe-plus"></i>&nbsp;Add Passport</b-button>
						</div>
					</div>
					<passport-row v-for="(row, index) in form.passports" :passportTypes="options.passportTypes" :countries="options.countries" :key="row.id" v-model="form.passports[index]" @duplicate="duplicateRow($event, form.passports)"
              @remove="removeRow($event, form.passports)" />
				</tab-content>
				<tab-content title= "Contract Details" icon = "fe fe-file" :before-change="()=>validateStep('contract')">
					<contract-details ref="contract" :agencies="options.agencies" :contractTypes="options.contractTypes" :grades="options.grades" v-model="form.contract" :designations="options.designations" @on-validate="mergeContractData"></contract-details>
				</tab-content>
				<tab-content title= "Dependents" icon = "fe fe-users">
					<div class="row">
						<div class="col-md">
							<b-button class="float-right mb-3" size="sm" variant="outline-primary" @click="addDependentMdl" v-b-modal.dependent-modal><i class="fe fe-plus-circle"></i>&nbsp;Add Dependent</b-button>
						</div>
					</div>
					<div class="row">
						<div class="col-md">
							<b-table :items="form.dependents" :fields="fields" small class="table-nowrap card-table">
								<div slot="imageURL" slot-scope="data">
									<b-img v-if="data.item.imageURL" width="50" height="50" rounded="circle" alt="Circle image" :src="data.item.imageURL" />
									<b-img v-else width="50" height="50" rounded="circle" alt="Circle image" src="/images/no_avatar.svg"></b-img>
								</div>

								<div slot="name" slot-scope="data">
									{{ data.item.lastName }} {{ data.item.otherNames }}
								</div>

								<div slot="relationshipType" slot-scope="data">
									{{ data.item.relationshipType.label }}
								</div>

								<div slot="dob" slot-scope="data">
									{{ data.item.dob | moment("MMMM Do, YYYY") }}
								</div>

								<div slot="actions" class="float-right" slot-scope="row">
									<b-button variant="warning" @click="editDependent(row.index)" size="sm" class="mr-1"> <i class = "fe fe-edit mr-2"></i>Edit</b-button>
									<b-button variant="danger" @click="deleteDependent(row.index)" size="sm"> <i class = "fe fe-trash mr-2"></i>Remove</b-button>
								</div>
							</b-table>
						</div>
					</div>
				</tab-content>
			</form-wizard>
		</b-card>

		<!-- Modal -->
		<b-modal id="dependent-modal" ref="dependent-modal" title="Add Dependent" size="lg" @ok="addDependent">
			<div class="row">
				<div class="col-md">
					<center>
						<b-img v-bind="options.imageProps" rounded="circle" alt="Circle image" :src="form.dependentModal.imageURL"></b-img>
						<b-button @click="launchFilePicker">Upload Avatar</b-button>
						<input type="file"
						ref="file"
						:name="uploadFieldName"
						@change="onFileChange(
						$event.target.name, $event.target.files)"
						style="display:none">
					</center>
				</div>
			</div>
			<div class="row">
				<div class="col-md">
					<div class="form-group">
						<label for="dependentLastName">Last Name</label>
						<b-input v-model="form.dependentModal.lastName" id="lastName" />
						<div v-if="modalErrors.dependent.lastName" class="my-invalid-feedback">Please provide a last name.</div>
					</div>

					<div class="form-group">
						<label for="dependentRelationship">Relationship</label>
						<v-select :options="options.relationships" v-model="form.dependentModal.relationshipType"></v-select>
						<div v-if="modalErrors.dependent.relationshipType" class="my-invalid-feedback">Please provide a relationship type.</div>
					</div>

					<div class="form-group">
						<label for="dependentPassport">Passport No.</label>
						<b-input v-model="form.dependentModal.passport" id="passport" />
						<div v-if="modalErrors.dependent.passport" class="my-invalid-feedback">Please provide a passport number.</div>
					</div>
				</div>
				<div class="col-md">
					<div class="form-group">
						<label for="dependentOtherNames">Other Names</label>
						<b-input v-model="form.dependentModal.otherNames" id="otherNames" />
						<div v-if="modalErrors.dependent.otherNames" class="my-invalid-feedback">Please provide other name(s).</div>
					</div>

					<div class="form-group">
						<label for="dependentCountry">Country</label>
						<v-select :options="options.countries" v-model="form.dependentModal.country"></v-select>
						<div v-if="modalErrors.dependent.country" class="my-invalid-feedback">Please provide a country.</div>
					</div>

					<div class="form-group">
						<label for="dependentDob">Date of Birth</label>
						<datetime v-model="form.dependentModal.dob" input-id="dependentDob" :max-datetime="dependentMaxDatetime" input-class="form-control"></datetime>
						<div v-if="modalErrors.dependent.dob" class="my-invalid-feedback">Please provide a date of birth.</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md">
					<div class="form-group">
						<label for="employmentDetails">Employment Details</label>
						<b-form-textarea id="employmentDetails" v-model="form.dependentModal.employment" placeholder="Please enter the employment details" rows="5" max-rows="6"></b-form-textarea>
					</div>
				</div>
			</div>
			
		</b-modal>
		<!-- Modal -->
	</div>
</template>
<script type="text/javascript">
	import {FormWizard, TabContent} from 'vue-form-wizard'
	import 'vue-form-wizard/dist/vue-form-wizard.min.css'
	import Form from '../core/Form'
	import rowForm from '../mixins/rowForm'
	import _ from 'lodash'
	import { Datetime } from 'vue-datetime'
	import PassportRow from './principal/PassportRow'
	import ContractDetails from './principal/ContractDetails'
	import Dependents from './principal/Dependents'
	import { required, minLength, between, email } from 'vuelidate/lib/validators'

	export default {
		components: { FormWizard, TabContent, datetime: Datetime, PassportRow, ContractDetails, Dependents },
		mixins: [rowForm],
		data() {
			return {
				case_no: (this.$route.query.case_no) ? this.$route.query.case_no : null,
				rqType: this.$route.query.type,
				uploadFieldName: 'file',
				principalFileName: 'principal_photo',
				fields: [
					{ key: "imageURL", label : ""},
					"name",
					"relationshipType",
					{ key: "passport", label: "Passport No."},
					{ key: "dob", label: "Birth Date"},
					{ key: "actions", label: "" } 
				],
				rules: {
					lastName: [{
						required: true,
						message: 'Last name is required',
						trigger: 'blur'
					}]
				},
				modalErrors: {
					dependent: {
						lastName: "",
						relationshipType : "",
						passport : "",
						otherNames : "",
						country : "",
						dob : ""
					}
				},
				modalEdit: false,
				editIndex: null,
				validationStates: {
					personal: {
						lastName: "",
						otherNames: "",
						dob: "",
						mobileNo: "",
						maritalStatus: "",
						gender: "",
						email: "",
						Address: "",
						residence: ""
					},
					contract: {
						agency: "",
						contractType: "",
						indexNo: "",
						designation: "", 
						functionalTitle: "",
						contractFrom: "",
						contractTo: "",
						grade: ""
					}
				},
				form: new Form({
					principalPhoto: "",
					principalPhotoFile: "",
					lastName: "",
					otherNames: "",
					maritalStatus:"",
					maxDate: new this.$moment().format(),
					gender: "",
					dob: "",
					mobileNo: "",
					email: "",
					officeNo: "",
					Address: "",
					residence: "",
					passports: [],
					pin: "",
					residenceNo: "",
					drivingLicense: "",
					contract: {
						agency: "",
						contractType: "",
						indexNo: "",
						designation: "", 
						functionalTitle: "",
						contractFrom: "",
						contractTo: "",
						grade: ""
					},
					dependents: [],
					dependentModal: {
						lastName: "",
						relationshipType : "",
						passport : "",
						otherNames : "",
						country : "",
						dob : "",
						employment : "",
						imageURL: "",
						imageFile: ""
					}
				}),
				formSize: "",
				dateAutoProp: true,
				options: {
					imageProps: { blank: true, blankColor: '#777', width: 100, height: 100, class: 'm1' },
					principalImageProps: { blank: true, blankColor: '#777', width: 100, height: 100, class: 'm1' },
					marital_status: [{ 
							value: "Single", 
							text: "Single"
						},{ 
							value: "Married", 
							text: "Married"
						},{ 
							value: "Divorced", 
							text: "Divorced"
						},{ 
							value: "Separated", 
							text: "Separated"
						}
					],
					gender: [
						{
							value: "Male",
							text: "Male"
						},{
							value: "Female",
							text: "Female"
						}
					],
					passportTypes: [],
					countries: [],
					agencies: [],
					contractTypes: [],
					grades: [],
					relationships: [],
					designations: []
				},
				rowTemplate: {
					id: "",
					passportType: null,
					passportNo: "",
					place_issue: "",
					country_issue: null,
					date_issue: "",
					expiry_date: ""
				}
			}
		},
		validations: {
			form: {
				lastName: {
					required
				},
				otherNames: {
					required
				},
				dob: {
					required
				},
				mobileNo: {
					required
				},
				maritalStatus:{
					required
				},
				gender:{
					required
				},
				email:{
					required,
					email
				},
				Address: {
					required
				},
				residence: {
					required
				},
			}
		},
		computed: {
			fullName: function() {
				return  _.upperCase(this.form.lastName) + " " + _.startCase(_.toLower(this.form.otherNames)) 
			},
			dependentMaxDatetime: function(){
				if(this.form.dependentModal.relationshipType.id == 2 || this.form.dependentModal.relationshipType.id == 7){
					return this.$moment().subtract(16, "Years").format()
				}else if(this.form.dependentModal.relationshipType.id == 5 || this.form.dependentModal.relationshipType.id == 6){
					return this.$moment().subtract(30, "Years").format()
				}

				return new this.$moment().format();
			},
			maxDobPrincipal: function(){
				return this.$moment().subtract(18, "Years").format()
			}
		},
		mounted() {
			// var query = this.$route.query
			// var case_no = (query.case_no) ? query.case_no : null

			// this.case_no = case_no
			this.getPrincipalOptions();
 		},
 		watch: {
 		},
		methods: {
			validateStep(name) {
				var em = this
				this.$v.form.$touch();
				var isValid = !this.$v.form.$invalid
				if(name == "contract"){
					var refToValidate = this.$refs[name];
					return refToValidate.validate();
				}
				else if(name == "passports"){
					if (this.form.passports.length == 0) {
						alert("You have to add at least one passport")
						return false
					}
					var errors = [];
					_.forOwn(this.form.passports, (passport, key) => {
						var pError = {}
						_.forOwn(passport, (value, title) => {
							if(em.form.passports[key].passportType == 3 && title == "place_issue"){
								pError[title] = false
							}else{
								pError[title] = (value == "" || value == null) ? true : false
							}
						})
						console.log(pError)
						errors[key] = _.includes(pError, true)
					})

					
					return !_.includes(errors, true)
				}
				else{
					var currKeys = _.keys(this.validationStates[name])
					_.forOwn(currKeys, (key) => {
						this.validationStates[name][key] = !this.$v.form[key].$error
					})
					return !_.includes(this.validationStates[name], false)
				}
			},

			mergeContractData(model, isValid){
				if (isValid) {
					this.form.contract = model.data
				}
			},
			getPrincipalOptions: function(){
				axios('/api/data/principal-options')
				.then((res) => {
					this.options.countries = _.map(res.data.countries, country => ({
						id: country.iso_3,
						label: country.official_name
					}));

					this.options.passportTypes = _.map(res.data.passportTypes, type => ({
						value: type.ID, 
						text: type.PPT_TYPE
					}));

					this.options.agencies = _.map(res.data.agencies, agency => ({
						id: agency.AGENCY_ID, 
						label: agency.ACRONYM + " - " + agency.AGENCYNAME
					}));

					this.options.contractTypes = _.map(res.data.contractTypes, type => ({
						id: type.ID,
						label: type.C_TYPE
					}));

					this.options.grades = _.map(res.data.grades, grade => ({
						id: grade.ID,
						label: grade.GRADE + " - " + grade.CATEGORY
					}))

					this.options.relationships = _.map(res.data.relationships, relationship => ({
						id: relationship.REL_ID,
						label: relationship.RELATIONSHIP
					}))

					var designationsVal = [];
					_.forOwn(res.data.designations, (value, key) => {
						var k = value.GRADE + " - " + value.CATEGORY;
						if(typeof designationsVal[k] == "undefined"){
							designationsVal[k] = []
						}
						designationsVal[k].push(value);
					})

					this.options.designations = designationsVal;

					// this.options.designations = _.map(res.data.designations, relationship => ({
					// 	id: relationship.REL_ID,
					// 	label: relationship.RELATIONSHIP
					// }))
				})
			},

			addDependentMdl: function(){
				this.modalEdit = false;
				this.editIndex = null;
			},

			addDependent: function(depModal){
				var em = this;

				var dataObj = {};
				var errors = {
					lastName: "",
					relationshipType : "",
					passport : "",
					otherNames : "",
					country : "",
					dob : ""
				};
				_.forOwn(errors, (value, key) => {
					if(em.form.dependentModal[key] == ""){
						errors[key] = true
					}
				})

				this.modalErrors.dependent = errors;

				if(!_.includes(errors, true)){
					_.forOwn(em.form.dependentModal, (value, key) =>{
						dataObj[key] = value
						em.form.dependentModal[key] = "";
					})

					this.options.imageProps.blank = true

					if(!em.modalEdit){
						em.form.dependents.push(dataObj);
					}else{
						em.form.dependents.splice(this.editIndex, 1, dataObj)
					}
					this.editIndex = null
				}else{
					depModal.preventDefault()
				}

				
			},

			editDependent: function(index){
				this.editIndex = index;
				this.form.dependentModal = this.form.dependents[index]
				if(this.form.dependentModal){
					this.options.imageProps.blank = false;
				}else{
					this.options.imageProps.blank = true;
				}
				this.modalEdit = true;
				this.$refs['dependent-modal'].show()
			},

			deleteDependent: function(index){
				this.$swal({
					title: "Delete Dependent?",
					text: "This action cannot be undone",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						this.proceedDelete(index)
					}
				});
			},

			proceedDelete: function(index){
				this.form.dependents.splice(index, 1);
			},

			launchFilePicker: function(){
				this.$refs.file.click();
			},

			launchPrincipalFilePicker: function(){
				this.$refs.principalFile.click();
			},

			onFileChange(fieldName, file) {
				const { maxSize } = this
				let imageFile = file[0] 

				//check if user actually selected a file
				if (file.length>0) {
					let size = imageFile.size / maxSize / maxSize
					if (!imageFile.type.match('image.*')) {
						alert("Please choose an image file");
					} else if (size>1) {
						// check whether the size is greater than the size limit
						alert('Your file is too big! Please select an image under 1MB')
					} else {
						// Append file into FormData & turn file into image URL
						let formData = new FormData()
						let imageURL = URL.createObjectURL(imageFile)
						this.form.dependentModal.imageURL = imageURL
						this.form.dependentModal.imageFile = imageFile
						this.options.imageProps.blank = false
					}
				}
			},

			onPrincipalFileChange(fieldName, file) {
				const { maxSize } = this
				let imageFile = file[0] 

				//check if user actually selected a file
				if (file.length>0) {
					let size = imageFile.size / maxSize / maxSize
					if (!imageFile.type.match('image.*')) {
						alert("Please choose an image file");
					} else if (size>1) {
						// check whether the size is greater than the size limit
						alert('Your file is too big! Please select an image under 1MB')
					} else {
						// Append file into FormData & turn file into image URL
						let formData = new FormData()
						let imageURL = URL.createObjectURL(imageFile)
						this.form.principalPhoto = imageURL
						this.form.principalPhotoFile = imageFile
						this.options.principalImageProps.blank = false
					}
				}
			},

			onComplete: function(){
				var em = this
				var urlParams = (this.rqType == "iframe") ? `case_no=${this.case_no}` : ""

				this.form.post(`/principal/add?${urlParams}`)
				.then((res) => {
					this.$swal(`Success! Host Country ID: ${res.principal.HOST_COUNTRY_ID}`, "The client has successfully been registered", "success")
					if(this.rqType != "iframe"){
						em.$router.push({ name: 'principal.view', params: { id: res.data.host_country_id } })
					}
				})
				.catch((error) => {
					console.log(error)
					this.$swal("Error!", "There was an error while performing your request!", "error")
				});
			}
		}
	}
</script>
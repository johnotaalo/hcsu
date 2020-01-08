<template>
	<div>
		<div class="header">

			<!-- Image -->
			<img v-if="!iframe" src="../../../public/img/covers/profile-cover-1.jpg" class="header-img-top" alt="...">

			<div class="container-fluid">

				<!-- Body -->
				<div class="header-body" :class="{'mt-n5 mt-md-n6' : !iframe}">
					<div class="row align-items-end">
						<div class="col-auto">

							<!-- Avatar -->
							<div class="avatar avatar-xxl header-avatar-top principal-avatar" @click="handleFileUpload">
								<b-img v-if="principal.image_link != '/storage/'" class ="avatar-img border border-4 border-body" rounded="circle" :alt="fullname" :src="principalImage" style="background: #FCE4EC;"></b-img>
								<b-img v-else class ="avatar-img border border-4 border-body" rounded="circle" alt="Circle image" src="/images/no_avatar.svg"></b-img>
								<div class="image-upload-btn">
									<!-- <div class="text">John Doe</div> -->
									<i class = "fe fe-camera"></i>
								</div>

								<input type="file"
								ref="principalFile"
								name="principalFile"
								@change="onPrincipalFileChange(
								$event.target.name, $event.target.files)"
								style="display:none">
							</div>
							<div v-if="form.principal.image.url" class="mt-1">
								<center><b-button size="sm" variant="danger" @click="cancelPrincipalUpload">Cancel</b-button></center>
							</div>

						</div>
						<div class="col mb-3 ml-n3 ml-md-n2">

							<!-- Pretitle -->
							<h6 class="header-pretitle">Principal</h6>

							<!-- Title -->
							<h1 class="header-title">{{ fullname }}</h1>

						</div>
						<div class="col-12 col-md-auto mt-2 mt-md-0 mb-md-3">
							<!-- Pretitle -->
							<h6 class="header-pretitle">HOST COUNTRY ID</h6>

							<!-- Title -->
							<h1 class="header-title">{{ principal.HOST_COUNTRY_ID }}</h1>
						</div>
					</div> <!-- / .row -->

				</div> <!-- / .header-body -->
				<div class="row align-items-center">
					<div class="col-md">
						<b-card no-body>
							<b-tabs card>
								<b-tab title="Principal Details" active>
									<div class="row">
										<div class="col-md">
											<div class="form-group">
												<label for="lastname">Last Name</label>
												<b-input v-model="form.principal.lastName" id="lastname" :size="formSize" required />
											</div>

											<div class="form-group">
												<label for="marital_status">Marital Status</label>
												<b-form-select v-model="form.principal.maritalStatus" :options="options.marital_status" :size="formSize"></b-form-select>
											</div>

											<div class="form-group">
												<label for="gender">Gender</label>
												<b-form-select v-model="form.principal.gender" :options="options.gender" id="gender" :size="formSize"></b-form-select>
											</div>
										</div>
										<div class="col-md">
											<div class="form-group">
												<label for="othernames">Other Names</label>
												<b-input v-model="form.principal.otherNames" id="othernames" :size="formSize" />
											</div>
											<div class="form-group">
												<label for="dob">Date of Birth</label>
												<datetime v-model="form.principal.dob" type="date" input-id="dob" input-class="form-control" :auto="dateAutoProp"></datetime>
											</div>
										</div>
									</div>
									<hr>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="pin">Place of Birth</label>
												<b-input v-model="form.principal.place_of_birth" :size="formSize"></b-input>
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												<label for="pin">Nationality</label>
												<v-select :options="options.countries" v-model="form.principal.nationality"></v-select>
											</div>
										</div>
									</div>
									<hr>
									<div class="row">
										<div class="col-md">
											<div class="form-group">
												<label for="pin">PIN No.</label>
												<b-input v-model="form.principal.pin" :size="formSize"></b-input>
											</div>
										</div>

										<div class="col-md">
											<div class="form-group">
												<label for="pin">Residence No. (RNo)</label>
												<b-input v-model="form.principal.residenceNo" :size="formSize"></b-input>
											</div>
										</div>

										<div class="col-md">
											<div class="form-group">
												<label for="pin">Driving License No.</label>
												<b-input v-model="form.principal.drivingLicense" :size="formSize"></b-input>
											</div>
										</div>
									</div>
									<hr/>
									<div class="row">
										<div class="col-md">
											<div class="form-group">
												<label for="mobile_no">Mobile No.</label>
												<b-input placeholder="+xx xxx xxx xxx" v-model="form.principal.mobileNo" id="mobile_no" :size="formSize" type="tel" required />
											</div>

											<div class="form-group">
												<label for="email">Email Address</label>
												<b-input v-model="form.principal.email" id="email" :size="formSize" type="email" required placeholder="someone@example.com"/>
											</div>
										</div>
										<div class="col-md">
											<div class="form-group">
												<label for="mobile_no">Office No.</label>
												<b-input placeholder="+xx xxx xxx xxx" v-model="form.principal.officeNo" id="mobile_no" :size="formSize" type="tel" required />
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md">
											<div class="form-group">
												<label for="address">Address</label>
												<b-form-textarea id="address" v-model="form.principal.Address" placeholder="Please enter the address" rows="3" max-rows="6"></b-form-textarea>
											</div>
										</div>
										<div class="col-md">
											<div class="form-group">
												<label for="residence">Residence</label>
												<b-form-textarea id="residence" v-model="form.principal.residence" placeholder="Please enter the residence" rows="3" max-rows="6"></b-form-textarea>
											</div>
										</div>
									</div>
									<b-button @click="updatePrincipalData" size="sm" variant="primary">Update Data</b-button>
								</b-tab>
								<b-tab title="Passports">
									<template slot="title">
										<i class="fe fe-book mr-2"></i>Passports <b-badge variant="success">{{ principal.passports.length }}</b-badge>
									</template>

									<b-button class="mb-3" variant="success" size="sm" v-b-modal.modal-passport><i class="fe fe-plus mr-1"></i>Add Passport</b-button>
									<b-table :fields="passports.table.fields" :items="principal.passports">
										<template slot="PASSPORT_TYPE" slot-scope="data">
											{{ data.item.type.PPT_TYPE }}
										</template>

										<template slot="ISSUE_DATE" slot-scope="data">
											{{ data.item.ISSUE_DATE | moment("MMMM Do, YYYY") }}
										</template>

										<template slot="EXPIRY_DATE" slot-scope="data">
											{{ data.item.EXPIRY_DATE | moment("MMMM Do, YYYY") }}
										</template>

										<template slot="ACTIONS" slot-scope="row">
											<span>
											<b-button variant = "info" size="sm" @click="editPassport(row.index)" v-b-modal.modal-passport><i class="fe fe-edit mr-1"></i>Edit</b-button>
											<b-button variant = "danger" size="sm" @click="removePassport(row.index)"><i class="fe fe-trash mr-1"></i>Remove</b-button>
											</span>
										</template>
									</b-table>
								</b-tab>

								<b-tab title="Contracts">
									<template slot="title">
										<i class="fe fe-file mr-2"></i> Contracts <b-badge variant="warning">{{ principal.contracts.length }}</b-badge>
									</template>
									<b-button class="mb-3" variant="success" size="sm" v-b-modal.modal-contract><i class="fe fe-plus mr-1"></i>Add Contract</b-button>
									<b-table :fields="contracts.table.fields" :items="principal.contracts">
										<template slot="AGENCY" slot-scope="data">
											<span v-if="data.item.contract_agency">{{ data.item.contract_agency.ACRONYM }}</span>
											<span v-else></span>
										</template>

										<template slot = "CONTRACT_TYPE" slot-scope ="data">
											<span v-if="data.item.type">{{ data.item.type.C_TYPE }}</span>
										</template>

										<template slot = "GRADE" slot-scope ="data">
											<span v-if="data.item.renewals.grade">{{ data.item.renewals.grade.GRADE }}</span>
										</template>

										<template slot="DURATION" slot-scope="data">
											<span v-if="data.item.renewals">{{ data.item.renewals.START_DATE | moment("DD/MM/YYYY") }} to {{ data.item.renewals.END_DATE | moment("DD/MM/YYYY") }}</span>
										</template>

										<template slot="ACTIONS" slot-scope="row">
											<span>
												<b-button variant="primary" size="sm" @click="editContract(row.index)" v-b-modal.modal-contract><i class = "fe fe-edit mr-1"></i>Edit</b-button>
												<b-button variant="danger" size="sm" @click="removeContract(row.index)"><i class = "fe fe-trash mr-1"></i>Delete</b-button>
											</span>
										</template>
									</b-table>
								</b-tab>

								<b-tab title="Dependents">
									<b-button class="mb-3" variant="success" size="sm" v-b-modal.dependent-modal><i class="fe fe-plus mr-1"></i>Add Dependent</b-button>
									<template slot="title">
										<i class="fe fe-users mr-2"></i>Dependents <b-badge variant="warning">{{ principal.dependents.length }}</b-badge>
									</template>
									<b-table :fields="dependents.table.fields" :items="principal.dependents">
										<template slot="IMAGE" slot-scope="data">
											<b-img width="50" height="50" rounded="circle" alt="Circle image" :src="data.item.image_link" />
										</template>

										<template slot = "NAME" slot-scope="data">
											{{ data.item.LAST_NAME }}, {{ data.item.OTHER_NAMES }}
										</template>

										<template slot="DATE_OF_BIRTH" slot-scope="data">
											{{ data.item.DATE_OF_BIRTH | moment("MMMM Do, YYYY") }}
										</template>

										<template slot = "RELATIONSHIP" slot-scope="data">
											{{ data.item.relationship.RELATIONSHIP }}
										</template>

										<template slot = "ACTIONS" slot-scope="row">
											<div class="dropdown mr-3">
												<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButtonTwo" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												Actions
												</button>
												<div class="dropdown-menu" aria-labelledby="dropdownMenuButtonTwo" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -2px, 0px);">
													<b-link class="dropdown-item" v-b-modal.dependent-passport-modal @click="selectedDependent = row.index">Passports</b-link>
													<b-link class="dropdown-item" @click="editDependent(row.index)" v-b-modal.dependent-modal>Edit</b-link>
													<b-link class="dropdown-item" @click="removeDependent(row.index)">Delete</b-link>
												</div>
											</div>
										</template>
									</b-table>
								</b-tab>

								<b-tab title="Vehicles">
									<template slot="title">
										<i class="fe fe-truck mr-2"></i>Vehicles <b-badge variant="warning">{{ principal.vehicles.length }}</b-badge>
									</template>
									<vehicle-list v-if="typeof principal.HOST_COUNTRY_ID != 'undefined'" :host_country_id="principal.HOST_COUNTRY_ID"></vehicle-list>
								</b-tab>
							</b-tabs>
						</b-card>
					</div>

				</div>
			</div>
		</div>
		<!-- passport modal -->
		<b-modal id="modal-passport" :title="activeTitle.passport" @ok="addPassport" @cancel="cancelPassportModal" @bv::modal::hidden="cancelPassportModal">
			<div class="form-group">
				<label class="control-label">Passport Type</label>
				<b-form-select v-model="modal.passport.passport_type" :options="options.passportTypes"></b-form-select>
			</div>

			<div class="form-group">
				<label for="passport_no">Passport No.</label>
				<b-input v-model="modal.passport.passportNo" id="passport_no" />
			</div>

			<div class="form-group" v-if="modal.passport.passport_type != 3">
				<label for="place_issue">Place of Issue</label>
				<b-input v-model="modal.passport.place_issue" id="place_issue" />
			</div>

			<div class="form-group">
				<label v-if="modal.passport.passport_type != 3" for="country_issue">Country of Issue</label>
				<label v-else for="country_issue">Issuing Organization</label>
				<v-select v-model="modal.passport.country_issue" :options="selectCountries"></v-select>
			</div>

			<div class="form-group">
				<label for="date_issue">Date of Issue</label>
				<datetime v-model="modal.passport.date_issue" input-id="date_issue" input-class="form-control" :max-datetime="maxFromDate"></datetime>
			</div>

			<div class="form-group">
				<label for="expiry_date">Expiry Date</label>
				<datetime v-model="modal.passport.expiry_date" input-id="expiry_date" input-class="form-control"></datetime>
			</div>
		</b-modal>

		<!-- contract modal -->
		<b-modal id="modal-contract" title="Add Contract" @ok="addContract" @cancel="cancelContractModal">
			<contract-details :agencies="options.agencies" :contractTypes="options.contractTypes" :grades="options.grades" v-model="modal.contract" :designations="filteredDesignations"></contract-details>
		</b-modal>

		<b-modal id="dependent-modal" ref="dependent-modal" title="Add Dependent" size="lg" @ok="addDependent" @cancel="cancelDependentModal">
			<div class="row">
				<div class="col-md">
					<center>
						<b-img v-bind="options.imageProps" rounded="circle" alt="Circle image" :src="modal.dependent.imageURL"></b-img>
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
						<b-input v-model="modal.dependent.lastName" id="lastName" />
					</div>

					<div class="form-group">
						<label for="dependentRelationship">Relationship</label>
						<v-select :options="options.relationships" v-model="modal.dependent.relationshipType"></v-select>
					</div>

					<div class="form-group">
						<label for="dependentPassport">Passport No.</label>
						<b-input v-model="modal.dependent.passport" id="passport" />
					</div>
				</div>
				<div class="col-md">
					<div class="form-group">
						<label for="dependentOtherNames">Other Names</label>
						<b-input v-model="modal.dependent.otherNames" id="otherNames" />
					</div>

					<div class="form-group">
						<label for="dependentCountry">Country</label>
						<v-select :options="options.countries" v-model="modal.dependent.country"></v-select>
					</div>

					<div class="form-group">
						<label for="dependentDob">Date of Birth</label>
						<datetime v-model="modal.dependent.dob" input-id="dependentDob" input-class="form-control" :max-datetime="dependentMaxDatetime"></datetime>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md">
					<div class="form-group">
						<label for="employmentDetails">Employment Details</label>
						<b-form-textarea id="employmentDetails" v-model="modal.dependent.employment" placeholder="Please enter the employment details" rows="5" max-rows="6"></b-form-textarea>
					</div>
				</div>
			</div>

		</b-modal>

		<b-modal id="dependent-passport-modal" ref="dependent-modal" title="Dependent Passport" size="xl">
			<passports v-if="selectedDependent != -1" :passportTypes="options.passportTypes" :countries="options.countries" :passports="dependentPassportDetails"></passports>
		</b-modal>
		<!-- <vue-toastr ref="toastr"></vue-toastr> -->
		</div>
</template>

<script type="text/javascript">
	import Form from '../core/Form'
	import { Datetime } from 'vue-datetime'
	import ContractDetails from './principal/ContractDetails'
	import VehicleList from './vehicle/VehicleList'
	import Passports from './dependent/Passports'
	import vUploader from 'v-uploader'

	const uploaderConfig = {
		// file uploader service url
		uploadFileUrl: '/api/data/upload/image',
		// file delete service url
		deleteFileUrl: '/api/data/upload/image',
		// set the way to show upload message(upload fail message)
		showMessage: (vue, message) => {
		    //using v-dialogs to show message
		    console.log(message)
		}
	};

	// this.$root.app.use(vUploader, uploaderConfig);

	export default {
		components: { datetime: Datetime, ContractDetails, VehicleList, Passports, vUploader },
		data() {
			return {
				userId: this.$route.params.id,
				formSize: "sm",
				dateAutoProp: true,
				uploadFieldName: 'file',
				selectCountries: [],
				selectedDependent: -1,
				countries: [],
				form: {
					principal: new Form({
						id: "",
						lastName: "",
						otherNames: "",
						maritalStatus: "",
						gender: "",
						dob: "",
						mobileNo: "",
						email: "",
						officeNo: "",
						Address: "",
						residence: "",
						pin: "",
						residenceNo: "",
						drivingLicense: "",
						image: {
							url: ""
						},
						place_of_birth: "",
						nationality: {}
					})
				},
				activeTitle: {
					contract: "",
					passport: ""
				},
				titles: {
					contract: {
						add: "Add Contract",
						edit: "Edit Contract"
					},
					passport: {
						add: "Add Passport",
						edit: "Edit Passport"
					}
				},
				modal: {
					passport: new Form({
						passport_type : "",
						passportNo : "",
						place_issue : "",
						country_issue : "",
						date_issue : "",
						expiry_date : "",
						principal_id: "",
						passport_id: "",
						editIndex: ""
					}),
					contract: new Form({
						principal_id: "",
						agency: "",
						contractType: "",
						indexNo: "",
						designation: "",
						functionalTitle: "",
						contractFrom: "",
						contractTo: "",
						editIndex: "",
						contract_id: "",
						grade: ""
					}),
					dependent: new Form({
						id: "",
						lastName: "",
						relationshipType : "",
						passport : "",
						otherNames : "",
						country : "",
						dob : "",
						employment : "",
						imageURL: "",
						imageFile: "",
						editIndex: "",
						principal_id: ""
					})
				},
				options: {
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
					imageProps: { blank: true, blankColor: '#777', width: 100, height: 100, class: 'm1' },
					countries: [],
					passportTypes: [],
					agencies: [],
					contractTypes: [],
					grades: [],
					relationships: [],
					designations: []
				},
				principal: {
					contracts:[],
					dependents: [],
					passports: [],
					vehicles: []
				},
				passports: {
					table: {
						fields: ["PASSPORT_TYPE", "PASSPORT_NO", "ISSUE_DATE", "EXPIRY_DATE", "PLACE_OF_ISSUE", "ACTIONS"]
					},
					editIndex: -1
				},
				contracts: {
					table: {
						fields: ["AGENCY", "INDEX_NO", "CONTRACT_TYPE", "GRADE", "DESIGNATION", "DURATION", "ACTIONS"]
					},
					editIndex: -1
				},
				dependents: {
					table: {
						fields: ["IMAGE", "HOST_COUNTRY_ID", "NAME", "RELATIONSHIP", "COUNTRY", "PASSPORT_NO", "DATE_OF_BIRTH", "EMPLOYMENT_DETAILS", "ACTIONS"]
					},
					editIndex: -1
				},
				principalImageProps: { blank: false, blankColor: '#777', width: 100, height: 100, class: 'm1' },
				iframe: false
			}
		},
		mounted() {
			this.$parent.isContainer = false
			this.getFormOptions();
			this.getPrincipalData();
			

			this.$root.$on('bv::modal::hidden', (bvEvent, modalId) => {
				if(modalId == "modal-passport"){
					this.cancelPassportModal()
				}else if(modalId == "modal-contract"){
					this.cancelContractModal()
				}else if(modalId = "dependent-modal"){
					this.cancelDependentModal()
				}
			})

			this.activeTitle.contract = this.titles.contract.add
			this.activeTitle.passport = this.titles.passport.add

			this.iframe = this.$parent.iframe
			// this.$root.$refs.$toastr.e("ERROR MESSAGE")
		},
		watch: {
			'modal.passport.passport_type': function(newVal, oldVal){
				if (this.modal.passport.passport_type == 3 && this.modal.passport.date_issue != "") {
					this.modal.passport.expiry_date = this.$moment(this.modal.passport.date_issue).add(5, "Years").format()
				}

				if(this.modal.passport.passport_type == 3){
					this.selectCountries = _.map(this.options.countries, function(o){
						if( o.id == "UNNY" || o.id == "UNOG" ) { return o }
					})

					this.selectCountries = _.without(this.selectCountries, undefined)
				}else{
					this.selectCountries = this.options.countries
				}
			},
			'modal.passport.date_issue': function(newVal, oldVal){
				if (this.modal.passport.passport_type == 3 && this.modal.passport.date_issue != "") {
					this.modal.passport.expiry_date = this.$moment(this.modal.passport.date_issue).add(5, "Years").format()
				}
			}
		},
		computed: {
			principalImage: function(){
				if(this.form.principal.image.url){
					return this.form.principal.image.url
				}
				return '/photos/principal/' + this.principal.HOST_COUNTRY_ID
			},
			fullname: function(){
				if(typeof(this.principal.LAST_NAME) != "undefined")
					return this.principal.LAST_NAME + ", " + this.principal.OTHER_NAMES;
				return ""
			},
			filteredDesignations: function(){
				var designations = this.options.designations;
				if(this.modal.contract.grade){
					var filtered = _.map(designations[this.modal.contract.grade.label], designation => ({
						id: designation.ID,
						label: designation.DESIGNATION
					}))
					return filtered
				}

				return []
			},
			dependentMaxDatetime: function(){
				var relationshipType = this.modal.dependent.relationshipType.id;
				if(relationshipType == 2 || relationshipType == 7){
					return this.$moment().subtract(16, "Years").format()
				}else if(relationshipType == 5 || relationshipType == 6){
					return this.$moment().subtract(30, "Years").format()
				}

				return new this.$moment().format();
			},
			maxFromDate: function(){
				return this.$moment().format()
			},
			dependentPassportDetails: function(){
				var passports = this.principal.dependents[this.selectedDependent].passports;
				var cleanedPassports = [];
				if (passports) {
					_.forOwn(passports, (passport) => {
						var pp = {}
						pp.passportType = passport.PASSPORT_TYPE_ID
						pp.passportNo = passport.PASSPORT_NO
						pp.date_issue = passport.ISSUE_DATE
						pp.expiry_date = passport.EXPIRY_DATE
						pp.place_issue = passport.PLACE_OF_ISSUE
						pp.country_issue = passport.COUNTRY_OF_ISSUE
						cleanedPassports.push(pp)
					})
				}
				return cleanedPassports
			},
			updatable: function(){
				return !this.iframe || this.$store.state.isUserRetrieved
			}
		},
		methods: {
			handleFileUpload: function(){
				this.$refs.principalFile.click();
			},
			launchFilePicker: function(){
				this.$refs.file.click();
			},
			cancelPrincipalUpload: function(){
				this.form.principal.image.url = ""
				this.form.principal.image.file = ""
				this.principal.image_link = "/storage/"
			},
			onPrincipalFileChange(fieldName, file){
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
						this.form.principal.image.url = imageURL
						this.form.principal.image.file = imageFile
						this.principal.image_link = imageURL
						this.options.imageProps.blank = false
					}
				}
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
						this.modal.dependent.imageURL = imageURL
						this.modal.dependent.imageFile = imageFile
						this.options.imageProps.blank = false
					}
				}
			},
			getPrincipalData: function(){
				axios('/api/principal/get/' + this.userId)
				.then((res) => {
					this.principal = res.data
					this.form.principal.id = this.principal.ID
					this.modal.passport.principal_id = this.principal.ID
					this.modal.contract.principal_id = this.principal.HOST_COUNTRY_ID
					this.modal.dependent.principal_id = this.principal.HOST_COUNTRY_ID
					this.form.principal.lastName = this.principal.LAST_NAME
					this.form.principal.otherNames = this.principal.OTHER_NAMES
					this.form.principal.maritalStatus = this.principal.MARITAL_STATUS
					this.form.principal.gender = this.principal.GENDER
					this.form.principal.dob = this.principal.DATE_OF_BIRTH
					this.form.principal.mobileNo = this.principal.MOBILE_NO
					this.form.principal.email = this.principal.EMAIL
					this.form.principal.officeNo = this.principal.OFFICE_NO
					this.form.principal.Address = this.principal.ADDRESS
					this.form.principal.residence = this.principal.RESIDENCE
					this.form.principal.pin = this.principal.PIN_NO
					this.form.principal.drivingLicense = this.principal.DL_NO
					this.form.principal.residenceNo = this.principal.R_NO
					this.form.principal.place_of_birth = this.principal.PLACE_OF_BIRTH
					let principalNationality = _.find(this.countries, ["id", this.principal.NATIONALITY])
					this.form.principal.nationality['label'] = principalNationality.official_name
					this.form.principal.nationality['id'] = principalNationality.iso_3
					
				})
			},
			getFormOptions: function() {
				axios('/api/data/principal-options')
				.then((res) => {
					this.countries = res.data.countries
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
					}));

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
				});
			},
			updatePrincipalData: function(){
				var _this = this;
				this.form.principal.post('/principal/update')
				.then((res) => {
					_this.principal = res
					_this.form.principal.image.url = ""
					_this.form.principal.image.file = ""
				});
			},
			addPassport: function(){
				var _this = this;
				if(_this.modal.passport.passport_id == ""){
					this.modal.passport.post('/principal/passport/add')
					.then((res) => {
						_this.principal.passports.push(res);

						_this.clearPassportModal()
					})
				}else{
					this.modal.passport.post('/principal/passport/edit/')
					.then((res) => {
						_this.principal.passports.splice(_this.passports.editIndex, 1, res)

						_this.clearPassportModal()
						this.$notify({
							group: 'foo',
							title: 'Success',
							text: "Successfully edited the passport",
							type: "success"
						});
					})
					.catch(error => {
						this.$notify({
							group: 'foo',
							title: 'Error',
							text: "There was an error editing the passport. Please contact the system administrator",
							type: "danger"
						});
					})
				}
			},
			editPassport: function(index){
				var _this = this;

				this.activeTitle.passport = this.titles.passport.edit

				var passportData = _this.principal.passports[index]

				_this.modal.passport.editIndex = index
				_this.passports.editIndex = index
				_this.modal.passport.passport_type = passportData.PASSPORT_TYPE_ID
				_this.modal.passport.passportNo = passportData.PASSPORT_NO
				_this.modal.passport.place_issue = passportData.PLACE_OF_ISSUE
				_this.modal.passport.country_issue = passportData.COUNTRY_OF_ISSUE
				_this.modal.passport.date_issue = this.$moment(passportData.ISSUE_DATE).format("YYYY-MM-DD")
				_this.modal.passport.expiry_date = this.$moment(passportData.EXPIRY_DATE).format("YYYY-MM-DD")
				_this.modal.passport.passport_id = passportData.ID
				// alert(index)
			},
			removePassport: function(index){
				var _this = this;
				this.$swal({
					title: "Delete Passport?",
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
				var _this = this;
				var passport_id = this.principal.passports[index].ID
				var passportNo = this.principal.passports[index].passportNo
				instance.default.delete('/principal/passport', { data: {id: passport_id } }).then(res => {
					if(res.data.status == "success"){
						this.$notify({
							group: 'foo',
							title: 'Success',
							text: "Successfully removed " + passportNo + " from " + _this.fullname + " profile",
							type: "success"
						});

						this.principal.passports.splice(index, 1)
					}
				})
				.catch(error => {
					var errorMessage = ""
					if(error.reponse){
						errorMessage = error.response.data.message
					}else if(error.request){
						var errorObj = JSON.parse(error.request.response)
						errorMessage = error.request.statusText + "<br/>" + errorObj.message
					}else{
						errorMessage = error.request
					}

					this.$notify({
						group: 'foo',
						title: 'Error',
						text: errorMessage,
						type: "danger"
					});
				});
			},
			clearPassportModal: function(){
				var _this = this
				_this.modal.passport.passport_type = ""
				_this.modal.passport.passportNo = ""
				_this.modal.passport.place_issue = ""
				_this.modal.passport.country_issue = ""
				_this.modal.passport.date_issue = ""
				_this.modal.passport.expiry_date = ""
				_this.modal.passport.passport_id = ""
				_this.modal.passport.editIndex = ""
				// _this.passports.editIndex = -1
				this.activeTitle.passport = this.titles.passport.add
			},
			cancelPassportModal: function(){
				this.clearPassportModal()
			},

			addContract: function(){
				var _this = this;
				if(_this.modal.contract.contract_id == ""){
					this.modal.contract.post('/principal/contract/add')
					.then((res) => {
						_this.principal.contracts.push(res);

						_this.clearContractModal()
					})
				}else{
					this.modal.contract.post('/principal/contract/edit')
					.then((res) => {
						_this.principal.contracts.splice(_this.contracts.editIndex, 1, res)

						_this.clearContractModal()
					})
				}
			},

			editContract: function(index){
				var _this = this;

				var contractData = _this.principal.contracts[index]

				_this.modal.contract.editIndex = index
				_this.contracts.editIndex = index



				var agencyObj = _.find(_this.options.agencies, [ 'id', contractData.AGENCY_ID ]);
				var contractTypeObj = _.find(_this.options.contractTypes, [ 'id', contractData.CONTRACT_TYPE_ID ]);
				var gradeObj = _.find(_this.options.grades, [ 'id', contractData.renewals.GRADE_ID ]);
				var cleanedDesignations = _.map(_this.options.designations[gradeObj.label], (o) => {
					return {
						id: o.ID,
						label: o.DESIGNATION
					}
				})
				var designationObj = _.find(cleanedDesignations, ['label', contractData.DESIGNATION])

				agencyObj = (typeof agencyObj == "undefined") ? {} : agencyObj
				contractTypeObj = (typeof contractTypeObj == "undefined") ? {} : contractTypeObj
				gradeObj = (typeof gradeObj == "undefined") ? {} : gradeObj
				designationObj = (typeof designationObj == "undefined") ? {} : designationObj

				_this.modal.contract.agency = agencyObj
				_this.modal.contract.contractType = contractTypeObj
				_this.modal.contract.indexNo = contractData.INDEX_NO
				_this.modal.contract.designation = designationObj
				_this.modal.contract.functionalTitle = contractData.FUNC_TITLE
				_this.modal.contract.contractFrom = contractData.renewals.START_DATE
				_this.modal.contract.contractTo = contractData.renewals.END_DATE
				_this.modal.contract.contract_id = contractData.ID
				_this.modal.contract.grade = gradeObj

				// console.log(_this.modal.contract);
			},

			cancelContractModal: function() {
				this.clearContractModal()
			},

			clearContractModal: function(){
				var _this = this

				_this.modal.contract.agency = ""
				_this.modal.contract.contractType = ""
				_this.modal.contract.indexNo = ""
				_this.modal.contract.designation = ""
				_this.modal.contract.functionalTitle = ""
				_this.modal.contract.contractFrom = ""
				_this.modal.contract.contractTo = ""
				_this.modal.contract.editIndex = ""
				_this.modal.contract.contract_id = ""
				_this.modal.contract.grade = ""
			},
			removeContract: function(index){
				var _this = this;
				this.$swal({
					title: "Delete Contract?",
					text: "This action cannot be undone",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						this.proceedDeleteContract(index)
					}
				});
			},
			proceedDeleteContract: function(index){
				var _this = this;
				var contract_id = this.principal.contracts[index].ID
				var indexNo = this.principal.contracts[index].INDEX_NO

				instance.default.delete('/principal/contract', { data: {id: contract_id } }).then(res => {
					if(res.data.status == "success"){
						this.$notify({
							group: 'foo',
							title: 'Success',
							text: "Successfully deleted contract index no: " + indexNo + " from " + _this.fullname + " profile",
							type: "success"
						});

						this.principal.contracts.splice(index, 1)
					}
				})
				.catch(error => {
					var errorMessage = ""
					if(error.reponse){
						errorMessage = error.response.data.message
					}else if(error.request){
						var errorObj = JSON.parse(error.request.response)
						errorMessage = error.request.statusText + "<br/>" + errorObj.message
					}else{
						errorMessage = error.request
					}

					this.$notify({
						group: 'foo',
						title: 'Error',
						text: errorMessage,
						type: "danger"
					});
				});
			},

			addDependent: function(){
				var _this = this
				if (this.modal.dependent.id == "") {
					this.modal.dependent.post('/principal/dependent/add')
					.then((res) => {
						_this.principal.dependents.push(res);

						_this.clearDependentModal()
					})
				}else{
					this.modal.dependent.post('/principal/dependent/edit')
					.then((res) => {
						console.log(res);
						_this.principal.dependents.splice(_this.dependents.editIndex, 1, res)

						_this.clearDependentModal()
					})
				}
			},

			editDependent: function(index){
				var dependent = this.principal.dependents[index]
				var _this = this

				var countryObj = _.find(_this.options.countries, [ 'label', dependent.COUNTRY ]);
				var relationshipObj = _.find(_this.options.relationships, [ 'id', dependent.RELATIONSHIP_ID ]);

				countryObj = (typeof countryObj == "undefined") ? {} : countryObj
				relationshipObj = (typeof relationshipObj == "undefined") ? {} : relationshipObj

				_this.modal.dependent.id = dependent.ID
				_this.modal.dependent.lastName = dependent.LAST_NAME
				_this.modal.dependent.relationshipType  = relationshipObj
				_this.modal.dependent.passport  = dependent.PASSPORT_NO
				_this.modal.dependent.otherNames  = dependent.OTHER_NAMES
				_this.modal.dependent.country  = countryObj
				_this.modal.dependent.dob  = dependent.DATE_OF_BIRTH
				_this.modal.dependent.employment  = dependent.EMPLOYMENT_DETAILS
				_this.modal.dependent.imageURL = dependent.image_link
				_this.modal.dependent.imageFile = ""
				_this.modal.dependent.editIndex = index
				_this.dependents.editIndex = index

				if(dependent.image_link != ""){
					this.options.imageProps.blank = false
				}else{
					this.options.imageProps.blank = true
				}
			},

			removeDependent: function(index){
				var _this = this;
				this.$swal({
					title: "Delete Dependent?",
					text: "This action cannot be undone",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						this.proceedDeleteDependent(index)
					}
				});
			},

			proceedDeleteDependent: function(index){
				var _this = this;
				var dependent_id = this.principal.dependents[index].ID
				var dependentName = this.principal.dependents[index].LAST_NAME + ", " + this.principal.dependents[index].OTHER_NAMES

				instance.default.delete('/principal/dependent', { data: {id: dependent_id } }).then(res => {
					if(res.data.status == "success"){
						this.$notify({
							group: 'foo',
							title: 'Success',
							text: "Successfully deleted dependent: " + dependentName + " from " + _this.fullname + " profile",
							type: "success"
						});

						this.principal.dependents.splice(index, 1)
					}
				})
				.catch(error => {
					var errorMessage = ""
					if(error.reponse){
						errorMessage = error.response.data.message
					}else if(error.request){
						var errorObj = JSON.parse(error.request.response)
						errorMessage = error.request.statusText + "<br/>" + errorObj.message
					}else{
						errorMessage = error.request
					}

					this.$notify({
						group: 'foo',
						title: 'Error',
						text: errorMessage,
						type: "danger"
					});
				});
			},

			clearDependentModal: function(){
				var _this = this

				_this.modal.dependent.id = ""
				_this.modal.dependent.lastName = ""
				_this.modal.dependent.relationshipType  = ""
				_this.modal.dependent.passport  = ""
				_this.modal.dependent.otherNames  = ""
				_this.modal.dependent.country  = ""
				_this.modal.dependent.dob  = ""
				_this.modal.dependent.employment  = ""
				_this.modal.dependent.imageURL = ""
				_this.modal.dependent.imageFile = ""
				_this.modal.dependent.editIndex = ""
				_this.options.imageProps.blank = true
			},

			cancelDependentModal: function(){
				this.clearDependentModal();
			}
		}
	}
</script>

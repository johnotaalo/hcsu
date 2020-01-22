<template>
	<div>
		<div class="row">
			<div class="col mb-3">
				<h6 class="header-pretitle">Domestic Worker</h6>
				<h1 class="header-title">{{ form.lastName }}, {{ form.otherNames }}</h1>
			</div>

			<div class="col-12 col-md-auto mt-2 mt-md-0 mb-md-3">
				<h6 class="header-pretitle">HOST COUNTRY ID</h6>
				<h1 class="header-title">{{ form.host_country_id }}</h1>
			</div>
		</div>

		<div class="row">
			<div class="col-md-9">
				<b-card>
					<domestic-worker v-model="form" :searchPrincipal="false" :principaldata="principal"></domestic-worker>
				</b-card>
			</div>

			<div class="col-md-3">
				<b-card title="Principal Details">
					<h5>Name</h5>
					<p>{{ principal.LAST_NAME }}, {{ principal.OTHER_NAMES }}</p>

					<h5>Organization</h5>
					<p  v-if="principal.latest_contract">{{ principal.latest_contract.ACRONYM }}</p><p v-else>N/A</p>
				</b-card>

				<b-button variant="primary" size="sm" block @click="updateData">Update Data</b-button>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../core/Form'
	import DomesticWorker from '../../components/domestic-worker/DomesticWorker'
	export default {
		components: { DomesticWorker },
		data(){
			return {
				principal: {},
				form: new Form({
					id: "",
					host_country_id: this.$route.params.id,
					principalId: "",
					lastName: "",
					otherNames: "",
					email: "",
					phone: "",
					placeOfBirth: "",
					dateOfBirth: "",
					employmentDate: "",
					nationality: "",
					rno: "",
					address: "",
					editIndex: "",
					passports: []
				})
			}
		},
		mounted(){
			this.getDomesticWorker()
		},
		methods: {
			getDomesticWorker(){
				axios.get(`/api/principal/domesticworker/get/${this.form.host_country_id}`)
				.then(res => {
					this.principal = res.data.principal

					this.form.id = res.data.id
					this.form.lastName = res.data.LAST_NAME
					this.form.otherNames = res.data.OTHER_NAMES
					this.form.principalId = res.data.PRINCIPAL_ID
					this.form.email = res.data.EMAIL
					this.form.phone = res.data.PHONE_NUMBER
					this.form.placeOfBirth = res.data.PLACE_OF_BIRTH
					this.form.dateOfBirth = res.data.DATE_OF_BIRTH
					this.form.employmentDate = res.data.CONTRACT_START_DATE
					this.form.nationality = res.data.NATIONALITY
					this.form.rno = res.data.R_NO
					this.form.address = res.data.ADDRESS
					this.form.passports = _.map(res.data.all_passports, (passport) => {
						const issuedate = this.subtractDateFix(passport.ISSUE_DATE)
						const expirydate = this.subtractDateFix(passport.EXPIRY_DATE)

						return {
							passportType: passport.PASSPORT_TYPE,
							passportNo: passport.PASSPORT_NO,
							place_issue: passport.PLACE_OF_ISSUE,
							date_issue: passport.ISSUE_DATE,
							expiry_date: passport.EXPIRY_DATE,
							country_issue: {
								id: passport.COUNTRY_OF_ISSUE,
								label: passport.actual_country_of_issue.official_name
							}
						}
					})
				});
			},
			subtractDateFix(dateString){
				let _date = new Date()
					_date.setYear(dateString.slice(0, 4))
					_date.setMonth(parseInt(dateString.slice(5, 7)) - 1)
					_date.setDate(dateString.slice(8, 10))

				return _date
			},

			updateData(){
				this.form.put('/principal/domesticworker/edit')
				.then(res => {
					this.$notify({
						group: 'foo',
						title: 'Success',
						text: "Successfully updated domestic worker",
						type: "success"
					});
				})
				.catch(error => {
					this.$notify({
						group: 'foo',
						title: 'Error',
						text: error.message,
						type: "error"
					});
				})
			}
		}
	}
</script>
<template>
	<div>
		<div class="header">
			<div class="header-body">
				<div class="row align-items-center">
					<div class="col">
						<!-- Pretitle -->
						<h6 class="header-pretitle">
							RETURNED PLATES
						</h6>
						<!-- Title -->
						<h1 class="header-title">
							CREATE RNP LIST
						</h1>
					</div>
				</div> <!-- / .row -->
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<div class="row align-items-center">
					<div class="col">
						<b-button class="btn btn-default" :to="{ name: 'rnp-list' }"><i class="fe fe-arrow-left"></i>&nbsp;&nbsp;Back to List</b-button>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md">
						<b-form-group label="RNP Date">
							<b-input type="date" v-model="form.rnpDate"/>
						</b-form-group>
					</div>
				</div>
				<div class="row align-items-center">
					<div class="col"></div>
					<div class="col-auto">
						<b-button variant="outline-primary" size="sm"  @click="addRow(form.returnedPlates)"><i class="fe fe-plus"></i>&nbsp;Add Row</b-button>
					</div>
				</div>

				<div class="row">
					<div class="col-md">
						<div v-if="form.returnedPlates.length">
							<plate-row v-for="(row, index) in form.returnedPlates" :key="row.id" v-model="form.returnedPlates[index]" @duplicate="duplicateRow($event, form.returnedPlates)"  @remove="removeRow($event, form.returnedPlates)"></plate-row>
							<b-button variant="primary" size="sm" @click="submitData">Create RNP</b-button>
						</div>
					</div>
				</div>


			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	import PlateRow from './components/PlateRow'
	import Form from '../../../../core/Form'
	import rowForm from '../../../../mixins/rowForm'
	export default{
		components: { PlateRow },
		mixins: [rowForm],
		data(){
			return {
				form: new Form({
					rnpDate: "",
					returnedPlates: []
				})
			}
		},
		methods: {
			submitData: function(){
				var plates = this.form.returnedPlates;
				var newPlates = _.map(plates, (plate) => {
					if (plate.clientType == "agency") {
						return {
							id: plate.selectedAgency.HOST_COUNTRY_ID,
							plateNo: plate.plateNo,
							measurements: plate.measurements,
							clientType: plate.clientType
						}
					}else if(plate.clientType == "staff"){
						return {
							id: plate.selectedStaff.HOST_COUNTRY_ID,
							plateNo: plate.plateNo,
							measurements: plate.measurements,
							clientType: plate.clientType
						}
					}else if(plate.clientType == "dependant"){
						return {
							id: plate.selectedDependent.HOST_COUNTRY_ID,
							plateNo: plate.plateNo,
							measurements: plate.measurements,
							clientType: plate.clientType
						}
					}
				})

				this.form.returnedPlates = newPlates
				// exit;
				this.form.post('vehicle/plates/returned/create')
				.then(res => {
					this.$swal(`Success`, "The RNP was successfully created!", "success")
					// this.$router.push({ name: 'rnp-list'})
				})
				.catch( error => {
					this.$swal(`Error`, error.message, "error")
					var errorString = "";
					if(error.errors){
						errorString = "<ul>";
						_.each(error.errors, (errorArr, k) => {
							errorString += `<li>${k}</li>`;
							errorString += "<ul>";
							_.each(errorArr, (e) => {
								errorString += `<li>${e}</li>`;
							})
							errorString += "</ul>";
						})
						errorString += "</ul>";

						this.$notify({
							group: 'foo',
							title: 'Validation Errors',
							text: errorString,
							duration: -1,
							type: "error"
						});
					}
				})
			}
		}
	}
</script>
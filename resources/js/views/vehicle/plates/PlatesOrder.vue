<template>
	<div>
		<div class="header">
			<div class="header-body">
				<div class="row align-items-center">
					<div class="col">
						<!-- Pretitle -->
						<h6 class="header-pretitle">
							Vehicle Plates
						</h6>
						<!-- Title -->
						<h1 class="header-title">
							Order Plates
						</h1>
					</div>
				</div> <!-- / .row -->
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<div class="row align-items-center">
							<div class="col">

								<!-- Title -->
								<h4 class="card-header-title">
									Orders
								</h4>

							</div>
							<div class="col-auto">

								<!-- Button -->

							</div>
						</div>
					</div>
					<div class="card-body">
						<loading :active.sync="loading" :can-cancel="false" :is-full-page="true"></loading>
						<b-form-group label="Plates Batch">
							<b-form-radio-group id="radio-group-batch" v-model="form.platesBatch" name="batch">
								<b-form-radio name="some-radios" value="single">Single</b-form-radio>
								<b-form-radio name="some-radios" value="multiple">Multiple (Bulk Plates Order)</b-form-radio>
							</b-form-radio-group>
						</b-form-group>

						<div v-if="form.platesBatch == 'single'">
							<div class="row">
								<div class="col-md">
									<label>Prefix</label>
									<b-select :options="prefixes" v-model = "form.prefix"></b-select>
								</div>

								<div class="col-md">
									<label>Plate Number</label>
									<b-input type="number" min = "1" max="999" v-model = "form.plate"/>
								</div>

								<div class="col-md">
									<label>Suffix</label>
									<b-select :options="suffix" v-model = "form.suffix"></b-select>
								</div>
							</div>

							<b-button class = "mt-3" size="sm" variant="primary" @click="addPlate">Add Plate</b-button>
						</div>
						<div v-else>
							<div v-if="uploadForm.plates.length > 0">
								<plate-row v-for="(row, index) in uploadForm.plates" :key="row.id" v-model="uploadForm.plates[index]" @remove="removeRow($event, uploadForm.plates)" :organisations="prefixes" :suffixes="suffix"></plate-row>
							</div>
							<div class="text-center" v-else>
								<h4>No plates order added yet. Click on add row below to add a row.</h4>
							</div>

							<div class="row">
								<div class="col">
									<b-link class="text-danger" @click="addRow(uploadForm.plates)"><i class="fe fe-plus"></i>&nbsp;Add Row</b-link>
								</div>
								<div class="col-auto">
									<b-button @click="uploadList">Upload Bulk List</b-button>
								</div>
							</div>
							
						</div>
						<!-- <div>
							<table>
								<tr v-for="order in platesOrder">
									<td>
										<v-select></v-select>
									</td>
									<td>
										<input />
									</td>
								</tr>
							</table>
						</div> -->
					</div>
				</div>
			</div>
			<!-- <div class="col-md-5"></div> -->
		</div>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../../core/Form'
	import rowForm from '../../../mixins/rowForm'
	import PlateRow from './components/PlateRow'
	export default {
		components: { PlateRow },
		mixins: [ rowForm ],
		data(){
			return {
				loading: false,

				platesOrder: [],
				suffix: ['K', 'AK'],
				prefixes: [],
				form: new Form({
					platesBatch: 'single',
					prefix: '',
					suffix: '',
					plate: ''
				}),
				uploadForm: new Form({
					plates: []
				})
			}
		},
		created(){
			this.getPlatePrefixes()
		},
		methods: {
			getPlatePrefixes(){
				this.loading = true
				axios.get('api/vehicle/plates/agency/prefixes')
				.then(res => {
					this.loading = false
					this.prefixes = _.map(res.data, (prefix) => {
						return {
							text: prefix.prefix,
							value: {
								id: prefix.id,
								highest_numbers: prefix.highest_number
							}
						}
					})

					// this.prefixes.unshift({ text: "Please select a prefix", value: null })
				})
				.catch(error => {
					this.loading = false
					console.log(error)
				});
			},
			addPlate(){
				this.loading = true
				this.form.post('vehicle/plates/add')
				.then(res => {
					this.$swal("Successfully added plate", {
						icon: 'success'
					});
					this.loading = false
				})
				.catch(error => {
					this.$swal(error.message, {
						icon: 'error'
					});

					this.loading = false
				});
			},
			uploadList(){
				this.loading = true
				this.uploadForm.post('vehicle/plates/add/bulk')
				.then(res => {
					this.loading = false
					this.$swal("Successfully uploaded list", {
						icon: 'success'
					})
				})
				.catch(error => {
					this.loading = false
					this.$swal(error.message, {
						icon: 'error'
					})
					this.uploadForm.errors = error
					console.log(error.message)
				})
			}
		}
	}
</script>
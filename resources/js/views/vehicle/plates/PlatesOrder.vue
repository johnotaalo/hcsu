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
								<a @click="openComponent({}, 'add')" class="btn btn-sm btn-white">
									Add Plates
								</a>

							</div>
						</div>
					</div>
					<div class="card-body">
						<b-form-group label="Plates Batch">
							<b-form-radio-group id="radio-group-batch" v-model="form.platesBatch" name="batch">
								<b-form-radio name="some-radios" value="single">Single</b-form-radio>
								<b-form-radio name="some-radios" value="multiple">Multiple</b-form-radio>
							</b-form-radio-group>
						</b-form-group>

						<div v-if="form.platesBatch == 'single'">
							<div class="row">
								<div class="col-md">
									<label>Prefix</label>
									<b-select></b-select>
								</div>

								<div class="col-md">
									<label>Plate Number</label>
									<b-input type="number" min = "1" max="999"/>
								</div>

								<div class="col-md">
									<label>Suffix</label>
									<b-select :options="suffix"></b-select>
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
	export default {
		data(){
			return {
				platesOrder: [],
				suffix: ['K', 'AK'],
				form: new Form({
					platesBatch: '',
					prefix: '',
					suffix: '',
					plate: ''
				})
			}
		}
	}
</script>
<template>
	<div>
		<div class="row">
			<div class="col-md">
				<div class="form-group">
					<label for="agency">Agency</label>
					<v-select class="is-invalid" :options="agencies" v-model="value.agency" :state="$v.value.agency.$error"></v-select>
					<div v-if="$v.value.agency.$error" class="my-invalid-feedback">Please provide an agency.</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md">
				<div class="form-group">
					<label for="contract_type">Contract Type</label>
					<v-select id="contract_type" :options="contractTypes" v-model="value.contractType"></v-select>
					<div v-if="$v.data.contractType.$error" class="my-invalid-feedback">Please provide a contract type.</div>
				</div>
				<div class="form-group">
					<label for="grade">Grade</label>
					<v-select id="grade" :options="grades" v-model="value.grade"></v-select>
					<div v-if="$v.data.grade.$error" class="my-invalid-feedback">Please provide a grade.</div>
				</div>
				<div class="form-group">
					<label for="designation">Designation</label>
					<!-- <b-input v-model="data.designation" id="designation" /> -->
					<v-select id="designation" :options="designations" v-model="value.designation" class = "mb-2"></v-select>
					<div v-if="$v.data.designation.$error" class="my-invalid-feedback">Please provide a designation.</div>
				</div>
			</div>
			<div class="col-md">
				<div class="form-group">
					<label for="index_no">Index No.</label>
					<b-input v-model="value.indexNo" id="index_no" />
					<div v-if="$v.data.indexNo.$error" class="my-invalid-feedback">Please provide an Index No.</div>
				</div>

				<div class="form-group">
					<label for="functional_title">Functional Title</label>
					<b-input v-model="value.functionalTitle" id="functional_title" />
					<div v-if="$v.data.functionalTitle.$error" class="my-invalid-feedback">Please provide a functional title.</div>
				</div>
			</div>
		</div>

		<b-button size="sm" variant="success" class = "mb-3" @click="toggleDesignation" :disabled="showDesignation"><i class = "fe fe-plus"></i>&nbsp;Add Designation</b-button>
		<div class="row">
			<div class="col-md-12">
				<designation v-if="showDesignation" @cancel="toggleDesignation" @saved="refreshDesignations" :grade="value.grade"></designation>
			</div>
		</div>
		

		<div class="row">
			<legend>Contract Duration</legend>
			<div class="col-md">
				<div class="form-group">
					<label for="from">From</label>
					<datetime value-zone="UTC+3" v-model="value.contractFrom" input-id="from" input-class="form-control"></datetime>
					<div v-if="$v.data.contractFrom.$error" class="my-invalid-feedback">Please provide a contract start date.</div>
				</div>
			</div>
			<div class="col-md">
				<div class="form-group">
					<label for="to">To</label>
					<datetime value-zone="UTC+3" v-model="value.contractTo" input-id="to" input-class="form-control" :min-datetime="minDateTime"></datetime>
					<div v-if="$v.data.contractTo.$error" class="my-invalid-feedback">Please provide a contract end date.</div>
				</div>
			</div>
		</div>	
	</div>
</template>

<script type="text/javascript">
	import { Datetime } from 'vue-datetime'
	import Designation from './Designation'
	import { required, minLength, between, email } from 'vuelidate/lib/validators'

	export default {
		name: "ContractDetails",
		components: {datetime: Datetime, Designation},
		props: {
			'value': { type: null, default: null },
			agencies: {
				type: Array, default: () => { return [] }
			},
			contractTypes: {
				type: Array, default: () => { return [] }
			},
			grades: {
				type: Array, default: () => { return [] }
			},
			designations: {
				type: Array, default: () => { return [] }
			}
		},
		data(){
			return {
				showDesignation: false,
				data: {
					agency : "",
					contractType : "",
					grade : "",
					designation : "",
					indexNo : "",
					functionalTitle : "",
					contractFrom : "",
					contractTo : ""
				}
			}
		},
		validations: {
			data: {
				agency: {
					required
				},
				contractType: {
					required
				},
				indexNo: {
					required
				},
				designation: {
					required
				}, 
				functionalTitle: {
					required
				},
				contractFrom: {
					required
				},
				contractTo: {
					required
				},
				grade: {
					required
				}
			},
			value: {
				agency: {
					required
				},
				contractType: {
					required
				},
				indexNo: {
					required
				},
				designation: {
					required
				}, 
				functionalTitle: {
					required
				},
				contractFrom: {
					required
				},
				contractTo: {
					required
				},
				grade: {
					required
				}
			}
		},
		methods: {
			validate(){
				this.$v.value.$touch();
				var isValid = !this.$v.value.$invalid
				this.$emit('on-validate', this.$value, isValid)
				return isValid
			},
			toggleDesignation(){
				this.showDesignation = !this.showDesignation
			},
			refreshDesignations(designation){
				// this.designations.push(designation)
				this.value.designation = designation
				this.$emit('refresh', designation)
				this.toggleDesignation()
			}
		},
		computed: {
			filteredDesignations: function(){
				var designations = this.designations;
				if(this.value.grade){
					var filtered = _.map(designations[this.value.grade.label], designation => ({
						id: designation.ID,
						label: designation.DESIGNATION
					}))
					return filtered
				}
				return []
			},
			maxDateTime: function(){
				return this.$moment().format()
			},
			minDateTime: function(){
				return this.$moment(this.data.contractFrom).add(3, 'months').format()
			}
		},
		watch: {
			'value.grade': function(newVal, oldVal){
				this.showDesignation = false
			}
		}
	}
</script>
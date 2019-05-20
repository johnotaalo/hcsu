<template>
	<div>
		<div class="row">
			<div class="col">
				<b-button @click="addRow(passports)" class="float-right" variant="primary" size="sm"><i class="fe fe-plus-circle mr-2"></i>Add Passport</b-button>
			</div>
		</div>

		<div class="row">
			<passport-row v-for="(row, index) in passports" :key="row.id" :passportTypes="passportTypes" :countries="countries" v-model="passports[index]"></passport-row>
		</div>
	</div>
</template>

<script type="text/javascript">
	import PassportRow from '../principal/PassportRow'
	import rowForm from '../../mixins/rowForm'
	export default {
		name: "Passports",
		components: { PassportRow },
		mixins: [rowForm],
		props: {
			'value': { type: null, default: null },
			'errors': { type: Object, default: null },
			'index': { type: null, default: null },
			passportTypes: {
				type: Array, default: () => { return [] }
			},
			countries: {
				type: Array, default: () => { return [] }
			},
			passports: {
				type: Array, default: () => { return [] }
			}
		},
		data() {
			return {
				// passportData: []
			}
		},
		created(){
			console.log(this.passports)
		}
	}
</script>
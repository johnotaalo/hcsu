<template>
	<div class="row align-items-center">
		<div class="col-md">
			<b-form-group label="Prefix">
				<b-select :options="organisations" v-model="value.prefix"></b-select>
			</b-form-group>
		</div>
		<div class="col-md">
			<b-form-group label="Suffix">
				<b-select :options="suffixes" v-model="value.suffix"></b-select>
			</b-form-group>
		</div>

		<div class="col-md">
			<b-form-group label="Start From">
				<b-input type="number" min = "0" max = "999" v-model="value.start"/>
			</b-form-group>
		</div>

		<div class="col-md">
			<b-form-group label="End At">
				<b-input type="number" min = "0" max = "999" v-model="value.end"/>
			</b-form-group>
		</div>

		<div class="col-auto">
			<b-button variant="danger" size="sm" @click="removeRow"><i class = "fe fe-x"></i>&nbsp;Remove</b-button>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default {
		props: {
			value: { type: null, default: null },
			errors: { type: Object, default: null },
			index: { type: null, default: null },
			organisations: { type: Array, default: () => { return [] } },
			suffixes: { type: Array, default: () => { return [] }  }
		},
		data(){
			return {

			}
		},
		methods: {
			removeRow () {
				this.$emit('remove', this.value.id)
			},
			getMaxNumber(){
				// alert('Max Number');
				var suffix, prefix;

				suffix = this.value.suffix
				prefix = this.value.prefix

				if (suffix && prefix) {
					var numbers = prefix.highest_numbers
					
					var specificNumber = _.find(numbers, (o) => {
						return o.suffix == suffix
					})

					if(specificNumber){
						return this.value.start = specificNumber.plate_number + 1
					}
				}

				return this.value.start = 1
			}
		},
		watch: {
			'value.suffix': function(newVal, oldVal){
				this.getMaxNumber()
			},
			'value.prefix': function(newVal, oldVal){
				this.getMaxNumber()
			}
		}
	}
</script>
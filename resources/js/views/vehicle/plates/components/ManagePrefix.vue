<template>
	<div>
		<label>Enter Prefix</label>
		<b-input :value = "data.prefix" v-model="prefix"></b-input>

		<b-button variant="primary" @click="submitInformation" class="mt-3" size="sm" block>Submit</b-button>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../../../core/Form'
	export default {
		props: {
			data: { type: Object, default: {} }
		},
		data(){
			return {
				prefix: ''
			}
		},
		created(){
			this.prefix = this.data.prefix
		},
		methods: {
			submitInformation: function(){
				if(!_.isEmpty(this.data)){
					var form = new Form({ id: this.data.id, prefix: this.prefix });
					form.put('vehicle/plates/prefix')
					.then(res => {
						this.$emit('success')
					})
					.catch((error) => {
						// console.log(error)
						this.$emit('error', error)
					})
				}else{
					var form = new Form({ id: this.data.id, prefix: this.prefix });
					form.post('vehicle/plates/prefix')
					.then(res => {
						this.$emit('success')
					})
					.catch((error) => {
						// console.log(error)
						this.$emit('error', error)
					})
				}
			}
		}
	}
</script>
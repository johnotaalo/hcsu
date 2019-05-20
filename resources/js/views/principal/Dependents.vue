<template>
	<div>
		<div class="row">
			<div class="col-md">
				<b-button class="float-right" size="sm" variant="outline-primary" v-b-modal.dependent-modal><i class="fe fe-plus-circle"></i>&nbsp;Add Dependent</b-button>
			</div>
			
		</div>

		
	</div>
</template>

<script type="text/javascript">
	import { Datetime } from 'vue-datetime'

	export default {
		name: "Dependents",
		components: {datetime: Datetime},
		props: {
			'value': { type: null, default: null },
			relationshipTypes: {
				type: Array, default: () => { return [] }
			},
			countries: {
				type: Array, default: () => { return [] }
			}
		},
		data(){
			return {
				mainProps: { blank: true, blankColor: '#777', width: 100, height: 100, class: 'm1' }
			}
		}
	}
</script>
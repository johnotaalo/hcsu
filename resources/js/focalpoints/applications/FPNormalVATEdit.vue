<template>
	<div>
		<normal-vat :id="id"></normal-vat>
	</div>
</template>

<script type="text/javascript">
	import NormalVAT from '../../views/applications/NormalVAT'

	export default {
		components: { 'normal-vat': NormalVAT },
		data() {
			return {
				id: parseInt(this.$route.params.id)
			}
		}
	}
</script>
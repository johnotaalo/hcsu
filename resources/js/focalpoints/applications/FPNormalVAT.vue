<template>
	<div>
		<normal-vat applier="fp"></normal-vat>
	</div>
</template>

<script type="text/javascript">
	import NormalVAT from '../../views/applications/NormalVAT'

	export default {
		name: "FPNormalVAT",
		components: { 'normal-vat': NormalVAT }
	}
</script>
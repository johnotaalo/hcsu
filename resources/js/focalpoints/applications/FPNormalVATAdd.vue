<template>
	<div>
		<b-card>
			<normal-vat applier="fp"></normal-vat>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	import NormalVAT from '../../views/applications/NormalVAT'

	export default {
		components: { 'normal-vat': NormalVAT },
	}
</script>
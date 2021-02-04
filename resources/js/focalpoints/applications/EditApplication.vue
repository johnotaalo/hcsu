<template>
	<div>
		<application-form :id="id"></application-form>
	</div>
</template>

<script type="text/javascript">
	import ApplicationForm from './ApplicationForm'
	export default {
		components: { ApplicationForm },
		data() {
			return {
				id: parseInt(this.$route.params.id)
			}
		}
	}
</script>
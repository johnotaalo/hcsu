<template>
	<div>
		<b-card>
			<iframe src="https://app.powerbi.com/view?r=eyJrIjoiYmQxMTlhZmItMzRiZS00NTlhLWE0ZTgtZmVkNGE5Y2MxODE2IiwidCI6IjBmOWUzNWRiLTU0NGYtNGY2MC1iZGNjLTVlYTQxNmU2ZGM3MCIsImMiOjh9" frameborder="0" allowFullScreen="true" style="width: 100%;height:100vh;"></iframe>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	export default {
		mounted(){
			this.$parent.isContainer = false
		}
	}
</script>
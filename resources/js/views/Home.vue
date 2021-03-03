<template>
	<div>
		<b-card>
			<iframe src="https://app.powerbi.com/reportEmbed?reportId=89294e1d-99d2-416f-9b2d-4ede3ae6c7c3&autoAuth=true&ctid=0f9e35db-544f-4f60-bdcc-5ea416e6dc70&config=eyJjbHVzdGVyVXJsIjoiaHR0cHM6Ly93YWJpLW5vcnRoLWV1cm9wZS1yZWRpcmVjdC5hbmFseXNpcy53aW5kb3dzLm5ldC8ifQ%3D%3D" frameborder="0" allowFullScreen="true" style="width: 100%;height:100vh;"></iframe>
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
<template>
	<div>
		<b-card>
			<b-card-title>
				<h3>VAT Application Case #{{ data.CASE_NO }}</h3>
				<div>
					<span v-if="data.STATUS == 'Pending'">
						<span class="text-warning">Pending</span>
					</span>
					<span v-if="data.STATUS == 'Claimed'">
						<span class="text-primary">Claimed</span>
					</span>
					<span v-if="data.STATUS == 'Not Approved'">
						<span class="text-danger">Not Approved</span>
					</span>

					<span v-if="data.APPROVED == 1">
						<span v-if="data.STATUS != 'Cancelled' && data.STATUS != 'Canceled'">
							<span class="text-success">{{ data.STATUS }}</span>
						</span>

						<span class = "text-danger" v-if="data.STATUS == 'Cancelled' || data.STATUS == 'Canceled'">Canceled</span>
					</span>
				</div>
			</b-card-title>

			<b-card-body class="clear-fix">
				<p>Client: {{ data.data.client.name }}</p>
			</b-card-body>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	export default {
		data(){
			return {
				id: parseInt(this.$route.params.id),
				data: {}
			}
		},
		mounted(){
			this.getData()
		},
		methods: {
			getData: function(){
				axios.get(`/api/focal-points/vat/user-application/${this.id}`)
				.then((res) => {
					this.data = res.data
				})
				.catch((error) => {
					alert(error.message)
				})
			}
		}
	}
</script>
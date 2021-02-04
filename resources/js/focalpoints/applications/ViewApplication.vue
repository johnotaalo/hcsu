<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="row align-items-center">
					<div class="col-auto">
						<router-link class="btn btn-sm btn-primary" :to="{ path: $routerHistory.previous().path }">Back</router-link>
					</div>
					<div class="col">
						
						<h4 class="card-header-title">
							Application Case #: <span v-if="application.APP_NUMBER">{{ application.APP_NUMBER }}</span><span v-else>Not Assigned</span>
						</h4>
					</div>
					<div class="col-auto">
						<span class = "goal-status" v-bind:class= "{ 'text-danger' : application.STATUS == 'CANCELED', 'text-primary' : application.STATUS == 'QUERIED', 'text-warning' : application.STATUS == 'SUBMITTED', 'text-success' : application.STATUS == 'ASSIGNED' }">
							{{ application.STATUS }}
						</span>
					</div>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th>Process</th>
							<td>{{ application.process.PRO_TITLE }}</td>
						</tr>
						<tr>
							<th>Client</th>
							<td>
								[{{ application.applicant_type }}]
								<span v-if="application.applicant_type == 'staff'">{{ application.applicant_details.LAST_NAME }}, {{ application.applicant_details.OTHER_NAMES }}</span>
								<span v-if="application.applicant_type == 'agency'">{{ application.applicant_details.ACRONYM }}</span>
							</td>
						</tr>
						<tr>
							<th>Uploaded Files</th>
							<td>
								<ul v-if="application.files.length">
									<li v-for="(file, key) in application.files" :key="file.id"><a :href="`uploads/${file.id}`" target="_blank">{{ file.FILE_DESCRIPTION }}</a></li>
								</ul>
								<span v-else>No Files Uploaded</span>
							</td>
						</tr>
						<tr>
							<th>Comments</th>
							<td>
								<p v-if="application.COMMENT">{{ application.COMMENT }}</p>
								<p v-else>N/A</p>
							</td>
						</tr>
						<tr>
							<th>Date Created</th>
							<td>{{ application.created_at }}</td>
						</tr>
						<tr>
							<th>Last Updated</th>
							<td>{{ application.updated_at }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default{
		name: "ViewApplication",
		data(){
			return {
				application: {
					process: {},
					files: []
				}
			}
		},
		mounted(){
			this.getApplicationData(this.$route.params.id)
		},
		methods: {
			getApplicationData(id){
				this.$store.commit('loadingOn');
				axios.get(`/api/focal-points/applications/get/${id}`)
				.then((res) => {
					this.$store.commit('loadingOff');
					this.application = res.data
				}).catch((error) => {
					this.$store.commit('loadingOff');
					alert(error.message)
				})
			}
		}
	}
</script>
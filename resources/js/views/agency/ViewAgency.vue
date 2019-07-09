<template>
	<div>
		<!-- <b-card no-body>
			<b-tabs pills card fill>
				<b-tab title="Agency Details" active>

				</b-tab>
				<b-tab title="Focal Points">
					
				</b-tab>
			</b-tabs>
		</b-card> -->
		<div class="header">
			<!-- <img src="/img/covers/team-cover.jpg" class="header-img-top" alt="..."> -->
			<div class="container-fluid">
				<div class="header-body mt-n1 mt-md-n2">
					<div class="row align-items-end">
						<div class="col-auto">
							<div class="avatar avatar-xxl header-avatar-top">
								<img src="/img/unep.png" alt="..." class="avatar-img rounded border border-4 border-body" style="background-color: #f5f5f5;">
							</div>
						</div>

						<div class="col mb-3 ml-n3 ml-md-n2">
							<h6 class="header-pretitle">AGENCY</h6>
							<h1 class="header-title">{{ agency.ACRONYM }}</h1>
							<!-- <h3>{{ agency.AGENCYNAME }}</h3> -->
						</div>
					</div>
				</div>
				<div class="row align-items-center mt-2">
					<div class="col-md-9">
						<b-card no-body>
							<b-tabs card>
								<b-tab title="Principal Details" active>
								</b-tab>
								<b-tab title="Focal Points">
								</b-tab>
							</b-tabs>
						</b-card>
					</div>

					<div class="col-md-3">
						<b-card>
							<div class="row align-items-center">
								<div class="col">
									<!-- Title -->
									<h5 class="mb-0">
										Active Clients
									</h5>
								</div>
								<div class="col-auto">
									<small class="text-muted">
									129
									</small>
								</div>
							</div>
							<hr>
							<div class="row align-items-center">
								<div class="col">
									<!-- Title -->
									<h5 class="mb-0">
										Focal Points
									</h5>
								</div>
								<div class="col-auto">
									<small class="text-muted">
									{{ agency.focal_points.length }}
									</small>
								</div>
							</div>
							<hr>
							<div class="row align-items-center">
								<div class="col">
									<!-- Title -->
									<h5 class="mb-0">
										Processes
									</h5>
								</div>
								<div class="col-auto">
									<small class="text-muted">
									0
									</small>
								</div>
							</div>
						</b-card>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default{
		data(){
			return {
				agencyId: this.$route.params.id,
				agency: {}
			}
		},
		created(){
			this.agency.focal_points = [];
			this.getAgencyDetails()
		},
		methods: {
			getAgencyDetails(){
				axios.get(`/api/agencies/get/${this.agencyId}`)
				.then(res => {
					this.agency = res.data
				})
			}
		}
	}
</script>
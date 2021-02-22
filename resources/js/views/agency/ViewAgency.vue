<template>
	<div>
		<div class="header">
			<!-- <img src="/img/covers/team-cover.jpg" class="header-img-top" alt="..."> -->
			<div class="container-fluid">
				<div class="header-body mt-n1 mt-md-n2">
					<div class="row align-items-end">
						<div class="col-auto">
							<div class="avatar avatar-xxl header-avatar-top">
								<b-img v-if="agency.logo_link"class="avatar-img rounded border border-4 border-body" :src="`/agency/logo/${agency.AGENCY_ID}`" style="background-color: #F2F2F2;"/>
								<b-img v-else class="avatar-img rounded border border-4 border-body" src="/images/unlogo.jpg" />
							</div>
						</div>

						<div class="col mb-3 ml-n3 ml-md-n2">
							<h6 class="header-pretitle">AGENCY</h6>
							<h1 class="header-title">{{ agency.ACRONYM }}</h1>
							<!-- <h3>{{ agency.AGENCYNAME }}</h3> -->
						</div>
					</div>
				</div>
				<div class="row mt-2">
					<div class="col-md-9">
						<b-card no-body>
							<b-tabs card>
								<b-tab title="Agency Details" active>
									<table class="table table-bordered">
										<tr>
											<td>
												<b>Host Country ID</b>
											</td>
											<td>
												{{ agency.HOST_COUNTRY_ID }}
											</td>
										</tr>
										<tr>
											<td>
												<b>Agency Name</b>
											</td>
											<td>
												{{ agency.AGENCYNAME }}
											</td>
										</tr>
										<tr>
											<td>
												<b>Acronym</b>
											</td>
											<td>
												{{ agency.ACRONYM }}
											</td>
										</tr>
										<tr>
											<td>
												<b>Location</b>
											</td>
											<td>
												{{ agency.LOCATION }}
											</td>
										</tr>
										<tr>
											<td>
												<b>Physical Address</b>
											</td>
											<td>
												{{ agency.PHY_ADDRESS }}
											</td>
										</tr>
									</table>
								</b-tab>
								<b-tab title="Focal Points">
									<div class="row">
										<div class="col-md">
											<b-table class="card-table" :fields="table.focalPoints.fields" :items="focalPoints" show-empty>
												<template #cell(NAME)="data">
													{{ data.item.LAST_NAME }}, {{ data.item.OTHER_NAMES }}
												</template>
											</b-table>
										</div>
									</div>
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
				agency: {},
				focalPoints: [],
				table: {
					focalPoints: {
						fields: ["INDEX_NO", "NAME", "EXTENSION", "MOBILE_NO", "EMAIL"]
					}
				}
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

					this.focalPoints = res.data.focal_points
				})
			},
			
		}
	}
</script>
<template>
	<div>
		<!-- <div class="card">
			<div class="card-header">
				<div class="row align-items-center">
					<div class="col">
						<h4 class="card-header-title">
						<i class="fe fe-truck mr-2"></i>Vehicles
						</h4>

					</div>
					<div class="col-auto">
						<b-button class= "float-right" :to="{ name: 'principal.add' }" variant="outline-primary" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Add Vehicle</b-button>
					</div>
				</div>
			</div>
			<div class="card-body">
				<vehicle-list></vehicle-list>
			</div>
		</div> -->
		<!-- <span class="badge badge-primary ml-auto">23</span> -->
		<div class="row">
			<div class="col-md-2">
				<!-- <div class="card">
					<div class="card-body"> -->
						<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light" style="position: relative; border-width: 0;">

							<!-- Toggler -->
							<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarVertical" aria-controls="navbarVertical" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-toggler-icon"></span>
							</button>
							<!-- Collapse -->
							<div class="collapse navbar-collapse" id="navbarVertical">

								<!-- Navigation -->
								<ul class="navbar-nav mb-md-3">
									<li class="nav-item">
										<router-link class="nav-link" :to="{ name: 'vehicle-home' }">
											<i class="fe fe-home"></i> Home
										</router-link>
									</li>
									<li class="nav-item">
										<router-link class="nav-link" :to="{ name: 'vehicle-plates' }" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="sidebarPlates">
											<i class="fe fe-hash"></i> Plates
										</router-link>
										<div class="collapse show" id="sidebarPlates">
											<ul class="nav nav-sm flex-column">
												<li class="nav-item">
													<router-link class="nav-link" :to="{ name: 'vehicle-plates-prefix' }">
														<i class="fe fe-circle"></i> Manage Prefixes
													</router-link>
												</li>
												<li class="nav-item">
													<router-link class="nav-link" :to="{ name: 'vehicle-plates-list' }">
														<i class="fe fe-list"></i> Plates List
													</router-link>
												</li>
												<li class="nav-item">
													<router-link class="nav-link" :to="{ name: 'vehicle-plates-orders' }">
														<i class="fe fe-shopping-bag"></i> Plate Orders
													</router-link>
												</li>

												<li class="nav-item">
													<router-link class="nav-link" :to="{ name: 'rnp-list' }">
														<i class="fe fe-corner-up-left"></i> Returned Number Plates
													</router-link>
												</li>
											</ul>
										</div>
									</li>
									<li class="nav-item">
										<router-link class="nav-link" :to="{ name: 'tims-list' }" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="sibebarTims">
											<i class="fe fe-hash"></i> Tims Registation
										</router-link>
										<div class="collapse show" id="sibebarTims">
											<ul class="nav nav-sm flex-column">
												<li class="nav-item">
													<router-link class="nav-link" :to="{ name: 'tims-list' }">
														<i class="fe fe-file-text"></i> Tims Registrations
													</router-link>
												</li>
												<li class="nav-item">
													<router-link class="nav-link" :to="{ name: 'tims-registration' }">
														<i class="fe fe-plus"></i> New Registration
													</router-link>
												</li>
											</ul>
										</div>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#">
											<i class="fe fe-truck"></i> Vehicles Database
										</a>
									</li>
								</ul>

							</div> <!-- / .navbar-collapse -->

						</nav>
					<!-- </div> -->
				<!-- </div> -->
			
			</div>
			<div class="col-md-10">
				<router-view></router-view>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	import VehicleList from './vehicle/VehicleList'
	export default {
		components: {VehicleList},
		data() {
			return {
			}
		},
		mounted(){
			this.$parent.isContainer = false
		},
	}
</script>
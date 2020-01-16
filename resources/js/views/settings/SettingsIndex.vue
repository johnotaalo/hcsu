<template>
	<div>
		<div class="row">
			<div class="col-md-3">
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
										<router-link class="nav-link" :to="{ name: 'settings-migration' }">
											<i class="fe fe-database"></i> Staff Data Migration
										</router-link>
									</li>

									<li class="nav-item">
										<router-link class="nav-link" :to="{ name: 'settings-templates' }">
											<i class="fe fe-file-text"></i> Document Templates
										</router-link>
									</li>

									<li class="nav-item">
										<router-link class="nav-link" :to="{ name: 'settings-ipmis-functionality' }">
											<i class="fe fe-globe"></i> IPMIS Functionality
										</router-link>
									</li>

								</ul>

							</div> <!-- / .navbar-collapse -->

						</nav>
					<!-- </div> -->
				<!-- </div> -->
			
			</div>
			<div class="col-md-9">
				<router-view></router-view>
			</div>
		</div>
	</div>
</template>


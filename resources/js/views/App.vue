<template>
	<div>
		<!-- <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
			<div class="container">
				<router-link class="navbar-brand" :to="{ name: 'home' }">HCSU Data Manager</router-link>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle Navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav mr-auto">

					</ul>
					<ul class="navbar-nav ml-auto">
						<li class="nav-item"><router-link class="nav-link" :to="{ name: 'home' }">Home</router-link></li>
						<li class="nav-item"><router-link class="nav-link" :to="{ name: 'principal' }">Principals</router-link></li>
						<li class="nav-item"><router-link class="nav-link" :to="{ name: 'agencies' }">Agencies</router-link></li>
						<li class="nav-item"><router-link class="nav-link" :to="{ name: 'vehicles' }">Vehicles</router-link></li>
					</ul>
				</div>
			</div>
		</nav>
 -->
		<nav class="navbar navbar-expand-lg navbar-light" id="topnav">
			<div class="container">

				<!-- Toggler -->
				<button class="navbar-toggler mr-auto" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				</button>

				<!-- Brand -->
				<a class="navbar-brand mr-auto" href="index.html">
					<img src="/img/unep.png" alt="..." class="navbar-brand-img">
				</a>

				<!-- Form -->
				<form class="form-inline mr-4 d-none d-lg-flex">
					<div class="input-group input-group-rounded input-group-merge" data-toggle="lists" data-lists-values="[&quot;name&quot;]">

						<!-- Input -->
						<input type="search" class="form-control form-control-prepended  dropdown-toggle search" data-toggle="dropdown" placeholder="Search" aria-label="Search">
						<div class="input-group-prepend">
							<div class="input-group-text">
								<i class="fe fe-search"></i>
							</div>
						</div>

						<!-- Menu -->
						<div class="dropdown-menu dropdown-menu-card">
							<div class="card-body">

								<!-- List group -->
								<div class="list-group list-group-flush list my-n3"></div>

							</div>
						</div> <!-- / .dropdown-menu -->

					</div>
				</form>

				<!-- User -->
				<div class="navbar-user">

					<!-- Dropdown -->
					<div class="dropdown mr-4 d-none d-lg-flex">

						<!-- Toggle -->
						<a href="#" class="text-muted" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<span class="icon active">
								<i class="fe fe-bell"></i>
							</span>
						</a>

						

					</div>

					<!-- Dropdown -->
					<div class="dropdown">

						<!-- Toggle -->
						<a href="#" class="avatar avatar-sm avatar-online dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<img src="/img/avatars/profiles/hcsu.svg" alt="..." class="avatar-img rounded-circle">
						</a>

						<!-- Menu -->
						<div class="dropdown-menu dropdown-menu-right">
							<a href="#" class="dropdown-item">Profile</a>
							<a href="#" class="dropdown-item">Settings</a>
							<hr class="dropdown-divider">
							<a href="#" class="dropdown-item">Logout</a>
						</div>

					</div>

				</div>

          <!-- Collapse -->
          <div class="collapse navbar-collapse mr-auto order-lg-first" id="navbar">

			<!-- Form -->
			<form class="mt-4 mb-3 d-md-none">
				<input type="search" class="form-control form-control-rounded" placeholder="Search" aria-label="Search">
			</form>

			<!-- Navigation -->
			<ul class="navbar-nav mr-auto">
				<li class="nav-item"><router-link class="nav-link" :to="{ name: 'home' }">Home</router-link></li>
				<li class="nav-item"><router-link class="nav-link" :to="{ name: 'principal' }">Principals</router-link></li>
				<li class="nav-item"><router-link class="nav-link" :to="{ name: 'agencies' }">Agencies</router-link></li>
				<li class="nav-item"><router-link class="nav-link" :to="{ name: 'vehicles' }">Vehicles</router-link></li>
			</ul>

          </div>

        </div> <!-- / .container -->
      </nav>
		<main class="py-4">
			<div class="container">
				<router-view></router-view>
				<notifications group="foo" />
			</div>
			
		</main>
	</div>
</template>
<script type="text/javascript">
	export default {}
</script>
<template>
	<div>
		<loading
		:active.sync="this.$store.state.loading > 0"
        :can-cancel="false"
        :is-full-page="true"
        :opacity="1"></loading>
		<div>
			<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light" id="sidebar" v-if="this.$store.state.isUserRetrieved">
				<div class="container-fluid">
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidebarCollapse" aria-controls="sidebarCollapse" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>

					<a class="navbar-brand" href="index.html">
						<img src="/images/UNLOGOBW.jpg" class="navbar-brand-img mx-auto" alt="...">
					</a>

					<p>{{ user.name }}</p>
					<P>{{ user.focal_point.agency.ACRONYM }}</P>

					<div class="navbar-user d-md-none">

						<!-- Dropdown -->
						<div class="dropdown">

							<!-- Toggle -->
							<a href="#" id="sidebarIcon" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<div class="avatar avatar-sm avatar-online">
									<img src="/images/no_avatar.svg" class="avatar-img rounded-circle" :alt="user.name">
								</div>
							</a>

							<!-- Menu -->
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="sidebarIcon">
								<!-- <a href="profile-posts.html" class="dropdown-item">Profile</a>
								<a href="settings.html" class="dropdown-item">Settings</a>
								<hr class="dropdown-divider"> -->
								<a href="/logout" class="dropdown-item">Logout</a>
							</div>

						</div>
					</div>

					<!-- Collapse -->
					<div class="collapse navbar-collapse" id="sidebarCollapse">
						<ul class="navbar-nav">

							<li class="nav-item">
								<router-link class="nav-link" :to="{ name: 'dashboard' }"><i class="fe fe-home"></i> Dashboard</router-link>
							</li>

							<li class="nav-item">
								<a class="nav-link" href="#sidebarComponents" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarComponents">
									<i class="fe fe-book-open"></i> Applications
								</a>
								<div class="collapse " id="sidebarComponents">
									<ul class="nav nav-sm flex-column">
										<li class="nav-item">
											<router-link class="nav-link" :to="{ name: 'applications.normal-vat' }">Normal VAT Application</router-link>
										</li>
									</ul>
								</div>
							</li>

							<li class="nav-item">
								<a class="nav-link" href="/logout"><i class="fe fe-lock"></i> Logout</a>
							</li>
						</ul>

						<!-- Push content down -->
						<div class="mt-auto"></div>

						<!-- User (md) -->
						<div class="navbar-user d-none d-md-flex" id="sidebarUser">

							<!-- Icon -->
							<a href="#sidebarModalActivity" class="navbar-user-link" data-toggle="modal">
								<span class="icon">
									<i class="fe fe-bell"></i>
								</span>
							</a>

							<!-- Dropup -->
							<div class="dropup">

								<!-- Toggle -->
								<a href="#" id="sidebarIconCopy" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<div class="avatar avatar-sm avatar-online">
										<img src="/images/no_avatar.svg" class="avatar-img rounded-circle" alt="">
									</div>
								</a>

								<!-- Menu -->
								<div class="dropdown-menu" aria-labelledby="sidebarIconCopy">
									<!-- <a href="profile-posts.html" class="dropdown-item">Profile</a>
									<a href="settings.html" class="dropdown-item">Settings</a>
									<hr class="dropdown-divider"> -->
									<a href="/logout" class="dropdown-item">Logout</a>
								</div>

							</div>

							<!-- Icon -->
							<a href="#sidebarModalSearch" class="navbar-user-link" data-toggle="modal">
								<span class="icon">
									<i class="fe fe-search"></i>
								</span>
							</a>

						</div>


					</div> <!-- / .navbar-collapse -->
				</div>
			</nav>

			<div class="main-content">
				<div class="header" v-if="showTitle">
					<div class="container-fluid">
						<div class="header-body">
							<div class="row align-items-end">
								<div class="col">
									<h6 class="header-pretitle">
										{{ pageSubtitle }}
									</h6>

									<h1 class="header-title">
										{{ pageTitle }}
									</h1>
								</div>
								<div class="col-auto">
									<div ></div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div v-bind:class="{ 'container-fluid' : !dashboard } ">
					<router-view></router-view>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default {
		props: {
			// dashboard: { type: Boolean, default: false }
		},
		data(){
			return {
				dashboard: false
			}
		},
		created(){
			// this.$route
			this.$store.dispatch('fetchCurrentUser').then(res => {
				console.log(res)
			});
		},
		computed: {
			pageTitle: function(){
				if(this.$route.meta.title)
					return this.$route.meta.title
				else
					return 'Focal Points Dashboard'
			},
			pageSubtitle: function(){
				if(this.$route.meta.title)
					return this.$route.meta.subtitle
				else
					return 'Overview'
			},
			user: function(){
				return this.$store.state.loggedInUser
			},
			showTitle: function(){
				if (this.$route.name == "dashboard" || this.$route.name == "applications.normal-vat") {
					return false
				}
				return true
			}
		},
		watch: {
			$route (to, from){
				this.dashboard = false;
			}
		}
	}
</script>
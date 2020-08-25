<template>
	<div v-if="$store.state.isUserRetrieved">
		<!-- <loading
		:active.sync="$store.state.loading > 0"
        :can-cancel="false"
        :is-full-page="true"
        :opacity="1"></loading> -->
		<nav v-if="!iframe" class="navbar navbar-expand-lg navbar-light" id="topnav">
			<div class = "container">

				<!-- Toggler -->
				<button class="navbar-toggler mr-auto" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				</button>

				<!-- Brand -->
				<a class="navbar-brand mr-auto" href="index.html">
					<img src="/images/UNLOGOBW.jpg" alt="..." class="navbar-brand-img">
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
							<a href="/logout" class="dropdown-item">Logout</a>
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
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true">Clients</a>
					<ul class="dropdown-menu">
						<li><router-link class="dropdown-item" :to="{ name: 'principal' }">Principals</router-link></li>
						<li><router-link class="dropdown-item" :to="{ name: 'agencies' }">Agencies</router-link></li>
						<li><router-link class="dropdown-item" :to="{ name: 'dependents.list' }">Dependents List</router-link></li>
						<li><router-link class="dropdown-item" :to="{ name: 'clients.other' }">Others (Meeting Participants etc.)</router-link></li>
					</ul>
				</li>
				<!-- <li class="nav-item"></li> -->
				<li class="nav-item"></li>
				<li class="nav-item"><router-link class="nav-link" :to="{ name: 'vehicles' }">Vehicles</router-link></li>
				<li class="nav-item"><router-link class="nav-link" :to="{ name: 'data-management' }">Data</router-link></li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true">Processes</a>
					<ul class="dropdown-menu">
						<li>
							<router-link class="dropdown-item" :to="{ name: 'blanket-vat' }">Blanket VAT</router-link>
						</li>
						<li>
							<router-link class="dropdown-item" :to="{ name: 'subprocesses-ipmis' }">Subprocesses</router-link>
						</li>
					</ul>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true">Exports</a>
					<ul class="dropdown-menu">
						<li>
							<router-link class="dropdown-item" :to="{ name: 'export-vat-list' }">VAT List</router-link>
						</li>
						<li>
							<router-link class="dropdown-item" :to="{ name: 'export-organization-data' }">Organization Data</router-link>
						</li>
					</ul>
				</li>
				<li class="nav-item"><router-link class="nav-link" :to="{ name: 'settings' }">Settings</router-link></li>
			</ul>

          </div>

        </div> <!-- / .container -->
      </nav>
		<main v-bind:class="{'py-4': showMainDIV}">
			<div v-bind:class="{ container: showMainDIV }" >
				<router-view></router-view>
				<notifications group="foo" />
			</div>

		</main>
	</div>
</template>
<script type="text/javascript">
	export default {
		props: {
			// iframe: { type: Boolean, default: false },
			// case: { type: String, default: null, required: false },
			// user: { type: String, default: null, required: false }
			// isContainer: { type: Boolean, default: true }
		},
		data(){
			return {
				isContainer: true,
				iframe: false,
				case: null,
				user: null
			}
		},
		mounted(){
			// console.log(typeof this.isContainer)
			var query = this.$route.query

			var type = query.type
			var case_no = query.case_no
			var user = query.user

			this.iframe = type == "iframe"
			this.case = case_no
			this.user = user

			if(!this.user)
				this.$store.dispatch('fetchCurrentUser');
			else
				this.$store.dispatch('checkProcessMakerSession', {user: user});
		},
		computed: {
			showMainDIV: function(){
				if (!this.iframe) {
					if (this.isContainer) {
						return true;
					}else{
						return false;
					}
				}else{
					return false;
				}
			}
		}
	}
</script>

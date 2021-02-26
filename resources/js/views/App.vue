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
				<router-link class="navbar-brand mr-auto" :to="{ name: 'home' }">
					<img src="/images/UNLOGOBW.jpg" alt="..." class="navbar-brand-img">
				</router-link>

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
					<div class="dropdown" v-if="this.$store.state.isUserRetrieved">

						<!-- Toggle -->
						<a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<img src="/img/avatars/profiles/hcsu.svg" alt="..." class="rounded-circle" height="40" width="40">
							{{ $store.state.loggedInUser.name }}
						</a>

						<!-- Menu -->
						<div class="dropdown-menu dropdown-menu-right">
							<router-link class="dropdown-item" :to="{name: 'settings'}" v-if="showMenu('settings')">Settings</router-link>
							<router-link v-if="showMenu('users')" class="dropdown-item" :to="{name: 'users'}">Users</router-link>
							<router-link v-if="showMenu('user-applications.all')" class="dropdown-item" :to="{name: 'user-applications.all'}">User Applications</router-link>
							<router-link v-if="showMenu('settings-adobesign-documents')" class="dropdown-item" :to="{ name: 'settings-adobesign-documents' }">Data</router-link>
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
				<li class="nav-item"><router-link class="nav-link" :to="{ name: 'home' }" v-if="showMenu('home')">Home</router-link></li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true">Clients</a>
					<ul class="dropdown-menu">
						<li><router-link class="dropdown-item" :to="{ name: 'principal' }" v-if="showMenu('principal')">Principals</router-link></li>
						<li><router-link class="dropdown-item" :to="{ name: 'agencies' }" v-if="showMenu('principal')">Agencies</router-link></li>
						<li><router-link class="dropdown-item" :to="{ name: 'dependents.list' }" v-if="showMenu('principal')">Dependents List</router-link></li>
						<li><router-link class="dropdown-item" :to="{ name: 'clients.other' }" v-if="showMenu('principal')">Others (Meeting Participants etc.)</router-link></li>
					</ul>
				</li>
				<!-- <li class="nav-item"></li> -->
				<li class="nav-item"></li>
				<li class="nav-item"><router-link class="nav-link" :to="{ name: 'vehicles' }" v-if="showMenu('vehicles')">Vehicles</router-link></li>
				<li class="nav-item"><router-link class="nav-link" :to="{ name: 'data-management' }" v-if="showMenu('data-management')">Data</router-link></li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true">Processes</a>
					<ul class="dropdown-menu">
						<li>
							<router-link class="dropdown-item" v-if="showMenu('blanket-vat')" :to="{ name: 'blanket-vat' }">Blanket VAT</router-link>
						</li>
						<li>
							<router-link class="dropdown-item" v-if="showMenu('subprocesses-ipmis')" :to="{ name: 'subprocesses-ipmis' }">Subprocesses</router-link>
						</li>
					</ul>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true">Exports</a>
					<ul class="dropdown-menu">
						<li>
							<router-link class="dropdown-item" v-if="showMenu('export-vat-list')" :to="{ name: 'export-vat-list' }">VAT List</router-link>
						</li>
						<li>
							<router-link class="dropdown-item" v-if="showMenu('export-organization-data')" :to="{ name: 'export-organization-data' }">Organization Data</router-link>
						</li>
					</ul>
				</li>
				<li class="nav-item"><router-link class="nav-link" v-if="showMenu('settings')" :to="{ name: 'settings' }">Settings</router-link></li>
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
            // this.findMenuRoles()
            console.log("mounted")
			let query = this.$route.query

			let type = query.type
			let case_no = query.case_no
			let user = query.user

			this.iframe = type == "iframe"
			this.case = case_no
			this.user = user

			if(!this.user){
				this.$store.dispatch('fetchCurrentUser');
			}
			else{
				this.$store.dispatch('checkProcessMakerSession', {user: user});
			}
		},
		methods: {
			showMenu(name){
				let exists = this.userRoles.some(role => {
					return _.includes(this.menuRoles[name], role)
				})

				return exists
			},
            flattenChildren(children, flattenedChildren = null){
			    let em = this
                if(!flattenedChildren) { flattenedChildren = {} }
                _.each(children, function(child){
                    if ('children' in child){
                        em.flattenChildren(child.children, flattenedChildren)
                    }else{
                        flattenedChildren[child.name] = child.meta.roles
                    }
                })

                return flattenedChildren
            },
            findMenuRoles(){
			    let em = this
                let routes = this.$router.options.routes
                let menuRoles = {}
                // console.log(em)

                _.forEach(routes, function(route) {
                    if('children' in route){
                        menuRoles[route.name] = route.meta.roles
                        let flattened = em.flattenChildren(route.children)
                        _.forEach(flattened, function(value, key){
                            menuRoles[key] = value
                        })
                    }else{
                        if('meta' in route) {
                            menuRoles[route.name] = route.meta.roles
                        }
                        else {
                            menuRoles[route.name] = []
                        }
                    }
                })

                return menuRoles
            }
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
			},
			displayUser: function(){
				return this.$store.state.loggedInUser
			},
			userRoles: function(){
				let roles = this.displayUser.roles
				let rolesArray = _.map(roles, (role) => {
					return role.name
				})

				return rolesArray
			},
            menuRoles: function(){
			    return this.findMenuRoles()
            }
		}
	}
</script>

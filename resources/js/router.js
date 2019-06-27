import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import Home from './views/Home'
import Principal from './views/Principal'
import AddPrincipal from './views/AddPrincipal'
import ViewPrincipal from './views/ViewPrincipal'
import Agencies from './views/Agencies'
import AddAgency from './views/AddAgency'
import Vehicles from './views/Vehicles'
import SearchClient from './views/iframe/SearchClient'

let routes = [
 	{
 		path: '/',
 		name: 'home',
 		component: Home,
 		meta: {
 			title: 'Home'
 		}
 	},
 	{
 		path: '/principal',
 		name: 'principal',
 		component: Principal,
 		meta: {
 			title: 'Principals'
 		}
 	},
 	{
 		path: '/principal/add',
 		name: 'principal.add',
 		component: AddPrincipal,
 		meta: {
 			title: 'Add Principal'
 		}
 	},
 	{
 		path: '/principal/view/:id',
 		name: 'principal.view',
 		component: ViewPrincipal,
 		meta: {
 			title: 'View Principal'
 		}
 	},
 	{
 		path: '/agencies',
 		name: 'agencies',
 		component: Agencies,
 		meta: {
 			title: 'Agencies'
 		}
 	},
 	{
 		path: '/agencies/add',
 		name: 'agencies.add',
 		component: AddAgency,
 		meta: {
 			title: 'Add Agency'
 		}
 	},
 	{
 		path: '/vehicles',
 		name: 'vehicles',
 		component: Vehicles,
 		meta: {
 			title: 'Vehicles'
 		}
 	},
 	{
 		path: '/client/search',
 		name: 'search-clients',
 		component: SearchClient,
 		meta: {
 			title: 'Search Client'
 		}
 }];

let router = new VueRouter({
	mode: "history",
	linkExactActiveClass: "active", 
	routes: routes ,
	scrollBehavior (to, from, savedPosition) {
		// Ensure that the page scrolls to the top after changing the route
		return { x: 0, y: 0 }
	}
})

router.beforeEach((to, from, next) => {
	document.title = to.meta.title
	next()
})

router.beforeResolve((to, from, next) => {
	if (to.name) {
		NProgress.start()
	}

	next()
});

router.afterEach((to, from) => {
	NProgress.done()
});

export default router
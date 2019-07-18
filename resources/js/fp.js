require('./bootstrap');
window.instance = require('./http')
window.Vue = require('vue');

import '../../public/fonts/feather/feather.min.css'
import '../../public/css/theme.min.css'

import 'bootstrap-vue/dist/bootstrap-vue.css'

import BootstrapVue from 'bootstrap-vue'
import VueRouter from 'vue-router'
import appEvent from './core/AppEvent'

Vue.use(VueRouter)
Vue.use(BootstrapVue)
Vue.use(require('vue-moment'))

import App from './focalpoints/App'
import Dashboard from './focalpoints/dashboard/Index'
import FPNormalVAT from './focalpoints/applications/FPNormalVAT'

const router = new VueRouter({
	linkExactActiveClass: "active",
	routes: [{
		path: "/",
		name: 'dashboard',
		component: Dashboard,
		meta: {
			title: "Dashboard",
			auth: true
		}
	}, {
		path: "/applications/normal-vat",
		name: "applications.normal-vat",
		meta: {
			title: "Normal VAT Application",
			subtitle: 'Applications',
			auth: true
		},
		component: FPNormalVAT
	}]
});

const app = new Vue({
    el: '#app',
    components: { App },
    router
});
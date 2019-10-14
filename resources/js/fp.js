require('./bootstrap');
window.instance = require('./http')
window.Vue = require('vue');

import '../../public/fonts/feather/feather.min.css'
import '../../public/css/theme.min.css'

import 'bootstrap-vue/dist/bootstrap-vue.css'

import BootstrapVue from 'bootstrap-vue'
import VueRouter from 'vue-router'
import appEvent from './core/AppEvent'
import store from './store'

import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import { ServerTable, ClientTable, Event } from 'vue-tables-2';

import VueSwal from 'vue-swal'

Vue.use(VueRouter)
Vue.use(BootstrapVue)
Vue.use(require('vue-moment'))
Vue.component('loading', Loading);
Vue.use(ServerTable, {}, false, 'bootstrap4', 'default');
Vue.use(ClientTable, {}, false, 'bootstrap4', 'default');
Vue.use(VueSwal)

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
    router,
    store
});
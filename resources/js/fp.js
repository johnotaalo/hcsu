require('./bootstrap');
window.instance = require('./http')
window.Vue = require('vue');
require('highcharts-vue');

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
import vSelect from 'vue-select'
import VueRouterBackButton from 'vue-router-back-button'
import HighchartsVue from 'highcharts-vue'
import VueHighcharts from 'vue-highcharts';
import Highcharts from 'highcharts';

// load these modules as your need
import { genComponent } from 'vue-highcharts';
import loadStock from 'highcharts/modules/stock.js';
import loadMap from 'highcharts/modules/map.js';
import loadGantt from 'highcharts/modules/gantt.js';
import loadDrilldown from 'highcharts/modules/drilldown.js';
// some charts like solid gauge require `highcharts-more.js`, you can find it in official document.
import loadHighchartsMore from 'highcharts/highcharts-more.js';
import loadSolidGauge from 'highcharts/modules/solid-gauge.js';
import More from 'highcharts/highcharts-more'

Vue.use(VueRouter)
Vue.use(BootstrapVue)
Vue.use(require('vue-moment'))
Vue.component('loading', Loading);
Vue.use(ServerTable, {}, false, 'bootstrap4', 'default');
Vue.use(ClientTable, {}, false, 'bootstrap4', 'default');
Vue.use(VueSwal)
Vue.component('v-select', vSelect)

loadStock(Highcharts);
loadMap(Highcharts);
loadGantt(Highcharts);
loadDrilldown(Highcharts);
loadHighchartsMore(Highcharts);
loadSolidGauge(Highcharts);
More(Highcharts)

Vue.use(HighchartsVue);
Vue.use(VueHighcharts, { Highcharts });

import App from './focalpoints/App'
import Dashboard from './focalpoints/dashboard/Index'
import FPNormalVAT from './focalpoints/applications/FPNormalVAT'
import FPNormalVATAdd from './focalpoints/applications/FPNormalVATAdd'
import FPNormalVATEdit from './focalpoints/applications/FPNormalVATEdit'
import FPNormalVATView from './focalpoints/applications/FPNormalVATView'

import AllApplications from './focalpoints/applications/AllApplications'
import NewApplication from './focalpoints/applications/NewApplication'
import ViewApplication from './focalpoints/applications/ViewApplication'
import EditApplication from './focalpoints/applications/EditApplication'
axios.interceptors.request.use(config => {
	NProgress.start()
	return config;
});

axios.interceptors.response.use(response => {
	NProgress.done()
	return response;
});

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
	}, 
	{
		path: "/applications/normal-vat",
		name: "applications.normal-vat",
		meta: {
			title: "Normal VAT Application",
			subtitle: 'Applications',
			auth: true
		},
		component: FPNormalVAT,
		children: [
			
		]
	},
	{
		path: "/applications/all",
		name: "applications.all",
		meta: {
			title: "All Applications",
			subtitle: 'Applications',
			auth: true
		},
		component: AllApplications,
		children: [
			
		]
	},{
		path: "/applications/new",
		name: "applications.new",
		meta: {
			title: "New Application",
			subtitle: 'Applications',
			auth: true
		},
		component: NewApplication,
	},
	{
		path: "/applications/view/:id",
		name: "applications.view",
		meta: {
			title: "View Application",
			subtitle: 'Applications',
			auth: true
		},
		component: ViewApplication,
	},
	{
		path: "/applications/edit/:id",
		name: "applications.edit",
		meta: {
			title: "Edit Application",
			subtitle: 'Applications',
			auth: true
		},
		component: EditApplication,
	}, 
	{
		path: '/applications/normal-vat/add',
		component: FPNormalVATAdd,
		name: 'applications.normal-vat.add',
		meta: {
			title: "Normal VAT Application",
			subtitle: 'Add Application',
			auth: true
		}
	},{
		path: '/applications/normal-vat/edit/:id',
		component: FPNormalVATEdit,
		name: 'applications.normal-vat.edit',
		meta: {
			title: "Normal VAT Application",
			subtitle: 'Edit Application',
			auth: true
		}
	}, {
		path: '/applications/normal-vat/view/:id',
		component: FPNormalVATView,
		name: 'applications.normal-vat.view',
		meta: {
			title: "Normal VAT Application",
			subtitle: 'View Application',
			auth: true
		}
	}]
});

Vue.use(VueRouterBackButton, { router })

const app = new Vue({
    el: '#app',
    components: { App },
    router,
    store
});
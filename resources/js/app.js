
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.instance = require('./http')

import BootstrapVue from 'bootstrap-vue';
import { ServerTable, ClientTable, Event } from 'vue-tables-2';
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCoffee, faUserFriends, faCogs, faMoneyBill, faPen, faEye } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import appEvent from './core/AppEvent'
import vSelect from 'vue-select'
import VCalendar from 'v-calendar'
import VueSwal from 'vue-swal'
import Toastr from 'vue-toastr'
import Notifications from 'vue-notification'
import Vuelidate from 'vuelidate'
import VuePluralize from 'vue-pluralize'
import VuePaginate from 'vue-paginate'
import 'v-calendar/lib/v-calendar.min.css';
import '../../public/fonts/feather/feather.min.css'
import '../../public/css/theme.min.css'
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-datetime/dist/vue-datetime.css'
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
require('vue-toastr/src/vue-toastr.scss');


window.Vue = require('vue');
Vue.use(BootstrapVue);
Vue.use(ServerTable, {}, false, 'bootstrap4', 'default');
Vue.use(ClientTable, {}, false, 'bootstrap4', 'default');
Vue.component('v-select', vSelect)
Vue.use(VCalendar);
Vue.use(require('vue-moment'))
Vue.use(VueSwal)
Vue.component('vue-toastr',Toastr);
Vue.use(Notifications)
Vue.use(Vuelidate)
Vue.use(VuePluralize)
Vue.use(VuePaginate)

library.add({faCoffee, faCogs, faMoneyBill, faUserFriends, faPen, faEye })

Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('loading', Loading);


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import VueRouter from 'vue-router'

Vue.use(VueRouter)

Vue.filter('nullable', function (value) {
	if (!value) return 'N/A'
	return value
})


// import App from './views/App'
// import router from './router'
import App from './views/App'
import Home from './views/Home'
import Principal from './views/Principal'
import AddPrincipal from './views/AddPrincipal'
import ViewPrincipal from './views/ViewPrincipal'
import ViewDependent from './views/dependent/ViewDependent'
import Agencies from './views/Agencies'
import AddAgency from './views/AddAgency'
import ViewAgency from './views/agency/ViewAgency'
import Vehicles from './views/Vehicles'
import SearchClient from './views/iframe/SearchClient'
import SettingsIndex from './views/settings/SettingsIndex'
import VAT from './views/vat/VAT'
import BlanketVAT from './views/vat/BlanketVAT'
// BlanketVATBatch
import BlanketVATBatch from './views/vat/components/BlanketVATBatch'
import NormalVAT from './views/vat/components/NormalVAT'

// Vehicles
import VehicleHome from './views/vehicle/VehicleHome'
import Plates from './views/vehicle/Plates'
import Prefix from './views/vehicle/plates/Prefix'
import PlatesOrder from './views/vehicle/plates/PlatesOrder'

// Data
import DataManagement from './views/data/DataManagement'

 const router = new VueRouter({
 	mode: 'history',
 	linkExactActiveClass: "active",
 	routes: [
	 	{
	 		path: '/',
	 		name: 'home',
	 		component: Home
	 	},
	 	{
	 		path: '/principal',
	 		name: 'principal',
	 		component: Principal
	 	},
	 	{
	 		path: '/principal/add',
	 		name: 'principal.add',
	 		component: AddPrincipal
	 	},
	 	{
	 		path: '/principal/view/:id',
	 		name: 'principal.view',
	 		component: ViewPrincipal
	 	},
    	{
	 		path: '/dependent/view/:id',
	 		name: 'dependent.view',
	 		component: ViewDependent
	 	},
	 	{
	 		path: '/agencies',
	 		name: 'agencies',
	 		component: Agencies
	 	},
	 	{
	 		path: '/agencies/add',
	 		name: 'agencies.add',
	 		component: AddAgency
	 	},
	 	{
	 		path: '/agencies/view/:id',
	 		name: 'agencies.view',
	 		component: ViewAgency
	 	},
	 	{
	 		path: '/vehicles',
	 		name: 'vehicles',
	 		component: Vehicles,
	 		children: [
	 			{
	 				path: '',
	 				component: VehicleHome,
	 				name: 'vehicle-home'
	 			},
	 			{
	 				path: 'plates',
	 				component: Plates,
	 				name: 'vehicle-plates',
	 				children: [
	 					{
	 						path: 'prefixes',
	 						component: Prefix,
	 						name: 'vehicle-plates-prefix'
	 					},
	 					{
	 						path: 'order',
	 						component: PlatesOrder,
	 						name: 'vehicle-plates-orders'
	 					}
	 				]
	 			}
	 		]
	 	},
	 	{
	 		path: '/client/search',
	 		name: 'search-clients',
	 		component: SearchClient
	 	},
	 	{
	 		path: '/settings',
	 		name: 'settings',
	 		component: SettingsIndex
	 	},
	 	{
	 		path: '/vat',
	 		name: 'vat',
	 		// component: VAT,
	 		children: [
	 			{
	 				path: 'blanket',
	 				component: BlanketVAT,
	 				name: 'blanket'
	 			},
	 			{
	 				path: 'normal',
	 				component: NormalVAT,
	 				name: 'normal-vat'
	 			}
	 		]
	 	},
	 	{
	 		path: '/blanket-vat',
	 		name: 'blanket-vat',
	 		component: BlanketVAT,
	 		children: [
	 			{
	 				path: 'batches',
	 				component: BlanketVATBatch,
	 				name: 'blanket-vat-batches'
	 			},
	 			{
	 				path: 'normal',
	 				component: NormalVAT,
	 				name: 'normal-vat'
	 			}
	 		]
	 	},
	 	{
	 		path: '/data-management',
	 		name: 'data-management',
	 		component: DataManagement
	 	}
 	]
 });

 router.beforeResolve((to, from, next) => {
 	if (to.name) {
 		NProgress.start()
 	}

 	next()
 });

 router.afterEach((to, from) => {
 	NProgress.done()
});

const app = new Vue({
    el: '#app',
    components: { App },
    router
});


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
require('vue-toastr/src/vue-toastr.scss');


window.Vue = require('vue');
Vue.use(BootstrapVue);
Vue.use(ServerTable, {}, false, 'bootstrap4', 'default');
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

import App from './views/App'
import router from './router'

const app = new Vue({
    el: '#app',
    components: { App },
    router
});

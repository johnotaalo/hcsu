require('./bootstrap');
window.instance = require('./http')
window.Vue = require('vue');

import Login from './views/Login'

Vue.component('login', Login);

const app = new Vue({
    el: '#app'
});
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// Veevalidate
import id from 'vee-validate/dist/locale/id';
import * as rules from 'vee-validate/dist/rules';
import { ValidationObserver, ValidationProvider, extend } from 'vee-validate';

for (let rule in rules) {
  extend(rule, {
    ...rules[rule],
    message: id.messages[rule]
  });
}

import "vue-swatches/dist/vue-swatches.min.css"
import 'vue2-datepicker/index.css';

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

// Veevalidate
Vue.component('validation-observer', ValidationObserver);
Vue.component('validation-provider', ValidationProvider);

// Recruitment
Vue.component('statuspayroll-table-records', require('./pages/recruitment/payroll/statusPayroll.vue').default);
Vue.component('payroll-table-records', require('./pages/recruitment/payroll/Payroll.vue').default);
Vue.component('pph-table-records', require('./pages/recruitment/pph/PPH.vue').default);
Vue.component('spt-table-records', require('./pages/recruitment/spt/SPT.vue').default);
Vue.component('thr-table-records', require('./pages/recruitment/thr/THR.vue').default);
Vue.component('bonus-table-records', require('./pages/recruitment/bonus/Bonus.vue').default);
Vue.component('pesangon-table-records', require('./pages/recruitment/pesangon/Pesangon.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
function newFunction() {
    return 'tahap-table-records';
}

// axios({
//     method: 'GET',
//     url: '/recruitment/payroll/deletePayroll',
// })

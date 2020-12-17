/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = require('vue');
//Import progressbar
require('./progressbar');
//Import sweet alert
require('./alert');
//Import components html tag
require('./components');
//Import vue filter function
require('./filter');
//Import google maps
require('./maps');



//Import View Router
import VueRouter from 'vue-router'
import Vuetify from '../plugins/vuetify'
//Import Vuelidate
import Vuelidate from 'vuelidate'
//Import v-from
import { Form, HasError, AlertError } from 'vform'
//Routes
import { routes } from './routes';
import store from "./store";
import '@fortawesome/fontawesome-free';
import 'material-design-icons-iconfont';
import { ImagePicker, imageUploadingStates } from "@nagoos/vue-image-picker"
import { helpers } from "./helpers";
import VueSpinners from 'vue-spinners'
import VueAxios from "vue-axios";
import axios from "axios";
import VueApexCharts from 'vue-apexcharts'
import JsonExcel from 'vue-json-excel'
import VueHtmlToPaper from 'vue-html-to-paper';
import VueLodash from 'vue-lodash'
import lodash from 'lodash'
import Cloudinary,{ CldImage, CldTransformation } from "cloudinary-vue";

const options = {
  name: '_blank',
  specs: [
    'fullscreen=yes',
    'titlebar=yes',
    'scrollbars=yes'
  ],
  styles: [
    'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
    'https://unpkg.com/kidlat-css/css/kidlat.css'
  ]
}

Vue.use(VueHtmlToPaper, options);
 
Vue.component('download-excel', JsonExcel)
Vue.use(VueApexCharts)
Vue.component('apexchart', VueApexCharts)
Vue.use(Cloudinary, {
  configuration: { cloudName: "dwslzbgaa" },
  components: {
    CldImage,
    CldTransformation
  }
});

Vue.use(VueSpinners)
Vue.use(VueRouter)
Vue.use(Vuelidate)
Vue.use(VueAxios, axios);
// Vue.use(VueLodash, { name: '_' , lodash: lodash })
Object.defineProperty(Vue.prototype, '_', { value: lodash });

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//=================================
window.Form = Form;
// Vue.component(HasError.name, HasError)
// Vue.component(AlertError.name, AlertError)
// Vue.component('ImagePicker', ImagePicker)


//Register Routes
const router = new VueRouter({
    routes,
    mode: 'hash',

})



Vue.mixin({
    data: function () {
        return {
            hi: "hi",
            helpers,
        }
    },
})

const app = new Vue({
    el: '#app',
    vuetify: Vuetify,
    router,
    store,
    icons: {
        iconfont: 'md' || 'fa'
    },

});

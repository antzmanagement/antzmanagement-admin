
//Common used Component
Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('loading', require('./components/Loading.vue').default);

Vue.component('navbar', require('./components/NavBar.vue').default);
Vue.component('home-navbar', require('./components/HomeNavBar.vue').default);
Vue.component('contact-us', require('./components/ContactUs.vue').default);
Vue.component('home-footer', require('./components/HomeFooter.vue').default);
Vue.component('google-map', require('./components/Map.vue').default);
Vue.component('confirm-dialog', require('./components/ConfirmDialog.vue').default);
Vue.component('change-password-dialog', require('./components/Management/UserManagement/ChangePasswordDialog.vue').default);

Vue.component('tenant-form', require('./components/Management/UserManagement/TenantManagement/TenantForm.vue').default);
Vue.component('tenant-filter-dialog', require('./components/Management/UserManagement/TenantManagement/TenantFilterDialog.vue').default);



Vue.component('room-form', require('./components/Management/RoomManagement/RoomForm.vue').default);
Vue.component('room-type-form', require('./components/Management/RoomTypeManagement/RoomTypeForm.vue').default);
Vue.component('room-filter-dialog', require('./components/Management/RoomManagement/RoomFilterDialog.vue').default);

Vue.component('room-type-sliders', require('./components/RoomPages/RoomTypeSliders.vue').default);
Vue.component('room-details', require('./components/RoomPages/RoomDetails.vue').default);
Vue.component('room-type-listing', require('./components/RoomPages/RoomTypeListing.vue').default);
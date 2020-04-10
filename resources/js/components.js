
//Common used Component
Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('loading', require('./components/Loading.vue').default);

Vue.component('navbar', require('./components/NavBar.vue').default);
Vue.component('confirm-dialog', require('./components/ConfirmDialog.vue').default);
Vue.component('change-password-dialog', require('./components/Management/UserManagement/ChangePasswordDialog.vue').default);

Vue.component('tenant-form', require('./components/Management/UserManagement/TenantManagement/TenantForm.vue').default);

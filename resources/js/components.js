
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
Vue.component('tenant-room', require('./components/Management/UserManagement/TenantManagement/TenantRoom.vue').default);


Vue.component('owner-form', require('./components/Management/UserManagement/OwnerManagement/OwnerForm.vue').default);
Vue.component('owner-filter-dialog', require('./components/Management/UserManagement/OwnerManagement/OwnerFilterDialog.vue').default);

Vue.component('staff-form', require('./components/Management/UserManagement/StaffManagement/StaffForm.vue').default);
Vue.component('staff-filter-dialog', require('./components/Management/UserManagement/StaffManagement/StaffFilterDialog.vue').default);


Vue.component('room-form', require('./components/Management/RoomManagement/RoomForm.vue').default);
Vue.component('room-type-form', require('./components/Management/RoomTypeManagement/RoomTypeForm.vue').default);
Vue.component('room-filter-dialog', require('./components/Management/RoomManagement/RoomFilterDialog.vue').default);

Vue.component('room-type-sliders', require('./components/RoomPages/RoomTypeSliders.vue').default);
Vue.component('room-details', require('./components/RoomPages/RoomDetails.vue').default);
Vue.component('room-type-listing', require('./components/RoomPages/RoomTypeListing.vue').default);

Vue.component('maintenance-form', require('./components/Management/MaintenanceManagement/MaintenanceForm.vue').default);
Vue.component('maintenance-filter-dialog', require('./components/Management/MaintenanceManagement/MaintenanceFilterDialog.vue').default);

Vue.component('property-form', require('./components/Management/PropertyManagement/PropertyForm.vue').default);
Vue.component('property-filter-form', require('./components/Management/PropertyManagement/PropertyFilterForm.vue').default);

Vue.component('services-dialog', require('./components/Management/ServiceManagement/ServicesDialog.vue').default);
Vue.component('service-form', require('./components/Management/ServiceManagement/ServiceForm.vue').default);
Vue.component('service-filter-form', require('./components/Management/ServiceManagement/ServiceFilterForm.vue').default);

Vue.component('contract-form', require('./components/Management/ContractManagement/ContractForm.vue').default);
Vue.component('contract-filter-form', require('./components/Management/ContractManagement/ContractFilterForm.vue').default);

Vue.component('room-contract-form', require('./components/Management/RoomContractManagement/RoomContractForm.vue').default);
Vue.component('room-contract-filter-dialog', require('./components/Management/RoomContractManagement/RoomContractFilterDialog.vue').default);

Vue.component('rental-payment-form', require('./components/Management/RentalPaymentManagement/RentalPaymentForm.vue').default);
Vue.component('rental-payment-filter-dialog', require('./components/Management/RentalPaymentManagement/RentalPaymentFilterDialog.vue').default);
Vue.component('rental-print', require('./components/Management/RentalPaymentManagement/RentalPrint.vue').default);

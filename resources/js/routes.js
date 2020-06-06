import Example from './components/ExampleComponent.vue'
import ManagementHome from './components/Management/ManagementHome.vue'
import Profile from './components/Profile/Profile.vue'
import Login from './components/Login/Login.vue'

import UserManagement from './components/Management/UserManagement/UserManagement.vue';

import AllTenant from './components/Management/UserManagement/TenantManagement/AllTenant.vue';
import Tenant from './components/Management/UserManagement/TenantManagement/Tenant.vue'


import AllOwner from './components/Management/UserManagement/OwnerManagement/AllOwner.vue';
import Owner from './components/Management/UserManagement/OwnerManagement/Owner.vue'

import AllStaff from './components/Management/UserManagement/StaffManagement/AllStaff.vue';
import Staff from './components/Management/UserManagement/StaffManagement/Staff.vue'

import AllRoom from './components/Management/RoomManagement/AllRoom.vue';
import Room from './components/Management/RoomManagement/Room.vue';

import AllMaintenance from './components/Management/MaintenanceManagement/AllMaintenance.vue';
import Maintenance from './components/Management/MaintenanceManagement/Maintenance.vue';

import AllProperty from './components/Management/PropertyManagement/AllProperty.vue';
import Property from './components/Management/PropertyManagement/Property.vue';

import AllService from './components/Management/ServiceManagement/AllService.vue';
import Service from './components/Management/ServiceManagement/Service.vue';

import AllContract from './components/Management/ContractManagement/AllContract.vue';
import Contract from './components/Management/ContractManagement/Contract.vue';

import Home from './components/Home/Home.vue'
import RoomPage from './components/Home/RoomPage.vue';
import ServicesPage from './components/Home/ServicesPage.vue';
import ProfilePage from './components/Home/Profile.vue';

export const routes = [
   
    { 
        path:'/',
        component:Home
    },
    { 
        path:'/home',
        component:Home,
        name : 'home',
    },
    { 
        path:'/roompages',
        component:RoomPage,
        name : 'roompages',
    },
    { 
        path:'/servicespage',
        component:ServicesPage,
        name : 'servicespage',
    },
    { 
        path:'/profile',
        component:ProfilePage,
        name : 'profile ',
    },
    { 
        path:'/management',
        component:ManagementHome,
        name : 'management',
    },
    { 
        path:'/example',
        component:Example
    },

    
    { 
        path:'/login',
        component:Login,
        name : 'login',
    },
    { 
        path:'/profile',
        component:Profile,
        name : 'profile',
    },

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //Management Route //
    { 
        path:'/usermanagement',
        component:UserManagement,
        name : 'usermanagement',
    },

    // Tenant
    { 
        path:'/tenants',
        component:AllTenant,
        name : 'tenants',
    },
    { 
        path:'/tenant/:uid',
        component:Tenant,
        name : 'tenant',
    },
    
    //Owner
    { 
        path:'/owners',
        component:AllOwner,
        name : 'owners',
    },
    { 
        path:'/owner/:uid',
        component:Owner,
        name : 'owner',
    },
    
    //Staff
    { 
        path:'/staffs',
        component:AllStaff,
        name : 'staffs',
    },
    { 
        path:'/staff/:uid',
        component:Staff,
        name : 'staff',
    },
    
    //Room
    { 
        path:'/rooms',
        component:AllRoom,
        name : 'rooms',
    },
    { 
        path:'/room/:uid',
        component:Room,
        name : 'room',
    },
 
    //Maintenance
    { 
        path:'/maintenances',
        component:AllMaintenance,
        name : 'maintenances',
    },
    { 
        path:'/maintenance/:uid',
        component:Maintenance,
        name : 'maintenance',
    },
 
    //Property
    { 
        path:'/properties',
        component:AllProperty,
        name : 'properties',
    },
    { 
        path:'/property/:uid',
        component:Property,
        name : 'property',
    },
 
    //Service
    { 
        path:'/services',
        component:AllService,
        name : 'services',
    },
    { 
        path:'/service/:uid',
        component:Service,
        name : 'service',
    },
    
    //Contract
    { 
        path:'/contracts',
        component:AllContract,
        name : 'contracts',
    },
    { 
        path:'/contract/:uid',
        component:Contract,
        name : 'contract',
    },
 
];
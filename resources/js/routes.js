import Example from './components/ExampleComponent.vue'
import ManagementHome from './components/Management/ManagementHome.vue'
import Profile from './components/Profile/Profile.vue'
import Login from './components/Login/Login.vue'
import AllStores from './components/Profile/MyStore/AllStores.vue'

import UserManagement from './components/Management/UserManagement/UserManagement.vue';

import AllTenants from './components/Management/UserManagement/TenantManagement/AllTenants.vue';
import Tenant from './components/Management/UserManagement/TenantManagement/Tenant.vue'


import AllOwners from './components/Management/UserManagement/OwnerManagement/AllOwners.vue';
import Owner from './components/Management/UserManagement/OwnerManagement/Owner.vue'

import AllStaffs from './components/Management/UserManagement/StaffManagement/AllStaffs.vue';
import Staff from './components/Management/UserManagement/StaffManagement/Staff.vue'

import AllRooms from './components/Management/RoomManagement/AllRooms.vue';
import Room from './components/Management/RoomManagement/Room.vue';

import AllMaintenances from './components/Management/MaintenanceManagement/AllMaintenances.vue';
import Maintenance from './components/Management/MaintenanceManagement/Maintenance.vue';

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
    { 
        path:'/stores',
        component:AllStores,
        name : 'stores',
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
        component:AllTenants,
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
        component:AllOwners,
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
        component:AllStaffs,
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
        component:AllRooms,
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
        component:AllMaintenances,
        name : 'maintenances',
    },
    { 
        path:'/maintenance/:uid',
        component:Maintenance,
        name : 'maintenance',
    },
 
 
];
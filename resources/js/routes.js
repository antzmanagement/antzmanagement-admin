import Example from './components/ExampleComponent.vue'
import Home from './components/Home/Home.vue'
import ManagementHome from './components/Management/ManagementHome.vue'
import Profile from './components/Profile/Profile.vue'
import Login from './components/Login/Login.vue'
import AllStores from './components/Profile/MyStore/AllStores.vue'

import UserManagement from './components/Management/UserManagement/UserManagement.vue';

import AllTenants from './components/Management/UserManagement/TenantManagement/AllTenants.vue';
import Tenant from './components/Management/UserManagement/TenantManagement/Tenant.vue'

import AllRooms from './components/Management/RoomManagement/AllRooms.vue';
import Room from './components/Management/RoomManagement/Room.vue';

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

    
    { 
        path:'/usermanagement',
        component:UserManagement,
        name : 'usermanagement',
    },
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
 
 
 
];
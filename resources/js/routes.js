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

import AllRoomType from './components/Management/RoomTypeManagement/AllRoomType.vue';
import RoomType from './components/Management/RoomTypeManagement/RoomType.vue';

import AllMaintenance from './components/Management/MaintenanceManagement/AllMaintenance.vue';
import Maintenance from './components/Management/MaintenanceManagement/Maintenance.vue';

import AllRoomCheck from './components/Management/RoomCheckManagement/AllRoomCheck.vue';
import RoomCheck from './components/Management/RoomCheckManagement/RoomCheck.vue';

import AllProperty from './components/Management/PropertyManagement/AllProperty.vue';
import Property from './components/Management/PropertyManagement/Property.vue';

import AllService from './components/Management/ServiceManagement/AllService.vue';
import Service from './components/Management/ServiceManagement/Service.vue';

import AllContract from './components/Management/ContractManagement/AllContract.vue';
import Contract from './components/Management/ContractManagement/Contract.vue';

import AllRoomContract from './components/Management/RoomContractManagement/AllRoomContract.vue';
import RoomContract from './components/Management/RoomContractManagement/RoomContract.vue';

import AllRentalPayment from './components/Management/RentalPaymentManagement/AllRentalPayment.vue';
import RentalPayment from './components/Management/RentalPaymentManagement/RentalPayment.vue';

import AllClaim from './components/Management/ClaimManagement/AllClaim.vue';
import Claim from './components/Management/ClaimManagement/Claim.vue';


import Home from './components/Home/Home.vue'
import RoomPage from './components/Home/RoomPage.vue';
import ServicesPage from './components/Home/ServicesPage.vue';
import ProfilePage from './components/Home/Profile.vue';

export const routes = [
   
    { 
        path:'/',
        component:ManagementHome
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
    
    //Room Type
    { 
        path:'/roomtypes',
        component:AllRoomType,
        name : 'roomtypes',
    },
    { 
        path:'/roomtype/:uid',
        component:RoomType,
        name : 'roomtype',
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
    //Room Check
    { 
        path:'/roomchecks',
        component:AllRoomCheck,
        name : 'roomchecks',
    },
    { 
        path:'/roomcheck/:uid',
        component:RoomCheck,
        name : 'roomcheck',
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

    
    //Room Contract
    { 
        path:'/roomcontracts',
        component:AllRoomContract,
        name : 'roomcontracts',
    },
    { 
        path:'/roomcontract/:uid',
        component: RoomContract,
        name : 'roomcontract',
    },

    //Rental Payment
    { 
        path:'/rentalpayments',
        component:AllRentalPayment,
        name : 'rentalpayments',
    },
    { 
        path:'/rentalpayment/:uid',
        component: RentalPayment,
        name : 'rentalpayment',
    },
    //Claim
    { 
        path:'/claims',
        component:AllClaim,
        name : 'claims',
    },
    { 
        path:'/claim/:uid',
        component: Claim,
        name : 'cliam',
    },
 
   
    { 
        path:'*',
        component:ManagementHome
    },
];
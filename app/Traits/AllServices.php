<?php

namespace App\Traits;

//Functionality Services
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;

//Model Services
use App\Traits\OwnerServices;
use App\Traits\TenantServices;
use App\Traits\UserServices;
use App\Traits\UserTypeServices;
use App\Traits\RoomServices;
use App\Traits\RoomTypeServices;
use App\Traits\StaffServices;


trait AllServices {

    use 
    GlobalFunctions,
    NotificationFunctions, 

    OwnerServices,
    RoomServices,
    RoomTypeServices,
    TenantServices,
    UserServices,
    UserTypeServices,
    StaffServices;


    
    private $staffType = "1";
    private $tenantType = "2";
    private $ownerType = "3";

}

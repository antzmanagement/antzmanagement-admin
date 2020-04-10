<?php

namespace App\Traits;

//Functionality Services
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;

//Model Services
use App\Traits\TenantServices;
use App\Traits\UserServices;
use App\Traits\UserTypeServices;


trait AllServices {

    use 
    GlobalFunctions,
    NotificationFunctions, 

    TenantServices,
    UserServices,
    UserTypeServices;

}

<?php

namespace App\Traits;

//Functionality Services
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\ImageHostingServices;

//Model Services
use App\Traits\ContractServices;
use App\Traits\ClaimServices;
use App\Traits\MaintenanceServices;
use App\Traits\OwnerServices;
use App\Traits\PropertyServices;
use App\Traits\TenantServices;
use App\Traits\UserServices;
use App\Traits\UserTypeServices;
use App\Traits\RentalPaymentServices;
use App\Traits\RoomContractServices;
use App\Traits\ReportServices;
use App\Traits\RoleServices;
use App\Traits\RoomServices;
use App\Traits\RoomTypeServices;
use App\Traits\ServiceServices;
use App\Traits\StaffServices;


trait AllServices {

    use 
    GlobalFunctions,
    NotificationFunctions, 
    ImageHostingServices,

    ClaimServices,
    ContractServices,
    MaintenanceServices,
    OwnerServices,
    PropertyServices,
    RentalPaymentServices,
    RoomContractServices,
    RoomServices,
    ReportServices,
    RoleServices,
    RoomTypeServices,
    ServiceServices,
    TenantServices,
    UserServices,
    UserTypeServices,
    StaffServices;


    
    private $staffType = "1";
    private $tenantType = "2";
    private $ownerType = "3";

}

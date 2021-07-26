<?php

namespace App\Traits;

//Functionality Services
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\ImageHostingServices;

//Model Services
use App\Traits\CleaningServices;
use App\Traits\ContractServices;
use App\Traits\OwnerClaimServices;
use App\Traits\MaintenanceServices;
use App\Traits\OwnerServices;
use App\Traits\PropertyServices;
use App\Traits\TenantServices;
use App\Traits\UserServices;
use App\Traits\UserTypeServices;
use App\Traits\RentalPaymentServices;
use App\Traits\RoomCheckServices;
use App\Traits\RoomContractServices;
use App\Traits\ReportServices;
use App\Traits\RoleServices;
use App\Traits\RoomServices;
use App\Traits\RoomTypeServices;
use App\Traits\ServiceServices;
use App\Traits\StaffServices;
use App\Traits\PaymentServices;
use App\Traits\OtherPaymentTitleServices;


trait AllServices {

    use 
    GlobalFunctions,
    NotificationFunctions, 
    ImageHostingServices,

    OwnerClaimServices,
    CleaningServices,
    ContractServices,
    MaintenanceServices,
    OwnerServices,
    PropertyServices,
    RentalPaymentServices,
    RoomCheckServices,
    RoomContractServices,
    RoomServices,
    ReportServices,
    RoleServices,
    RoomTypeServices,
    ServiceServices,
    TenantServices,
    UserServices,
    UserTypeServices,
    StaffServices,
    OtherPaymentTitleServices,
    PaymentServices;

    private $staffType = "1";
    private $tenantType = "2";
    private $ownerType = "3";

}

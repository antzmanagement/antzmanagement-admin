<?php

namespace App\Traits;

use App\UserType;
use App\RoomType;
use App\Room;
use App\RoomContract;
use App\RentalPayment;
use App\Maintenance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

trait ReportServices
{



    private function getReports($requester)
    {
        $report['unpaidTenant'] = $this->getUnpaidRentalTenant($requester);

        $report['roomTypesPortion'] = $this->getRoomTypePortion($requester);

        $report['roomStatusPortion'] = $this->getRoomStatusPortion($requester);

        $report['currentMonthUnpaidTenantPortion'] = $this->getCurrentMonthUnpaidTenantPortion($requester);

        $report['todayPaidRental'] = $this->getTotalPaidRental($requester);

        $report['roomContractAlmostEnd'] = $this->getRoomContractAlmostEnd($requester);

        $report['monthlyMaintenancesPortion'] = $this->getMonthlyMaintenancePortion($requester);

        return $report;
    }


    private function filterReports($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->reportFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->uid, $keyword) == TRUE || stristr($item->name, $keyword) == TRUE || stristr($item->icno, $keyword) == TRUE || stristr($item->email, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

      
        $data = $data->unique('id');

        return $data;
    }

    private function getUnpaidRentalTenant($requester)
    {
        try {
            $rentalPayments = RentalPayment::where('paid', false)->get();
            
            $rentalPayments = $rentalPayments->map(function($rentalPayment){
                $rentalPayment['tenant'] = $rentalPayment->roomcontract->tenant;
                $rentalPayment['room'] = $rentalPayment->roomcontract->room;
                return $rentalPayment;
            })->values();
    
    
            return $rentalPayments;
        } catch (\Throwable $th) {
            return collect();
        }
    }

    private function getTotalPaidRental($requester)
    {
        try {
            $rentalPayments = RentalPayment::whereDate('paymentdate', '>=', Carbon::now()->startOfDay())->whereDate('paymentdate', '<=', Carbon::now()->endOfDay())->get();
            
            $rentalPayments = $rentalPayments->map(function($rentalPayment){
                $rentalPayment['tenant'] = $rentalPayment->roomcontract->tenant;
                $rentalPayment['room'] = $rentalPayment->roomcontract->room;
                return $rentalPayment;
            })->values();
    
    
            return $rentalPayments;
        } catch (\Throwable $th) {
            return collect();
        }
    }

    private function getRoomTypePortion($requester)
    {
        try {
            $total = 0;
    
            $roomTypes = RoomType::where('status', true)->get();
    
            foreach ($roomTypes as $roomType) {
                $roomsCount = $roomType->rooms()->where('rooms.status',true)->wherePivot('status', true)->count();
                $total += $roomsCount;
                $roomType->count = $roomsCount;
            }
    
            $data['data'] = $roomTypes;
            $data['total'] = $total;
            return $data;
        } catch (\Throwable $th) {
            return collect();
        }
    }
    

    private function getRoomStatusPortion($requester)
    {
        try {
            $total = 0;
    
            $rooms = Room::where('status', true)->get();
    
            $rooms = $rooms->countBy(function($room){
                return $room->room_status;
            });
    
            $total = 0;
            foreach ($rooms as $room) {
                $total += $room;
            }
    
            $data['data'] = $rooms;
            $data['total'] = $total;
            return $data;
        } catch (\Throwable $th) {
            return collect();
        }
    }

    private function getCurrentMonthUnpaidTenantPortion($requester)
    {
        try {
            $total = 0;
    
            $rentalPayments = RentalPayment::whereDate('rentaldate', '>=', Carbon::now()->firstOfMonth())->get();
    
            $rentalPayments = $rentalPayments->countBy(function($rentalPayment){
                return $rentalPayment->paid;
            });
            $rentalPayments['Paid'] = collect($rentalPayments)->get('1') || 0;
            $rentalPayments['Unpaid'] = collect($rentalPayments)->get('0') || 0;
            unset($rentalPayments['0']);
            unset($rentalPayments['1']);
    
            $total = 0;
            foreach ($rentalPayments as $rentalPayment) {
                $total += $rentalPayment;
            }
            
    
            $data['data'] = $rentalPayments;
            $data['total'] = $total;
            return $data;
        } catch (\Throwable $th) {
            error_log($th);
            return collect();
        }
    }

    private function getRoomContractAlmostEnd($requester)
    {
        try {
            $roomcontracts = RoomContract::whereDate('enddate', '>=', Carbon::now()->firstOfMonth()->startOfMonth()->startOfDay())->whereDate('enddate', '<=', Carbon::now()->firstOfMonth()->addMonths(2)->endOfMonth()->endOfDay())->get();
    
            error_log(Carbon::now()->firstOfMonth()->startOfMonth()->startOfDay());
            error_log(Carbon::now()->firstOfMonth()->addMonths(2)->endOfMonth()->endOfDay());
            $roomcontracts = $roomcontracts->map(function($roomcontract){
                $roomcontract['room'] = $roomcontract->room;
                $roomcontract['contract'] = $roomcontract->contract;
                $roomcontract['tenant'] = $roomcontract->tenant;
                $roomcontract['owner'] = $roomcontract->owner;
                $roomcontract['addonservices'] = $roomcontract->addonservices;
                $roomcontract['origservices'] = $roomcontract->origservices;
                return $roomcontract;
            })->values();
            return $roomcontracts;
        } catch (\Throwable $th) {
            return collect();
        }
    }

    private function getMonthlyMaintenancePortion($requester)
    {
        try {
            $total = 0;
    
            $ownerMaintenaces = Maintenance::whereDate('created_at', '>=', Carbon::now()->firstOfMonth())->where('claim_by_owner', true)->get();
            $systemMaintenances = Maintenance::whereDate('created_at', '>=', Carbon::now()->firstOfMonth())->where('claim_by_owner', false)->get();
            
    
            $data['data'][0]['type'] = 'owner';
            $data['data'][0]['total'] = $ownerMaintenaces->count();
            $data['data'][1]['type'] = 'system';
            $data['data'][1]['total'] = $systemMaintenances->count();
            $data['total'] = $ownerMaintenaces->count() + $systemMaintenances->count();
            return $data;
        } catch (\Throwable $th) {
            return collect();
        }
    }
}

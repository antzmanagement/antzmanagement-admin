<?php

namespace App\Traits;

use App\UserType;
use App\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

trait ReportServices
{



    private function getReports($requester)
    {
        $report['unpaidTenant'] = $this->getUnpaidRentalTenant($requester);

        $report['roomTypesPortion'] = $this->getRoomTypePortion($requester);


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
            });
        }

      
        $data = $data->unique('id');

        return $data;
    }

    private function getUnpaidRentalTenant($requester)
    {
        $params = collect([
            'paid' => false,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $rentalPayments = $this->getRentalPayments($requester);
        $rentalPayments = $this->filterRentalPayments($rentalPayments, $params);
        
        $rentalPayments = $rentalPayments->map(function($rentalPayment){
            $rentalPayment['tenant'] = $rentalPayment->roomcontract->tenant->name;
            $rentalPayment['contract'] = $rentalPayment->roomcontract->name;
            return $rentalPayment;
        });


        return $rentalPayments;
    }

    private function getRoomTypePortion($requester)
    {
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
    }

}

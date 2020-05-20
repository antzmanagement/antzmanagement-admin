<?php

namespace App\Traits;

use App\RoomContract;
use Carbon\Carbon;
use App\Traits\AllServices;
use OpenApi\Annotations\Property;

trait RoomContractServices
{

    use AllServices;


    private function getRoomContracts($requester)
    {

        $data = collect();

        $data = RoomContract::where('status', true)->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterRoomContracts($data, $params)
    {
        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params, $this->roomContractFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->uid, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            });
        }
        if ($params->rooms) {
            error_log('Filtering roomContracts with rooms....');
            $rooms = collect($params->rooms);
            $data = $data->filter(function ($item) use ($rooms) {

                if (!$rooms->contains($item->room->id)) {
                    return false;
                }
                return true;
            })->values();
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getRoomContract($uid)
    {

        $data = RoomContract::where('uid', $uid)->with(['room', 'tenant', 'contract'])->where('status', true)->first();
        return $data;
    }

    private function getRoomContractById($id)
    {
        $data = RoomContract::where('id', $id)->with(['room', 'tenant', 'contract'])->where('status', true)->first();
        return $data;
    }

    private function createRoomContract($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->roomContractAllCols());
        $data = new RoomContract();
        $data->uid = Carbon::now()->timestamp . RoomContract::count();
        $data->name = $params->name;
        $data->duration = $this->toInt($params->duration);
        $data->leftmonth = $data->duration;
        $data->latestmonth = 0;
        $data->expired = false;
        $data->terms  = $params->terms;
        $data->autorenew  = $params->autorenew;
        $data->startdate  = $this->toDate($params->startdate);

        $room = $this->getRoomById($params->room_id);
        if ($this->isEmpty($room)) {
            return false;
        }
        $data->room()->associate($room);

        $contract = $this->getContractById($params->contract_id);
        if ($this->isEmpty($contract)) {
            return false;
        }
        $data->contract()->associate($contract);

        $tenant = $this->getTenantById($params->tenant_id);
        if ($this->isEmpty($tenant)) {
            return false;
        }
        $data->tenant()->associate($tenant);

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    //Make Sure RoomContract is not empty when calling this function
    private function updateRoomContract($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->roomContractAllCols());
        $data->price  = $this->toDouble($params->price);
        $data->remark = $params->remark;

        $room = $this->getRoomById($params->room_id);
        if ($this->isEmpty($room)) {
            return false;
        }
        $data->room()->associate($room);

        if ($params->property_id) {
            $property = $this->getPropertyById($params->property_id);
            if ($this->isEmpty($property)) {
                return false;
            }
            $data->property()->associate($property);
        }

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteRoomContract($data)
    {

        $data->status = false;
        if ($this->saveModel($data)) {
            return $data->refresh();
        } else {
            return null;
        }
    }


    private function syncWithRentalPayment($data)
    {

        $rentalpayments = $data->rentalpayments()->where('status', true)->get();

        $duration = $data->duration;
        $data->leftmonth = $data->duration - $rentalpayments->count();


        $startdate = $data->startdate;
        $latestmonth = 0;
        for ($x = 0; $x < $duration; $x++) {

            $latestdate = Carbon::parse($startdate)->addMonth($latestmonth);

            $latest = true;
            foreach ($rentalpayments as $rentalpayment) {
                if (Carbon::parse($rentalpayment->rentaldate)->year == Carbon::parse($latestdate)->year && Carbon::parse($rentalpayment->rentaldate)->month == Carbon::parse($latestdate)->month) {

                    $latest = false;
                    break;
                }
            }
            if ($latest) {
                $data->latestmonth = $latestmonth;
                break;
            }

            $latestmonth++;
        }

        if ($data->leftmonth <= 0) {
            $data->expired = true;
            if ($data->autorenew) {
                $startdate =  Carbon::parse($data->startdate)->addYear(1)->addMonth(1)->startOfMonth()->format('Y-m-d');
                $params = collect([
                    'room_id' => $data->room_id,
                    'tenant_id' => $data->tenant_id,
                    'contract_id' => $data->contract_id,
                    'name' => $data->name,
                    'duration' => $data->duration,
                    'terms' => $data->terms,
                    'autorenew' => $data->autorenew,
                    'startdate' => $startdate,
                ]);
                //Convert To Json Object
                $params = json_decode(json_encode($params));
                $roomContract = $this->createRoomContract($params);
                if($this->isEmpty($roomContract)){
                    return null;
                }
             }
        }


        if ($this->saveModel($data)) {
            return $data->refresh();
        } else {
            return null;
        }
    }



    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function roomContractAllCols()
    {

        return ['id', 'uid', 'price', 'remark'];
    }

    public function roomContractDefaultCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }


    public function roomContractListingCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }
    public function roomContractFilterCols()
    {
        return ['keyword'];
    }
}

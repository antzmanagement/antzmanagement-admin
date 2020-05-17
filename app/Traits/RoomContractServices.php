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

        return $data->refresh();
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

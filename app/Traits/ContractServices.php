<?php

namespace App\Traits;

use App\Contract;
use Carbon\Carbon;
use OpenApi\Annotations\Property;

trait ContractServices
{


    private function getContracts($requester)
    {

        $data = collect();

        $data = Contract::where('status', true)->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterContracts($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->contractFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->uid, $keyword) == TRUE || stristr($item->name, $keyword) == TRUE  ) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getContract($uid)
    {

        $data = Contract::where('uid', $uid)->with(['roomcontracts'])->where('status', true)->first();
        return $data;
    }

    private function getContractById($id)
    {
        $data = Contract::where('id', $id)->with(['roomcontracts'])->where('status', true)->first();
        return $data;
    }

    private function createContract($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->contractAllCols());

        $data = new Contract();
        $data->uid = Carbon::now()->timestamp . Contract::count();
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

    //Make Sure Contract is not empty when calling this function
    private function updateContract($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->contractAllCols());
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

    private function deleteContract($data)
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
    public function contractAllCols()
    {

        return ['id', 'uid', 'price', 'remark'];
    }

    public function contractDefaultCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }


    public function contractListingCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }
    public function contractFilterCols()
    {
        return ['keyword'];
    }
}

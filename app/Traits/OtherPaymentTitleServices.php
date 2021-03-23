<?php

namespace App\Traits;

use App\OtherPaymentTitle;
use Carbon\Carbon;

trait OtherPaymentTitleServices
{


    private function getOtherPaymentTitles($requester)
    {

        $data = collect();

        $data = OtherPaymentTitle::where('status', true)->with(['roomcontract' => function ($q) {
            // Query the name field in status table
            $q->with(['tenant' => function ($q1) {
                // Query the name field in status table
                $q1->where('status', true);
            }]);
            $q->with(['room' => function ($q1) {
                // Query the name field in status table
                $q1->where('status', true);
            }]);
            $q->where('status', true);
        }, 'services' => function ($q) {
        }])->get();

        $data = $data->unique('id')->sortByDesc('id')->flatten(1);

        return $data;
    }


    private function filterOtherPaymentTitles($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->otherPaymentTitleFilterCols());

     
        if($params->tenant_id){
            $tenant_id = $params->tenant_id;
            $data = $data->filter(function ($item) use($tenant_id) {
                if($item->roomcontract){
                    if($item->roomcontract->tenant)
                    return $item->roomcontract->tenant->id == $tenant_id;
                }
                return false;
            })->values();
        }

        if($params->room_id){
            $room_id = $params->room_id;
            $data = $data->filter(function ($item) use($room_id) {
                if($item->roomcontract){
                    if($item->roomcontract->room)
                    return $item->roomcontract->room->id == $room_id;
                }
                return false;
            })->values();
        }

        if ($params->sequence) {
            $sequence = $params->sequence;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($sequence) {
                return $item->sequence == $sequence;
            })->values();
        }

        if ($params->fromdate) {
            $date = Carbon::parse($params->fromdate);
            $data = collect($data);
            $data = $data->filter(function ($item) use ($date) {
                return Carbon::parse(data_get($item, 'otherPaymentTitledate'))->gte($date);
            })->values();
        }
        
        if ($params->todate) {
            $date = Carbon::parse($params->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return Carbon::parse(data_get($item, 'otherPaymentTitledate'))->lte($date);
            })->values();
        }

        if($params->service_ids){
            $service_ids = collect($params->service_ids);
            error_log($service_ids);
            $data = $data->filter(function ($item) use($service_ids) {
                $existed = false;
                if($item->services){
                    error_log($item->services->pluck('id'));
                    foreach ($item->services as $service) {
                        if($service_ids->contains($service->id)){
                            $existed = true;
                            break;
                        }
                    }
                }
                error_log($existed. ' ');
                return $existed;
            })->values();
        }
        $data = $data->unique('id');

        return $data;
    }

    private function getOtherPaymentTitleByName($name)
    {

        $data = OtherPaymentTitle::where('name', $name)->first();
        return $data;
    }

    private function getOtherPaymentTitle($uid)
    {

        $data = OtherPaymentTitle::where('uid', $uid)->with(['roomcontract' => function ($q) {
            // Query the name field in status table
            $q->with(['tenant' => function ($q1) {
                // Query the name field in status table
                $q1->where('status', true);
            }]);
            $q->with(['room' => function ($q1) {
                // Query the name field in status table
                $q1->where('status', true);
            }]);
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function getOtherPaymentTitleById($id)
    {
        $data = OtherPaymentTitle::where('id', $id)->with(['roomcontract' => function ($q) {
            // Query the name field in status table
            $q->with(['tenant' => function ($q1) {
                // Query the name field in status table
                $q1->where('status', true);
            }]);
            $q->with(['room' => function ($q1) {
                // Query the name field in status table
                $q1->where('status', true);
            }]);
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function createOtherPaymentTitle($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->otherPaymentTitleAllCols());

        $data = new OtherPaymentTitle();
        $data->uid = Carbon::now()->timestamp . OtherPaymentTitle::count();
        $data->name = $params->name;
        
        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    //Make Sure OtherPaymentTitle is not empty when calling this function
    private function updateOtherPaymentTitle($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->otherPaymentTitleAllCols());
        $data->name = $params->name;

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteOtherPaymentTitle($data)
    {
        try {
            $data->services()->sync([]);
            $data->delete();

        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }


        return $data;
    }

    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function otherPaymentTitleAllCols()
    {

        return ['id', 'uid', 'price', 'remark'];
    }

    public function otherPaymentTitleDefaultCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }


    public function otherPaymentTitleListingCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }
    public function otherPaymentTitleFilterCols()
    {
        return ['fromdate', 'todate', 'tenant_id', 'room_id', 'sequence', 'service_ids'];
    }
}

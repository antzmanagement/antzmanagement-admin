<?php

namespace App\Traits;

use App\Service;
use App\ServiceType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait ServiceServices
{


    private function getServices($requester)
    {

        $data = collect();

        $data = Service::where('status', true)->get();

        $data = $data->unique('id')->sortBy(function ($item, $key) {
            return $item->name;
        })->flatten(1);

        return $data;
    }


    private function filterServices($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->serviceFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->name, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            });
        }
       

        $data = $data->unique('id')->sortBy(function ($item, $key) {
            return $item->name;
        })->flatten(1);

        return $data;
    }

    private function getService($uid)
    {

        $data = Service::where('uid', $uid)->where('status', true)->first();
        return $data;
    }

    private function getServiceById($id)
    {
        $data =  Service::where('id', $id)->where('status', true)->first();
        return $data;
    }

    private function createService($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->serviceAllCols());

        $data = new Service();
        $data->uid = Carbon::now()->timestamp . Service::count();
        $data->name  = $params->name;
        $data->text =  Str::ucfirst($params->name);
        $data->desc = $params->desc;
        $data->price = $this->toDouble($params->price);

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Service is not empty when calling this function
    private function updateService($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->serviceAllCols());
        $data->name  = $params->name;
        $data->text =  Str::ucfirst($params->name);
        $data->desc = $params->desc;
        $data->price = $this->toDouble($params->price);


        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteService($data)
    {
        $data->status = false;
        try {
            $ids = $data->room_types()->wherePivot('status', true)->get();
            $ids = $ids->pluck('id');
            $data->room_types()->updateExistingPivot($ids, ['status' => false]);

            $ids = $data->room_contracts_with_orig()->wherePivot('status', true)->get();
            $ids = $ids->pluck('id');
            $data->room_contracts_with_orig()->updateExistingPivot($ids, ['status' => false]);

            $ids = $data->room_contracts_with_add_on()->wherePivot('status', true)->get();
            $ids = $ids->pluck('id');
            $data->room_contracts_with_add_on()->updateExistingPivot($ids, ['status' => false]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if ($this->saveModel($data)) {
            return $data->refresh();
        } else {
            return null;
        }

        return $data->refresh();
    }


    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function serviceAllCols()
    {

        return ['id', 'uid', 'name', 'address', 'postcode', 'state', 'city', 'country', 'price', 'status'];
    }

    public function serviceDefaultCols()
    {

        return [
            'id', 'channel_id', 'playlist_id', 'uid',
            'title', 'desc', 'icon', 'servicepublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }


    public function serviceListingCols()
    {

        return [
            'id', 'uid', 'name', 'email',
            'icno', 'tel1', 'password', 'servicepublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }
    public function serviceFilterCols()
    {

        return ['keyword', 'serviceTypes'];
    }
}

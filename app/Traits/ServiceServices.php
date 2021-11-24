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


    private function filterServices( $params, $take, $skip)
    {
        $params = $this->checkUndefinedProperty($params, $this->serviceFilterCols());

        $query = Service::query();
        $query->orderBy('name');
        if ($params->keyword) {
            $keyword = $params->keyword;
            $query->where('name', 'like', '%' . $keyword . '%');
        }
       

        $total = $query->count();
        if($skip){
            $query->skip($skip);
        }
        if($take){
            $query->take($take);
        }
        $data = $query->where('status', true)->get();

        $result['data'] = $data;
        $result['total'] = $total;

        return  $result;
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
            $data->room_types()->sync([]);
            $data->room_contracts_with_orig()->sync([]);
            $data->room_contracts_with_add_on()->sync([]);
            $data->payments()->sync([]);
            
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

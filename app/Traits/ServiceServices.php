<?php

namespace App\Traits;

use App\Service;
use App\ServiceType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait ServiceServices
{

    use AllServices;


    private function getServices($requester)
    {

        $data = collect();

        $data = Service::where('status', true)->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterServices($data, $params)
    {
        $data = $this->globalFilter($data, $params);
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
       

        $data = $data->unique('id');

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
        $data->text = $params->text;
        $data->desc = $params->desc;
        $data->icon = $params->icon;

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
        $data->text = $params->text;
        $data->desc = $params->desc;
        $data->icon = $params->icon;

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteService($data)
    {

        $data->serviceTypes()->detach();
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
    public function serviceAllCols()
    {

        return ['id', 'uid', 'name', 'address', 'postcode', 'state', 'city', 'country', 'price', 'status'];
    }

    public function serviceDefaultCols()
    {

        return [
            'id', 'channel_id', 'playlist_id', 'uid',
            'title', 'desc', 'servicepath', 'servicepublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
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

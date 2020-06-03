<?php

namespace App\Traits;

use App\Property;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait PropertyServices
{

    use AllServices;


    private function getProperties($requester)
    {

        $data = collect();

        $data = Property::where('status', true)->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterProperties($data, $params)
    {
        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params, $this->propertyFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->uid, $keyword) == TRUE || stristr($item->name, $keyword) == TRUE  ) {
                    return true;
                } else {
                    return false;
                }
            });
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getProperty($uid)
    {

        $data = Property::where('uid', $uid)->where('status', true)->first();
        return $data;
    }

    private function getPropertyById($id)
    {
        $data = Property::where('id', $id)->where('status', true)->first();
        return $data;
    }

    private function createProperty($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->propertyAllCols());

        $data = new Property();
        $data->uid = Carbon::now()->timestamp . Property::count();
        $data->name  = $params->name;
        $data->text = $params->text;
        $data->desc = $params->desc;
        $data->icon = $params->icon;

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Property is not empty when calling this function
    private function updateProperty($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->propertyAllCols());
        $data->name  = $params->name;
        $data->text = $params->text;
        $data->desc = $params->desc;
        $data->icon = $params->icon;

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteProperty($data)
    {

        $data->propertyTypes()->detach();
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
    public function propertyAllCols()
    {

        return ['id', 'uid', 'name', 'address', 'postcode', 'state', 'city', 'country', 'price', 'status'];
    }

    public function propertyDefaultCols()
    {

        return [
            'id', 'channel_id', 'playlist_id', 'uid',
            'title', 'desc', 'propertypath', 'propertypublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }


    public function propertyListingCols()
    {

        return [
            'id', 'uid', 'name', 'email',
            'icno', 'tel1', 'password', 'propertypublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }
    public function propertyFilterCols()
    {

        return ['keyword', 'propertyTypes'];
    }

}

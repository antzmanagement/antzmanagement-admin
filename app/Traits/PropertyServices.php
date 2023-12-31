<?php

namespace App\Traits;

use App\Property;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

trait PropertyServices
{


    private function getProperties($requester)
    {

        $data = collect();

        $data = Property::where('status', true)->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterProperties($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->propertyFilterCols());

        if ($params->name) {
            $name = $params->name;
            $data = $data->filter(function ($item) use ($name) {
                //check string exist inside or not
                if (stristr($item->name, $name) == TRUE   ) {
                    return true;
                } else {
                    return false;
                }
            });
        }


        if ($params->category) {
            $category = $params->category;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($category) {
                return $item->category== $category;
            })->values();
        }


        $data = $data->unique('id')->sortBy(function ($item, $key) {
            return $item->category;
        })->flatten(1);

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
        $data->category = $params->category;
        $data->price = $this->toDouble($params->price);

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
        $data->category = $params->category;
        $data->price = $this->toDouble($params->price);

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteProperty($data)
    {

        $data->status = false;

        $maintenances = $data->maintenances()->where('status', true)->get();
        foreach ($maintenances as $maintenance) {
            $this->deleteMaintenance($maintenance);
        }
        $rooms = $data->rooms()->where('rooms.status', true)->get();
        foreach ($rooms as $room) {
            $room->properties()->detach([$data->id]);
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
    public function propertyAllCols()
    {

        return ['id', 'uid', 'name', 'address', 'postcode', 'state', 'city', 'country', 'price', 'status', 'desc', 'category'];
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

        return ['name', 'category'];
    }

}

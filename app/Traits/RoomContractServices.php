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

        $data = RoomContract::where('status', true)->with(['room', 'tenant', 'contract'])->get();

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

        $data = $data->unique('id');

        return $data;
    }

    private function getRoomContract($uid)
    {

        $data = RoomContract::where('uid', $uid)->with(['room' => function ($q) {
            // Query the name field in status table
            $q->with(['roomtypes' => function ($q1) {
                // Query the name field in status table
                $q1->wherePivot('status', true);
            }]);
            $q->where('status', true);
        }, 'contract' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'tenant' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'addonservices' => function ($q) {
            // Query the name field in status table
            $q->wherePivot('status', true);
        }, 'origservices' => function ($q) {
            // Query the name field in status table
            $q->wherePivot('status', true);
        }, 'rentalpayments' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function getRoomContractById($id)
    {
        $data = RoomContract::where('id', $id)->with(['room' => function ($q) {
            // Query the name field in status table
            $q->with(['roomtypes' => function ($q1) {
                // Query the name field in status table
                $q1->wherePivot('status', true);
            }]);
            $q->where('status', true);
        }, 'contract' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'tenant' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'addonservices' => function ($q) {
            // Query the name field in status table
            $q->wherePivot('status', true);
        }, 'origservices' => function ($q) {
            // Query the name field in status table
            $q->wherePivot('status', true);
        }, 'rentalpayments' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }])->where('status', true)->first();
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
        $data->booking_fees  = $this->toDouble($params->booking_fees);
        $data->deposit  = $this->toDouble($params->deposit);
        $data->outstanding_deposit  = $this->toDouble($data->deposit - $data->booking_fees);
        $data->rental  = $this->toDouble($params->rental);

        $room = $this->getRoomById($params->room_id);
        if ($this->isEmpty($room)) {
            return null;
        }
        $data->room()->associate($room);

        $contract = $this->getContractById($params->contract_id);
        if ($this->isEmpty($contract)) {
            return null;
        }
        
        $data->contract()->associate($contract);

        $tenant = $this->getTenantById($params->tenant_id);
        if ($this->isEmpty($tenant)) {
            return null;
        }
        $data->tenant()->associate($tenant);

        if (!$this->saveModel($data)) {
            return null;
        }


        $data = $data->refresh();
        foreach ($params->add_on_service_ids as $id) {
            $service = $this->getServiceById($id);
            if ($this->isEmpty($service)) {
                return null;
            }
            try {
                $data->addonservices()->syncWithoutDetaching([$id => ['status' => true]]);
            } catch (Exception $e) {
                return null;
            }
        }
        foreach ($params->orig_service_ids as $id) {
            $service = $this->getServiceById($id);
            if ($this->isEmpty($service)) {
                return null;
            }
            try {
                $data->origservices()->syncWithoutDetaching([$id => ['status' => true]]);
            } catch (Exception $e) {
                return null;
            }
        }


        return $data->refresh();
    }

    //Make Sure RoomContract is not empty when calling this function
    private function updateRoomContract($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->roomContractAllCols());
        $data->name = $params->name;
        $data->duration = $this->toInt($params->duration);
        $data->leftmonth = $data->duration;
        $data->latestmonth = 0;
        $data->expired = false;
        $data->terms  = $params->terms;
        $data->autorenew  = $params->autorenew;
        $data->startdate  = $this->toDate($params->startdate);
        $data->booking_fees  = $this->toDouble($params->booking_fees);
        $data->deposit  = $this->toDouble($params->deposit);
        $data->outstanding_deposit  = $this->toDouble($params->outstanding_deposit);
        $data->rental  = $this->toDouble($params->rental);

        $room = $this->getRoomById($params->room_id);
        if ($this->isEmpty($room)) {
            return null;
        }
        $data->room()->associate($room);

        $contract = $this->getContractById($params->contract_id);
        if ($this->isEmpty($contract)) {
            return null;
        }
        $data->contract()->associate($contract);

        $tenant = $this->getTenantById($params->tenant_id);
        if ($this->isEmpty($tenant)) {
            return null;
        }
        $data->tenant()->associate($tenant);

        if (!$this->saveModel($data)) {
            return null;
        }


        $data = $data->refresh();
        foreach ($params->add_on_service_ids as $id) {
            $service = $this->getServiceById($id);
            if ($this->isEmpty($service)) {
                return null;
            }
            try {
                $data->addonservices()->syncWithoutDetaching([$id => ['status' => true]]);
            } catch (Exception $e) {
                return null;
            }
        }
        foreach ($params->orig_service_ids as $id) {
            $service = $this->getServiceById($id);
            if ($this->isEmpty($service)) {
                return null;
            }
            try {
                $data->origservices()->syncWithoutDetaching([$id => ['status' => true]]);
            } catch (Exception $e) {
                return null;
            }
        }


        return $data->refresh();
    }

    private function deleteRoomContract($data)
    {

        $rentalpayments = $data->rentalpayments()->where('status', true)->get();
        foreach ($rentalpayments as $rentalpayment) {
            $this->deleteRentalPayment($rentalpayment);
        }
        $data = $this->syncWithRentalPayment($data);


        $childrenroomcontracts = $data->childrenroomcontracts()->where('status', true)->get();
        foreach ($childrenroomcontracts as $childrenroomcontract) {
            $this->deleteRoomContract($childrenroomcontract);
        }
        try {
            $ids = $data->addonservices()->wherePivot('status', true)->get();
            $ids = $ids->pluck('id');
            $data->addonservices()->updateExistingPivot($ids, ['status' => false]);

            $ids = $data->origservices()->wherePivot('status', true)->get();
            $ids = $ids->pluck('id');
            $data->origservices()->updateExistingPivot($ids, ['status' => false]);

        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }


        $data->status = false;
        if ($this->saveModel($data)) {
            return $data->refresh();
        } else {
            return null;
        }

        return $data->refresh();
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
                $origServiceIds = $data->origservices()->wherePivot('status', true)->get();
                $origServiceIds = $origServiceIds->pluck('id');
                $addOnServicesIds = $data->addonservices()->wherePivot('status', true)->get();
                $addOnServicesIds = $addOnServicesIds->pluck('id');
                $params = collect([
                    'room_id' => $data->room_id,
                    'tenant_id' => $data->tenant_id,
                    'contract_id' => $data->contract_id,
                    'orig_service_ids' => $origServiceIds,
                    'add_on_service_ids' => $addOnServicesIds,
                    'name' => $data->name,
                    'duration' => $data->duration,
                    'terms' => $data->terms,
                    'autorenew' => $data->autorenew,
                    'startdate' => $startdate,
                    'rental' => $data->price,
                    'deposit' => $data->deposit,
                    'booking_fees' => $data->booking_fees,
                    'outstanding_deposit' => $data->outstanding_deposit,
                ]);
                //Convert To Json Object
                $params = json_decode(json_encode($params));
                $roomContract = $this->createRoomContract($params);
                if($this->isEmpty($roomContract)){
                    return null;
                }
             }
        }else{
           $data->expired = false; 
        }


        if ($this->saveModel($data)) {
            return $data->refresh();
        } else {
            return null;
        }
    }

    public function transferRoomContract($room_contract_id, $tenant_id)
    {
        
        $roomContract = $this->getRoomContractById($room_contract_id);
        if($this->isEmpty($roomContract)){
            return null;
        }

        $tenant = $this->getTenantById($tenant_id);
        if($this->isEmpty($tenant)){
            return null;
        }

        error_log($roomContract->tenant_id);
        $roomContract->tenant()->associate($tenant);

        if (!$this->saveModel($roomContract)) {
            return null;
        }

        error_log($roomContract->refresh()->tenant_id);

        return $roomContract->refresh();
    }


    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function roomContractAllCols()
    {
        return ['id', 'uid', 'name', 'room_id', 'tenant_id','duration', 'leftmonth' ,
        'latestmonth', 'expired', 'terms', 'autorenew', 'startdate',
        'booking_fees','deposit','rental', 'outstanding_deposit',
         'orig_service_ids', 'add_on_service_ids'];
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

<?php

namespace App\Traits;

use App\RoomContract;
use Carbon\Carbon;
use OpenApi\Annotations\Property;

trait RoomContractServices
{



    private function getRoomContracts($requester)
    {

        $data = collect();

        $data = RoomContract::where('status', true)->with(['room' => function ($q) {
            // Query the name field in status table
            $q->with(['roomtypes' => function ($q1) {
                // Query the name field in status table
                $q1->wherePivot('status', true);
            }]);
            $q->with(['owners' => function ($q1) {
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
        }, 'childrenroomcontracts' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'parentroomcontract' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'payments' => function ($q) {
            // Query the name field in status table
            $q->with('services');
            $q->where('status', true);
        }])->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterRoomContracts($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->roomContractFilterCols());

        if($params->tenant_id){
            $tenant_id = $params->tenant_id;
            $data = $data->filter(function ($item) use($tenant_id) {
                if($item->tenant){
                    return $item->tenant->id == $tenant_id;
                }
                return false;
            });
        }

        if($params->room_id){
            $room_id = $params->room_id;
            $data = $data->filter(function ($item) use($room_id) {
                if($item->room){
                    return $item->room->id == $room_id;
                }
                return false;
            });
        }

        if($params->owner_id){
            $owner_id = $params->owner_id;
            $data = $data->filter(function ($item) use($owner_id) {
                if($item->room){
                    if($item->room->owners){
                        return $item->room->owners->contains('id' , $owner_id);
                    }
                }
                return false;
            });
        }

        if($params->service_ids){
            $service_ids = collect($params->service_ids);
            error_log($service_ids);
            $data = $data->filter(function ($item) use($service_ids) {
                $existed = false;
                if($item->origservices || $item->addonservices ){
                    $services = collect($item->origservices)->concat($item->addonservices);
                    error_log($services->pluck('id'));
                    foreach ($services as $service) {
                        if($service_ids->contains($service->id)){
                            $existed = true;
                            break;
                        }
                    }
                }
                error_log($existed. ' ');
                return $existed;
            });
        }

        if ($params->sequence) {
            $sequence = $params->sequence;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($sequence) {
                return $item->sequence == $sequence;
            })->values();
        }


        if (property_exists($params, 'outstanding_deposit') && $params->outstanding_deposit != null) {
            error_log('filter outstanding_deposit');
            $outstanding_deposit = $params->outstanding_deposit;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($outstanding_deposit) {
                return $outstanding_deposit ? $item->outstanding_deposit > 0 : $item->outstanding_deposit == 0;
            })->values();
        }
        
        if (property_exists($params, 'checkedout') && $params->checkedout != null) {
            error_log('filter checkedout');
            $checkedout = $params->checkedout;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($checkedout) {
                return $item->checkedout == $checkedout;
            })->values();
        }


        if ($params->fromdate) {
            $date = Carbon::parse($params->fromdate);
            $data = collect($data);
            $data = $data->filter(function ($item) use ($date) {
                return Carbon::parse(data_get($item, 'startdate'))->gte($date);
            });
        }
        
        if ($params->todate) {
            $date = Carbon::parse($params->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return Carbon::parse(data_get($item, 'startdate'))->lte($date);
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
        }, 'childrenroomcontracts' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'parentroomcontract' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'payments' => function ($q) {
            // Query the name field in status table
            $q->with('services');
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
        }, 'childrenroomcontracts' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'parentroomcontract' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'payments' => function ($q) {
            // Query the name field in status table
            $q->with('services');
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
        $data->left = $data->duration;
        $data->latest = 0;
        $data->expired = false;
        $data->terms  = $params->terms;
        // $data->autorenew  = $params->autorenew;
        $data->autorenew  = false;
        $data->penalty_day  = $this->toInt($params->penalty_day);
        $data->penalty  = $this->toDouble($params->penalty);
        $data->startdate  = $this->toDate($params->startdate);
        $data->enddate  = $this->toDate($params->enddate);
        $data->rental_type  = $params->rental_type;
        $data->booking_fees  = $this->toDouble($params->booking_fees);
        $data->agreement_fees  = $this->toDouble($params->agreement_fees);
        $data->deposit  = $this->toDouble($params->deposit);
        $data->agreement_fees  = $this->toDouble($params->agreement_fees);
        $data->outstanding_deposit  = $this->toDouble($data->deposit - $data->booking_fees);
        $data->rental  = $this->toDouble($params->rental);
        $data->sequence = $this->toInt($params->sequence);

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

        if($params->room_contract_id){
            $roomcontract = $this->getRoomContractById($params->room_contract_id);
            if ($this->isEmpty($tenant)) {
                return null;
            }
            $data->parentroomcontract()->associate($roomcontract);
        }

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

        $this->syncRoomStatus($data->room);

        return $data->refresh();
    }

    //Make Sure RoomContract is not empty when calling this function
    private function updateRoomContract($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->roomContractAllCols());
        // $data->autorenew  = $params->autorenew;
        $data->autorenew  = false;
        $data->booking_fees  = $this->toDouble($params->booking_fees);
        $data->agreement_fees  = $this->toDouble($params->agreement_fees);
        $data->outstanding_deposit  = $this->toDouble($params->outstanding_deposit);
        $data->deposit  = $this->toDouble($params->deposit);
        $data->rental  = $this->toDouble($params->rental);

        $room = $this->getRoomById($params->room_id);
        if ($this->isEmpty($room)) {
            return null;
        }
        $data->room()->associate($room);

        $tenant = $this->getTenantById($params->tenant_id);
        if ($this->isEmpty($tenant)) {
            return null;
        }
        $data->tenant()->associate($tenant);

        $subcontracts = $data->childrenroomcontracts;
        foreach ($subcontracts as $subcontract) {
            $subcontract->booking_fees  = $this->toDouble($params->booking_fees);
            $subcontract->agreement_fees  = $this->toDouble($params->agreement_fees);
            $subcontract->deposit  = $this->toDouble($params->deposit);
            $subcontract->outstanding_deposit  = $this->toDouble($params->outstanding_deposit);
            $subcontract->rental  = $this->toDouble($params->rental);
            if (!$this->saveModel($subcontract)) {
                return null;
            }

        }

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


        $this->syncRoomStatus($data->room);
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

        $this->syncRoomStatus($data->room);
        return $data->refresh();
    }


    private function syncWithRentalPayment($data)
    {

        $rentalpayments = $data->rentalpayments()->where('status', true)->get();

        $duration = $data->duration;
        $data->left = $data->duration - $rentalpayments->count();


        $startdate = $data->startdate;
        $latest = 0;
        for ($x = 0; $x < $duration; $x++) {

            if($data->rental_type == 'day'){
                $latestdate = Carbon::parse($startdate)->addDay($latest);
            }else{
                $latestdate = Carbon::parse($startdate)->addMonth($latest);
            }

            $isLatest = true;
            foreach ($rentalpayments as $rentalpayment) {
                if (Carbon::parse($rentalpayment->rentaldate)->year == Carbon::parse($latestdate)->year && Carbon::parse($rentalpayment->rentaldate)->month == Carbon::parse($latestdate)->month && Carbon::parse($rentalpayment->rentaldate)->day == Carbon::parse($latestdate)->day) {
                    $isLatest = false;
                    break;
                }
            }
            if ($isLatest) {
                $data->latest = $latest;
                break;
            }

            $latest++;
        }

        if ($data->left <= 0) {
            $data->expired = true;
            if ($data->autorenew) {

                $contract = $data->contract;
                if ($this->isEmpty($contract)) {
                    DB::rollBack();
                    return $this->notFoundResponse('Contract');
                }
        

                if($contract->rental_type == 'day'){
                    $startdate = Carbon::parse($data->startdate)->addDay($duration + 1)->format('Y-m-d');
                    $enddate = Carbon::parse($startdate)->addDay($duration)->format('Y-m-d');
                }else{
                    $startdate = Carbon::parse($data->startdate)->addMonth($duration + 1)->format('Y-m-d');
                    $enddate = Carbon::parse($startdate)->addMonth($duration)->format('Y-m-d');
                }

                $origServiceIds = $data->origservices()->wherePivot('status', true)->get();
                $origServiceIds = $origServiceIds->pluck('id');
                $addOnServicesIds = $data->addonservices()->wherePivot('status', true)->get();
                $addOnServicesIds = $addOnServicesIds->pluck('id');

                if($data->room_contract_id){
                    $parentId = $data->room_contract_id;
                }else{
                    $parentId = $data->id;
                }
                $params = collect([
                    'room_id' => $data->room_id,
                    'tenant_id' => $data->tenant_id,
                    'contract_id' => $data->contract_id,
                    'room_contract_id' => $parentId,
                    'orig_service_ids' => $origServiceIds,
                    'add_on_service_ids' => $addOnServicesIds,
                    'name' => $data->room->unit . '_' . $startdate. '_' . $contract->name,
                    'duration' => $duration,
                    'terms' => $data->terms,
                    'penalty' => $contract->penalty,
                    'penalty_day' => $contract->penalty_day,
                    'rental_type' => $contract->rental_type,
                    'autorenew' => $contract->autorenew,
                    'startdate' => $startdate,
                    'enddate' => $enddate,
                    'rental' => $data->rental,
                    'deposit' => $data->deposit,
                    'agreement_fees' => $data->agreement_fees,
                    'booking_fees' => $data->booking_fees,
                    'agreement_fees' => $data->agreement_fees,
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

    private function checkoutRoomContract($data,  $params)
    {

        $subcontracts = [];
        if(!$data){
            return null;
        }else{
            $totalRental = $data->rentalpayments()->where('status', true)->count();
            $totalPaidRental = $data->rentalpayments()->where('paid', true)->where('status', true)->count();
            $subcontracts = $data->childrenroomcontracts;
            foreach ($subcontracts as $subcontract) {
                $totalRental  += $subcontract->rentalpayments()->where('status', true)->count();
                $totalPaidRental  += $subcontract->rentalpayments()->where('paid', true)->where('status', true)->count();
            }
            if($totalPaidRental != $totalRental){
                return null;
            }
        }

        $data->checkout_date  = $this->toDate($params->checkout_date);
        $data->return_deposit  = $this->toDouble($params->return_deposit);
        $data->checkout_charges  = $this->toDouble($params->checkout_charges);
        $data->checkout_remark  = $params->checkout_remark;
        $data->checkedout = true;

        $totalPaidRental = 0;
        $totalPaidRental  += $data->rentalpayments()->where('paid', true)->where('status', true)->count();
        if($totalPaidRental > 12 && $data->rental_type == 'month'){
            $data->commission = $data->rental;
        }else{
            if($data->rental_type == 'month'){
                $data->commission = $this->toDouble(($data->rental / 12) * $totalPaidRental);
            }
        }

        $subcontracts = $data->childrenroomcontracts;
        foreach ($subcontracts as $subcontract) {
            $subcontract->checkout_date  = $this->toDate($params->checkout_date);
            $subcontract->return_deposit  = $this->toDouble($params->return_deposit);
            $subcontract->checkout_charges  = $this->toDouble($params->checkout_charges);
            $subcontract->checkout_remark  = $params->checkout_remark;
            $subcontract->checkedout = true;
            $totalPaidRental  += $subcontract->rentalpayments()->where('paid', true)->where('status', true)->count();
            if($totalPaidRental > 12 && $subcontract->rental_type == 'month'){
                $subcontract->commission = $subcontract->rental;
            }else{
                if($subcontract->rental_type == 'month'){
                    $subcontract->commission = $this->toDouble(($subcontract->rental / 12) * $totalPaidRental);
                }
            }

            if (!$this->saveModel($subcontract)) {
                return null;
            }

        }

        $parentcontract = $data->parentroomcontract;
        if($parentcontract){
            $parentcontract->checkout_date  = $this->toDate($params->checkout_date);
            $parentcontract->return_deposit  = $this->toDouble($params->return_deposit);
            $parentcontract->checkout_charges  = $this->toDouble($params->checkout_charges);
            $parentcontract->checkout_remark  = $params->checkout_remark;
            $parentcontract->checkedout = true;
            $totalPaidRental  += $parentcontract->rentalpayments()->where('paid', true)->where('status', true)->count();
            if($totalPaidRental > 12 && $parentcontract->rental_type == 'month'){
                $parentcontract->commission = $parentcontract->rental;
            }else{
                if($parentcontract->rental_type == 'month'){
                    $parentcontract->commission = $this->toDouble(($parentcontract->rental / 12) * $totalPaidRental);
                }
            }

            if (!$this->saveModel($parentcontract)) {
                return null;
            }
        }


        if (!$this->saveModel($data)) {
            return null;
        }

        $this->syncRoomStatus($data->room);
        return $data->refresh();
    }

    


    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function roomContractAllCols()
    {
        return ['id', 'uid', 'name', 'room_id', 'tenant_id','duration', 'left' ,
        'latest', 'expired', 'terms', 'autorenew', 'startdate',
        'booking_fees','deposit','rental', 'outstanding_deposit',
         'orig_service_ids', 'add_on_service_ids', 'room_contract_id'];
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
        return ['keyword', 'fromdate', 'todate', 'tenant_id', 'owner_id', 'service_ids', 'room_id', 'checkedout', 'outstanding_deposit', 'sequence'];
    }
}

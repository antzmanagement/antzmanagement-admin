<?php

namespace App\Traits;

use App\RentalPayment;
use Carbon\Carbon;

trait RentalPaymentServices
{


    private function getRentalPayments($requester, $params = null)
    {

        $params = $this->checkUndefinedProperty($params, $this->rentalPaymentFilterCols());
        $data = collect();

        if (property_exists($params, 'status') && $params->status != null) {   
            $data = RentalPayment::where('status', $params->status)->with(['roomcontract' => function ($q) {
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
            }, 'issueby', 'deletedby'])->get();
        }else{
            $data = RentalPayment::where('status', true)->with(['roomcontract' => function ($q) {
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
            }, 'issueby'])->get();
        }

        $data = $data->unique('id')->sortByDesc('sequence')->flatten(1);

        return $data;
    }


    private function filterRentalPayments( $params, $take, $skip)
    {
        $params = $this->checkUndefinedProperty($params, $this->rentalPaymentFilterCols());


        $query = RentalPayment::query();
        $query->orderBy('sequence', 'DESC');

        if($params->tenant_id){
            $tenant_id = $params->tenant_id;
            $query->whereHas('roomcontract', function($q) use($tenant_id) {
                    $q->whereHas('tenant', function($q1) use($tenant_id) {
                        $q1->where('id', $tenant_id);
                });
            });
        }

        if($params->room_id){
            $room_id = $params->room_id;
            $query->whereHas('roomcontract', function($q) use($room_id) {
                    $q->whereHas('room', function($q1) use($room_id) {
                        $q1->where('id', $room_id);
                });
            });
        }

        if ($params->sequence) {
            $sequence = $params->sequence;
            $query->where('sequence', $sequence);
        }


        if (property_exists($params, 'penalty') && $params->penalty != null) {
            $penalty = $params->penalty;
            if($penalty){
                $query->where('penalty', '>' , 0);
            }else{
                $query->where('penalty' , 0);
            }
        }
        
        if (property_exists($params, 'paid') && $params->paid != null) {
            $paid = $params->paid;
            $query->where('paid' , $params->paid);
        }

        if ($params->fromdate) {
            $date = Carbon::parse($params->fromdate);
            $query->whereDate('rentaldate', '>=', $date );
        }
        
        if ($params->todate) {
            $date = Carbon::parse($params->todate)->endOfDay();
            $query->whereDate('rentaldate', '<=', $date );
        }

        if ($params->paymentfromdate) {
            $date = Carbon::parse($params->paymentfromdate);
            $query->whereDate('paymentdate', '>=', $date );
        }
        
        if ($params->paymenttodate) {
            $date = Carbon::parse($params->paymenttodate);
            $query->whereDate('paymentdate', '<=', $date );
        }
        
        if ($params->paymentmethod) {
            $paymentmethod = $params->paymentmethod;
            $query->where('paymentmethod' , $params->paymentmethod);
        }
        
        $total = $query->count();
        if($skip){
            $query->skip($skip);
        }
        if($take){
            $query->take($take);
        }else{
            $query->take(10);
        }
        if (property_exists($params, 'status') && $params->status != null) {   
            $data = $query->where('status', $params->status)->with(['roomcontract' => function ($q) {
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
            }, 'issueby', 'deletedby'])->get();
        }else{
            $data = $query->where('status', true)->with(['roomcontract' => function ($q) {
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
            }, 'issueby'])->get()->unique('id')->flatten(1);;
        }

        $result['data'] = $data;
        $result['total'] = $total;

        return  $result;
    }

    private function getRentalPayment($uid)
    {

        $data = RentalPayment::where('uid', $uid)->with(['roomcontract' => function ($q) {
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
        }, 'issueby'])->where('status', true)->first();
        return $data;
    }

    private function getRentalPaymentById($id)
    {
        $data = RentalPayment::where('id', $id)->with(['roomcontract' => function ($q) {
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
        }, 'issueby'])->where('status', true)->first();
        return $data;
    }

    private function createRentalPayment($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->rentalPaymentAllCols());

        $data = new RentalPayment();
        $data->uid = Carbon::now()->timestamp . RentalPayment::count();
        $data->price = $this->toDouble($params->price);
        $data->payment = 0;
        $data->penalty = 0;
        $data->outstanding = $this->toDouble($data->price + $data->penalty + $data->processing_fees - $data->payment);
        $data->paid = false;
        $data->rentaldate = $this->toDate($params->rentaldate);
        $data->paymentdate = null;
        $data->remark = $params->remark;
        
        $roomContract = $this->getRoomContractById($params->room_contract_id);
        if ($this->isEmpty($roomContract)) {
            return false;
        }
        $data->roomcontract()->associate($roomContract);


        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    //Make Sure RentalPayment is not empty when calling this function
    private function updateRentalPayment($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->rentalPaymentAllCols());
        $data->price = $this->toDouble($params->price);
        $data->payment = $this->toDouble($params->payment);
        $data->penalty =  $this->toDouble($params->penalty);
        $data->processing_fees =  $this->toDouble($params->processing_fees);
        $data->service_fees =  $this->toDouble($params->service_fees);
        $data->outstanding = $this->toDouble($data->price + $data->penalty + $data->processing_fees  - $data->payment);
        $data->paid = $params->paid;
        $data->rentaldate = $this->toDate($params->rentaldate);
        $data->sequence = $this->toInt($params->sequence);
        $data->remark = $params->remark;
        $data->penaltyEdited = $params->penaltyEdited == true;
        $data->processingEdited = $params->processingEdited == true;
        
        $roomContract = $this->getRoomContractById($params->room_contract_id);
        if ($this->isEmpty($roomContract)) {
            return false;
        }
        $data->roomcontract()->associate($roomContract);

        if($params->paid){
            
            $data->receive_from = $params->receive_from;
            $data->paymentmethod = $params->paymentmethod;
            $data->referenceno = $params->referenceno;
            $data->paymentdate = $this->toDate($params->paymentdate);
            if($params->issueby){
                $issueBy = $this->getUserById($params->issueby);
                if ($this->isEmpty($issueBy)) {
                    return false;
                }
                $data->issueby()->associate($issueBy);
            }
        }

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteRentalPayment($data, $user_id = null)
    {

        $data->status = false;

        if($user_id){
            $data->deletedby = $user_id;
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
    public function rentalPaymentAllCols()
    {

        return ['id', 'uid', 'price', 'remark', 'referenceno', 'issueby', 'penaltyEdited', 'processingEdited'];
    }

    public function rentalPaymentDefaultCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }


    public function rentalPaymentListingCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }
    public function rentalPaymentFilterCols()
    {
        return ['fromdate', 'todate', 'tenant_id', 'room_id', 'penalty', 'paid', 'sequence', 'paymentfromdate', 'paymenttodate', 'paymentmethod', 
        'receive_from', 'paymentmethod', 'issueby', 'status'];
    }
}

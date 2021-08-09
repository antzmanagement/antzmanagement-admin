<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\RentalPayment;
use App\RoomContract;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

class RentalPaymentController extends Controller
{
    use AllServices;

    private $controllerName = '[RentalPaymentController]';

    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of rentalPayments.');
        // api/rentalPayment (GET)
        $rentalPayments = $this->getRentalPayments($request->user());
        if ($this->isEmpty($rentalPayments)) {
            return $this->errorPaginateResponse('RentalPayments');
        } else {
            return $this->successPaginateResponse('RentalPayments', $rentalPayments, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered rentalPayments.');
        // api/rentalPayment/filter (GET)
        $params = collect([
            'tenant_id' => $request->tenant_id,
            'room_id' => $request->room_id,
            'paymentfromdate' => $request->paymentfromdate,
            'paymenttodate' => $request->paymenttodate,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'penalty' => $request->penalty,
            'paid' => $request->paid,
            'sequence' => $request->sequence,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $rentalPayments = $this->getRentalPayments($request->user());
        $rentalPayments = $this->filterRentalPayments($rentalPayments, $params);

        if ($this->isEmpty($rentalPayments)) {
            return $this->errorPaginateResponse('RentalPayments');
        } else {
            return $this->successPaginateResponse('RentalPayments', $rentalPayments, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function show(Request $request, $uid)
    {
        // api/rentalPayment/{rentalPaymentid} (GET)
        error_log($this->controllerName . 'Retrieving rentalPayment of uid:' . $uid);
        $rentalPayment = $this->getRentalPayment($uid);
        if ($this->isEmpty($rentalPayment)) {
            $data['data'] = null;
            return $this->notFoundResponse('RentalPayment');
        } else {
            return $this->successResponse('RentalPayment', $rentalPayment, 'retrieve');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/rentalPayment (POST)
        $this->validate($request, [
            'room_contract_id' => 'required|numeric',
        ]);
        error_log($this->controllerName . 'Creating rentalPayment.');

        $roomContract = $this->getRoomContractById($request->room_contract_id);

        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if ($roomContract->left > 0 ) {

            $room = $roomContract->room;
            if ($this->isEmpty($room)) {
                DB::rollBack();
                return $this->errorResponse();
            }

            if($roomContract->rental_type == 'month'){
                $rentalDate = $this->toDate(Carbon::parse($roomContract->startdate)->addMonth($roomContract->latest)->startOfMonth());
            }else{
                $rentalDate = $this->toDate(Carbon::parse($roomContract->startdate)->addDay($roomContract->latest)->startOfDay());
            }
            $rentalPayment = new RentalPayment();
            $rentalPayment->uid = Carbon::now()->timestamp . RentalPayment::count();
            $params = collect([
                'price' => $roomContract->rental,
                'rentaldate' => $rentalDate,
                'remark' => $request->remark,
                'room_contract_id' => $roomContract->id,
            ]);
            //Convert To Json Object
            $params = json_decode(json_encode($params));
            $rentalPayment = $this->createRentalPayment($params);
    
            if ($this->isEmpty($rentalPayment)) {
                DB::rollBack();
                return $this->errorResponse();
            }
    
            if (!$this->syncWithRentalPayment($roomContract)) {
                DB::rollBack();
                return $this->errorResponse();
            }
    
          
        }else{
            DB::rollBack();
            return $this->errorResponse();

        }



        DB::commit();
        return $this->successResponse('RentalPayment', $rentalPayment, 'create');
    }

    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/rentalPayment/{rentalPaymentid} (PUT)
        error_log($this->controllerName . 'Updating rentalPayment of uid: ' . $uid);
        $rentalPayment = $this->getRentalPayment($uid);
        $this->validate($request, [
            'price' => 'required|numeric',
        ]);
        if ($this->isEmpty($rentalPayment)) {
            DB::rollBack();
            return $this->notFoundResponse('RentalPayment');
        }
        $params = collect([
            'price' => $request->price,
            'payment' => $request->price,
            'paid' => $request->paid,
            'penalty' => $this->toDouble($request->penalty),
            'processing_fees' => $this->toDouble($request->processing_fees),
            'service_fees' => $this->toDouble($request->service_fees),
            'paymentdate' => $request->paymentdate,
            'rentaldate' => $rentalPayment->rentaldate,
            'remark' => $request->remark,
            'sequence' => $rentalPayment->sequence,
            'room_contract_id' => $rentalPayment->roomcontract->id,
            'referenceno' => $request->referenceno,
            'paymentmethod' => $request->paymentmethod,
            // 'receive_from' => $request->receive_from,
            'receive_from' => $rentalPayment->roomcontract->tenant->name,
            'issueby' => $request->user()->id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $rentalPayment = $this->updateRentalPayment($rentalPayment, $params);

        if ($this->isEmpty($rentalPayment)) {
            DB::rollBack();
            return $this->errorResponse();
        }   if ($this->isEmpty($rentalPayment)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $rentalPayment = $this->getRentalPaymentById($rentalPayment->id);
        if ($this->isEmpty($rentalPayment)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        
        DB::commit();
        return $this->successResponse('RentalPayment', $rentalPayment, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/rentalPayment/{rentalPaymentid} (DELETE)
        error_log($this->controllerName . 'Deleting rentalPayment of uid: ' . $uid);
        $rentalPayment = $this->getRentalPayment($uid);
        if ($this->isEmpty($rentalPayment)) {
            DB::rollBack();
            return $this->notFoundResponse('RentalPayment');
        }
        $rentalPayment = $this->deleteRentalPayment($rentalPayment);
        if ($this->isEmpty($rentalPayment)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if (!$this->syncWithRentalPayment($rentalPayment->roomcontract)) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('RentalPayment', $rentalPayment, 'delete');
    }

    public function makePayment(Request $request, $uid)
    {
        DB::beginTransaction();
        $this->validate($request, [
            'penalty' => 'nullable|numeric',
        ]);
        $rentalPayment = $this->getRentalPayment($uid);
        if ($this->isEmpty($rentalPayment)) {
            DB::rollBack();
            return $this->notFoundResponse('RentalPayment');
        }

        $roomContract = $rentalPayment->roomcontract;
        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomContract');
        }
        
        $max = RentalPayment::where('status', true)->max('sequence') + 1;

        $params = collect([
            'price' => $request->price,
            'payment' => $request->price,
            'paid' => true,
            'penalty' => $this->toDouble($request->penalty),
            'processing_fees' => $this->toDouble($request->processing_fees),
            'service_fees' => $this->toDouble($request->service_fees),
            'paymentdate' => $request->paymentdate,
            'rentaldate' => $rentalPayment->rentaldate,
            'remark' => $rentalPayment->remark,
            'sequence' => $max,
            'room_contract_id' => $rentalPayment->roomcontract->id,
            'referenceno' => $request->referenceno,
            'paymentmethod' => $request->paymentmethod,
            // 'receive_from' => $request->receive_from,
            'receive_from' => $rentalPayment->roomcontract->tenant->name,
            'issueby' => $request->user()->id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $rentalPayment = $this->updateRentalPayment($rentalPayment, $params);

        if ($this->isEmpty($rentalPayment)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $rentalPayment = $this->getRentalPaymentById($rentalPayment->id);
        if ($this->isEmpty($rentalPayment)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if($rentalPayment->price < $roomContract->rental){
            $balance = $roomContract->rental - $rentalPayment->price;
            $params = collect([
                'room_contract_id' => $roomContract->id,
                'other_charges' => $balance,
            ]);
            //Convert To Json Object
            $params = json_decode(json_encode($params));
            $payment = $this->createPayment($params);
            $data = $this->getOtherPaymentTitleByName($rentalPayment->rentaldate . ' Rental Balance');
            if (!$this->isEmpty($data)) {
                $data->price = $this->toDouble($balance);
                $payment->otherpayments->push($data);
                $payment->otherpayments()->syncWithoutDetaching([$data->id => ['status' => true, 'price' => $data->price]]);
            }else{
                $params = collect([
                    'name' => $rentalPayment->rentaldate . ' Rental Balance',
                ]);
                //Convert To Json Object
                $params = json_decode(json_encode($params));
                $data = $this->createOtherPaymentTitle($params);
                if (!$this->isEmpty($data)) {
                    $data->price = $this->toDouble($balance);
                    $payment->otherpayments->push($data);
                    $payment->otherpayments()->syncWithoutDetaching([$data->id => ['status' => true, 'price' => $data->price]]);
                }
            }

            $payment = $this->getPaymentById($payment->id);
            $rentalPayment->addOnPayment = $payment;
        }

        
        DB::commit();
        return $this->successResponse('RentalPayment', $rentalPayment, 'update');
    }
}

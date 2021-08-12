<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Payment;
use App\PaymentContract;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

class PaymentController extends Controller
{
    use AllServices;

    private $controllerName = '[PaymentController]';

    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of payments.');
        // api/payment (GET)
        $payments = $this->getPayments($request->user());
        if ($this->isEmpty($payments)) {
            return $this->errorPaginateResponse('Payments');
        } else {
            return $this->successPaginateResponse('Payments', $payments, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered payments.');
        // api/payment/filter (GET)
        $params = collect([
            'tenant_id' => $request->tenant_id,
            'room_id' => $request->room_id,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'penalty' => $request->penalty,
            'paid' => $request->paid,
            'sequence' => $request->sequence,
            'otherPaymentTitle' => $request->otherPaymentTitle,
            'service_ids' => $request->service_ids,
            'paymentmethod' => $request->paymentmethod,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $payments = $this->getPayments($request->user());
        $payments = $this->filterPayments($payments, $params);

        if ($this->isEmpty($payments)) {
            return $this->errorPaginateResponse('Payments');
        } else {
            return $this->successPaginateResponse('Payments', $payments, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function show(Request $request, $uid)
    {
        // api/payment/{paymentid} (GET)
        error_log($this->controllerName . 'Retrieving payment of uid:' . $uid);
        $payment = $this->getPayment($uid);
        if ($this->isEmpty($payment)) {
            $data['data'] = null;
            return $this->notFoundResponse('Payment');
        } else {
            return $this->successResponse('Payment', $payment, 'retrieve');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/payment (POST)
        $this->validate($request, [
            'room_contract_id' => 'required',
            'price' => 'required|numeric',
            'services' => 'array',
            'otherpayments' => 'array',
        ]);
        error_log($this->controllerName . 'Creating payment.');

        $params = collect([
            'room_contract_id' => $request->room_contract_id,
            'price' => $request->price,
            'other_charges' => $request->other_charges,
            'processing_fees' => $request->processing_fees,
            'remark' => $request->remark,
            'paymentdate' => $request->paymentdate,
            'referenceno' => $request->referenceno,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $payment = $this->createPayment($params);
        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        //For front end view
        $payment->services = collect();

        if($request->services){
            $services = collect(json_decode(json_encode($request->services)));
            foreach ($services as $service) {
                $service = $this->getService($service);
                if (!$this->isEmpty($service)) {
                    $payment->services->push($service);
                    $payment->services()->syncWithoutDetaching([$service->id => ['status' => true, 'price' => $this->toDouble($service->price)]]);
                }
            }
        }
        
        $payment->otherpayments = collect();
        if($request->otherpayments){
            $otherpayments = collect(json_decode(json_encode($request->otherpayments)));
            foreach ($otherpayments as $otherpayment) {
                $data = $this->getOtherPaymentTitleByName($otherpayment->name);
                if (!$this->isEmpty($data)) {
                    $data->price = $this->toDouble($otherpayment->price);
                    $payment->otherpayments->push($data);
                    $payment->otherpayments()->syncWithoutDetaching([$data->id => ['status' => true, 'price' => $data->price]]);
                }else{
                    $params = collect([
                        'name' => $otherpayment->name,
                    ]);
                    //Convert To Json Object
                    $params = json_decode(json_encode($params));
                    $data = $this->createOtherPaymentTitle($params);
                    if (!$this->isEmpty($data)) {
                        $data->price = $this->toDouble($otherpayment->price);
                        $payment->otherpayments->push($data);
                        $payment->otherpayments()->syncWithoutDetaching([$data->id => ['status' => true, 'price' => $data->price]]);
                    }
                }
            }
        }
        

        DB::commit();
        return $this->successResponse('Payment', $payment, 'create');
    }

    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/payment/{paymentid} (PUT)
        error_log($this->controllerName . 'Updating payment of uid: ' . $uid);
        $payment = $this->getPayment($uid);
        $this->validate($request, [
            'room_contract_id' => 'required',
            'price' => 'required|numeric',
            'services' => 'array',
            'otherpayments' => 'array',
        ]);
        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->notFoundResponse('Payment');
        }
        $params = collect([
            'price' => $request->price,
            'paid' => $request->paid,
            'other_charges' => $request->other_charges,
            'paymentdate' => $request->paymentdate,
            'remark' => $request->remark,
            'room_contract_id' => $payment->roomcontract->id,
            'processing_fees' => $request->processing_fees,
            'referenceno' => $request->referenceno,
            'paymentmethod' => $request->paymentmethod,
            // 'receive_from' => $request->receive_from,
            'receive_from' => $request->paid ? $payment->roomcontract->tenant->name : '',
            'issueby' => $request->paid ? $request->user()->id : '',
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $payment = $this->updatePayment($payment, $params);
        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        //For front end view

        if($request->services){
            $services = collect(json_decode(json_encode($request->services)));
            $finalservices = collect();
            foreach ($services as $service) {
                $service = $this->getService($service);
                if (!$this->isEmpty($service)) {
                    $finalservices[$service->id] = ['status' => true, 'price' => $this->toDouble($service->price)];
                }
            }
            $payment->services()->sync($finalservices);
        }
        
        if($request->otherpayments){
            $otherpayments = collect(json_decode(json_encode($request->otherpayments)));
            $finalotherpayments = collect();
            foreach ($otherpayments as $otherpayment) {
                $data = $this->getOtherPaymentTitleByName($otherpayment->name);
                if (!$this->isEmpty($data)) {
                    $price = $this->toDouble($otherpayment->price);
                    $finalotherpayments[$data->id] = ['status' => true, 'price' => $price];
                }else{
                    $params = collect([
                        'name' => $otherpayment->name,
                    ]);
                    //Convert To Json Object
                    $params = json_decode(json_encode($params));
                    $data = $this->createOtherPaymentTitle($params);
                    if (!$this->isEmpty($data)) {
                        $price = $this->toDouble($otherpayment->price);
                        $finalotherpayments[$data->id] = ['status' => true, 'price' => $price];
                    }
                }
            }
            $payment->otherpayments()->sync($finalotherpayments);
        }
        
        $payment = $this->getPaymentById($payment->id);
        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        DB::commit();
        return $this->successResponse('Payment', $payment, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/payment/{paymentid} (DELETE)
        error_log($this->controllerName . 'Deleting payment of uid: ' . $uid);
        $payment = $this->getPayment($uid);
        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->notFoundResponse('Payment');
        }

        $payment = $this->deletePayment($payment);

        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('Payment', $payment, 'delete');
    }

    public function makePayment(Request $request, $uid)
    {
        DB::beginTransaction();
        $this->validate($request, [
            'penalty' => 'nullable|numeric',
        ]);
        $payment = $this->getPayment($uid);
        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->notFoundResponse('Payment');
        }

        $max = Payment::where('status', true)->max('sequence') + 1;
        $params = collect([
            'price' => $request->price,
            'totalpayment' => $request->price,
            'paid' => true,
            'penalty' => $this->toDouble($request->penalty),
            'processing_fees' => $this->toDouble($request->processing_fees),
            'other_charges' => $request->other_charges,
            'paymentdate' => Carbon::now()->format('Y-m-d'),
            'remark' => $payment->remark,
            'sequence' => $max,
            'room_contract_id' => $payment->roomcontract->id,
            'referenceno' => $request->referenceno,
            'paymentmethod' => $request->paymentmethod,
            // 'receive_from' => $request->receive_from,
            'receive_from' => $payment->roomcontract->tenant->name,
            'issueby' => $request->user()->id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $payment = $this->updatePayment($payment, $params);

        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        
        DB::commit();
        $payment = $this->getPaymentById($payment->id);
        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        return $this->successResponse('Payment', $payment, 'update');
    }


    public function payDeposit(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/payment (POST)
        $this->validate($request, [
            'room_contract_id' => 'required',
            'price' => 'required|numeric',
            'other_charges' => 'nullable|numeric',
        ]);
        error_log($this->controllerName . 'Creating payment.');

        $max = Payment::where('status', true)->max('sequence') + 1;
        $params = collect([
            'room_contract_id' => $request->room_contract_id,
            'price' => $request->price,
            'other_charges' => $request->other_charges,
            'remark' => $request->remark,
            'sequence' => $max,
            'paymentdate' => $request->paymentdate,
            'referenceno' => $request->referenceno,
            'paymentmethod' => $request->paymentmethod,
            // 'receive_from' => $request->receive_from,
            'receive_from' => $rentalPayment->roomcontract->tenant->name,
            'issueby' => $request->user()->id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $payment = $this->createPayment($params);
        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        
        $data = $this->getOtherPaymentTitleByName('Deposit');
        if (!$this->isEmpty($data)) {
            $data->price = $this->toDouble($request->price);
            $payment->otherpayments->push($data);
            $payment->otherpayments()->syncWithoutDetaching([$data->id => ['status' => true, 'price' => $data->price]]);
        }else{
            $params = collect([
                'name' => 'Deposit',
            ]);
            //Convert To Json Object
            $params = json_decode(json_encode($params));
            $data = $this->createOtherPaymentTitle($params);
            if (!$this->isEmpty($data)) {
                $data->price = $this->toDouble($request->price);
                $payment->otherpayments->push($data);
                $payment->otherpayments()->syncWithoutDetaching([$data->id => ['status' => true, 'price' => $data->price]]);
            }
        }
        
        $roomcontract = $this->getRoomContractById($request->room_contract_id);
        if ($this->isEmpty($roomcontract)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $roomcontract = $this->payRoomContractDeposit($roomcontract, $request->price);

        if ($this->isEmpty($roomcontract)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('Payment', $payment, 'create');
    }

}

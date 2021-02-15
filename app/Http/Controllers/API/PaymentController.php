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
            'service_ids' => $request->service_ids,
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
            'properties' => 'array',
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
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $payment = $this->createPayment($params);
        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->errorResponse();
        }
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
            'name' => 'required|string|max:300',
            'address' => 'nullable|string|max:300',
            'postcode' => 'nullable|string|max:300',
            'state' => 'nullable|string|max:300',
            'city' => 'nullable|string|max:300',
            'country' => 'nullable|string|max:300',
            'price' => 'required|numeric',
            'paymentTypes' => 'required',
        ]);
        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->notFoundResponse('Payment');
        }
        $params = collect([
            'name' => $request->name,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'city' => $request->city,
            'country' => $request->country,
            'price' => $request->price,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $payment = $this->updatePayment($payment, $params);

        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $paymentTypes = PaymentType::find($request->paymentTypes);
        if ($this->isEmpty($paymentTypes)) {
            DB::rollBack();
            return $this->notFoundResponse('PaymentType');
        }

        try {
            $payment->paymentTypes()->sync($paymentTypes->pluck('id'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
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

        $params = collect([
            'price' => $request->price,
            'payment' => $request->price,
            'paid' => true,
            'penalty' => $this->toDouble($request->penalty),
            'processing_fees' => $this->toDouble($request->processing_fees),
            'service_fees' => $this->toDouble($request->service_fees),
            'paymentdate' => Carbon::now()->format('Y-m-d'),
            'rentaldate' => $payment->rentaldate,
            'remark' => $payment->remark,
            'payment_contract_id' => $payment->paymentcontract->id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $payment = $this->updatePayment($payment, $params);

        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $payment = $this->getPaymentById($payment->id);
        if ($this->isEmpty($payment)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        
        DB::commit();
        return $this->successResponse('Payment', $payment, 'update');
    }
}

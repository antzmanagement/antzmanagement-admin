<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\RoomContract;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

class RoomContractController extends Controller
{
    use AllServices;

    private $controllerName = '[RoomContractController]';

    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of roomContracts.');
        // api/roomContract (GET)
        $roomContracts = $this->getRoomContracts($request->user());
        if ($this->isEmpty($roomContracts)) {
            return $this->errorPaginateResponse('RoomContracts');
        } else {
            error_log($roomContracts);
            return $this->successPaginateResponse('RoomContracts', $roomContracts, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered roomContracts.');
        // api/property/filter (GET)
        $params = collect([
            'tenant_id' => $request->tenant_id,
            'owner_id' => $request->owner_id,
            'service_ids' => $request->service_ids,
            'room_id' => $request->room_id,
            'checkedout' => $request->checkedout,
            'outstanding_deposit' => $request->outstanding_deposit,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'sequence' => $request->sequence,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $roomContracts = $this->getRoomContracts($request->user());
        $roomContracts = $this->filterRoomContracts( $roomContracts , $params);

        if ($this->isEmpty($roomContracts)) {
            return $this->errorPaginateResponse('RoomContracts');
        } else {
            return $this->successPaginateResponse('RoomContracts', $roomContracts, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function show(Request $request, $uid)
    {
        // api/roomContract/{roomContractid} (GET)
        error_log($this->controllerName . 'Retrieving roomContract of uid:' . $uid);
        $roomContract = $this->getRoomContract($uid);
        if ($this->isEmpty($roomContract)) {
            $data['data'] = null;
            return $this->notFoundResponse('RoomContract');
        } else {
            return $this->successResponse('RoomContract', $roomContract, 'retrieve');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/roomContract (POST)
        $this->validate($request, [
            'tenant' => 'required',
            'room' => 'required',
        ]);
        error_log($this->controllerName . 'Creating roomContract.');
        $tenant = $this->getTenantById($request->tenant);
        if ($this->isEmpty($tenant)) {
            DB::rollBack();
            return $this->notFoundResponse('Tenant');
        }
        $request->room = json_decode(json_encode($request->room));
        $room = $this->getRoomById($request->room->id);
        if ($this->isEmpty($room)) {
            DB::rollBack();
            return $this->notFoundResponse('Room');
        }

        $contract = $this->getContractById($request->room->contract_id);
        if ($this->isEmpty($contract)) {
            DB::rollBack();
            return $this->notFoundResponse('Contract');
        }

        $startdate = Carbon::parse($request->room->startdate)->format('Y-m-d');
        $enddate = Carbon::parse($request->room->enddate)->format('Y-m-d');
        if(isset($request->room->rental_payment_start_date)){
            $rental_payment_start_date = Carbon::parse($request->room->rental_payment_start_date)->format('Y-m-d');
        }else{
            $rental_payment_start_date = $startdate;
        }

        $duration = $contract->duration;
        if($contract->rental_type == 'day'){
            $duration = Carbon::parse($startdate)->diffInDays(Carbon::parse($enddate)) + 1;
        }else{
            $duration = Carbon::parse($startdate)->diffInMonths(Carbon::parse($enddate)) + 1;
        }

        $latest = 0;
        if($contract->rental_type == 'day'){
            if(isset($request->room->rental_payment_start_date)){
                $latest = Carbon::parse($startdate)->diffInDays(Carbon::parse($rental_payment_start_date));
            }
        }else{
            if(isset($request->room->rental_payment_start_date)){
                $latest = Carbon::parse($startdate)->diffInMonths(Carbon::parse($rental_payment_start_date));
            }
        }


        $servicesIds = collect($request->room->services)->pluck('id');
        $origServiceIds = collect($request->room->origServices)->pluck('id');
        $addOnServicesIds = $servicesIds->diff($origServiceIds);

        $max = RoomContract::where('status', true)->max('sequence') + 1;
        $params = collect([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'contract_id' => $contract->id,
            'orig_service_ids' => $origServiceIds,
            'add_on_service_ids' => $addOnServicesIds,
            'name' => $startdate. ' - ' . $enddate,
            'duration' => $duration,
            'latest' => $latest,
            'penalty' => $contract->penalty,
            'penalty_day' => $contract->penalty_day,
            'rental_type' => $contract->rental_type,
            'terms' => $contract->terms,
            'autorenew' => $request->room->autorenew,
            'startdate' => $startdate,
            'enddate' => $enddate,
            'rental_payment_start_date' => $rental_payment_start_date,
            'rental' => $request->room->price,
            'deposit' => $request->room->deposit,
            'agreement_fees' => $request->room->agreement_fees,
            'booking_fees' => $request->room->booking_fees,
            'sequence' => $max,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $roomContract = $this->createRoomContract($params);
        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('RoomContract', $roomContract, 'create');
    }

    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/roomContract/{roomContractid} (PUT)
        error_log($this->controllerName . 'Updating roomContract of uid: ' . $uid);
        $roomContract = $this->getRoomContract($uid);
        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomContract');
        }
        $this->validate($request, [
            'tenant' => 'required',
            'room' => 'required',
        ]);
        $tenant = $this->getTenantById($request->tenant);
        if ($this->isEmpty($tenant)) {
            DB::rollBack();
            return $this->notFoundResponse('Tenant');
        }
        $request->room = json_decode(json_encode($request->room));
        $room = $this->getRoomById($request->room->id);
        if ($this->isEmpty($room)) {
            DB::rollBack();
            return $this->notFoundResponse('Room');
        }

        $contract = $this->getContractById($request->room->contract_id);
        if ($this->isEmpty($contract)) {
            DB::rollBack();
            return $this->notFoundResponse('Contract');
        }

        $servicesIds = collect($request->room->services)->pluck('id');
        $origServiceIds = collect($request->room->origServices)->pluck('id');
        $addOnServicesIds = $servicesIds->diff($origServiceIds);
        $params = collect([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'orig_service_ids' => $origServiceIds,
            'add_on_service_ids' => $addOnServicesIds,
            'autorenew' => $request->room->autorenew,
            'rental' => $request->room->price,
            'deposit' => $request->room->deposit,
            'outstanding_deposit' => $request->room->outstanding_deposit,
            'agreement_fees' => $request->room->agreement_fees,
            'booking_fees' => $request->room->booking_fees,

        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $roomContract = $this->updateRoomContract($roomContract , $params);
        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('RoomContract', $roomContract, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/roomContract/{roomContractid} (DELETE)
        error_log($this->controllerName . 'Deleting roomContract of uid: ' . $uid);
        $roomContract = $this->getRoomContract($uid);
        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomContract');
        }
        $roomContract = $this->deleteRoomContract($roomContract);
        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('RoomContract', $roomContract, 'delete');
    }


    public function transfer(Request $request)
    {
        DB::beginTransaction();
        // api/roomContract (GET)
        $this->validate($request, [
            'room_contract_id' => 'required',
            'tenant_id' => 'required',
        ]);

        $roomContract = $this->transferRoomContract($request->room_contract_id, $request->tenant_id);
        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('RoomContract', $roomContract, 'update');
    }

    public function checkout(Request $request)
    {
        DB::beginTransaction();
        // api/roomContract (GET)
        $this->validate($request, [
            'checkout_date' => 'required',
            'return_deposit' => 'required',
            'checkout_charges' => 'required',
            'uid' => 'required',
            'checkout_remark' => 'nullable',
        ]);

        error_log('uid');
        error_log($request->uid);
        $roomContract = $this->getRoomContract($request->uid);
        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomContract');
        }
        $params = collect([
            'checkout_date' => $request->checkout_date,
            'return_deposit' => $request->return_deposit,
            'checkout_charges' => $request->checkout_charges,
            'checkout_remark' => $request->checkout_remark,

        ]);
        $params = json_decode(json_encode($params));
        $roomContract = $this->checkoutRoomContract($roomContract, $params);
        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('RoomContract', $roomContract, 'update');
    }
}

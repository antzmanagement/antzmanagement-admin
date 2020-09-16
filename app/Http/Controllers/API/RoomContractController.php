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
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
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

        $startdate = Carbon::parse($request->room->contractstartdate)->format('Y-m-d');

        $servicesIds = collect($request->room->services)->pluck('id');
        $origServiceIds = collect($request->room->origServices)->pluck('id');
        $addOnServicesIds = $servicesIds->diff($origServiceIds);
        $params = collect([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'contract_id' => $contract->id,
            'orig_service_ids' => $origServiceIds,
            'add_on_service_ids' => $addOnServicesIds,
            'name' => $room->name . $startdate . $contract->name,
            'duration' => $contract->duration,
            'terms' => $contract->terms,
            'autorenew' => $contract->autorenew,
            'startdate' => $startdate,
            'rental' => $request->room->price,
            'deposit' => $request->room->deposit,
            'booking_fees' => $request->room->booking_fees,

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

      
        $startdate = Carbon::parse($request->room->contractstartdate)->format('Y-m-d');

        $servicesIds = collect($request->room->services)->pluck('id');
        $origServiceIds = collect($request->room->origServices)->pluck('id');
        $addOnServicesIds = $servicesIds->diff($origServiceIds);
        $params = collect([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'contract_id' => $contract->id,
            'orig_service_ids' => $origServiceIds,
            'add_on_service_ids' => $addOnServicesIds,
            'name' => $room->name . $startdate . $contract->name,
            'duration' => $contract->duration,
            'terms' => $contract->terms,
            'autorenew' => $contract->autorenew,
            'startdate' => $startdate,
            'rental' => $request->room->price,
            'deposit' => $request->room->deposit,
            'booking_fees' => $request->room->booking_fees,
            'outstanding_deposit' => $request->room->outstanding_deposit,

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
}

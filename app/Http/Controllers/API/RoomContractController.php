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
        // api/roomContract/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'company_id' => $request->company_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $roomContracts = $this->filterRoomContracts($request->roomContract(), $params);

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
            'name' => 'required|string|max:300',
            'price' => 'nullable|numeric',
        ]);
        error_log($this->controllerName . 'Creating roomContract.');
        $params = collect([
            'name' => $request->name,
            'price' => $request->price,
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
        $this->validate($request, [
            'email' => 'required|string|max:191|unique:users,email,' . $roomContract->id,
            'name' => 'required|string|max:191',
        ]);
        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomContract');
        }
        $params = collect([
            'icno' => $request->icno,
            'name' => $request->name,
            'email' => $request->email,
            'tel1' => $request->tel1,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $roomContract = $this->updateRoom($roomContract, $params);
        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('RoomContract', $roomContract, 'update');
        }
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
        $roomContract = $this->deleteRoom($roomContract);
        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $userType = RoomContract::where('name', 'roomContract')->first();
        if ($this->isEmpty($userType)) {
            $data['data'] = null;
            return $this->notFoundResponse('RoomContract');
        }
        try {
            $userType->users()->updateExistingPivot($roomContract->id, ['status' => false]);
        } catch (Exception $e) {
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

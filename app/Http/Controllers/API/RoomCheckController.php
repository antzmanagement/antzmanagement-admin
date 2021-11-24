<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\RoomCheck;
use App\RoomCheckType;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

class RoomCheckController extends Controller
{
    use AllServices;

    private $controllerName = '[RoomCheckController]';

    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of roomChecks.');
        // api/roomCheck (GET)
        $roomChecks = $this->getRoomChecks($request->user());
        if ($this->isEmpty($roomChecks)) {
            return $this->errorPaginateResponse('RoomChecks');
        } else {
            return $this->successPaginateResponse('RoomChecks', $roomChecks, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered roomChecks.');
        // api/roomCheck/filter (GET)
        $params = collect([
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'category' => $request->category,
            'room_id' => $request->room_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $data = $this->filterRoomChecks($params, $this->toInt($request->pageSize), ($this->toInt($request->pageNumber) - 1 ) * $this->toInt($request->pageSize));

        if ($this->isEmpty($data)) {
            return $this->errorPaginateResponse('RoomChecks');
        } else {
            return $this->successPaginateResponse('RoomChecks', $data['data'], $this->toInt($request->pageSize), $this->toInt($request->pageNumber), $data['total']);
        }
    }

    public function show(Request $request, $uid)
    {
        // api/roomCheck/{roomCheckid} (GET)
        error_log($this->controllerName . 'Retrieving roomCheck of uid:' . $uid);
        $roomCheck = $this->getRoomCheck($uid);
        if ($this->isEmpty($roomCheck)) {
            $data['data'] = null;
            return $this->notFoundResponse('RoomCheck');
        } else {
            return $this->successResponse('RoomCheck', $roomCheck, 'retrieve');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/roomCheck (POST)
        $this->validate($request, [
            'room_id' => 'required',
        ]);
        error_log($this->controllerName . 'Creating roomCheck.');
        $roomCheck = $this->createRoomCheck($request);
        if ($this->isEmpty($roomCheck)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        foreach ($request->maintenances as $maintenance) {
            $maintenance = json_decode(json_encode($maintenance));
            $maintenance->room_check_id = $roomCheck->id;
            $maintenance = $this->createMaintenance($maintenance);
            if ($this->isEmpty($maintenance)) {
                DB::rollBack();
                return $this->errorResponse();
            }
        }
        
        foreach ($request->cleanings as $cleaning) {
            $cleaning = json_decode(json_encode($cleaning));
            $cleaning->room_check_id = $roomCheck->id;
            $cleaning = $this->createCleaning($cleaning);
            if ($this->isEmpty($cleaning)) {
                DB::rollBack();
                return $this->errorResponse();
            }
        }

        $this->syncRoomStatus($roomCheck->room);

        DB::commit();
        return $this->successResponse('RoomCheck', $roomCheck, 'create');
    }

    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/roomCheck/{roomCheckid} (PUT)   
        error_log($this->controllerName . 'Updating roomCheck of uid: ' . $uid);
        $roomCheck = $this->getRoomCheck($uid);
        $this->validate($request, [
            'room_id' => 'required',
        ]);
        if ($this->isEmpty($roomCheck)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomCheck');
        }
        $roomCheck = $this->updateRoomCheck($roomCheck, $request);

        if ($this->isEmpty($roomCheck)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $origMaintenances = $roomCheck->maintenances;
        foreach($origMaintenances as $origMaintenance){

            if(collect($request->maintenances)->where('uid', $origMaintenance->uid)->count() <= 0){
                $this->deleteMaintenance($origMaintenance);
            }
        }

        foreach ($request->maintenances as $maintenance) {
            $maintenance = json_decode(json_encode($maintenance));
            $maintenance->room_check_id = $roomCheck->id;

            if($origMaintenances->where('uid', $maintenance->uid)->count() > 0){
                $origMaintenance = $this->getMaintenance($maintenance->uid);
                if ($this->isEmpty($origMaintenance)) {
                    DB::rollBack();
                    return $this->errorResponse();
                }
                $maintenance = $this->updateMaintenance($origMaintenance, $maintenance);
            }else if($origMaintenances->where('uid', $maintenance->uid)->count() <= 0){
                $maintenance = $this->createMaintenance($maintenance);
            }

            if ($this->isEmpty($maintenance)) {
                DB::rollBack();
                return $this->errorResponse();
            }
        }

        $origCleanings = $roomCheck->cleanings;
        foreach($origCleanings as $origCleaning){

            if(collect($request->cleanings)->where('uid', $origCleaning->uid)->count() <= 0){
                $this->deleteCleaning($origCleaning);
            }
        }

        foreach ($request->cleanings as $cleaning) {
            $cleaning = json_decode(json_encode($cleaning));
            $cleaning->room_check_id = $roomCheck->id;

            if($origCleanings->where('uid', $cleaning->uid)->count() > 0){
                $origCleaning = $this->getCleaning($cleaning->uid);
                if ($this->isEmpty($origCleaning)) {
                    DB::rollBack();
                    return $this->errorResponse();
                }
                $cleaning = $this->updateCleaning($origCleaning, $cleaning);
            }else if($origCleanings->where('uid', $cleaning->uid)->count() <= 0){
                $cleaning = $this->createCleaning($cleaning);
            }

            if ($this->isEmpty($cleaning)) {
                DB::rollBack();
                return $this->errorResponse();
            }
        }

        $this->syncRoomStatus($roomCheck->room);


        DB::commit();
        return $this->successResponse('RoomCheck', $roomCheck, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/roomCheck/{roomCheckid} (DELETE)
        error_log($this->controllerName . 'Deleting roomCheck of uid: ' . $uid);
        $roomCheck = $this->getRoomCheck($uid);
        if ($this->isEmpty($roomCheck)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomCheck');
        }
        $roomCheck = $this->deleteRoomCheck($roomCheck);
        if ($this->isEmpty($roomCheck)) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('RoomCheck', $roomCheck, 'delete');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\RoomType;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

class RoomTypeController extends Controller
{
    use AllServices;

    private $controllerName = '[RoomTypeController]';
    
    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of roomTypes.');
        // api/roomType (GET)
        $roomTypes = $this->getRoomTypes($request->user());
        if ($this->isEmpty($roomTypes)) {
            return $this->errorPaginateResponse('RoomTypes');
        } else {
            return $this->successPaginateResponse('RoomTypes', $roomTypes, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered roomTypes.');
        // api/roomType/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'company_id' => $request->company_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $roomTypes = $this->getRoomTypes($request->user());
        $roomTypes = $this->filterRoomTypes($roomTypes, $params);

        if ($this->isEmpty($roomTypes)) {
            return $this->errorPaginateResponse('RoomTypes');
        } else {
            return $this->successPaginateResponse('RoomTypes', $roomTypes, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
 
    public function show(Request $request, $uid)
    {
        // api/roomType/{roomTypeid} (GET)
        error_log($this->controllerName . 'Retrieving roomType of uid:' . $uid);
        $roomType = $this->getRoomType($uid);
        if ($this->isEmpty($roomType)) {
            $data['data'] = null;
            return $this->notFoundResponse('RoomType');
        } else {
            return $this->successResponse('RoomType', $roomType, 'retrieve');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/roomType (POST)
        $this->validate($request, [
            'name' => 'required|string|max:300',
            'price' => 'nullable|numeric',
            'services' => 'array',
        ]);
        error_log($this->controllerName . 'Creating roomType.');
        $params = collect([
            'name' => $request->name,
            'price' => $request->price,
            'area' => $request->area,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $roomType = $this->createRoomType($params);
        if ($this->isEmpty($roomType)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $services = collect(json_decode(json_encode($request->services)));
        foreach ($services as $service) {
            $service = $this->getService($service->uid);
            if (!$this->isEmpty($service)) {
                $roomType->services()->syncWithoutDetaching([$service->id => ['status' => true]]);
            }
        }
        

        DB::commit();
        return $this->successResponse('RoomType', $roomType, 'create');
    }

    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/roomType/{roomTypeid} (PUT)
        error_log($this->controllerName . 'Updating roomType of uid: ' . $uid);
        $roomType = $this->getRoomType($uid);
        $this->validate($request, [
            'name' => 'required|string|max:300',
            'price' => 'nullable|numeric',
            'services' => 'array',
        ]);
        if ($this->isEmpty($roomType)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomType');
        }
        $params = collect([
            'name' => $request->name,
            'price' => $request->price,
            'area' => $request->area,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $roomType = $this->updateRoomType($roomType, $params);
        if ($this->isEmpty($roomType)) {
            DB::rollBack();
            return $this->errorResponse();
        } 
        
        $services = collect(json_decode(json_encode($request->services)));
        error_log($services);
        foreach ($services as $service) {
            $service = $this->getService($service->uid);
            $service_id_array[$service->id] = ['status' => true];  
        }
        if (!$this->isEmpty($services)) {
            $roomType->services()->sync($service_id_array);
        }else{
            $roomType->services()->sync([]);
        }
        DB::commit();
        return $this->successResponse('RoomType', $roomType, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/roomType/{roomTypeid} (DELETE)
        error_log($this->controllerName . 'Deleting roomType of uid: ' . $uid);
        $roomType = $this->getRoomType($uid);
        if ($this->isEmpty($roomType)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomType');
        }
        $roomType = $this->deleteRoomType($roomType);
        if ($this->isEmpty($roomType)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('RoomType', $roomType, 'delete');
    }


    public function getPublicRoomTypes(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of roomTypes.');
        // api/roomType (GET)
        $roomTypes = $this->getRoomTypes($request->user());
        if ($this->isEmpty($roomTypes)) {
            return $this->errorPaginateResponse('RoomTypes');
        } else {
            return $this->successPaginateResponse('RoomTypes', $roomTypes, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Room;
use App\RoomType;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

class RoomController extends Controller
{
    use AllServices;

    private $controllerName = '[RoomController]';

    public function index(Request $request)
    {
        return $this->successPaginateResponse('Rooms', collect(), $this->toInt($request->pageSize), $this->toInt($request->pageNumber), 0);
    }

    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered rooms.');
        // api/room/filter (GET)
        $params = collect([
            'id' => $request->id,
            'unit' => $request->unit,
            'floor' => $request->floor,
            'lot' => $request->lot,
            'jalan' => $request->jalan,
            'owner_id' => $request->owner_id,
            'room_type_id' => $request->room_type_id,
            'room_status' => $request->room_status,
            'minPrice' => $request->minPrice,
            'maxPrice' => $request->maxPrice,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $rooms = $this->filterRooms($params, $this->toInt($request->pageSize), ($this->toInt($request->pageNumber) - 1 ) * $this->toInt($request->pageSize));
        if ($this->isEmpty($rooms['data'])) {
            error_log('here');
            return $this->errorPaginateResponse('Rooms');
        } else {
            return $this->successPaginateResponse('Rooms', $rooms['data'], $this->toInt($request->pageSize), $this->toInt($request->pageNumber), $rooms['total']);
        }
    }

    public function show(Request $request, $uid)
    {
        // api/room/{roomid} (GET)
        error_log($this->controllerName . 'Retrieving room of uid:' . $uid);
        $room = $this->getRoom($uid);
        if ($this->isEmpty($room)) {
            $data['data'] = null;
            return $this->notFoundResponse('Room');
        } else {
            return $this->successResponse('Room', $room, 'retrieve');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/room (POST)
        $this->validate($request, [
            'unit' => 'required|string|max:300',
            'address' => 'nullable|string|max:300',
            'postcode' => 'nullable|string|max:300',
            'state' => 'nullable|string|max:300',
            'city' => 'nullable|string|max:300',
            'country' => 'nullable|string|max:300',
            'price' => 'required|numeric',
            'roomType' => 'required',
            'properties' => 'array',
            'sublet' => 'required|boolean',
            'room_status' => 'required|string',
        ]);
        error_log($request->unit);
        error_log($this->controllerName . 'Creating room.');
        $params = collect([
            'name' => $request->unit,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'city' => $request->city,
            'country' => $request->country,
            'price' => $request->price,
            'jalan' =>  $request->jalan,
            'block' =>  $request->block,
            'floor' =>  $request->floor,
            'unit' => $request->unit,
            'size' => $request->size,
            'remark' => $request->remark,
            'sublet' => $request->sublet,
            'sublet_claim' => $request->sublet_claim,
            'owner_claim' => $request->owner_claim,
            'roomType' => $request->roomType,
            'owner' => $request->owner,
            'room_status' => $request->room_status,
            'lot' =>  $request->lot,
            'tnb_account_no' => $request->tnb_account_no,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $room = $this->createRoom($params);
        if ($this->isEmpty($room)) {
            DB::rollBack();
            return $this->errorResponse();
        }

      
        $roomType = RoomType::find($request->roomType);
        if ($this->isEmpty($roomType)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomType');
        }
        try {
            $room->roomTypes()->sync($roomType->id);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if($request->owner){
            $owner = User::find($request->owner);
            if ($this->isEmpty($owner)) {
                DB::rollBack();
                return $this->notFoundResponse('Owner');
            }
            try {
                $room->owners()->syncWithoutDetaching([$owner->id  => ['status' => true]]);
            } catch (Exception $e) {
                DB::rollBack();
                return $this->errorResponse();
            }
    
        }

        if($request->properties){
            $finalproperties = collect();
            foreach ($request->properties as $property) {
                $property = json_decode(json_encode($property));
                $origProperty = $this->getPropertyById($property->id);
                if ($this->isEmpty($origProperty)) {
                    DB::rollBack();
                    return $this->errorResponse();
                }
    
                $finalproperties[$property->id] = ['status' => true, 'qty' => $property->qty, 'remark' => $property->remark];
            }
            error_log($finalproperties);
            $room->properties()->sync($finalproperties);
        }
        

        DB::commit();
        return $this->successResponse('Room', $room, 'create');
    }

    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/room/{roomid} (PUT)
        error_log($this->controllerName . 'Updating room of uid: ' . $uid);
        $room = $this->getRoom($uid);
        $this->validate($request, [
            'unit' => 'required|string|max:300',
            'address' => 'nullable|string|max:300',
            'postcode' => 'nullable|string|max:300',
            'state' => 'nullable|string|max:300',
            'city' => 'nullable|string|max:300',
            'country' => 'nullable|string|max:300',
            'price' => 'required|numeric',
            'roomType' => 'required',
            'properties' => 'array',
            'sublet' => 'required|boolean',
            'room_status' => 'required|string',
        ]);
        if ($this->isEmpty($room)) {
            DB::rollBack();
            return $this->notFoundResponse('Room');
        }
        $params = collect([
            'name' => $request->unit,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'city' => $request->city,
            'country' => $request->country,
            'price' => $request->price,
            'jalan' =>  $request->jalan,
            'block' =>  $request->block,
            'floor' =>  $request->floor,
            'unit' => $request->unit,
            'size' => $request->size,
            'remark' => $request->remark,
            'sublet' => $request->sublet,
            'sublet_claim' => $request->sublet_claim,
            'owner_claim' => $request->owner_claim,
            'roomType' => $request->roomType,
            'owner' => $request->owner,
            'room_status' => $request->room_status,
            'lot' =>  $request->lot,
            'tnb_account_no' => $request->tnb_account_no,
            'properties' => $request->properties,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $room = $this->updateRoom($room, $params);

        if ($this->isEmpty($room)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $roomType = RoomType::find($request->roomType);
        if ($this->isEmpty($roomType)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomType');
        }
        try {
            $room->roomTypes()->sync([$roomType->id  => ['status' => true]]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if($request->owner){
            $owner = User::find($request->owner);
            if ($this->isEmpty($owner)) {
                DB::rollBack();
                return $this->notFoundResponse('Owner');
            }
            try {
                $room->owners()->sync([$owner->id  => ['status' => true]]);
            } catch (Exception $e) {
                DB::rollBack();
                return $this->errorResponse();
            }
        }else{
            $room->owners()->sync([]);
        }

        if($request->properties){
            $finalproperties = collect();
            foreach ($request->properties as $property) {
                $property = json_decode(json_encode($property));
                $origProperty = $this->getPropertyById($property->id);
                if ($this->isEmpty($origProperty)) {
                    DB::rollBack();
                    return $this->errorResponse();
                }
                $finalproperties[$property->id] = ['status' => true, 'qty' => $property->qty, 'remark' => $property->remark];
            }
            error_log($finalproperties);
            $room->properties()->sync($finalproperties);
        }
        


        DB::commit();
        return $this->successResponse('Room', $room, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/room/{roomid} (DELETE)
        error_log($this->controllerName . 'Deleting room of uid: ' . $uid);
        $room = $this->getRoom($uid);
        if ($this->isEmpty($room)) {
            DB::rollBack();
            return $this->notFoundResponse('Room');
        }
        $room = $this->deleteRoom($room);
        if ($this->isEmpty($room)) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('Room', $room, 'delete');
    }
}

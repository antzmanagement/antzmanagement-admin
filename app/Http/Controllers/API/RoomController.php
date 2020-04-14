<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Room;
use App\RoomType;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

class RoomController extends Controller
{
    use AllServices;

    private $controllerName = '[RoomController]';

    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of rooms.');
        // api/room (GET)
        $rooms = $this->getRooms($request->user());
        if ($this->isEmpty($rooms)) {
            return $this->errorPaginateResponse('Rooms');
        } else {
            return $this->successPaginateResponse('Rooms', $rooms, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered rooms.');
        // api/room/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'company_id' => $request->company_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $rooms = $this->filterRooms($request->room(), $params);

        if ($this->isEmpty($rooms)) {
            return $this->errorPaginateResponse('Rooms');
        } else {
            return $this->successPaginateResponse('Rooms', $rooms, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
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
            'name' => 'required|string|max:300',
            'address' => 'nullable|string|max:300',
            'postcode' => 'nullable|string|max:300',
            'state' => 'nullable|string|max:300',
            'city' => 'nullable|string|max:300',
            'country' => 'nullable|string|max:300',
            'price' => 'required|numeric',
            'roomTypes' => 'required',
        ]);
        error_log($this->controllerName . 'Creating room.');
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
        $room = $this->createRoom($params);
        if ($this->isEmpty($room)) {
            DB::rollBack();
            return $this->errorResponse();
        }

      
        $roomTypes = RoomType::find($request->roomTypes);
        if ($this->isEmpty($roomTypes)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomType');
        }

        try {
            $room->roomTypes()->sync($roomTypes->pluck('id'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
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
            'name' => 'required|string|max:300',
            'address' => 'nullable|string|max:300',
            'postcode' => 'nullable|string|max:300',
            'state' => 'nullable|string|max:300',
            'city' => 'nullable|string|max:300',
            'country' => 'nullable|string|max:300',
            'price' => 'required|numeric',
            'roomTypes' => 'required',
        ]);
        if ($this->isEmpty($room)) {
            DB::rollBack();
            return $this->notFoundResponse('Room');
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
        $room = $this->updateRoom($room, $params);

        if ($this->isEmpty($room)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $roomTypes = RoomType::find($request->roomTypes);
        if ($this->isEmpty($roomTypes)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomType');
        }

        try {
            $room->roomTypes()->sync($roomTypes->pluck('id'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
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

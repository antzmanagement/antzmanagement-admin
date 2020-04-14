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
            error_log($roomTypes);
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
        $roomTypes = $this->filterRoomTypes($request->roomType(), $params);

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
        ]);
        error_log($this->controllerName . 'Creating roomType.');
        $params = collect([
            'name' => $request->name,
            'price' => $request->price,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $roomType = $this->createRoomType($params);
        if ($this->isEmpty($roomType)) {
            DB::rollBack();
            return $this->errorResponse();
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
            'email' => 'required|string|max:191|unique:users,email,' . $roomType->id,
            'name' => 'required|string|max:191',
        ]);
        if ($this->isEmpty($roomType)) {
            DB::rollBack();
            return $this->notFoundResponse('RoomType');
        }
        $params = collect([
            'icno' => $request->icno,
            'name' => $request->name,
            'email' => $request->email,
            'tel1' => $request->tel1,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $roomType = $this->updateRoom($roomType, $params);
        if ($this->isEmpty($roomType)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('RoomType', $roomType, 'update');
        }
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
        $roomType = $this->deleteRoom($roomType);
        if ($this->isEmpty($roomType)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $userType = RoomType::where('name', 'roomType')->first();
        if ($this->isEmpty($userType)) {
            $data['data'] = null;
            return $this->notFoundResponse('RoomType');
        }
        try {
            $userType->users()->updateExistingPivot($roomType->id, ['status' => false]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('RoomType', $roomType, 'delete');
    }


    public function authentication(Request $request)
    {
        // TODO Authenticate currently logged in roomType
        error_log($this->controllerName . 'Authenticating roomType.');
        return response()->json($request->roomType(), 200);
    }

    public function register(Request $request)
    {
        // TODO Registers roomTypes without needing authorization
        error_log($this->controllerName . 'Registering roomType.');
        // api/register (POST)
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:roomTypes',
            'password' => 'required|string|min:6|confirmed',
        ]);
        DB::beginTransaction();
        $roomType = new RoomType();
        $roomType->uid = Carbon::now()->timestamp . RoomType::count();
        $roomType->name = $request->name;
        $roomType->email = $request->email;
        $roomType->password = Hash::make($request->password);
        $roomType->status = true;
        try {
            DB::commit();
            $roomType->save();
            $data['status'] = 'success';
            $data['msg'] = $this->getCreatedSuccessMsg('RoomType Account');
            $data['data'] = $roomType;
            $data['code'] = 200;
            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }
    }


    public function roomTypeRoles(Request $request)
    {
        // TODO Authenticate currently logged in roomType
        $roomType = $this->getRoomTypeById($request->roomType()->id);
        if ($this->isEmpty($roomType)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $roles = $roomType->roles()->wherePivot('status', true)->get();

        return $this->successResponse('Role', $roles, 'retrieve');
    }
}

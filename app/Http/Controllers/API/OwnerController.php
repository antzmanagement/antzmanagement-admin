<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Room;
use App\UserType;
use App\Role;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;
use Illuminate\Validation\Rule;

class OwnerController extends Controller
{
    use AllServices;

    private $controllerName = '[OwnerController]';
    /**
     * @OA\Get(
     *      path="/api/owner",
     *      operationId="getOwners",
     *      tags={"OwnerControllerService"},
     *      summary="Get list of owners",
     *      description="Returns list of owners",
     *   @OA\Parameter(
     *     name="pageNumber",
     *     in="query",
     *     description="Page number",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="number of pageSize",
     *     @OA\Schema(type="integer")
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved list of owners"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of owners")
     *    )
     */
    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of owners.');
        // api/owner (GET)
        $owners = $this->getOwners($request->user());
        if ($this->isEmpty($owners)) {
            return $this->errorPaginateResponse('Owners');
        } else {
            return $this->successPaginateResponse('Owners', $owners, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }


    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered owners.');
        // api/owner/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'roomTypes' => $request->roomTypes,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $owners = $this->getOwners($request->user());
        $owners = $this->filterOwners($owners, $params);

        if ($this->isEmpty($owners)) {
            return $this->errorPaginateResponse('Owners');
        } else {
            return $this->successPaginateResponse('Owners', $owners, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    /**
     * @OA\Get(
     *   tags={"OwnerControllerService"},
     *   path="/api/owner/{uid}",
     *   summary="Retrieves owner by Uid.",
     *     operationId="getOwnerByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Owner_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Owner has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the owner."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/owner/{ownerid} (GET)
        error_log($this->controllerName . 'Retrieving owner of uid:' . $uid);
        $owner = $this->getOwner($uid);
        if ($this->isEmpty($owner)) {
            $data['data'] = null;
            return $this->notFoundResponse('Owner');
        } else {
            return $this->successResponse('Owner', $owner, 'retrieve');
        }
    }

    /**
     * @OA\Post(
     *   tags={"OwnerControllerService"},
     *   path="/api/owner",
     *   summary="Creates a owner.",
     *   operationId="createOwner",
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Ownername",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="icno",
     * in="query",
     * description="IC No",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="tel1",
     * in="query",
     * description="Phone",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="email",
     * in="query",
     * description="Email",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="password",
     * in="query",
     * description="Password",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="password_confirmation",
     * in="query",
     * description="Password Confirmation",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Owner has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the owner."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/owner (POST)
        $this->validate($request, [
            'name' => 'required|string|max:300',
            'icno' => 'required|string|max:14',
            'tel1' => 'nullable|string|max:20',
            'email' =>
            [
                'required',
                'string',
                'email',
                'max:191',
                Rule::unique('users')->where(function ($query) {
                    $query->where('status', true);
                }),
            ],
            // 'password' => 'required|string|min:6|confirmed',
        ]);
        $role = Role::where('name', 'owner')->where('status', true)->first();
        if ($this->isEmpty($role)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        error_log($this->controllerName . 'Creating owner.');
        $params = collect([
            'name' => $request->name,
            'icno' => $request->icno,
            'tel1' => $request->tel1,
            'email' => $request->email,
            'occupation' => $request->occupation,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'city' => $request->city,
            'role_id' => $role->id,
            // 'password' => $request->password,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $owner = $this->createOwner($params);
        if ($this->isEmpty($owner)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $userType = UserType::where('name', 'owner')->first();
        if ($this->isEmpty($userType)) {
            DB::rollBack();
            return $this->notFoundResponse('UserType');
        }

        try {
            $userType->users()->syncWithoutDetaching([$owner->id => ['status' => true]]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if ($request->rooms) {

            $rooms = Room::find($request->rooms);
            if ($this->isEmpty($rooms)) {
                DB::rollBack();
                return $this->notFoundResponse('Rooms');
            }
            try {
                $owner->ownrooms()->sync($rooms->pluck('id'));
            } catch (Exception $e) {
                DB::rollBack();
                return $this->errorResponse();
            }
        }

        DB::commit();
        return $this->successResponse('Owner', $owner, 'create');
    }

    /**
     * @OA\Put(
     *   tags={"OwnerControllerService"},
     *   path="/api/owner/{uid}",
     *   summary="Update owner by Uid.",
     *     operationId="updateOwnerByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Owner_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Ownername.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="email",
     *     in="query",
     *     description="Email.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="tel1",
     *     in="query",
     *     description="Telephone Number #1.",
     *     required=false,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="address1",
     *     in="query",
     *     description="Address #1.",
     *     required=false,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="city",
     *     in="query",
     *     description="City.",
     *     required=false,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="postcode",
     *     in="query",
     *     description="PostCode.",
     *     required=false,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="state",
     *     in="query",
     *     description="State.",
     *     required=false,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="country",
     *     in="query",
     *     description="Country.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="icno",
     *     in="query",
     *     description="IC Number.",
     *     required=false,
     *     @OA\Schema(type="string")
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="Owner has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the owner."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/owner/{ownerid} (PUT)
        error_log($this->controllerName . 'Updating owner of uid: ' . $uid);
        $owner = $this->getOwner($uid);
        $this->validate($request, [

            'email' =>
            [
                'required',
                'string',
                'email',
                'max:191',
                Rule::unique('users')->where(function ($query) use ($owner) {
                    $query->where('status', true);
                    $query->where('id', '!=', $owner->id);
                }),
            ],
            'name' => 'required|string|max:191',
        ]);
        if ($this->isEmpty($owner)) {
            DB::rollBack();
            return $this->notFoundResponse('Owner');
        }
        $role = Role::where('name', 'owner')->where('status', true)->first();
        if ($this->isEmpty($role)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        $params = collect([
            'icno' => $request->icno,
            'name' => $request->name,
            'email' => $request->email,
            'tel1' => $request->tel1,
            'occupation' => $request->occupation,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'city' => $request->city,
            'role_id' => $role->id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $owner = $this->updateOwner($owner, $params);
        if ($this->isEmpty($owner)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if ($request->rooms) {

            $rooms = Room::find($request->rooms);
            if ($this->isEmpty($rooms)) {
                DB::rollBack();
                return $this->notFoundResponse('Rooms');
            }
            $origrooms = $owner->ownrooms()->wherePivot('status', true)->where('rooms.status', true)->get();

            //Add New Room
            foreach ($rooms as $room) {
                if (!$origrooms->contains('id', $room->id)) {

                    try {
                        $owner->ownrooms()->syncWithoutDetaching([$room->id => ['status' => true]]);
                    } catch (Exception $e) {
                        DB::rollBack();
                        return $this->errorResponse();
                    }
                }
            }

            //Delete Room
            foreach ($origrooms as $origroom) {
                if (!$rooms->contains('id', $origroom->id)) {

                    try {
                        $owner->ownrooms()->updateExistingPivot([$origroom->id], ['status' => false]);
                    } catch (Exception $e) {
                        DB::rollBack();
                        return $this->errorResponse();
                    }
                }
            }
        }

        DB::commit();
        return $this->successResponse('Owner', $owner, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/owner/{ownerid} (DELETE)
        error_log($this->controllerName . 'Deleting owner of uid: ' . $uid);
        $owner = $this->getOwner($uid);
        if ($this->isEmpty($owner)) {
            DB::rollBack();
            return $this->notFoundResponse('Owner');
        }
        $owner = $this->deleteOwner($owner);
        if ($this->isEmpty($owner)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('Owner', $owner, 'delete');
    }

    public function getUnclaimRentalPayments(Request $request, $uid)
    {
        // api/owner/{ownerid} (GET)
        error_log($this->controllerName . 'Retrieving owner of uid:' . $uid);
        $owner = $this->getOwner($uid);
        
        if ($this->isEmpty($owner)) {
            $data['data'] = null;
            return $this->notFoundResponse('Owner');
        } 

        $data = $this->getOwnerUnclaimRentalPayments($owner);
        return $this->successResponse('Rental Payments', $data, 'retrieve');
        
    }
    
    public function getUnclaimMaintenances(Request $request, $uid)
    {
        // api/owner/{ownerid} (GET)
        error_log($this->controllerName . 'Retrieving owner of uid:' . $uid);
        $owner = $this->getOwner($uid);
        
        if ($this->isEmpty($owner)) {
            $data['data'] = null;
            return $this->notFoundResponse('Owner');
        } 

        $data = $this->getOwnerUnclaimMaintenances($owner);
        return $this->successResponse('Maintenances', $data, 'retrieve');
    }
}

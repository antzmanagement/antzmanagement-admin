<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Tenant;
use App\Room;
use App\Role;
use App\RoomContract;
use App\UserType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use App\Traits\AllServices;

class TenantController extends Controller
{
    use AllServices;

    private $controllerName = '[TenantController]';
    /**
     * @OA\Get(
     *      path="/api/tenant",
     *      operationId="getTenants",
     *      tags={"TenantControllerService"},
     *      summary="Get list of tenants",
     *      description="Returns list of tenants",
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
     *          description="Successfully retrieved list of tenants"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of tenants")
     *    )
     */
    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of tenants.');
        // api/tenant (GET)
        $tenants = $this->getTenants($request->user());
        if ($this->isEmpty($tenants)) {
            return $this->errorPaginateResponse('Tenants');
        } else {
            return $this->successPaginateResponse('Tenants', $tenants, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }


    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered tenants.');
        // api/tenant/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'gender' => $request->gender,
            'religion' => $request->religion,
            'approach_method' => $request->approach_method,
            'pic' => $request->pic,
            'birthdayfromdate' => $request->birthdayfromdate,
            'birthdaytodate' => $request->birthdaytodate,
            'room_id' => $request->room_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $tenants = $this->getTenants($request->user());
        $tenants = $this->filterTenants($tenants, $params);

        if ($this->isEmpty($tenants)) {
            return $this->errorPaginateResponse('Tenants');
        } else {
            return $this->successPaginateResponse('Tenants', $tenants, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    /**
     * @OA\Get(
     *   tags={"TenantControllerService"},
     *   path="/api/tenant/{uid}",
     *   summary="Retrieves tenant by Uid.",
     *     operationId="getTenantByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Tenant_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Tenant has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the tenant."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/tenant/{tenantid} (GET)
        error_log($this->controllerName . 'Retrieving tenant of uid:' . $uid);
        $tenant = $this->getTenant($uid);
        if ($this->isEmpty($tenant)) {
            return $this->notFoundResponse('Tenant');
        } else {
            return $this->successResponse('Tenant', $tenant, 'retrieve');
        }
    }

    /**
     * @OA\Post(
     *   tags={"TenantControllerService"},
     *   path="/api/tenant",
     *   summary="Creates a tenant.",
     *   operationId="createTenant",
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Tenantname",
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
     *     description="Tenant has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the tenant."
     *   )
     * )
     */
    public function store(Request $request)
    {
        error_log("creating");
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/tenant (POST)
        $this->validate($request, [
            'name' => 'required|string|max:300',
            'icno' => 'required|string|max:14',
            'tel1' => 'nullable|string|max:100',
            'tel2' => 'nullable|string|max:100',
            'tel3' => 'nullable|string|max:100',
            'email' =>
            [
                'nullable',
                'email',
                'max:300',
            ],
        ]);
        error_log($this->controllerName . 'Creating tenant.');
        $role = Role::where('name', 'tenant')->where('status', true)->first();
        if ($this->isEmpty($role)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        $params = collect([
            'name' => $request->name,
            'icno' => $request->icno,
            'tel1' => $request->tel1,
            'tel2' => $request->tel2,
            'tel3' => $request->tel3,
            'email' => $request->email,
            'age' => $request->age,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            'religion' => $request->religion,
            'approach_method' => $request->approach_method,
            'occupation' => $request->occupation,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'city' => $request->city,
            'mother_name' => $request->mother_name,
            'mother_tel' => $request->mother_tel,
            'father_name' => $request->father_name,
            'father_tel' => $request->father_tel,
            'emergency_name' => $request->emergency_name,
            'emergency_contact' => $request->emergency_contact,
            'emergency_relationship' => $request->emergency_relationship,
            'role_id' => $role->id,
            'pic' => $request->pic,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $tenant = $this->createTenant($params);
        if ($this->isEmpty($tenant)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $userType = UserType::where('name', 'tenant')->first();
        if ($this->isEmpty($userType)) {
            DB::rollBack();
            return $this->notFoundResponse('UserType');
        }

        try {
            $userType->users()->syncWithoutDetaching([$tenant->id => ['status' => true]]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if (!$this->isEmpty($request->rooms)) {

            foreach ($request->rooms as $room) {
                $room = json_decode(json_encode($room));
                $contract = $this->getContractById($room->contract_id);
                if ($this->isEmpty($contract)) {
                    DB::rollBack();
                    return $this->notFoundResponse('Contract');
                }
                $startdate = Carbon::parse($room->startdate)->format('Y-m-d');
                $enddate = Carbon::parse($room->enddate)->format('Y-m-d');

                $duration = $contract->duration;
                if($contract->rental_type == 'day'){
                    $duration = Carbon::parse($startdate)->diffInDays(Carbon::parse($enddate)) + 1;
                }else{
                    $duration = Carbon::parse($startdate)->diffInMonths(Carbon::parse($enddate)) + 1;
                }        
                $origRoom = $this->getRoomById($room->id);
                if ($this->isEmpty($origRoom)) {
                    DB::rollBack();
                    return $this->notFoundResponse('Room');
                }

                $servicesIds = collect($room->services)->pluck('id');
                $origServiceIds = collect($room->origServices)->pluck('id');
                $addOnServicesIds = $servicesIds->diff($origServiceIds);        
                $max = RoomContract::where('status', true)->max('sequence') + 1;

                $params = collect([
                    'tenant_id' => $tenant->id,
                    'room_id' => $origRoom->id,
                    'contract_id' => $contract->id,
                    'orig_service_ids' => $origServiceIds,
                    'add_on_service_ids' => $addOnServicesIds,
                    'name' => $room->unit . '_' . $startdate. '_' . $contract->name,
                    'duration' => $duration,
                    'penalty' => $contract->penalty,
                    'penalty_day' => $contract->penalty_day,
                    'rental_type' => $contract->rental_type,
                    'terms' => $contract->terms,
                    'autorenew' => $room->autorenew,
                    'startdate' => $startdate,
                    'enddate' => $enddate,
                    'rental' => $room->price,
                    'deposit' => $room->deposit,
                    'agreement_fees' => $room->agreement_fees,
                    'booking_fees' => $room->booking_fees,
                    'sequence' => $max,
                ]);
                //Convert To Json Object
                $params = json_decode(json_encode($params));
                $roomContract = $this->createRoomContract($params);
                if ($this->isEmpty($roomContract)) {
                    DB::rollBack();
                    return $this->errorResponse();
                }
            }
        }

        DB::commit();
        return $this->successResponse('Tenant', $tenant, 'create');
    }

    /**
     * @OA\Put(
     *   tags={"TenantControllerService"},
     *   path="/api/tenant/{uid}",
     *   summary="Update tenant by Uid.",
     *     operationId="updateTenantByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Tenant_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Tenantname.",
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
     *     description="Tenant has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the tenant."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/tenant/{tenantid} (PUT)
        error_log($this->controllerName . 'Updating tenant of uid: ' . $uid);
        $tenant = $this->getTenant($uid);
        if ($this->isEmpty($tenant)) {
            DB::rollBack();
            return $this->notFoundResponse('Tenant');
        }

        $role = Role::where('name', 'tenant')->where('status', true)->first();
        if ($this->isEmpty($role)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        $this->validate($request, [
            'email' =>
            [
                'nullable',
                'email',
                'max:300',
                // Rule::unique('users')->where(function ($query) use ($tenant) {
                //     $query->where('status', true);
                //     $query->where('id', '!=', $tenant->id);
                // }),
            ],
            'name' => 'required|string|max:191',
        ]);
        $params = collect([
            'name' => $request->name,
            'icno' => $request->icno,
            'tel1' => $request->tel1,
            'tel2' => $request->tel2,
            'tel3' => $request->tel3,
            'email' => $request->email,
            'age' => $request->age,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            'religion' => $request->religion,
            'approach_method' => $request->approach_method,
            'occupation' => $request->occupation,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'city' => $request->city,
            'mother_name' => $request->mother_name,
            'mother_tel' => $request->mother_tel,
            'father_name' => $request->father_name,
            'father_tel' => $request->father_tel,
            'emergency_name' => $request->emergency_name,
            'emergency_contact' => $request->emergency_contact,
            'emergency_relationship' => $request->emergency_relationship,
            'role_id' => $role->id,
            'pic' => $request->pic,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $tenant = $this->updateTenant($tenant, $params);
        if ($this->isEmpty($tenant)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        // if ($request->rooms) {

        //     $rooms = Room::find($request->rooms);
        //     if ($this->isEmpty($rooms)) {
        //         DB::rollBack();
        //         return $this->notFoundResponse('Rooms');
        //     }
        //     $origrooms = $tenant->rentrooms()->wherePivot('status', true)->where('rooms.status', true)->get();

        //     //Add New Room
        //     foreach ($rooms as $room) {
        //         if (!$origrooms->contains('id', $room->id)) {

        //             try {
        //                 $tenant->rentrooms()->syncWithoutDetaching([$room->id => ['status' => true]]);
        //             } catch (Exception $e) {
        //                 DB::rollBack();
        //                 return $this->errorResponse();
        //             }
        //         }
        //     }

        //     //Delete Room
        //     foreach ($origrooms as $origroom) {
        //         if (!$rooms->contains('id', $origroom->id)) {

        //             try {
        //                 $tenant->rentrooms()->updateExistingPivot([$origroom->id], ['status' => false]);
        //             } catch (Exception $e) {
        //                 DB::rollBack();
        //                 return $this->errorResponse();
        //             }
        //         }
        //     }
        // }

        DB::commit();
        return $this->successResponse('Tenant', $tenant, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/tenant/{tenantid} (DELETE)
        error_log($this->controllerName . 'Deleting tenant of uid: ' . $uid);
        $tenant = $this->getTenant($uid);
        if ($this->isEmpty($tenant)) {
            DB::rollBack();
            return $this->notFoundResponse('Tenant');
        }
        $tenant = $this->deleteTenant($tenant);
        if ($this->isEmpty($tenant)) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('Tenant', $tenant, 'delete');
    }
}

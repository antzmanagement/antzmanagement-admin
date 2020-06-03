<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Tenant;
use App\Room;
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
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'roomTypes' => $request->roomTypes,
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
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/tenant (POST)
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
            'password' => 'required|string|min:6|confirmed',
        ]);
        error_log($this->controllerName . 'Creating tenant.');
        $params = collect([
            'name' => $request->name,
            'icno' => $request->icno,
            'tel1' => $request->tel1,
            'email' => $request->email,
            'password' => $request->password,
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
                $startdate = Carbon::parse($room->contractstartdate)->format('Y-m-d');
                $room = $this->getRoomById($room->id);
                if ($this->isEmpty($room)) {
                    DB::rollBack();
                    return $this->notFoundResponse('Room');
                }

                $params = collect([
                    'room_id' => $room->id,
                    'tenant_id' => $tenant->id,
                    'contract_id' => $contract->id,
                    'name' => $contract->name,
                    'duration' => $contract->duration,
                    'terms' => $contract->terms,
                    'autorenew' => $contract->autorenew,
                    'startdate' => $startdate,
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
        $this->validate($request, [
            'email' =>
            [
                'required',
                'string',
                'email',
                'max:191',
                Rule::unique('users')->where(function ($query) use ($tenant) {
                    $query->where('status', true);
                    $query->where('id', '!=', $tenant->id);
                }),
            ],
            'name' => 'required|string|max:191',
        ]);
        if ($this->isEmpty($tenant)) {
            DB::rollBack();
            return $this->notFoundResponse('Tenant');
        }
        $params = collect([
            'icno' => $request->icno,
            'name' => $request->name,
            'email' => $request->email,
            'tel1' => $request->tel1,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $tenant = $this->updateTenant($tenant, $params);
        if ($this->isEmpty($tenant)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if ($request->rooms) {

            $rooms = Room::find($request->rooms);
            if ($this->isEmpty($rooms)) {
                DB::rollBack();
                return $this->notFoundResponse('Rooms');
            }
            $origrooms = $tenant->rentrooms()->wherePivot('status', true)->where('rooms.status', true)->get();

            //Add New Room
            foreach ($rooms as $room) {
                if (!$origrooms->contains('id', $room->id)) {

                    try {
                        $tenant->rentrooms()->syncWithoutDetaching([$room->id => ['status' => true]]);
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
                        $tenant->rentrooms()->updateExistingPivot([$origroom->id], ['status' => false]);
                    } catch (Exception $e) {
                        DB::rollBack();
                        return $this->errorResponse();
                    }
                }
            }
        }

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

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Tenant;
use App\UserType;
use Illuminate\Support\Facades\Hash;
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

    /**
     * @OA\Get(
     *      path="/api/filter/tenant",
     *      operationId="filterTenants",
     *      tags={"TenantControllerService"},
     *      summary="Filter list of tenants",
     *      description="Returns list of filtered tenants",
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
     *   @OA\Parameter(
     *     name="keyword",
     *     in="query",
     *     description="Keyword for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="fromdate",
     *     in="query",
     *     description="From Date for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="todate",
     *     in="query",
     *     description="To string for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="status",
     *     in="query",
     *     description="status for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="company_id",
     *     in="query",
     *     description="Company id for filter",
     *     @OA\Schema(type="string")
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved list of filtered tenants"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of tenants")
     *    )
     */
    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered tenants.');
        // api/tenant/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'company_id' => $request->company_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $tenants = $this->filterTenants($request->tenant(), $params);

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
            $data['data'] = null;
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
            'email' => 'required|string|email|max:191|unique:users',
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
        $tenant = $this->createUser($params);
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
            $userType->users()->syncWithoutDetaching([$tenant->id]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
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
            'email' => 'required|string|max:191|unique:users,email,' . $tenant->id,
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
        $tenant = $this->updateUser($tenant, $params);
        if ($this->isEmpty($tenant)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Tenant', $tenant, 'update');
        }
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
        $tenant = $this->deleteUser($tenant);
        if ($this->isEmpty($tenant)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $userType = UserType::where('name', 'tenant')->first();
        if ($this->isEmpty($userType)) {
            $data['data'] = null;
            return $this->notFoundResponse('UserType');
        }
        try {
            $userType->users()->updateExistingPivot($tenant->id, ['status' => false]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('Tenant', $tenant, 'delete');
    }



    /**
     * @OA\Post(
     *   tags={"TenantControllerService"},
     *   summary="Authenticates current request's tenant.",
     *     operationId="authenticateCurrentRequestsTenant",
     * path="/api/authentication",
     *   @OA\Response(
     *     response=200,
     *     description="Tenant is already authenticated."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Tenant is not authenticated."
     *   )
     * )
     */
    public function authentication(Request $request)
    {
        // TODO Authenticate currently logged in tenant
        error_log($this->controllerName . 'Authenticating tenant.');
        return response()->json($request->tenant(), 200);
    }

    /**
     * @OA\Post(
     *   tags={"TenantControllerService"},
     *   summary="Creates a tenant without needing authorization.",
     *     operationId="createTenantWithoutAuthorization",
     * path="/api/register",
     *   @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Tenantname.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="email",
     *     in="query",
     *     description="Email.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="password",
     *     in="query",
     *     description="Password.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="password_confirmation",
     *     in="query",
     *     description="Confirm Password.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Tenant has been successfully created."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Tenant is not created."
     *   )
     * )
     */
    public function register(Request $request)
    {
        // TODO Registers tenants without needing authorization
        error_log($this->controllerName . 'Registering tenant.');
        // api/register (POST)
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:tenants',
            'password' => 'required|string|min:6|confirmed',
        ]);
        DB::beginTransaction();
        $tenant = new Tenant();
        $tenant->uid = Carbon::now()->timestamp . Tenant::count();
        $tenant->name = $request->name;
        $tenant->email = $request->email;
        $tenant->password = Hash::make($request->password);
        $tenant->status = true;
        try {
            DB::commit();
            $tenant->save();
            $data['status'] = 'success';
            $data['msg'] = $this->getCreatedSuccessMsg('Tenant Account');
            $data['data'] = $tenant;
            $data['code'] = 200;
            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }
    }


    /**
     * @OA\Get(
     *   tags={"TenantControllerService"},
     *   summary="get current request's tenant role.",
     *     operationId="getCurrentRequestTenantRole",
     * path="/api/tenantroles",
     *   @OA\Response(
     *     response=200,
     *     description="Tenant role is retrieved."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Tenant role is not retrieved."
     *   )
     * )
     */
    public function tenantRoles(Request $request)
    {
        // TODO Authenticate currently logged in tenant
        $tenant = $this->getTenantById($request->tenant()->id);
        if ($this->isEmpty($tenant)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $roles = $tenant->roles()->wherePivot('status', true)->get();

        return $this->successResponse('Role', $roles, 'retrieve');
    }
}

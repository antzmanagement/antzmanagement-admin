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
            'birthdayFromMonth' => $request->birthdayFromMonth,
            'birthdayFromDay' => $request->birthdayFromDay,
            'birthdayToMonth' => $request->birthdayToMonth,
            'birthdayToDay' => $request->birthdayToDay,
            'occupation' => $request->occupation,
            'state' => $request->state,
            'room_id' => $request->room_id,
            'tel' => $request->tel,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $data = $this->filterTenants($params, $this->toInt($request->pageSize), ($this->toInt($request->pageNumber) - 1 ) * $this->toInt($request->pageSize));

        if ($this->isEmpty($data)) {
            return $this->errorPaginateResponse('Tenants');
        } else {
            return $this->successPaginateResponse('Tenants', $data['data'], $this->toInt($request->pageSize), $this->toInt($request->pageNumber), $data['total']);
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
            'pic' => $request->user()->id,
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

        if (!$this->isEmpty($request->room)) {

          
        $pic = $this->getStaffById($request->pic);
        if ($this->isEmpty($pic)) {
            DB::rollBack();
            return $this->notFoundResponse('Staff');
        }


        //Copy from this line (for tenant store method)
        $request->room = json_decode(json_encode($request->room));
        $room = $this->getRoomById($request->room->id);
        if ($this->isEmpty($room)) {
            DB::rollBack();
            return $this->notFoundResponse('Room');
        }

        $contract = $this->getContractById($request->room->contract_id);
        if ($this->isEmpty($contract)) {
            DB::rollBack();
            return $this->notFoundResponse('Contract');
        }

        $startdate = Carbon::parse($request->room->startdate)->format('Y-m-d');
        $enddate = Carbon::parse($request->room->enddate)->format('Y-m-d');
        if(isset($request->room->rental_payment_start_date)){
            $rental_payment_start_date = Carbon::parse($request->room->rental_payment_start_date)->format('Y-m-d');
            if(Carbon::parse($startdate)->greaterThan(Carbon::parse($rental_payment_start_date))){
                $rental_payment_start_date = $startdate;
            }else if(Carbon::parse($rental_payment_start_date)->greaterThan(Carbon::parse($enddate))){
                $rental_payment_start_date = $enddate;
            }
        }else{
            $rental_payment_start_date = $startdate;
        }

        $duration = $contract->duration;
        if($contract->rental_type == 'day'){
            $duration = Carbon::parse($startdate)->diffInDays(Carbon::parse($enddate)) + 1;
        }else{
            $date1 = Carbon::parse($startdate)->firstOfMonth();
            $date2 = Carbon::parse($enddate)->firstOfMonth();
            if($date1->greaterThan($date2)){
                $duration = 0;
            }else{
                $duration = $date1->floatDiffInMonths($date2) + 1;
            }
        }

        $servicesIds = collect($request->room->services)->pluck('id');
        $origServiceIds = collect($request->room->origServices)->pluck('id');
        $addOnServicesIds = $servicesIds->diff($origServiceIds);


        $latest = 0;
        if($contract->rental_type == 'day'){
            if(isset($request->room->rental_payment_start_date)){
                $latest = Carbon::parse($startdate)->diffInDays(Carbon::parse($rental_payment_start_date));
            }
        }else{
            if(isset($request->room->rental_payment_start_date)){
                $date1 = Carbon::parse($startdate)->firstOfMonth();
                $date2 = Carbon::parse($rental_payment_start_date)->firstOfMonth();
                $latest = $date1->floatDiffInMonths($date2, false);
            }
        }

        $max = RoomContract::where('status', true)->max('sequence') + 1;
        $outstanding = $this->toDouble($request->room->deposit) + $this->toDouble($request->room->agreement_fees) - $this->toDouble($request->room->booking_fees);
        $params = collect([
            'tenant_id' => $tenant->id,
            'pic' => $pic->id,
            'room_id' => $room->id,
            'contract_id' => $contract->id,
            'name' => $startdate. ' - ' . $enddate,
            'duration' => $duration,
            'latest' => $latest,
            'penalty' => $contract->penalty,
            'penalty_day' => $contract->penalty_day,
            'rental_type' => $contract->rental_type,
            'terms' => $contract->terms,
            'autorenew' => $request->room->autorenew,
            'startdate' => $startdate,
            'enddate' => $enddate,
            'rental_payment_start_date' => $rental_payment_start_date,
            'rental' => $request->room->price,
            'deposit' => $request->room->deposit,
            'agreement_fees' => $request->room->agreement_fees,
            'outstanding' => $outstanding,
            'booking_fees' => $request->room->booking_fees,
            'remark' => $request->remark,
            'sequence' => $max,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $roomContract = $this->createRoomContract($params);
        if ($this->isEmpty($roomContract)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $finalservices = collect();
        foreach ($addOnServicesIds as $id) {
            $service = $this->getServiceById($id);
            if ($this->isEmpty($service)) {
                DB::rollBack();
                return $this->errorResponse();
            }

            $finalservices[$service->id] = ['status' => true];
        }
        $roomContract->addonservices()->sync($finalservices);

        $finalservices = collect();
        foreach ($origServiceIds as $id) {
            $service = $this->getServiceById($id);
            if ($this->isEmpty($service)) {
                DB::rollBack();
                return $this->errorResponse();
            }
            $finalservices[$service->id] = ['status' => true];
        }

        $roomContract->origservices()->sync($finalservices);

        $outstanding_deposit = $roomContract->deposit - $roomContract->booking_fees;
        if($outstanding_deposit > 0){
            $params = collect([
                'room_contract_id' => $roomContract->id,
                'other_charges' => $outstanding_deposit,
            ]);
            //Convert To Json Object
            $params = json_decode(json_encode($params));
            $payment = $this->createPayment($params);
            $data = $this->getOtherPaymentTitleByName('Deposit');
            if (!$this->isEmpty($data)) {
                $data->price = $this->toDouble($outstanding_deposit);
                $payment->otherpayments->push($data);
                $payment->otherpayments()->syncWithoutDetaching([$data->id => ['status' => true, 'price' => $data->price]]);
            }else{
                $params = collect([
                    'name' => 'Deposit',
                ]);
                //Convert To Json Object
                $params = json_decode(json_encode($params));
                $data = $this->createOtherPaymentTitle($params);
                if (!$this->isEmpty($data)) {
                    $data->price = $this->toDouble($outstanding_deposit);
                    $payment->otherpayments->push($data);
                    $payment->otherpayments()->syncWithoutDetaching([$data->id => ['status' => true, 'price' => $data->price]]);
                }

            }
        }


        if($roomContract->booking_fees > 0){
            $params = collect([
                'room_contract_id' => $roomContract->id,
                'other_charges' => $roomContract->booking_fees,
            ]);
            //Convert To Json Object
            $params = json_decode(json_encode($params));
            $payment = $this->createPayment($params);
            $data = $this->getOtherPaymentTitleByName('Partial Payment(Deposit)');
            if (!$this->isEmpty($data)) {
                $data->price = $this->toDouble($roomContract->booking_fees);
                $payment->otherpayments->push($data);
                $payment->otherpayments()->syncWithoutDetaching([$data->id => ['status' => true, 'price' => $data->price]]);
            }else{
                $params = collect([
                    'name' => 'Partial Payment(Deposit)',
                ]);
                //Convert To Json Object
                $params = json_decode(json_encode($params));
                $data = $this->createOtherPaymentTitle($params);
                if (!$this->isEmpty($data)) {
                    $data->price = $this->toDouble($roomContract->booking_fees);
                    $payment->otherpayments->push($data);
                    $payment->otherpayments()->syncWithoutDetaching([$data->id => ['status' => true, 'price' => $data->price]]);
                }

            }
        }
        

        if($roomContract->agreement_fees > 0){
            $params = collect([
                'room_contract_id' => $roomContract->id,
                'other_charges' => $roomContract->agreement_fees,
            ]);
            //Convert To Json Object
            $params = json_decode(json_encode($params));
            $payment = $this->createPayment($params);
            $data = $this->getOtherPaymentTitleByName('Agreement Fees');
            if (!$this->isEmpty($data)) {
                $data->price = $this->toDouble($roomContract->agreement_fees);
                $payment->otherpayments->push($data);
                $payment->otherpayments()->syncWithoutDetaching([$data->id => ['status' => true, 'price' => $data->price]]);
            }else{
                $params = collect([
                    'name' => 'Agreement Fees',
                ]);
                //Convert To Json Object
                $params = json_decode(json_encode($params));
                $data = $this->createOtherPaymentTitle($params);
                if (!$this->isEmpty($data)) {
                    $data->price = $this->toDouble($roomContract->agreement_fees);
                    $payment->otherpayments->push($data);
                    $payment->otherpayments()->syncWithoutDetaching([$data->id => ['status' => true, 'price' => $data->price]]);
                }

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

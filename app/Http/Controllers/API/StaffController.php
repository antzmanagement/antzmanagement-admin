<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\UserType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use App\Traits\AllServices;

class StaffController extends Controller
{
    use AllServices;

    private $controllerName = '[StaffController]';
    /**
     * @OA\Get(
     *      path="/api/staff",
     *      operationId="getStaffs",
     *      tags={"StaffControllerService"},
     *      summary="Get list of staffs",
     *      description="Returns list of staffs",
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
     *          description="Successfully retrieved list of staffs"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of staffs")
     *    )
     */
    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of staffs.');
        // api/staff (GET)
        $staffs = $this->getStaffs($request->user());
        if ($this->isEmpty($staffs)) {
            return $this->errorPaginateResponse('Staffs');
        } else {
            return $this->successPaginateResponse('Staffs', $staffs, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }


    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered staffs.');
        // api/staff/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $data = $this->filterStaffs($params, $this->toInt($request->pageSize), ($this->toInt($request->pageNumber) - 1 ) * $this->toInt($request->pageSize));
        if ($this->isEmpty($data)) {
            return $this->errorPaginateResponse('Staffs');
        } else {
            return $this->successPaginateResponse('Staffs', $data['data'], $this->toInt($request->pageSize), $this->toInt($request->pageNumber), $data['total']);
        }
    }
    /**
     * @OA\Get(
     *   tags={"StaffControllerService"},
     *   path="/api/staff/{uid}",
     *   summary="Retrieves staff by Uid.",
     *     operationId="getStaffByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Staff_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Staff has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the staff."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/staff/{staffid} (GET)
        error_log($this->controllerName . 'Retrieving staff of uid:' . $uid);
        $staff = $this->getStaff($uid);
        if ($this->isEmpty($staff)) {
            $data['data'] = null;
            return $this->notFoundResponse('Staff');
        } else {
            return $this->successResponse('Staff', $staff, 'retrieve');
        }
    }

    /**
     * @OA\Post(
     *   tags={"StaffControllerService"},
     *   path="/api/staff",
     *   summary="Creates a staff.",
     *   operationId="createStaff",
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Staffname",
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
     *     description="Staff has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the staff."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        $proccessingimgids = collect();
        // Can only be used by Authorized personnel
        // api/staff (POST)
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
            'role_id' => 'required',
        ]);
        error_log($this->controllerName . 'Creating staff.');
        $params = collect([
            'name' => $request->name,
            'icno' => $request->icno,
            'tel1' => $request->tel1,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $request->role_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $staff = $this->createStaff($params);
        if ($this->isEmpty($staff)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        //Uploading image
        if($request->file('img') != null){
            $img = $this->uploadImage($request->file('img') , "/Staff/". $staff->uid);
            if(!$this->isEmpty($img)){
                $staff->profile_img = $img->imgurl;
                $staff->profile_img_publicid = $img->publicid;
                $proccessingimgids->push($img->publicid);
                if(!$this->saveModel($staff)){
                    DB::rollBack();
                    $this->deleteImages($proccessingimgids);
                    return $this->errorResponse();
                }
            }else{
                DB::rollBack();
                $this->deleteImages($proccessingimgids);
                return $this->errorResponse();
            }
        }


        $userType = UserType::where('name', 'staff')->first();
        if ($this->isEmpty($userType)) {
            DB::rollBack();
            return $this->notFoundResponse('UserType');
        }

        try {
            $userType->users()->syncWithoutDetaching([$staff->id => ['status' => true]]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('Staff', $staff, 'create');
    }

    /**
     * @OA\Put(
     *   tags={"StaffControllerService"},
     *   path="/api/staff/{uid}",
     *   summary="Update staff by Uid.",
     *     operationId="updateStaffByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Staff_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Staffname.",
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
     *     description="Staff has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the staff."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        // api/staff/{staffid} (PUT)
        error_log($this->controllerName . 'Updating staff of uid: ' . $uid);
        $staff = $this->getStaff($uid);
        $this->validate($request, [
            'email' =>
            [
                'required',
                'string',
                'email',
                'max:191',
                Rule::unique('users')->where(function ($query) use ($staff) {
                    $query->where('status', true);
                    $query->where('id', '!=', $staff->id);
                }),
            ],
            'name' => 'required|string|max:191',
            'role_id' => 'required',
        ]);
        if ($this->isEmpty($staff)) {
            DB::rollBack();
            return $this->notFoundResponse('Staff');
        }
        $params = collect([
            'icno' => $request->icno,
            'name' => $request->name,
            'email' => $request->email,
            'tel1' => $request->tel1,
            'role_id' => $request->role_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $staff = $this->updateStaff($staff, $params);
        if ($this->isEmpty($staff)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        //Associating Image Relationship
        if($request->file('img') != null){
          $img = $this->uploadImage($request->file('img') , "/Staff/". $staff->uid);
          if(!$this->isEmpty($img)){
              error_log('inside edi');
              //Delete Previous Image
              if($staff->profile_img_publicid){
                  if(!$this->deleteImage($staff->profile_img_publicid)){
                      error_log('wrong 7 edi');
                      DB::rollBack();
                      return $this->errorResponse();
                  }
              }

              $staff->profile_img = $img->imgurl;
              $staff->profile_img_publicid = $img->publicid;
              $proccessingimgids->push($img->publicid);
              if(!$this->saveModel($staff)){
                  DB::rollBack();
                  $this->deleteImages($proccessingimgids);
                  return $this->errorResponse();
              }
          }else{
              DB::rollBack();
              $this->deleteImages($proccessingimgids);
              return $this->errorResponse();
          }
      }

        DB::commit();
        return $this->successResponse('Staff', $staff, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/staff/{staffid} (DELETE)
        error_log($this->controllerName . 'Deleting staff of uid: ' . $uid);
        $staff = $this->getStaff($uid);
        if ($this->isEmpty($staff)) {
            DB::rollBack();
            return $this->notFoundResponse('Staff');
        }
        $staff = $this->deleteStaff($staff);
        if ($this->isEmpty($staff)) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('Staff', $staff, 'delete');
    }
}

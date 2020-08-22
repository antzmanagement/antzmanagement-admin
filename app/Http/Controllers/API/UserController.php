<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

class UserController extends Controller
{
    use AllServices;

    private $controllerName = '[UserController]';

    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of users.');
        // api/user (GET)
        $users = $this->getUsers($request->user());
        if ($this->isEmpty($users)) {
            return $this->errorPaginateResponse('Users');
        } else {
            return $this->successPaginateResponse('Users', $users, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }


    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered users.');
        // api/user/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'company_id' => $request->company_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $users = $this->filterUsers($request->user(), $params);

        if ($this->isEmpty($users)) {
            return $this->errorPaginateResponse('Users');
        } else {
            return $this->successPaginateResponse('Users', $users, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function show(Request $request, $uid)
    {
        // api/user/{userid} (GET)
        error_log($this->controllerName . 'Retrieving user of uid:' . $uid);
        $user = $this->getUser($request->user(), $uid);
        if ($this->isEmpty($user)) {
            $data['data'] = null;
            return $this->notFoundResponse('User');
        } else {
            return $this->successResponse('User', $user, 'retrieve');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/user (POST)
        $this->validate($request, [
            'email' => 'nullable|string|email|max:191|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        error_log($this->controllerName . 'Creating user.');
        $params = collect([
            'icno' => $request->icno,
            'name' => $request->name,
            'email' => $request->email,
            'tel1' => $request->tel1,
            'tel2' => $request->tel2,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'city' => $request->city,
            'country' => $request->country,
            'password' => $request->password,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $user = $this->createUser($params);

        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('User', $user, 'create');
        }
    }

    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/user/{userid} (PUT)
        error_log($this->controllerName . 'Updating user of uid: ' . $uid);
        $user = $this->getUser($uid);
        $this->validate($request, [
            'email' => 'required|string|max:191|unique:users,email,' . $user->id,
            'name' => 'required|string|max:191',
        ]);
        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->notFoundResponse('User');
        }
        $params = collect([
            'icno' => $request->icno,
            'name' => $request->name,
            'email' => $request->email,
            'tel1' => $request->tel1,
            'tel2' => $request->tel2,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'city' => $request->city,
            'country' => $request->country,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $user = $this->updateUser($user, $params);
        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('User', $user, 'update');
        }
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/user/{userid} (DELETE)
        error_log($this->controllerName . 'Deleting user of uid: ' . $uid);
        $user = $this->getUser($uid);
        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->notFoundResponse('User');
        }
        $user = $this->deleteUser($user);
        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('User', $user, 'delete');
        }
    }


    public function authentication(Request $request)
    {
        // TODO Authenticate currently logged in user
        error_log($this->controllerName . 'Authenticating user.');
        
        $user = $this->getUserById($request->user()->id);
        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        return $this->successResponse('User', $user, 'retrieve');
    }

    public function register(Request $request)
    {
        // TODO Registers users without needing authorization
        error_log($this->controllerName . 'Registering user.');
        // api/register (POST)
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        DB::beginTransaction();
        $user = new User();
        $user->uid = Carbon::now()->timestamp . User::count();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->status = true;
        try {
            DB::commit();
            $user->save();
            $data['status'] = 'success';
            $data['msg'] = $this->getCreatedSuccessMsg('User Account');
            $data['data'] = $user;
            $data['code'] = 200;
            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }
    }

    public function changePassword(Request $request, $uid)
    {
        DB::beginTransaction();
        
        $this->validate($request, [
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = $this->getUser($uid);
        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if (Hash::check($request->oldpassword ,$user->password )) {
            $user->password = Hash::make($request->password);
            if(!$this->saveModel($user)){
                return $this->errorResponse();
            }
            DB::commit();
            return $this->successResponse('Password', true, 'update');
        } else {
            return $this->errorResponse();
        }
    }

    public function checkPassword(Request $request, $uid)
    {
        // TODO Authenticate currently logged in user
        $user = $this->getUser($uid);
        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if (Hash::check($request->password, $user->password)) {
            return $this->successResponse('Password', true, 'retrieve');
        } else {
            return $this->successResponse('Password', false, 'retrieve');
        }
    }

    public function userRoles(Request $request)
    {
        // TODO Authenticate currently logged in user
        $user = $this->getUserById($request->user()->id);
        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $roles = $user->roles()->wherePivot('status', true)->get();

        return $this->successResponse('Role', $roles, 'retrieve');
    }
}

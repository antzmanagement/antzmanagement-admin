<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Claim;
use App\RoomContract;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

class ClaimController extends Controller
{
    use AllServices;

    private $controllerName = '[ClaimController]';

    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of claims.');
        // api/claim (GET)
        $claims = $this->getClaims($request->user());
        if ($this->isEmpty($claims)) {
            return $this->errorPaginateResponse('Claims');
        } else {
            return $this->successPaginateResponse('Claims', $claims, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered claims.');
        // api/claim/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'paid' => $request->paid,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $claims = $this->getClaims($request->user());
        $claims = $this->filterClaims($claims, $params);

        if ($this->isEmpty($claims)) {
            return $this->errorPaginateResponse('Claims');
        } else {
            return $this->successPaginateResponse('Claims', $claims, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function show(Request $request, $uid)
    {
        // api/claim/{claimid} (GET)
        error_log($this->controllerName . 'Retrieving claim of uid:' . $uid);
        $claim = $this->getClaim($uid);
        if ($this->isEmpty($claim)) {
            $data['data'] = null;
            return $this->notFoundResponse('Claim');
        } else {
            return $this->successResponse('Claim', $claim, 'retrieve');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/roomType (POST)
        $this->validate($request, [
            'maintenance_fees' => 'nullable|numeric',
            'admin_fees' => 'nullable|numeric',
            'other_fees' => 'nullable|numeric',
            'rental_fees' => 'nullable|numeric',
            'owner' => 'required',
            'rentalpayments' => 'array',
            'maintenances' => 'array',
        ]);
        error_log($this->controllerName . 'Creating roomType.');
        $params = collect([
            'maintenance_fees' => $request->maintenance_fees,
            'admin_fees' => $request->admin_fees,
            'other_fees' => $request->other_fees,
            'rental_fees' => $request->rental_fees,
            'owner_id' => $request->owner,
            'rentalpayments' => $request->rentalpayments,
            'maintenances' => $request->maintenances,
            'remark' => $request->remark,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $claim = $this->createClaim($params);
        if ($this->isEmpty($claim)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('Claim', $claim, 'create');
    }

    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/claim/{claimid} (PUT)
        error_log($this->controllerName . 'Updating claim of uid: ' . $uid);
        $claim = $this->getClaim($uid);
        $this->validate($request, [
            'name' => 'required|string|max:300',
            'address' => 'nullable|string|max:300',
            'postcode' => 'nullable|string|max:300',
            'state' => 'nullable|string|max:300',
            'city' => 'nullable|string|max:300',
            'country' => 'nullable|string|max:300',
            'price' => 'required|numeric',
            'claimTypes' => 'required',
        ]);
        if ($this->isEmpty($claim)) {
            DB::rollBack();
            return $this->notFoundResponse('Claim');
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
        $claim = $this->updateClaim($claim, $params);

        if ($this->isEmpty($claim)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $claimTypes = ClaimType::find($request->claimTypes);
        if ($this->isEmpty($claimTypes)) {
            DB::rollBack();
            return $this->notFoundResponse('ClaimType');
        }

        try {
            $claim->claimTypes()->sync($claimTypes->pluck('id'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }
        $claim = $this->getClaimById($claim->id);
        if ($this->isEmpty($claim)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        DB::commit();
        return $this->successResponse('Claim', $claim, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/claim/{claimid} (DELETE)
        error_log($this->controllerName . 'Deleting claim of uid: ' . $uid);
        $claim = $this->getClaim($uid);
        if ($this->isEmpty($claim)) {
            DB::rollBack();
            return $this->notFoundResponse('Claim');
        }
        $claim = $this->deleteClaim($claim);
        if ($this->isEmpty($claim)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if (!$this->syncWithClaim($claim->roomcontract)) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('Claim', $claim, 'delete');
    }

}

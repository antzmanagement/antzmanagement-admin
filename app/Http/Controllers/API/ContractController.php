<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\AllServices;

class ContractController extends Controller
{
    use AllServices;

    private $controllerName = '[ContractController]';

    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of contracts.');
        // api/contract (GET)
        $contracts = $this->getContracts($request->user());
        if ($this->isEmpty($contracts)) {
            return $this->errorPaginateResponse('Contracts');
        } else {
            return $this->successPaginateResponse('Contracts', $contracts, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered contracts.');
        // api/contract/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $contracts = $this->getContracts($request->user());
        $contracts = $this->filterContracts($contracts, $params);

        if ($this->isEmpty($contracts)) {
            return $this->errorPaginateResponse('Contracts');
        } else {
            return $this->successPaginateResponse('Contracts', $contracts, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function show(Request $request, $uid)
    {
        // api/contract/{contractid} (GET)
        error_log($this->controllerName . 'Retrieving contract of uid:' . $uid);
        $contract = $this->getContract($uid);
        if ($this->isEmpty($contract)) {
            $data['data'] = null;
            return $this->notFoundResponse('Contract');
        } else {
            return $this->successResponse('Contract', $contract, 'retrieve');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/contract (POST)
        $this->validate($request, [
            'remark' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'rooms' => 'required|array',
            'properties' => 'nullable|array',
        ]);
        error_log($this->controllerName . 'Creating contract.');

        foreach ($request->rooms as $roomid) {
            foreach ($request->properties as $propertyid) {

                $params = collect([
                    'remark' => $request->remark,
                    'price' => $request->price,
                    'room_id' => $roomid,
                    'property_id' => $propertyid,
                ]);
                //Convert To Json Object
                $params = json_decode(json_encode($params));
                $contract = $this->createContract($params);
                if ($this->isEmpty($contract)) {
                    DB::rollBack();
                    return $this->errorResponse();
                }
            }
        }

        DB::commit();
        return $this->successResponse('Contract', $contract, 'create');
    }

    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/contract/{contractid} (PUT)
        error_log($this->controllerName . 'Updating contract of uid: ' . $uid);

        $this->validate($request, [
            'remark' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'rooms' => 'required|numeric',
            'properties' => 'nullable|numeric',
        ]);
        $contract = $this->getContract($uid);
        if ($this->isEmpty($contract)) {
            DB::rollBack();
            return $this->notFoundResponse('Contract');
        }
        $params = collect([
            'remark' => $request->remark,
            'price' => $request->price,
            'room_id' => $request->rooms,
            'property_id' => $request->properties,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $contract = $this->updateContract($contract, $params);

        if ($this->isEmpty($contract)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('Contract', $contract, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/contract/{contractid} (DELETE)
        error_log($this->controllerName . 'Deleting contract of uid: ' . $uid);
        $contract = $this->getContract($uid);
        if ($this->isEmpty($contract)) {
            DB::rollBack();
            return $this->notFoundResponse('Contract');
        }
        $contract = $this->deleteContract($contract);
        if ($this->isEmpty($contract)) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('Contract', $contract, 'delete');
    }
}

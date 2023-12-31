<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Maintenance;
use App\Traits\AllServices;

class MaintenanceController extends Controller
{
    use AllServices;

    private $controllerName = '[MaintenanceController]';

    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of maintenances.');
        // api/maintenance (GET)
        $maintenances = $this->getMaintenances($request->user());
        if ($this->isEmpty($maintenances)) {
            return $this->errorPaginateResponse('Maintenances');
        } else {
            return $this->successPaginateResponse('Maintenances', $maintenances, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered maintenances.');
        // api/maintenance/filter (GET)
        $params = collect([
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'maintenance_type' => $request->maintenance_type,
            'owner_id' => $request->owner_id,
            'tenant_id' => $request->tenant_id,
            'room_id' => $request->room_id,
            'property_id' => $request->property_id,
            'maintenance_status' => $request->maintenance_status,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $data = $this->filterMaintenances($params, $this->toInt($request->pageSize), ($this->toInt($request->pageNumber) - 1 ) * $this->toInt($request->pageSize));

        if ($this->isEmpty($data)) {
            return $this->errorPaginateResponse('Maintenances');
        } else {
            return $this->successPaginateResponse('Maintenances', $data['data'], $this->toInt($request->pageSize), $this->toInt($request->pageNumber), $data['total']);
        }
    }

    public function show(Request $request, $uid)
    {
        // api/maintenance/{maintenanceid} (GET)
        error_log($this->controllerName . 'Retrieving maintenance of uid:' . $uid);
        $maintenance = $this->getMaintenance($uid);
        if ($this->isEmpty($maintenance)) {
            $data['data'] = null;
            return $this->notFoundResponse('Maintenance');
        } else {
            return $this->successResponse('Maintenance', $maintenance, 'retrieve');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/maintenance (POST)
        $this->validate($request, [
            'remark' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'room_id' => 'required',
            'property_id' => 'required',
            'maintenance_status' => 'required',
            'maintenance_type' => 'required',
            'claim_by_owner' => 'required',
            'claim_by_tenant' => 'required',
        ]);
        error_log($this->controllerName . 'Creating maintenance.');

        $maintenance = $this->createMaintenance($request);
        if ($this->isEmpty($maintenance)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('Maintenance', $maintenance, 'create');
    }

    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/maintenance/{maintenanceid} (PUT)
        error_log($this->controllerName . 'Updating maintenance of uid: ' . $uid);

        $this->validate($request, [
            'remark' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'room_id' => 'required',
            'property_id' => 'required',
            'maintenance_status' => 'required',
            'maintenance_type' => 'required',
        ]);
        $maintenance = $this->getMaintenance($uid);
        if ($this->isEmpty($maintenance)) {
            DB::rollBack();
            return $this->notFoundResponse('Maintenance');
        }
        if($maintenance->claim_by_tenant && $request->paid){
            $request->receive_from = $maintenance->tenant->name;
            $request->issue_by = $request->user()->id;
        }else if($maintenance->claim_by_owner && $request->paid){
            $request->receive_from = $maintenance->owner->name;
            $request->issue_by = $request->user()->id;
        }else if(!$maintenance->claim_by_owner && !$maintenance->claim_by_tenant){
            $request->issue_by = $request->user()->id;
        }

        if($request->paid && !$request->sequence){
            $request->sequence = Maintenance::where('status', true)->max('sequence') + 1;
            $request->receiptno = 'mp-'.$request->sequence;
        }
        $maintenance = $this->updateMaintenance($maintenance, $request);

        if ($this->isEmpty($maintenance)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        DB::commit();
        return $this->successResponse('Maintenance', $maintenance, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/maintenance/{maintenanceid} (DELETE)
        error_log($this->controllerName . 'Deleting maintenance of uid: ' . $uid);
        $maintenance = $this->getMaintenance($uid);
        if ($this->isEmpty($maintenance)) {
            DB::rollBack();
            return $this->notFoundResponse('Maintenance');
        }
        $maintenance = $this->deleteMaintenance($maintenance);
        if ($this->isEmpty($maintenance)) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('Maintenance', $maintenance, 'delete');
    }
}

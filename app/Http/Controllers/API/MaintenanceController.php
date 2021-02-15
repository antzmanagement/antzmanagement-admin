<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
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
            'room_id' => $request->room_id,
            'property_id' => $request->property_id,
            'maintenance_status' => $request->maintenance_status,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $maintenances = $this->getMaintenances($request->user());
        $maintenances = $this->filterMaintenances($maintenances, $params);

        if ($this->isEmpty($maintenances)) {
            return $this->errorPaginateResponse('Maintenances');
        } else {
            return $this->successPaginateResponse('Maintenances', $maintenances, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
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
            'room' => 'required',
            'property' => 'required',
            'maintenance_status' => 'required',
            'maintenance_type' => 'required',
        ]);
        error_log($this->controllerName . 'Creating maintenance.');

        $params = collect([
            'remark' => $request->remark,
            'price' => $request->price,
            'room_id' => $request->room,
            'property_id' => $request->property,
            'owner_id' => $request->owner,
            'maintenance_status' => $request->maintenance_status,
            'maintenance_type' => $request->maintenance_type,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $maintenance = $this->createMaintenance($params);
        if ($this->isEmpty($maintenance)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        error_log($maintenance);

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
            'room' => 'required',
            'property' => 'required',
            'maintenance_status' => 'required',
            'maintenance_type' => 'required',
        ]);
        $maintenance = $this->getMaintenance($uid);
        if ($this->isEmpty($maintenance)) {
            DB::rollBack();
            return $this->notFoundResponse('Maintenance');
        }
        $params = collect([
            'remark' => $request->remark,
            'price' => $request->price,
            'room_id' => $request->room,
            'property_id' => $request->property,
            'owner_id' => $request->owner,
            'maintenance_status' => $request->maintenance_status,
            'maintenance_type' => $request->maintenance_type,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $maintenance = $this->updateMaintenance($maintenance, $params);

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

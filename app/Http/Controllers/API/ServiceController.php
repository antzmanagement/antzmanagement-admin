<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\AllServices;

class ServiceController extends Controller
{
    use AllServices;

    private $controllerName = '[ServiceController]';

    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of services.');
        // api/service (GET)
        $services = $this->getServices($request->user());
        if ($this->isEmpty($services)) {
            return $this->errorPaginateResponse('Services');
        } else {
            return $this->successPaginateResponse('Services', $services, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered services.');
        // api/service/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'serviceTypes' => $request->serviceTypes,
            'status' => $request->status,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $data = $this->filterServices($params, $this->toInt($request->pageSize), ($this->toInt($request->pageNumber) - 1 ) * $this->toInt($request->pageSize));

        if ($this->isEmpty($data)) {
            return $this->errorPaginateResponse('Services');
        } else {
            return $this->successPaginateResponse('Services', $data['data'], $this->toInt($request->pageSize), $this->toInt($request->pageNumber), $data['total']);
        }
    }

    public function show(Request $request, $uid)
    {
        // api/service/{serviceid} (GET)
        error_log($this->controllerName . 'Retrieving service of uid:' . $uid);
        $service = $this->getService($uid);
        if ($this->isEmpty($service)) {
            $data['data'] = null;
            return $this->notFoundResponse('Service');
        } else {
            return $this->successResponse('Service', $service, 'retrieve');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/service (POST)
        $this->validate($request, [
            'name' => 'required|string|max:300',
            'price' => 'required|max:300',
            'text' => 'nullable|string|max:300',
            'desc' => 'nullable|string|max:2500',
            'icon' => 'nullable|string|max:300',
        ]);

        error_log($this->controllerName . 'Creating service.');
        $params = collect([
            'name' => $request->name,
            'text' => $request->text,
            'desc' => $request->desc,
            'icon' => $request->icon,
            'price' => $request->price,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $service = $this->createService($params);
        if ($this->isEmpty($service)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('Service', $service, 'create');
    }

    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/service/{serviceid} (PUT)
        error_log($this->controllerName . 'Updating service of uid: ' . $uid);
        $service = $this->getService($uid);
        if ($this->isEmpty($service)) {
            DB::rollBack();
            return $this->notFoundResponse('Service');
        }

        $this->validate($request, [
            'name' => 'required|string|max:300',
            'price' => 'required|max:300',
            'text' => 'nullable|string|max:300',
            'desc' => 'nullable|string|max:2500',
            'icon' => 'nullable|string|max:300',
        ]);
        $params = collect([
            'name' => $request->name,
            'text' => $request->text,
            'desc' => $request->desc,
            'icon' => $request->icon,
            'price' => $request->price,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $service = $this->updateService($service, $params);

        if ($this->isEmpty($service)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        DB::commit();
        return $this->successResponse('Service', $service, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/service/{serviceid} (DELETE)
        error_log($this->controllerName . 'Deleting service of uid: ' . $uid);
        $service = $this->getService($uid);
        if ($this->isEmpty($service)) {
            DB::rollBack();
            return $this->notFoundResponse('Service');
        }
        $service = $this->deleteService($service);
        if ($this->isEmpty($service)) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('Service', $service, 'delete');
    }
}

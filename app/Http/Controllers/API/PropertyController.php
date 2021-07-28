<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\AllServices;

class PropertyController extends Controller
{
    use AllServices;

    private $controllerName = '[PropertyController]';

    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of properties.');
        // api/property (GET)
        $properties = $this->getProperties($request->user());
        if ($this->isEmpty($properties)) {
            return $this->errorPaginateResponse('Properties');
        } else {
            return $this->successPaginateResponse('Properties', $properties, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered properties.');
        // api/property/filter (GET)
        $params = collect([
            'name' => $request->name,
            'category' => $request->category,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $properties = $this->getProperties($request->user());
        $properties = $this->filterProperties( $properties , $params);

        if ($this->isEmpty($properties)) {
            return $this->errorPaginateResponse('Properties');
        } else {
            return $this->successPaginateResponse('Properties', $properties, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function show(Request $request, $uid)
    {
        // api/property/{propertyid} (GET)
        error_log($this->controllerName . 'Retrieving property of uid:' . $uid);
        $property = $this->getProperty($uid);
        if ($this->isEmpty($property)) {
            $data['data'] = null;
            return $this->notFoundResponse('Property');
        } else {
            return $this->successResponse('Property', $property, 'retrieve');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/property (POST)
        $this->validate($request, [
            'name' => 'required|string|max:300',
            'text' => 'nullable|string|max:300',
            'desc' => 'nullable|string|max:2500',
        ]);
        error_log($this->controllerName . 'Creating property.');
        $params = collect([
            'name' => $request->name,
            'text' => $request->text,
            'desc' => $request->desc,
            'price' => $request->price,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $property = $this->createProperty($params);
        if ($this->isEmpty($property)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('Property', $property, 'create');
    }

    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/property/{propertyid} (PUT)
        error_log($this->controllerName . 'Updating property of uid: ' . $uid);
      
        $this->validate($request, [
            'name' => 'required|string|max:300',
            'text' => 'nullable|string|max:300',
            'desc' => 'nullable|string|max:2500',
        ]);

        $property = $this->getProperty($uid);
        if ($this->isEmpty($property)) {
            DB::rollBack();
            return $this->notFoundResponse('Property');
        }
        $params = collect([
            'name' => $request->name,
            'text' => $request->text,
            'desc' => $request->desc,
            'price' => $request->price,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $property = $this->updateProperty($property, $params);

        if ($this->isEmpty($property)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('Property', $property, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/property/{propertyid} (DELETE)
        error_log($this->controllerName . 'Deleting property of uid: ' . $uid);
        $property = $this->getProperty($uid);
        if ($this->isEmpty($property)) {
            DB::rollBack();
            return $this->notFoundResponse('Property');
        }
        $property = $this->deleteProperty($property);
        if ($this->isEmpty($property)) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('Property', $property, 'delete');
    }
}

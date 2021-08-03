<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Cleaning;
use App\Traits\AllServices;

class CleaningController extends Controller
{
    use AllServices;

    private $controllerName = '[CleaningController]';

    public function index(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of cleanings.');
        // api/cleaning (GET)
        $cleanings = $this->getCleanings($request->user());
        if ($this->isEmpty($cleanings)) {
            return $this->errorPaginateResponse('Cleanings');
        } else {
            return $this->successPaginateResponse('Cleanings', $cleanings, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function filter(Request $request)
    {
        error_log($this->controllerName . 'Retrieving list of filtered cleanings.');
        // api/cleaning/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $cleanings = $this->getCleanings($request->user());
        $cleanings = $this->filterCleanings($cleanings, $params);

        if ($this->isEmpty($cleanings)) {
            return $this->errorPaginateResponse('Cleanings');
        } else {
            return $this->successPaginateResponse('Cleanings', $cleanings, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    public function show(Request $request, $uid)
    {
        // api/cleaning/{cleaningid} (GET)
        error_log($this->controllerName . 'Retrieving cleaning of uid:' . $uid);
        $cleaning = $this->getCleaning($uid);
        if ($this->isEmpty($cleaning)) {
            $data['data'] = null;
            return $this->notFoundResponse('Cleaning');
        } else {
            return $this->successResponse('Cleaning', $cleaning, 'retrieve');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/cleaning (POST)
        $this->validate($request, [
            'remark' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'rooms' => 'required|array',
            'properties' => 'nullable|array',
        ]);
        error_log($this->controllerName . 'Creating cleaning.');

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
                $cleaning = $this->createCleaning($params);
                if ($this->isEmpty($cleaning)) {
                    DB::rollBack();
                    return $this->errorResponse();
                }
            }
        }

        DB::commit();
        return $this->successResponse('Cleaning', $cleaning, 'create');
    }

    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/cleaning/{cleaningid} (PUT)
        error_log($this->controllerName . 'Updating cleaning of uid: ' . $uid);

        $this->validate($request, [
            'remark' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'room_id' => 'required',
            'cleaning_status' => 'required',
            'cleaning_type' => 'required',
        ]);
        $cleaning = $this->getCleaning($uid);
        if ($this->isEmpty($cleaning)) {
            DB::rollBack();
            return $this->notFoundResponse('Cleaning');
        }

        if($cleaning->claim_by_tenant && $request->paid){
            $request->receive_from = $cleaning->tenant->name;
            $request->issue_by = $request->user()->id;
        }else if($cleaning->claim_by_owner && $request->paid){
            $request->receive_from = $cleaning->owner->name;
            $request->issue_by = $request->user()->id;
        }else if(!$cleaning->claim_by_owner && !$cleaning->claim_by_tenant){
            $request->issue_by = $request->user()->id;
        }

        if($request->paid && !$request->sequence){
            $request->sequence = Cleaning::where('status', true)->max('sequence') + 1;
            $request->receiptno = 'cp-'.$request->sequence;
        }
        $cleaning = $this->updateCleaning($cleaning, $request);

        if ($this->isEmpty($cleaning)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        DB::commit();
        return $this->successResponse('Cleaning', $cleaning, 'update');
    }

    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/cleaning/{cleaningid} (DELETE)
        error_log($this->controllerName . 'Deleting cleaning of uid: ' . $uid);
        $cleaning = $this->getCleaning($uid);
        if ($this->isEmpty($cleaning)) {
            DB::rollBack();
            return $this->notFoundResponse('Cleaning');
        }
        $cleaning = $this->deleteCleaning($cleaning);
        if ($this->isEmpty($cleaning)) {
            DB::rollBack();
            return $this->errorResponse();
        }


        DB::commit();
        return $this->successResponse('Cleaning', $cleaning, 'delete');
    }
}

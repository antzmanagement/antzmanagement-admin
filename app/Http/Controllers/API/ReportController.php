<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Room;
use App\RoomType;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

class ReportController extends Controller
{
    use AllServices;


    public function index(Request $request)
    {
        $reports = $this->getReports($request->user());
        if ($this->isEmpty($reports)) {
            $data['data'] = null;
            return $this->notFoundResponse('Report');
        } else {
            return $this->successResponse('Report', $reports, 'retrieve');
        }
    }

    public function filter(Request $request)
    {
    }

    public function show(Request $request, $uid)
    {
    }

    public function store(Request $request)
    {
    }

    public function update(Request $request, $uid)
    {
    }

    public function destroy(Request $request, $uid)
    {
    }
}

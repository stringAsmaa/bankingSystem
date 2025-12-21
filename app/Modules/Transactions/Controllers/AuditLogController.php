<?php

namespace App\Modules\Transactions\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = Activity::query()->with('causer')->latest()->paginate(20);

        return ApiResponse::sendResponse(200,'Audit logs retrieved successfully.',$logs);
    }
}

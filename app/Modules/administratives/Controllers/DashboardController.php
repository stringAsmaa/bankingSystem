<?php

namespace App\Modules\administratives\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Modules\administratives\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        try {
            $data = $this->dashboardService->getDashboardData($request->user());

            return ApiResponse::sendResponse(
                200,
                'Dashboard data retrieved successfully',
                $data
            );
        } catch (\Exception $e) {
            return ApiResponse::sendError($e->getMessage(), 403);
        }
    }
}

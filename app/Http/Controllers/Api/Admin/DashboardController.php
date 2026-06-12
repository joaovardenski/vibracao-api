<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Admin\Services\DashboardService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->dashboardService->getStats(),
        ]);
    }
}
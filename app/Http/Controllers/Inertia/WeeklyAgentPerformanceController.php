<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Services\OwnerDashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WeeklyAgentPerformanceController extends Controller
{
    protected $dashboardService;

    public function __construct(OwnerDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the Weekly Agent Performance Report.
     */
    public function index(Request $request): Response
    {
        $authUser = $request->user();

        // Guard permission check
        abort_unless(
            $authUser->can('view performance report') || in_array($authUser->type, ['company', 'super admin'], true),
            403,
            'You do not have permission to view this page.'
        );

        $period = $request->input('period', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $centerId = $request->input('center_id') ? (int) $request->input('center_id') : null;

        // Fetch center list
        $centers = Center::select('id', 'center_name')->get();

        // Retrieve the weekly agent performance data
        $weeklyPerformanceReport = $this->dashboardService->getWeeklyAgentPerformanceReport($period, $startDate, $endDate, $centerId);

        return Inertia::render('WeeklyAgentPerformance/Index', [
            'initialFilters' => [
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'center_id' => $centerId,
            ],
            'centers' => $centers,
            'weeklyPerformanceReport' => $weeklyPerformanceReport,
        ]);
    }
}

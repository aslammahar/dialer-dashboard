<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Services\OwnerDashboardService;
use App\Models\Center;
use App\Models\Client;
use App\Models\ClosedCall;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class OwnerDashboardController extends Controller
{
    protected OwnerDashboardService $dashboardService;

    public function __construct(OwnerDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Authorize user access to Owner Dashboard.
     */
    protected function authorizeOwner()
    {
        $user = auth()->user();
        if (!$user || !in_array($user->type, ['super admin', 'company', 'Director'], true)) {
            abort(403, 'Unauthorized access to the Owner Dashboard.');
        }
    }

    /**
     * Render Owner Dashboard main page.
     */
    public function index(Request $request): Response
    {
        $this->authorizeOwner();

        $period = $request->input('period', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $centerId = $request->input('center_id') ? (int) $request->input('center_id') : null;

        // Fetch center list
        $centers = Center::select('id', 'center_name')->get();

        // Query initial data for all sections
        $salesSummary = $this->dashboardService->getSalesSummary($period, $startDate, $endDate, $centerId);
        $teamLeaderboard = $this->dashboardService->getTeamLeaderboard($period, $startDate, $endDate, $centerId);
        $agentPerformance = $this->dashboardService->getAgentPerformance($period, $startDate, $endDate, $centerId);
        $attendance = $this->dashboardService->getAttendanceOverview(null, $centerId);
        $retention = $this->dashboardService->getRetentionMetrics($period, $startDate, $endDate, $centerId);
        $alerts = $this->dashboardService->getAlerts($centerId);
        $closerStats = $this->dashboardService->getCloserStats($period, $startDate, $endDate, $centerId);
        $leadsSummary = $this->dashboardService->getLeadsSummary($period, $startDate, $endDate, $centerId);



        // Fetch closed calls data for the dashboard tab
        $search = $request->string('search')->trim()->toString();
        $page = max(1, (int) $request->query('page', 1));

        $closedCallsQuery = ClosedCall::query()
            ->orderBy('created_at', 'desc')
            ->with([
                'closer:id,name',
                'client:id,name'
            ])
            ->select([
                'id',
                'created_at',
                'closer_id',
                'closername',
                'status',
                'customer_eligibility',
                'clients_id',
                'carrier',
                'monthly_premium'
            ]);

        // Tenant/Client scoping (matching legacy logic)
        $authUser = auth()->user();
        if ($authUser->type === 'client') {
            $authUserEmail = $authUser->email;
            $client = Client::where('email', $authUserEmail)->first();
            if ($client) {
                $associatedUserIds = User::where('type', 'client')
                    ->where('client_id', $client->id)
                    ->pluck('id')
                    ->toArray();
                if (!empty($associatedUserIds)) {
                    $closedCallsQuery->whereIn('clients_id', $associatedUserIds);
                } else {
                    $closedCallsQuery->where('id', 0);
                }
            } else {
                $closedCallsQuery->where('clients_id', $authUser->id);
            }
        }

        // Apply search filter
        if ($search !== '') {
            $term = '%' . addcslashes($search, '%_\\') . '%';
            $closedCallsQuery->where(function (Builder $builder) use ($term) {
                $builder->where('customer_full_name', 'like', $term)
                    ->orWhere('carrier', 'like', $term)
                    ->orWhere('status', 'like', $term)
                    ->orWhere('closername', 'like', $term)
                    ->orWhere('customer_eligibility', 'like', $term);
            });
        }

        $closedCallsPaginator = $closedCallsQuery->paginate(20, ['*'], 'page', $page)
            ->withQueryString();

        return Inertia::render('OwnerDashboard', [
            'initialFilters' => [
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'center_id' => $centerId,
            ],
            'centers' => $centers,
            'salesSummary' => $salesSummary,
            'teamLeaderboard' => $teamLeaderboard,
            'agentPerformance' => $agentPerformance,
            'attendance' => $attendance,
            'retention' => $retention,
            'alerts' => $alerts,
            'closerStats' => $closerStats,
            'leadsSummary' => $leadsSummary,
            'closedCalls' => [
                'data' => $closedCallsPaginator->items(),
                'current_page' => $closedCallsPaginator->currentPage(),
                'last_page' => $closedCallsPaginator->lastPage(),
                'total' => $closedCallsPaginator->total(),
            ],
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * JSON Endpoint: Sales Summary
     */
    public function salesSummary(Request $request): JsonResponse
    {
        $this->authorizeOwner();

        $period = $request->input('period', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $centerId = $request->input('center_id') ? (int) $request->input('center_id') : null;

        $data = $this->dashboardService->getSalesSummary($period, $startDate, $endDate, $centerId);

        return response()->json($data);
    }

    /**
     * JSON Endpoint: Team Leaderboard
     */
    public function teamLeaderboard(Request $request): JsonResponse
    {
        $this->authorizeOwner();

        $period = $request->input('period', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $centerId = $request->input('center_id') ? (int) $request->input('center_id') : null;

        $data = $this->dashboardService->getTeamLeaderboard($period, $startDate, $endDate, $centerId);

        return response()->json($data);
    }

    /**
     * JSON Endpoint: Agent & Closer Performance
     */
    public function agentPerformance(Request $request): JsonResponse
    {
        $this->authorizeOwner();

        $period = $request->input('period', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $centerId = $request->input('center_id') ? (int) $request->input('center_id') : null;

        $data = $this->dashboardService->getAgentPerformance($period, $startDate, $endDate, $centerId);

        return response()->json($data);
    }

    /**
     * JSON Endpoint: Attendance Overview
     */
    public function attendance(Request $request): JsonResponse
    {
        $this->authorizeOwner();

        $date = $request->input('date');
        $centerId = $request->input('center_id') ? (int) $request->input('center_id') : null;

        $data = $this->dashboardService->getAttendanceOverview($date, $centerId);

        return response()->json($data);
    }

    /**
     * JSON Endpoint: Retention & Quality Metrics
     */
    public function retention(Request $request): JsonResponse
    {
        $this->authorizeOwner();

        $period = $request->input('period', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $centerId = $request->input('center_id') ? (int) $request->input('center_id') : null;

        $data = $this->dashboardService->getRetentionMetrics($period, $startDate, $endDate, $centerId);

        return response()->json($data);
    }
}

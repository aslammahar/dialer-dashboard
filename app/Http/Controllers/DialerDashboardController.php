<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DialerApiService;
use App\Services\SalesService;

class DialerDashboardController extends Controller
{
    protected array $groups = ['QA', 'Closer', 'Avatar', 'Retention', 'Support', 'Sales'];
protected array $editorEmails = [
    'm.muzammil@jsonscommunication.com',
    'fazail@jsonscommunications.com',
    'ubaid.khan@jsonscommunication.com',
    'hussamjanjua@jsons.com',
    'furqankashif@jsons.com',
];
    public function index(Request $request)
{
    
    $view = $request->get('view', 'active');

    $defaultFrom = $view === 'archive'
        ? now()->startOfMonth()->toDateString()
        : now()->subDays(1)->toDateString();
    $defaultTo = $view === 'archive'
        ? now()->endOfMonth()->toDateString()
        : now()->toDateString();

    $filters = [
        'view'  => $view,
        'group' => $request->get('group', 'all'),
        'from'  => $request->get('from', $defaultFrom),
        'to'    => $request->get('to', $defaultTo),
    ];

    $canEdit = auth()->check() && in_array(auth()->user()->email, $this->editorEmails);
$isCloser = auth()->check() && auth()->user()->type === 'Closer';
// Safety net: closer kabhi bhi URL se ?view=archive ya ?group=X force nahi kar sakta
if ($isCloser) {
    $filters['view']  = 'active';
    $filters['group'] = 'all';
}
    $leaderboard       = [];
    $leaderboardTotals = ['calls' => 0, 'time' => '0:00:00', 'avg_talk_time' => '0:00:00'];
    $stats             = $this->fallbackStats();
    $trend             = ['labels' => [], 'avg_talk_time_minutes' => [], 'call_volume' => []];
    $groups            = $this->groups;
    $dialerError       = null;

    try {
        $dialerApi   = app(DialerApiService::class);
        $leaderboard = $dialerApi->leaderboard($filters);
        $groups      = $dialerApi->leaderboardTeams($leaderboard) ?: $this->groups;

        if ($filters['group'] !== 'all') {
            $leaderboard = array_values(array_filter(
                $leaderboard,
                fn ($a) => $a['team'] === $filters['group']
            ));
        }

        $leaderboardTotals = $dialerApi->leaderboardTotals($leaderboard);
        $stats             = $dialerApi->summary($filters);
        $trend             = $dialerApi->trend($filters);
    } catch (\Throwable $e) {
        report($e);
        $dialerError = 'Could not load live data from the dialer right now.';
    }

    $todayTotals = ['calls' => 0, 'avg_talk_time' => '0:00:00'];
try {
    $todayLeaderboard = $dialerApi->leaderboard([
        'from' => now()->toDateString(),
        'to'   => now()->toDateString(),
    ]);
    $todayTotals = $dialerApi->leaderboardTotals($todayLeaderboard);
} catch (\Throwable $e) {
    report($e);
}

    // Live sales board + monthly goal + team boxes — pulled from our own
    // DailySalesEntry table (see SalesService), no more hardcoded numbers.
  $salesService      = app(SalesService::class);
$todayNY            = now('America/New_York')->toDateString();

$dailyBoard         = $salesService->dailyBoard($todayNY);
$dailyBoard         = $salesService->mergeDialerStats($dailyBoard, $leaderboard);
$dailyBoardTotals   = $salesService->dailyBoardTotals($dailyBoard);
$monthlyPerformance = $salesService->monthlyPerformanceRanking(null, $leaderboard);
$avgCallsPerSale    = $salesService->avgCallsPerSale($dailyBoardTotals);
$goal               = $salesService->monthlyGoal();
$teamBoxes          = $salesService->teamBoxes();
$allTeams           = \App\Models\SalesTeam::orderBy('name')->get();
$closerCounts       = $salesService->closerCounts();
$activeStats        = $salesService->activeClosersDialerStats($leaderboard);
$teamPie            = $salesService->teamPerformancePie();
$teamBoard          = $salesService->teamWiseClosersBoard($todayNY, $todayNY);
$mergedTeams        = $salesService->mergeDialerStatsIntoTeams($teamBoard['teams'], $leaderboard);
$teamsSummary       = $salesService->teamsSummaryTable($mergedTeams);
$teamsSummaryTotals = $salesService->teamsSummaryTotals($teamsSummary);
$clientsSummary     = $salesService->clientsSummaryTable($todayNY, $todayNY);
$carriersSummary    = $salesService->carriersSummaryTable($todayNY, $todayNY);
$monthlyPerformanceTotals = $salesService->monthlyPerformanceTotals($monthlyPerformance);

    return view('dialer-dashboard.index', [
        'filters'           => $filters,
        'groups'            => $groups,
        'stats'             => $stats,
        'trend'             => $trend,
        'leaderboard'       => $leaderboard,
        'leaderboardTotals' => $leaderboardTotals,
        'todayTotals' => $todayTotals,
        'dialerError'       => $dialerError,
        'dailyBoard'        => $dailyBoard,
        'dailyBoardTotals'  => $dailyBoardTotals,
        'monthlyPerformance' => $monthlyPerformance,
        'avgCallsPerSale' => $avgCallsPerSale,
        'goal'              => $goal,
        'canEdit'           => $canEdit,
'isCloser' => $isCloser,
        'lastSyncedAt'      => cache('dialer_last_synced_at'),
        'teamBoxes'         => $teamBoxes,
        'allTeams'          => $allTeams,
        'closerCounts' => $closerCounts,
'activeStats'  => $activeStats,
        'teamPie' => $teamPie,
        'teamsSummary'    => $teamsSummary,
        'teamsSummaryTotals' => $teamsSummaryTotals,
'clientsSummary'  => $clientsSummary,
'carriersSummary' => $carriersSummary,
'monthlyPerformanceTotals' => $monthlyPerformanceTotals,


    ]);
}

public function liveBoard(Request $request)
{
    $salesService = app(SalesService::class);
    $todayNY      = now('America/New_York')->toDateString();

    $leaderboard = [];
    try {
        $dialerApi   = app(DialerApiService::class);
        $leaderboard = $dialerApi->leaderboard([
            'from' => $todayNY,
            'to'   => $todayNY,
            'view' => 'active',
        ]);
    } catch (\Throwable $e) {
        report($e);
    }

    $dailyBoard       = $salesService->dailyBoard($todayNY);
    $dailyBoard       = $salesService->mergeDialerStats($dailyBoard, $leaderboard);
    $dailyBoard       = $salesService->sortDailyBoardByScore($dailyBoard); // Smart ranking: approved + level% + conversion
    $dailyBoardTotals = $salesService->dailyBoardTotals($dailyBoard);
    $activeStats      = $salesService->activeClosersDialerStats($leaderboard);
    $closerCounts     = $salesService->closerCounts();
    $clientsSummary   = $salesService->clientsSummaryTable($todayNY, $todayNY);
    $carriersSummary  = $salesService->carriersSummaryTable($todayNY, $todayNY);

    $teamBoard          = $salesService->teamWiseClosersBoard($todayNY, $todayNY);
    $mergedTeams        = $salesService->mergeDialerStatsIntoTeams($teamBoard['teams'], $leaderboard);
    $teamsSummary       = $salesService->teamsSummaryTable($mergedTeams);
    $teamsSummaryTotals = $salesService->teamsSummaryTotals($teamsSummary);

    $monthlyPerformance = $salesService->monthlyPerformanceRanking(null, $leaderboard);
    $monthlyPerformanceTotals = $salesService->monthlyPerformanceTotals($monthlyPerformance);

    $latestApproved = \App\Models\DailySalesEntry::with('closer')
        ->where('status', 'approved')
        ->whereDate('entry_date', $todayNY)
        ->latest('created_at')
        ->first();

    return response()->json([
        'board'                      => $dailyBoard,
        'totals'                     => $dailyBoardTotals,
        'active_stats'               => $activeStats,
        'closer_counts'              => $closerCounts,
        'clients_summary'            => $clientsSummary,
        'carriers_summary'           => $carriersSummary,
        'teams_summary'              => $teamsSummary,
        'teams_summary_totals'       => $teamsSummaryTotals,
        'monthly_performance'        => $monthlyPerformance,
        'monthly_performance_totals' => $monthlyPerformanceTotals,
        'latest_id'                  => $latestApproved->id ?? null,
        'latest_closer'              => $latestApproved->closer->name ?? null,
    ]);
}

public function publicIndex(Request $request, string $token)
{
    abort_unless(hash_equals((string) config('services.dialer.public_token'), (string) $token), 404);

    return $this->index($request);
}
   public function syncNow(Request $request)
{
    abort_unless(
        auth()->check() && in_array(auth()->user()->email, $this->editorEmails),
        403,
        'Only the assigned dialer data owner can trigger a manual update.'
    );

    \App\Jobs\SyncDialerStatsJob::dispatch();

    return back()->with('status', 'Dialer data sync queued.');
}

    /**
     * Zeros, not fake numbers — shown briefly if the dialer call fails
     * before summary() can populate real figures.
     */
    protected function fallbackStats(): array
    {
        return [
            'total_calls'           => 0,
            'calls_trend'           => 0,
            'avg_talk_time'         => '0:00:00',
            'daily_avg_talk_time'   => '0:00:00',
            'daily_trend'           => 0,
            'monthly_avg_talk_time' => '0:00:00',
            'monthly_trend'         => 0,
        ];
    }

    public function leaderboardPage(Request $request)
{
    $isCloser = auth()->check() && auth()->user()->type === 'Closer';

    $view = $request->get('view', 'active');

    $defaultFrom = $view === 'archive'
        ? now('America/New_York')->startOfMonth()->toDateString()
        : now('America/New_York')->subDays(1)->toDateString();
    $defaultTo = $view === 'archive'
        ? now('America/New_York')->endOfMonth()->toDateString()
        : now('America/New_York')->toDateString();

    $filters = [
        'view'  => $view,
        'group' => $request->get('group', 'all'),
        'from'  => $request->get('from', $defaultFrom),
        'to'    => $request->get('to', $defaultTo),
    ];

if ($isCloser) {
    $filters['view']  = 'active';
    $filters['group'] = 'all';
}
    $leaderboard       = [];
    $leaderboardTotals = ['calls' => 0, 'time' => '0:00:00', 'avg_talk_time' => '0:00:00'];
    $groups            = $this->groups;
    $dialerError       = null;

    try {
        $dialerApi   = app(DialerApiService::class);
        $leaderboard = $dialerApi->leaderboard($filters);
        $groups      = $dialerApi->leaderboardTeams($leaderboard) ?: $this->groups;

        if ($filters['group'] !== 'all') {
            $leaderboard = array_values(array_filter(
                $leaderboard,
                fn ($a) => $a['team'] === $filters['group']
            ));
        }

        $leaderboardTotals = $dialerApi->leaderboardTotals($leaderboard);
    } catch (\Throwable $e) {
        report($e);
        $dialerError = 'Could not load live data from the dialer right now.';
    }

    return view('dialer-dashboard.leaderboard', [
        'filters'           => $filters,
        'groups'            => $groups,
        'leaderboard'       => $leaderboard,
        'leaderboardTotals' => $leaderboardTotals,
        'dialerError'       => $dialerError,
        'lastSyncedAt'      => cache('dialer_last_synced_at'),
        'isCloser'          => $isCloser,
    ]);
}
}
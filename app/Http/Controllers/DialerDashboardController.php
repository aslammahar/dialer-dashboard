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
    'fazail@jsonscommunication.com',
    'ubaid.khan@jsonscommunication.com',
    'hussamjanjua@jsons.com',
    'furqankashif@jsons.com',
    'sheikh.noman@jsonscommunication.com',
    'sheikh.nouman@jsonscommunication.com',
    'm.muzamil@jsonscommunication.com',
    'taimoorjanjua@mgmt.jsonscommunications.com',
  
];
    public function index(Request $request)
{
    
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
    $todayNY = now('America/New_York')->toDateString();
    try {
        $todayLeaderboard = $dialerApi->leaderboard([
            'from' => $todayNY,
            'to'   => $todayNY,
        ]);
        $todayTotals = $dialerApi->leaderboardTotals($todayLeaderboard);
    } catch (\Throwable $e) {
        report($e);
    }

    // Live sales board + monthly goal + team boxes — pulled from our own
    // DailySalesEntry table (see SalesService), no more hardcoded numbers.
    $salesService      = app(SalesService::class);
    $monthlyPerformanceMonth = $this->selectedPerformanceMonth($request);
    $monthlyPerformanceLabel = $this->performanceMonthLabel($monthlyPerformanceMonth);

$dailyBoard         = $salesService->dailyBoard($todayNY);
$dailyBoard         = $salesService->mergeDialerStats($dailyBoard, $leaderboard);
$dailyBoardTotals   = $salesService->dailyBoardTotals($dailyBoard);
$monthlyPerformance = $salesService->monthlyPerformanceRanking($monthlyPerformanceMonth, $leaderboard);
$avgCallsPerSale    = $salesService->avgCallsPerSale($dailyBoardTotals);
$goal               = $salesService->monthlyGoal();
$goal['current_spd'] = $salesService->todayActiveCloserSpd();
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
$carriersData       = $salesService->carriersSummaryTable($todayNY, $todayNY);
$carriersSummary    = $carriersData['rows'];
$carriersClients    = $carriersData['clients'];
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
        'monthlyPerformanceMonth' => $monthlyPerformanceMonth,
        'monthlyPerformanceLabel' => $monthlyPerformanceLabel,
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
'carriersClients' => $carriersClients,
'monthlyPerformanceTotals' => $monthlyPerformanceTotals,


    ]);
}

public function liveBoard(Request $request)
{
    $salesService = app(SalesService::class);
    $todayNY      = now('America/New_York')->toDateString();
    $monthlyPerformanceMonth = $this->selectedPerformanceMonth($request);

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
    $carriersData     = $salesService->carriersSummaryTable($todayNY, $todayNY);
    $carriersSummary  = $carriersData['rows'];
    $carriersClients  = $carriersData['clients'];

    $teamBoard          = $salesService->teamWiseClosersBoard($todayNY, $todayNY);
    $mergedTeams        = $salesService->mergeDialerStatsIntoTeams($teamBoard['teams'], $leaderboard);
    $teamsSummary       = $salesService->teamsSummaryTable($mergedTeams);
    $teamsSummaryTotals = $salesService->teamsSummaryTotals($teamsSummary);

    $monthlyPerformance = $salesService->monthlyPerformanceRanking($monthlyPerformanceMonth, $leaderboard);
    $monthlyPerformanceTotals = $salesService->monthlyPerformanceTotals($monthlyPerformance);

    $latestApproved = \App\Models\DailySalesEntry::with('closer.team')
        ->where('status', 'approved')
        ->whereDate('entry_date', $todayNY)
        ->latest('updated_at')
        ->first();
    $teamOvertake = $this->teamOvertakeEvent($teamsSummary, $latestApproved);

    return response()->json([
        'board'                      => $dailyBoard,
        'totals'                     => $dailyBoardTotals,
        'active_stats'               => $activeStats,
        'closer_counts'              => $closerCounts,
        'clients_summary'            => $clientsSummary,
        'carriers_summary'           => $carriersSummary,
        'carriers_clients'           => $carriersClients,
        'teams_summary'              => $teamsSummary,
        'teams_summary_totals'       => $teamsSummaryTotals,
        'monthly_performance'        => $monthlyPerformance,
        'monthly_performance_totals' => $monthlyPerformanceTotals,
        'monthly_performance_month'  => $monthlyPerformanceMonth,
        'monthly_performance_label'  => $this->performanceMonthLabel($monthlyPerformanceMonth),
        'latest_id'                  => $latestApproved->id ?? null,
        'latest_closer'              => $latestApproved->closer->name ?? null,
        'team_overtake'              => $teamOvertake,
        'announcement'               => \Illuminate\Support\Facades\Cache::get('dashboard_announcement'),
    ]);
}

protected function selectedPerformanceMonth(Request $request): string
{
    $month = trim((string) $request->query('performance_month', ''));
    $currentMonth = now('America/New_York')->startOfMonth();

    if (! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
        return $currentMonth->format('Y-m');
    }

    try {
        $selectedMonth = \Carbon\Carbon::createFromFormat('Y-m-d', $month . '-01', 'America/New_York')->startOfMonth();
    } catch (\Throwable $e) {
        return $currentMonth->format('Y-m');
    }

    return $selectedMonth->gt($currentMonth)
        ? $currentMonth->format('Y-m')
        : $selectedMonth->format('Y-m');
}

protected function performanceMonthLabel(string $month): string
{
    return \Carbon\Carbon::createFromFormat('Y-m-d', $month . '-01', 'America/New_York')->format('F Y');
}

protected function teamOvertakeEvent(array $teamsSummary, ?\App\Models\DailySalesEntry $latestApproved): ?array
{
    if (! $latestApproved || ! $latestApproved->updated_at || $latestApproved->updated_at->lt(now()->subMinutes(10))) {
        return null;
    }

    $winnerTeam = optional(optional($latestApproved->closer)->team)->name;
    if (! $winnerTeam) {
        return null;
    }

    $winnerRow = collect($teamsSummary)->firstWhere('team', $winnerTeam);
    if (! $winnerRow) {
        return null;
    }

    $currentApproved = (int) ($winnerRow['approved'] ?? 0);
    if ($currentApproved <= 0) {
        return null;
    }

    $previousApproved = $currentApproved - 1;
    $beatenTeam = collect($teamsSummary)
        ->filter(function ($team) use ($winnerTeam, $previousApproved, $currentApproved) {
            $teamApproved = (int) ($team['approved'] ?? 0);

            return ($team['team'] ?? null) !== $winnerTeam
                && $previousApproved <= $teamApproved
                && $currentApproved > $teamApproved;
        })
        ->sortByDesc(fn ($team) => (int) ($team['approved'] ?? 0))
        ->first();

    if (! $beatenTeam) {
        return null;
    }

    return [
        'id'     => $latestApproved->id,
        'winner' => $winnerTeam,
        'loser'  => $beatenTeam['team'],
        'sales'  => $currentApproved,
    ];
}

public function broadcastAnnouncement(Request $request)
{
    abort_unless(
        auth()->check() && in_array(auth()->user()->email, $this->editorEmails),
        403,
        'Only authorized users can broadcast announcements.'
    );

    $message = $request->input('message');
    
    if ($message) {
        \Illuminate\Support\Facades\Cache::put('dashboard_announcement', $this->announcementPayload($message), now()->addMinute());
    }

    return response()->json(['success' => true]);
}

protected function announcementPayload(string $message): array
{
    $user = auth()->user();
    $email = strtolower($user->email ?? '');
    $presenters = [
        'hussamjanjua@jsons.com' => [
            'name' => 'Hussam Janjua',
            'image' => 'images/announcements/hussamjanjua.jpg',
        ],
        'sheikh.noman@jsonscommunication.com' => [
            'name' => 'Sheikh Nouman',
            'image' => 'images/announcements/sheikh_nouman.jpg',
        ],
        'sheikh.nouman@jsonscommunication.com' => [
            'name' => 'Sheikh Nouman',
            'image' => 'images/announcements/sheikh_nouman.jpg',
        ],
        'fazail@jsonscommunications.com' => [
            'name' => 'Fazail',
            'image' => 'images/announcements/fazail.jpg',
        ],
        'fazail@jsonscommunication.com' => [
            'name' => 'Fazail',
            'image' => 'images/announcements/fazail.jpg',
        ],
        'm.muzammil@jsonscommunication.com' => [
            'name' => 'M. Muzammil',
            'image' => 'images/announcements/m_muzammil.jpg',
        ],
        'm.muzamil@jsonscommunication.com' => [
            'name' => 'M. Muzammil',
            'image' => 'images/announcements/m_muzammil.jpg',
        ],
        'taimoorjanjua@mgmt.jsonscommunications.com' => [
            'name' => 'Taimoor Janjua',
            'image' => 'images/announcements/taimorjanjua.jpg',
        ],
    ];

    $presenter = $presenters[$email] ?? [
        'name' => $user->name ?? $email,
        'image' => null,
    ];

    return [
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'created_at' => now()->toIso8601String(),
        'message' => $message,
        'author_name' => $presenter['name'],
        'author_email' => $email,
        'author_image' => $presenter['image'] ? asset($presenter['image']) : null,
    ];
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

    public function scReportPage(Request $request)
    {
        $isCloser = auth()->check() && auth()->user()->type === 'Closer';

        $defaultFrom = now('America/New_York')->startOfMonth()->toDateString();
        $defaultTo   = now('America/New_York')->toDateString();

        $filters = [
            'from' => $request->get('from', $defaultFrom),
            'to'   => $request->get('to', $defaultTo),
        ];

        $salesService = app(SalesService::class);
        $scData       = $salesService->scReportData($filters);

        return view('dialer-dashboard.sc-report', [
            'filters'      => $filters,
            'reportRows'   => $scData['rows'],
            'totals'       => $scData['totals'],
            'averages'     => $scData['averages'],
            'lastSyncedAt' => cache('dialer_last_synced_at'),
            'isCloser'     => $isCloser,
        ]);
    }
}


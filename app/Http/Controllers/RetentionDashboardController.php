<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RetentionService;
use App\Models\RetentionSalesEntry;
use App\Models\SalesCloser;
use App\Models\SalesTeam;
use App\Models\SalesClient;
use App\Models\SalesCarrier;
use App\Models\ClosersAttendance;

class RetentionDashboardController extends Controller
{
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
        'aslambaig@jsons.com',
    ];

    public function index(Request $request)
    {
        $todayNY = now('America/New_York')->toDateString();
        $from = $request->get('from', $todayNY);
        $to = $request->get('to', $todayNY);

        $canEdit = auth()->check() && in_array(auth()->user()->email, $this->editorEmails);
        $isCloser = auth()->check() && auth()->user()->type === 'Closer';

        $service = app(RetentionService::class);
        $dailyBoard = $service->dailyBoardRetention($todayNY);
        $dailyBoardTotals = $service->dailyBoardRetentionTotals($dailyBoard);
        $teamsSummary = $service->teamsSummaryRetention($from, $to);
        $clientsSummary = $service->clientsSummaryRetention($from, $to);

        $retentionTeam = SalesTeam::where('name', 'LIKE', '%Retention%')->first();
        $closersQuery = SalesCloser::where('active', true);
        if ($retentionTeam) {
            $closersQuery->where('sales_team_id', $retentionTeam->id);
        }
        $closers = $closersQuery->orderBy('name')->get();
        $teams = $retentionTeam ? collect([$retentionTeam]) : SalesTeam::orderBy('name')->get();
        $clients = SalesClient::orderBy('name')->get();
        $carriers = SalesCarrier::orderBy('name')->get();

        return view('retention.index', [
            'filters' => ['from' => $from, 'to' => $to],
            'dailyBoard' => $dailyBoard,
            'dailyBoardTotals' => $dailyBoardTotals,
            'teamsSummary' => $teamsSummary,
            'clientsSummary' => $clientsSummary,
            'closers' => $closers,
            'teams' => $teams,
            'clients' => $clients,
            'carriers' => $carriers,
            'canEdit' => $canEdit,
            'isCloser' => $isCloser,
            'lastSyncedAt' => now()->format('h:i A'),
        ]);
    }

    public function report(Request $request)
    {
        $month = $request->get('month', now('America/New_York')->format('Y-m'));
        $canEdit = auth()->check() && in_array(auth()->user()->email, $this->editorEmails);
        $isCloser = auth()->check() && auth()->user()->type === 'Closer';

        $service = app(RetentionService::class);
        $reportData = $service->retentionClosersReport($month);

        return view('retention.report', [
            'month' => $month,
            'reportData' => $reportData,
            'canEdit' => $canEdit,
            'isCloser' => $isCloser,
        ]);
    }

    public function clients(Request $request)
    {
        $month = $request->get('month', now('America/New_York')->format('Y-m'));
        $canEdit = auth()->check() && in_array(auth()->user()->email, $this->editorEmails);
        $isCloser = auth()->check() && auth()->user()->type === 'Closer';

        $service = app(RetentionService::class);
        $clientsData = $service->retentionClientsReport($month);

        return view('retention.clients', [
            'month' => $month,
            'clients' => $clientsData['clients'],
            'carriers' => $clientsData['carriers'],
            'canEdit' => $canEdit,
            'isCloser' => $isCloser,
        ]);
    }

    public function createSale()
    {
        $canEdit = auth()->check() && in_array(auth()->user()->email, $this->editorEmails);
        if (!$canEdit) {
            abort(403, 'Unauthorized');
        }

        $retentionTeam = SalesTeam::where('name', 'LIKE', '%Retention%')->first();
        $closersQuery = SalesCloser::where('active', true);
        if ($retentionTeam) {
            $closersQuery->where('sales_team_id', $retentionTeam->id);
        }
        $closers = $closersQuery->orderBy('name')->get();
        $clients = SalesClient::orderBy('name')->get();
        $carriers = SalesCarrier::orderBy('name')->get();
        $recentEntries = RetentionSalesEntry::with(['closer', 'client', 'carrier'])
            ->orderBy('id', 'desc')
            ->take(15)
            ->get();

        return view('retention.create-sale', [
            'closers' => $closers,
            'clients' => $clients,
            'carriers' => $carriers,
            'recentEntries' => $recentEntries,
        ]);
    }

    public function storeSale(Request $request)
    {
        $request->validate([
            'sales_closer_id' => 'required|exists:sales_closers,id',
            'retention_type' => 'required|in:rewrite,fixed,correspondence,new_policy',
            'sale_type' => 'nullable|in:level,gi',
            'sales_client_id' => 'nullable|exists:sales_clients,id',
            'sales_carrier_id' => 'nullable|exists:sales_carriers,id',
            'avg_pre' => 'nullable|numeric|min:0',
            'entry_date' => 'required|date',
        ]);

        $closer = SalesCloser::find($request->sales_closer_id);

        RetentionSalesEntry::create([
            'entry_date' => $request->entry_date,
            'sales_closer_id' => $closer->id,
            'sales_team_id' => $closer->sales_team_id,
            'sales_client_id' => $request->sales_client_id,
            'sales_carrier_id' => $request->sales_carrier_id,
            'retention_type' => $request->retention_type,
            'status' => 'approved',
            'sale_type' => $request->sale_type,
            'avg_pre' => $request->avg_pre,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('retention.dashboard')->with('success', 'Retention sale added successfully!');
    }

    public function destroySale($id)
    {
        $canEdit = auth()->check() && in_array(auth()->user()->email, $this->editorEmails);
        if (!$canEdit) {
            abort(403, 'Unauthorized');
        }

        $entry = RetentionSalesEntry::findOrFail($id);
        $entry->delete();

        return redirect()->back()->with('status', 'Sale deleted successfully.');
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
            'group' => $request->get('group', 'Retention'), // Default to Retention for this scope
            'from'  => $request->get('from', $defaultFrom),
            'to'    => $request->get('to', $defaultTo),
        ];

        if ($isCloser) {
            $filters['view']  = 'active';
            $filters['group'] = 'Retention';
        }

        $leaderboard       = [];
        $leaderboardTotals = ['calls' => 0, 'time' => '0:00:00', 'avg_talk_time' => '0:00:00'];
        $groups            = ['QA', 'Closer', 'Avatar', 'Retention', 'Support', 'Sales'];
        $dialerError       = null;

        try {
            $dialerApi   = app(\App\Services\DialerApiService::class);
            $leaderboard = $dialerApi->leaderboard($filters);
            $groups      = $dialerApi->leaderboardTeams($leaderboard) ?: $groups;

            if ($filters['group'] !== 'all') {
                $leaderboard = array_values(array_filter(
                    $leaderboard,
                    fn ($a) => $a['team'] === $filters['group'] || stripos($a['team'], 'retention') !== false
                ));
            }

            $leaderboardTotals = $dialerApi->leaderboardTotals($leaderboard);
        } catch (\Throwable $e) {
            report($e);
            $dialerError = 'Could not load live data from the dialer right now.';
        }

        return view('retention.leaderboard', [
            'filters'           => $filters,
            'groups'            => $groups,
            'leaderboard'       => $leaderboard,
            'leaderboardTotals' => $leaderboardTotals,
            'dialerError'       => $dialerError,
            'lastSyncedAt'      => cache('dialer_last_synced_at'),
            'isCloser'          => $isCloser,
        ]);
    }

    public function resolveDateRange(Request $request)
    {
        $range = $request->get('range', 'monthly');
        $month = $request->get('month', now('America/New_York')->toDateString());
        $startDate = $request->get('start_date', now('America/New_York')->toDateString());
        $endDate   = $request->get('end_date', now('America/New_York')->toDateString());

        switch ($range) {
            case 'daily':
                $from = now('America/New_York')->toDateString();
                $to   = now('America/New_York')->toDateString();
                break;
            case 'weekly':
                $from = now('America/New_York')->startOfWeek()->toDateString();
                $to   = now('America/New_York')->toDateString();
                break;
            case 'custom':
                $from = $startDate;
                $to   = $endDate;
                break;
            default:
                $from = \Carbon\Carbon::parse($month)->startOfMonth()->toDateString();
                $to   = min(\Carbon\Carbon::parse($month)->endOfMonth(), now('America/New_York'))->toDateString();
                break;
        }

        return [$from, $to, $range, $month, $startDate, $endDate];
    }

    public function teamWise(Request $request)
    {
        [$from, $to, $range, $month, $startDate, $endDate] = $this->resolveDateRange($request);

        $salesService = app(\App\Services\SalesService::class);
        $teams = SalesTeam::orderBy('name')->get();
        // Here we build a retention specific team board instead
        $entries = RetentionSalesEntry::with(['closer.team', 'client'])
            ->where('status', 'approved')
            ->whereBetween('entry_date', [$from, $to])
            ->get();

        $allClients = SalesClient::orderBy('name')->pluck('name')->toArray();
        $grouped = $entries->groupBy(fn($e) => $e->closer->team->name ?? 'Retention');
        
        $teamData = [];
        foreach ($grouped as $teamName => $tEntries) {
            $closers = [];
            foreach ($tEntries->groupBy('sales_closer_id') as $closerId => $cEntries) {
                $closerName = $cEntries->first()->closer->name ?? 'Unknown';
                $level = $cEntries->where('sale_type', 'level')->count();
                $gi = $cEntries->where('sale_type', 'gi')->count();
                $total = $cEntries->count();
                
                $clientCounts = [];
                foreach ($allClients as $cName) {
                    $clientCounts[$cName] = $cEntries->where('client.name', $cName)->count();
                }

                $avgPreValues = $cEntries->pluck('avg_pre')->filter(fn($v) => $v > 0)->all();
                $avgPre = count($avgPreValues) > 0 ? round(array_sum($avgPreValues) / count($avgPreValues), 2) : 0;

                $closers[] = [
                    'id' => $closerId,
                    'closer' => $closerName,
                    'name' => $closerName,
                    'working_days' => 1, // simplified
                    'mtd' => $total,
                    'approved' => $total, // For the box cards
                    'spd' => $total, // simplified
                    'level' => $level,
                    'gi' => $gi,
                    'level_pct' => $total > 0 ? round(($level/$total)*100, 1) : 0,
                    'avg_pre' => $avgPre,
                    'clients' => $clientCounts,
                ];
            }

            usort($closers, fn($a, $b) => $b['mtd'] <=> $a['mtd']);

            $tLevel = array_sum(array_column($closers, 'level'));
            $tMTD = array_sum(array_column($closers, 'mtd'));

            $clientTotals = [];
            foreach ($allClients as $cName) {
                $clientTotals[$cName] = [
                    'total' => array_sum(array_column(array_column($closers, 'clients'), $cName)),
                    'avg' => count($closers) ? round(array_sum(array_column(array_column($closers, 'clients'), $cName)) / count($closers), 1) : 0
                ];
            }

            $tAvgPreArr = array_filter(array_column($closers, 'avg_pre'));
            
            $teamData[] = [
                'name' => $teamName,
                'team' => $teamName, // for team boxes
                'total_approved' => $tMTD,
                'total_closers' => count($closers),
                'closers' => $closers,
                'totals' => [
                    'mtd' => $tMTD,
                    'level' => $tLevel,
                    'gi' => array_sum(array_column($closers, 'gi')),
                    'clients' => $clientTotals,
                ],
                'averages' => [
                    'working_days' => 1,
                    'spd' => count($closers) ? round($tMTD / count($closers), 1) : 0,
                    'level_pct' => $tMTD > 0 ? round(($tLevel/$tMTD)*100, 1) : 0,
                    'avg_pre' => count($tAvgPreArr) ? round(array_sum($tAvgPreArr)/count($tAvgPreArr), 2) : 0,
                ]
            ];
        }

        $retentionTeam = SalesTeam::where('name', 'LIKE', '%Retention%')->first();
        // Closers NOT in retention team – available to add
        $availableClosers = SalesCloser::where('active', true)
            ->where(function($q) use ($retentionTeam) {
                if ($retentionTeam) {
                    $q->where('sales_team_id', '!=', $retentionTeam->id)
                      ->orWhereNull('sales_team_id');
                }
            })
            ->orderBy('name')
            ->get();

        // Current Retention team closers (for the remove strip)
        $retentionClosers = $retentionTeam
            ? SalesCloser::where('active', true)->where('sales_team_id', $retentionTeam->id)->orderBy('name')->get()
            : collect();

        return view('retention.reports.team-wise', [
            'month'   => $month,
            'range'   => $range,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'teams'   => $teamData,
            'teamBoxes' => $teamData,
            'clients' => $allClients,
            'canEdit' => auth()->check() && in_array(auth()->user()->email, $this->editorEmails),
            'allTeams' => $teams,
            'availableClosers' => $availableClosers,
            'retentionClosers' => $retentionClosers,
        ]);
    }

    public function addCloser(Request $request)
    {
        $canEdit = auth()->check() && in_array(auth()->user()->email, $this->editorEmails);
        if (!$canEdit) { abort(403, 'Unauthorized'); }

        $request->validate(['sales_closer_id' => 'required|exists:sales_closers,id']);

        $retentionTeam = SalesTeam::where('name', 'LIKE', '%Retention%')->first();
        if (!$retentionTeam) {
            return redirect()->back()->with('error', 'Retention team not found.');
        }

        $closer = SalesCloser::findOrFail($request->sales_closer_id);
        $closer->sales_team_id = $retentionTeam->id;
        $closer->save();

        return redirect()->back()->with('success', $closer->name . ' has been added to the Retention team.');
    }

    public function removeCloser(Request $request)
    {
        $canEdit = auth()->check() && in_array(auth()->user()->email, $this->editorEmails);
        if (!$canEdit) { abort(403, 'Unauthorized'); }

        $request->validate(['sales_closer_id' => 'required|exists:sales_closers,id']);

        $closer = SalesCloser::findOrFail($request->sales_closer_id);
        $closer->sales_team_id = null;
        $closer->save();

        return redirect()->back()->with('success', $closer->name . ' has been removed from the Retention team.');
    }

    public function clientWise(Request $request)
    {
        [$from, $to, $range, $month, $startDate, $endDate] = $this->resolveDateRange($request);
        
        $entries = RetentionSalesEntry::with(['client', 'closer.team', 'carrier'])
            ->whereBetween('entry_date', [$from, $to])
            ->where('status', 'approved')
            ->get();

        $allClients = SalesClient::orderBy('name')->get();
        // For columns we just want client names
        $clientNames = $allClients->pluck('name')->toArray();

        $groupped = $entries->groupBy('sales_closer_id');
        
        $board = [];
        $totals = ['approved' => 0, 'level' => 0, 'gi' => 0];
        $clientTotals = [];
        foreach ($clientNames as $c) {
            $clientTotals[$c] = 0;
        }
        
        foreach ($groupped as $closerId => $cEntries) {
            $closer = $cEntries->first()->closer;
            $approved = $cEntries->count();
            $level = $cEntries->where('sale_type', 'level')->count();
            $gi = $cEntries->where('sale_type', 'gi')->count();
            
            $cClientCounts = [];
            foreach ($clientNames as $cName) {
                $c = $cEntries->where('client.name', $cName)->count();
                $cClientCounts[$cName] = $c;
                $clientTotals[$cName] += $c;
            }

            $board[] = [
                'closer' => $closer->name ?? 'Unknown',
                'team' => $closer->team->name ?? 'Retention',
                'approved' => $approved,
                'level' => $level,
                'gi' => $gi,
                'level_pct' => $approved > 0 ? round(($level/$approved)*100, 1) : 0,
                'clients' => $cClientCounts
            ];

            $totals['approved'] += $approved;
            $totals['level'] += $level;
            $totals['gi'] += $gi;
        }

        usort($board, fn($a, $b) => $b['approved'] <=> $a['approved']);
        $totals['level_pct'] = $totals['approved'] > 0 ? round(($totals['level']/$totals['approved'])*100, 1) : 0;
        $totals['clients'] = $clientTotals;

        return view('retention.reports.client-wise', [
            'month'   => $month,
            'range'   => $range,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'board'   => $board,
            'clients' => $clientNames,
            'totals'  => $totals,
            'clientBoxes' => [] // Assuming not heavily used or mock empty
        ]);
    }

    public function carrierWise(Request $request)
    {
        [$from, $to, $range, $month, $startDate, $endDate] = $this->resolveDateRange($request);
        
        $entries = RetentionSalesEntry::with(['client', 'closer.team', 'carrier'])
            ->whereBetween('entry_date', [$from, $to])
            ->where('status', 'approved')
            ->get();

        $allCarriers = SalesCarrier::orderBy('name')->get();
        // Limit active carriers for the view or passing all
        $activeCarriers = $allCarriers->filter(function($car) use ($entries) {
            return $entries->where('carrier.name', $car->name)->count() > 0;
        })->pluck('name')->toArray();

        $clientNames = SalesClient::orderBy('name')->pluck('name')->toArray();
        $groupped = $entries->groupBy('sales_closer_id');
        
        $board = [];
        $totals = ['approved' => 0];
        foreach ($activeCarriers as $c) {
            $totals[$c] = 0;
        }
        foreach ($clientNames as $c) {
            $totals["client_$c"] = 0;
        }
        
        foreach ($groupped as $closerId => $cEntries) {
            $closer = $cEntries->first()->closer;
            $approved = $cEntries->count();
            
            $cCarrierCounts = [];
            foreach ($activeCarriers as $carName) {
                $c = $cEntries->where('carrier.name', $carName)->count();
                $cCarrierCounts[$carName] = $c;
                $totals[$carName] += $c;
            }
            
            $cClientCounts = [];
            foreach ($clientNames as $cName) {
                $c = $cEntries->where('client.name', $cName)->count();
                $cClientCounts[$cName] = $c;
                $totals["client_$cName"] += $c;
            }

            $board[] = [
                'closer' => $closer->name ?? 'Unknown',
                'team' => $closer->team->name ?? 'Retention',
                'approved' => $approved,
                'carriers' => $cCarrierCounts,
                'clients' => $cClientCounts
            ];

            $totals['approved'] += $approved;
        }

        usort($board, fn($a, $b) => $b['approved'] <=> $a['approved']);

        return view('retention.reports.carrier-wise', [
            'month'   => $month,
            'range'   => $range,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'board'   => $board,
            'carriers' => $activeCarriers,
            'clients' => $clientNames,
            'totals'  => $totals,
        ]);
    }

    /**
     * Retention Closers Attendance View
     */
    public function attendanceIndex(Request $request)
    {
        $date = $request->get('date', now('America/New_York')->toDateString());
        $retentionTeam = SalesTeam::where('name', 'LIKE', '%Retention%')->first();

        $closersQuery = SalesCloser::where('active', true);
        if ($retentionTeam) {
            $closersQuery->where('sales_team_id', $retentionTeam->id);
        }
        $closers = $closersQuery->orderBy('name')->get();

        $existing = ClosersAttendance::whereDate('attendance_date', $date)
            ->pluck('status', 'sales_closer_id');

        $startOfMonth = now('America/New_York')->startOfMonth()->toDateString();
        $endOfMonth = now('America/New_York')->toDateString();
        $monthAttendances = ClosersAttendance::whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->whereIn('sales_closer_id', $closers->pluck('id'))
            ->get();

        $monthlySummary = [];
        foreach ($closers as $closer) {
            $cAtt = $monthAttendances->where('sales_closer_id', $closer->id);
            $present = $cAtt->where('status', 'present')->unique('attendance_date')->count();
            $halfDay = $cAtt->where('status', 'half_day')->unique('attendance_date')->count();
            $absent = $cAtt->where('status', 'absent')->unique('attendance_date')->count();
            $leave = $cAtt->where('status', 'leave')->unique('attendance_date')->count();

            $weightedDays = $present + ($halfDay * 0.5);

            $monthlySummary[] = [
                'closer_id' => $closer->id,
                'closer_name' => $closer->name,
                'present' => $present,
                'half_day' => $halfDay,
                'absent' => $absent,
                'leave' => $leave,
                'weighted_days' => $weightedDays,
            ];
        }

        $canEdit = auth()->check() && in_array(auth()->user()->email, $this->editorEmails);
        $isCloser = auth()->check() && auth()->user()->type === 'Closer';

        return view('retention.attendance', [
            'date' => $date,
            'closers' => $closers,
            'existing' => $existing,
            'monthlySummary' => $monthlySummary,
            'canEdit' => $canEdit,
            'isCloser' => $isCloser,
        ]);
    }

    /**
     * Store Retention Closers Attendance
     */
    public function attendanceStore(Request $request)
    {
        $canEdit = auth()->check() && in_array(auth()->user()->email, $this->editorEmails);
        if (!$canEdit) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'date' => ['required', 'date'],
            'status' => ['required', 'array'],
            'status.*' => ['required', 'in:present,absent,leave,half_day'],
        ]);

        foreach ($data['status'] as $closerId => $status) {
            ClosersAttendance::updateOrCreate(
                ['sales_closer_id' => $closerId, 'attendance_date' => $data['date']],
                ['status' => $status, 'marked_by' => auth()->id()]
            );
        }

        return redirect()
            ->route('retention.attendance.index', ['date' => $data['date']])
            ->with('success', 'Retention attendance marked for ' . $data['date'] . '.');
    }
}

<?php

namespace App\Services;

use App\Models\DailySalesEntry;
use App\Models\SalesTarget;
use Carbon\Carbon;

class SalesService
{
    /**
     * Per-closer breakdown for a given date (default: today) — Approved,
     * Pending, Level, GI, Avg Pre — mirrors the Daily Board sheet columns.
     */
public function dailyBoard(?string $date = null): array
{
    $date = $date ?? now('America/New_York')->toDateString();

    $entries = DailySalesEntry::with(['closer', 'team'])
        ->whereDate('entry_date', $date)
        ->get();

    return $entries
        ->groupBy('sales_closer_id')
        ->map(function ($rows) {
            $closer = $rows->first()->closer;
            $approved = $rows->where('status', 'approved');
            $approvedCount = $approved->count();
            $levelCount = $approved->where('sale_type', 'level')->count();

           $team = $closer->team->name ?? '-';

            return [
                'closer'               => $closer->name ?? 'Unknown',
                'team'                 => $team,
                'approved'             => $approvedCount,
                'pending'              => $rows->where('status', 'pending')->count(),
                'level'                => $levelCount,
                'gi'                   => $approved->where('sale_type', 'gi')->count(),
                'level_pct'            => $approvedCount > 0 ? round(($levelCount / $approvedCount) * 100, 1) : 0,
                'avg_pre'              => $approved->avg('avg_pre') ? round($approved->avg('avg_pre'), 2) : 0,
                'time_since_last_sale' => $this->timeSinceLastSale($closer->id),
            ];
        })
        ->sortByDesc('approved')
        ->values()
        ->all();
}

/**
 * Totals row for the Daily Board — sums Approved/Pending/Level/GI across
 * all closers, plus overall Level% and Avg Pre for that day.
 */
public function dailyBoardTotals(array $board): array
{
    $rowsWithSales = array_filter($board, fn ($row) => $row['approved'] > 0);
    $avgPreValues  = array_column($rowsWithSales, 'avg_pre');

    $totalApproved = array_sum(array_column($board, 'approved'));
    $totalLevel    = array_sum(array_column($board, 'level'));
    $totalCalls    = array_sum(array_column($board, 'calls'));
    $totalClosers  = count(array_filter($board, fn ($row) => (($row['approved'] ?? 0) + ($row['pending'] ?? 0)) > 0));

    // Weighted average talk time — weighted by each closer's call count,
    // not a plain average of averages (which skews toward low-call agents).
    $weightedSecondsSum = 0;
    $callsCounted = 0;
    foreach ($board as $row) {
        $calls = $row['calls'] ?? 0;
        if ($calls > 0 && isset($row['avg_talk_time']) && $row['avg_talk_time'] !== '-') {
            $weightedSecondsSum += $this->hmsToSeconds($row['avg_talk_time']) * $calls;
            $callsCounted += $calls;
        }
    }
    $avgTalkSeconds = $callsCounted > 0 ? intdiv($weightedSecondsSum, $callsCounted) : 0;

    return [
        'approved'      => $totalApproved,
        'closers'       => $totalClosers,
        'pending'       => array_sum(array_column($board, 'pending')),
        'level'         => $totalLevel,
        'gi'            => array_sum(array_column($board, 'gi')),
        'level_pct'     => $totalApproved > 0 ? round(($totalLevel / $totalApproved) * 100, 1) : 0,
        'avg_pre'       => count($avgPreValues) > 0 ? round(array_sum($avgPreValues) / count($avgPreValues), 2) : 0,
        'calls'         => $totalCalls,
        'avg_talk_time' => $this->secondsToHms($avgTalkSeconds),
    ];
}


/**
 * Dashboard "highest rank" strip — who has the most approved sales today,
 * who made the most calls today, and whose average talk time is longest.
 */
public function topPerformersToday(array $dailyBoard): array
{
    if (empty($dailyBoard)) {
        return ['top_sales' => null, 'top_calls' => null, 'top_talktime' => null];
    }

    $collection = collect($dailyBoard);

    $topSales = $collection->sortByDesc('approved')->first();
    $topCalls = $collection->sortByDesc(fn ($r) => $r['calls'] ?? 0)->first();
    $topTalk  = $collection->sortByDesc(fn ($r) => ($r['avg_talk_time'] ?? '-') !== '-'
        ? $this->hmsToSeconds($r['avg_talk_time'])
        : -1
    )->first();

    // Don't show a "top" if the leading value is actually zero (no real data)
    return [
        'top_sales'    => ($topSales['approved'] ?? 0) > 0 ? $topSales : null,
        'top_calls'    => ($topCalls['calls'] ?? 0) > 0 ? $topCalls : null,
        'top_talktime' => (($topTalk['avg_talk_time'] ?? '-') !== '-') ? $topTalk : null,
    ];
}

/**
 * Full monthly per-closer report — mirrors the "Closers" sheet tab
 * (WorkingDays, MTD, SPD, Level, GI, Level%, Avg Pre, Avatar/Jcs Calls,
 * Conversion%, Avg Talk Time), plus a combined performance score.
 */
/**
 * Full monthly per-closer report — mirrors the "Closers" sheet tab
 * (WorkingDays, MTD, SPD, Level, GI, Level%, Avg Pre, Avatar/Jcs Calls,
 * Conversion (calls per sale — lower is better), Avg Talk Time),
 * plus a combined performance score.
 */
public function monthlyPerformanceRanking(?string $month = null, array $dialerLeaderboard = []): array
{
    $monthStart = $month ? Carbon::parse($month, 'America/New_York')->startOfMonth() : now('America/New_York')->startOfMonth();
    $monthEnd   = now('America/New_York')->min($monthStart->copy()->endOfMonth());

    $entries = DailySalesEntry::with(['closer.team'])
        ->whereBetween('entry_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
        ->get();

    // Real attendance-based working days per closer this month — NOT
    // derived from sales entries (a closer can be present 4 days but only
    // sell on 1-2 of them; "Working Days" must reflect attendance).
    $workingDaysByCloser = \App\Models\ClosersAttendance::where('status', 'present')
        ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
        ->get()
        ->groupBy('sales_closer_id')
        ->map(fn ($rows) => $rows->count())
        ->all();

    $exactLookup = [];
    $firstNameLookup = [];
    foreach ($dialerLeaderboard as $agent) {
        $fullName = strtolower(trim($agent['name']));
        $firstName = strtolower(trim(explode(' ', $agent['name'])[0]));
        $exactLookup[$fullName] = $agent;
        if (! isset($firstNameLookup[$firstName]) || $agent['calls'] > $firstNameLookup[$firstName]['calls']) {
            $firstNameLookup[$firstName] = $agent;
        }
    }

    $rows = $entries
        ->groupBy('sales_closer_id')
        ->map(function ($rows) use ($exactLookup, $firstNameLookup, $workingDaysByCloser) {
            $closer = $rows->first()->closer;

            // Skip orphaned entries where closer has been deleted
            if (! $closer) {
                return null;
            }

            $approved = $rows->where('status', 'approved');
            $levelCount = $approved->where('sale_type', 'level')->count();
            $mtd = $approved->count();

            // Attendance-based working days — falls back to 0 if no
            // attendance was ever marked for this closer this month.
            $workingDays = $workingDaysByCloser[$closer->id] ?? 0;

            $closerName = strtolower(trim($closer->name ?? ''));
            $firstName = strtolower(trim(explode(' ', $closer->name ?? '')[0]));
            $match = $exactLookup[$closerName] ?? $firstNameLookup[$firstName] ?? null;
            $calls = $match['calls'] ?? 0;

            $clientBreakdown = [];
            foreach ($approved as $entry) {
                $cName = optional($entry->client)->name;
                if ($cName) {
                    $clientBreakdown[$cName] = ($clientBreakdown[$cName] ?? 0) + 1;
                }
            }

            return [
                'closer'           => $closer->name ?? 'Unknown',
                'team'             => $closer->team->name ?? '-',
                'working_days'     => $workingDays,
                'mtd'              => $mtd,
                'spd'              => $workingDays > 0 ? round($mtd / $workingDays, 1) : 0,
                'level'            => $levelCount,
                'gi'               => $approved->where('sale_type', 'gi')->count(),
                'level_pct'        => $mtd > 0 ? round(($levelCount / $mtd) * 100) : 0,
                'client_breakdown' => $clientBreakdown,
                'avg_pre'          => $approved->avg('avg_pre') ? round($approved->avg('avg_pre'), 2) : 0,
                'calls'            => $calls,
                'conversion'       => $mtd > 0 ? round($calls / $mtd, 2) : 0,
                'avg_talk_time'    => $match['avg_talk_time'] ?? '-',
            ];
        })
        ->filter()  // Remove null entries (orphaned closers)
        ->values()
        ->all();

    if (empty($rows)) {
        return [];
    }

    $maxMtd      = max(array_column($rows, 'mtd')) ?: 1;
    $maxLevelPct = 100;

    $positiveConversions = array_filter(array_column($rows, 'conversion'), fn ($c) => $c > 0);
    $minConversion = ! empty($positiveConversions) ? min($positiveConversions) : 0;

    foreach ($rows as &$row) {
        $salesScore = $row['mtd'] / $maxMtd;
        $levelScore = $row['level_pct'] / $maxLevelPct;
        $conversionScore = $row['conversion'] > 0 && $minConversion > 0
            ? $minConversion / $row['conversion']
            : 0;

        $row['score'] = round(
            ($salesScore * 0.5) + ($conversionScore * 0.35) + ($levelScore * 0.15),
            4
        );
    }
    unset($row);

  usort($rows, function ($a, $b) {
    // MTD (sales count) hamesha pehli priority — jiski zyada sales, wo upar
    if ($b['mtd'] !== $a['mtd']) {
        return $b['mtd'] <=> $a['mtd'];
    }
    // Barabar MTD walon mein purana blended score tie-breaker ka kaam karega
    return $b['score'] <=> $a['score'];
});

    foreach ($rows as $i => &$row) {
        $row['rank'] = $i + 1;
    }
    unset($row);

    return $rows;
}


public function monthlyPerformanceTotals(array $rows): array
{
    $count = count($rows);
    if ($count === 0) {
        return [
            'working_days_avg' => 0, 'mtd_total' => 0, 'mtd_avg' => 0,
            'spd_avg' => 0, 'level_total' => 0, 'level_avg' => 0,
            'gi_total' => 0, 'gi_avg' => 0, 'level_pct_avg' => 0,
            'avg_pre_avg' => 0, 'calls_total' => 0, 'calls_avg' => 0,
            'conversion_avg' => 0, 'avg_talk_time_avg' => '0:00:00',
        ];
    }

    $mtdTotal   = array_sum(array_column($rows, 'mtd'));
    $levelTotal = array_sum(array_column($rows, 'level'));
    $giTotal    = array_sum(array_column($rows, 'gi'));
    $callsTotal = array_sum(array_column($rows, 'calls'));

    $weightedSeconds = 0;
    $callsCounted = 0;
    foreach ($rows as $r) {
        $c = $r['calls'] ?? 0;
        if ($c > 0 && ($r['avg_talk_time'] ?? '-') !== '-') {
            $weightedSeconds += $this->hmsToSeconds($r['avg_talk_time']) * $c;
            $callsCounted += $c;
        }
    }
    $avgTalkSeconds = $callsCounted > 0 ? intdiv($weightedSeconds, $callsCounted) : 0;

    return [
        'working_days_avg' => round(array_sum(array_column($rows, 'working_days')) / $count, 1),
        'mtd_total'        => $mtdTotal,
        'mtd_avg'          => round($mtdTotal / $count, 1),
        'spd_avg'          => round(array_sum(array_column($rows, 'spd')) / $count, 1),
        'level_total'      => $levelTotal,
        'level_avg'        => round($levelTotal / $count, 1),
        'gi_total'         => $giTotal,
        'gi_avg'           => round($giTotal / $count, 1),
        'level_pct_avg'    => $mtdTotal > 0 ? round(($levelTotal / $mtdTotal) * 100) : 0,
        'avg_pre_avg'      => round(array_sum(array_column($rows, 'avg_pre')) / $count, 2),
        'calls_total'      => $callsTotal,
        'calls_avg'        => round($callsTotal / $count, 1),
        'conversion_avg'   => $mtdTotal > 0 ? round($callsTotal / $mtdTotal, 2) : 0,
        'avg_talk_time_avg'=> $this->secondsToHms($avgTalkSeconds),
    ];
}
public function rankedPerformersToday(array $dailyBoard): array
{
    $rows = collect($dailyBoard)
        ->filter(fn ($r) => ($r['approved'] ?? 0) > 0 || ($r['calls'] ?? 0) > 0)
        ->sortByDesc('approved')
        ->values()
        ->all();

    return array_values(array_map(function ($row, $i) {
        $row['rank'] = $i + 1;
        return $row;
    }, $rows, array_keys($rows)));
}
    /**
     * Month-to-date approved sales + SPD (sales-per-day) progress against
     * this month's stored target.
     */
    public function monthlyGoal(): array
{
    $monthStart = now('America/New_York')->startOfMonth();
    $target = SalesTarget::whereDate('month', $monthStart->toDateString())->first()
        ?? new SalesTarget([
            'spd_target'         => 2.0,
            'monthly_spd_target' => 2.5,
            'raw_target'         => 40,
            'reward_headline'    => 'the whole team earns a trip',
            'milestone_1_label'  => 'Movie Night for Closers',
            'milestone_2_label'  => 'Cash Bonus',
            'milestone_2_amount' => '100k',
            'milestone_3_label'  => 'Team Trip',
        ]);

    $approvedThisMonth = DailySalesEntry::where('status', 'approved')
        ->whereBetween('entry_date', [$monthStart->toDateString(), now('America/New_York')->toDateString()])
        ->count();

    $daysElapsed   = max($monthStart->diffInDays(now()) + 1, 1);
    $activeClosers = max(\App\Models\SalesCloser::where('active', true)->count(), 1);

    $currentSpd = round($approvedThisMonth / $daysElapsed / $activeClosers, 2);
    $pct        = $target->raw_target > 0
        ? (int) round(($approvedThisMonth / $target->raw_target) * 100)
        : 0;

    return [
        'approved_mtd'        => $approvedThisMonth,
        'raw_target'          => $target->raw_target,
        'pct'                 => min($pct, 100),
        'current_spd'         => $currentSpd,
        'spd_target'          => (float) $target->spd_target,
        'monthly_spd_target'  => (float) $target->monthly_spd_target,
        'reward_headline'     => $target->reward_headline,
        'milestone_1_label'   => $target->milestone_1_label,
        'milestone_2_label'   => $target->milestone_2_label,
        'milestone_2_amount'  => $target->milestone_2_amount,
        'milestone_3_label'   => $target->milestone_3_label,
    ];
}
/**
 * Monthly per-closer summary — mirrors the Daily Board sheet's
 * Approved/Level/GI/Level%/Avg Pre/SPD/MTD SPD columns.
 */
public function monthlyClosersReport(?string $month = null): array
{
    $monthStart = $month ? Carbon::parse($month)->startOfMonth() : now()->startOfMonth();
    $monthEnd   = $monthStart->copy()->min(now())->endOfDay();
    $daysElapsed = max($monthStart->diffInDays(now()->min($monthStart->copy()->endOfMonth())) + 1, 1);

    $entries = DailySalesEntry::with(['closer.team'])
        ->whereBetween('entry_date', [$monthStart->toDateString(), now()->toDateString()])
        ->get();

    return $entries
        ->groupBy('sales_closer_id')
        ->map(function ($rows) use ($daysElapsed) {
            $closer = $rows->first()->closer;
            $approved = $rows->where('status', 'approved');
            $levelCount = $approved->where('sale_type', 'level')->count();

            return [
                'closer'     => $closer->name ?? 'Unknown',
                'team'       => $closer->team->name ?? '-',
                'approved'   => $approved->count(),
                'level'      => $levelCount,
                'gi'         => $approved->where('sale_type', 'gi')->count(),
                'level_pct'  => $approved->count() > 0 ? round(($levelCount / $approved->count()) * 100) : 0,
                'avg_pre'    => $approved->avg('avg_pre') ? round($approved->avg('avg_pre'), 2) : 0,
                'mtd_spd'    => round($approved->count() / $daysElapsed, 2),
            ];
        })
        ->sortByDesc('approved')
        ->values()
        ->all();
}

/**
 * Monthly per-client (D6/PM7/FCF/UL5) breakdown — Approved/Level/GI/Target/Left/Avg Pre.
 */
public function monthlyClientsReport(?string $from = null, ?string $to = null): array
{
    $from = $from ?? now('America/New_York')->startOfMonth()->toDateString();
    $to   = $to ?? now('America/New_York')->toDateString();

    $entries = DailySalesEntry::with('client')
        ->whereBetween('entry_date', [$from, $to])
        ->whereNotNull('sales_client_id')
        ->get();

    return $entries
        ->groupBy('sales_client_id')
        ->map(function ($rows) {
            $client = $rows->first()->client;
            $approved = $rows->where('status', 'approved');
            $levelCount = $approved->where('sale_type', 'level')->count();
            $mtd = $approved->count();

            return [
                'client'    => $client->name ?? 'Unknown',
                'approved'  => $mtd,
                'level'     => $levelCount,
                'gi'        => $approved->where('sale_type', 'gi')->count(),
                'level_pct' => $mtd > 0 ? round(($levelCount / $mtd) * 100) : 0,
                'avg_pre'   => $approved->avg('avg_pre') ? round($approved->avg('avg_pre'), 2) : 0,
            ];
        })
        ->sortByDesc('approved')
        ->values()
        ->all();
}

/**
 * Team-wise monthly rollup — mirrors the sheet's "Teams" summary table.
 */
public function monthlyTeamsReport(?string $month = null): array
{
    $monthStart = $month ? Carbon::parse($month, 'America/New_York')->startOfMonth() : now('America/New_York')->startOfMonth();

    $entries = DailySalesEntry::with('team')
        ->whereBetween('entry_date', [$monthStart->toDateString(), now('America/New_York')->toDateString()])
        ->whereNotNull('sales_team_id')
        ->get();

    return $entries
        ->groupBy('sales_team_id')
        ->map(function ($rows) {
            $team = $rows->first()->team;
            $approved = $rows->where('status', 'approved');
            $levelCount = $approved->where('sale_type', 'level')->count();
            $closers = $rows->pluck('sales_closer_id')->unique()->count();

            return [
                'team'      => $team->name ?? 'Unknown',
                'closers'   => $closers,
                'approved'  => $approved->count(),
                'level'     => $levelCount,
                'gi'        => $approved->where('sale_type', 'gi')->count(),
                'level_pct' => $approved->count() > 0 ? round(($levelCount / $approved->count()) * 100) : 0,
            ];
        })
        ->sortByDesc('approved')
        ->values()
        ->all();
}
/**
 * Team Wise Closers board — one block per team, each closer's monthly
 * stats (mirrors the "Team Wise Closers" sheet, minus the call-log
 * columns which need per-closer dialer name-matching).
 */
public function teamWiseClosersBoard(?string $from = null, ?string $to = null): array
{
    $from = $from ?? now('America/New_York')->startOfMonth()->toDateString();
    $to   = $to ?? now('America/New_York')->toDateString();

    $presentCloserIds = \App\Models\ClosersAttendance::where('status', 'present')
        ->whereBetween('attendance_date', [$from, $to])
        ->pluck('sales_closer_id')
        ->unique()
        ->toArray();

    // Attendance-based working days per closer (same as monthlyPerformanceRanking)
    $workingDaysByCloser = \App\Models\ClosersAttendance::where('status', 'present')
        ->whereBetween('attendance_date', [$from, $to])
        ->get()
        ->groupBy('sales_closer_id')
        ->map(fn ($rows) => $rows->count())
        ->all();

    $entries = DailySalesEntry::with(['closer.team', 'team', 'client'])
        ->whereBetween('entry_date', [$from, $to])
        ->get();

    $clientNames = \App\Models\SalesClient::orderBy('name')->pluck('name')->all();

    $teams = $entries
        ->filter(fn ($e) => $e->closer !== null)
        ->groupBy(fn ($e) => $e->closer->team->name ?? 'Unassigned')
        ->map(function ($teamEntries, $teamName) use ($clientNames, $presentCloserIds, $workingDaysByCloser) {
            $closers = $teamEntries
                ->groupBy('sales_closer_id')
                ->map(function ($rows) use ($clientNames, $workingDaysByCloser) {
                    $closer = $rows->first()->closer;

                    if (! $closer) {
                        return null;
                    }

                    $approved = $rows->where('status', 'approved');
                    $levelCount = $approved->where('sale_type', 'level')->count();
                    $workingDays = $workingDaysByCloser[$closer->id] ?? 0;
                    $mtd = $approved->count();

                    $clientCounts = [];
                    foreach ($clientNames as $clientName) {
                        $clientCounts[$clientName] = $approved
                            ->filter(fn ($e) => optional($e->client)->name === $clientName)
                            ->count();
                    }

                    return [
                        'closer'       => $closer->name ?? 'Unknown',
                        'working_days' => $workingDays,
                        'mtd'          => $mtd,
                        'spd'          => $workingDays > 0 ? round($mtd / $workingDays, 1) : 0,
                        'level'        => $levelCount,
                        'gi'           => $approved->where('sale_type', 'gi')->count(),
                        'level_pct'    => $mtd > 0 ? round(($levelCount / $mtd) * 100) : 0,
                        'clients'      => $clientCounts,
                        'avg_pre'      => $approved->avg('avg_pre') ? round($approved->avg('avg_pre'), 2) : 0,
                    ];
                })
                ->filter()
                ->sortByDesc('mtd')
                ->values()
                ->all();

            $count = count($closers);
            $sum = fn ($key) => array_sum(array_column($closers, $key));

            $totals = [
                'mtd'   => $sum('mtd'),
                'level' => $sum('level'),
                'gi'    => $sum('gi'),
                'clients' => collect($clientNames)->mapWithKeys(function ($c) use ($closers) {
                    $total = array_sum(array_map(fn ($cl) => $cl['clients'][$c] ?? 0, $closers));
                    $closersWithSales = count(array_filter($closers, fn ($cl) => ($cl['clients'][$c] ?? 0) > 0));

                    return [$c => [
                        'total' => $total,
                        'avg'   => $closersWithSales > 0 ? round($total / $closersWithSales, 1) : 0,
                    ]];
                })->all(),
            ];

            $teamModel = \App\Models\SalesTeam::where('name', $teamName)->first();
            $totalTeamClosers = $count;
            if ($teamModel) {
                $totalTeamClosers = \App\Models\SalesCloser::where('sales_team_id', $teamModel->id)
                    ->whereIn('id', $presentCloserIds)
                    ->count();
            }
            if ($totalTeamClosers === 0) {
                $totalTeamClosers = max($count, 1);
            }

            $avgWorkingDays = $count > 0 ? round($sum('working_days') / $count, 1) : 0;

            $averages = [
                'total_closers'=> $totalTeamClosers,
                'working_days' => $avgWorkingDays,
                'mtd'          => $count > 0 ? round($sum('mtd') / $count, 1) : 0,
                'spd'          => $avgWorkingDays > 0 ? round($totals['mtd'] / $avgWorkingDays, 2) : 0,
                'level'        => $count > 0 ? round($sum('level') / $count, 1) : 0,
                'gi'           => $count > 0 ? round($sum('gi') / $count, 1) : 0,
                'level_pct'    => $totals['mtd'] > 0 ? round(($totals['level'] / $totals['mtd']) * 100, 2) : 0,
                'avg_pre'      => $count > 0 ? round(array_sum(array_column($closers, 'avg_pre')) / $count) : 0,
            ];

            return [
                'name'          => $teamName,
                'total_closers' => $totalTeamClosers,
                'closers'       => $closers,
                'totals'        => $totals,
                'averages'      => $averages,
            ];
        })
        ->sortByDesc(fn ($t) => $t['totals']['mtd'])
        ->values()
        ->all();

    return [
        'teams'   => $teams,
        'clients' => $clientNames,
    ];
}
/**
 * Formats seconds as H:M:S (shared helper — same logic as DialerApiService).
 */
protected function secondsToHms(int $seconds): string
{
    $h = intdiv($seconds, 3600);
    $m = intdiv($seconds % 3600, 60);
    $s = $seconds % 60;

    return sprintf('%d:%02d:%02d', $h, $m, $s);
}

/**
 * Time elapsed since this closer's most recent APPROVED sale (any date,
 * not just today) — updates live as new approved sales come in.
 */
protected function timeSinceLastSale(int $closerId): ?string
{
    $last = DailySalesEntry::where('sales_closer_id', $closerId)
        ->where('status', 'approved')
        ->latest('created_at')
        ->first();

    if (! $last) {
        return null;
    }

    return $this->secondsToHms($last->created_at->diffInSeconds(now()));
}

/**
 * Small per-team leaderboard cards for the dashboard — every ACTIVE closer
 * grouped by their CURRENT assigned team (from sales_closers.sales_team_id,
 * managed via /sales-closers), with this month's approved count + exact
 * level % (not the old binary 0/100 shortcut).
 */
public function teamBoxes(?string $month = null): array
{
    $monthStart = $month ? Carbon::parse($month, 'America/New_York')->startOfMonth() : now('America/New_York')->startOfMonth();

    $approvedEntries = DailySalesEntry::where('status', 'approved')
        ->whereBetween('entry_date', [$monthStart->toDateString(), now('America/New_York')->toDateString()])
        ->get();

    $closers = \App\Models\SalesCloser::with('team')->where('active', true)->orderBy('name')->get();

    return $closers
        ->groupBy(fn ($c) => $c->team->name ?? 'Unassigned')
        ->map(function ($teamClosers, $teamName) use ($approvedEntries) {
            $closerStats = $teamClosers->map(function ($closer) use ($approvedEntries) {
                $closerEntries = $approvedEntries->where('sales_closer_id', $closer->id);
                $count = $closerEntries->count();
                $level = $closerEntries->where('sale_type', 'level')->count();

                return [
                    'id'        => $closer->id,
                    'name'      => $closer->name,
                    'approved'  => $count,
                    'level_pct' => $count > 0 ? round(($level / $count) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('approved')
            ->values()
            ->all();

            return [
                'team'           => $teamName,
                'total_approved' => array_sum(array_column($closerStats, 'approved')),
                'closers'        => $closerStats,
            ];
        })
        ->sortByDesc('total_approved')
        ->values()
        ->all();
}

/**
 * Merges live VICIdial leaderboard stats (Calls, Avg Talk Time) into the
 * daily sales board rows. VICIdial gives full names ("Richard Miles")
 * while our closer list often has only a first name ("Richard"), so we
 * match on: exact name first, then first-name-only, then "contains".
 */
public function mergeDialerStats(array $board, array $dialerLeaderboard): array
{
    $exactLookup = [];
    $firstNameLookup = [];

    foreach ($dialerLeaderboard as $agent) {
        $fullName = strtolower(trim($agent['name']));
        $firstName = strtolower(trim(explode(' ', $agent['name'])[0]));

        $exactLookup[$fullName] = $agent;

        // Don't overwrite if two agents share a first name — keep the
        // one with more calls (better signal than a random pick).
        if (! isset($firstNameLookup[$firstName]) || $agent['calls'] > $firstNameLookup[$firstName]['calls']) {
            $firstNameLookup[$firstName] = $agent;
        }
    }

    return array_map(function ($row) use ($exactLookup, $firstNameLookup) {
        $closerName = strtolower(trim($row['closer']));
        $closerFirstName = strtolower(trim(explode(' ', $row['closer'])[0]));

        $match = $exactLookup[$closerName] ?? $firstNameLookup[$closerFirstName] ?? null;

        $row['calls']         = $match['calls'] ?? 0;
        $row['avg_talk_time'] = $match['avg_talk_time'] ?? '-';

        return $row;
    }, $board);
}

/**
 * Sort the daily board using a blended performance score — same approach
 * as monthlyPerformanceRanking but applied to today's numbers:
 *   approved  → 50%  (more sales = higher score)
 *   level_pct → 15%  (higher level% = higher score)
 *   calls/approved ratio (conversion efficiency) → 35%
 *              lower calls-per-sale = more efficient = higher score
 *
 * Closers with zero approved sales are pushed to the bottom.
 */
public function sortDailyBoardByScore(array $board): array
{
    if (empty($board)) {
        return $board;
    }

    $maxApproved = max(array_column($board, 'approved')) ?: 1;
    $maxLevelPct = 100;

    // Conversion = calls / approved (lower is better, like monthly logic)
    $conversions = array_filter(
        array_map(fn ($r) => ($r['approved'] ?? 0) > 0 && ($r['calls'] ?? 0) > 0
            ? ($r['calls'] / $r['approved'])
            : null,
        $board),
        fn ($v) => $v !== null
    );
    $minConversion = !empty($conversions) ? min($conversions) : 0;

    foreach ($board as &$row) {
        $approved = $row['approved'] ?? 0;
        $calls    = $row['calls']    ?? 0;

        $salesScore     = $approved / $maxApproved;
        $levelScore     = ($row['level_pct'] ?? 0) / $maxLevelPct;
        $conversionRatio = ($approved > 0 && $calls > 0) ? ($approved / $calls) : 0;
        $conversionScore = $conversionRatio > 0 ? $conversionRatio : 0;

        // Normalize conversion score so best possible = 1
        $maxConvRatio = $maxApproved > 0 ? 1 : 1; // 1 sale per call is best possible
        $conversionScore = min($conversionScore, 1);

        $row['score'] = round(
            ($salesScore * 0.50) + ($conversionScore * 0.35) + ($levelScore * 0.15),
            4
        );
    }
    unset($row);

    usort($board, fn ($a, $b) => $b['score'] <=> $a['score']);

    return array_values($board);
}

public function monthlyCarriersReport(?string $from = null, ?string $to = null): array
{
    $from = $from ?? now()->startOfMonth()->toDateString();
    $to   = $to ?? now()->toDateString();

    $entries = DailySalesEntry::with('carrier')
        ->whereBetween('entry_date', [$from, $to])
        ->whereNotNull('sales_carrier_id')
        ->get();

    return $entries
        ->groupBy('sales_carrier_id')
        ->map(function ($rows) {
            $carrier = $rows->first()->carrier;
            $approved = $rows->where('status', 'approved');
            $levelCount = $approved->where('sale_type', 'level')->count();
            $mtd = $approved->count();

            return [
                'carrier'   => $carrier->name ?? 'Unknown',
                'approved'  => $mtd,
                'level'     => $levelCount,
                'gi'        => $approved->where('sale_type', 'gi')->count(),
                'level_pct' => $mtd > 0 ? round(($levelCount / $mtd) * 100) : 0,
                'avg_pre'   => $approved->avg('avg_pre') ? round($approved->avg('avg_pre'), 2) : 0,
            ];
        })
        ->sortByDesc('approved')
        ->values()
        ->all();
}

protected function hmsToSeconds(string $hms): int
{
    $parts = array_map('intval', explode(':', $hms));
    $parts = array_pad($parts, 3, 0);
    [$h, $m, $s] = array_slice($parts, -3);

    return ($h * 3600) + ($m * 60) + $s;
}
/**
 * Client Wise board — one box per client, showing each closer's
 * approved count against that client this month.
 */
public function clientWiseBoard(?string $month = null): array
{
    $monthStart = $month ? Carbon::parse($month)->startOfMonth() : now()->startOfMonth();

    $entries = DailySalesEntry::with(['closer', 'client'])
        ->where('status', 'approved')
        ->whereBetween('entry_date', [$monthStart->toDateString(), now()->toDateString()])
        ->whereNotNull('sales_client_id')
        ->get();

    return $entries
        ->groupBy(fn ($e) => $e->client->name ?? 'Unknown')
        ->map(function ($rows, $clientName) {
            $closerStats = $rows->groupBy('sales_closer_id')
                ->map(function ($closerRows) {
                    $closer = $closerRows->first()->closer;
                    $levelCount = $closerRows->where('sale_type', 'level')->count();

                    return [
                        'name'      => $closer->name ?? 'Unknown',
                        'approved'  => $closerRows->count(),
                        'level_pct' => $closerRows->count() > 0 ? round(($levelCount / $closerRows->count()) * 100, 1) : 0,
                    ];
                })
                ->sortByDesc('approved')
                ->values()
                ->all();

            return [
                'name'           => $clientName,
                'total_approved' => $rows->count(),
                'avg_pre'        => $rows->avg('avg_pre') ? round($rows->avg('avg_pre'), 2) : 0,
                'closers'        => $closerStats,
            ];
        })
        ->sortByDesc('total_approved')
        ->values()
        ->all();
}

/**
 * Carrier Wise board — one box per carrier, showing each closer's
 * approved count against that carrier this month.
 */
public function carrierWiseBoard(?string $month = null): array
{
    $monthStart = $month ? Carbon::parse($month)->startOfMonth() : now()->startOfMonth();

    $entries = DailySalesEntry::with(['closer', 'carrier'])
        ->where('status', 'approved')
        ->whereBetween('entry_date', [$monthStart->toDateString(), now()->toDateString()])
        ->whereNotNull('sales_carrier_id')
        ->get();

    return $entries
        ->groupBy(fn ($e) => $e->carrier->name ?? 'Unknown')
        ->map(function ($rows, $carrierName) {
            $closerStats = $rows->groupBy('sales_closer_id')
                ->map(function ($closerRows) {
                    $closer = $closerRows->first()->closer;
                    $levelCount = $closerRows->where('sale_type', 'level')->count();

                    return [
                        'name'      => $closer->name ?? 'Unknown',
                        'approved'  => $closerRows->count(),
                        'level_pct' => $closerRows->count() > 0 ? round(($levelCount / $closerRows->count()) * 100, 1) : 0,
                    ];
                })
                ->sortByDesc('approved')
                ->values()
                ->all();

            return [
                'name'           => $carrierName,
                'total_approved' => $rows->count(),
                'avg_pre'        => $rows->avg('avg_pre') ? round($rows->avg('avg_pre'), 2) : 0,
                'closers'        => $closerStats,
            ];
        })
        ->sortByDesc('total_approved')
        ->values()
        ->all();
}
// public function closerCounts(): array
// {
//     return [
//         'active'   => \App\Models\SalesCloser::where('active', true)->count(),
//         'inactive' => \App\Models\SalesCloser::where('active', false)->count(),
//     ];
// }
public function teamPerformancePie(?string $month = null): array
{
    $teamBoxes = $this->teamBoxes($month);

    $labels = array_column($teamBoxes, 'team');
    $data   = array_column($teamBoxes, 'total_approved');

    $topIndex = $data ? array_search(max($data), $data) : null;

    return [
        'labels'    => $labels,
        'data'      => $data,
        'top_team'  => $topIndex !== null && isset($labels[$topIndex]) ? $labels[$topIndex] : null,
    ];
}
/**
 * Adds live VICIdial columns (Avatar/Jcs Calls, Conversion %, Avg Talk
 * Time) into each closer row, matched by name — same fuzzy matching as
 * mergeDialerStats() for the daily board.
 */
public function mergeDialerStatsIntoTeams(array $teams, array $dialerLeaderboard): array
{
    $exactLookup = [];
    $firstNameLookup = [];

    foreach ($dialerLeaderboard as $agent) {
        $fullName = strtolower(trim($agent['name']));
        $firstName = strtolower(trim(explode(' ', $agent['name'])[0]));

        $exactLookup[$fullName] = $agent;

        if (! isset($firstNameLookup[$firstName]) || $agent['calls'] > $firstNameLookup[$firstName]['calls']) {
            $firstNameLookup[$firstName] = $agent;
        }
    }

    foreach ($teams as &$team) {
        $totalCalls = 0;
        $talkSeconds = [];

        foreach ($team['closers'] as &$closer) {
            $closerName = strtolower(trim($closer['closer']));
            $closerFirstName = strtolower(trim(explode(' ', $closer['closer'])[0]));

            $match = $exactLookup[$closerName] ?? $firstNameLookup[$closerFirstName] ?? null;
            $calls = $match['calls'] ?? 0;

            $closer['avatar_calls']  = $calls;
            $closer['avg_talk_time'] = $match['avg_talk_time'] ?? '-';
            $closer['conversion']    = $closer['mtd'] > 0 ? round($calls / $closer['mtd'], 2) : 0;

            $totalCalls += $calls;
            if ($match && $match['avg_talk_time'] !== '-') {
                $talkSeconds[] = $this->hmsToSeconds($match['avg_talk_time']);
            }
        }
        unset($closer);

        $avgTalkSeconds = count($talkSeconds) > 0 ? intdiv(array_sum($talkSeconds), count($talkSeconds)) : 0;

        $team['totals']['calls']         = $totalCalls;
        $team['totals']['avg_talk_time'] = $this->secondsToHms($avgTalkSeconds);
        $team['totals']['conversion']    = $team['totals']['mtd'] > 0 ? round($totalCalls / $team['totals']['mtd'], 2) : 0;
    }
    unset($team);

    return $teams;
}

/**
 * Flat Closer x Client table — one row per (closer, client) combo this
 * period, mirroring the team-wise granularity but grouped by client.
 */
public function clientWiseFlatReport(?string $from = null, ?string $to = null): array
{
    $from = $from ?? now()->startOfMonth()->toDateString();
    $to   = $to ?? now()->toDateString();

    $entries = DailySalesEntry::with(['closer', 'client'])
        ->whereBetween('entry_date', [$from, $to])
        ->whereNotNull('sales_client_id')
        ->get();

    $rows = $entries
        ->groupBy(fn ($e) => ($e->client->name ?? 'Unknown').'|'.$e->sales_closer_id)
        ->map(function ($group) {
            $closer = $group->first()->closer;
            $clientName = $group->first()->client->name ?? 'Unknown';
            $approved = $group->where('status', 'approved');
            $levelCount = $approved->where('sale_type', 'level')->count();
            $mtd = $approved->count();
            $workingDays = $group->pluck('entry_date')->map(fn ($d) => $d->toDateString())->unique()->count();

            return [
                'closer'      => $closer->name ?? 'Unknown',
                'client'      => $clientName,
                'working_days'=> $workingDays,
                'mtd'         => $mtd,
                'spd'         => $workingDays > 0 ? round($mtd / $workingDays, 1) : 0,
                'level'       => $levelCount,
                'gi'          => $approved->where('sale_type', 'gi')->count(),
                'level_pct'   => $mtd > 0 ? round(($levelCount / $mtd) * 100) : 0,
                'avg_pre'     => $approved->avg('avg_pre') ? round($approved->avg('avg_pre'), 2) : 0,
            ];
        })
        ->sortBy([['client', 'asc'], ['mtd', 'desc']])
        ->values()
        ->all();

    return $rows;
}

/**
 * Flat Closer x Carrier table — same shape, grouped by carrier instead.
 */
public function carrierWiseFlatReport(?string $from = null, ?string $to = null): array
{
    $from = $from ?? now()->startOfMonth()->toDateString();
    $to   = $to ?? now()->toDateString();

    $entries = DailySalesEntry::with(['closer', 'carrier'])
        ->whereBetween('entry_date', [$from, $to])
        ->whereNotNull('sales_carrier_id')
        ->get();

    $rows = $entries
        ->groupBy(fn ($e) => ($e->carrier->name ?? 'Unknown').'|'.$e->sales_closer_id)
        ->map(function ($group) {
            $closer = $group->first()->closer;
            $carrierName = $group->first()->carrier->name ?? 'Unknown';
            $approved = $group->where('status', 'approved');
            $levelCount = $approved->where('sale_type', 'level')->count();
            $mtd = $approved->count();
            $workingDays = $group->pluck('entry_date')->map(fn ($d) => $d->toDateString())->unique()->count();

            return [
                'closer'      => $closer->name ?? 'Unknown',
                'carrier'     => $carrierName,
                'working_days'=> $workingDays,
                'mtd'         => $mtd,
                'spd'         => $workingDays > 0 ? round($mtd / $workingDays, 1) : 0,
                'level'       => $levelCount,
                'gi'          => $approved->where('sale_type', 'gi')->count(),
                'level_pct'   => $mtd > 0 ? round(($levelCount / $mtd) * 100) : 0,
                'avg_pre'     => $approved->avg('avg_pre') ? round($approved->avg('avg_pre'), 2) : 0,
            ];
        })
        ->sortBy([['carrier', 'asc'], ['mtd', 'desc']])
        ->values()
        ->all();

    return $rows;
}
public function avgCallsPerSale(array $dailyBoardTotals): float
{
    return $dailyBoardTotals['approved'] > 0
        ? round($dailyBoardTotals['calls'] / $dailyBoardTotals['approved'], 1)
        : 0;
}


public function mergeDialerStatsIntoFlatRows(array $rows, array $dialerLeaderboard): array
{
    $exactLookup = [];
    $firstNameLookup = [];

    foreach ($dialerLeaderboard as $agent) {
        $fullName = strtolower(trim($agent['name']));
        $firstName = strtolower(trim(explode(' ', $agent['name'])[0]));

        $exactLookup[$fullName] = $agent;
        if (! isset($firstNameLookup[$firstName]) || $agent['calls'] > $firstNameLookup[$firstName]['calls']) {
            $firstNameLookup[$firstName] = $agent;
        }
    }

    return array_map(function ($row) use ($exactLookup, $firstNameLookup) {
        $name = strtolower(trim($row['closer']));
        $firstName = strtolower(trim(explode(' ', $row['closer'])[0]));

        $match = $exactLookup[$name] ?? $firstNameLookup[$firstName] ?? null;
        $calls = $match['calls'] ?? 0;

        $row['avatar_calls']  = $calls;
        $row['avg_talk_time'] = $match['avg_talk_time'] ?? '-';
        $row['conversion']    = $calls > 0 ? round(($row['mtd'] / $calls) * 100, 1) : 0;

        return $row;
    }, $rows);
}
/**
 * Dashboard "Teams" summary — one row per team with Target/Left, mirrors
 * the sheet's top table. Reuses the calls/talk-time merge if leaderboard
 * data is passed in.
 */
public function teamsSummaryTable(array $mergedTeams): array
{
    $lastSaleTimes = $this->teamLastSaleTimes();
    $activeIds     = $this->activeClosersToday();

    return array_values(array_filter(array_map(function ($team) use ($lastSaleTimes, $activeIds) {
        if ($team['name'] === 'Unassigned') {
            return null;
        }

        $target = \App\Models\SalesTeam::where('name', $team['name'])->value('target') ?? 0;

        $teamModel = \App\Models\SalesTeam::where('name', $team['name'])->first();
        $totalClosers = max(count($team['closers']), 1);
        $activeClosersCount = 0;
        
        if ($teamModel) {
            $totalClosers = \App\Models\SalesCloser::where('sales_team_id', $teamModel->id)->where('active', true)->count();
            $activeClosersCount = \App\Models\SalesCloser::where('sales_team_id', $teamModel->id)->whereIn('id', $activeIds)->count();
        }

        if ($totalClosers === 0) {
            $totalClosers = max(count($team['closers']), 1);
        }
        
        // For Dashboard Team Summary SPD, you requested:
        // "hr team ky present closers sy uski SPD nikly"
        // Active closers derived from Vicidial / activeIds
        $spdDivisor = $activeClosersCount > 0 ? $activeClosersCount : $totalClosers;

        $approved = $team['totals']['mtd'];
        $spd = round($approved / $spdDivisor, 2);

        return [
            'team'           => $team['name'],
            'closers'        => $totalClosers,
            'active_closers' => $activeClosersCount,
            'approved'       => $approved,
            'level'         => $team['totals']['level'],
            'gi'            => $team['totals']['gi'],
            'level_pct'     => $team['averages']['level_pct'],
            'spd'           => $spd,
            'avg_pre'       => $team['averages']['avg_pre'],
            'calls'         => $team['totals']['calls'] ?? 0,
            'avg_talk_time' => $team['totals']['avg_talk_time'] ?? '-',
            'target'        => $target,
            'left'          => max($target - $approved, 0),
            'last_sale'     => $lastSaleTimes[$team['name']] ?? '-',
        ];
    }, $mergedTeams)));
}


protected function teamLastSaleTimes(): array
{
    return DailySalesEntry::with('closer.team')
        ->where('status', 'approved')
        ->get()
        ->groupBy(fn ($e) => $e->closer->team->name ?? 'Unassigned')
        ->map(function ($rows) {
            $last = $rows->max('created_at');
            return $last ? $this->secondsToHms($last->diffInSeconds(now())) : '-';
        })
        ->all();
}

public function teamsSummaryTotals(array $teamsSummary): array
{
    $count = count($teamsSummary);
    if ($count === 0) {
        return [
            'closers' => 0, 'approved' => 0, 'level' => 0, 'gi' => 0,
            'level_pct' => 0, 'spd' => 0, 'avg_pre' => 0,
            'avg_talk_time' => '0:00:00', 'target' => 0, 'left' => 0,
        ];
    }

    $approved = array_sum(array_column($teamsSummary, 'approved'));
    $level    = array_sum(array_column($teamsSummary, 'level'));

    $weightedSeconds = 0;
    $totalCalls = 0;
    foreach ($teamsSummary as $t) {
        $calls = $t['calls'] ?? 0;
        if ($calls > 0 && ($t['avg_talk_time'] ?? '-') !== '-') {
            $weightedSeconds += $this->hmsToSeconds($t['avg_talk_time']) * $calls;
            $totalCalls += $calls;
        }
    }
    $avgTalkSeconds = $totalCalls > 0 ? intdiv($weightedSeconds, $totalCalls) : 0;

    $totalClosers       = array_sum(array_column($teamsSummary, 'closers'));
    $totalActiveClosers = array_sum(array_column($teamsSummary, 'active_closers'));
    
    $spdDivisor = $totalActiveClosers > 0 ? $totalActiveClosers : ($totalClosers > 0 ? $totalClosers : 1);

    return [
        'closers'        => $totalClosers,
        'active_closers' => $totalActiveClosers,
        'approved'       => $approved,
        'level'          => $level,
        'gi'             => array_sum(array_column($teamsSummary, 'gi')),
        'level_pct'      => $approved > 0 ? round(($level / $approved) * 100) : 0,
        'spd'            => round($approved / $spdDivisor, 2),
        'avg_pre'       => round(array_sum(array_column($teamsSummary, 'avg_pre')) / $count),
        'avg_talk_time' => $this->secondsToHms($avgTalkSeconds),
        'target'        => array_sum(array_column($teamsSummary, 'target')),
        'left'          => array_sum(array_column($teamsSummary, 'left')),
    ];
}
/**
 * Dashboard "Clients" summary — Underwriting(pending) / Approved / Level /
 * GI / Level% / Target / Left / Avg Pre, matching the sheet.
 */
public function clientsSummaryTable(?string $from = null, ?string $to = null): array
{
    $from = $from ?? now('America/New_York')->startOfMonth()->toDateString();
    $to   = $to ?? now('America/New_York')->toDateString();

    $entries = DailySalesEntry::with('client')
        ->whereBetween('entry_date', [$from, $to])
        ->whereNotNull('sales_client_id')
        ->get();

    return $entries
        ->groupBy('sales_client_id')
        ->map(function ($rows) {
            $client = $rows->first()->client;
            $approved = $rows->where('status', 'approved');
            $levelCount = $approved->where('sale_type', 'level')->count();
            $approvedCount = $approved->count();
            $target = $client->target ?? 0;

            return [
                'client'        => $client->name ?? 'Unknown',
                'underwriting'  => $rows->where('status', 'pending')->count(),
                'approved'      => $approvedCount,
                'level'         => $levelCount,
                'gi'            => $approved->where('sale_type', 'gi')->count(),
                'level_pct'     => $approvedCount > 0 ? round(($levelCount / $approvedCount) * 100) : 0,
                'target'        => $target,
                'left'          => max($target - $approvedCount, 0),
                'avg_pre'       => $approved->avg('avg_pre') ? round($approved->avg('avg_pre'), 2) : 0,
                'last_sale' => $approved->max('created_at') ? $approved->max('created_at')->format('d M, h:i A') : '-',
            ];
        })
        ->sortByDesc('approved')
        ->values()
        ->all();
}

/**
 * Dashboard "Carriers" summary — same shape as clients.
 */
public function carriersSummaryTable(?string $from = null, ?string $to = null): array
{
    $from = $from ?? now('America/New_York')->startOfMonth()->toDateString();
    $to   = $to ?? now('America/New_York')->toDateString();

    $entries = DailySalesEntry::with('carrier')
        ->whereBetween('entry_date', [$from, $to])
        ->whereNotNull('sales_carrier_id')
        ->get();

    return $entries
        ->groupBy('sales_carrier_id')
        ->map(function ($rows) {
            $carrier = $rows->first()->carrier;
            $approved = $rows->where('status', 'approved');
            $levelCount = $approved->where('sale_type', 'level')->count();
            $approvedCount = $approved->count();
            $target = $carrier->target ?? 0;

            return [
                'carrier'       => $carrier->name ?? 'Unknown',
                'underwriting'  => $rows->where('status', 'pending')->count(),
                'approved'      => $approvedCount,
                'level'         => $levelCount,
                'gi'            => $approved->where('sale_type', 'gi')->count(),
                'level_pct'     => $approvedCount > 0 ? round(($levelCount / $approvedCount) * 100) : 0,
                'target'        => $target,
                'left'          => max($target - $approvedCount, 0),
                'avg_pre'       => $approved->avg('avg_pre') ? round($approved->avg('avg_pre'), 2) : 0,
            ];
        })
        ->sortByDesc('approved')
        ->values()
        ->all();
}

public function attendanceMonthlySummary(?string $month = null): array
{
    $monthStart = $month ? Carbon::parse($month)->startOfMonth() : now()->startOfMonth();
    $monthEnd   = now()->min($monthStart->copy()->endOfMonth());

    $records = \App\Models\ClosersAttendance::with('closer')
        ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
        ->get();

    return $records
        ->groupBy('sales_closer_id')
        ->map(function ($rows) {
            $closer = $rows->first()->closer;

            return [
                'closer'   => $closer->name ?? 'Unknown',
                'present'  => $rows->where('status', 'present')->count(),
                'half_day' => $rows->where('status', 'half_day')->count(),
                'absent'   => $rows->where('status', 'absent')->count(),
                'leave'    => $rows->where('status', 'leave')->count(),
                'total'    => $rows->count(),
            ];
        })
        ->sortBy('closer')
        ->values()
        ->all();
}
/**
 * Closers marked "present" today via attendance AND assigned to a team.
 * Unassigned closers never count as active, no matter their attendance.
 */
public function activeClosersToday(): array
{
    return \App\Models\ClosersAttendance::whereDate('attendance_date', now('America/New_York')->toDateString())
        ->where('status', 'present')
        ->whereHas('closer', fn ($q) => $q->whereNotNull('sales_team_id'))
        ->pluck('sales_closer_id')
        ->unique()
        ->values()
        ->all();
}

public function closerCounts(): array
{
    $activeIds = $this->activeClosersToday();

    return [
        'active' => count($activeIds),
        'total'  => \App\Models\SalesCloser::whereNotNull('sales_team_id')->count(),
    ];
}

/**
 * Dialer stats (calls, avg talk time) restricted to only today's ACTIVE
 * (present + assigned) closers — matched by name to the VICIdial feed.
 */
public function activeClosersDialerStats(array $dialerLeaderboard): array
{
    $activeNames = \App\Models\SalesCloser::whereIn('id', $this->activeClosersToday())->pluck('name');

    $exactLookup = [];
    $firstNameLookup = [];
    foreach ($dialerLeaderboard as $agent) {
        $fullName = strtolower(trim($agent['name']));
        $firstName = strtolower(trim(explode(' ', $agent['name'])[0]));
        $exactLookup[$fullName] = $agent;
        if (! isset($firstNameLookup[$firstName]) || $agent['calls'] > $firstNameLookup[$firstName]['calls']) {
            $firstNameLookup[$firstName] = $agent;
        }
    }

    $totalCalls = 0;
    $weightedSeconds = 0;
    $callsCounted = 0;

    foreach ($activeNames as $name) {
        $key = strtolower(trim($name));
        $firstKey = strtolower(trim(explode(' ', $name)[0]));
        $match = $exactLookup[$key] ?? $firstNameLookup[$firstKey] ?? null;

        if ($match) {
            $calls = $match['calls'];
            $totalCalls += $calls;
            if ($calls > 0 && $match['avg_talk_time'] !== '-') {
                $weightedSeconds += $this->hmsToSeconds($match['avg_talk_time']) * $calls;
                $callsCounted += $calls;
            }
        }
    }

    $avgTalkSeconds = $callsCounted > 0 ? intdiv($weightedSeconds, $callsCounted) : 0;

    return [
        'calls'         => $totalCalls,
        'avg_talk_time' => $this->secondsToHms($avgTalkSeconds),
    ];
}

/**
 * One row per closer (not per closer+client) — MTD/SPD/Level/GI/Level%/Avg
 * Pre aggregated across ALL their clients, with a per-client APPROVED
 * count as extra columns. This is what the pivot table on the
 * Client-Wise page needs (team-wise already does this per team).
 */
public function closerClientMatrix(?string $from = null, ?string $to = null): array
{
    $from = $from ?? now('America/New_York')->startOfMonth()->toDateString();
    $to   = $to ?? now('America/New_York')->toDateString();

    $entries = DailySalesEntry::with(['closer', 'client'])
        ->whereBetween('entry_date', [$from, $to])
        ->get();

    $clientNames = \App\Models\SalesClient::orderBy('name')->pluck('name')->all();

    $rows = $entries
        ->groupBy('sales_closer_id')
        ->map(function ($rows) use ($clientNames) {
            $closer = $rows->first()->closer;
            $approved = $rows->where('status', 'approved');
            $levelCount = $approved->where('sale_type', 'level')->count();
            $mtd = $approved->count();
            $workingDays = $rows->pluck('entry_date')->map(fn ($d) => $d->toDateString())->unique()->count();

            $clientCounts = [];
            foreach ($clientNames as $clientName) {
                // Approved-only count for this client — pending never counted here
                $clientCounts[$clientName] = $approved
                    ->filter(fn ($e) => optional($e->client)->name === $clientName)
                    ->count();
            }

            return [
                'closer'       => $closer->name ?? 'Unknown',
                'working_days' => $workingDays,
                'mtd'          => $mtd,
                'spd'          => $workingDays > 0 ? round($mtd / $workingDays, 1) : 0,
                'level'        => $levelCount,
                'gi'           => $approved->where('sale_type', 'gi')->count(),
                'level_pct'    => $mtd > 0 ? round(($levelCount / $mtd) * 100) : 0,
                'avg_pre'      => $approved->avg('avg_pre') ? round($approved->avg('avg_pre'), 2) : 0,
                'clients'      => $clientCounts,
            ];
        })
        ->sortByDesc('mtd')
        ->values()
        ->all();

    return ['rows' => $rows, 'clients' => $clientNames];
}

/**
 * One row per closer (not per closer+carrier) — MTD/SPD/Level/GI/Level%/
 * Avg Pre aggregated across ALL their carriers, with per-carrier APPROVED
 * counts as extra columns, plus merged dialer stats (calls/conversion/
 * talk time) at the closer level.
 */
public function closerCarrierMatrix(?string $from = null, ?string $to = null, array $dialerLeaderboard = []): array
{
    $from = $from ?? now('America/New_York')->startOfMonth()->toDateString();
    $to   = $to ?? now('America/New_York')->toDateString();

    $entries = DailySalesEntry::with(['closer', 'carrier'])
        ->whereBetween('entry_date', [$from, $to])
        ->get();

    $carrierNames = \App\Models\SalesCarrier::orderBy('name')->pluck('name')->all();

    $exactLookup = [];
    $firstNameLookup = [];
    foreach ($dialerLeaderboard as $agent) {
        $fullName = strtolower(trim($agent['name']));
        $firstName = strtolower(trim(explode(' ', $agent['name'])[0]));
        $exactLookup[$fullName] = $agent;
        if (! isset($firstNameLookup[$firstName]) || $agent['calls'] > $firstNameLookup[$firstName]['calls']) {
            $firstNameLookup[$firstName] = $agent;
        }
    }

    $rows = $entries
        ->groupBy('sales_closer_id')
        ->map(function ($rows) use ($carrierNames, $exactLookup, $firstNameLookup) {
            $closer = $rows->first()->closer;
            $approved = $rows->where('status', 'approved');
            $levelCount = $approved->where('sale_type', 'level')->count();
            $mtd = $approved->count();
            $workingDays = $rows->pluck('entry_date')->map(fn ($d) => $d->toDateString())->unique()->count();

            $carrierCounts = [];
            foreach ($carrierNames as $carrierName) {
                $carrierCounts[$carrierName] = $approved
                    ->filter(fn ($e) => optional($e->carrier)->name === $carrierName)
                    ->count();
            }

            $closerName = strtolower(trim($closer->name ?? ''));
            $firstName = strtolower(trim(explode(' ', $closer->name ?? '')[0]));
            $match = $exactLookup[$closerName] ?? $firstNameLookup[$firstName] ?? null;
            $calls = $match['calls'] ?? 0;

            return [
                'closer'        => $closer->name ?? 'Unknown',
                'working_days'  => $workingDays,
                'mtd'           => $mtd,
                'spd'           => $workingDays > 0 ? round($mtd / $workingDays, 1) : 0,
                'level'         => $levelCount,
                'gi'            => $approved->where('sale_type', 'gi')->count(),
                'level_pct'     => $mtd > 0 ? round(($levelCount / $mtd) * 100) : 0,
                'avg_pre'       => $approved->avg('avg_pre') ? round($approved->avg('avg_pre'), 2) : 0,
                'avatar_calls'  => $calls,
                'conversion'    => $calls > 0 ? round(($mtd / $calls) * 100, 1) : 0,
                'avg_talk_time' => $match['avg_talk_time'] ?? '-',
                'carriers'      => $carrierCounts,
            ];
        })
        ->sortByDesc('mtd')
        ->values()
        ->all();

    return ['rows' => $rows, 'carriers' => $carrierNames];
}
}

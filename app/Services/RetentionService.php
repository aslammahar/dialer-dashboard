<?php

namespace App\Services;

use App\Models\RetentionSalesEntry;
use App\Models\SalesCloser;
use App\Models\SalesTeam;
use App\Models\SalesClient;
use App\Models\SalesCarrier;
use App\Models\ClosersAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RetentionService
{
    /**
     * Daily Board for Retention — mirrors Daily Board Retention sheet
     */
    public function dailyBoardRetention(?string $date = null): array
    {
        $todayNY = $date ?? now('America/New_York')->toDateString();
        $monthStart = Carbon::parse($todayNY, 'America/New_York')->startOfMonth()->toDateString();

        // Get daily retention sales
        $dailyEntries = RetentionSalesEntry::with(['closer.team', 'client', 'carrier'])
            ->where('status', 'approved')
            ->whereDate('entry_date', $todayNY)
            ->get();

        // Get monthly retention sales
        $monthlyEntries = RetentionSalesEntry::with(['closer.team', 'client', 'carrier'])
            ->where('status', 'approved')
            ->whereBetween('entry_date', [$monthStart, $todayNY])
            ->get();

        // Get active retention closers or all closers having entries
        $retentionTeam = SalesTeam::where('name', 'LIKE', '%Retention%')->first();
        $closersQuery = SalesCloser::query();
        if ($retentionTeam) {
            $closersQuery->where('sales_team_id', $retentionTeam->id);
        }
        $closers = $closersQuery->where('active', true)->get();

        // Include any closer who has sales today even if not in retention team
        $dailyCloserIds = $dailyEntries->pluck('sales_closer_id')->unique();
        $missingClosers = SalesCloser::whereIn('id', $dailyCloserIds)->get();
        $closers = $closers->merge($missingClosers)->unique('id');

        $rows = [];
        foreach ($closers as $closer) {
            $cDaily = $dailyEntries->where('sales_closer_id', $closer->id);
            $cMonthly = $monthlyEntries->where('sales_closer_id', $closer->id);

            $rewrite = $cDaily->where('retention_type', 'rewrite')->count();
            $fixed = $cDaily->where('retention_type', 'fixed')->count();
            $corr = $cDaily->where('retention_type', 'correspondence')->count();
            $newPolicy = $cDaily->where('retention_type', 'new_policy')->count();
            $totalSales = $rewrite + $fixed + $corr + $newPolicy;

            $level = $cDaily->where('sale_type', 'level')->count();
            $gi = $cDaily->where('sale_type', 'gi')->count();
            $levelPct = $totalSales > 0 ? round(($level / $totalSales) * 100) : 0;

            $avgPreValues = $cDaily->pluck('avg_pre')->filter(fn($v) => $v > 0)->all();
            $avgPre = count($avgPreValues) > 0 ? round(array_sum($avgPreValues) / count($avgPreValues), 2) : 0;

            // Last sale time
            $lastEntry = $cDaily->sortByDesc('created_at')->first();
            $timeSinceLastSale = '-';
            if ($lastEntry && $lastEntry->created_at) {
                $diffSeconds = now()->diffInSeconds($lastEntry->created_at);
                $timeSinceLastSale = gmdate('H:i:s', $diffSeconds);
            }

            // Monthly counts
            $mRewrite = $cMonthly->where('retention_type', 'rewrite')->count();
            $mFixed = $cMonthly->where('retention_type', 'fixed')->count();
            $mCorr = $cMonthly->where('retention_type', 'correspondence')->count();
            $mNewPolicy = $cMonthly->where('retention_type', 'new_policy')->count();
            $mTotalSales = $mRewrite + $mFixed + $mCorr + $mNewPolicy;
            $mLevel = $cMonthly->where('sale_type', 'level')->count();
            $mGi = $cMonthly->where('sale_type', 'gi')->count();

            $rows[] = [
                'closer_id' => $closer->id,
                'closer' => $closer->name,
                'team' => $closer->team->name ?? 'Retention',
                'time_since_last_sale' => $timeSinceLastSale,
                'rewrite' => $rewrite,
                'fixed' => $fixed,
                'correspondence' => $corr,
                'new_policy' => $newPolicy,
                'total_sales' => $totalSales,
                'level' => $level,
                'gi' => $gi,
                'level_pct' => $levelPct,
                'avg_pre' => $avgPre,
                // Monthly
                'm_rewrite' => $mRewrite,
                'm_fixed' => $mFixed,
                'm_corr' => $mCorr,
                'm_new_policy' => $mNewPolicy,
                'm_total_sales' => $mTotalSales,
                'm_level' => $mLevel,
                'm_gi' => $mGi,
            ];
        }

        // Sort rows by total_sales desc
        usort($rows, fn($a, $b) => $b['total_sales'] <=> $a['total_sales']);

        return $rows;
    }

    /**
     * Totals for daily board retention
     */
    public function dailyBoardRetentionTotals(array $rows): array
    {
        $totals = [
            'rewrite' => array_sum(array_column($rows, 'rewrite')),
            'fixed' => array_sum(array_column($rows, 'fixed')),
            'correspondence' => array_sum(array_column($rows, 'correspondence')),
            'new_policy' => array_sum(array_column($rows, 'new_policy')),
            'total_sales' => array_sum(array_column($rows, 'total_sales')),
            'level' => array_sum(array_column($rows, 'level')),
            'gi' => array_sum(array_column($rows, 'gi')),
            'level_pct' => 0,
            'm_rewrite' => array_sum(array_column($rows, 'm_rewrite')),
            'm_fixed' => array_sum(array_column($rows, 'm_fixed')),
            'm_corr' => array_sum(array_column($rows, 'm_corr')),
            'm_new_policy' => array_sum(array_column($rows, 'm_new_policy')),
            'm_total_sales' => array_sum(array_column($rows, 'm_total_sales')),
            'm_level' => array_sum(array_column($rows, 'm_level')),
            'm_gi' => array_sum(array_column($rows, 'm_gi')),
        ];

        if ($totals['total_sales'] > 0) {
            $totals['level_pct'] = round(($totals['level'] / $totals['total_sales']) * 100, 1);
        }

        return $totals;
    }

    /**
     * Teams summary table for Retention
     */
    public function teamsSummaryRetention(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $from = $dateFrom ?? now('America/New_York')->startOfMonth()->toDateString();
        $to = $dateTo ?? now('America/New_York')->toDateString();

        $entries = RetentionSalesEntry::with(['closer.team'])
            ->where('status', 'approved')
            ->whereBetween('entry_date', [$from, $to])
            ->get();

        $grouped = $entries->groupBy(fn($e) => $e->closer->team->name ?? 'Retention');

        $rows = [];
        foreach ($grouped as $teamName => $tEntries) {
            $closersCount = $tEntries->pluck('sales_closer_id')->unique()->count();
            $rewriteFixed = $tEntries->whereIn('retention_type', ['rewrite', 'fixed'])->count();
            $level = $tEntries->where('sale_type', 'level')->count();
            $gi = $tEntries->where('sale_type', 'gi')->count();
            $totalSales = $tEntries->count();
            $levelPct = $totalSales > 0 ? round(($level / $totalSales) * 100, 1) : 0;
            
            // Working days approximation for SPD
            $days = Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1;
            $spd = $closersCount > 0 ? round($totalSales / ($closersCount * max(1, $days)), 1) : 0;
            $mtdSpd = $closersCount > 0 ? round($totalSales / $closersCount, 1) : 0;

            $rows[] = [
                'team' => $teamName,
                'closers' => $closersCount,
                'rewrite_fixed' => $rewriteFixed,
                'level' => $level,
                'gi' => $gi,
                'level_pct' => $levelPct,
                'spd' => $spd,
                'mtd_spd' => $mtdSpd,
            ];
        }

        return $rows;
    }

    /**
     * Clients summary table for Retention
     */
    public function clientsSummaryRetention(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $from = $dateFrom ?? now('America/New_York')->startOfMonth()->toDateString();
        $to = $dateTo ?? now('America/New_York')->toDateString();

        $allClients = SalesClient::orderBy('name')->get();
        $entries = RetentionSalesEntry::with('client')
            ->where('status', 'approved')
            ->whereBetween('entry_date', [$from, $to])
            ->get();

        $rows = [];
        foreach ($allClients as $client) {
            $cEntries = $entries->where('sales_client_id', $client->id);

            $rewrite = $cEntries->where('retention_type', 'rewrite')->count();
            $fixed = $cEntries->where('retention_type', 'fixed')->count();
            $corr = $cEntries->where('retention_type', 'correspondence')->count();
            $newPolicy = $cEntries->where('retention_type', 'new_policy')->count();
            $level = $cEntries->where('sale_type', 'level')->count();
            $gi = $cEntries->where('sale_type', 'gi')->count();
            
            $target = 20; // Default target per client
            $totalSales = $cEntries->count();
            $left = max(0, $target - $totalSales);

            $avgPreValues = $cEntries->pluck('avg_pre')->filter(fn($v) => $v > 0)->all();
            $avgPre = count($avgPreValues) > 0 ? round(array_sum($avgPreValues) / count($avgPreValues), 2) : 0;

            $rows[] = [
                'client' => $client->name,
                'underwriting' => 0,
                'rewrite' => $rewrite,
                'fixed' => $fixed,
                'correspondence' => $corr,
                'new_policy' => $newPolicy,
                'level' => $level,
                'gi' => $gi,
                'target' => $target,
                'left' => $left,
                'avg_pre' => $avgPre,
            ];
        }

        return $rows;
    }

    /**
     * Retention Closers Monthly Report (Screenshot 2 & 5)
     */
    public function retentionClosersReport(?string $month = null): array
    {
        $monthStart = $month ? Carbon::parse($month, 'America/New_York')->startOfMonth() : now('America/New_York')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $entries = RetentionSalesEntry::with(['closer', 'client', 'carrier'])
            ->where('status', 'approved')
            ->whereBetween('entry_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();

        $grouped = $entries->groupBy('sales_closer_id');
        $closers = SalesCloser::where('active', true)->get();

        $rows = [];
        foreach ($closers as $closer) {
            $cEntries = $grouped->get($closer->id, collect());

            $workingDays = ClosersAttendance::where('sales_closer_id', $closer->id)
                ->whereIn('status', ['present', 'half_day'])
                ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->count();
            $workingDays = max(1, $workingDays);

            $rewrite = $cEntries->where('retention_type', 'rewrite')->count();
            $fixed = $cEntries->where('retention_type', 'fixed')->count();
            $corr = $cEntries->where('retention_type', 'correspondence')->count();
            $newPolicy = $cEntries->where('retention_type', 'new_policy')->count();
            $totalCount = $cEntries->count();

            $level = $cEntries->where('sale_type', 'level')->count();
            $gi = $cEntries->where('sale_type', 'gi')->count();
            $levelPct = $totalCount > 0 ? round(($level / $totalCount) * 100, 1) : 0;
            $spd = round($totalCount / $workingDays, 1);

            // Client specific counts
            $fcf = $cEntries->where('client.name', 'FCF')->count();
            $d6 = $cEntries->where('client.name', 'D6')->count();
            $pm7 = $cEntries->where('client.name', 'PM7')->count();

            $avgPreValues = $cEntries->pluck('avg_pre')->filter(fn($v) => $v > 0)->all();
            $avgPre = count($avgPreValues) > 0 ? round(array_sum($avgPreValues) / count($avgPreValues), 2) : 0;

            $rows[] = [
                'closer' => $closer->name,
                'working_days' => $workingDays,
                'total_count' => $totalCount,
                'rewrite' => $rewrite,
                'fixed' => $fixed,
                'correspondence' => $corr,
                'new_policy' => $newPolicy,
                'spd' => $spd,
                'level' => $level,
                'gi' => $gi,
                'level_pct' => $levelPct,
                'fcf' => $fcf,
                'd6' => $d6,
                'pm7' => $pm7,
                'avg_pre' => $avgPre,
            ];
        }

        return $rows;
    }

    /**
     * Retention Clients Report (Screenshot 4)
     */
    public function retentionClientsReport(?string $month = null): array
    {
        $monthStart = $month ? Carbon::parse($month, 'America/New_York')->startOfMonth() : now('America/New_York')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $entries = RetentionSalesEntry::with(['client', 'carrier'])
            ->where('status', 'approved')
            ->whereBetween('entry_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();

        $clients = SalesClient::orderBy('name')->get();
        $carriers = SalesCarrier::orderBy('name')->get();

        $clientRows = [];
        foreach ($clients as $client) {
            $cEntries = $entries->where('sales_client_id', $client->id);
            $mtd = $cEntries->count();
            $level = $cEntries->where('sale_type', 'level')->count();
            $gi = $cEntries->where('sale_type', 'gi')->count();
            $levelPct = $mtd > 0 ? round(($level / $mtd) * 100, 1) : 0;
            $avgPreValues = $cEntries->pluck('avg_pre')->filter(fn($v) => $v > 0)->all();
            $avgPre = count($avgPreValues) > 0 ? round(array_sum($avgPreValues) / count($avgPreValues), 2) : 0;

            $clientRows[] = [
                'client' => $client->name,
                'work_days' => 2,
                'mtd' => $mtd,
                'spd' => round($mtd / 2, 1),
                'level' => $level,
                'gi' => $gi,
                'level_pct' => $levelPct,
                'avg_pre' => $avgPre,
            ];
        }

        $carrierMatrix = [];
        foreach ($carriers as $carrier) {
            $row = ['carrier' => $carrier->name];
            foreach ($clients as $client) {
                $e = $entries->where('sales_carrier_id', $carrier->id)->where('sales_client_id', $client->id);
                $row[$client->name . '_rewrite'] = $e->where('retention_type', 'rewrite')->count();
                $row[$client->name . '_fixed'] = $e->where('retention_type', 'fixed')->count();
                $row[$client->name . '_correspondence'] = $e->where('retention_type', 'correspondence')->count();
                $row[$client->name . '_new_policy'] = $e->where('retention_type', 'new_policy')->count();
            }
            $carrierMatrix[] = $row;
        }

        return [
            'clients' => $clientRows,
            'carriers' => $carrierMatrix,
        ];
    }
}

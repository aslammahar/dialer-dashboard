<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DialerApiService
{
    protected string $closerStatsUrl = 'https://jsonsd4.maxtelco.com/vicidial/AST_CLOSERstats.php';

    protected array $closerGroups = [
        '001to299', '002OUs', '003to002', '003TO007', '003To007DrXF', '003To299',
        '003To929', '004to299', '004to929', '007to002', '007To003', '007to010',
        '007To299', '007To929', '01001', '200DIDS', 'AGENTDIRECT', 'CALLBACK',
        'CALLBACK992', 'CALLBACKDID200', 'CALLBACKDIDS', 'Jsons1', 'Jsons3',
        'Jsons5', 'TFNnumbers',
    ];

    protected int $cacheTtl;

    public function __construct()
    {
        $this->cacheTtl = (int) config('services.dialer.cache_ttl', 300);
    }

    public function pullCallLogs(?string $from = null, ?string $to = null): array
    {
        return [];
    }

    /**
     * Live stat cards: total calls, avg talk time (selected range), daily
     * average + trend, monthly average + trend — all computed from real
     * CLOSERstats data, compared against the equivalent prior period.
     */
    public function summary(array $filters): array
    {
        $current = $this->leaderboardTotals($this->leaderboard($filters));

        $from = Carbon::parse($filters['from']);
        $to   = Carbon::parse($filters['to']);
        $days = $from->diffInDays($to) + 1;

        $prevTo   = $from->copy()->subDay();
        $prevFrom = $prevTo->copy()->subDays($days - 1);
        $previous = $this->leaderboardTotals($this->leaderboard([
            'from' => $prevFrom->toDateString(),
            'to'   => $prevTo->toDateString(),
        ]));

        $callsTrend = $this->percentChange($previous['calls'], $current['calls']);

        $yesterday       = now('America/New_York')->subDay();
        $dayBefore       = now('America/New_York')->subDays(2);
        $yesterdayTotals = $this->leaderboardTotals($this->leaderboard([
            'from' => $yesterday->toDateString(), 'to' => $yesterday->toDateString(),
        ]));
        $dayBeforeTotals = $this->leaderboardTotals($this->leaderboard([
            'from' => $dayBefore->toDateString(), 'to' => $dayBefore->toDateString(),
        ]));
        $dailyTrend = $this->percentChange(
            $this->hmsToSeconds($dayBeforeTotals['avg_talk_time']),
            $this->hmsToSeconds($yesterdayTotals['avg_talk_time'])
        );

        $mtdFrom     = now('America/New_York')->startOfMonth();
        $mtdTo       = now('America/New_York');
        $monthTotals = $this->leaderboardTotals($this->leaderboard([
            'from' => $mtdFrom->toDateString(), 'to' => $mtdTo->toDateString(),
        ]));

        $lastMonthFrom   = $mtdFrom->copy()->subMonthNoOverflow();
        $lastMonthTo     = $lastMonthFrom->copy()->addDays($mtdFrom->diffInDays($mtdTo));
        $lastMonthTotals = $this->leaderboardTotals($this->leaderboard([
            'from' => $lastMonthFrom->toDateString(), 'to' => $lastMonthTo->toDateString(),
        ]));
        $monthlyTrend = $this->percentChange(
            $this->hmsToSeconds($lastMonthTotals['avg_talk_time']),
            $this->hmsToSeconds($monthTotals['avg_talk_time'])
        );

        return [
            'total_calls'           => $current['calls'],
            'calls_trend'           => $callsTrend,
            'avg_talk_time'         => $current['avg_talk_time'],
            'daily_avg_talk_time'   => $yesterdayTotals['avg_talk_time'],
            'daily_trend'           => $dailyTrend,
            'monthly_avg_talk_time' => $monthTotals['avg_talk_time'],
            'monthly_trend'         => $monthlyTrend,
        ];
    }

    /**
     * Real per-day trend for the chart. Capped at 31 days so a big
     * "archive" range doesn't fire 100+ dialer requests — each day is
     * cached separately, so repeat loads are fast.
     */
    public function trend(array $filters): array
    {
        $from = Carbon::parse($filters['from']);
        $to   = Carbon::parse($filters['to']);
        $to   = $from->copy()->addDays(min($from->diffInDays($to), 30));

        $labels = [];
        $talkTimeMinutes = [];
        $callVolume = [];

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $dayTotals = $this->leaderboardTotals($this->leaderboard([
                'from' => $date->toDateString(),
                'to'   => $date->toDateString(),
                'view' => $filters['view'] ?? 'active',
            ]));

            $labels[]          = $date->format('M j');
            $talkTimeMinutes[] = round($this->hmsToSeconds($dayTotals['avg_talk_time']) / 60, 1);
            $callVolume[]      = $dayTotals['calls'];
        }

        return [
            'labels'                => $labels,
            'avg_talk_time_minutes' => $talkTimeMinutes,
            'call_volume'           => $callVolume,
        ];
    }

    public function leaderboard(array $filters): array
    {
        $csv = $this->fetchCloserStatsCsv($filters);

        return $this->parseCloserStatsCsv($csv);
    }

    public function leaderboardTotals(array $leaderboard): array
    {
        $calls = array_sum(array_column($leaderboard, 'calls'));

        $totalSeconds = array_sum(array_map(
            fn ($a) => $this->hmsToSeconds($a['time']),
            $leaderboard
        ));

        $count = count($leaderboard);
        $avgSeconds = $count > 0 ? intdiv($totalSeconds, $count) : 0;

        return [
            'calls'         => $calls,
            'time'          => $this->secondsToHms($totalSeconds),
            'avg_talk_time' => $this->secondsToHms($avgSeconds),
        ];
    }

    public function leaderboardTeams(array $leaderboard): array
    {
        return collect($leaderboard)->pluck('team')->unique()->sort()->values()->all();
    }

    protected function fetchCloserStatsCsv(array $filters): string
    {
        $from = $filters['from'] ?? now('America/New_York')->toDateString();
        $to   = $filters['to'] ?? now('America/New_York')->toDateString();

        // VICIdial only returns data outside its live retention window when
        // search_archived_data is explicitly sent as "checked" — without it,
        // an archive date-range silently comes back empty.
        $isArchive = ($filters['view'] ?? 'active') === 'archive';

        $cacheKey = "dialer:closerstats:{$from}:{$to}:" . ($isArchive ? 'archive' : 'active');

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($from, $to, $isArchive) {
            $response = Http::withOptions(['verify' => false])
                ->withBasicAuth(
                    config('services.dialer.username'),
                    config('services.dialer.password')
                )
                ->get($this->closerStatsUrl, [
                    'DB'                   => 0,
                    'DID'                  => '',
                    'query_date'           => $from,
                    'end_date'             => $to,
                    'group'                => $this->closerGroups,
                    'shift'                => 'ALL',
                    'SUBMIT'               => 'SUBMIT',
                    'file_download'        => 7,
                    'search_archived_data' => $isArchive ? 'checked' : '',
                ]);

            if (! $response->successful()) {
                Log::warning('DialerApiService: CLOSERstats fetch failed', ['status' => $response->status()]);
                throw new \RuntimeException('Unable to reach VICIdial CLOSERstats endpoint (HTTP '.$response->status().')');
            }

            Cache::put('dialer_last_synced_at', now('America/New_York')->toDateTimeString(), 3600);

            return $response->body();
        });
    }

    protected function parseCloserStatsCsv(string $csv): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($csv));
        $rows  = array_map('str_getcsv', $lines);
        $rows  = array_slice($rows, 2);

        $agents = [];

        foreach ($rows as $row) {
            if (count($row) < 4) {
                continue;
            }

            [$agentRaw, $calls, $time, $average] = $row;
            $agentRaw = trim($agentRaw);

            if (str_starts_with($agentRaw, 'VDCL') || str_starts_with($agentRaw, 'TOTAL Agents')) {
                continue;
            }

            [$empId, $name] = array_pad(explode(' - ', $agentRaw, 2), 2, '');
            $empId = trim($empId);
            $name  = trim($name) ?: $agentRaw;

            $team = 'Direct';
            if (str_starts_with($empId, 'SLZ')) {
                $team = 'Sellerz';
                $name = trim(preg_replace('/^Sellerz\s*/i', '', $name));
            } elseif (str_starts_with($name, 'OUS ')) {
                $team = 'OUS';
                $name = trim(substr($name, 4));
            }

            $parts    = preg_split('/\s+/', $name);
            $initials = strtoupper(substr($parts[0] ?? $name, 0, 1).substr(end($parts), 0, 1));

            $agents[] = [
                'emp_id'        => $empId,
                'name'          => $name,
                'team'          => $team,
                'initials'      => $initials,
                'calls'         => (int) $calls,
                'time'          => $time,
                'avg_talk_time' => $average,
            ];
        }

        usort($agents, fn ($a, $b) => $b['calls'] <=> $a['calls']);

        return $agents;
    }

    protected function hmsToSeconds(string $hms): int
    {
        $parts = array_map('intval', explode(':', $hms));
        $parts = array_pad($parts, 3, 0);
        [$h, $m, $s] = array_slice($parts, -3);

        return ($h * 3600) + ($m * 60) + $s;
    }

    protected function secondsToHms(int $seconds): string
    {
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;

        return sprintf('%d:%02d:%02d', $h, $m, $s);
    }

    protected function percentChange(int|float $old, int|float $new): float
    {
        if ($old <= 0) {
            return $new > 0 ? 100.0 : 0.0;
        }

        return round((($new - $old) / $old) * 100, 1);
    }
}
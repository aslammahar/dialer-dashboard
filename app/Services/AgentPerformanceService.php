<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Fetches and parses VICIdial Agent Performance Detail Report
 * (AST_agent_performance_detail.php) for use in the SC Report.
 *
 * Key columns extracted per agent:
 *   name, user_group, calls, login_time, talkavg, xfer, sale
 */
class AgentPerformanceService
{
    protected string $baseUrl = 'https://jsonsd4.maxtelco.com/vicidial/AST_agent_performance_detail.php';

    protected int $cacheTtl;

    public function __construct()
    {
        $this->cacheTtl = (int) config('services.dialer.cache_ttl', 300);
    }

    /**
     * Returns per-agent performance data for the SC Report.
     * Indexed by lowercase agent name for easy matching.
     *
     * Each entry:
     *  [
     *    'name'        => string,
     *    'emp_id'      => string,
     *    'user_group'  => string,   // e.g. JSCsBB, JuniorCLs, SeniorCLs
     *    'calls'       => int,
     *    'login_time'  => string,   // H:MM:SS
     *    'talk_time'   => string,   // H:MM:SS total talk
     *    'talkavg'     => string,   // M:SS avg talk per call
     *    'xfer'        => int,      // transfers (Avatar calls for SCs)
     *    'sale'        => int,      // SALE dispositions
     *  ]
     */
    public function fetch(string $from, string $to): array
    {
        $cacheKey = "dialer:agentperf:{$from}:{$to}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($from, $to) {
            return $this->fetchAndParse($from, $to);
        });
    }

    protected function fetchAndParse(string $from, string $to): array
    {
        $html = $this->httpGet($from, $to);

        if (empty($html)) {
            return [];
        }

        return $this->parseHtml($html);
    }

    protected function httpGet(string $from, string $to): string
    {
        $username = config('services.dialer.username');
        $password = config('services.dialer.password');

        $params = [
            'DB'                  => 0,
            'query_date'          => $from,
            'query_time'          => '00:00:00',
            'end_date'            => $to,
            'end_time'            => '23:59:59',
            'group[]'             => '--ALL--',
            'user_group[]'        => '--ALL--',
            'users[]'             => '--ALL--',
            'report_display_type' => 'TEXT',
            'shift'               => '--',
            'SUBMIT'              => 'SUBMIT',
        ];

        $url = $this->baseUrl . '?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err    = curl_error($ch);
        curl_close($ch);

        if ($err) {
            Log::warning('AgentPerformanceService: cURL error', ['error' => $err]);
            return '';
        }

        if ($status !== 200) {
            Log::warning('AgentPerformanceService: HTTP error', ['status' => $status]);
            return '';
        }

        return $body ?: '';
    }

    /**
     * Parse the HTML pipe-delimited table from AST_agent_performance_detail.php
     *
     * Row format (all pipe-separated):
     * | NAME | USER | GROUP | USER_GROUP | CALLS | TIME | [extra] | PAUSE | PAUSAVG | WAIT | WAITAVG
     *   | TALK | TALKAVG | DISPO | DISPAVG | DEAD | DEADAVG | CUSTOMER | CUSTAVG
     *   | A | B | CAF | CALLBK | DAIR | DC | DEC | DNC | DNQ | DRC | LOA | LOS
     *   | N | NDM | NI | NP | OB | PS | SALE | XFER |
     *
     * Column indices (0-based after splitting on |):
     *  0  = padding
     *  1  = NAME
     *  2  = USER (link)
     *  3  = GROUP
     *  4  = USER_GROUP
     *  5  = CALLS
     *  6  = TIME (total login)
     *  7  = [sometimes extra or TALKAVG-derived]
     *  8  = PAUSE
     *  9  = PAUSAVG
     *  10 = WAIT
     *  11 = WAITAVG
     *  12 = TALK
     *  13 = TALKAVG
     *  ...
     *  last-2 = SALE
     *  last-1 = XFER
     */
    protected function parseHtml(string $html): array
    {
        $agents = [];

        // Find all pipe-delimited data rows: lines starting with |
        $lines = preg_split('/\r\n|\r|\n/', $html);

        foreach ($lines as $line) {
            $line = strip_tags($line);
            $line = html_entity_decode($line, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $trimmed = trim($line);

            // Data rows start and end with |
            if (! str_starts_with($trimmed, '|') || ! str_ends_with($trimmed, '|')) {
                continue;
            }

            $cols = array_map('trim', explode('|', $trimmed));
            // Remove first (empty) and last (empty) elements from split
            $cols = array_values(array_slice($cols, 1, -1));

            // Need at least 38 columns - skip header/separator lines
            if (count($cols) < 30) {
                continue;
            }

            $name      = trim($cols[0]);
            $empId     = trim($cols[1]);
            $userGroup = trim($cols[3]);  // e.g. JSCsBB, JuniorCLs, SeniorCLs
            $calls     = (int) ($cols[4] ?? 0);
            $loginTime = trim($cols[5] ?? '0:00:00');

            // Skip header rows and separator rows
            if (! is_numeric($calls) && ! ctype_digit((string)$calls)) {
                continue;
            }
            if (str_contains($name, '---') || strtolower($name) === 'name' || empty($name)) {
                continue;
            }

            // TALK is col 11, TALKAVG is col 12
            $talkTime = trim($cols[11] ?? '0:00:00');
            $talkAvg  = trim($cols[12] ?? '0:00:00');

            // Last two cols before the empty closing are SALE, XFER
            $colCount = count($cols);
            $sale = (int) ($cols[$colCount - 2] ?? 0);
            $xfer = (int) ($cols[$colCount - 1] ?? 0);

            $nameKey = strtolower($name);

            // If same agent appears multiple times (date breakdown), aggregate
            if (isset($agents[$nameKey])) {
                $agents[$nameKey]['calls']    += $calls;
                $agents[$nameKey]['sale']     += $sale;
                $agents[$nameKey]['xfer']     += $xfer;
                // Sum login seconds, reformat later
                $agents[$nameKey]['login_seconds'] += $this->hmsToSeconds($loginTime);
                $agents[$nameKey]['talk_seconds']  += $this->hmsToSeconds($talkTime);
            } else {
                $agents[$nameKey] = [
                    'name'          => $name,
                    'emp_id'        => $empId,
                    'user_group'    => $userGroup,
                    'calls'         => $calls,
                    'login_seconds' => $this->hmsToSeconds($loginTime),
                    'login_time'    => $loginTime,
                    'talk_seconds'  => $this->hmsToSeconds($talkTime),
                    'talk_time'     => $talkTime,
                    'talkavg'       => $talkAvg,
                    'sale'          => $sale,
                    'xfer'          => $xfer,
                ];
            }
        }

        // Re-format times after aggregation
        foreach ($agents as &$agent) {
            $agent['login_time'] = $this->secondsToHms($agent['login_seconds']);
            $agent['talk_time']  = $this->secondsToHms($agent['talk_seconds']);
            $agent['talkavg']    = $agent['calls'] > 0
                ? $this->secondsToHms(intdiv($agent['talk_seconds'], $agent['calls']))
                : '0:00:00';
        }
        unset($agent);

        return $agents;
    }

    protected function hmsToSeconds(string $hms): int
    {
        $hms = trim($hms);
        if (empty($hms) || $hms === '-') return 0;
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
}

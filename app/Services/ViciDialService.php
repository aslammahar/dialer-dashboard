<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ViciDialService
{
    protected int $timeout;
    protected array $baseUrls;
    protected string $user;
    protected string $pass;

    public function __construct()
    {
        $this->baseUrls = config('services.vicidial.base_urls', []);
        $this->user = config('services.vicidial.user');
        $this->pass = config('services.vicidial.pass');
        $this->timeout = (int) config('services.vicidial.timeout', 15);

        if (empty($this->baseUrls[4]) || empty($this->user) || empty($this->pass)) {
            throw new RuntimeException('VICIdial service is not configured. Please set VICIDIAL_BASE_URL_4, VICIDIAL_USER, and VICIDIAL_PASS in your .env file.');
        }
    }

    public function getAgentDetails(string $agentUser, string $stage = 'csv', string $source = 'test', int $dialer = 4): array
    {
        $query = [
            'source' => $source,
            'user' => $this->user,
            'pass' => $this->pass,
            'function' => 'user_details',
            'agent_user' => $agentUser,
            'stage' => $stage,
            'header' => 'YES',
        ];

        return $this->request($query, $stage, $dialer);
    }

    public function getAgentStats(string $startDate, string $endDate, string $stage = 'csv', string $source = 'crm', int $dialer = 4, string $groupByCampaign = 'YES'): array
    {
        $query = [
            'source' => $source,
            'user' => $this->user,
            'pass' => $this->pass,
            'function' => 'agent_stats_export',
            'datetime_start' => $this->normalizeDatetime($startDate),
            'datetime_end' => $this->normalizeDatetime($endDate),
            'stage' => $stage,
            'header' => 'YES',
            'group_by_campaign' => $groupByCampaign,
        ];

        return $this->request($query, $stage, $dialer);
    }

    protected function normalizeDatetime(string $datetime): string
    {
        return trim($datetime);
    }

    protected function getBaseUrlForDialer(int $dialer): string
    {
        $baseUrl = $this->baseUrls[$dialer] ?? null;

        if (empty($baseUrl)) {
            throw new RuntimeException("VICIdial base URL for dialer {$dialer} is not configured.");
        }

        return $baseUrl;
    }

    protected function createClient(string $baseUrl): Client
    {
        return new Client([
            'base_uri' => $this->getBaseUrlWithoutQuery($baseUrl),
            'timeout' => $this->timeout,
        ]);
    }

    protected function getBaseUrlWithoutQuery(string $baseUrl): string
    {
        $parts = parse_url($baseUrl);

        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            throw new RuntimeException('Invalid VICIdial base URL configured.');
        }

        $uri = sprintf('%s://%s', $parts['scheme'], $parts['host']);

        if (!empty($parts['port'])) {
            $uri .= ':' . $parts['port'];
        }

        $uri .= $parts['path'] ?? '';

        return $uri;
    }

    protected function extractQueryFromBaseUrl(string $baseUrl): array
    {
        $parts = parse_url($baseUrl);

        if ($parts === false || empty($parts['query'])) {
            return [];
        }

        parse_str($parts['query'], $query);

        return $query;
    }

    protected function request(array $queryParams, string $stage = 'json', int $dialer = 4): array
    {
        $baseUrl = $this->getBaseUrlForDialer($dialer);
        $queryParams = array_merge($this->extractQueryFromBaseUrl($baseUrl), $queryParams);
        $client = $this->createClient($baseUrl);

        try {
            $response = $client->request('GET', '', [
                'query' => $queryParams,
            ]);

            $body = trim((string) $response->getBody());

            if ($stage === 'csv') {
                return [
                    'raw' => $body,
                    'data' => $this->parseCsv($body),
                ];
            }

            $decoded = json_decode($body, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return [
                    'raw' => $body,
                    'data' => $decoded,
                ];
            }

            if ($this->looksLikeCsv($body)) {
                Log::warning('VICIdial returned CSV when JSON was expected', [
                    'query' => $queryParams,
                    'sample' => substr($body, 0, 200),
                ]);

                return [
                    'raw' => $body,
                    'data' => $this->parseCsv($body),
                ];
            }

            throw new RuntimeException('Failed to parse VICIdial JSON response: ' . json_last_error_msg());
        } catch (GuzzleException $exception) {
            Log::error('VICIdial HTTP request failed', [
                'message' => $exception->getMessage(),
                'query' => $queryParams,
            ]);

            throw new RuntimeException('Failed to contact VICIdial service.');
        }
    }

    protected function looksLikeCsv(string $body): bool
    {
        $lines = array_values(array_filter(array_map('trim', explode("\n", $body)), fn ($line) => $line !== ''));
        if (count($lines) < 2) {
            return false;
        }

        return str_contains($lines[0], ',') && str_contains($lines[1], ',');
    }

    protected function parseCsv(string $csvContent): array
    {
        $lines = array_filter(array_map('trim', explode("\n", trim($csvContent))), fn ($line) => $line !== '');

        if (empty($lines)) {
            return [];
        }

        $rows = array_map(fn ($line) => str_getcsv($line), $lines);
        $headers = array_shift($rows);

        if (empty($headers)) {
            return [];
        }

        return array_map(function ($row) use ($headers) {
            return array_combine($headers, array_pad($row, count($headers), null));
        }, $rows);
    }
}

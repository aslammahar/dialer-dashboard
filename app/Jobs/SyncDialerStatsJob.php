<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncDialerStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // TODO once Dialer API (Inbound V2) is connected:
        // 1. Call app(\App\Services\DialerApiService::class)->pullCallLogs()
        // 2. Upsert into a dialer_call_logs table (call_id, agent_id, group,
        //    started_at, ended_at, talk_time_seconds, direction, status)
        // 3. Recompute and cache daily/monthly averages so the dashboard
        //    controller can read from cache instead of hitting the API on
        //    every page load.

        Log::info('SyncDialerStatsJob ran (placeholder, no live data source yet).');
    }
}
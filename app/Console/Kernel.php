<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SyncDialerStatsJob;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('backup:avatar_leads')->hourly();
        $schedule->command('recruitment:send-backup')->dailyAt('11:30');
        $schedule->command('closedcall:send-backup')->dailyAt('12:00');
        $schedule->command('attandance:send-backup')->dailyAt('12:00');
        // $schedule->command('leads:fix-double-encryption')->dailyAt('12:00');
        $schedule->command('report:send-daily-duplicates')->dailyAt('12:05'); // 6 PM daily
        // $schedule->command('report:daily-leads')->dailyAt('18:00'); // 6 PM daily
        
        // Download recordings for today every ten minutes
        $schedule->command('recordings:download-range')->everyTenMinutes();

         $schedule->job(new SyncDialerStatsJob())->hourly();
         
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

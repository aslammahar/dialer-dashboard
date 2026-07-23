<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendDailyDuplicatesReportJob;

class SendDailyDuplicatesCommand extends Command
{
    protected $signature = 'report:send-daily-duplicates';
    protected $description = 'Dispatch job to send daily duplicate leads report via email';

    public function handle()
    {
        SendDailyDuplicatesReportJob::dispatch();
        $this->info('Daily duplicates report job dispatched successfully.');
        return Command::SUCCESS;
    }
}

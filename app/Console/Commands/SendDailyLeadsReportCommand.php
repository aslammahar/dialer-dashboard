<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendDailyLeadsReportJob;

class SendDailyLeadsReportCommand extends Command
{
    protected $signature = 'report:daily-leads';
    protected $description = 'Send daily leads report via email';

    public function handle()
    {
        dispatch(new SendDailyLeadsReportJob());
        $this->info('Daily leads report job dispatched successfully.');
        return self::SUCCESS;
    }
}

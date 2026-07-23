<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class BackfillPhoneHash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:backfill-phone-hash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill phone_hash for old leads';

    public function handle()
    {
        DB::table('avatar_leads')->orderBy('id')->chunk(1000, function ($leads) {
            foreach ($leads as $lead) {
                try {
                    $phone = Crypt::decryptString($lead->phone_number);
                    $hash  = hash('sha256', $phone);

                    DB::table('avatar_leads')
                        ->where('id', $lead->id)
                        ->update(['phone_hash' => $hash]);
                } catch (\Exception $e) {
                    $this->error("Failed decrypting lead ID {$lead->id}");
                }
            }
            $this->info("Processed 1000 leads...");
        });

        $this->info("Backfill complete!");
    }
}

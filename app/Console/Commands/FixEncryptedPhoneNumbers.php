<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AvatarLead;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class FixEncryptedPhoneNumbers extends Command
{
    protected $signature = 'leads:fix-double-encryption
                            {--start-date= : Start date in Y-m-d format (default: today)}
                            {--end-date= : End date in Y-m-d format (default: 2 days ago)}';
    protected $description = 'Fix leads with double-encrypted phone numbers created on or after 2025-09-05';

    public function handle()
    {
        $startDateInput = $this->option('start-date');
        $endDateInput = $this->option('end-date');

        if (!empty($startDateInput)) {
            try {
                $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $startDateInput)->startOfDay();
            } catch (\Exception $e) {
                return $this->error('Invalid --start-date format. Use Y-m-d, for example: 2026-03-31');
            }
        } else {
            $startDate = \Carbon\Carbon::now()->startOfDay(); // default: today
        }

        if (!empty($endDateInput)) {
            try {
                $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $endDateInput)->startOfDay();
            } catch (\Exception $e) {
                return $this->error('Invalid --end-date format. Use Y-m-d, for example: 2026-03-31');
            }
        } else {
            $endDate = \Carbon\Carbon::now()->subDays(2)->startOfDay(); // default: day before yesterday
        }

        if ($startDate->lt($endDate)) {
            return $this->error('--start-date must be today/later than --end-date for this backward day-by-day scan.');
        }

        $this->info("Scanning leads day by day from {$startDate->toDateString()} down to {$endDate->toDateString()}...");

        $totalFixed = 0;

        for ($date = $startDate; $date->gte($endDate); $date->subDay()) {
            $this->info("📅 Processing {$date->toDateString()}...");

            $leads = AvatarLead::whereDate('created_at', $date->toDateString())->get();
            $fixed = 0;

            foreach ($leads as $lead) {
                try {
                    $originalValue = $lead->getRawOriginal('phone_number');

                    // First decryption
                    $firstDecrypt = Crypt::decryptString($originalValue);

                    // Try second decryption
                    $secondDecrypt = null;
                    try {
                        $secondDecrypt = Crypt::decryptString($firstDecrypt);
                    } catch (\Exception $e) {
                        // Not double encrypted, ignore
                    }

                    if ($secondDecrypt) {
                        $decryptedPhone = $secondDecrypt;

                        $lead->phone_number = $decryptedPhone;
                        $lead->phone_hash   = hash('sha256', $decryptedPhone);
                        $lead->save();

                        $this->line("   ✅ Fixed lead #{$lead->id} → {$decryptedPhone}");
                        $fixed++;
                    }
                } catch (\Exception $e) {
                    Log::error("Failed fixing lead #{$lead->id}: " . $e->getMessage());
                }
            }

            $this->info("   → Fixed {$fixed} leads for {$date->toDateString()}.\n");
            $totalFixed += $fixed;
        }

        $this->info("🎉 Done! Total fixed across all days: {$totalFixed}");
    }

    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

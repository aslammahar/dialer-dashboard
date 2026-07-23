<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AvatarLead;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\AvatarLeadsExported;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportAvatarLeads extends Command
{
    protected $signature = 'export:avatar-leads
                            {--start-date= : Start date in Y-m-d format (e.g. 2025-08-04)}
                            {--end-date= : End date in Y-m-d format (default: today)}';
    protected $description = 'Export avatar_leads by date range with decrypted phone numbers';

    public function handle()
    {
        try {
            $startDateInput = $this->option('start-date');
            $endDateInput = $this->option('end-date');

            if (empty($startDateInput)) {
                return $this->error('Please provide --start-date in Y-m-d format. Example: php artisan export:avatar-leads --start-date=2025-08-04');
            }

            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $startDateInput)->startOfDay();
            } catch (\Exception $e) {
                return $this->error('Invalid --start-date format. Use Y-m-d, for example: 2025-08-04');
            }

            if (!empty($endDateInput)) {
                try {
                    $endDate = Carbon::createFromFormat('Y-m-d', $endDateInput)->endOfDay();
                } catch (\Exception $e) {
                    return $this->error('Invalid --end-date format. Use Y-m-d, for example: 2026-03-31');
                }
            } else {
                $endDate = Carbon::now()->endOfDay();
            }

            if ($startDate->gt($endDate)) {
                return $this->error('--start-date cannot be greater than --end-date.');
            }

            $this->info("Fetching leads between $startDate and $endDate...");

            $leads = AvatarLead::whereBetween('created_at', [$startDate, $endDate])
                ->get(['lead_id', 'phone_number', 'dialer_id', 'recording_link', 'QAstatus', 'Qacomments', 'created_at']);

            if ($leads->isEmpty()) {
                $this->info('No leads found in the date range.');
                return;
            }

            // Decrypt phone numbers exactly once from raw DB value.
            $avatarLeadsData = $leads->map(function ($lead) {
                $rawPhone = $lead->getRawOriginal('phone_number');

                if (!empty($rawPhone)) {
                    try {
                        $lead->phone_number = Crypt::decryptString($rawPhone);
                    } catch (\Exception $e) {
                        \Log::error("Error decrypting phone number (ID: {$lead->id}): " . $e->getMessage());
                        $lead->phone_number = 'Invalid/Corrupted';
                    }
                } else {
                    $lead->phone_number = 'Empty';
                }

                return $lead;
            })->values(); // Optional: re-index collection

            $fileName = 'avatar_leads_export_' . now()->format('Y_m_d_His') . '.xlsx';

            // Export to file
            $filePath = 'backups/' . $fileName;
            Excel::store(new class($avatarLeadsData) implements FromCollection, WithHeadings {
                protected $data;
                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function collection()
                {
                    return $this->data;
                }

                public function headings(): array
                {
                    return ['lead_id', 'phone_number', 'dialer_id', 'recording_link', 'QAstatus', 'Qacomments', 'created_at'];
                }
            }, $filePath);

            $this->info("✅ Export complete: storage/app/$filePath");

            // Send the email with the exported file as attachment
            Mail::to('jamilahmad7666@gmail.com')->send(new AvatarLeadsExported($filePath));

            $this->info("✅ Email sent to jamilahmad7666@gmail.com with the export file.");
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
    }
}

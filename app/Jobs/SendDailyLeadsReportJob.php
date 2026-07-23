<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\AvatarLead;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailyLeadsExport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class SendDailyLeadsReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private function tryDecrypt($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value; // return as-is if not encrypted
        }
    }

    public function handle(): void
    {
        $today = now()->toDateString();

        $leads = AvatarLead::select(
            DB::raw('MIN(id) as id'),
            DB::raw('MIN(lead_id) as lead_id'),
            'phone_number',
            DB::raw('COUNT(*) as total_count'),
            DB::raw('SUM(QAstatus = "approved") as approved_count')
        )
            ->whereDate('created_at', $today)
            ->groupBy('phone_number')
            ->get()
            ->map(function ($lead) {
                $lead->phone_number = $this->tryDecrypt($lead->phone_number);
                return $lead;
            });



        // \Log::info('Sample decrypted leads for today', $leads->toArray());

        $fileName = 'daily_leads_' . $today . '.xlsx';
        Storage::makeDirectory('phone_number_records');

        Excel::store(
            new DailyLeadsExport($leads),
            'phone_number_records/' . $fileName,
            'local'
        );

        $filePath = storage_path('phone_number_records/' . $fileName);

        Mail::raw(
            'Please find the daily leads report attached.',
            function ($message) use ($filePath, $today) {
                $message->to('msohaib97@gmail.com')
                    ->subject('Daily Leads Report - ' . $today)
                    ->attach($filePath);
            }
        );
    }
}

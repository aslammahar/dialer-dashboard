<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailyDuplicatesExport;
use App\Mail\DailyDuplicatesReport;
use Illuminate\Support\Facades\Log;

class SendDailyDuplicatesReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;



    // public function handle()
    // {
    //     // Get last 40 leads
    //     $testLeads = DB::table('avatar_leads')
    //         ->orderByDesc('id')
    //         ->limit(40)
    //         ->get();

    //     echo "Testing decryption for last 40 leads...\n";

    //     foreach ($testLeads as $lead) {
    //         try {
    //             $plain = Crypt::decryptString($lead->phone_number);
    //             echo "✅ Lead #{$lead->id} → {$plain}\n";
    //         } catch (\Exception $e) {
    //             echo "❌ Lead #{$lead->id} failed decryption\n";
    //             echo "   Raw: {$lead->phone_number}\n";
    //             echo "   Error: {$e->getMessage()}\n";
    //         }
    //     }

    //     echo "\n--- Your duplicates report code will continue here ---\n";
    // }



    public function handle()
    {
        // $today = Carbon::today(); this should yesterday
        $today = Carbon::yesterday(); // it should be yesterday

        $rows = DB::table('avatar_leads as today')
            ->leftJoin('avatar_leads as prev', function ($join) use ($today) {
                $join->on('today.phone_hash', '=', 'prev.phone_hash')
                    ->where('prev.created_at', '<', $today);
            })
            ->whereDate('today.created_at', $today)
            ->select(
                'today.id',
                'today.phone_number',
                DB::raw('COALESCE(COUNT(prev.id), 0) as previous_count')
            )
            ->groupBy('today.id', 'today.phone_number')
            ->get();

        $report = $rows->map(function ($row) {
            try {
                $phone = Crypt::decryptString($row->phone_number);
            } catch (\Exception $e) {
                $phone = 'DECRYPT_ERROR';
            }

            return [
                'lead_id'        => $row->id,
                'phone_number'   => $phone,
                'previous_count' => $row->previous_count ?? 0,
            ];
        });
        Log::info('Daily duplicates sample', $report->take(5)->toArray());

        $fileName = 'daily_duplicates_' . $today->format('Y_m_d') . '.xlsx';
        Excel::store(new DailyDuplicatesExport($report), $fileName, 'local');
        $filePath = storage_path("$fileName");

        Mail::to('msohaib97@gmail.com')->send(new DailyDuplicatesReport($filePath));
        Mail::to('jamilahmad7666@gmail.com')->send(new DailyDuplicatesReport($filePath));
    }
}

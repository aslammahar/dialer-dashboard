<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\ClosedCallExport;
use App\Mail\ClosedCallBackupMail;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class SendClosedCallFullExport extends Command
{
    protected $signature = 'closedcall:send-full-export';
    protected $description = 'Send all ClosedCall records (pending, charged_backed, NSF, rejected) as Excel to jamilahmadd7666@gmail.com';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info('Starting ClosedCall full export (all data for statuses: pending, charged_backed, NSF, rejected)...');

            $fileName = 'closedcall_full_export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            // System export: fixed statuses, no limit – gets all records for those statuses
            $export = new ClosedCallExport(null, PHP_INT_MAX, [], true);
            
            // Store the export file
            Excel::store($export, 'backup/' . $fileName);
            
            $this->info('Excel file generated: ' . $fileName);
            
            // Send email to jamilahmadd7666@gmail.com
            Mail::to('jamilahmad7666@gmail.com')->send(new ClosedCallBackupMail($fileName));
            
            $this->info('ClosedCall full export sent successfully to jamilahmadd7666@gmail.com!');
            
            // Log the export
            Log::info('ClosedCall full export sent', [
                'email' => 'jamilahmadd7666@gmail.com',
                'file' => $fileName,
                'timestamp' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
            Log::error('ClosedCall full export failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'email' => 'jamilahmad7666@gmail.com'
            ]);
            return 1; // Return error code
        }
        
        return 0; // Success
    }
}


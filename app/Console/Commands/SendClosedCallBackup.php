<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\ClosedCallExport;
use App\Mail\ClosedCallBackupMail;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendClosedCallBackup extends Command
{
    protected $signature = 'closedcall:send-backup';
    protected $description = 'Send the last 500 ClosedCall records as an Excel file to email';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            // System export - pass isSystemExport = true to bypass user authorization
            // This is safe because it's only called from console commands (scheduled tasks)
            $fileName = 'closedcall_backup_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            // Create export with system export flag (no user context needed for scheduled backups)
            $export = new ClosedCallExport(null, 1000, [], true);
            
            Excel::store($export, 'backup/' . $fileName);
            Mail::to('jamilahmad7666@gmail.com')->send(new ClosedCallBackupMail($fileName));
            $this->info('ClosedCall backup sent successfully!');
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
            \Log::error('ClosedCall backup failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\ClosedCallExportByCenter;
use App\Mail\ClosedCallBackupMail;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class SendClosedCallExportByCenter extends Command
{
    protected $signature = 'closedcall:export-by-center {center_name}';
    protected $description = 'Export all ClosedCall records filtered by Center Name and send as Excel file to jamilahmadd7666@gmail.com';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $centerName = $this->argument('center_name');
            
            if (empty($centerName)) {
                $this->error('Center Name is required!');
                $this->info('Usage: php artisan closedcall:export-by-center "Center Name"');
                return Command::FAILURE;
            }
            
            $this->info("Starting ClosedCall export for Center: {$centerName}...");
            
            // System export - pass isSystemExport = true to bypass user authorization
            // This is safe because it's only called from console commands
            $fileName = 'closedcall_export_' . str_replace(' ', '_', $centerName) . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            // Create export with system export flag, center name filter, and very high limit to get all records
            // Using PHP_INT_MAX to ensure we get all records
            $export = new ClosedCallExportByCenter($centerName, PHP_INT_MAX, [], true);
            
            // Store the export file
            Excel::store($export, 'backup/' . $fileName);
            
            $this->info('Excel file generated: ' . $fileName);
            
            // Send email to jamilahmadd7666@gmail.com
            Mail::to('jamilahmad7666@gmail.com')->send(new ClosedCallBackupMail($fileName));
            
            $this->info("ClosedCall export for Center '{$centerName}' sent successfully to jamilahmadd7666@gmail.com!");
            
            // Log the export
            Log::info('ClosedCall export by center sent', [
                'center_name' => $centerName,
                'email' => 'jamilahmadd7666@gmail.com',
                'file' => $fileName,
                'record_count' => $export->getRecordCount(),
                'timestamp' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
            Log::error('ClosedCall export by center failed: ' . $e->getMessage(), [
                'center_name' => $this->argument('center_name') ?? 'N/A',
                'trace' => $e->getTraceAsString(),
                'email' => 'jamilahmad7666@gmail.com'
            ]);
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}

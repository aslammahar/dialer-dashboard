<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\RecruitmentExport;
use App\Mail\RecruitmentBackupMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SendRecruitmentBackup extends Command
{
    protected $signature = 'recruitment:send-backup';
    protected $description = 'Send the last 500 recruitment records as an Excel file to email';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            // Create filename with timestamp
            $fileName = 'recruitment_backup_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            // Store the Excel file in storage/app/backup
            Excel::store(new RecruitmentExport, 'backup/' . $fileName);

            // Send the email with attachment
            Mail::to('M.sohaib97@gmail.com')->send(new RecruitmentBackupMail($fileName));

            $this->info('Backup sent successfully!');
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
        }
    }
}

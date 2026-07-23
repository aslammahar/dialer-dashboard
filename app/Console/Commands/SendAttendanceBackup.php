<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\AttendanceExport;
use App\Mail\AttendanceBackupMail;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
class SendAttendanceBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attandance:send-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    
    public function handle()
    {
        try {
            $fileName = 'attendance_backup_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            Excel::store(new AttendanceExport, 'backup/' . $fileName);
            Mail::to('M.sohaib97@gmail.com')->send(new AttendanceBackupMail($fileName));
            $this->info('Attendance backup sent successfully!');
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\AvatarLead;
use Illuminate\Support\Facades\Mail;
use App\Mail\AvatarLeadsBackupMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BackupAvatarLeads extends Command
{
    protected $signature = 'backup:avatar_leads';
    protected $description = 'Create a backup of the avatar_leads table and email it as a CSV file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            // Fetch data from avatar_leads table for the current month
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $avatarLeadsData = AvatarLead::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->select([
                    'id', 'lead_id', 'phone_number', 'dialer_id', 'agent_name', 'recording_link', 'centername',
                    'Isgreetings', 'Ispitch_call_about', 'Isage', 'Issmoker', 'Ishealth1', 'Isbeneficiary',
                    'Isaccount', 'Isplan', 'Istransfer_details', 'Isxfer_consent', 'rebuttals',
                    'Qacomments', 'QAstatus', 'QapersonId', 'created_at', 'updated_at'
                ])
                ->get();

            if ($avatarLeadsData->isEmpty()) {
                $this->info('No avatar leads found for the current month.');
                return;
            }

            // Convert data to CSV format
            $csvData = $this->convertToCsv($avatarLeadsData);

            // Save CSV file
            $csvFileName = 'avatar_leads_backup_' . now()->format('Y-m-d_H-i-s') . '.csv';
            Storage::put('backup/' . $csvFileName, $csvData);

            // Send email with attachment
            // $recipients = ['jamilahmad7666@gmail.com'];
            $recipients = ['Jsonsrealtime@gmail.com', 'rafia.ameerkhan092@gmail.com'];

            // Send email with attachment to multiple recipients
            Mail::to($recipients)->send(new AvatarLeadsBackupMail($csvFileName));

            // Inform user that the command was successful
            $this->info('Avatar leads backup created and emailed successfully.');
        } catch (\Exception $e) {
            Log::error('Error sending backup email: ' . $e->getMessage());
            $this->error('Failed to send backup email.');
        }
    }

    private function convertToCsv($data)
    {
        $csvData = '';
        if ($data->isNotEmpty()) {
            // Extract raw attributes to avoid decrypting the phone_number
            $columns = array_keys($data->first()->getAttributes());

            // Add the headers
            $csvData .= implode(',', $columns) . "\n";

            // Add the data
            foreach ($data as $row) {
                $attributes = $row->getAttributes();
                $csvData .= implode(',', array_map(function ($value) {
                    // Enclose in double quotes and escape any double quotes within the value
                    return '"' . str_replace('"', '""', $value) . '"';
                }, $attributes)) . "\n";
            }
        }

        return $csvData;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Recording;
use App\Models\DialersList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttachDialerRecordingLinkToRecordings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recordings:attach-dialer-link {--start-date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach dialerlist_tb recording_link to recordings recording_link by matching server_ip with dialer_ip';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to attach dialer recording links to recordings...');
        
        // Get start date from option or default to today
        $startDate = $this->option('start-date');
        
        if (empty($startDate)) {
            $startDate = Carbon::today()->format('Y-m-d');
        }
        
        // Validate date format
        try {
            $cutoffDate = Carbon::parse($startDate)->startOfDay();
        } catch (\Exception $e) {
            $this->error('Invalid date format. Please use YYYY-MM-DD format (e.g., 2026-01-24)');
            return Command::FAILURE;
        }
        
        $this->info("Processing recordings created on or after: {$startDate}");
        
        // Get all dialerlist_tb records with dialer_ip and recording_link
        $dialerLists = DialersList::whereNotNull('dialer_ip')
            ->whereNotNull('recording_link')
            ->get(['dialer_ip', 'recording_link']);
        
        if ($dialerLists->isEmpty()) {
            $this->warn('No dialer lists found with dialer_ip and recording_link.');
            return Command::SUCCESS;
        }
        
        $this->info("Found {$dialerLists->count()} dialer list(s) with recording links.");
        
        // Create a map for quick lookup: dialer_ip => recording_link
        $dialerIpMap = [];
        foreach ($dialerLists as $dialer) {
            $dialerIpMap[$dialer->dialer_ip] = $dialer->recording_link;
        }
        
        // Get recordings where created_at >= start date and status != 'downloaded'
        $recordings = Recording::where('created_at', '>=', $cutoffDate)
            ->where(function ($query) {
                $query->whereNull('status')
                      ->orWhere('status', '!=', 'downloaded');
            })
            ->whereNotNull('server_ip')
            ->whereNotNull('recording_link')
            ->get();
        
        $this->info("Found {$recordings->count()} recording(s) to process (created_at >= {$startDate}, status != 'downloaded').");
        
        if ($recordings->isEmpty()) {
            $this->warn('No recordings found matching the criteria.');
            return Command::SUCCESS;
        }
        
        $matched = 0;
        $updated = 0;
        $failed = 0;
        
        $progressBar = $this->output->createProgressBar($recordings->count());
        $progressBar->start();
        
        foreach ($recordings as $recording) {
            try {
                // Match server_ip with dialer_ip
                if (isset($dialerIpMap[$recording->server_ip])) {
                    $matched++;
                    
                    $dialerRecordingLink = trim($dialerIpMap[$recording->server_ip]);
                    $currentRecordingLink = trim($recording->recording_link);
                    
                    // Prepend dialer recording_link to the recordings recording_link
                    // Ensure proper URL concatenation (handle trailing/leading slashes)
                    $dialerLink = rtrim($dialerRecordingLink, '/');
                    $recordingLink = ltrim($currentRecordingLink, '/');
                    
                    $newRecordingLink = $dialerLink . '/' . $recordingLink;
                    
                    // Update the recording
                    $recording->recording_link = $newRecordingLink;
                    $recording->save();
                    
                    $updated++;
                    
                    Log::info("Attached dialer recording link to recording", [
                        'recording_id' => $recording->id,
                        'server_ip' => $recording->server_ip,
                        'dialer_ip' => $recording->server_ip,
                        'dialer_recording_link' => $dialerRecordingLink,
                        'original_recording_link' => $currentRecordingLink,
                        'new_recording_link' => $newRecordingLink
                    ]);
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error("Failed to attach dialer recording link to recording ID {$recording->id}", [
                    'error' => $e->getMessage(),
                    'recording_id' => $recording->id,
                    'server_ip' => $recording->server_ip ?? 'N/A'
                ]);
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Display results
        $this->info("Processing Statistics:");
        $this->line("Total recordings processed: {$recordings->count()}");
        $this->line("Matched with dialer IP: {$matched}");
        $this->line("Successfully updated: {$updated}");
        $this->line("Failed: {$failed}");
        
        if ($updated > 0) {
            $this->info("✓ Successfully attached dialer recording links to {$updated} recording(s).");
        }
        
        return Command::SUCCESS;
    }
}

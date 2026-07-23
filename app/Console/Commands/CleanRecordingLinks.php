<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Recording;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanRecordingLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recordings:clean-links {--start-date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean duplicate URL prefixes in recording_link by trimming everything before /RECORDINGS/';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to clean recording links...');
        
        // Get start date from option or default to 2026-01-26
        $startDate = $this->option('start-date');
        
        if (empty($startDate)) {
            $startDate = '2026-01-26';
        }
        
        // Validate date format
        try {
            $cutoffDate = Carbon::parse($startDate)->startOfDay();
        } catch (\Exception $e) {
            $this->error('Invalid date format. Please use YYYY-MM-DD format (e.g., 2026-01-26)');
            return Command::FAILURE;
        }
        
        $this->info("Processing recordings created on or after: {$startDate}");
        $this->info("Status filter: rec missing");
        
        // Get recordings where status = 'rec missing' and created_at >= start date
        $recordings = Recording::where('status', 'rec missing')
            ->where('created_at', '>=', $cutoffDate)
            ->whereNotNull('recording_link')
            ->get();
        
        $this->info("Found {$recordings->count()} recording(s) to process.");
        
        if ($recordings->isEmpty()) {
            $this->warn('No recordings found matching the criteria.');
            return Command::SUCCESS;
        }
        
        $updated = 0;
        $skipped = 0;
        $failed = 0;
        
        $progressBar = $this->output->createProgressBar($recordings->count());
        $progressBar->start();
        
        foreach ($recordings as $recording) {
            try {
                $originalLink = trim($recording->recording_link);
                
                // Find the position of /RECORDINGS/ in the string
                $recordingsPos = strpos($originalLink, '/RECORDINGS/');
                
                if ($recordingsPos !== false) {
                    // Extract everything from /RECORDINGS/ onwards
                    $cleanedLink = substr($originalLink, $recordingsPos);
                    
                    // Only update if the link actually changed
                    if ($cleanedLink !== $originalLink) {
                        $recording->recording_link = $cleanedLink;
                        $recording->save();
                        
                        $updated++;
                        
                        Log::info("Cleaned recording link", [
                            'recording_id' => $recording->id,
                            'original_link' => $originalLink,
                            'cleaned_link' => $cleanedLink
                        ]);
                    } else {
                        $skipped++;
                    }
                } else {
                    // No /RECORDINGS/ found, skip this record
                    $skipped++;
                    Log::warning("No /RECORDINGS/ found in recording link", [
                        'recording_id' => $recording->id,
                        'recording_link' => $originalLink
                    ]);
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error("Failed to clean recording link for recording ID {$recording->id}", [
                    'error' => $e->getMessage(),
                    'recording_id' => $recording->id,
                    'recording_link' => $recording->recording_link ?? 'N/A'
                ]);
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Display results
        $this->info("Processing Statistics:");
        $this->line("Total recordings processed: {$recordings->count()}");
        $this->line("Successfully updated: {$updated}");
        $this->line("Skipped (no change needed or no /RECORDINGS/ found): {$skipped}");
        $this->line("Failed: {$failed}");
        
        if ($updated > 0) {
            $this->info("✓ Successfully cleaned {$updated} recording link(s).");
        }
        
        return Command::SUCCESS;
    }
}

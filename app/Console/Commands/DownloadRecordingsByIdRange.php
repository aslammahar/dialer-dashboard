<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Recording;
use App\Models\AvatarLead;
use App\Models\DialersUnified;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DownloadRecordingsByIdRange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recordings:download-range {--start-date=} {--connect-timeout=5} {--timeout=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download recordings from recordings table for a specific date (defaults: today), update avatar_leads';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Default to today if not provided
        $startDate = $this->option('start-date');
        
        // Set default: today
        if (empty($startDate)) {
            $startDate = \Carbon\Carbon::today()->format('Y-m-d');
        }
        
        // Validate date format
        try {
            $startDateObj = \Carbon\Carbon::parse($startDate)->startOfDay();
            $endDateObj = \Carbon\Carbon::parse($startDate)->endOfDay();
        } catch (\Exception $e) {
            $this->error('Invalid date format. Please use YYYY-MM-DD format (e.g., 2026-01-19)');
            return Command::FAILURE;
        }
        
        $this->info("Downloading recordings created on {$startDate}...");
        
        // Get recordings for the specific date where status is not 'downloaded' (includes failed for retry)
        $recordings = Recording::whereBetween('created_at', [$startDateObj, $endDateObj])
            ->where(function ($query) {
                $query->whereNotNull('recording_link')
                    ->orWhere(function ($q) {
                        $q->whereNotNull('server_ip')->whereNotNull('recording_filename');
                    });
            })
            ->where(function ($query) {
                $query->whereNull('status')
                      ->orWhere('status', '!=', 'downloaded');
            })
            ->get();
        
        $this->info("Found {$recordings->count()} recordings to download.");
        
        if ($recordings->isEmpty()) {
            $this->warn('No recordings found in the specified range.');
            return Command::SUCCESS;
        }
        
        // Create storage directory
        $storagePath = 'recordings';
        $disk = Storage::disk('public');
        
        if (!$disk->exists($storagePath)) {
            $disk->makeDirectory($storagePath);
        }
        
        $downloaded = 0;
        $failed = 0;
        $skipped = 0;
        
        $progressBar = $this->output->createProgressBar($recordings->count());
        $progressBar->start();
        
        $connectTimeout = max(1, (int) $this->option('connect-timeout'));
        $timeout        = max(1, (int) $this->option('timeout'));

        foreach ($recordings as $recording) {
            try {
                // Use stored recording_link or build from dialers_unified if we have server_ip + recording_filename
                $recordingUrl = $this->resolveRecordingUrl($recording);
                if (empty($recordingUrl)) {
                    $this->markFailed($recording, 'no_url');
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                $recordingUrl = trim($recordingUrl);
                $recordingUrl = ltrim($recordingUrl, '/');

                // Persist resolved URL back to recording if it was built from dialers_unified
                if (empty($recording->recording_link)) {
                    $recording->recording_link = $recordingUrl;
                    $recording->save();
                }

                // Always update recording column in avatar_leads with original URL
                $this->updateAvatarLeadRecording($recording, $recordingUrl);

                // Validate URL (accepts both HTTP and HTTPS)
                if (!filter_var($recordingUrl, FILTER_VALIDATE_URL)) {
                    $this->markFailed($recording, 'invalid_url');
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Generate filename
                $filename = $this->generateFilename($recording);
                $filePath = $storagePath . '/' . $filename;

                // Download the file (fast-fail on unreachable hosts)
                $fileContent = $this->downloadFile($recordingUrl, $connectTimeout, $timeout);

                if ($fileContent === false) {
                    $this->markFailed($recording, 'download_failed');
                    $failed++;
                    $progressBar->advance();
                    continue;
                }

                // Validate it's an audio file
                if (!$this->isValidAudioFile($fileContent, $recordingUrl)) {
                    $this->markFailed($recording, 'invalid_audio');
                    $failed++;
                    $progressBar->advance();
                    continue;
                }

                // Save the file
                $disk->put($filePath, $fileContent);

                if ($disk->exists($filePath)) {
                    $downloaded++;

                    // Update recording status to 'downloaded'
                    $recording->status = 'downloaded';
                    $recording->save();

                    // Update avatar_leads table with the downloaded file path
                    $this->updateAvatarLeadRecordingLink($recording, $filePath);
                } else {
                    $this->markFailed($recording, 'write_failed');
                    $failed++;
                }

            } catch (\Exception $e) {
                $this->markFailed($recording, 'exception');
                $failed++;
                Log::error("Failed to download recording ID {$recording->id}: " . $e->getMessage());
            }

            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Display results
        $this->info("Download Statistics:");
        $this->line("Total found: {$recordings->count()}");
        $this->line("Successfully downloaded: {$downloaded}");
        $this->line("Failed: {$failed}");
        $this->line("Skipped: {$skipped}");
        
        if ($downloaded > 0) {
            $this->info("✓ Files saved to: storage/app/public/{$storagePath}/");
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Resolve recording URL: use stored recording_link or build from dialers_unified using server_ip + recording_filename.
     */
    private function resolveRecordingUrl(Recording $recording): ?string
    {
        if (!empty(trim((string) $recording->recording_link))) {
            return trim($recording->recording_link);
        }
        $serverIp = $recording->server_ip ?? null;
        $recordingFilename = $recording->recording_filename ?? null;
        if (empty($serverIp) || empty($recordingFilename)) {
            return null;
        }
        $server = DialersUnified::where('server_ip', $serverIp)->first();
        if (!$server || empty($server->recording_link) || empty($server->folder_name)) {
            return null;
        }
        $baseLink = rtrim($server->recording_link, '/');
        $folder = trim($server->folder_name, '/');
        $baseName = preg_replace('/\.(mp3|wav|m4a|ogg)$/i', '', $recordingFilename);
        $fileWithExt = $baseName . '-all.mp3';
        return "{$baseLink}/{$folder}/{$fileWithExt}";
    }

    /**
     * Download file from URL using cURL with strict connect and stall timeouts so
     * unreachable hosts fail fast and we can move on to the next recording.
     */
    private function downloadFile(string $url, int $connectTimeout = 5, int $timeout = 30): string|false
    {
        if (!function_exists('curl_init')) {
            // Fallback to file_get_contents if cURL is unavailable.
            $context = stream_context_create([
                'http' => [
                    'timeout' => $timeout,
                    'follow_location' => true,
                    'max_redirects' => 5,
                    'user_agent' => 'Mozilla/5.0 (compatible; RecordingDownloader/1.0)',
                ],
            ]);
            $fileContent = @file_get_contents($url, false, $context);
            return $fileContent === false ? false : $fileContent;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_CONNECTTIMEOUT => $connectTimeout,
            CURLOPT_TIMEOUT        => $timeout,
            // Abort if transfer rate stays below 1KB/s for 10s (dead/stalled link)
            CURLOPT_LOW_SPEED_LIMIT => 1024,
            CURLOPT_LOW_SPEED_TIME  => 10,
            CURLOPT_FAILONERROR     => true,
            CURLOPT_USERAGENT       => 'Mozilla/5.0 (compatible; RecordingDownloader/1.0)',
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => 0,
        ]);

        $fileContent = curl_exec($ch);
        $httpCode    = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno       = curl_errno($ch);
        $error       = curl_error($ch);
        curl_close($ch);

        if ($fileContent === false || $errno !== 0 || $httpCode >= 400) {
            if ($errno !== 0) {
                Log::warning("Recording download cURL error", [
                    'url'   => $url,
                    'errno' => $errno,
                    'error' => $error,
                    'http'  => $httpCode,
                ]);
            }
            return false;
        }

        // Reject obvious error pages returned with 200
        if (strlen($fileContent) < 1000 && (stripos($fileContent, '<html') !== false || stripos($fileContent, '<!DOCTYPE') !== false)) {
            return false;
        }

        return $fileContent;
    }

    /**
     * Persist a failure status on the recording so it can be retried on the next run.
     */
    private function markFailed(Recording $recording, string $reason): void
    {
        try {
            $recording->status = 'failed';
            $recording->save();
        } catch (\Exception $e) {
            Log::warning("Could not mark recording {$recording->id} as failed ({$reason}): " . $e->getMessage());
        }
    }
    
    /**
     * Validate if downloaded content is a valid audio file
     */
    private function isValidAudioFile(string $content, string $url): bool
    {
        // Check file extension from URL
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        if (in_array($extension, ['mp3', 'wav', 'm4a', 'ogg'])) {
            return true;
        }
        
        // Check file signature (magic bytes)
        if (substr($content, 0, 3) === 'ID3' || 
            (ord($content[0]) === 0xFF && (ord($content[1]) & 0xE0) === 0xE0)) {
            return true;
        }
        
        // If content is too small, might be an error page
        if (strlen($content) < 1000) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Generate filename for the recording
     */
    private function generateFilename(Recording $recording): string
    {
        if (!empty($recording->recording_filename)) {
            $baseName = $recording->recording_filename;
            $baseName = preg_replace('/\.(mp3|wav|m4a|ogg)$/i', '', $baseName);
            return $baseName . '.mp3';
        }
        
        $leadId = $recording->lead_id ?? 'unknown';
        return "recording_{$recording->id}_{$leadId}.mp3";
    }
    
    /**
     * Always update avatar_leads recording column with original URL from recordings table
     * This allows users to manually download the recording
     * Only updates if avatar_lead exists for the lead_id
     */
    private function updateAvatarLeadRecording(Recording $recording, string $originalUrl): void
    {
        if (empty($recording->lead_id)) {
            return;
        }
        
        try {
            // Check if avatar_lead exists first
            $avatarLead = AvatarLead::where('lead_id', $recording->lead_id)->first();
            
            if ($avatarLead) {
                // Update the recording column with the original URL
                $avatarLead->recording = $originalUrl;
                $avatarLead->save();
                
                Log::info("Updated avatar_lead recording column with original URL", [
                    'lead_id' => $recording->lead_id,
                    'recording_id' => $recording->id,
                    'url' => $originalUrl
                ]);
            } else {
                Log::warning("AvatarLead not found for lead_id, skipping update", [
                    'lead_id' => $recording->lead_id,
                    'recording_id' => $recording->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to update avatar_lead recording column", [
                'lead_id' => $recording->lead_id,
                'recording_id' => $recording->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Update avatar_leads recording_link with the downloaded file path
     * Only updates if avatar_lead exists for the lead_id
     */
    private function updateAvatarLeadRecordingLink(Recording $recording, string $filePath): void
    {
        if (empty($recording->lead_id)) {
            return;
        }
        
        try {
            // Check if avatar_lead exists first
            $avatarLead = AvatarLead::where('lead_id', $recording->lead_id)->first();
            
            if ($avatarLead) {
                // Generate full web-accessible URL
                $webPath = '/storage/' . $filePath;
                $appUrl = rtrim(config('app.url'), '/');
                $webUrl = $appUrl . $webPath;
                
                // Update avatar_leads table
                $avatarLead->recording_link = $webUrl;
                $avatarLead->save();
                
                Log::info("Updated avatar_lead recording_link", [
                    'lead_id' => $recording->lead_id,
                    'recording_id' => $recording->id,
                    'url' => $webUrl
                ]);
            } else {
                Log::warning("AvatarLead not found for lead_id, skipping update", [
                    'lead_id' => $recording->lead_id,
                    'recording_id' => $recording->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to update avatar_lead recording_link", [
                'lead_id' => $recording->lead_id,
                'recording_id' => $recording->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

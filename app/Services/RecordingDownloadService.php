<?php

namespace App\Services;

use App\Models\Recording;
use App\Models\AvatarLead;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class RecordingDownloadService
{
    /**
     * Download recordings from recordings table where status is not 'downloaded'
     * 
     * @param int|null $limit Maximum number of recordings to process (null for all)
     * @return array Statistics about the download process
     */
    public function downloadRecordings(?int $limit = null): array
    {
        $stats = [
            'total' => 0,
            'downloaded' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        try {
            // Get recordings that need to be downloaded
            $query = Recording::where(function ($q) {
                $q->whereNull('status')
                  ->orWhere('status', '!=', 'downloaded');
            })
            ->whereNotNull('recording_link')
            ->whereNotNull('lead_id');

            if ($limit) {
                $query->limit($limit);
            }

            $recordings = $query->get();
            $stats['total'] = $recordings->count();

            Log::info("RecordingDownloadService: Found {$stats['total']} recordings to process");

            foreach ($recordings as $recording) {
                try {
                    $result = $this->downloadSingleRecording($recording);
                    
                    if ($result['success']) {
                        $stats['downloaded']++;
                    } elseif ($result['skipped']) {
                        $stats['skipped']++;
                    } else {
                        $stats['failed']++;
                        $stats['errors'][] = [
                            'recording_id' => $recording->id,
                            'lead_id' => $recording->lead_id,
                            'error' => $result['error']
                        ];
                    }
                } catch (Exception $e) {
                    $stats['failed']++;
                    $stats['errors'][] = [
                        'recording_id' => $recording->id,
                        'lead_id' => $recording->lead_id,
                        'error' => $e->getMessage()
                    ];
                    Log::error("RecordingDownloadService: Error processing recording ID {$recording->id}: " . $e->getMessage());
                }
            }

            Log::info("RecordingDownloadService: Completed. Downloaded: {$stats['downloaded']}, Failed: {$stats['failed']}, Skipped: {$stats['skipped']}");

            return $stats;

        } catch (Exception $e) {
            Log::error("RecordingDownloadService: Fatal error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Download a single recording file
     * 
     * @param Recording $recording
     * @return array
     */
    private function downloadSingleRecording(Recording $recording): array
    {
        try {
            // Validate recording_link
            if (empty($recording->recording_link) || empty($recording->lead_id)) {
                return [
                    'success' => false,
                    'skipped' => true,
                    'error' => 'Missing recording_link or lead_id'
                ];
            }

            $recordingUrl = $recording->recording_link;
            
            // Clean the URL: remove leading/trailing slashes and whitespace
            $recordingUrl = trim($recordingUrl);
            $recordingUrl = ltrim($recordingUrl, '/'); // Remove leading slashes
            
            // Validate URL format
            if (!filter_var($recordingUrl, FILTER_VALIDATE_URL)) {
                return [
                    'success' => false,
                    'skipped' => true,
                    'error' => 'Invalid URL format: ' . $recordingUrl
                ];
            }

            // Only download HTTPS links, skip HTTP links
            if (!str_starts_with(strtolower($recordingUrl), 'https://')) {
                return [
                    'success' => false,
                    'skipped' => true,
                    'error' => 'Only HTTPS links are allowed, skipping HTTP link'
                ];
            }

            // Create storage directory if it doesn't exist
            // Use public disk for recordings so they're accessible via web
            $storagePath = 'recordings';
            $disk = Storage::disk('public');
            
            if (!$disk->exists($storagePath)) {
                $disk->makeDirectory($storagePath);
            }

            // Generate filename based on lead_id and recording_filename
            $filename = $this->generateFilename($recording);
            $filePath = $storagePath . '/' . $filename;

            // Check if file already exists
            if ($disk->exists($filePath)) {
                Log::info("RecordingDownloadService: File already exists for recording ID {$recording->id}, updating status");
                
                // Update recording status and avatar_lead
                $this->updateRecordingStatus($recording, $filePath);
                
                return [
                    'success' => true,
                    'skipped' => false,
                    'message' => 'File already exists, status updated'
                ];
            }

            // Download the file
            $fileContent = $this->downloadFile($recordingUrl);
            
            if ($fileContent === false) {
                return [
                    'success' => false,
                    'skipped' => true,
                    'error' => 'Failed to download file from URL'
                ];
            }

            // Validate it's an MP3 file (check content or extension)
            if (!$this->isValidAudioFile($fileContent, $recordingUrl)) {
                return [
                    'success' => false,
                    'skipped' => true,
                    'error' => 'Downloaded file is not a valid audio file'
                ];
            }

            // Save the file
            $disk->put($filePath, $fileContent);

            // Verify file was saved
            if (!$disk->exists($filePath)) {
                return [
                    'success' => false,
                    'skipped' => false,
                    'error' => 'File was not saved successfully'
                ];
            }

            // Update recording status and avatar_lead
            $this->updateRecordingStatus($recording, $filePath);

            Log::info("RecordingDownloadService: Successfully downloaded recording ID {$recording->id} to {$filePath}");

            return [
                'success' => true,
                'skipped' => false,
                'file_path' => $filePath
            ];

        } catch (Exception $e) {
            Log::error("RecordingDownloadService: Error downloading recording ID {$recording->id}: " . $e->getMessage());
            return [
                'success' => false,
                'skipped' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Download file from URL with timeout and error handling
     * 
     * @param string $url
     * @return string|false File content or false on failure
     */
    private function downloadFile(string $url): string|false
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30, // 30 seconds timeout
                    'follow_location' => true,
                    'max_redirects' => 5,
                    'user_agent' => 'Mozilla/5.0 (compatible; RecordingDownloader/1.0)'
                ]
            ]);

            $fileContent = @file_get_contents($url, false, $context);

            if ($fileContent === false) {
                $error = error_get_last();
                Log::warning("RecordingDownloadService: Failed to download from {$url}: " . ($error['message'] ?? 'Unknown error'));
                return false;
            }

            // Check if we got a valid response (not HTML error page)
            if (strlen($fileContent) < 1000 && (strpos($fileContent, '<html') !== false || strpos($fileContent, '<!DOCTYPE') !== false)) {
                Log::warning("RecordingDownloadService: Received HTML instead of audio file from {$url}");
                return false;
            }

            return $fileContent;

        } catch (Exception $e) {
            Log::error("RecordingDownloadService: Exception downloading from {$url}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate if downloaded content is a valid audio file
     * 
     * @param string $content
     * @param string $url
     * @return bool
     */
    private function isValidAudioFile(string $content, string $url): bool
    {
        // Check file extension from URL
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        if (in_array($extension, ['mp3', 'wav', 'm4a', 'ogg'])) {
            return true;
        }

        // Check file signature (magic bytes)
        // MP3 files can start with ID3 tag or directly with frame sync
        if (substr($content, 0, 3) === 'ID3' || 
            (ord($content[0]) === 0xFF && (ord($content[1]) & 0xE0) === 0xE0)) {
            return true;
        }

        // If content is too small, might be an error page
        if (strlen($content) < 1000) {
            return false;
        }

        // Default: assume valid if we got content
        return true;
    }

    /**
     * Generate filename for the recording
     * 
     * @param Recording $recording
     * @return string
     */
    private function generateFilename(Recording $recording): string
    {
        // Use recording_filename if available, otherwise use lead_id
        if (!empty($recording->recording_filename)) {
            $baseName = $recording->recording_filename;
            // Remove extension if present, we'll add .mp3
            $baseName = preg_replace('/\.(mp3|wav|m4a|ogg)$/i', '', $baseName);
            return $baseName . '.mp3';
        }

        // Fallback to lead_id
        $leadId = $recording->lead_id;
        $timestamp = time();
        return "recording_{$leadId}_{$timestamp}.mp3";
    }

    /**
     * Update recording status and avatar_lead recording_link
     * 
     * @param Recording $recording
     * @param string $filePath Storage path
     * @return void
     */
    private function updateRecordingStatus(Recording $recording, string $filePath): void
    {
        DB::beginTransaction();
        try {
            // Update recording status
            $recording->status = 'downloaded';
            $recording->save();

            // Generate URL for the stored file (accessible via web)
            // Files stored in storage/app/public/recordings/ are accessible via /storage/recordings/
            // Make sure 'php artisan storage:link' has been run to create the symlink
            
            // Update avatar_leads table
            $avatarLead = AvatarLead::where('lead_id', $recording->lead_id)->first();
            
            if ($avatarLead) {
                // Store the path that can be accessed via web
                // Format: /storage/recordings/filename.mp3 (Laravel's public storage)
                // This requires the storage symlink: php artisan storage:link
                $webPath = '/storage/' . $filePath;
                $avatarLead->recording_link = $webPath;
                $avatarLead->save();
                
                Log::info("RecordingDownloadService: Updated avatar_lead ID {$avatarLead->id} with recording path: {$webPath}");
            } else {
                Log::warning("RecordingDownloadService: AvatarLead not found for lead_id: {$recording->lead_id}");
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("RecordingDownloadService: Failed to update status for recording ID {$recording->id}: " . $e->getMessage());
            throw $e;
        }
    }
}

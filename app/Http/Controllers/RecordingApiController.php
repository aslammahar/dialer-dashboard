<?php

namespace App\Http\Controllers;

use App\Models\Recording;
use App\Models\AvatarLead;
use App\Services\RecordingDownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RecordingApiController extends Controller
{
    /**
     * Get recordings that need to be downloaded
     * Returns recordings where status is NULL or not in ('Uploaded', 'Downloaded', 'OK', 'Wrong')
     */
    public function getRecordingsToDownload(Request $request)
    {
        try {
            // Get optional date parameters
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $date = $request->input('date'); // Single date for specific day
            
            Log::info('getRecordingsToDownload - Start', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'date' => $date
            ]);
            
            // Increase memory and time limits for large queries
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutes
            
            // Build the query
            $query = DB::table('recordings')
                ->select(
                    'recordings.id',
                    'recordings.recording_link',
                    'recordings.lead_id',
                    'recordings.status',
                    'recordings.created_at',
                    DB::raw("DATE_FORMAT(recordings.created_at, '%Y-%m') AS month")
                )
                ->where(function ($q) {
                    $q->whereNull('recordings.status')
                        ->orWhereNotIn('recordings.status', ['uploaded', 'downloaded', 'OK', 'Wrong']);
                });
            
            // Add date filtering
            if ($date) {
                // Single date - get records for that specific day
                $query->whereDate('recordings.created_at', $date);
                Log::info('getRecordingsToDownload - Filtering by date', ['date' => $date]);
            } elseif ($startDate && $endDate) {
                // Date range
                $query->whereBetween('recordings.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                Log::info('getRecordingsToDownload - Filtering by date range', [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]);
            } elseif ($startDate) {
                // Only start date - from that date onwards
                $query->where('recordings.created_at', '>=', $startDate . ' 00:00:00');
                Log::info('getRecordingsToDownload - Filtering from start date', ['start_date' => $startDate]);
            }
            
            // Execute query with ordering
            $recordings = $query
                ->orderBy(DB::raw("DATE_FORMAT(recordings.created_at, '%Y-%m')"), 'desc')
                ->orderBy('recordings.created_at', 'desc')
                ->get();
            
            Log::info('getRecordingsToDownload - Query Success', ['count' => $recordings->count()]);

            return response()->json([
                'success' => true,
                'message' => 'Recordings to download fetched successfully',
                'data' => $recordings,
                'count' => $recordings->count(),
                'filters' => [
                    'date' => $date,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting recordings to download', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // In development, show more details
            $errorMessage = config('app.debug') 
                ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()
                : 'Error retrieving recordings';
                
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
    }

    /**
     * Update recording status
     */
    public function updateRecordingStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'status' => 'required|string|in:downloaded,OK,uploaded,Wrong'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $recording = Recording::where('id', $request->id)->first();

            if (!$recording) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recording not found'
                ], 404);
            }

            $recording->status = $request->status;
            $recording->save();

            Log::info('Recording status updated', [
                'id' => $request->id,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Recording status updated successfully',
                    'data' => [
                    'id' => $recording->id,
                    'status' => $recording->status
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating recording status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating recording status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update recording_link in avatar_leads table
     */
    public function updateAvatarLeadRecordingLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|string',
            'recording_link' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $avatarLead = AvatarLead::where('lead_id', $request->lead_id)->first();

            if (!$avatarLead) {
                return response()->json([
                    'success' => false,
                    'message' => 'Avatar lead not found'
                ], 404);
            }

            $avatarLead->recording_link = $request->recording_link;
            $avatarLead->save();

            Log::info('Avatar lead recording link updated', [
                'lead_id' => $request->lead_id,
                'recording_link' => $request->recording_link
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Recording link updated successfully',
                'data' => [
                    'lead_id' => $avatarLead->lead_id,
                    'recording_link' => $avatarLead->recording_link
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating avatar lead recording link: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating recording link: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch update recording statuses
     */
    public function batchUpdateRecordingStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'updates' => 'required|array',
            'updates.*.id' => 'required|integer',
            'updates.*.status' => 'required|string|in:downloaded,OK,uploaded,Wrong'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updated = 0;
            $failed = [];

            foreach ($request->updates as $update) {
                $recording = Recording::where('id', $update['id'])->first();
                
                if ($recording) {
                    $recording->status = $update['status'];
                    $recording->save();
                    $updated++;
                } else {
                    $failed[] = $update['id'];
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Updated {$updated} recordings successfully",
                'data' => [
                    'updated' => $updated,
                    'failed' => $failed
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error batch updating recording statuses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error batch updating: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get total count of avatar leads
     */
    public function getAvatarLeadsCount()
    {
        try {
            $count = DB::table('avatar_leads')->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting avatar leads count', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = config('app.debug') 
                ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()
                : 'Error getting count';
                
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
    }

    /**
     * Download recordings from recordings table
     * Downloads recordings where status is not 'downloaded' and stores them locally
     */
    public function downloadRecordings(Request $request)
    {
        try {
            $limit = $request->input('limit', null);
            if ($limit) {
                $limit = (int) $limit;
            }

            Log::info('downloadRecordings - Start', ['limit' => $limit]);

            $service = new RecordingDownloadService();
            $stats = $service->downloadRecordings($limit);

            return response()->json([
                'success' => true,
                'message' => 'Recording download process completed',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error downloading recordings', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = config('app.debug')
                ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()
                : 'Error downloading recordings';
                
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
    }

    /**
     * Test endpoint to verify API is working
     * This endpoint doesn't require database access
     */
    public function test()
    {
        return response()->json([
            'success' => true,
            'message' => 'API is working!',
            'timestamp' => now()->toDateTimeString(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version()
        ]);
    }
}

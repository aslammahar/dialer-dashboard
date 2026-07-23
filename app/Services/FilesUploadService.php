<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FilesUploadService
{
    
    public function uploadMultipleFiles(array $files, int $userId, string $modelClass, int $modelId): array
    {
        $results = [
            'success' => true,
            'uploads' => [],
            'errors' => []
        ];

        try {
            foreach ($files as $fileData) {
                $file = $fileData['file'];
                $category = $fileData['category'];

                // Delete existing files for this category
                $this->deleteFilesByCategory($userId, $category, $modelClass, $modelId);

                // Upload new file
                $uploadResult = $this->uploadFile($file, $userId, $category, $modelClass, $modelId);
                
                $results['uploads'][$category] = [
                    'success' => $uploadResult['success'],
                    'message' => $uploadResult['success'] ? 'File uploaded successfully' : $uploadResult['message']
                ];

                if (!$uploadResult['success']) {
                    $results['success'] = false;
                    $results['errors'][$category] = $uploadResult['message'];
                }
            }

            return $results;

        } catch (\Exception $e) {
            Log::error('Multiple file upload failed: ' . $e->getMessage());
            $results['success'] = false;
            $results['errors']['general'] = 'Upload process failed: ' . $e->getMessage();
            return $results;
        }
    }

    /**
     * Upload a single file
     */
    public function uploadFile($file, int $userId, string $category, string $modelClass, int $modelId): array
    {
        try {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            
            // Generate unique filename
            $filename = $category . '_' . time() . '_' . uniqid() . '.' . $extension;
            $filePath = config('variables.attachments.path', 'attachments') . '/' . $filename;
            
            // Store file
            Storage::put($filePath, file_get_contents($file));
            
            // Determine file type based on mime type
            $fileType = $this->getFileTypeFromMime($mimeType);
            
            // Create attachment record
            Attachment::create([
                'user_id' => $userId,
                'attachable_id' => $modelId,
                'attachable_type' => $modelClass,
                'file_path' => $filePath,
                'file_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'file_type' => $fileType, // ADD THIS FIELD
                'category' => $category,
            ]);
            
            Log::info("File uploaded successfully: {$filePath}");
            return [
                'success' => true,
                'message' => 'File uploaded successfully',
                'file_path' => $filePath
            ];
            
        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Determine file type from MIME type
     */
    private function getFileTypeFromMime(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } elseif ($mimeType === 'application/pdf') {
            return 'pdf';
        } elseif (in_array($mimeType, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ])) {
            return 'document';
        } elseif (in_array($mimeType, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ])) {
            return 'spreadsheet';
        } else {
            return 'other';
        }
    }

    /**
     * Delete files by category
     */
    public function deleteFilesByCategory(int $userId, string $category, string $modelClass, int $modelId): bool
    {
        try {
            $attachments = Attachment::where([
                'user_id' => $userId,
                'attachable_type' => $modelClass,
                'attachable_id' => $modelId,
                'category' => $category,
            ])->get();
            
            foreach ($attachments as $attachment) {
                // Delete physical file
                if (Storage::exists($attachment->file_path)) {
                    Storage::delete($attachment->file_path);
                }
                // Delete database record
                $attachment->delete();
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('File deletion failed: ' . $e->getMessage());
            return false;
        }
    }
}
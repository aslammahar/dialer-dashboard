<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FileVerificationService
{
    /**
     * Verify if file type is allowed for the category
     */
    public function verifyFileType($file, string $category): array
    {
        try {
            $config = config("variables.attachments.file_types.{$category}");
            
            if (!$config) {
                return [
                    'success' => false,
                    'message' => "Unknown file category: {$category}"
                ];
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();

            // Check if file extension is allowed
            if (!in_array($extension, $config['allowed_types'])) {
                $allowedTypes = implode(', ', $config['allowed_types']);
                return [
                    'success' => false,
                    'message' => "Invalid file type for {$category}. Allowed types: {$allowedTypes}"
                ];
            }

            // Check file size
            $maxSize = config('variables.attachments.max_size', 5120) * 1024; // Convert to bytes
            if ($file->getSize() > $maxSize) {
                $maxSizeMB = config('variables.attachments.max_size', 5120) / 1024;
                return [
                    'success' => false,
                    'message' => "File too large for {$category}. Maximum size: {$maxSizeMB}MB"
                ];
            }

            return [
                'success' => true,
                'message' => "File verified successfully for {$category}"
            ];

        } catch (\Exception $e) {
            Log::error("File verification failed for {$category}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "File verification failed: " . $e->getMessage()
            ];
        }
    }

    /**
     * Get category description
     */
    public function getCategoryDescription(string $category): string
    {
        return config("variables.attachments.file_types.{$category}.description", "Unknown category");
    }
}
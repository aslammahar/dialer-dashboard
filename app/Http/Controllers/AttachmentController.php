<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AttachmentController extends Controller
{
    public function show($id)
    {
        try {
            $attachment = Attachment::findOrFail($id);
            
            // Permission check: User can view attachment if:
            // 1. They are the UPLOADER (user_id matches) OR  
            // 2. They have 'view.attachments' permission
            $canView = $attachment->user_id === Auth::id() || 
                      Auth::user()->can('attachments');
            
            if (!$canView) {
                // Return 404 instead of 403 to hide existence of files
                abort(404, 'File not found.');
            }
            
            // Check if file exists
            if (!Storage::exists($attachment->file_path)) {
                abort(404, 'File not found.');
            }
            
            $file = Storage::get($attachment->file_path);
            $mimeType = Storage::mimeType($attachment->file_path);
            
            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline');
                
        } catch (\Exception $e) {
            Log::error('Attachment access error: ' . $e->getMessage());
            abort(404, 'File not found.');
        }
    }
}
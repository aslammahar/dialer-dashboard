<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attachable_id',
        'attachable_type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'file_type', // ADD THIS FIELD
        'category'
    ];

    /**
     * Get the parent attachable model (UserDetail, etc.)
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Get the URL for the attachment
     */
    public function getUrlAttribute()
    {
        return route('attachments.show', $this->id);
    }

    /**
     * Get human readable file size
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
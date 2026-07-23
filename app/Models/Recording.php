<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Recording extends Model
{
    use HasFactory;

    protected $fillable = [
        'recording_link',
        'lead_id',
        'server_ip',           // Added field
        'recording_filename',  // Added field
        'status',              // Status field
        'dialer_name',         // Dialer name field
        'dialer_id',           // Dialer ID field
        'audio_duration',      // Audio duration field
    ];

    // Accessor to get created_at in New York timezone
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('America/New_York');
    }

    // Accessor to get updated_at in New York timezone
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('America/New_York');
    }
}

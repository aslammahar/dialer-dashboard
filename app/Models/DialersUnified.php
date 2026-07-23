<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Merged table of dialerlist_tb and dialers_servers (all common/unified columns).
 */
class DialersUnified extends Model
{
    use HasFactory;

    protected $table = 'dialers_unified';

    protected $fillable = [
        'dialer_ip',
        'dialer_weblink',
        'dialer_access',
        'dialer_no',
        'dialer_team',
        'dialer_name',
        'server_no',
        'server_ip',
        'folder_name',
        'server_status',
        'recording_link',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

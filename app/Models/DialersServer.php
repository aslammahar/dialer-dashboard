<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DialersServer extends Model
{
    use HasFactory;

    protected $table = 'dialers_servers';

    protected $fillable = [
        'dialer_name',
        'server_no',
        'server_ip',
        'folder_name',
        'server_status',
        'recording_link'
    ];

     protected $casts = [
        'server_status' => 'integer', // Cast to integer instead of boolean
    ];
}
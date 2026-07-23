<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DialersList extends Model
{
    use HasFactory;

    protected $table = 'dialerlist_tb';

    protected $fillable = [
        'dialer_ip',
        'dialer_weblink',
        'dialer_access',
        'dialer_no',
        'dialer_team',
        'recording_link',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
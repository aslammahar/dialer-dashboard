<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HikVisionAttendance extends Model
{
    use HasFactory;
    protected $table = 'hik_vision_attendance';
    protected $fillable = [
        'employee_no',
        'employee_name',
        'status',
        'event_time',
        'raw_event',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'raw_event' => 'array',
    ];
}

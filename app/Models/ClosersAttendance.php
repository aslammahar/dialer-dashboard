<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClosersAttendance extends Model
{
    protected $table = 'closer_attendance';

    protected $fillable = ['sales_closer_id', 'attendance_date', 'status', 'marked_by'];

    protected $casts = ['attendance_date' => 'date'];

    public function closer()
    {
        return $this->belongsTo(SalesCloser::class, 'sales_closer_id');
    }
}
<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AttendanceEmployee extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'status',
        'clock_in',
        'clock_out',
        'late',
        'early_leaving',
        'overtime',
        'total_rest',
        'created_by',
    ];

    // Relationship to Employee: Use 'belongsTo' instead of 'hasOne'
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }

    // Accessor to convert clock_in time to Asia/Karachi timezone
    public function getClockInAttribute($value)
    {
        return Carbon::createFromTimeString($value, 'UTC')
            ->setTimezone('Asia/Karachi')
            ->format('H:i:s');
    }

    // Mutator to store clock_in time in UTC
    public function setClockInAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['clock_in'] = Carbon::createFromTimeString($value, 'Asia/Karachi')
                ->setTimezone('UTC')
                ->format('H:i:s');
        }
    }

    // Mutator to store clock_out time in UTC
    public function setClockOutAttribute($value)
    {
        if (!empty($value)) {
            try {
                $this->attributes['clock_out'] = Carbon::createFromTimeString($value, 'Asia/Karachi')
                    ->setTimezone('UTC')
                    ->format('H:i:s');
            } catch (\Exception $e) {
                // Handle the exception in case of an invalid time string
                $this->attributes['clock_out'] = null; // Set to null if parsing fails
                \Log::error('Error parsing clock_out time: ' . $e->getMessage()); // Log the error for debugging
            }
        } else {
            $this->attributes['clock_out'] = null; // Set to null if value is empty
        }
    }
}

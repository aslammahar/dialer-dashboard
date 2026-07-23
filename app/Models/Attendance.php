<?php

namespace App\Models;

use App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';

    // Make sure to use an array for $fillable
    protected $fillable = [
        'uid',
        'employee_id',
        'state',
        'attendance_date',
        'attendance_time',
        'status',
        'type',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}

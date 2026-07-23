<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    protected $fillable = [
        'warning_to',
        'warning_by',
        'subject',
        'warning_date',
        'description',
        'created_by',
    ];

    public function employeeTo()
    {
        return $this->belongsTo('App\Models\Employee', 'warning_to');
    }

    public function employeeBy()
    {
        return $this->belongsTo('App\Models\Employee', 'warning_by');
    }
}

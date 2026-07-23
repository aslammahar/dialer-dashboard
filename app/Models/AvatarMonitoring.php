<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvatarMonitoring extends Model
{
    use HasFactory;

    // Explicitly define the table name
    protected $table = 'avatar_monitoring';

    protected $fillable = [
        'employee_id',
        'filled_by',
        'monitor_from',
        'monitor_to',
        'monitor_date',
        'greeting',
        'response_on_answering_machine',
        'response_time',
        'customer_response',
        'leave_3_way',
        'questions',
        'dispositions',
        'comments_suggestions',
        'disposition_records',
        'score',
        'notify_to',
    ];

    protected $casts = [
        'notify_to' => 'array',
        'disposition_records' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function filledBy()
    {
        return $this->belongsTo(User::class, 'filled_by');
    }
}

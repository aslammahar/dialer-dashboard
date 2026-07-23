<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusChangeLog extends Model
{
    protected $fillable = [
        'closed_call_id',
        'policy_id',
        'customer_name',
        'old_status',
        'new_status',
        'source_file',
        'upload_batch',
        'changed_by',
        'changed_at',
        'paid_to_date',
        'description',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function closedCall()
    {
        return $this->belongsTo(ClosedCall::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
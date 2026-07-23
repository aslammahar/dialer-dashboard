<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifierAssignment extends Model
{
    protected $fillable = [
        'closed_call_id',
        'verifier_id',
        'assigned_by',
        'assigned_at',
        'reason',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function closedCall()
    {
        return $this->belongsTo(ClosedCall::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}

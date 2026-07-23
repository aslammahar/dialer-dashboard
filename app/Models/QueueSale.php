<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class QueueSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'closed_call_id',
        'customer_full_name',
        'state',
        'carrier',
        'clients_id',
        'validator_id',
        'validator_updated_at',
        'status',
        'status_updated_at',
        'is_connected',
        'connected_at',
    ];

    protected $casts = [
        'validator_updated_at' => 'datetime',
        'status_updated_at' => 'datetime',
        'connected_at' => 'datetime',
        'is_connected' => 'integer', // Changed from boolean to integer to support 3 states (null, 0, 1)
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static $rules = [
        'validator_id' => 'required|exists:validators,id',
        'status' => 'required|in:pending,approved,rejected',
    ];

    // Get validator_updated_at in Pakistan time (keeping for backward compatibility)
    public function getValidatorUpdatedAtPkAttribute()
    {
        return $this->validator_updated_at ? 
            Carbon::parse($this->validator_updated_at)->setTimezone('Asia/Karachi')->format('Y-m-d h:i A') : null;
    }

    // Get status_updated_at in Pakistan time (keeping for backward compatibility)
    public function getStatusUpdatedAtPkAttribute()
    {
        return $this->status_updated_at ? 
            Carbon::parse($this->status_updated_at)->setTimezone('Asia/Karachi')->format('Y-m-d h:i A') : null;
    }

    // Get connected_at in Pakistan time (keeping for backward compatibility)
    public function getConnectedAtPkAttribute()
    {
        return $this->connected_at ? 
            Carbon::parse($this->connected_at)->setTimezone('Asia/Karachi')->format('Y-m-d h:i A') : null;
    }

    // Get created_at in Pakistan time (keeping for backward compatibility)
    public function getCreatedAtPkAttribute()
    {
        return $this->created_at ? 
            Carbon::parse($this->created_at)->setTimezone('Asia/Karachi')->format('Y-m-d h:i A') : null;
    }

    // NEW: Get validator_updated_at in New York time
    public function getValidatorUpdatedAtNyAttribute()
    {
        return $this->validator_updated_at ? 
            Carbon::parse($this->validator_updated_at)->setTimezone('America/New_York')->format('m/d/Y h:i A') : null;
    }

    // NEW: Get status_updated_at in New York time
    public function getStatusUpdatedAtNyAttribute()
    {
        return $this->status_updated_at ? 
            Carbon::parse($this->status_updated_at)->setTimezone('America/New_York')->format('m/d/Y h:i A') : null;
    }

    // NEW: Get connected_at in New York time
    public function getConnectedAtNyAttribute()
    {
        return $this->connected_at ? 
            Carbon::parse($this->connected_at)->setTimezone('America/New_York')->format('m/d/Y h:i A') : null;
    }

    // NEW: Get created_at in New York time
    public function getCreatedAtNyAttribute()
    {
        // Try to get from closed_call first, fallback to queue_sale created_at
        if ($this->closedCall && $this->closedCall->created_at) {
            return Carbon::parse($this->closedCall->created_at)->setTimezone('America/New_York')->format('m/d/Y h:i A');
        }
        return $this->created_at ? 
            Carbon::parse($this->created_at)->setTimezone('America/New_York')->format('m/d/Y h:i A') : null;
    }

    public function closedCall()
    {
        return $this->belongsTo(ClosedCall::class, 'closed_call_id');
    }

    public function validator()
    {
        return $this->belongsTo(Validator::class);
    }

    public function comments()
    {
        return $this->hasMany(QueueSaleComment::class)->whereNull('parent_id')->with('replies')->latest();
    }

    public function allComments()
    {
        return $this->hasMany(QueueSaleComment::class);
    }
}
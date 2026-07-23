<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'description', 
        'reminder_time', 
        'status', 
        'user_id'
    ];

    protected $dates = ['reminder_time'];

    // OR alternatively
    protected $casts = [
        'reminder_time' => 'datetime'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope to get active reminders
    public function scopeActive($query)
    {
        return $query->where('status', 'pending')
                     ->where('reminder_time', '<=', now());
    }

    
}
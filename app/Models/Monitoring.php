<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    use HasFactory;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'employee_id',
        'monitor_from',
        'monitor_to',
        'monitor_date',
        'call_rapport_building',
        'qualifying_part',
        'agents_efforts',
        'rebuttals',
        'overall_call_details',
        'vocabulary',
        'customer_response',
        'suggestions',
        'score',
        'notify',
        'greeting',
        'energy',
        'qa',
        'focus',
        'positivity',
        'confidence',
        'motivation',
        'energy_level',
        'smile',
        'filled_by',
        
    ];

    // Relationship with User model
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function filledBy()
    {
        return $this->belongsTo(User::class, 'filled_by');
    }
}

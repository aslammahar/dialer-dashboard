<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    use HasFactory;

    protected $table = 'carrier-search'; // Table name if it's not the plural form of the model name

    // Specify the fields that are mass assignable
    protected $fillable = [
        'licensed_agency',
        'state',
        'licensed_agent_name',
        'carriers'
    ];

    // Cast licensed_agency and state to array (for handling multiple selections)
    protected $casts = [
        'licensed_agency' => 'array', // Converts JSON to array
        'state' => 'array', // Converts JSON to array
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suspension extends Model
{
    protected $fillable = [
        'suspended_by',
        'userid',
        'start_date',
        'end_date',
        'reason',
        'created_by',
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'userid')->first();
    }
}

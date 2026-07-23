<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamAudit extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'updated_by', 'event_type', 'old_values', 'new_values'];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}


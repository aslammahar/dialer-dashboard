<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TeamAgentAudit;
use Illuminate\Database\Eloquent\Model;

class TeamAgent extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::created(function ($teamAgent) {
            TeamAgentAudit::create([
                'team_agent_id' => $teamAgent->id,
                'updated_by' => auth()->id(),
                'event_type' => 'created',
                'old_values' => null,
                'new_values' => $teamAgent->toArray(),
            ]);
        });

        static::updated(function ($teamAgent) {
            TeamAgentAudit::create([
                'team_agent_id' => $teamAgent->id,
                'updated_by' => auth()->id(),
                'event_type' => 'updated',
                'old_values' => $teamAgent->getOriginal(),
                'new_values' => $teamAgent->getChanges(),
            ]);
        });

        static::deleted(function ($teamAgent) {
            TeamAgentAudit::create([
                'team_agent_id' => $teamAgent->id,
                'updated_by' => auth()->id(),
                'event_type' => 'deleted',
                'old_values' => $teamAgent->toArray(),
                'new_values' => null,
            ]);
        });
    }

}

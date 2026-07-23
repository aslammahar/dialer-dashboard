<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TeamAudit;
use Illuminate\Support\Facades\Auth;

class Team extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'leader_id', 'center_id','hc_override', 'created_by', 'updated_by', 'deleted_by'];
    
    // User types allowed to bypass center scoping for Team queries.
    public const CENTER_BYPASS_TYPES = [
        'super admin',
        'company',
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function center()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }

    public function agents()
    {
        return $this->belongsToMany(User::class, 'team_agent', 'team_id', 'agent_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Global center scope for Team collections
        static::addGlobalScope('center', function (Builder $builder) {
            if (app()->runningInConsole()) {
                return;
            }

            $authUser = Auth::user();
            if (!$authUser) {
                return;
            }

            if (method_exists($authUser, 'canBypassCenterScope') && $authUser->canBypassCenterScope()) {
                return;
            }

            if (!empty($authUser->center_id)) {
                $builder->where('teams.center_id', $authUser->center_id);
                return;
            }

            $builder->whereRaw('1=0');
        });

        // Auto-assign center_id on create (non-bypass users)
        static::creating(function (Team $team) {
            if (app()->runningInConsole()) {
                return;
            }

            $authUser = Auth::user();
            if (!$authUser) {
                return;
            }

            if (method_exists($authUser, 'canBypassCenterScope') && $authUser->canBypassCenterScope()) {
                return;
            }

            $team->center_id = $authUser->center_id;
        });

        static::created(function ($team) {
            TeamAudit::create([
                'team_id' => $team->id,
                'updated_by' => auth()->id(),
                'event_type' => 'created',
                'old_values' => null,
                'new_values' => $team->toArray(),
            ]);
        });

        static::updated(function ($team) {
            TeamAudit::create([
                'team_id' => $team->id,
                'updated_by' => auth()->id(),
                'event_type' => 'updated',
                'old_values' => $team->getOriginal(),
                'new_values' => $team->getChanges(),
            ]);
        });

        static::deleted(function ($team) {
            TeamAudit::create([
                'team_id' => $team->id,
                'updated_by' => auth()->id(),
                'event_type' => 'deleted',
                'old_values' => $team->toArray(),
                'new_values' => null,
            ]);
        });
    }

}

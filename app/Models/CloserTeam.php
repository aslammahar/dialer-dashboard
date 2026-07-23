<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CloserTeam extends Model
{
    use HasFactory;

    protected $table = 'closer_teams';

    protected $fillable = [
        'name',
        'description',
        'team_lead_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the team lead
     */
    public function teamLead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }

    /**
     * Get all team members
     */
  public function members(): BelongsToMany
{
    return $this->belongsToMany(User::class, 'closer_team_members', 'closer_team_id', 'user_id')
                ->withPivot('joined_at')
                ->withTimestamps()
                ->whereIn('type', ['Closer', 'Outsourcing']);
}
    /**
     * Get team member pivot records
     */
    public function teamMembers(): HasMany
    {
        return $this->hasMany(CloserTeamMember::class);
    }

    /**
     * Check if user is in this team
     */
    public function hasUser($userId): bool
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    /**
     * Get active teams
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get teams with member count
     */
    public function scopeWithMemberCount($query)
    {
        return $query->withCount('members');
    }
}
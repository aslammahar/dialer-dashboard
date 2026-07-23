<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AvatarLead extends Model
{
    protected $table = 'avatar_leads';
    protected $appends = ['team_name'];
    protected $casts = [
        // 'phone_number' => 'encrypted',
    ];

    protected $fillable = [
        'agent_id',
        'center_id',
        'team_id',
        'lead_id',
        'dialer_id', // Dailer id is Aloo Called
        'AGE',
        'Smoker',
        'verifier',
        'center',
        'list_id',
        'phone_number',
        'campaign',
        'closer',
        'group_a',
        'server_ip',
        'dispo',
        'agent_name',
        'recording_filename',
        'recording',
        'center',
        'verifier',
        'recording_id',
        'recording_link',
        'dialer_name',
        'recordinglink2',
        'entry_list_id',
        'user_group',
        'list_name',
        'list_description',
        'entry_date',
        'closer_name',
        'dialername',
        'centername',
        'xferSubmission',
        'billing_status',
        'billable_duration',
        'QAstatus',
        'phone_hash'
    ];

    protected static function boot()
    {
        parent::boot();

        // Convert timestamps to US Mountain Time Zone before saving
        static::saving(function ($lead) {
            $now = Carbon::now()->setTimezone('America/New_York');

            if (!$lead->exists) {
                $lead->created_at = $now;
            }
            $lead->updated_at = $now;
        });
    }

    protected static function booted(): void
    {
        static::addGlobalScope('center', function (Builder $builder) {
            // Skip scoping for console commands.
            if (app()->runningInConsole()) {
                return;
            }

            $user = Auth::user();
            if (!$user) {
                return;
            }

            // Bypass for privileged user types who can access all centers.
            // Adjust if needed.
            $bypassTypes = [
                'super admin',
               'company',
            ];

            if (in_array($user->type, $bypassTypes, true)) {
                return;
            }

            if (!empty($user->center_id)) {
                $builder->where('avatar_leads.center_id', $user->center_id);
            }
        });
    }

    // Define the relationship with the User model for the users
    public function agent()
    {
        return $this->belongsTo(User::class, 'dialer_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier');
    }

    public function qaPerson()
    {
        return $this->belongsTo(User::class, 'QaPersonId', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id'); // Adjust 'team_id' according to your database schema
    }

    // Encrypt phone number before saving
    public function setPhoneNumberAttribute($value)
    {
        try {
            if (!empty($value)) {
                // Encrypt for storage
                $encryptedValue = Crypt::encryptString($value);
                $this->attributes['phone_number'] = $encryptedValue;

                // Hash for duplicate detection
                $this->attributes['phone_hash'] = hash('sha256', $value);
            } else {
                $this->attributes['phone_number'] = null;
                $this->attributes['phone_hash'] = null;
            }
        } catch (\Exception $e) {
            Log::error('Error encrypting phone number: ' . $e->getMessage());
        }
    }

    // Decrypt phone number when retrieving
    public function getPhoneNumberAttribute($value)
    {
        try {
            Log::debug('Encrypted Value from DB: ' . $value);
            $decryptedValue = Crypt::decryptString($value);
            Log::debug('Decrypted Value: ' . $decryptedValue);
            return $decryptedValue;
        } catch (\Exception $e) {
            Log::error('Error decrypting phone number: ' . $e->getMessage());
            return null; // or handle the error as appropriate
        }
    }
    public function getTeamNameAttribute()
    {
        return $this->team->name ?? 'No Team';
    }
}

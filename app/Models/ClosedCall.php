<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class ClosedCall extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope('center', function (Builder $builder) {
            // Only enforce center scoping for web requests with an authenticated user.
            if (app()->runningInConsole()) {
                return;
            }

            $user = Auth::user();
            if (!$user) {
                return;
            }

            // Bypass for privileged user types.
            // Adjust this list to match who should see cross-center data.
            $bypassTypes = [
                'super admin',
                'company',
                // 'Director',`
                'client',

            ];

            if (in_array($user->type, $bypassTypes, true)) {
                return;
            }

            if (!empty($user->center_id)) {
                $builder->where('closed_calls.center_id', $user->center_id);
            }
        });

        // Prevent accidental data loss: ignore null overwrites on update
        // when the existing database value is already non-null.
        static::updating(function (ClosedCall $closedCall) {
            foreach ($closedCall->getDirty() as $attribute => $newValue) {
                if ($newValue === null && $closedCall->getOriginal($attribute) !== null) {
                    $closedCall->{$attribute} = $closedCall->getOriginal($attribute);
                }
            }
        });
    }

    protected $fillable = [
        'customer_full_name',
        'phone_number',
        'alternate_phone_number',
        'cx_email',
        'address',
        'city',
        'state',
        'zip_code',
        'gender',
        'martial_status',
        'age',
        'dob',
        'palce_of_birth',
        'height',
        'weight',
        'social_security',
        'smoker',
        'health_condition',
        'medication',
        'hospital_name',
        'hospital_address',
        'physician_name',
        'monthly_premium',
        'carrier',
        'coverage_plan',
        'customer_eligibility',
        'beneficiary',
        'beneficiary_relation',
        'beneficiary_phone',
        'beneficiary_dob',
        'payor',
        'bank_name',
        'bank_address',
        'routing_number',
        'bank_account_number',
        'debit_card_direct_express_no',
        'debit_card_direct_express_expiration',
        'debit_card_direct_express_cvv',
        'account_type',
        'initial_draft_date',
        'future_draft_date',
        'underwriter_name',
        'remarks',
        'closer_id',
        'junior_closer_name',
        'center_name',
        'center_id',
        'sale_made_by',
        'status',
        'clients_comment',
        'clients_id',
        'closername',
        'juniorcloser2',
        'lead_id',
        'teamname',
        'agentname',
        'dialeragentname',
        'dialername',
        'list_id_2',
        'list_id_1',
        'recording_id',
        'hippa_id',
        'policy_id',
        'signature_type',      // Add new field
        'call_id',            // Add new field
        'dialer_name_new',
        'client_name_2',
        'agent_status',
        'assigned_history',
        'updated_by'
    ];

    protected $casts = [
        'dob' => 'date',
        'beneficiary_dob' => 'date',
        'initial_draft_date' => 'date',
        'future_draft_date' => 'date'
    ];

    // Define validation rules for the model
    public static $rules = [
        'status' => 'required|in:pending,approved,rejected',
    ];

    public function closer()
    {
        return $this->belongsTo(User::class, 'closer_id');
    }

    public function juniorcloser()
    {
        return $this->belongsTo(User::class, 'junior_closer_name');
    }

    // Define relationship with clients table
    public function client()
    {
        return $this->belongsTo(User::class, 'clients_id');
    }

    public function verifierAssignment()
    {
        return $this->hasOne(\App\Models\VerifierAssignment::class);
    }

    public function getClientNameAttribute()
    {
        return $this->client?->name ?? null;
    }

    public function setSocialSecurityAttribute($value)
    {
        $this->attributes['social_security'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getSocialSecurityAttribute()
    {
        if (isset($this->attributes['social_security']) && $this->attributes['social_security']) {
            try {
                return Crypt::decryptString($this->attributes['social_security']);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return null;
            }
        }
        return null;
    }

    public function setBankAccountNumberAttribute($value)
    {
        $this->attributes['bank_account_number'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getBankAccountNumberAttribute()
    {
        if (isset($this->attributes['bank_account_number']) && $this->attributes['bank_account_number']) {
            try {
                return Crypt::decryptString($this->attributes['bank_account_number']);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return null;
            }
        }
        return null;
    }

    public function setDebitCardDirectExpressNoAttribute($value)
    {
        $this->attributes['debit_card_direct_express_no'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getDebitCardDirectExpressNoAttribute()
    {
        if (isset($this->attributes['debit_card_direct_express_no']) && $this->attributes['debit_card_direct_express_no']) {
            try {
                return Crypt::decryptString($this->attributes['debit_card_direct_express_no']);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return null;
            }
        }
        return null;
    }

    public function setDebitCardDirectExpressExpirationAttribute($value)
    {
        $this->attributes['debit_card_direct_express_expiration'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getDebitCardDirectExpressExpirationAttribute()
    {
        if (isset($this->attributes['debit_card_direct_express_expiration']) && $this->attributes['debit_card_direct_express_expiration']) {
            try {
                return Crypt::decryptString($this->attributes['debit_card_direct_express_expiration']);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return null;
            }
        }
        return null;
    }

    public function setDebitCardDirectExpressCvvAttribute($value)
    {
        $this->attributes['debit_card_direct_express_cvv'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getDebitCardDirectExpressCvvAttribute()
    {
        if (isset($this->attributes['debit_card_direct_express_cvv']) && $this->attributes['debit_card_direct_express_cvv']) {
            try {
                return Crypt::decryptString($this->attributes['debit_card_direct_express_cvv']);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return null;
            }
        }
        return null;
    }


    // add near other relationships
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}

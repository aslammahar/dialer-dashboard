<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class OutsourceClosedCall extends Model
{
    use HasFactory;

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
        'signature_type',
        'call_id',
        'dialer_name_new',
        'client_name_2',
        'agent_status',
        'recording_status',
    ];

    protected $casts = [
        'dob' => 'date',
        'beneficiary_dob' => 'date',
        'initial_draft_date' => 'date',
        'future_draft_date' => 'date'
    ];

    // Define validation rules for the model
    public static $rules = [
        'status' => 'required|in:pending,approved,rejected,funded,charged_backed,DNF,Cancelled,NSF,DNC,Underwriting,Need to Reach',
    ];

    // Relationships
    public function closer()
    {
        return $this->belongsTo(User::class, 'closer_id');
    }

    public function juniorcloser()
    {
        return $this->belongsTo(User::class, 'junior_closer_name');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'clients_id');
    }

    // Accessor for client name
    public function getClientNameAttribute()
    {
        $client = User::find($this->clients_id);
        if ($client) {
            return $client->name;
        }
        return null;
    }

    // Encryption/Decryption for sensitive fields
    public function setSocialSecurityAttribute($value)
    {
        if ($value) {
            $this->attributes['social_security'] = Crypt::encryptString($value);
        }
    }

    public function getSocialSecurityAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    public function setBankAccountNumberAttribute($value)
    {
        if ($value) {
            $this->attributes['bank_account_number'] = Crypt::encryptString($value);
        }
    }

    public function getBankAccountNumberAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    public function setDebitCardDirectExpressNoAttribute($value)
    {
        if ($value) {
            $this->attributes['debit_card_direct_express_no'] = Crypt::encryptString($value);
        }
    }

    public function getDebitCardDirectExpressNoAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }
}
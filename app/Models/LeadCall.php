<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadCall extends Model
{
    protected $fillable = [
        'lead_id',
        'subject',
        'call_type',
        'duration',
        'user_id',
        'description',
        'call_result',
    ];

    public function getLeadCallUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    // added by jamil

        public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoiceQALead extends Model
{
    protected $table = 'voiceqa_leads';

    protected $fillable = [
        'agent_id',
        'lead_id',
        'dialer_id',
        'verifiers',
        'recording',
        'GREETINGS',
        'PITCH_Call_About',
        'AGE',
        'Smoker',
        'Health1',
        'Beneficiary',
        'Account',
        'Plan',
        'Transfer_details',
        'Xfer_Consent',
        'Rebuttals',
        'COMMENTS',
        'Status',
        'QA_Person',
        'Use_of_Rebuttals',
        'No_of_Refusals',
        'count',
    ];

    /**
     * Define a many-to-one relationship with the "users" table for the agent.
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // Add any other relationships or custom methods as needed
}

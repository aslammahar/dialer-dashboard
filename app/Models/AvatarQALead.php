<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvatarQALead extends Model
{
    use HasFactory;

    protected $table = 'avatar_q_a_leads'; // Specify the custom table name

    protected $fillable = [
        // 'agent_email',
        'agent_name',
        'phone_number',
        'dialer_id',
        'verifiers',
        'recording',
        'greetings',
        'pitch_call_about',
        'age',
        'smoker',
        'health1',
        'health',
        'beneficiary',
        'account',
        'plan',
        'transfer_details',
        'xfer_consent',
        'rebuttal', // rebuttal
        'comments',
        'status',
        'qa_person',
        'use_of_rebuttals', // = total_refusal
        'total_refusal',
        'no_of_refusals',
        'count',
        'date_time',
        'xferSubmissionTime',
        'agent_name',
        'lead_id',
        // 'closer_name', // this is the name of the cLOSER
        'recording_link',
        'pitch',
        'health',
        'total_rebuttal',
        'qa_comment',
        'call_status',
        'total_duration',
        'played_duration',
        'qa_timestamp',
        
    ];

    // Define relationships if needed

    public function agent()
    {
         return $this->belongsTo(User::class, 'agent_email', 'email');
    }
}

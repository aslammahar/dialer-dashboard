<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QaLeadsVoice extends Model
{
    use HasFactory;

    protected $table = 'qaleadsvoice'; // Specify the custom table name

    protected $fillable = [
        'user_email',
        'phone_number',
        'state',
        'licenced_agent_name',
        'status',
        'comments',
        'recordings',
        'qa_person',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_email', 'email');
    }
}

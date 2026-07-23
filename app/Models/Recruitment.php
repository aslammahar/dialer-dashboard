<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recruitment extends Model
{
    use HasFactory;

    // Specify the table name if it's not the default plural form of the model
    protected $table = 'recruitments';

    // Define fillable fields to allow mass assignment
    protected $fillable = [
        'name',
        'contact_no',
        'email',
        'experience',
        'location',
        'interview_taken_by',
        'remarks',
        'age',
        'alternate_number',
        'date',
        'emergency_number',
        'relation',
        'source',
        'work_from',
        'designation',
        'status',
        'interviewer_remarks',
        'willing_to_work_night_shift',
        'telemarketing_experience',
        'total_work_experience',
        'job_reason',
        'currently_student',
        'strength',
        'weakness',
        'self_description',
        'communication_score',
        'accent_score',
        'energy_score',
        'comprehension_score',
        'experience_score',
        'hired_comments',
        'project_assigned',
        'signing_bonus',
        'salary_expectation',
        'final_status',
        'joining_date',
        'rejection_reason',
        'rejection_comments',
        'rejection_communication',
        'rejection_energy',
        'rejection_accent',
        'rejection_comprehension',
        'formid',
    'pin',
    'refer_to',
    ];

    public function referredTo()
{
    return $this->belongsTo(Employee::class, 'refer_to');
}

}

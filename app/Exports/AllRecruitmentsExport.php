<?php

namespace App\Exports;

use App\Models\Recruitment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class AllRecruitmentsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    public function collection()
    {
        return Recruitment::orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Name', 'Contact No', 'Email', 'Experience', 'Location', 'Interview Taken By', 'Remarks', 
            'Age', 'Alternate Number', 'Date', 'Emergency Number', 'Relation', 'Source', 'Work From', 
            'Designation', 'Status', 'Interviewer Remarks', 'Willing to Work Night Shift', 'Telemarketing Experience', 
            'Total Work Experience', 'Job Reason', 'Currently Student', 'Strength', 'Weakness', 'Self Description', 
            'Communication Score', 'Accent Score', 'Energy Score', 'Comprehension Score', 'Experience Score', 
            'Hired Comments', 'Project Assigned', 'Signing Bonus', 'Salary Expectation', 'Final Status', 
            'Joining Date', 'Rejection Reason', 'Rejection Comments', 'Rejection Communication', 'Rejection Energy', 
            'Rejection Accent', 'Rejection Comprehension'
        ];
    }

    public function map($recruitment): array
    {
        return [
            $recruitment->name,
            $recruitment->contact_no,
            $recruitment->email,
            $recruitment->experience,
            $recruitment->location,
            $recruitment->interview_taken_by,
            $recruitment->remarks,
            $recruitment->age,
            $recruitment->alternate_number,
            $recruitment->date,
            $recruitment->emergency_number,
            $recruitment->relation,
            $recruitment->source,
            $recruitment->work_from,
            $recruitment->designation,
            $recruitment->status,
            $recruitment->interviewer_remarks,
            $recruitment->willing_to_work_night_shift,
            $recruitment->telemarketing_experience,
            $recruitment->total_work_experience,
            $recruitment->job_reason,
            $recruitment->currently_student,
            $recruitment->strength,
            $recruitment->weakness,
            $recruitment->self_description,
            $recruitment->communication_score,
            $recruitment->accent_score,
            $recruitment->energy_score,
            $recruitment->comprehension_score,
            $recruitment->experience_score,
            $recruitment->hired_comments,
            $recruitment->project_assigned,
            $recruitment->signing_bonus,
            $recruitment->salary_expectation,
            $recruitment->final_status,
            $recruitment->joining_date,
            $recruitment->rejection_reason,
            $recruitment->rejection_comments,
            $recruitment->rejection_communication,
            $recruitment->rejection_energy,
            $recruitment->rejection_accent,
            $recruitment->rejection_comprehension,
        ];
    }
}

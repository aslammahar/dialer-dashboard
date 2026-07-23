@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h2>View Recruitment</h2>
    <a href="{{ route('recruitments.index') }}" class="btn btn-secondary mb-3">Back</a>
    <table class="table table-bordered">
      
        <tr><th>Name</th><td>{{ $recruitment->name }}</td></tr>

        <tr><th>Contact No</th><td>{{ $recruitment->contact_no }}</td></tr>
        <tr><th>Interview taken by</th><td>{{ $recruitment->interview_taken_by }}</td></tr>

        <tr><th>Email</th><td>{{ $recruitment->email }}</td></tr>
        <tr><th>Experience</th><td>{{ $recruitment->experience }}</td></tr>
        <tr><th>Location</th><td>{{ $recruitment->location }}</td></tr>
        <tr><th>Status</th><td>{{ $recruitment->status }}</td></tr>
        <tr><th>Remarks</th><td>{{ $recruitment->remarks }}</td></tr>
        <tr><th>age</th><td>{{ $recruitment->age }}</td></tr>
        <tr><th>willing_to_work_night_shift</th><td>{{ $recruitment->willing_to_work_night_shift }}</td></tr>
        <tr><th>telemarketing_experience</th><td>{{ $recruitment->telemarketing_experience }}</td></tr>
        <tr><th>total_work_experience</th><td>{{ $recruitment->total_work_experience }}</td></tr>
        <tr><th>job_reason</th><td>{{ $recruitment->job_reason }}</td></tr>
        <tr><th>currently_student</th><td>{{ $recruitment->currently_student }}</td></tr>
        <tr><th>strength</th><td>{{ $recruitment->strength }}</td></tr>
        <tr><th>weakness</th><td>{{ $recruitment->weakness }}</td></tr>
        <tr><th>self_description</th><td>{{ $recruitment->self_description }}</td></tr>
        <tr><th>source</th><td>{{ $recruitment->source }}</td></tr>
        <tr><th>work_from</th><td>{{ $recruitment->work_from }}</td></tr>
        <tr><th>designation</th><td>{{ $recruitment->designation }}</td></tr>
        <tr><th>status</th><td>{{ $recruitment->status }}</td></tr>
        <tr><th>interviewer_remarks</th><td>{{ $recruitment->interviewer_remarks }}</td></tr>
        <tr><th>communication_score</th><td>{{ $recruitment->communication_score }}</td></tr>
        <tr><th>accent_score</th><td>{{ $recruitment->accent_score }}</td></tr>
        <tr><th>energy_score</th><td>{{ $recruitment->energy_score }}</td></tr>
        <tr><th>comprehension_score</th><td>{{ $recruitment->comprehension_score }}</td></tr>
        <tr><th>experience_score</th><td>{{ $recruitment->experience_score }}</td></tr>
        <tr><th>hired_comments</th><td>{{ $recruitment->hired_comments }}</td></tr>
        <tr><th>project_assigned</th><td>{{ $recruitment->project_assigned }}</td></tr>
        <tr><th>signing_bonus</th><td>{{ $recruitment->signing_bonus }}</td></tr>
        <tr><th>salary_expectation</th><td>{{ $recruitment->salary_expectation }}</td></tr>
        <tr><th>final_status</th><td>{{ $recruitment->final_status }}</td></tr>
        <tr><th>joining_date</th><td>{{ $recruitment->joining_date }}</td></tr>
        <tr><th>rejection_reason</th><td>{{ $recruitment->rejection_reason }}</td></tr>
        <tr><th>rejection_comments</th><td>{{ $recruitment->rejection_comments }}</td></tr>
        <tr><th>rejection_communication</th><td>{{ $recruitment->rejection_communication }}</td></tr>
        <tr><th>rejection_energy</th><td>{{ $recruitment->rejection_energy }}</td></tr>
        


    </table>
</div>
@endsection



        
           
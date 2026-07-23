@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Final Interview Form</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('recruitments.update', $recruitment->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Basic Information --}}
        <h4>Basic Information</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $recruitment->name }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Contact No</label>
                    <input type="text" name="contact_no" class="form-control" value="{{ $recruitment->contact_no }}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $recruitment->email }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Experience</label>
                    <input type="text" name="experience" class="form-control" value="{{ $recruitment->experience }}">
                </div>
            </div>
        </div>

        {{-- Location and Source --}}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" value="{{ $recruitment->location }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Source</label>
                    <select name="source" class="form-control">
                        <option value="">Select Source</option>
                        @foreach($sources as $source)
                            <option value="{{ $source }}" {{ $recruitment->source == $source ? 'selected' : '' }}>
                                {{ $source }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Age and Alternate Contact --}}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" class="form-control" value="{{ $recruitment->age }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Alternate Number</label>
                    <input type="text" name="alternate_number" class="form-control" value="{{ $recruitment->alternate_number }}">
                </div>
            </div>
        </div>

        {{-- Emergency Contact --}}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Emergency Number</label>
                    <input type="text" name="emergency_number" class="form-control" value="{{ $recruitment->emergency_number }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Relation</label>
                    <input type="text" name="relation" class="form-control" value="{{ $recruitment->relation }}">
                </div>
            </div>
        </div>

        {{-- Work Information --}}
        <h4>Work Information</h4>

        <div class="row">
            <div class="col-md-6">
            <div class="form-group">
            <label>Work From</label>
            <select name="work_from" class="form-control">
                <option value="">Select Work From</option>
                @foreach($workFromOptions as $option)
                    <option value="{{ $option }}" {{ $recruitment->work_from == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
        </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
            <label>Interview Taken By</label>
            <input type="text" name="interview_taken_by" class="form-control" value="{{ $recruitment->interview_taken_by }}">
        </div>
            </div>
        </div>

       

        

        {{-- Remarks --}}
        <div class="form-group">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control">{{ $recruitment->remarks }}</textarea>
        </div>

        {{-- Other fields from your form --}}
        <h4>Additional Information</h4>
 

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Joining Date</label>
                    <input type="date" name="joining_date" class="form-control" value="{{ $recruitment->joining_date }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" value="{{ $recruitment->status }}"> 
                        <option value="">Select Status</option>
                        <option value="Pending" {{ $recruitment->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Selected" {{ $recruitment->status == 'Selected' ? 'selected' : '' }}>Selected</option>
                        <option value="Rejected" {{ $recruitment->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Interview Taken By</label>
            <input type="text" name="interview_taken_by" class="form-control" value="{{$recruitment->interviewer_remarks}}">
        </div>

        </div>
        <div class="col-md-6">
        <div class="form-group">
            <label>Work From</label>
            <select name="work_from" class="form-control">
                <option value="">Select Work From</option>
                @foreach($workFromOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div>
        </div>
        </div>
        
        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Designation</label>
            <select name="designation" class="form-control">
                <option value="">Select Designation</option>
                @foreach($designations as $designation)
                    <option value="{{ $designation }}">{{ $designation }}</option>
                @endforeach
            </select>
        </div>

        </div>
        <div class="col-md-6">

        </div>
        </div>
        
        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Interviewer Remarks</label>
            <select name="interviewer_remarks" class="form-control">
                <option value="">Select Remarks</option>
                @foreach($interviewerRemarks as $remark)
                    <option value="{{ $remark }}">{{ $remark }}</option>
                @endforeach
            </select>
        </div>

        </div>
        <div class="col-md-6">
        <div class="form-group">
            <label>Schedule Date</label>
            <input type="date" name="date" class="form-control">
        </div>
        </div>
        </div>
        
        <h4>Questions</h4>

       
        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Are you willing to work in Night Shift?</label>
            <select name="willing_to_work_night_shift" class="form-control" required>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        </div>
        <div class="col-md-6">
        <div class="form-group">
            <label>Telemarketing / Call Center Experience?</label>
            <select name="telemarketing_experience" class="form-control" required>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        </div>
        </div>
       
        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Total Work Experience</label>
            <input type="text" name="total_work_experience" class="form-control" value="{{$recruitment->total_work_experience}}">
        </div>
        </div>
        <div class="col-md-6">
        <div class="form-group">
            <label>Currently a student?</label>
            <select name="currently_student" class="form-control" required>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        </div>
      
       



        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Why do you need this job?</label>
            <textarea name="job_reason" class="form-control" value="">{{$recruitment->job_reason}}</textarea>
        </div>
        </div>
     
        <div class="col-md-6">
        <div class="form-group">
            <label>Describe Your Strength</label>
            <textarea name="strength" class="form-control">{{$recruitment->strength}}</textarea>
        </div>
        </div>
        </div>

       
        <div class="row">
        <div class="col-md-12">
        <div class="form-group">
            <label>Describe Yourself</label>
            <textarea name="self_description" class="form-control">{{$recruitment->self_description}}</textarea>
        </div>
        </div>
        
        </div>
       
        <h4>Score out of 5</h4>

        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Communication</label>
            <input type="number" name="communication_score" class="form-control" min="0" max="10" value="{{$recruitment->communication_score}}">
        </div>
        </div>
        <div class="col-md-6">
        <div class="form-group">
            <label>Accent</label>
            <input type="number" name="accent_score" class="form-control" min="0" max="10" value="{{$recruitment->accent_score}}">
        </div>

        </div>
        </div>


        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Energy</label>
            <input type="number" name="energy_score" class="form-control" min="0" max="10" value="{{$recruitment->energy_score}}">
        </div>
        </div>
        <div class="col-md-6">
        <div class="form-group">
            <label>Comprehension</label>
            <input type="number" name="comprehension_score" class="form-control" min="0" max="10" value="{{$recruitment->comprehension_score}}">
        </div>
        </div>
        </div>


        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Experience</label>
            <input type="number" name="experience_score" class="form-control" min="0" max="10" value="{{$recruitment->experience_score}}">
        </div>
        </div>
        <div class="col-md-6">

        </div>
        </div>



        <h4>Hired</h4>

        <div class="row">
        <div class="col-md-6">
       
        <div class="form-group">
            <label>Comments</label>
            <textarea name="hired_comments" class="form-control">{{$recruitment->hired_comments}}</textarea>
        </div>
        </div>
        <div class="col-md-6">
        <div class="form-group">
            <label>Project Assigned</label>
            <input type="text" name="project_assigned" class="form-control" value="{{$recruitment->project_assigned}}">
        </div>
          
        </div>
        </div>

        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Signing Bonus</label>
            <input type="text" name="signing_bonus" class="form-control" value="{{$recruitment->signing_bonus}}">
        </div>

        </div>
        <div class="col-md-6">
        <div class="form-group">
            <label>Salary Expectation</label>
            <input type="text" name="salary_expectation" class="form-control" value="{{$recruitment->salary_expectation}}">
        </div>

          
        </div>
        </div>

        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Final Status</label>
            <input type="text" name="final_status" class="form-control" value="{{$recruitment->final_status}}">
        </div>

        </div>
        <div class="col-md-6">
       
        <div class="form-group">
            <label>Joining Date</label>
            <input type="date" name="joining_date" class="form-control" value="{{$recruitment->joining_date}}">
        </div>
        </div>
        </div>

        <h4>Rejected</h4>

        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Reason for Rejection</label>
            <textarea name="rejection_reason" class="form-control">{{$recruitment->rejection_reason}}</textarea>
        </div>

        </div>
        <div class="col-md-6">
        <div class="form-group">
            <label>Comments</label>
            <textarea name="rejection_comments" class="form-control">{{$recruitment->rejection_comments}}</textarea>
        </div>
          
        </div>
        </div>


        <div class="row">
        <div class="col-md-6">
       
        <div class="form-group">
            <label>Communication</label>
            <input type="number" name="rejection_communication" class="form-control" min="0" max="10" value="{{$recruitment->rejection_communication}}">
        </div>


        </div>
        <div class="col-md-6">
        <div class="form-group">
            <label>Energy</label>
            <input type="number" name="rejection_energy" class="form-control" min="0" max="10" value="{{$recruitment->rejection_energy}}">
        </div>
          
        </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Update</button>
    </form>
</div>
@endsection

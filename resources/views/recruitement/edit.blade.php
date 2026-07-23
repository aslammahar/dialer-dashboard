@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Candidate Form</h2>

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
                    <label>Describe Experience</label>
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

     

      
<!-- 
        {{-- Other fields from your form --}}
        <h4>Additional Information</h4> -->
        <div class="row">
         

        <!-- <div class="row">
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
        </div> -->

        <!-- <div class="row">
        <div class="col-md-6">
        <div class="form-group">
                    <label>Designation</label>
                    <input type="text" name="designation" class="form-control" value="{{ $recruitment->designation }}">
                </div>
            </div> -->

        
        <div class="col-md-6">
        <!-- <div class="form-group">
            <label>Work From</label>
            <select name="work_from" class="form-control">
                <option value="">Select Work From</option>
                @foreach($workFromOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div> -->
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
       
        
        <button type="submit" class="btn btn-primary mt-3">Update</button>
    </form>
</div>
@endsection

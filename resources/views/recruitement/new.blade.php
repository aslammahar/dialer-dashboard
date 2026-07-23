@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>New Candidate Form</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('recruitment.newstore') }}" method="POST">
        @csrf
        @method('POST')

        {{-- Basic Information --}}
        <h4>Basic Information</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control"  required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Contact No</label>
                    <input type="text" name="contact_no" class="form-control"  required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"  required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Describe Experience</label>
                    <input type="text" name="experience" class="form-control" >
                </div>
            </div>
        </div>

        {{-- Location and Source --}}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Source</label>
                    <select name="source" class="form-control">
                        <option value="">Select Source</option>
                        @foreach($sources as $source)
                            <option value="{{ $source }}" >
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
                    <input type="number" name="age" class="form-control" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Alternate Number</label>
                    <input type="text" name="alternate_number" class="form-control" >
                </div>
            </div>
        </div>

        {{-- Emergency Contact --}}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Emergency Number</label>
                    <input type="text" name="emergency_number" class="form-control" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Relation</label>
                    <input type="text" name="relation" class="form-control" >
                </div>
            </div>
        </div>

     

      

        <div class="row">
         

        

        
        <div class="col-md-6">
       
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
            <input type="text" name="total_work_experience" class="form-control" >
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
            <textarea name="job_reason" class="form-control" value=""></textarea>
        </div>
        </div>
     
        <div class="col-md-6">
        <div class="form-group">
            <label>Describe Your Strength</label>
            <textarea name="strength" class="form-control"></textarea>
        </div>
        </div>
        </div>

       
        <div class="row">
        <div class="col-md-12">
        <div class="form-group">
            <label>Describe Yourself</label>
            <textarea name="self_description" class="form-control"></textarea>
        </div>
        </div>
        
        </div>
       
        
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>
</div>
@endsection

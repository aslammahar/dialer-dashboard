@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Recruitment Form</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('recruitment.store') }}" method="POST">
        @csrf

        {{-- Basic Information --}}
        <h4>Basic Information</h4>

        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
    <label>Name</label>
    <input type="text" name="name" class="form-control" required oninput="capitalizeFirstLetter(this)" required>
</div>

<script>
    function capitalizeFirstLetter(input) {
        // Capitalize the first letter of each word in the input
        input.value = input.value.replace(/\b\w/g, char => char.toUpperCase());
    }
</script>
        </div>
        <div class="col-md-6">
        <div class="form-group">
            <label>Contact No</label>
            <input type="text" name="contact_no" class="form-control" required>
        </div>
        </div>
        </div>

        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" >
        </div>
        </div>
        <div class="col-md-6">
        <div class="form-group">
            <label>Experience</label>
            <input type="text" name="experience" class="form-control">
        </div>
        </div>
        </div>


        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" class="form-control">
        </div>
        </div>
        <div class="col-md-6">
        <div class="form-group">
            <label>Source</label>
            <select name="source" class="form-control">
                <option value="">Select Source</option>
                @foreach($sources as $source)
                    <option value="{{ $source }}">{{ $source }}</option>
                @endforeach
            </select>
        </div>
        </div>
        </div>


   


    


        <div class="row">
        <div class="col-md-12">
        <div class="form-group">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control"></textarea>
        </div>
        </div>
        
        </div>

        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label>Recruiter</label>

            <select name="interview_taken_by" class="form-control" required>
                <option value="">Select Employee</option>
                @foreach($interviewer as $id => $name)
                    <option value="{{ $name }}">{{ $name }}</option>
                @endforeach
            </select>
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
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="">Select Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
        </div>
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
        
        <div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Refer To</label>
            <select name="refer_to" class="form-control">
                <option value="">Select Employee</option>
                @foreach($employees as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>










        

        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>
</div>
@endsection

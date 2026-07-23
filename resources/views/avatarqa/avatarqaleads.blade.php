

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>



    <style>
    .form-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
</style>
</head>
<body>
    
</body>
</html>



@extends('layouts.admin')
@push('script-page')
@endpush
@section('page-title')
    {{__('Support')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('avatarLead')}}</h5>
    </div>
@endsection
<!-- Header script by imran niaz  -->
@push('theme-script')
<script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
@endpush

@section('breadcrumb')
<link rel="stylesheet" type="text/css" href="{{asset('css/app.css')}}">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('avatarLead')}}</li>
@endsection
<!-- Header script by imran niaz  -->
@section('action-btn')
  
   
@endsection
    <!-- Add the download button -->
 
  
 @section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Avatarqalead')}}</li>
@endsection


@section('content')


<div id="company-settings" class="card">
<div class="card-header">
    <h5 class=" h6 mb-0">{{__('Avatar Lead')}}</h5>
        <h2>Create Avatarqalead</h2>
<form action="{{ route('avatarqaleads.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-container">
        <div class="form-group">
            <label for="user_id">Select User</label>
            <select class="form-control" name="user_id" id="user_id" required>
                @foreach ($users as $user)
                    @if ($user->type === 'avatar')
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="lead_ids">Lead Id's</label>
            <input class="form-control" type="text" name="lead_ids" id="lead_ids" required>
        </div>

        <div class="form-group">
            <label for="dialer_ids">Dialer Id's</label>
            <input class="form-control" type="text" name="dialer_ids" id="dialer_ids" required>
        </div>

        <div class="form-group">
            <label for="verifiers">Verifiers</label>
            <input class="form-control" type="text" name="verifiers" id="verifiers" required>
        </div>

        <div class="form-group">
            <label for="recording">Recording</label>
            <input class="form-control" type="text" name="recording" id="recording" required>
        </div>
        <br>
        
        <!-- Radio options in the first column -->
        <div class="form-group">
            <label class="radio-label">Greetings:</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="greetings" id="greetings_yes" value="yes" required>
                <label class="form-check-label" for="greetings_yes">Yes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="greetings" id="greetings_no" value="no" required>
                <label class="form-check-label" for="greetings_no">No</label>
            </div>

            <!-- Add similar code for other radio options -->
            
            <label class="radio-label">PITCH/ Call About:</label>
            <div class="form-check ">
                <input class="form-check-input" type="radio" name="pitch_call_about" id="pitch_call_about_yes" value="yes" required>
                <label class="form-check-label" for="pitch_call_about_yes">Yes</label>
            </div>
            <div class="form-check ">
                <input class="form-check-input" type="radio" name="pitch_call_about" id="pitch_call_about_no" value="no" required>
                <label class="form-check-label" for="pitch_call_about_no">No</label>
            </div>
            
            <!-- Repeat the above code for each radio option -->
        </div>

        <!-- Other form fields in the second column -->
        <div class="form-group">
            <label class="radio-label">AGE:</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="age" id="age_yes" value="yes" required>
                <label class="form-check-label" for="age_yes">Yes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="age" id="age_no" value="no" required>
                <label class="form-check-label" for="age_no">No</label>
            </div>

            <!-- Add similar code for other radio options -->
            
            <div class="form-group">
                <label class="radio-label">Smoker:</label>
                <div class="form-check ">
                    <input class="form-check-input" type="radio" name="smoker" id="smoker_yes" value="yes" required>
                    <label class="form-check-label" for="smoker_yes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="smoker" id="smoker_no" value="no" required>
                    <label class="form-check-label" for="smoker_no">No</label>
                </div>
            </div>
            
            <!-- Repeat the above code for each radio option -->
        </div>
    </div>

    <!-- Add other form fields outside the form-container -->

    <div class="form-group">
        <label for="comments">Comments</label>
        <input class="form-control" type="text" name="comments" id="comments">
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select class="form-control" name="status" id="status">
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <div class="form-group">
        <label for="qa_person">QA Person</label>
        <input class="form-control" type="text" name="qa_person" id="qa_person">
    </div>

    <div class="form-group">
        <label for="use_of_rebuttals">Use of Rebuttals</label>
        <input class="form-control" type="text" name="use_of_rebuttals" id="use_of_rebuttals">
    </div>

    <div class="form-group">
        <label for="no_of_refusals">No of Refusals</label>
        <input class="form-control" type="number" name="no_of_refusals" id="no_of_refusals">
    </div>

    <div class="form-group">
        <label for="count">Count</label>
        <input class="form-control" type="number" name="count" id="count" required>
    </div>

    <br>
    <button class="btn btn-sm btn-primary" type="submit">Submit</button>
</form>
    </div>

@endsection
 
 
    <!-- Include Bootstrap JS (optional) -->
   
    @section('content')
 
 

 @endsection
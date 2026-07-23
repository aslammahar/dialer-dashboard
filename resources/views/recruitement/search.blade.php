<!-- Search Form -->






@extends('layouts.admin')

@section('content')

<div class="container mt-5">
    <h2 class="mb-4">Search Form</h2>
    <form action="{{ route('recruitment.search') }}" method="GET" >
<div class="row">
<div class="col-md-6">
    <div class="form-group">
        <label for="id">Enter Form No:</label>
        <input type="number" class="form-control" name="id" id="id"  placeholder="Enter ID to search" required>
        <label for="pin">Enter Pin :</label>

        <input type="text" class="form-control" name="pin" placeholder="Enter Pin" required />

    </div>
    <div class="col-md-6"> 
         <button type="submit" class="btn btn-primary">Search</button>
         <a href="{{ route('recruitment.new') }}" class="btn btn-warning">Walk In Interview</a>

        
        </div>
</div>
</div>    
</form>
    <!-- Display error message -->
    @if(session('error'))
        <div class="alert alert-danger mt-4">{{ session('error') }}</div>
    @endif



    <!-- Search Result -->
    @if (isset($recruitment))
        @if ($recruitment)
            <div class="card mt-4 col-md-6">
                <div class="card-body">
                    <h3>{{ $recruitment->name }}</h3>
                    <p><strong>Contact No:</strong> {{ $recruitment->contact_no }}</p>
                    <p><strong>Email:</strong> {{ $recruitment->email }}</p>
                    <p><strong>Experience:</strong> {{ $recruitment->experience }}</p>
                    <p><strong>Location:</strong> {{ $recruitment->location }}</p>
                    <p><strong>Designation:</strong> {{ $recruitment->designation }}</p>
                    <p><strong>Status:</strong> {{ $recruitment->status }}</p>
                    <p><strong>Interview Taken By:</strong> {{ $recruitment->interview_taken_by }}</p>
                    <a href="{{ route('recruitments.edit', $recruitment->id) }}" class="btn btn-warning">Edit</a>
                </div>


            </div>
        @else
            <div class="alert alert-danger mt-4">Record not found</div>
        @endif
    @elseif(request()->has('id'))
        <!-- Display this message if a search was performed but no record found -->
        <div class="alert alert-danger mt-4 col-md-6">Record not found</div>
    @endif
</div>

@endsection

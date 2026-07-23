@extends('layouts.admin')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Lead Recording Search</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('lead.search') }}">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label>Lead ID</label>
                            <input type="text" 
                                   name="lead_id" 
                                   class="form-control @error('lead_id') is-invalid @enderror" 
                                   placeholder="Enter Lead ID" 
                                   required>
                            @error('lead_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label>User ID</label>
                            <input type="text" 
                                   name="user" 
                                   class="form-control @error('user') is-invalid @enderror" 
                                   placeholder="Enter User ID" 
                                   
                                   required>
                            @error('user')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label>Begin Date</label>
                            <input type="date" 
                                   name="begin_date" 
                                   class="form-control @error('begin_date') is-invalid @enderror" 
                                   value="{{ $begin_date }}" 
                                   required>
                            @error('begin_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label>End Date</label>
                            <input type="date" 
                                   name="end_date" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   value="{{ $end_date }}" 
                                   required>
                            @error('end_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Search Lead Recording</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@extends('layouts.admin')

@section('content')




<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-primary">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{ __('Leaderboard') }}</small>
                                <li><a href="{{ route('leaderboard') }}" class="btn btn-sm btn-primary">{{ __('Leaderboard') }}</a></li>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <h3 class="m-0"></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





<!-- resources/views/search/index.blade.php -->
<form action="{{ route('search.search') }}" method="post">
    @csrf
    <div class="form-group row">
        <label for="dialer_id" class="col-sm-2 col-form-label">Dialer ID:</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" name="dialer_id" id="dialer_id" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="start_date" class="col-sm-2 col-form-label">Start Date:</label>
        <div class="col-sm-4">
            <input type="date" class="form-control" name="start_date" id="start_date" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="end_date" class="col-sm-2 col-form-label">End Date:</label>
        <div class="col-sm-4">
            <input type="date" class="form-control" name="end_date" id="end_date" required>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </div>
</form>

@if(isset($results))
<table class="table">
    <thead>
        <tr>
            <th class="text-center font-weight-bold">Agent Name</th>
            <th class="text-center font-weight-bold">Total Leads</th>
            <th class="text-center font-weight-bold">Approved Leads</th>
            <th class="text-center font-weight-bold">Rejected Leads</th>
            <th class="text-center font-weight-bold">Pending Leads</th>
            <th class="text-center font-weight-bold">On Review Leads</th>
            <th class="text-center font-weight-bold">No Recording Leads</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $result)
        <tr>
            <td class="text-center">{{ $result['agent_name'] }}</td>
            <td class="text-center">{{ $result['total_leads'] }}</td>
            <td class="text-center">{{ $result['approved_leads'] }}</td>
            <td class="text-center">{{ $result['rejected_leads'] }}</td>
            <td class="text-center">{{ $result['pending_leads'] }}</td>
            <td class="text-center">{{ $result['review_leads'] }}</td>
            <td class="text-center">{{ $result['norec_leads'] }}</td>
        </tr>
        @endforeach
        <tr class="table-info">
            <td class="text-center font-weight-bold">Total</td>
            <td class="text-center font-weight-bold">{{ $totalLeads }}</td>
            <td class="text-center font-weight-bold">{{ $totalApprovedLeads }}</td>
            <td class="text-center font-weight-bold">{{ $totalRejectedLeads }}</td>
            <td class="text-center font-weight-bold">{{ $totalPendingLeads }}</td>
            <td class="text-center font-weight-bold">{{ $totalReviewLeads }}</td>
            <td class="text-center font-weight-bold">{{ $totalNoRecLeads }}</td>
        </tr>
    </tbody>
</table>
@endif

@endsection
@extends('layouts.admin')

@section('page-title')
{{ __('Team Stats') }}
@endsection

@push('theme-script')
<script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
@endpush

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('AvatarLeads') }}</li>
@endsection

@section('content')

@if(Auth::user()->type == 'QA'){
<a href="daily-leads"> <u>Voice Leads</u></a><br>
<a href="avatar-leads"> <u>Avatar Leads</u></a><br><br>
<a href="qa-section"><u>QA section</u></a><br>


@elseif(\Auth::user()->type == 'Team Lead' || \Auth::user()->type == 'Director')
<a href="teams"><u>Go To Management</u></a><br>

<div class="col-lg-3 col-md-6">
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto mb-/3 mb-sm-0">
                    <div class="d-flex align-items-center">
                        <div class="theme-avtar bg-info">
                            <i class="ti ti-cast"></i>
                        </div>
                        <div class="ms-3">
                            <small class="text-muted">{{ __('All Leads') }} </small>
                            <li><a href="avatarleads" class="btn btn-sm btn-info">All Leads</a></li>
                        </div>
                    </div>
                </div>
                <div class="col-auto text-end">
                    <h3 class="m-0"> </h3>
                </div>
            </div>
        </div>
    </div>
</div>


@endif


<div class="container">
    <div class="row mb-3">
        @foreach ($teams as $team)
        @php
        $pendingleads = 0; // Initialize total records count for the current team
        $approvedCount = 0; // Initialize count for approved records
        $rejectedCount = 0; // Initialize count for rejected records
        $teamLeaderName = $team->leader->name; // Get the team leader's name
        @endphp

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $team->name }}</h5>
                    <h6 class="card-text">TL : {{ $teamLeaderName }}</h6>
                    {{-- Calculate and display the total records for the current team --}}
                    @foreach ($userLeads as $lead)
                    @if ($lead->Agent->teams->contains('id', $team->id))
                    @php
                    if ($lead->QAstatus === 'pending') {
                    $pendingleads++;
                    } elseif ($lead->QAstatus === 'approved') {
                    $approvedCount++; // Increment approved count
                    } elseif ($lead->QAstatus === 'rejected') {
                    $rejectedCount++;
                    }
                    @endphp
                    @endif
                    @endforeach

                    <p class="card-text">Total Pending: {{ $pendingleads }}</p>
                    <p class="card-text">Approved: {{ $approvedCount }}</p>
                    <p class="card-text">Rejected: {{ $rejectedCount }}</p>
                </div>
            </div>
        </div>

        @endforeach
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>

                            <th>ID</th>
                            <th>Agent</th>
                            <th>Phone Number</th>
                            <th>Dialer Id</th>
                            <th>Verifier</th>
                            <th>Recordings</th>
                            <th>Comments</th>
                            <th> QA Status</th>
                            <th>QA Person</th>
                            <th>Team</th>
                            <th>Count</th>
                            <th>Date_Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userLeads as $lead)
                        @php
                        $statusColor = $lead->status === 'approved' ? 'green' : ($lead->status === 'rejected' ? 'red' : 'inherit');
                        @endphp
                        <tr style="background-color: {{ $statusColor }}; color: black;">
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->Agent->name }}</td>
                            <td>{{ $lead->lead_id }}</td>
                            <td>{{ $lead->dialer_id }}</td>
                            <td>{{ $lead->verifiers }}</td>
                            <td>{{ $lead->recording }}</td>
                            <td>{{ $lead->comments }}</td>
                            <td>{{ $lead->QAstatus }}</td>
                            <td>{{ $lead->qa_person }}</td>
                            <td>
                                @foreach ($lead->Agent->teams as $team)
                                {{ $team->name }}
                                @if (!$loop->last)
                                , <!-- Add a comma if not the last team -->
                                @endif
                                @endforeach
                            </td>
                            <td>{{ $lead->count }}</td>
                            <td>{{ $lead->date_time }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
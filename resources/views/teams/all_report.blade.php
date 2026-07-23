@extends('layouts.admin')




@section('page-title')

{{ __('All Reports') }}

@endsection



{{---
<style>
    .team-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        flex-direction: column;
    }

    .team {
        width: 30%;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table, th, td {
        border: 1px solid black;
    }

    th, td {
        padding: 5px;
        text-align: left;
    }

    #alter tr:nth-child(even) {
        background-color: #eee;
    }

    #alter tr:nth-child(odd) {
        background-color: #fff;
    }

    #alter th {
        background-color: black;
        color: white;
    }
</style>
--}}




@section('content')
<div class="all-button-box row d-flex justify-content-end">
    <div class="card bg-primary mb-0">
        <div class="card-body">
            <div class="d-block d-sm-flex align-items-center justify-content-between">
                <div>
                    <div class="col">
                        <a class="btn btn-sm btn-primary" href="{{ route('leaderboard') }}">Leaderboard</a>
                        {{-- <aclass="btnbtn-smbtn-primary"href="route('agent-reports') --">Agent Reports</a>--}}
                        <a class="btn btn-sm btn-primary" href="{{ route('approved-reports') }}">Approved Leads Reports</a>
                        <a class="btn btn-sm btn-primary" href="{{ route('rejected-reports') }}">Rejected Leads Reports</a>
                        <h5 class="text-white text-nowrap"> </h5>
                    </div>

                </div>
                <div class="row align-items-center">
                    <div class="box box-primary">
                        <form method="GET" class="dataTable-input" action="{{ route('all-reports') }}">
                            <label for="start_date">Start Date:</label>
                            <input type="date" id="start_date" name="start_date">

                            <label for="end_date">End Date:</label>
                            <input type="date" id="end_date" name="end_date">

                            <button class="btn" type="submit" style="color: rgb(199, 196, 0)">Get Reports</button>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>


    {{-- Define a box for a form here --}}


    {{-- <div class="row">
        @foreach ($teams as $team)
            <div class="team">
                <h2>{{ $team->name }}</h2>
    <table id="alter">
        <thead>
            <tr>
                <th>Team Member</th>
                <th>Xfers</th>
                <th>Approved transfer</th>
                <th>Rejected Transfer</th>
                <th>Working Days</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ $team->leader->name }} (TL)</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @php
            $totalLeadCount = 0;
            $totalapproved = 0;
            $totalrejected = 0;// Initialize total lead count for the team
            @endphp
            @foreach ($team->agents as $agent)
            <tr>
                <td>{{ $agent->name }} (agent)</td>
                <td>
                    @php
                    $leadCount = $mergedLeadsCount
                    ->where('id', $agent->id)
                    ->first()
                    ->total_lead_count ?? 0;
                    $totalLeadCount += $leadCount; // Add to the total lead count
                    @endphp
                    {{ $leadCount }}
                </td>
                <td>
                    @php
                    // Get the count of "approved" AvatarQALeads for the current agent
                    $approvedCount = $approvedAvatarQALeadCount
                    ->where('email', $agent->email)
                    ->first()
                    ->approved_count ?? 0;
                    $totalapproved += $approvedCount; // Add to the total approved count
                    @endphp
                    {{ $approvedCount }}
                </td>
                <td>
                    @php
                    // Get the count of "rejected" AvatarQALeads for the current agent
                    $rejectedCount = $rejectedAvatarQALeadCount
                    ->where('email', $agent->email)
                    ->first()
                    ->rejected_count ?? 0;
                    $totalrejected += $rejectedCount; // Add to the total rejected count
                    @endphp
                    {{ $rejectedCount }}
                </td>
            </tr>
            @endforeach


            <tr>
                <th>Total</th>
                <th>{{ $totalLeadCount }}</th>
                <th>{{ $totalapproved }}</th>
                <th>{{ $totalrejected }}</th>
                <td>{{ $workingDays }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endforeach
</div>
---}}


<!-- new html code -->
<div class="row">

    <div class="col-xl-12">
        @foreach ($teams as $team)
        <div class="card" style="background: rgba(151, 90, 4, 0.219)">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable" style="color: rgb(0, 0, 0)">
                        <thead>
                            <tr>
                                <th>Team Member</th>
                                <th>Xfers</th>
                                <th>Approved transfer</th>
                                <th>Rejected Transfer</th>
                                <th>Working Days</th>
                            </tr>
                        </thead>

                        <tbody>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ $team->leader->name }} (TL)</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                            $totalLeadCount = 0;
                            $totalapproved = 0;
                            $totalrejected = 0;// Initialize total lead count for the team
                            @endphp
                            @foreach ($team->agents as $agent)
                            <tr>
                                <td>{{ $agent->name }} (agent)</td>
                                <td>
                                    @php
                                    $leadCount = $mergedLeadsCount
                                    ->where('id', $agent->id)
                                    ->first()
                                    ->total_lead_count ?? 0;
                                    $totalLeadCount += $leadCount; // Add to the total lead count
                                    @endphp
                                    {{ $leadCount }}
                                </td>
                                <td>
                                    @php
                                    // Get the count of "approved" AvatarQALeads for the current agent
                                    $approvedCount = $approvedAvatarQALeadCount
                                    ->where('email', $agent->email)
                                    ->first()
                                    ->approved_count ?? 0;
                                    $totalapproved += $approvedCount; // Add to the total approved count
                                    @endphp
                                    {{ $approvedCount }}
                                </td>
                                <td>
                                    @php
                                    // Get the count of "rejected" AvatarQALeads for the current agent
                                    $rejectedCount = $rejectedAvatarQALeadCount
                                    ->where('email', $agent->email)
                                    ->first()
                                    ->rejected_count ?? 0;
                                    $totalrejected += $rejectedCount; // Add to the total rejected count
                                    @endphp
                                    {{ $rejectedCount }}
                                </td>
                            </tr>
                            @endforeach


                            <tr>
                                <th class="co">Total</th>
                                <th>{{ $totalLeadCount }}</th>
                                <th>{{ $totalapproved }}</th>
                                <th>{{ $totalrejected }}</th>
                                <td>{{ $workingDays }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection


@section('content')




@endsection
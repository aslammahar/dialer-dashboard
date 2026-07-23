@extends('layouts.admin')

@section('content')
    <style>
        .div1 {
            height: calc(100vh - 120px);
            overflow: scroll;
        }

        .div1 th,
        .div1 td {
            border-left: none;
            border-right: 1px solid #bbbbbb;
            padding: 8px;
            width: 80px;
            min-width: 80px;
            text-align: center;
            font-weight: bold;
        }

        .div1 th {
            position: sticky;
            top: 0;
            background: #1E3A5F;
            color: #FFFFFF;
            z-index: 2;
        }

        .div1 td {
            background: #FFFFFF;
        }

        .div1 th:nth-child(1),
        .div1 td:nth-child(1) {
            position: sticky;
            left: 0;
            background: #1E3A5F;
            color: #FFFFFF;
            z-index: 3;
        }

        .div1 th:nth-child(2),
        .div1 td:nth-child(2) {
            position: sticky;
            left: 85px;
            background: #004D40;
            color: #FFFFFF;
            z-index: 3;
        }

        .div1 th:last-child,
        .div1 td:last-child {
            background: #004D40;
            color: #FFFFFF;
            z-index: 2;
        }
    </style>

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-/3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-success">
                                    <i class="ti ti-cast"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Teams Management') }}</small>
                                    <li><a href="{{ url('teams') }}"><u>Teams Management</u></a></li>
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

        <div class="container">
            <h1>Shrinkage Leads Report</h1>

            <!-- Filter form -->
            <form method="GET" action="{{ route('all.team.reports') }}" class="form-inline">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label for="team_id" class="mr-2">Select Team:</label>
                        <select id="team_id" name="team_id" class="form-control">
                            <option value="">All Teams</option>
                            <option value="without_team" {{ $selectedTeamId === 'without_team' ? 'selected' : '' }}>
                                Without Team
                            </option>
                            @foreach ($teams as $team)
                                <option value="{{ $team->id }}"
                                    {{ $selectedTeamId == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="qa_status" class="mr-2">Select QA Status:</label>
                        <select id="qa_status" name="qa_status" class="form-control">
                            <option value="total" {{ $selectedQAStatus == 'total' ? 'selected' : '' }}>Total</option>
                            <option value="approved" {{ $selectedQAStatus == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ $selectedQAStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ $selectedQAStatus == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="start_date" class="mr-2">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" class="form-control"
                            value="{{ $startDate ?? '' }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="end_date" class="mr-2">End Date:</label>
                        <input type="date" id="end_date" name="end_date" class="form-control"
                            value="{{ $endDate ?? '' }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <button type="submit" name="export" value="1" class="btn btn-success">Export to Excel</button>
                    </div>
                </div>
            </form>

            <div>
                <div>
                    <div class="div1">
                        <table class="table table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>Team</th>
                                    <th>Agent</th>
                                    @foreach ($dates as $date)
                                        <th>{{ \Carbon\Carbon::parse($date)->format('M d') }}</th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                                <tr>
                                    <th colspan="1">Daily</th>
                                    <th colspan="1">Totals</th>
                                    @foreach ($dates as $date)
                                        <th>
                                            @php
                                                $dailyTotal = 0;
                                                foreach ($groupedLeadsCount as $dialerId => $agentDates) {
                                                    if (isset($agentDates[$date])) {
                                                        $dailyTotal += $agentDates[$date];
                                                    }
                                                }
                                            @endphp
                                            {{ $dailyTotal }}
                                        </th>
                                    @endforeach
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $processedDialerIds = []; @endphp

                                @foreach ($reportTeams as $team)
                                    @foreach ($team->agents as $agent)
                                        @if (!in_array($agent->dialer_id, $processedDialerIds))
                                            @php $processedDialerIds[] = $agent->dialer_id; @endphp
                                            <tr>
                                                <td>{{ $team->name }}</td>
                                                <td>{{ $agent->name }}</td>
                                                @php $totalLeadCount = 0; @endphp
                                                @foreach ($dates as $date)
                                                    <td>
                                                        @php
                                                            $leadCount = isset($groupedLeadsCount[$agent->dialer_id][$date])
                                                                ? $groupedLeadsCount[$agent->dialer_id][$date]
                                                                : 0;
                                                            $totalLeadCount += $leadCount;
                                                        @endphp
                                                        {{ $leadCount }}
                                                    </td>
                                                @endforeach
                                                <td>{{ $totalLeadCount }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
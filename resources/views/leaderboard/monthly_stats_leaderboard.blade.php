@extends('layouts.admin')



@section('page-title')

{{ __('Leaderboard') }}
@endsection




@section('content')

<!-- <div class="all-button-box row d-flex justify-content-end">
    <div class="card bg-primary mb-0">
        <div class="card-body">
            <div class="d-block d-sm-flex align-items-center justify-content-between">
                <div>
                    <div class="col">
                        <a class="btn btn-sm btn-primary" href="avatar_qa_leads">Avatar Checked Leads</a>



                        <a href="agent-reports" class="btn btn-sm btn-primary">Agent Reports </a>
                        <a href="approved-reports" class="btn btn-sm btn-primary">Approved Leads Reports</a>
                        <a href="rejected-reports" class="btn btn-sm btn-primary">Rejected Leads Reports</a>
                        <a href="avatar-section" class="btn btn-sm btn-primary">Avatar Section</a>
                        @if(\Auth::user()->type == 'voice')
                        <a href="{{route('voice-section')}}" class="btn btn-sm btn-primary">Voice Section</a>
                        @elseif(\Auth::user()->type == 'QA')
                        <a href="qa-section" class="btn btn-sm btn-primary">QA section</a>


                        @elseif(\Auth::user()->type == 'Team Lead')
                        <a href="teams" class="btn btn-sm btn-primary">Go To Management</a>

                        @endif


                        <h5 class="text-white text-nowrap"> </h5>
                    </div>

                </div>
                <div class="row align-items-center">
                    <div class="box box-primary">

                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

<br> -->

<!-- links goes here  -->
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-info">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{__('Create Lead')}}</small>
                                <li><a href="{{route('leads.create')}}" class="btn btn-sm btn-info">Create Lead</a></li>
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
                                <small class="text-muted">{{ __('Leaderboard Monthly') }} </small>
                                <li> <a href="{{route('avatar-leaderboard-monthly')}}" class="btn btn-sm btn-primary">Leaderboard Monthly</a></li>
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

    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-/3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-warning">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{ __('Avatar Section') }} </small>
                                <li><a href="avatar-section" class="btn btn-sm btn-warning">Avatar Section</a></li>
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





    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-/3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-danger">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{ __('Leaderboard Daily') }} </small>
                                <li><a href="{{route('avatar-leaderboard-daily')}}" class="btn btn-sm btn-danger">Leaderboard Daily</a></li>
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
</div>
<!-- links ends here  -->



<div class="container">
    <h1>Leaderboard</h1>
    <style>
        table#alter {
            border-collapse: collapse;
            width: 50%;
            /* Adjust the width as needed */
        }

        table#alter,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 5px;
            /* Reduce padding for a smaller table */
            text-align: left;
        }

        table#alter tr:nth-child(even) {
            background-color: #eee;
        }

        table#alter tr:nth-child(odd) {
            background-color: #fff;
        }

        table#alter th {
            color: white;
            background-color: gray;
        }
    </style>


    <style>
        table#alter {
            width: 50%;
        }

        table#alter th {
            width: 15%;
        }

        table#alter td {
            width: 15%;
        }


        th,
        td {
            text-align: center;
            font-weight: bold;
    </style>


    @php
    $printTableHeadings = true; // Initialize a variable to control whether to print table headings
    $totalAgentCountAllTeams = 0; // Initialize total agent count for all teams
    $totalLeadCountAllTeams = 0; // Initialize total lead count for all teams
    @endphp

    <div>
        @php
        $totalSaleMadeCountAllTeams = 0; // Initialize total sale made count for all teams
        $printTableHeadings = true; // Initialize a variable to control whether to print table headings
        $totalAgentCountAllTeams = 0; // Initialize total agent count for all teams
        $totalLeadCountAllTeams = 0; // Initialize total lead count for all teams
        @endphp

        @foreach ($teams as $team)
        <table id="alter" style="margin-right: 20px;">
            @if ($printTableHeadings)
            <thead>
                <tr>
                    <th>Team Name</th>
                    <th>Leader</th>
                    <th>HC</th>
                    <th>Xfers</th>
                    <th>Total Sales</th>
                    <th>Attempts per Agent</th>
                </tr>
            </thead>
            @php
            $printTableHeadings = false; // Set to false after printing headings
            @endphp
            @endif
            <tbody>
                <tr>
                    <td>{{ $team->name }}</td>
                    <td>{{ $team->leader->name }} (leader)</td>
                    <td>
                        @php
                        $totalAgentCount = count($team->agents); // Count the number of agents in the team
                        $totalLeadCount = 0; // Initialize total lead count for the team
                        $totalAgentCountAllTeams += $totalAgentCount; // Add to the total agent count for all teams
                        @endphp
                        {{ $totalAgentCount }}
                    </td>
                    <td>
                        @foreach ($team->agents as $agent)
                        @php
                        $leadCount = $mergedLeadsCount
                        ->where('id', $agent->id)
                        ->first()
                        ->total_lead_count ?? 0;
                        $totalLeadCount += $leadCount; // Add to the total lead count for the team
                        @endphp
                        @endforeach
                        {{ $totalLeadCount }}
                        @php
                        $totalLeadCountAllTeams += $totalLeadCount; // Add to the total lead count for all teams
                        @endphp
                    </td>
                    <td style=" font-size: 1.5em; background-color: 
    @php
    $totalSaleMadeCount = 0; // Initialize total sale made count for the team
    foreach ($team->agents as $agent) {
        $saleMadeCount = $mergedLeadsCount
            ->where('id', $agent->id)
            ->first()
            ->total_count ?? 0; // Ensure this references total_count
        $totalSaleMadeCount += $saleMadeCount; // Add to the total sale made count for the team
    }
    $totalSaleMadeCountAllTeams += $totalSaleMadeCount; // Add to the total sale made count for all teams

    if ($totalSaleMadeCount == 0) {
        echo '#ff0066'; // Red color for 0 sales
    } elseif ($totalSaleMadeCount == 1) {
        echo '#99ff33'; // Green color for 1 sale
    } elseif ($totalSaleMadeCount == 2) {
        echo '#33cc33'; // Dark green color for 2 sales
    } elseif ($totalSaleMadeCount == 3) {
        echo '#009900'; // Forest green color for 3 sales
    } elseif ($totalSaleMadeCount == 4) {
        echo '#00e6ac'; // Cyan color for 4 sales
    } elseif ($totalSaleMadeCount == 5) {
        echo '#009999'; // Teal color for 5 sales
    } elseif ($totalSaleMadeCount >= 6 && $totalSaleMadeCount <= 8) {
        echo '#3399ff'; // Light blue color for 6-8 sales
    } elseif ($totalSaleMadeCount >= 9 && $totalSaleMadeCount <= 10) {
        echo '#9933ff'; // Purple color for 9-10 sales
    } elseif ($totalSaleMadeCount > 10) {
        echo '#9900cc'; // Violet color for more than 10 sales
    }
    @endphp
    ;">
                        {{ $totalSaleMadeCount }}
                    </td>

                    @php
                    $attemptsPerAgent = $totalLeadCount / $totalAgentCount; // Calculate attempts per agent
                    $formattedAttemptsPerAgent = number_format($attemptsPerAgent, 1); // Format to one decimal place
                    @endphp

                    <td style="font-size: 1.5em; text-align: center; font-weight: bold; background-color: 
    @php
    if ($attemptsPerAgent == 0) {
        echo '#ff0066'; // Red color for 0 attempts
    } elseif ($attemptsPerAgent > 0 && $attemptsPerAgent <= 3) {
        echo '#99ff33'; // Green color for 0.1-3 attempts
    } elseif ($attemptsPerAgent > 3 && $attemptsPerAgent <= 6) {
        echo '#33cc33'; // Dark green color for 3.1-6 attempts
    } elseif ($attemptsPerAgent > 6 && $attemptsPerAgent <= 9) {
        echo '#009900'; // Forest green color for 6.1-9 attempts
    } elseif ($attemptsPerAgent > 9 && $attemptsPerAgent <= 12) {
        echo '#00e6ac'; // Cyan color for 9.1-12 attempts
    } elseif ($attemptsPerAgent > 12 && $attemptsPerAgent <= 15) {
        echo '#009999'; // Teal color for 12.1-15 attempts
    } elseif ($attemptsPerAgent > 15 && $attemptsPerAgent <= 20) {
        echo '#3399ff'; // Light blue color for 15.1-20 attempts
    } elseif ($attemptsPerAgent > 20 && $attemptsPerAgent <= 25) {
        echo '#9933ff'; // Purple color for 20.1-25 attempts
    } elseif ($attemptsPerAgent > 25) {
        echo '#9900cc'; // Violet color for more than 25 attempts
    }
    @endphp
    ;">


                        {{ $formattedAttemptsPerAgent }}
                    </td>







                </tr>
            </tbody>
        </table>
        @endforeach


    </div>





    <table id="alter" style="margin-right: 20px;">
        <thead>
            <tr>
                <th></th>
                <th>Totals</th>
                <th>{{ $totalAgentCountAllTeams }}</th>
                <th>{{ $totalLeadCountAllTeams }}</th>
                <th>{{ $totalSaleMadeCountAllTeams }}</th>
                <th>
                    @php
                    $attemptsPerAgent = $totalLeadCountAllTeams / $totalAgentCountAllTeams; // Calculate attempts per agent
                    $formattedAttemptsPerAgent = number_format($attemptsPerAgent, 1); // Format to one decimal place
                    @endphp
                    {{ $formattedAttemptsPerAgent }}
                </th>





                {{-- Empty column for alignment --}}
            </tr>
        </thead>
    </table> <br><br>


    <?php

    use Carbon\Carbon; // Import the Carbon library

    // Initialize the maximum attempts per agent and the Team of the Day variables
    $maxAttemptsPerAgent = 0;
    $teamOfTheDay = '';

    // Initialize an array to hold team information
    $teamsInfo = [];

    foreach ($teams as $team) {
        $totalAgentCount = count($team->agents);
        $totalLeadCount = 0;

        foreach ($team->agents as $agent) {
            $leadCount = $mergedLeadsCount
                ->where('id', $agent->id)
                ->first()
                ->total_lead_count ?? 0;
            $totalLeadCount += $leadCount;
        }

        // Calculate attempts per agent for this team
        $formattedAttemptsPerAgent = $totalLeadCount / $totalAgentCount;
        $attemptsPerAgent = number_format($formattedAttemptsPerAgent, 1);

        // Store team information in the array
        $teamsInfo[] = [
            'name' => $team->name,
            'attemptsPerAgent' => $attemptsPerAgent,
            'totalLeadCount' => $totalLeadCount,
            'totalAgentCount' => $totalAgentCount // Save the totalAgentCount for later use
        ];

        // Update the Team of the Day if this team has more attempts per agent
        if ($attemptsPerAgent > $maxAttemptsPerAgent) {
            $maxAttemptsPerAgent = $attemptsPerAgent;
            $teamOfTheDay = $team->name;
        }
    }

    // Sort teams in descending order based on attempts per agent
    usort($teamsInfo, function ($a, $b) {
        return $b['attemptsPerAgent'] <=> $a['attemptsPerAgent'];
    });

    // Calculate and set the leads needed for each team to be the Team of the Day
    foreach ($teamsInfo as &$teamInfo) {
        $leadsNeeded = ($maxAttemptsPerAgent - $teamInfo['attemptsPerAgent']) * $teamInfo['totalAgentCount'];
        $teamInfo['leadsNeeded'] = ceil($leadsNeeded);
    }
    unset($teamInfo); // Unset the reference to the last item in the array

    // Get the Team of the Day, second and third position teams
    $secondPositionTeam = $teamsInfo[1]['name'] ?? '';
    $thirdPositionTeam = $teamsInfo[2]['name'] ?? '';
    ?>





    <table>
        <thead>
            <tr>
                <th>Position</th>
                <th>Team Name</th>
                <th>Number Of Xfers</th>
                <th>Attempts Per Agent</th>
                <th>Needed Xfers To Be Team Of The Day</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teamsInfo as $index => $teamInfo) : ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $teamInfo['name']; ?></td>
                    <td><?php echo $teamInfo['totalLeadCount']; ?></td>
                    <td><?php echo $teamInfo['attemptsPerAgent']; ?></td>
                    <td><?php echo $teamInfo['leadsNeeded']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>












    <br><br>

    <!-- monthly_stats_leaderboard.blade.php -->

<div class="table-responsive">
    @foreach ($teams as $team)
    <table id="alter" style="margin-right: 20px;">
        <thead>
            <tr>
                <th>{{ $team->name }}</th>
                <th>Xfers</th>
                <th>Pending</th>
                <th>Approved</th>
                <th>Rejected</th>
                <th>Sold</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="6">{{ $team->leader->name }} (TL)</td>
            </tr>
            @foreach ($team->agents as $agent)
            <tr>
                <td>{{ $agent->name }}</td>
                <td>{{ $agent->total_lead_count }}</td>
                <td>{{ $agent->pending_count }}</td>
                <td>{{ $agent->approved_count }}</td>
                <td>{{ $agent->rejected_count }}</td>
                <td>{{ $agent->sale_made_count }}</td>
            </tr>
            @endforeach
            <tr style="background-color: #5e6b62; color: white;">
                <th>Total Xfers</th>
                <th>{{ $team->total_lead_count }}</th>
                <th>{{ $team->total_pending_count }}</th>
                <th>{{ $team->total_approved_count }}</th>
                <th>{{ $team->total_rejected_count }}</th>
                <th>{{ $team->total_sale_made_count }}</th>
            </tr>
        </tbody>
    </table>
    @endforeach
</div>











    <br><br>





</div>




@endsection
<!DOCTYPE html>
<html>
<head>
  



    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
            crossorigin="anonymous"></script>

    <!-- Include DataTables CSS -->
    <link href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- Include DataTables JavaScript -->

    <!-- Other meta tags and links -->
    <link rel="stylesheet" type="text/css" href="/css/leads-report/leads-report.css">

    @include('teams.report_css')
    <script>
        $(document).ready(function() {
            $('#alter').DataTable();
        });
    </script>
</head>
<body>
    @extends('layouts.admin')
    <title>Agent Leads Reports</title>
 @section('content')
<?php
if (Auth::user()->role == 'Team Lead') {
    ?>
    <a href="teams-create"><u>Create New Team</u></a><br>
    <a href="team-assignment"><u>Assign Agents</u></a><br>
    <a href="teams-overview"><u>Remove Agents</u></a><br>
    <a href="list-teams"><u>Teams Management</u></a><br>
    <?php
} else {
    ?>

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
                                <small class="text-muted">{{__('Leaderboard')}}</small>
                                <li><a href="{{ route('leaderboard') }}">{{ __('Leaderboard') }}</a></li> 
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
                            <div class="theme-avtar bg-info">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{__('Create New Team')}}</small>
                                <li><a href="{{ url('approved-reports') }}"><u>Approved Leads Reports</u></a></li>
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
                            <div class="theme-avtar bg-success">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{__('Create New Team')}}</small>
                                <li><a href="{{ url('rejected-reports') }}"><u>Rejected Leads Reports</u></a></li>
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


    





    <?php
}
?>

    




<div class="row">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">                          
                        <thead>
                        <tr>
                            <th>Agent Name</th>
                            @foreach ($dates as $date)
                                <th>{{ $date }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($agents as $agent)
                            <tr>
                                <td>{{ $agent->name }}</td>
                                @foreach ($dates as $date)
                                    <td>
                                        {{ $mergedLeadsCount
                                            ->where('name', $agent->name)
                                            ->where('date', $date)
                                            ->first()
                                            ->total_lead_count ?? 0
                                        }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@endsection
</body>
</html>

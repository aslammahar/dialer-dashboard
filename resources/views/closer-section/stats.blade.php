@extends('layouts.admin')

@section('page-title')



{{ __('All Closers Stats') }}

@endsection



@section('content')
<div class="create-link">
    <a href="{{ route('closed_calls.index') }}" class="btn btn-primary">Manage Policies</a>
</div><br>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h4>Overall Reports</h4>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Total Pending Policies This Month</h6>
                                <p>{{ $pendingCount }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Total Approved Policies This Month</h6>
                                <p>{{ $approvedCount }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Total Rejected Policies This Month</h6>
                                <p>{{ $rejectedCount }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Total Funded Policies This Month</h6>
                                <p>{{ $fundedCount }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Total Charged Backed Policies This Month</h6>
                                <p>{{ $chargedbackedCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- center reports starts here -->


        <!-- center reports ends here -->

        <div class="col-md-12">
            <h4>jsons Stats This Months</h4>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Jsons Total Policies Month</h6>
                                <p>{{ $totalJsonsCount }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Jsons Total Approved This Month</h6>
                                <p>{{ $totalJsonsApproved }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Jsons Total Rejected This Month</h6>
                                <p>{{ $totalJsonsRejected }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Jsons Total Charged Backed This Month</h6>
                                <p>{{ $totalJsonsChargedbacked }}</p>
                            </div>
                        </div>
                    </div> <!-- Closing row here -->
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <h4>Sellerz Stats This Months</h4>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Sellerz Total Policies This Month</h6>
                                <p>{{ $totalSellersCount }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Sellerz Total Approved This Month</h6>
                                <p>{{ $totalSellerzApproved }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Sellerz Total Rejected This Month</h6>
                                <p>{{ $totalSellerzrejected }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Sellerz Total Charged Backed This Month</h6>
                                <p>{{ $totalSellerzChargedbacked }}</p>
                            </div>
                        </div>
                    </div> <!-- Closing row here -->
                </div>
            </div>
        </div>







        <!-- clients status starts here -->
        <div class="col-md-12">
            <h4>Client Status</h4>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Total policies Assigned To Clients</h6>
                                <p>{{ $clientsCount }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Total policies Not Assigned To Clients</h6>
                                <p>{{ $clientsCountMissing }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- clients status ends here -->

        <!-- leaderboard starts here -->

        <h4>Today Leaderboard</h4>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Closers</th>
                                    <th>Total Closed Calls</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usersWithTotalClosedCalls as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->closed_calls_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- leaderboard ends  here -->



    </div>





</div>
<div class="container">

    <h4>This Month Stats Closers</h4>

    <div class="row">
        <!-- monthly reports starts here -->


        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Total Closed Calls</th>
                        <th>Pending</th>
                        <th>Approved</th>
                        <th>Rejected</th>
                        <th>Funded</th>
                        <th>Charged Backed</th>
                        <th>DNF</th>
                        <th>Cancelled</th>
                        <th>NSF</th>
                        <th>DNC</th>
                        <th>Underwriting</th>
                        <th>Need to Reach</th>
                        <!-- Add more columns for other statuses if needed -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($userStats as $stat)
                    <tr>
                        <td>{{ $stat->closer->name }}</td> <!-- Assuming there's a relationship to retrieve the closer's name -->
                        <td>{{ $stat->total }}</td>
                        <td>{{ $stat->pending }}</td>
                        <td>{{ $stat->approved }}</td>
                        <td>{{ $stat->rejected }}</td>
                        <td>{{ $stat->funded }}</td>
                        <td>{{ $stat->charged_backed }}</td>
                        <td>{{ $stat->DNF }}</td>
                        <td>{{ $stat->Cancelled }}</td>
                        <td>{{ $stat->NSF }}</td>
                        <td>{{ $stat->DNC }}</td>
                        <td>{{ $stat->Underwriting }}</td>
                        <td>{{ $stat->NeedtoReach }}</td>
                        <!-- Add more cells for other statuses if needed -->
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- monthly reports ends here -->
    </div>
</div>



@endsection
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <!-- links goes here  -->
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
                                    <small class="text-muted">{{ __('Create Lead') }}</small>
                                    <li><a href="{{ route('leads.create') }}" class="btn btn-sm btn-info">Create Lead</a></li>
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

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-warning">
                                    <i class="ti ti-cast"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Leaderboard Daily') }}</small>
                                    <li><a href="{{ route('avatar-leaderboard-daily') }}" class="btn btn-sm btn-warning">Leaderboard Daily</a></li>
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

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-danger">
                                    <i class="ti ti-cast"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Leaderboard Monthly') }}</small>
                                    <li><a href="{{ route('avatar-leaderboard-monthly') }}" class="btn btn-sm btn-danger">Leaderboard Monthly</a></li>
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
        <!-- links ends here  -->

        <div class="col-md-12">
            <h1>Search Leads</h1>

            <!-- Search form -->


            <div class="row">
                <!-- Search form -->
                <div class="col-md-6">
                    <form action="{{ route('avatar.all_avatar_leads') }}" method="POST">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" name="search" class="form-control" placeholder="Search by Lead ID , Phone Number or Dialer Id" value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <!-- Display search results -->
                <div class="col-md-12">
                    @if($searchResults->isNotEmpty())
                    <h2>Search Results</h2>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <!-- Table header -->
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Agent Name</th>
                                    <th>Lead Id</th>
                                    <!-- <th>Phone Number</th> -->
                                    <th>Dialer Id</th>
                                    <th>Recording Link</th>
                                    <th>Is Greetings</th>
                                    <th>Is Pitch Call About</th>
                                    <th>Is Age</th>
                                    <th>Is Smoker</th>
                                    <th>Is Health1</th>
                                    <th>Is Beneficiary</th>
                                    <th>Is Account</th>
                                    <th>Is Plan</th>
                                    <th>Is Transfer Details</th>
                                    <th>Is Xfer Consent</th>
                                    <th>Rebuttals</th>
                                    <th>QA Comments</th>
                                    <th>QA Status</th>
                                    <th>Use Of Rebuttals</th>

                                    <th>Time</th>
                                </tr>
                            </thead>
                            <!-- Table body -->
                            <tbody>
                                @foreach($searchResults as $avatarlead)
                                <tr>
                                    <td>{{ $avatarlead->id }}</td>
                                    <td>{{ $avatarlead->agent_name }}</td>
                                    <td>{{ $avatarlead->lead_id }}</td>
                                    <!-- <td>{{ $avatarlead->phone_number }}</td> -->
                                    <td>{{ $avatarlead->dialer_id }}</td>
                                    <td>
                                        <audio controls>
                                            <source src="{{ $avatarlead->recording_link }}" type="audio/mpeg">
                                        </audio>
                                    </td>
                                    <td>{{ $avatarlead->Isgreetings }}</td>
                                    <td>{{ $avatarlead->Ispitch_call_about }}</td>
                                    <td>{{ $avatarlead->Isage }}</td>
                                    <td>{{ $avatarlead->Issmoker }}</td>
                                    <td>{{ $avatarlead->Ishealth1 }}</td>
                                    <td>{{ $avatarlead->Isbeneficiary }}</td>
                                    <td>{{ $avatarlead->Isaccount }}</td>
                                    <td>{{ $avatarlead->Isplan }}</td>
                                    <td>{{ $avatarlead->Istransfer_details }}</td>
                                    <td>{{ $avatarlead->Isxfer_consent }}</td>
                                    <td>{{ $avatarlead->rebuttals }}</td>
                                    <td>{{ $avatarlead->Qacomments }}</td>
                                    <td style="background-color: 
                                @if($avatarlead->QAstatus == 'approved') #5cd65c
                                @elseif($avatarlead->QAstatus == 'rejected') #ff3333
                                @elseif($avatarlead->QAstatus == 'pending') #e6f2ff
                                @endif;">
                                        {{ $avatarlead->QAstatus }}
                                    </td>
                                    <td>{{ $avatarlead->use_of_rebuttals }}</td>

                                    <td>{{ $avatarlead->created_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <!-- Display all Avatar Leads -->
                <div class="col-md-12">
                    <h2>All Avatar Leads</h2>
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Agent Name</th>
                                        <th>Lead Id</th>
                                        <!-- <th>Phone Number</th> -->
                                        <th>Dialer Id</th>
                                        <th>Recording Link</th>
                                        <th>Is Greetings</th>
                                        <th>Is Pitch Call About</th>
                                        <th>Is Age</th>
                                        <th>Is Smoker</th>
                                        <th>Is Health1</th>
                                        <th>Is Beneficiary</th>
                                        <th>Is Account</th>
                                        <th>Is Plan</th>
                                        <th>Is Transfer Details</th>
                                        <th>Is Xfer Consent</th>
                                        <th>Rebuttals</th>
                                        <th>QA Comments</th>
                                        <th>QA Status</th>
                                        <th>Use Of Rebuttals</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($avatarLeads as $avatarlead)
                                    <tr>
                                        <td>{{ $avatarlead->id }}</td>
                                        <td>{{ $avatarlead->agent_name }}</td>
                                        <td>{{ $avatarlead->lead_id }}</td>
                                        <!-- <td>{{ $avatarlead->phone_number }}</td> -->
                                        <td>{{ $avatarlead->dialer_id }}</td>
                                        <td>
                                            <audio controls>
                                                <source src="{{ $avatarlead->recording_link }}" type="audio/mpeg">
                                            </audio>
                                        </td>
                                        <td>{{ $avatarlead->Isgreetings }}</td>
                                        <td>{{ $avatarlead->Ispitch_call_about }}</td>
                                        <td>{{ $avatarlead->Isage }}</td>
                                        <td>{{ $avatarlead->Issmoker }}</td>
                                        <td>{{ $avatarlead->Ishealth1 }}</td>
                                        <td>{{ $avatarlead->Isbeneficiary }}</td>
                                        <td>{{ $avatarlead->Isaccount }}</td>
                                        <td>{{ $avatarlead->Isplan }}</td>
                                        <td>{{ $avatarlead->Istransfer_details }}</td>
                                        <td>{{ $avatarlead->Isxfer_consent }}</td>
                                        <td>{{ $avatarlead->rebuttals }}</td>
                                        <td>{{ $avatarlead->Qacomments }}</td>
                                        <td style="background-color: 
                                @if($avatarlead->QAstatus == 'approved') #5cd65c
                                @elseif($avatarlead->QAstatus == 'rejected') #ff3333
                                @elseif($avatarlead->QAstatus == 'pending') #e6f2ff
                                @endif;">
                                            {{ $avatarlead->QAstatus }}
                                        </td>
                                        <td>{{ $avatarlead->use_of_rebuttals }}</td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
    @endsection
@extends('layouts.admin')

@section('page-title')
    {{ __('QA Stats') }}
@endsection

@section('content')



    <body>

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
                                        <small class="text-muted">{{ __('Assign Leads') }} </small>
                                        <li><a href="avatar-q-a-leads" class="btn btn-sm btn-success">Assign Leads</a></li>
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
                                        <small class="text-muted">{{ __('MY Leads') }} </small>
                                        <li><a href="avatar-calls" class="btn btn-sm btn-danger">My Leads</a></li>
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
                                        <small class="text-muted">{{ __('QA section') }} </small>
                                        <li><a href="qa-section" class="btn btn-sm btn-warning">QA section</a></li>
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
                                    <div class="theme-avtar bg-info">
                                        <i class="ti ti-cast"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted">{{ __('Upload Recordings') }} </small>
                                        <li><a href="no-rec-leads" class="btn btn-sm btn-info">Upload Recordings</a></li>
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


        <div class="container">

            <form action="{{ route('avatar-leads.filter-qa-stats') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" class="form-control" id="start_date" name="start_date"
                        value="{{ request('start_date') }}">
                </div>
                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" class="form-control" id="end_date" name="end_date"
                        value="{{ request('end_date') }}">
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            @if ($qaStats->isNotEmpty())
                <h3>Results</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>QA Person</th>
                            <th>Pending</th>
                            <th>Approved</th>
                            <th>Rejected</th>
                            <th>On Review</th>
                            <th>No Recording</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($qaStats as $qaPersonId => $stats)
                            <tr>
                                <td>{{ $stats['name'] }}</td>
                                <td>{{ $stats['Pending'] }}</td>
                                <td>{{ $stats['Approved'] }}</td>
                                <td>{{ $stats['Rejected'] }}</td>
                                <td>{{ $stats['On Review'] }}</td>
                                <td>{{ $stats['No Recording'] }}</td>
                                <td>{{ $stats['Total'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </body>

@endsection

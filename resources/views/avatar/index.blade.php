@extends('layouts.admin')

@section('page-title')
    {{ __('Avatar Home') }}
@endsection
<!-- cistome css here -->
<style>
    @import url("https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap");

    .cardBox {
        position: relative;
        width: 100%;
        padding: 20px;
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        grid-gap: 30px;
    }

    * {
        font-family: "Ubuntu", sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }


    .cardBox .card {
        position: relative;
        background: var(--white);
        padding: 30px;
        border-radius: 20px;
        display: flex;
        justify-content: space-between;
        cursor: pointer;
        box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
    }

    .cardBox .card .numbers {
        position: relative;
        font-weight: 500;
        font-size: 2.5rem;
        color: var(--blue);

    }

    .cardBox .card .iconBx {
        font-size: 3.5rem;
        color: var(--black2);
        display: flex;
        /* Added */
        align-items: center;
        /* Added */
    }

    .cardBox .card .iconBx ion-icon {
        margin-left: 10px;
        /* Adjust spacing between the content and the icon */
    }

    .cardBox .card .iconBx {
        font-size: 3.5rem;
        color: var(--black2);
    }

    .cardBox .card:hover {
        background: var(--blue);
    }

    .cardBox .card:hover .numbers,
    .cardBox .card:hover .cardName,
    .cardBox .card:hover .iconBx {
        color: var(--white);
    }


    .details .cardHeader {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .cardHeader h2 {
        font-weight: 600;
        color: var(--blue);
    }

    .cardHeader .btn {
        position: relative;
        padding: 5px 10px;
        background: var(--blue);
        text-decoration: none;
        color: var(--white);
        border-radius: 6px;
    }

    :root {
        --blue: #2a2185;
        --white: #fff;
        --gray: #f5f5f5;
        --black1: #222;
        --black2: #999;
    }

    .container {
        position: relative;
        width: 100%;
    }

    body {
        min-height: 100vh;
        overflow-x: hidden;
    }

    .navigation {
        position: fixed;
        width: 300px;
        height: 100%;
        background: var(--blue);
        border-left: 10px solid var(--blue);
        transition: 0.5s;
        overflow: hidden;
    }

    .main {
        position: absolute;
        width: calc(100% - 300px);
        left: 300px;
        min-height: 100vh;
        background: var(--white);
        transition: 0.5s;
    }

    .main.active {
        width: calc(100% - 80px);
        left: 80px;
    }

    .topbar {
        width: 100%;
        height: 60px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 10px;
    }

    .toggle {
        position: relative;
        width: 60px;
        height: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 2.5rem;
        cursor: pointer;
    }

    .details .cardHeader {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .cardHeader h2 {
        font-weight: 600;
        color: var(--blue);
    }

    .cardHeader .btn {
        position: relative;
        padding: 5px 10px;
        background: var(--blue);
        text-decoration: none;
        color: var(--white);
        border-radius: 6px;
    }
</style>
<!-- cistome css ends here -->

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            {{-- <h2>{{ $sectionTitle }}</h2> --}}
            {{-- <a href="{{ route('') }}" class="h6 d-inline-block font-weight-400 mb-0">Monthly Leads</a> --}}
        </div>

        <!-- <a href="{{ route('leads.create') }}">Create Lead</a><br>
                    <a href="{{ route('my-avatar-checked-leads') }}">My Checked Leads</a><br>
                    <a href="{{ route('avatar-leaderboard-daily') }}">Leaderboard Daily</a><br>
                    <a href="{{ route('avatar-leaderboard-monthly') }}">Leaderboard Monthly</a><br> -->







        <div class="row">




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
                                        <small class="text-muted">{{ __('All Leads') }}</small>
                                        <li><a href="{{ route('all_avatar-leads') }}" class="btn btn-sm btn-success">All
                                                Leads</a></li>
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
                                        <small class="text-muted">{{ __('Create Lead') }}</small>
                                        <li><a href="{{ route('leads.create') }}" class="btn btn-sm btn-info">Create
                                                Lead</a></li>
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
                                        <small class="text-muted">{{ __('Leaderboard') }}</small>
                                        <li><a href="{{ route('leaderboard') }}"
                                                class="btn btn-sm btn-primary">{{ __('Leaderboard') }}</a></li>
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
                                        <small class="text-muted">{{ __('Leaderboard Daily') }} </small>
                                        <li><a href="{{ route('avatar-leaderboard-daily') }}"
                                                class="btn btn-sm btn-warning">Leaderboard Daily</a></li>
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
                                        <small class="text-muted">{{ __('Leaderboard Monthly') }} </small>
                                        <li><a href="{{ route('avatar-leaderboard-monthly') }}"
                                                class="btn btn-sm btn-danger">Leaderboard Monthly</a></li>
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
                                    <div class="theme-avtar bg-success">
                                        <i class="ti ti-cast"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted">{{ __('Dialer') }} </small>
                                        <li><a href="dialer" class="btn btn-sm btn-success">Dialer</a></li>
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




            <!-- Table for pending leads -->
            <!-- Table for pending leads -->
            <!-- Table for pending leads -->

            <div class="cardBox">
                <div class="card">
                    <div>
                        <div class="numbers">{{ $pendingCount }}</div>
                        <div class="cardName">Total Pending Leads</div>
                        <div class="iconBx">
                            <ion-icon name="hourglass-outline"></ion-icon>

                        </div>
                    </div>

                </div>

                <div class="card">
                    <div>
                        <div class="numbers">{{ $approvedCount }}</div>
                        <div class="cardName">Total Approved Leads</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="checkmark-done-outline"></ion-icon>

                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="numbers">{{ $rejectedCount }}</div>
                        <div class="cardName">Total Rejected Leads</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="checkmark-done-outline"></ion-icon>

                    </div>
                </div>

            </div>



            <!-- Table for approved leads -->
            @if ($pendingLeads->isNotEmpty())
                <h3>Pening Leads</h3>
                <div class="table-responsive">
                    <table class="table datatable table-striped table-bordered .table-condensed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Lead ID</th>
                                <th>Dialer ID</th>
                                <th>Recording Link</th>
                                <th>Is Greetings</th>
                                <th>Is Pitch Call About</th>
                                <th>Is Age</th>
                                <th>Is Smoker</th>
                                <th>Is Transfer Details</th>
                                <th>Is Transfer Consent</th>
                                <th>QA Status</th>

                                <th>Billing Status</th>
                                <th>QA Comments</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingLeads as $lead)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $lead->lead_id }}</td>
                                    <td>{{ $lead->dialer_id }}</td>
                                    <td>
                                        <a href="{{ $lead->recording_link }}" target="_blank"
                                            class="btn btn-primary btn-sm">Listen</a>
                                    </td>
                                    <td>{{ $lead->Isgreetings }}</td>
                                    <td>{{ $lead->Ispitch_call_about }}</td>
                                    <td>{{ $lead->Isage }}</td>
                                    <td>{{ $lead->Issmoker }}</td>
                                    <td>{{ $lead->Istransfer_details }}</td>
                                    <td>{{ $lead->Isxfer_consent }}</td>
                                    <td>{{ $lead->QAstatus }}</td>
                                    <td>{{ $lead->billing_status }}</td>

                                    <td>{{ $lead->Qacomments }}</td>
                                    <td>{{ $lead->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No Pending leads found.</p>
            @endif
            @if ($approvedLeads->isNotEmpty())
                <h3>Approved Leads</h3>
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Lead ID</th>
                                <th>Dialer ID</th>
                                <th>Recording Link</th>
                                <th>Is Greetings</th>
                                <th>Is Pitch Call About</th>
                                <th>Is Age</th>
                                <th>Is Smoker</th>
                                <th>Is Transfer Details</th>
                                <th>Is Transfer Consent</th>
                                <th>QA Status</th>
                                <th>Billing Status</th>

                                <th>QA Comments</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($approvedLeads as $lead)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $lead->lead_id }}</td>
                                    <td>{{ $lead->dialer_id }}</td>
                                    <td>{{ $lead->recording_link }}</td>
                                    <td>{{ $lead->Isgreetings }}</td>
                                    <td>{{ $lead->Ispitch_call_about }}</td>
                                    <td>{{ $lead->Isage }}</td>
                                    <td>{{ $lead->Issmoker }}</td>
                                    <td>{{ $lead->Istransfer_details }}</td>
                                    <td>{{ $lead->Isxfer_consent }}</td>
                                    <td>{{ $lead->QAstatus }}</td>
                                    <td>{{ $lead->billing_status }}</td>

                                    <td>{{ $lead->Qacomments }}</td>
                                    <td>{{ $lead->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No Approved leads found.</p>
            @endif
            @if ($rejectedLeads->isNotEmpty())
                <h3>Rejected Leads</h3>
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Lead ID</th>
                                <th>Dialer ID</th>
                                <th>Recording Link</th>
                                <th>Is Greetings</th>
                                <th>Is Pitch Call About</th>
                                <th>Is Age</th>
                                <th>Is Smoker</th>
                                <th>Is Transfer Details</th>
                                <th>Is Transfer Consent</th>
                                <th>QA Status</th>
                                <th>Billing Status</th>

                                <th>QA Comments</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rejectedLeads as $lead)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $lead->lead_id }}</td>
                                    <td>{{ $lead->dialer_id }}</td>
                                    <td>{{ $lead->recording_link }}</td>
                                    <td>{{ $lead->Isgreetings }}</td>
                                    <td>{{ $lead->Ispitch_call_about }}</td>
                                    <td>{{ $lead->Isage }}</td>
                                    <td>{{ $lead->Issmoker }}</td>
                                    <td>{{ $lead->Istransfer_details }}</td>
                                    <td>{{ $lead->Isxfer_consent }}</td>
                                    <td>{{ $lead->QAstatus }}</td>
                                    <td>{{ $lead->billing_status }}</td>

                                    <td>{{ $lead->Qacomments }}</td>
                                    <td>{{ $lead->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No Rejected leads found.</p>
            @endif


            @if ($otherLeads->isNotEmpty())
                <h3>Other Leads</h3>
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Lead ID</th>
                                <th>Dialer ID</th>
                                <th>Recording Link</th>
                                <th>Is Greetings</th>
                                <th>Is Pitch Call About</th>
                                <th>Is Age</th>
                                <th>Is Smoker</th>
                                <th>Is Transfer Details</th>
                                <th>Is Transfer Consent</th>
                                <th>QA Status</th>
                                <th>Billing Status</th>

                                <th>QA Comments</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($otherLeads as $lead)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $lead->lead_id }}</td>
                                    <td>{{ $lead->dialer_id }}</td>
                                    <td>{{ $lead->recording_link }}</td>
                                    <td>{{ $lead->Isgreetings }}</td>
                                    <td>{{ $lead->Ispitch_call_about }}</td>
                                    <td>{{ $lead->Isage }}</td>
                                    <td>{{ $lead->Issmoker }}</td>
                                    <td>{{ $lead->Istransfer_details }}</td>
                                    <td>{{ $lead->Isxfer_consent }}</td>
                                    <td>{{ $lead->QAstatus }}</td>
                                    <td>{{ $lead->billing_status }}</td>

                                    <td>{{ $lead->Qacomments }}</td>
                                    <td>{{ $lead->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No other leads found.</p>
            @endif



        </div>


    @endsection

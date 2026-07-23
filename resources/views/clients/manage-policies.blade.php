@extends('layouts.admin')

@section('page-title')
{{ __('Manage Polices') }}
@endsection

@section('content')



<!-- clients stats goes here -->

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h4>Overall Reports</h4>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Total Pending Polocies</h6>
                                <p>{{ $pendingCount }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Total Approved Polocies</h6>
                                <p>{{ $approvedCount }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="box pending-box">
                                <h6>Total Rejected Polocies</h6>
                                <p>{{ $rejectedCount }}</p>
                            </div>
                        </div>
                      
                    </div>
                </div>
            </div>
        </div>



    </div>
</div>
<!-- clients stats ends here -->





<div class="container mt-4">
    <div class="table-responsive">
        <table id="closedCallsTable" class="table table-bordered table-striped align-middle">
            <thead style="background-color: #000000; color: #ffffff; font-weight: bold;">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Address</th>
                    <th>Personal Info</th>
                    <th>Physical Info</th>
                    <th>Medical Info</th>
                    <th>Insurance</th>
                    <th>Beneficiary</th>
                    <th>Drafts</th>
                    <th>Remarks</th>
                  
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($closedCalls as $closedCall)
                    <tr>
                        <td>{{ $closedCall->id }}</td>
                        <td>{{ $closedCall->customer_full_name }}</td>

                        <!-- Address -->
                        <td>
                            {{ $closedCall->address }}<br>
                            {{ $closedCall->city }}, {{ $closedCall->state }} {{ $closedCall->zip_code }}
                        </td>

                        <!-- Personal Info -->
                        <td>
                            <strong>Age:</strong> {{ $closedCall->age }}<br>
                            <strong>DOB:</strong> {{ $closedCall->dob ? $closedCall->dob->format('F j, Y') : 'N/A' }}<br>
                            <strong>Smoker:</strong> {{ $closedCall->smoker }}<br>
                            <strong>Gender:</strong> {{ $closedCall->gender }}<br>
                            <strong>Marital Status:</strong> {{ $closedCall->martial_status }}<br>
                            <strong>Birthplace:</strong> {{ $closedCall->palce_of_birth }}
                        </td>

                        <!-- Physical Info -->
                        <td>
                            <strong>Height:</strong> {{ $closedCall->height }}<br>
                            <strong>Weight:</strong> {{ $closedCall->weight }}<br>
                            <strong>SSN:</strong> {{ $closedCall->social_security }}
                        </td>

                        <!-- Medical Info -->
                        <td>
                            <strong>Health:</strong> {{ $closedCall->health_condition }}<br>
                            <strong>Medications:</strong> {{ $closedCall->medication }}<br>
                            <strong>Hospital:</strong> {{ $closedCall->hospital_name }}<br>
                            <strong>Physician:</strong> {{ $closedCall->physician_name }}
                        </td>

                        <!-- Insurance Info -->
                        <td>
                            <strong>Premium:</strong> ${{ $closedCall->monthly_premium }}<br>
                            <strong>Carrier:</strong> {{ $closedCall->carrier }}<br>
                            <strong>Plan:</strong> {{ $closedCall->coverage_plan }}<br>
                            <strong>Eligibility:</strong> {{ $closedCall->customer_eligibility }}
                        </td>

                        <!-- Beneficiary Info -->
                        <td>
                            <strong>Name:</strong> {{ $closedCall->beneficiary }}<br>
                            <strong>Relation:</strong> {{ $closedCall->beneficiary_relation }}<br>
                            <strong>Phone:</strong> {{ $closedCall->beneficiary_phone }}<br>
                            <strong>Alternative Phone:</strong> {{ $closedCall->beneficiary_phone }}<br>

                            <strong>DOB:</strong> {{ $closedCall->beneficiary_dob ? $closedCall->beneficiary_dob->format('F j, Y') : 'N/A' }}
                        </td>

                        <!-- Draft Info -->
                        <td>
                            <strong>Initial:</strong> {{ $closedCall->initial_draft_date ? $closedCall->initial_draft_date->format('F j, Y') : 'N/A' }}<br>
                            <strong>Future:</strong> {{ $closedCall->future_draft_date ? $closedCall->future_draft_date->format('F j, Y') : 'N/A' }}
                        </td>

                        <td>{{ $closedCall->remarks }}</td>
                        
                        <td>
                            <span class="badge bg-success">{{ $closedCall->status }}</span>
                        </td>

                        <!-- Actions -->
                        <td>
    <a href="{{ route('closed-calls.show', $closedCall->id) }}" class="btn btn-primary btn-sm">
        View
    </a>
</td>

                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination Links with Custom Styles -->
        <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination">
                    <!-- Custom Pagination links -->
                    {{ $closedCalls->links('pagination::bootstrap-5') }}
                </ul>
            </nav>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>


@endsection
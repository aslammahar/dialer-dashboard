@extends('layouts.admin')

@section('page-title')
    {{ __('View Closed Call Details') }}
@endsection

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header justify-content-between">
            <h4>Closed Call Details</h4>
            <!-- Edit Button -->

            


            @can(abilities: 'manage closedcall')
            <a href="{{ url('/outsource-dialer-edit', $closedCall->id) }}" class="btn btn-info btn-sm">Dialer Edit</a>
                   
            @endcan
            

            @can('manage closedcall')
            <a href="{{ url('/edit-outsource-calls', parameters: $closedCall->id) }}" class="btn btn-info btn-sm">Admin Edit</a>
                   
            @endcan
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Customer Info -->
                <div class="col-md-6">
                    <h5>Customer Info</h5>
                    <p><strong>Name:</strong> {{ $closedCall->customer_full_name }}</p>
                    <p><strong>Address:</strong> {{ $closedCall->address }}, {{ $closedCall->city }}, {{ $closedCall->state }} {{ $closedCall->zip_code }}</p>
                    @can(abilities: 'show phone')
                    <p><strong>Phone :</strong> {{ $closedCall->phone_number }}</p>
            @endcan

                </div>

                <!-- Personal Info -->
                <div class="col-md-6">
                    <h5>Personal Info</h5>
                    <p><strong>Age:</strong> {{ $closedCall->age }}</p>
                    <p><strong>DOB:</strong> {{ $closedCall->dob ? $closedCall->dob->format('F j, Y') : 'N/A' }}</p>
                    <p><strong>Place Of Birth:</strong> {{ $closedCall->palce_of_birth }}</p>

                    <p><strong>Smoker:</strong> {{ $closedCall->smoker }}</p>
                    <p><strong>Gender:</strong> {{ $closedCall->gender }}</p>
                    <p><strong>Marital Status:</strong> {{ $closedCall->martial_status }}</p>
                </div>
            </div>

            <hr>

            <div class="row">
                <!-- Physical Info -->
                <div class="col-md-6">
                    <h5>Physical Info</h5>
                    <p><strong>Height:</strong> {{ $closedCall->height }}</p>
                    <p><strong>Weight:</strong> {{ $closedCall->weight }}</p>
                    <p><strong>SSN:</strong> {{ $closedCall->social_security }}</p>
                </div>

                <!-- Medical Info -->
                <div class="col-md-6">
                    <h5>Medical Info</h5>
                    <p><strong>Health Condition:</strong> {{ $closedCall->health_condition }}</p>
                    <p><strong>Medications:</strong> {{ $closedCall->medication }}</p>
                    <p><strong>Hospital:</strong> {{ $closedCall->hospital_name }}</p>
                    <p><strong>Physician:</strong> {{ $closedCall->physician_name }}</p>
                </div>
            </div>

            <hr>

            <div class="row">
                <!-- Insurance Info -->
                <div class="col-md-6">
                    <h5>Insurance Info</h5>
                    <p><strong>Premium:</strong> ${{ $closedCall->monthly_premium }}</p>
                    <p><strong>Carrier:</strong> {{ $closedCall->carrier }}</p>
                    <p><strong>Plan:</strong> {{ $closedCall->coverage_plan }}</p>
                    <p><strong>Eligibility:</strong> {{ $closedCall->customer_eligibility }}</p>
                </div>

                <!-- Beneficiary Info -->
                <div class="col-md-6">
                    <h5>Beneficiary Info</h5>
                    <p><strong>Name:</strong> {{ $closedCall->beneficiary }}</p>
                    <p><strong>Relation:</strong> {{ $closedCall->beneficiary_relation }}</p>
                      @can(abilities: 'show phone')
                    <p><strong>Phone:</strong> {{ $closedCall->beneficiary_phone }}</p>
                                @endcan

                    <p><strong>DOB:</strong> {{ $closedCall->beneficiary_dob ? $closedCall->beneficiary_dob->format('F j, Y') : 'N/A' }}</p>
                </div>
            </div>

            <hr>

            <div class="row">
                <!-- Draft Info -->
                <div class="col-md-6">
                    <h5>Draft Info</h5>
                    <p><strong>Initial Draft:</strong> {{ $closedCall->initial_draft_date ? $closedCall->initial_draft_date->format('F j, Y') : 'N/A' }}</p>
                    <p><strong>Future Draft:</strong> {{ $closedCall->future_draft_date ? $closedCall->future_draft_date->format('F j, Y') : 'N/A' }}</p>
                    <p><strong>Remarks:</strong> {{ $closedCall->remarks }}</p>

                </div>

                <!-- Additional Info -->
                <div class="col-md-6">
                    <h5>Additional Info</h5>
                    <p><strong>Filled By:</strong> {{ $closedCall->closer->name }}</p>
                    <p><strong>Closer:</strong> {{ $closedCall->closername }}</p>

                    <p><strong>Junior Closer:</strong> {{ $closedCall->juniorcloser->name ?? $closedCall->junior_closer_name }}</p>
                    <p><strong>Center:</strong> {{ $closedCall->center_name }}</p>
                    <p><strong>Sale Made By:</strong> {{ $closedCall->sale_made_by }}</p>
                </div>
            </div>
            <div class="row">
                <!-- Draft Info -->
                <div class="col-md-6">
<p><strong>Client Name:</strong> {{ $closedCall->client->name ?? 'N/A' }}</p>
                    <h5>Client  Comment</h5>
                    <p>{{ $closedCall->clients_comment }}</p>
                </div>

                <!-- Additional Info -->
                
            </div>
        </div>
    </div>
</div>
@endsection

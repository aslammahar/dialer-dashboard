<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .mail {
            display: flex;
            align-items: center;
        }
        .existing-record-card {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .duplicate-checkbox-container {
            background-color: #f8f9fa;
            border: 2px solid #0d6efd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .text-danger {
            color: #dc3545;
        }
    </style>
</head>

<body>

    @extends('layouts.admin')

    @section('page-title')
    {{ __('Update Admin') }}
    @endsection

    @section('content')

    <div class="container mt-5">
{{-- validation & flash messages --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>There were some problems with your input:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
        <!-- Existing Record Alert -->
        @if(session('error') && session('existing_record') && session('show_duplicate_warning'))
        <div class="alert alert-danger alert-dismissible fade show existing-record-card" role="alert">
            <h5 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>{{ session('error') }}</strong>
            </h5>
            <hr>
            <p class="mb-2"><strong>An existing record was found with the following details:</strong></p>
            
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Record ID:</strong> {{ session('existing_record')->id }}</p>
                    <p class="mb-1"><strong>Customer Name:</strong> {{ session('existing_record')->customer_full_name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Phone Number:</strong> {{ session('existing_record')->phone_number ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Alt. Phone:</strong> {{ session('existing_record')->alternate_phone_number ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Address:</strong> {{ session('existing_record')->address ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>City:</strong> {{ session('existing_record')->city ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>State:</strong> {{ session('existing_record')->state ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Gender:</strong> {{ ucfirst(session('existing_record')->gender ?? 'N/A') }}</p>
                </div>
            </div>
            
            <div class="row mt-2">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Carrier:</strong> {{ session('existing_record')->carrier ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Coverage Plan:</strong> 
                        @if(session('existing_record')->coverage_plan)
                            ${{ number_format(session('existing_record')->coverage_plan) }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
            
            <hr>
            <div class="alert alert-warning mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>To proceed with this update anyway, check the "Allow Duplicate Entry" box below and submit again.</strong>
            </div>
            
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Error Message -->
        @if(session('error') && !session('show_duplicate_warning'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>{{ session('error') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row">
            <div class="">
                <form action="" method="POST" id="updateForm">
                    @csrf
                    @method('PUT')

                    <!-- ALWAYS VISIBLE DUPLICATE CHECKBOX -->
                    <div class="duplicate-checkbox-container">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="allow_duplicate" id="allow_duplicate" value="1">
                            <label class="form-check-label" for="allow_duplicate">
                                <strong><i class="fas fa-check-square text-primary me-2"></i>Allow Duplicate Phone Number</strong>
                                <p class="mb-0 text-muted small">Check this box if you want to submit this form even if the phone number already exists in the system.</p>
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_full_name" class="form-label">Customer Full Name</label>
                            <input type="text" class="form-control" id="customer_full_name" name="customer_full_name" value="{{ old('customer_full_name', $update->customer_full_name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="number" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $update->phone_number) }}">
                            <small class="text-muted" id="phone_check_message"></small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="alternate_phone_number">Alternate Phone Number</label>
                            <input type="number" class="form-control" id="alternate_phone_number" name="alternate_phone_number" value="{{ old('alternate_phone_number', $update->alternate_phone_number) }}">
                            <small class="text-muted" id="alt_phone_check_message"></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cx_email">CX Email</label>
                            <input type="email" class="form-control" id="cx_email" name="cx_email" value="{{ old('cx_email', $update->cx_email) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $update->address) }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $update->city) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="state">State</label>
                            <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $update->state) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="zip_code" class="form-label">Zip Code</label>
                            <input type="number" class="form-control" id="zip_code" name="zip_code" value="{{ old('zip_code', $update->zip_code) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $update->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $update->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $update->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="martial_status">Marital Status</label>
                            <select class="form-control" id="martial_status" name="martial_status">
                                <option value="">Select Marital Status</option>
                                <option value="single" {{ old('martial_status', $update->martial_status) == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('martial_status', $update->martial_status) == 'married' ? 'selected' : '' }}>Married</option>
                                <option value="divorced" {{ old('martial_status', $update->martial_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="widowed" {{ old('martial_status', $update->martial_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="separated" {{ old('martial_status', $update->martial_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" class="form-control" id="age" name="age" value="{{ old('age', $update->age) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dob">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" 
                                   value="{{ old('dob', $update->dob ? date('Y-m-d', strtotime($update->dob)) : '') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="social_security" class="form-label">Social Security</label>
                            <input type="text" class="form-control" id="social_security" name="social_security" value="{{ old('social_security', $update->social_security) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Smoker</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="smoker" id="smoker_yes" value="yes" {{ old('smoker', $update->smoker) == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="smoker_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="smoker" id="smoker_no" value="no" {{ old('smoker', $update->smoker) == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="smoker_no">No</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="health_condition">Health Condition</label>
                            <textarea class="form-control" id="health_condition" name="health_condition" rows="3">{{ old('health_condition', $update->health_condition) }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="medication" class="form-label">Medication</label>
                            <input type="text" class="form-control" id="medication" name="medication" value="{{ old('medication', $update->medication) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="hospital_name" class="form-label">Hospital Name</label>
                            <input type="text" class="form-control" id="hospital_name" name="hospital_name" value="{{ old('hospital_name', $update->hospital_name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hospital_address">Hospital Address</label>
                            <input type="text" class="form-control" id="hospital_address" name="hospital_address" value="{{ old('hospital_address', $update->hospital_address) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="physician_name">Physician Name</label>
                            <input type="text" class="form-control" id="physician_name" name="physician_name" value="{{ old('physician_name', $update->physician_name) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="monthly_premium">Monthly Premium</label>
                            <input type="text" class="form-control" id="monthly_premium" name="monthly_premium" value="{{ old('monthly_premium', $update->monthly_premium) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="coverage_plan">Coverage Plan</label>
                            <input type="text" class="form-control" id="coverage_plan" name="coverage_plan" value="{{ old('coverage_plan', $update->coverage_plan) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_eligibility">Customer Eligibility</label>
                            <input type="text" class="form-control" id="customer_eligibility" name="customer_eligibility" value="{{ old('customer_eligibility', $update->customer_eligibility) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="beneficiary">Beneficiary</label>
                            <input type="text" class="form-control" id="beneficiary" name="beneficiary" value="{{ old('beneficiary', $update->beneficiary) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="beneficiary_relation">Beneficiary Relation</label>
                            <input type="text" class="form-control" id="beneficiary_relation" name="beneficiary_relation" value="{{ old('beneficiary_relation', $update->beneficiary_relation) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="beneficiary_phone">Beneficiary Phone</label>
                            <input type="number" class="form-control" id="beneficiary_phone" name="beneficiary_phone" value="{{ old('beneficiary_phone', $update->beneficiary_phone) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="beneficiary_dob">Beneficiary Date of Birth</label>
                            <input type="date" class="form-control" id="beneficiary_dob" name="beneficiary_dob" 
                                   value="{{ old('beneficiary_dob', $update->beneficiary_dob ? date('Y-m-d', strtotime($update->beneficiary_dob)) : '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payor">Payor</label>
                            <input type="text" class="form-control" id="payor" name="payor" value="{{ old('payor', $update->payor) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $update->bank_name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bank_address">Bank Address</label>
                            <input type="text" class="form-control" id="bank_address" name="bank_address" value="{{ old('bank_address', $update->bank_address) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="routing_number">Routing Number</label>
                            <input type="number" class="form-control" id="routing_number" name="routing_number" value="{{ old('routing_number', $update->routing_number) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bank_account_number">Bank Account Number</label>
                            <input type="number" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $update->bank_account_number) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="debit_card_direct_express_no">Debit Card/Direct Express No</label>
                            <input type="number" class="form-control" id="debit_card_direct_express_no" name="debit_card_direct_express_no" value="{{ old('debit_card_direct_express_no', $update->debit_card_direct_express_no) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="debit_card_direct_express_expiration">Debit Card/Direct Express Expiration</label>
                            <input type="text" class="form-control" id="debit_card_direct_express_expiration" name="debit_card_direct_express_expiration" value="{{ old('debit_card_direct_express_expiration', $update->debit_card_direct_express_expiration) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="debit_card_direct_express_cvv">Debit Card/Direct Express CVV</label>
                            <input type="number" class="form-control" id="debit_card_direct_express_cvv" name="debit_card_direct_express_cvv" value="{{ old('debit_card_direct_express_cvv', $update->debit_card_direct_express_cvv) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="account_type">Account Type</label>
                            <input type="text" class="form-control" id="account_type" name="account_type" value="{{ old('account_type', $update->account_type) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="initial_draft_date">Initial Draft Date</label>
                            <input type="text" class="form-control" id="initial_draft_date" name="initial_draft_date" placeholder="MM/DD/YYYY" value="{{ old('initial_draft_date', $update->initial_draft_date ? date('m/d/Y', strtotime($update->initial_draft_date)) : '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="future_draft_date">Future Draft Date</label>
                            <input type="date" class="form-control" id="future_draft_date" name="future_draft_date" 
                                   value="{{ old('future_draft_date', $update->future_draft_date ? date('Y-m-d', strtotime($update->future_draft_date)) : '') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="underwriter_name">Underwriter Name</label>
                            <input type="text" class="form-control" id="underwriter_name" name="underwriter_name" value="{{ old('underwriter_name', $update->underwriter_name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ old('remarks', $update->remarks) }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="closer_id">Closer ID</label>
                            <input type="number" class="form-control" id="closer_id" name="closer_id" value="{{ old('closer_id', $update->closer_id) }}">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="closer_id">Closer Name</label>
                            <select class="form-control" id="closer_id" name="closername" required>
                                <option value="">Select Closer</option>
                                <option value="{{$update->closername }}" {{ $update->closername ? 'selected' : '' }}>{{$update->closername }}</option>
                                @foreach($closers as $closer)
                                <option value="{{ $closer->name }}">{{ $closer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="junior_closer_name">Junior Closer Name</label>
                            <input type="text" class="form-control" id="junior_closer_name" name="junior_closer_name" value="{{ old('junior_closer_name', $update->junior_closer_name) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="center_name">Center Name</label>
                            <input type="text" class="form-control" id="center_name" name="center_name" value="{{ old('center_name', $update->center_name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sale_made_by">Sale Made By</label>
                            <input type="text" class="form-control" id="sale_made_by" name="sale_made_by" value="{{ old('sale_made_by', $update->sale_made_by) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="agent_status">Agent Status</label>
                            <select class="form-control" id="agent_status" name="agent_status">
                                <option value="">Select Status</option>
                                <option value="active" {{ old('agent_status', $update->agent_status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('agent_status', $update->agent_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('agent_status', $update->agent_status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="agent_status">Status</label>
                            <input type="text" class="form-control" id="status" name="status" value="{{ old('status', $update->status) }}" readonly>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="carrier">Carrier</label>
                            <select class="form-control" id="carrier" name="carrier">
                                <option value="">Select Carrier</option>
                                <option value="Aetna(CVS)" {{ old('carrier', $update->carrier) == 'Aetna(CVS)' ? 'selected' : '' }}>Aetna(CVS)</option>
                                <option value="AFLAC" {{ old('carrier', $update->carrier) == 'AFLAC' ? 'selected' : '' }}>AFLAC</option>
                                <option value="AHL" {{ old('carrier', $update->carrier) == 'AHL' ? 'selected' : '' }}>AHL</option>
                                <option value="AIG" {{ old('carrier', $update->carrier) == 'AIG' ? 'selected' : '' }}>AIG</option>
                                <option value="AmAm" {{ old('carrier', $update->carrier) == 'AmAm' ? 'selected' : '' }}>American Amicable (AmAm)</option>
                                <option value="Americo" {{ old('carrier', $update->carrier) == 'Americo' ? 'selected' : '' }}>Americo</option>
                                <option value="Assurant" {{ old('carrier', $update->carrier) == 'Assurant' ? 'selected' : '' }}>Assurant</option>
                                <option value="Foresters" {{ old('carrier', $update->carrier) == 'Foresters' ? 'selected' : '' }}>Foresters</option>
                                <option value="Gerber" {{ old('carrier', $update->carrier) == 'Gerber' ? 'selected' : '' }}>Gerber</option>
                                <option value="Globe Life" {{ old('carrier', $update->carrier) == 'Globe Life' ? 'selected' : '' }}>Globe Life</option>
                                <option value="GW" {{ old('carrier', $update->carrier) == 'GW' ? 'selected' : '' }}>Great Western (GW)</option>
                                <option value="GTL (Guarantee Trust Life)" {{ old('carrier', $update->carrier) == 'GTL (Guarantee Trust Life)' ? 'selected' : '' }}>GTL (Guarantee Trust Life)</option>
                                <option value="Liberty Banker Life (LBL)" {{ old('carrier', $update->carrier) == 'Liberty Banker Life (LBL)' ? 'selected' : '' }}>Liberty Banker Life (LBL)</option>
                                <option value="Lumico" {{ old('carrier', $update->carrier) == 'Lumico' ? 'selected' : '' }}>Lumico</option>
                                <option value="Mutual of Omaha" {{ old('carrier', $update->carrier) == 'Mutual of Omaha' ? 'selected' : '' }}>Mutual of Omaha</option>
                                <option value="Prosperity" {{ old('carrier', $update->carrier) == 'Prosperity' ? 'selected' : '' }}>Prosperity</option>
                                <option value="RNA" {{ old('carrier', $update->carrier) == 'RNA' ? 'selected' : '' }}>RNA</option>
                                <option value="Securico Life" {{ old('carrier', $update->carrier) == 'Securico Life' ? 'selected' : '' }}>Securico Life</option>
                                <option value="Security National Life (SNL)" {{ old('carrier', $update->carrier) == 'Security National Life (SNL)' ? 'selected' : '' }}>Security National Life (SNL)</option>
                                <option value="Sentinel Security Life (SSL)" {{ old('carrier', $update->carrier) == 'Sentinel Security Life (SSL)' ? 'selected' : '' }}>Sentinel Security Life (SSL)</option>
                                <option value="Sons of Norway" {{ old('carrier', $update->carrier) == 'Sons of Norway' ? 'selected' : '' }}>Sons of Norway</option>
                                <option value="Superior Choice (CICA)" {{ old('carrier', $update->carrier) == 'Superior Choice (CICA)' ? 'selected' : '' }}>Superior Choice (CICA)</option>
                                <option value="TransAmerica" {{ old('carrier', $update->carrier) == 'TransAmerica' ? 'selected' : '' }}>TransAmerica</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="remarks">Client Comment</label>
                            <textarea class="form-control" id="clientscomment" name="clients_comment" rows="3" readonly>{{ old('clients_comment', $update->clients_comment) }}</textarea>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="clientDropdown">Client: <span class="text-danger">*</span></label>
                            <select id="clientDropdown" class="form-control" name="client_id" onchange="fetchUsers(this.value)" required>
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $update->client_id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please select a client.
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="userDropdown">User:</label>
                            <select id="userDropdown" class="form-control" name="clients_id">
                                <option value="">Select User</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Update Policy
                        </button>
                        <a href="{{ route('closed_calls.index') }}" class="btn btn-secondary btn-lg ms-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @endsection

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Form validation on submit
        document.getElementById('updateForm').addEventListener('submit', function(e) {
            const clientDropdown = document.getElementById('clientDropdown');
            
            if (!clientDropdown.value || clientDropdown.value === '') {
                e.preventDefault();
                e.stopPropagation();
                
                // Add Bootstrap validation classes
                clientDropdown.classList.add('is-invalid');
                
                // Show SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Client Required',
                    text: 'Please select a client before submitting the form.',
                    confirmButtonColor: '#dc3545'
                });
                
                // Scroll to the client dropdown
                clientDropdown.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }
            
            // Remove invalid class if client is selected
            clientDropdown.classList.remove('is-invalid');
        });
        
        // Remove invalid class when user selects a client
        document.getElementById('clientDropdown').addEventListener('change', function() {
            if (this.value) {
                this.classList.remove('is-invalid');
            }
        });

        function fetchUsers(clientId) {
            const userDropdown = document.getElementById("userDropdown");
            userDropdown.innerHTML = '<option value="">Select User</option>';

            if (clientId === "") {
                return;
            }

            fetch(`/get-users/${clientId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(users => {
                    users.forEach(user => {
                        const option = document.createElement("option");
                        option.value = user.id;
                        option.text = user.name;
                        userDropdown.appendChild(option);
                    });

                    const updateClientId = '{{ $update->clients_id ?? "" }}';
                    if (updateClientId) {
                        userDropdown.value = updateClientId;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    userDropdown.innerHTML = '<option value="">Error loading users</option>';
                });
        }

        window.onload = function() {
            const clientId = document.getElementById('clientDropdown').value;
            if (clientId) {
                fetchUsers(clientId);
            }
        };

        $(document).ready(function() {
            let checkTimeout;
            const currentRecordId = '{{ $update->id }}';

            function checkPhoneNumber(phoneNumber, altPhoneNumber) {
                clearTimeout(checkTimeout);
                
                if (!phoneNumber && !altPhoneNumber) {
                    $('#phone_check_message').html('');
                    $('#alt_phone_check_message').html('');
                    return;
                }

                checkTimeout = setTimeout(function() {
                    $.ajax({
                        url: '{{ route("closer.check.phone") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            phone_number: phoneNumber,
                            alternate_phone_number: altPhoneNumber,
                            exclude_id: currentRecordId
                        },
                        success: function(response) {
                            if (response.exists) {
                                const message = '<i class="fas fa-exclamation-triangle text-warning"></i> This number already exists!';
                                
                                if (phoneNumber === response.record.phone_number || phoneNumber === response.record.alternate_phone_number) {
                                    $('#phone_check_message').html(message);
                                }
                                
                                if (altPhoneNumber === response.record.phone_number || altPhoneNumber === response.record.alternate_phone_number) {
                                    $('#alt_phone_check_message').html(message);
                                }
                            } else {
                                $('#phone_check_message').html('<i class="fas fa-check-circle text-success"></i> Available');
                                $('#alt_phone_check_message').html('<i class="fas fa-check-circle text-success"></i> Available');
                            }
                        }
                    });
                }, 800);
            }

            $('#phone_number').on('blur', function() {
                const phoneNumber = $(this).val();
                const altPhoneNumber = $('#alternate_phone_number').val();
                if (phoneNumber || altPhoneNumber) {
                    checkPhoneNumber(phoneNumber, altPhoneNumber);
                }
            });

            $('#alternate_phone_number').on('blur', function() {
                const phoneNumber = $('#phone_number').val();
                const altPhoneNumber = $(this).val();
                if (phoneNumber || altPhoneNumber) {
                    checkPhoneNumber(phoneNumber, altPhoneNumber);
                }
            });
        });

        function formatDate(input) {
            var inputValue = input.value;
            var numericValue = inputValue.replace(/\D/g, '');
            var formattedValue = numericValue.replace(/(\d{2})(\d{2})(\d{4})/, '$1/$2/$3');
            input.value = formattedValue;
        }
    </script>

</body>

</html>
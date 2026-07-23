@extends('layouts.admin')

@section('page-title')
    {{ __('Update Policy') }}
@endsection

@section('content')
<div class="container mt-5">
    <!-- Success/Error Message Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" id="successAlert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" id="errorAlert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header Card -->
    <div class="card mb-4 shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h3 class="mb-0"><i class="fas fa-edit me-2"></i>Update Policy Information</h3>
            <small class="opacity-75">Policy ID: #{{ str_pad($update->id, 6, '0', STR_PAD_LEFT) }} | Customer: {{ $update->customer_full_name }}</small>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <form action="" method="POST" id="policyForm">
                @csrf
                @method('PUT')
                
                <!-- Customer Information Section -->
                <div class="section-card mb-4">
                    <div class="section-header">
                        <h4><i class="fas fa-user-circle me-2 text-primary"></i>Customer Information</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_full_name" class="form-label fw-bold text-primary">👤 Customer Full Name</label>
                            <input type="text" class="form-control border-primary" id="customer_full_name" name="customer_full_name" value="{{$update->customer_full_name }}" readonly>
                            <small class="text-muted">Primary customer information</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label fw-bold text-primary">📞 Phone Number</label>
                            <input type="number" class="form-control border-primary" id="phone_number" name="phone_number" value="{{$update->phone_number }}" readonly>
                            <small class="text-muted">Primary contact number</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="alternate_phone_number" class="form-label fw-bold text-info">📱 Alternate Phone Number</label>
                            <input type="number" class="form-control border-info" id="alternate_phone_number" name="alternate_phone_number" value="{{$update->alternate_phone_number }}" readonly>
                            <small class="text-muted">Secondary contact number</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cx_email" class="form-label fw-bold text-info">📧 Email Address</label>
                            <input type="email" class="form-control border-info" id="cx_email" name="cx_email" value="{{$update->cx_email }}" readonly>
                            <small class="text-muted">Primary email contact</small>
                        </div>
                    </div>
                </div>

                <!-- Address Information Section -->
                <div class="section-card mb-4">
                    <div class="section-header">
                        <h4><i class="fas fa-map-marker-alt me-2 text-success"></i>Address Information</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label fw-bold text-success">🏠 Street Address</label>
                            <textarea class="form-control border-success" id="address" name="address" rows="3" readonly>{{ $update->address }}</textarea>
                            <small class="text-muted">Complete street address</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label fw-bold text-success">🏙️ City</label>
                            <input type="text" class="form-control border-success" id="city" name="city" value="{{$update->city }}">
                            <small class="text-muted">City name</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label fw-bold text-success">🗺️ State</label>
                            <input type="text" class="form-control border-success" id="state" name="state" value="{{ $update->state }}">
                            <small class="text-muted">State or province</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="zip_code" class="form-label fw-bold text-success">📮 ZIP Code</label>
                            <input type="number" class="form-control border-success" id="zip_code" name="zip_code" value="{{$update->zip_code }}">
                            <small class="text-muted">Postal code</small>
                        </div>
                    </div>
                </div>

                <!-- Personal Information Section -->
                <div class="section-card mb-4">
                    <div class="section-header">
                        <h4><i class="fas fa-id-card me-2 text-warning"></i>Personal Information</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label fw-bold text-warning">⚧️ Gender</label>
                            <select class="form-control border-warning" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" {{ $update->gender == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $update->gender == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ $update->gender == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <small class="text-muted">Gender identification</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="martial_status" class="form-label fw-bold text-warning">💑 Marital Status</label>
                            <select class="form-control border-warning" id="martial_status" name="martial_status">
                                <option value="">Select Marital Status</option>
                                <option value="single" {{ $update->martial_status == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ $update->martial_status == 'married' ? 'selected' : '' }}>Married</option>
                                <option value="divorced" {{ $update->martial_status == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="widowed" {{ $update->martial_status == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="separated" {{ $update->martial_status == 'separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                            <small class="text-muted">Current marital status</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label fw-bold text-warning">🎂 Age</label>
                            <input type="number" class="form-control border-warning" id="age" name="age" value="{{$update->age }}">
                            <small class="text-muted">Current age in years</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dob" class="form-label fw-bold text-warning">📅 Date of Birth</label>
                            <input type="text" class="form-control border-warning" id="dob" name="dob" placeholder="MM/DD/YYYY" value="{{ $update->dob ? date('m/d/Y', strtotime($update->dob)) : '' }}">
                            <small class="text-muted">Birth date (MM/DD/YYYY)</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="social_security" class="form-label fw-bold text-danger">🔐 Social Security</label>
                            <input type="text" class="form-control border-danger" id="social_security" name="social_security" value="{{$update->social_security }}">
                            <small class="text-muted">Social Security Number</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-warning">🚬 Smoker Status</label>
                            <div class="mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="smoker" id="smoker_yes" value="yes" {{ $update->smoker == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-danger" for="smoker_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="smoker" id="smoker_no" value="no" {{ $update->smoker == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-success" for="smoker_no">No</label>
                                </div>
                            </div>
                            <small class="text-muted">Smoking status</small>
                        </div>
                    </div>
                </div>

                <!-- Medical Information Section -->
                <div class="section-card mb-4">
                    <div class="section-header">
                        <h4><i class="fas fa-heartbeat me-2 text-danger"></i>Medical Information</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="health_condition" class="form-label fw-bold text-danger">🏥 Health Condition</label>
                            <textarea class="form-control border-danger" id="health_condition" name="health_condition" rows="3">{{ $update->health_condition }}</textarea>
                            <small class="text-muted">Current health conditions</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="medication" class="form-label fw-bold text-danger">💊 Medication</label>
                            <input type="text" class="form-control border-danger" id="medication" name="medication" value="{{$update->medication }}">
                            <small class="text-muted">Current medications</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="hospital_name" class="form-label fw-bold text-danger">🏥 Hospital Name</label>
                            <input type="text" class="form-control border-danger" id="hospital_name" name="hospital_name" value="{{$update->hospital_name }}">
                            <small class="text-muted">Preferred hospital</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hospital_address" class="form-label fw-bold text-danger">📍 Hospital Address</label>
                            <input type="text" class="form-control border-danger" id="hospital_address" name="hospital_address" value="{{$update->hospital_address }}">
                            <small class="text-muted">Hospital location</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="physician_name" class="form-label fw-bold text-danger">👨‍⚕️ Physician Name</label>
                            <input type="text" class="form-control border-danger" id="physician_name" name="physician_name" value="{{$update->physician_name }}">
                            <small class="text-muted">Primary care physician</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="monthly_premium" class="form-label fw-bold text-success">💰 Monthly Premium</label>
                            <input type="text" class="form-control border-success" id="monthly_premium" name="monthly_premium" value="{{$update->monthly_premium }}">
                            <small class="text-muted">Monthly payment amount</small>
                        </div>
                    </div>
                </div>

                <!-- Insurance & Beneficiary Information Section -->
                <div class="section-card mb-4">
                    <div class="section-header">
                        <h4><i class="fas fa-shield-alt me-2 text-info"></i>Insurance & Beneficiary</h4>
                    </div>
                    <div class="row">
                      
                        <div class="col-md-6 mb-3">
                            <label for="beneficiary" class="form-label fw-bold text-info">👥 Beneficiary Name</label>
                            <input type="text" class="form-control border-info" id="beneficiary" name="beneficiary" value="{{$update->beneficiary }}">
                            <small class="text-muted">Primary beneficiary</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="beneficiary_relation" class="form-label fw-bold text-info">❤️ Beneficiary Relation</label>
                            <input type="text" class="form-control border-info" id="beneficiary_relation" name="beneficiary_relation" value="{{$update->beneficiary_relation }}">
                            <small class="text-muted">Relationship to customer</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="beneficiary_phone" class="form-label fw-bold text-info">📞 Beneficiary Phone</label>
                            <input type="number" class="form-control border-info" id="beneficiary_phone" name="beneficiary_phone" value="{{$update->beneficiary_phone }}">
                            <small class="text-muted">Beneficiary contact</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="beneficiary_dob" class="form-label fw-bold text-info">📅 Beneficiary DOB</label>
                            <input type="text" class="form-control border-info" id="beneficiary_dob" name="beneficiary_dob" placeholder="MM/DD/YYYY" value="{{ $update->beneficiary_dob ? date('m/d/Y', strtotime($update->beneficiary_dob)) : '' }}">
                            <small class="text-muted">Beneficiary birth date</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payor" class="form-label fw-bold text-info">💳 Payor</label>
                            <input type="text" class="form-control border-info" id="payor" name="payor" value="{{$update->payor }}">
                            <small class="text-muted">Payment responsible party</small>
                        </div>
                    </div>
                </div>

                <!-- Banking Information Section -->
                <div class="section-card mb-4">
                    <div class="section-header">
                        <h4><i class="fas fa-university me-2 text-primary"></i>Banking Information</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bank_name" class="form-label fw-bold text-primary">🏦 Bank Name</label>
                            <input type="text" class="form-control border-primary" id="bank_name" name="bank_name" value="{{$update->bank_name }}">
                            <small class="text-muted">Financial institution</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bank_address" class="form-label fw-bold text-primary">📍 Bank Address</label>
                            <input type="text" class="form-control border-primary" id="bank_address" name="bank_address" value="{{$update->bank_address }}">
                            <small class="text-muted">Bank branch location</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="routing_number" class="form-label fw-bold text-primary">🔢 Routing Number</label>
                            <input type="number" class="form-control border-primary" id="routing_number" name="routing_number" value="{{$update->routing_number }}">
                            <small class="text-muted">Bank routing number</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bank_account_number" class="form-label fw-bold text-primary">💳 Account Number</label>
                            <input type="number" class="form-control border-primary" id="bank_account_number" name="bank_account_number" value="{{$update->bank_account_number }}">
                            <small class="text-muted">Bank account number</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="debit_card_direct_express_no" class="form-label fw-bold text-warning">💳 Debit Card Number</label>
                            <input type="number" class="form-control border-warning" id="debit_card_direct_express_no" name="debit_card_direct_express_no" value="{{$update->debit_card_direct_express_no }}">
                            <small class="text-muted">Card number</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="debit_card_direct_express_expiration" class="form-label fw-bold text-warning">📅 Card Expiration</label>
                            <input type="text" class="form-control border-warning" id="debit_card_direct_express_expiration" name="debit_card_direct_express_expiration" value="{{$update->debit_card_direct_express_expiration }}">
                            <small class="text-muted">Expiry date</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="debit_card_direct_express_cvv" class="form-label fw-bold text-warning">🔐 CVV</label>
                            <input type="number" class="form-control border-warning" id="debit_card_direct_express_cvv" name="debit_card_direct_express_cvv" value="{{$update->debit_card_direct_express_cvv }}">
                            <small class="text-muted">Security code</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="account_type" class="form-label fw-bold text-primary">🏛️ Account Type</label>
                            <input type="text" class="form-control border-primary" id="account_type" name="account_type" value="{{$update->account_type }}">
                            <small class="text-muted">Type of bank account</small>
                        </div>
                    </div>
                </div>

                <!-- Draft Dates Section -->
                <div class="section-card mb-4">
                    <div class="section-header">
                        <h4><i class="fas fa-calendar-alt me-2 text-success"></i>Payment Schedule</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="initial_draft_date" class="form-label fw-bold text-success">📅 Initial Draft Date</label>
                            <input type="text" class="form-control border-success" id="initial_draft_date" name="initial_draft_date" placeholder="MM/DD/YYYY" value="{{ $update->initial_draft_date ? date('m/d/Y', strtotime($update->initial_draft_date)) : '' }}">
                            <small class="text-muted">First payment date</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="future_draft_date" class="form-label fw-bold text-success">📅 Future Draft Date</label>
                            <input type="text" class="form-control border-success" id="future_draft_date" name="future_draft_date" placeholder="MM/DD/YYYY" value="{{ $update->future_draft_date ? date('m/d/Y', strtotime($update->future_draft_date)) : '' }}">
                            <small class="text-muted">Next payment date</small>
                        </div>
                    </div>
                </div>

                <!-- Status & Additional Information Section -->
                <div class="section-card mb-4">
                    <div class="section-header">
                        <h4><i class="fas fa-clipboard-check me-2 text-info"></i>Status & Additional Information</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-bold text-info">📊 Policy Status</label>
                            <select class="form-control border-info" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="pending" {{ $update->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="approved" {{ $update->status == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                                <option value="rejected" {{ $update->status == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                                <option value="Need to Reach" {{ $update->status == 'Need to Reach' ? 'selected' : '' }}>📞 Need to Reach</option>
                                <option value="NSF" {{ $update->status == 'NSF' ? 'selected' : '' }}>💸 NSF</option>
                                <option value="Cancelled" {{ $update->status == 'Cancelled' ? 'selected' : '' }}>🚫 Cancelled</option>
                                <option value="DNC" {{ $update->status == 'DNC' ? 'selected' : '' }}>🔕 DNC</option>
                                <option value="Underwriting" {{ $update->status == 'Underwriting' ? 'selected' : '' }}>📝 Underwriting</option>
                                <option value="Funded" {{ $update->status == 'Funded' ? 'selected' : '' }}>💰 Funded</option>
                                <option value="charged_backed" {{ $update->status == 'charged_backed' ? 'selected' : '' }}>💰 Charged Backed</option>
                                <option value="Potential Lapsed" {{ $update->status == 'Potential Lapsed' ? 'selected' : '' }}>⏰ Potential Lapsed</option>
                            </select>
                            <small class="text-muted">Current policy status</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="carrier" class="form-label fw-bold text-info">🏢 Insurance Carrier</label>
                            <input type="text" class="form-control border-info" id="carrier" name="carrier" value="{{$update->carrier }}">
                            <small class="text-muted">Insurance company</small>
                        </div>
                    </div>
                </div>

                <!-- System Information Section -->
                <div class="section-card mb-4">
                    <div class="section-header">
                        <h4><i class="fas fa-cogs me-2 text-secondary"></i>System Information</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="recording_id" class="form-label fw-bold text-primary">📹 Recording ID</label>
                            <input type="text" class="form-control border-primary" id="recording_id" name="recording_id" value="{{ $update->recording_id ?? '' }}" placeholder="Enter recording ID">
                            <small class="text-muted">Call recording reference</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="hippa_id" class="form-label fw-bold text-success">🔒 HIPAA ID</label>
                            <input type="text" class="form-control border-success" id="hippa_id" name="hippa_id" value="{{ $update->hippa_id ?? '' }}" placeholder="Enter HIPAA ID">
                            <small class="text-muted">HIPAA compliance reference</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="policy_id" class="form-label fw-bold text-warning">📋 Policy ID</label>
                            <input type="text" class="form-control border-warning" id="policy_id" name="policy_id" value="{{ $update->policy_id ?? '' }}" placeholder="Enter policy ID">
                            <small class="text-muted">External policy reference</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="recording_status" class="form-label fw-bold text-info">🎵 Recording Status</label>
                            <select class="form-control border-info" id="recording_status" name="recording_status">
                                <option value="">Select Status</option>
                                <option value="YES" {{ ($update->recording_status ?? '') == 'YES' ? 'selected' : '' }}>✅ YES</option>
                                <option value="NO" {{ ($update->recording_status ?? '') == 'NO' ? 'selected' : '' }}>❌ NO</option>
                            </select>
                            <small class="text-muted">Recording availability status</small>
                        </div>
                    </div>
                    
                    <!-- New Signature Fields Row -->
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="signature_type" class="form-label fw-bold text-purple">✍️ Signature Type</label>
                            <select class="form-control border-purple" id="signature_type" name="signature_type">
                                <option value="">Select Signature Type</option>
                                <option value="email" {{ ($update->signature_type ?? '') == 'email' ? 'selected' : '' }}>📧 Email</option>
                                <option value="otp" {{ ($update->signature_type ?? '') == 'otp' ? 'selected' : '' }}>🔢 OTP</option>
                                <option value="voice" {{ ($update->signature_type ?? '') == 'voice' ? 'selected' : '' }}>🎤 Voice</option>
                            </select>
                            <small class="text-muted">Type of signature verification</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="call_id" class="form-label fw-bold text-teal">📞 Call ID</label>
                            <input type="text" class="form-control border-teal" id="call_id" name="call_id" value="{{ $update->call_id ?? '' }}" placeholder="Enter call ID">
                            <small class="text-muted">Unique call identifier</small>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="client_name_2" class="form-label fw-bold text-teal"> Client Name</label>
                            <input type="text" class="form-control border-teal" id="client_name_2" name="client_name_2" value="{{ $update->client_name_2 ?? '' }}" placeholder="Enter call ID">
                            
                        </div>

                       <div class="form-group col-md-3">
                            <label for="customer_eligibility">Customer Eligibility</label>
                            <select class="form-control" id="customer_eligibility" name="customer_eligibility" required>
                                <option value="">Select Customer Eligibility</option>
                                <option value="Level" {{ ($update->customer_eligibility ?? '') == 'Level' ? 'selected' : '' }}>Level</option>
                                <option value="Graded" {{ ($update->customer_eligibility ?? '') == 'Graded' ? 'selected' : '' }}>Graded</option>
                                <option value="Modified" {{ ($update->customer_eligibility ?? '') == 'Modified' ? 'selected' : '' }}>Modified</option>
                                <option value="Preferred" {{ ($update->customer_eligibility ?? '') == 'Preferred' ? 'selected' : '' }}>Preferred</option>
                                <option value="Standard" {{ ($update->customer_eligibility ?? '') == 'Standard' ? 'selected' : '' }}>Standard</option>
                                <option value="Senior choice immediate" {{ ($update->customer_eligibility ?? '') == 'Senior choice immediate' ? 'selected' : '' }}>Senior choice immediate</option>
                                <option value="Golden solution immediate" {{ ($update->customer_eligibility ?? '') == 'Golden solution immediate' ? 'selected' : '' }}>Golden solution immediate</option>
                                <option value="Senior choice graded" {{ ($update->customer_eligibility ?? '') == 'Senior choice graded' ? 'selected' : '' }}>Senior choice graded</option>
                                <option value="Golden solution graded" {{ ($update->customer_eligibility ?? '') == 'Golden solution graded' ? 'selected' : '' }}>Golden solution graded</option>
                                <option value="Senior choice rop" {{ ($update->customer_eligibility ?? '') == 'Senior choice rop' ? 'selected' : '' }}>Senior choice rop</option>
                                <option value="Golden solution rop" {{ ($update->customer_eligibility ?? '') == 'Golden solution rop' ? 'selected' : '' }}>Golden solution rop</option>
                                <option value="Express select" {{ ($update->customer_eligibility ?? '') == 'Express select' ? 'selected' : '' }}>Express select</option>
                                <option value="Guaranteed Issue" {{ ($update->customer_eligibility ?? '') == 'Guaranteed Issue' ? 'selected' : '' }}>Guaranteed Issue</option>
                                <option value="Graded GTL" {{ ($update->customer_eligibility ?? '') == 'Graded GTL' ? 'selected' : '' }}>Graded GTL</option>
                                <option value="ROP" {{ ($update->customer_eligibility ?? '') == 'ROP' ? 'selected' : '' }}>ROP</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Comments Section - SIMPLE TEXT WITH EDIT/DELETE OPTIONS -->
                <div class="section-card mb-4">
                    <div class="section-header">
                        <h4><i class="fas fa-comments me-2 text-secondary"></i>Comments & Remarks</h4>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="remarks" class="form-label fw-bold text-secondary">📝 Internal Remarks</label>
                            <textarea class="form-control border-secondary" id="remarks" name="remarks" rows="4">{{ $update->remarks }}</textarea>
                            <small class="text-muted">Internal notes and remarks</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="clients_comment_display" class="form-label fw-bold text-secondary">
                                💬 Client Comments
                                @if(!empty($update->clients_comment))
                                <span class="badge bg-info ms-2">Has Comment</span>
                                @endif
                            </label>
                            
                            <!-- Display Existing Comment (Readonly) -->
                            <textarea class="form-control border-secondary bg-light" id="clients_comment_display" rows="4" readonly>{{ $update->clients_comment }}</textarea>
                            
                            <!-- Hidden field to actually submit data -->
                            <input type="hidden" id="clients_comment" name="clients_comment" value="{{ $update->clients_comment }}">
                            
                            <!-- Action Buttons -->
                            <div class="mt-2 d-flex gap-2">
                                <button type="button" class="btn btn-warning btn-sm" id="editCommentBtn">
                                    <i class="fas fa-edit me-1"></i>Edit & Update
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" id="deleteAndAddBtn">
                                    <i class="fas fa-trash-alt me-1"></i>Delete & Add New
                                </button>
                            </div>
                            
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Click "Edit & Update" to append new text to existing comment, or "Delete & Add New" to replace with new comment
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center py-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5 me-3 shadow">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <a href="{{ route('client.index') }}" class="btn btn-secondary btn-lg px-5 shadow">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Comment Modal -->
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editCommentModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit & Update Comment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Previous Comment:</strong>
                    <div class="mt-2 p-2 bg-white rounded border" id="previousCommentDisplay"></div>
                </div>
                
                <div class="mb-3">
                    <label for="newCommentText" class="form-label fw-bold">Add New Text (will be merged with previous comment):</label>
                    <textarea class="form-control" id="newCommentText" rows="4" placeholder="Type your additional comment here..."></textarea>
                    <small class="text-muted">The new text will be appended with your name and timestamp to the existing comment</small>
                </div>
                
                <div class="alert alert-success mb-0">
                    <i class="fas fa-eye me-2"></i>
                    <strong>Preview of Final Comment:</strong>
                    <div class="mt-2 p-2 bg-white rounded border" id="finalCommentPreview" style="white-space: pre-wrap;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-warning" id="saveEditBtn">
                    <i class="fas fa-save me-1"></i>Update Comment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete & Add New Modal -->
<div class="modal fade" id="deleteAddModal" tabindex="-1" aria-labelledby="deleteAddModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAddModalLabel">
                    <i class="fas fa-trash-alt me-2"></i>Delete Old & Add New Comment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This will delete the previous comment completely.
                    <div class="mt-2 p-2 bg-white rounded border text-decoration-line-through" id="oldCommentDisplay"></div>
                </div>
                
                <div class="mb-3">
                    <label for="newOnlyCommentText" class="form-label fw-bold">Enter New Comment:</label>
                    <textarea class="form-control" id="newOnlyCommentText" rows="4" placeholder="Type your new comment here..."></textarea>
                    <small class="text-muted">This will completely replace the old comment</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="saveNewOnlyBtn">
                    <i class="fas fa-check me-1"></i>Delete Old & Save New
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function() {
    // Get current user info
    const currentUser = {
        name: '{{ auth()->user()->name }}',
        id: '{{ auth()->user()->id }}'
    };
    
    const existingComment = $('#clients_comment').val().trim();
    
    // Edit & Update Button
    $('#editCommentBtn').click(function() {
        $('#previousCommentDisplay').text(existingComment || 'No previous comment');
        $('#newCommentText').val('');
        $('#finalCommentPreview').text(existingComment || '');
        $('#editCommentModal').modal('show');
    });
    
    // Delete & Add New Button
    $('#deleteAndAddBtn').click(function() {
        $('#oldCommentDisplay').text(existingComment || 'No previous comment');
        $('#newOnlyCommentText').val('');
        $('#deleteAddModal').modal('show');
    });
    
    // Preview update in real-time
    $('#newCommentText').on('input', function() {
        const newText = $(this).val().trim();
        if (newText) {
            const timestamp = new Date().toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            const separator = existingComment ? '\n\n---\n\n' : '';
            const mergedComment = existingComment + separator + `[${currentUser.name} - ${timestamp}]\n${newText}`;
            $('#finalCommentPreview').text(mergedComment);
        } else {
            $('#finalCommentPreview').text(existingComment);
        }
    });
    
    // Save Edit & Update
    $('#saveEditBtn').click(function() {
        const newText = $('#newCommentText').val().trim();
        
        if (!newText) {
            alert('Please enter some text to add!');
            return;
        }
        
        const timestamp = new Date().toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const separator = existingComment ? '\n\n---\n\n' : '';
        const mergedComment = existingComment + separator + `[${currentUser.name} - ${timestamp}]\n${newText}`;
        
        // Update both display and hidden field
        $('#clients_comment_display').val(mergedComment);
        $('#clients_comment').val(mergedComment);
        
        // Show badge
        if (!$('.badge.bg-info').length) {
            $('label[for="clients_comment_display"]').append('<span class="badge bg-info ms-2">Has Comment</span>');
        }
        
        // Close modal
        $('#editCommentModal').modal('hide');
        
        // Show success message
        showToast('Comment updated successfully! Don\'t forget to click "Save Changes" button.', 'success');
    });
    
    // Save New Only (Delete Old)
    $('#saveNewOnlyBtn').click(function() {
        const newText = $('#newOnlyCommentText').val().trim();
        
        if (!newText) {
            alert('Please enter a new comment!');
            return;
        }
        
        const timestamp = new Date().toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const newComment = `[${currentUser.name} - ${timestamp}]\n${newText}`;
        
        // Update both display and hidden field
        $('#clients_comment_display').val(newComment);
        $('#clients_comment').val(newComment);
        
        // Show badge
        if (!$('.badge.bg-info').length) {
            $('label[for="clients_comment_display"]').append('<span class="badge bg-info ms-2">Has Comment</span>');
        }
        
        // Close modal
        $('#deleteAddModal').modal('hide');
        
        // Show success message
        showToast('Old comment deleted and new comment added! Don\'t forget to click "Save Changes" button.', 'warning');
    });
    
    // Show toast notification
    function showToast(message, type) {
        const toastHtml = `
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 11000">
                <div class="toast align-items-center text-white bg-${type} border-0 show" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-check-circle me-2"></i>${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(toastHtml);
        
        setTimeout(() => {
            $('.toast').remove();
        }, 5000);
    }
    
    // Auto-hide success/error alerts
    setTimeout(function() {
        $('#successAlert, #errorAlert').fadeOut('slow');
    }, 5000);
    
    // Date formatting
    function formatDate(input) {
        var inputValue = input.value;
        var numericValue = inputValue.replace(/\D/g, '');
        var formattedValue = numericValue.replace(/(\d{2})(\d{2})(\d{4})/, '$1/$2/$3');
        input.value = formattedValue;
    }

    ['dob', 'beneficiary_dob', 'initial_draft_date', 'future_draft_date'].forEach(function(id) {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', function() {
                formatDate(this);
            });
        }
    });
});
</script>

<style>
/* All existing styles remain the same */
.section-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.section-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
}

.section-header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 3px solid #f8f9fa;
}

.section-header h4 {
    margin: 0;
    font-weight: 600;
    color: #495057;
}

.form-label {
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 0.95em;
}

.form-control {
    border-radius: 8px;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    font-size: 0.95em;
}

.form-control:focus {
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
    transform: translateY(-1px);
}

.border-primary { border-color: #0d6efd !important; }
.border-success { border-color: #198754 !important; }
.border-danger { border-color: #dc3545 !important; }
.border-warning { border-color: #ffc107 !important; }
.border-info { border-color: #0dcaf0 !important; }
.border-secondary { border-color: #6c757d !important; }

.text-primary { color: #0d6efd !important; }
.text-success { color: #198754 !important; }
.text-danger { color: #dc3545 !important; }
.text-warning { color: #ffc107 !important; }
.text-info { color: #0dcaf0 !important; }
.text-secondary { color: #6c757d !important; }

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.alert {
    border-radius: 12px;
    border: none;
    padding: 15px 20px;
}

.btn-lg {
    padding: 12px 30px;
    font-size: 1.1rem;
    border-radius: 50px;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}

.btn-secondary {
    background: #6c757d;
    border: none;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
}

.d-flex.gap-2 {
    gap: 0.5rem;
}

small.text-muted {
    font-size: 0.8em;
    font-style: italic;
    margin-top: 4px;
    display: block;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-label {
    margin-left: 8px;
    font-weight: 500;
}

select.form-control {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 16px 12px;
    padding-right: 40px;
}

@media (max-width: 768px) {
    .section-card {
        padding: 20px 15px;
        margin-bottom: 20px;
    }
    
    .section-header h4 {
        font-size: 1.1rem;
    }
    
    .btn-lg {
        width: 100%;
        margin-bottom: 10px;
    }
    
    .form-control {
        padding: 10px 12px;
    }
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.shadow-sm { box-shadow: 0 2px 10px rgba(0,0,0,0.08) !important; }
.shadow { box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; }
.shadow-lg { box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important; }

.form-control:focus,
.form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.form-label i {
    margin-right: 8px;
}

.section-card:hover .section-header {
    border-bottom-color: #dee2e6;
}
</style>

@endsection
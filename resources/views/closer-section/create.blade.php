<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Closer-section</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js" integrity="sha512-eyHL1atYNycXNXZMDndxrDhNAegH2BDWt1TmkXJPoGf1WLlNYt08CSjkqF5lnCRmdm3IrkHid8s2jOUY4NIZVQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 for better alerts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        .mail {
            display: flex;
            align-items: center;
        }

        .mail .form-group {
            margin-right: 20px;
        }

        .radio-group {
            display: flex;
            align-items: center;
        }

        .existing-record-card {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>

<body>

    @extends('layouts.admin')

    @section('page-title')
    <!-- Wrap the link in a div or any other container element -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>{{ __('Create Policy') }}</h3>
        <div>
            <a href="{{ route('closer.search.existing') }}" class="btn btn-warning me-2">
                <i class="fas fa-search me-1"></i>Search Existing Records
            </a>
            <a href="{{ route('closers.reports') }}" class="btn btn-primary">
                <i class="fas fa-chart-bar me-1"></i>Closers Reports
            </a>
        </div>
    </div>
    @endsection

    @section('content')

                <!-- Existing Record Alert -->
                @if(session('error') && session('existing_record'))
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
                    <div class="d-flex gap-2">
                        <a href="{{ route('closed-calls.show', session('existing_record')->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye me-1"></i>View Existing Record
                        </a>
                        <a href="{{ route('closer.search.existing') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-search me-1"></i>Search More Records
                        </a>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Regular Error Messages -->
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <h5 class="alert-heading">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                    </h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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

                <!-- resources/views/closed_calls/create.blade.php -->
                <form method="POST" action="{{ route('closer.store') }}" class="needs-validation" id="closerForm">
                    @csrf

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="customer_full_name">Customer Full Name</label>
                            <input type="text" class="form-control" id="customer_full_name" name="customer_full_name" 
                                   placeholder="Customer Full Name" value="{{ old('customer_full_name') }}" required>
                            <div class="invalid-feedback">
                                Please enter the customer's full name.
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="phone_number">Lead id / Phone Number</label>
                            <input type="number" class="form-control" id="phone_number" name="phone_number" 
                                   placeholder="Lead id / Phone Number" value="{{ old('phone_number') }}" required 
                                   minlength="10" maxlength="10" oninput="this.value = this.value.slice(0, 10)">
                            <div class="invalid-feedback">
                                Please enter a valid phone number.
                            </div>
                            <small class="text-muted" id="phone_check_message"></small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mail form-group col-md-6">
                            <div class="form-group col-md-10">
                                <label for="alternate_phone_number">Lead id / Alternate Phone Number</label>
                                <input type="number" class="form-control" id="alternate_phone_number" name="alternate_phone_number" 
                                       placeholder="Lead id / Alternate_phone_number" value="{{ old('alternate_phone_number') }}"
                                       minlength="10" maxlength="10" oninput="this.value = this.value.slice(0, 10)">
                                <div class="invalid-feedback">
                                    Please enter a valid phone number.
                                </div>
                                <small class="text-muted" id="alt_phone_check_message"></small>
                            </div>

                            <div class="form-group col-md-2 radio-group">
                                <input type="checkbox" name="radio" />
                                <label for="none">None</label>
                            </div>
                        </div>

                        <div class="mail form-group col-md-6">
                            <div class="form-group col-md-10">
                                <label for="cx_email">CX Email</label>
                                <input type="email" class="form-control" id="cx_email" name="cx_email" 
                                       placeholder="email" value="{{ old('cx_email') }}" />
                                <div class="invalid-feedback">Please enter a valid email.</div>
                            </div>

                            <div class="form-group col-md-2 radio-group">
                                <input type="checkbox" name="radio" id="none" />
                                <label for="none">None</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" name="address" placeholder="Address" rows="3">{{ old('address') }}</textarea>
                            <div class="invalid-feedback">
                                Please enter the address.
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" 
                                   placeholder="City" value="{{ old('city') }}" required>
                            <div class="invalid-feedback">
                                Please enter the city.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="state">State</label>
                            <select class="form-control" id="state" name="state" required>
                                <option value="">Select State</option>
                                <option value="AL" {{ old('state') == 'AL' ? 'selected' : '' }}>Alabama</option>
                                <option value="AK" {{ old('state') == 'AK' ? 'selected' : '' }}>Alaska</option>
                                <option value="AZ" {{ old('state') == 'AZ' ? 'selected' : '' }}>Arizona</option>
                                <option value="AR" {{ old('state') == 'AR' ? 'selected' : '' }}>Arkansas</option>
                                <option value="CA" {{ old('state') == 'CA' ? 'selected' : '' }}>California</option>
                                <option value="CO" {{ old('state') == 'CO' ? 'selected' : '' }}>Colorado</option>
                                <option value="CT" {{ old('state') == 'CT' ? 'selected' : '' }}>Connecticut</option>
                                <option value="DE" {{ old('state') == 'DE' ? 'selected' : '' }}>Delaware</option>
                                <option value="DC" {{ old('state') == 'DC' ? 'selected' : '' }}>District Of Columbia</option>
                                <option value="FL" {{ old('state') == 'FL' ? 'selected' : '' }}>Florida</option>
                                <option value="GA" {{ old('state') == 'GA' ? 'selected' : '' }}>Georgia</option>
                                <option value="HI" {{ old('state') == 'HI' ? 'selected' : '' }}>Hawaii</option>
                                <option value="ID" {{ old('state') == 'ID' ? 'selected' : '' }}>Idaho</option>
                                <option value="IL" {{ old('state') == 'IL' ? 'selected' : '' }}>Illinois</option>
                                <option value="IN" {{ old('state') == 'IN' ? 'selected' : '' }}>Indiana</option>
                                <option value="IA" {{ old('state') == 'IA' ? 'selected' : '' }}>Iowa</option>
                                <option value="KS" {{ old('state') == 'KS' ? 'selected' : '' }}>Kansas</option>
                                <option value="KY" {{ old('state') == 'KY' ? 'selected' : '' }}>Kentucky</option>
                                <option value="LA" {{ old('state') == 'LA' ? 'selected' : '' }}>Louisiana</option>
                                <option value="ME" {{ old('state') == 'ME' ? 'selected' : '' }}>Maine</option>
                                <option value="MD" {{ old('state') == 'MD' ? 'selected' : '' }}>Maryland</option>
                                <option value="MA" {{ old('state') == 'MA' ? 'selected' : '' }}>Massachusetts</option>
                                <option value="MI" {{ old('state') == 'MI' ? 'selected' : '' }}>Michigan</option>
                                <option value="MN" {{ old('state') == 'MN' ? 'selected' : '' }}>Minnesota</option>
                                <option value="MS" {{ old('state') == 'MS' ? 'selected' : '' }}>Mississippi</option>
                                <option value="MO" {{ old('state') == 'MO' ? 'selected' : '' }}>Missouri</option>
                                <option value="MT" {{ old('state') == 'MT' ? 'selected' : '' }}>Montana</option>
                                <option value="NE" {{ old('state') == 'NE' ? 'selected' : '' }}>Nebraska</option>
                                <option value="NV" {{ old('state') == 'NV' ? 'selected' : '' }}>Nevada</option>
                                <option value="NH" {{ old('state') == 'NH' ? 'selected' : '' }}>New Hampshire</option>
                                <option value="NJ" {{ old('state') == 'NJ' ? 'selected' : '' }}>New Jersey</option>
                                <option value="NM" {{ old('state') == 'NM' ? 'selected' : '' }}>New Mexico</option>
                                <option value="NY" {{ old('state') == 'NY' ? 'selected' : '' }}>New York</option>
                                <option value="NC" {{ old('state') == 'NC' ? 'selected' : '' }}>North Carolina</option>
                                <option value="ND" {{ old('state') == 'ND' ? 'selected' : '' }}>North Dakota</option>
                                <option value="OH" {{ old('state') == 'OH' ? 'selected' : '' }}>Ohio</option>
                                <option value="OK" {{ old('state') == 'OK' ? 'selected' : '' }}>Oklahoma</option>
                                <option value="OR" {{ old('state') == 'OR' ? 'selected' : '' }}>Oregon</option>
                                <option value="PA" {{ old('state') == 'PA' ? 'selected' : '' }}>Pennsylvania</option>
                                <option value="RI" {{ old('state') == 'RI' ? 'selected' : '' }}>Rhode Island</option>
                                <option value="SC" {{ old('state') == 'SC' ? 'selected' : '' }}>South Carolina</option>
                                <option value="SD" {{ old('state') == 'SD' ? 'selected' : '' }}>South Dakota</option>
                                <option value="TN" {{ old('state') == 'TN' ? 'selected' : '' }}>Tennessee</option>
                                <option value="TX" {{ old('state') == 'TX' ? 'selected' : '' }}>Texas</option>
                                <option value="UT" {{ old('state') == 'UT' ? 'selected' : '' }}>Utah</option>
                                <option value="VT" {{ old('state') == 'VT' ? 'selected' : '' }}>Vermont</option>
                                <option value="VA" {{ old('state') == 'VA' ? 'selected' : '' }}>Virginia</option>
                                <option value="WA" {{ old('state') == 'WA' ? 'selected' : '' }}>Washington</option>
                                <option value="WV" {{ old('state') == 'WV' ? 'selected' : '' }}>West Virginia</option>
                                <option value="WI" {{ old('state') == 'WI' ? 'selected' : '' }}>Wisconsin</option>
                                <option value="WY" {{ old('state') == 'WY' ? 'selected' : '' }}>Wyoming</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a state.
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="zip_code">ZIP Code</label>
                            <input type="text" class="form-control" id="zip_code" name="zip_code" 
                                   placeholder="ZIP Code" value="{{ old('zip_code') }}">
                            <div class="invalid-feedback">
                                Please enter a valid ZIP code.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a gender.
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="martial_status">Marital Status</label>
                            <select class="form-control" id="martial_status" name="martial_status">
                                <option value="">Select Marital Status</option>
                                <option value="single" {{ old('martial_status') == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('martial_status') == 'married' ? 'selected' : '' }}>Married</option>
                                <option value="divorced" {{ old('martial_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="widowed" {{ old('martial_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="separated" {{ old('martial_status') == 'separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a marital status.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="age">Age</label>
                            <input type="number" class="form-control" id="age" name="age" 
                                   placeholder="Age" value="{{ old('age') }}" required
                                   minlength="1" maxlength="2" oninput="this.value = this.value.slice(0, 2)">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="dob">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" 
                                   placeholder="MM/DD/YYYY" value="{{ old('dob') }}" 
                                   title="Please enter a date in the format MM/DD/YYYY" onchange="calculateAge()">
                            <div class="invalid-feedback">
                                Please enter a valid date in the format MM/DD/YYYY.
                            </div>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="calculatedage">Calculated Age</label>
                            <input type="text" class="form-control" id="calculatedage" name="calculatedage" 
                                   placeholder="Age" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="palce_of_birth">Place of Birth</label>
                            <input type="text" class="form-control" id="palce_of_birth" name="palce_of_birth" 
                                   placeholder="palce_of_birth" value="{{ old('palce_of_birth') }}">
                        </div>

                        <div class="mail form-group col-md-6">
                            <div class="form-group col-md-6">
                                <label for="height">Height</label>
                                <div class="input-group">
                                    <select class="form-control" id="height_feet" name="height_feet" onchange="updateHeight()">
                                        <option value="" disabled selected>Feet</option>
                                        @for ($i = 1; $i <= 8; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <span class="input-group-text">ft</span>
                                    <select class="form-control" id="height_inches" name="height_inches" onchange="updateHeight()">
                                        <option value="" disabled selected>Inches</option>
                                        @for ($i = 0; $i <= 11; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <span class="input-group-text">in</span>
                                </div>
                                <input type="hidden" id="height" name="height">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="weight">Weight</label>
                                <input type="number" class="form-control" id="height_weight" name="weight" 
                                       placeholder="Weight" value="{{ old('weight') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="social_security">Social Security Number</label>
                            <input type="number" class="form-control" id="social_security" name="social_security" 
                                   placeholder="Social Security Number" value="{{ old('social_security') }}">
                            <div class="invalid-feedback">
                                Please enter social security number.
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Smoker</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="smoker" id="smoker_yes" 
                                           value="yes" {{ old('smoker') == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="smoker_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="smoker" id="smoker_no" 
                                           value="no" {{ old('smoker') == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="smoker_no">No</label>
                                </div>
                            </div>
                            <div class="invalid-feedback">
                                Please select smoker field.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="health_condition">Health Condition</label>
                            <textarea class="form-control" id="health_condition" name="health_condition" 
                                      placeholder="Customer health condition" rows="3">{{ old('health_condition') }}</textarea>
                            <div class="invalid-feedback">
                                Please enter Customer health condition.
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="medication">Medication</label>
                            <textarea class="form-control" id="medication" name="medication" 
                                      placeholder="Enter medication" rows="3">{{ old('medication') }}</textarea>
                            <div class="invalid-feedback">
                                Please enter medication.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="hospital_name">Hospital Name</label>
                            <input type="text" class="form-control" id="hospital_name" name="hospital_name" 
                                   placeholder="Hospital Name" value="{{ old('hospital_name') }}">
                            <div class="invalid-feedback">
                                Please enter the name of the hospital.
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="hospital_address">Hospital Address</label>
                            <input type="text" class="form-control" id="hospital_address" name="hospital_address" 
                                   placeholder="Hospital Address" value="{{ old('hospital_address') }}">
                            <div class="invalid-feedback">
                                Please enter the address of the hospital.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="physician_name">Physician Name</label>
                            <input type="text" class="form-control" id="physician_name" name="physician_name" 
                                   placeholder="Physician Name" value="{{ old('physician_name') }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="monthly_premium">Monthly Premium</label>
                            <input type="number" step="any" class="form-control" id="monthly_premium" name="monthly_premium" 
                                   placeholder="Enter Monthly Premium" value="{{ old('monthly_premium') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="carrier">Carrier</label>
                            <select class="form-control" id="carrier" name="carrier" required>
                                <option value="">Select Carrier</option>
                                <option value="Aetna(CVS)" {{ old('carrier') == 'Aetna(CVS)' ? 'selected' : '' }}>Aetna(CVS)</option>
                                <option value="AFLAC" {{ old('carrier') == 'AFLAC' ? 'selected' : '' }}>AFLAC</option>
                                <option value="AIG" {{ old('carrier') == 'AIG' ? 'selected' : '' }}>AIG</option>
                                <option value="AmAm" {{ old('carrier') == 'AmAm' ? 'selected' : '' }}>American Amicable (AmAm)</option>
                                <option value="Americo" {{ old('carrier') == 'Americo' ? 'selected' : '' }}>Americo</option>
                                <option value="Assurant" {{ old('carrier') == 'Assurant' ? 'selected' : '' }}>Assurant</option>
                                <option value="CVS" {{ old('carrier') == 'CVS' ? 'selected' : '' }}>CVS</option>
                                <option value="Foresters" {{ old('carrier') == 'Foresters' ? 'selected' : '' }}>Foresters</option>
                                <option value="Globe Life" {{ old('carrier') == 'Globe Life' ? 'selected' : '' }}>Globe Life</option>
                                <option value="GW" {{ old('carrier') == 'GW' ? 'selected' : '' }}>Great Western (GW)</option>
                                <option value="GTL (Guarantee Trust Life)" {{ old('carrier') == 'GTL (Guarantee Trust Life)' ? 'selected' : '' }}>GTL (Guarantee Trust Life)</option>
                                <option value="Liberty Banker Life (LBL)" {{ old('carrier') == 'Liberty Banker Life (LBL)' ? 'selected' : '' }}>Liberty Banker Life (LBL)</option>
                                <option value="Lumico" {{ old('carrier') == 'Lumico' ? 'selected' : '' }}>Lumico</option>
                                <option value="Mutual of Omaha" {{ old('carrier') == 'Mutual of Omaha' ? 'selected' : '' }}>Mutual of Omaha</option>
                                <option value="Prosperity" {{ old('carrier') == 'Prosperity' ? 'selected' : '' }}>Prosperity</option>
                                <option value="RNA" {{ old('carrier') == 'RNA' ? 'selected' : '' }}>RNA</option>
                                <option value="Security National Life (SNL)" {{ old('carrier') == 'Security National Life (SNL)' ? 'selected' : '' }}>Security National Life (SNL)</option>
                                <option value="Sentinel Security Life (SSL)" {{ old('carrier') == 'Sentinel Security Life (SSL)' ? 'selected' : '' }}>Sentinel Security Life (SSL)</option>
                                <option value="Sons of Norway" {{ old('carrier') == 'Sons of Norway' ? 'selected' : '' }}>Sons of Norway</option>
                                <option value="Superior Choice (CICA)" {{ old('carrier') == 'Superior Choice (CICA)' ? 'selected' : '' }}>Superior Choice (CICA)</option>
                                <option value="TransAmerica" {{ old('carrier') == 'TransAmerica' ? 'selected' : '' }}>TransAmerica</option>
                                <option value="Gerber" {{ old('carrier') == 'Gerber' ? 'selected' : '' }}>Gerber</option>
                                <option value="AHL" {{ old('carrier') == 'AHL' ? 'selected' : '' }}>AHL</option>
                                <option value="Securico Life" {{ old('carrier') == 'Securico Life' ? 'selected' : '' }}>Securico Life</option>

                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="coverage_plan">Coverage Plan</label>
                            <select class="form-control" id="coverage_plan" name="coverage_plan" required>
                                <option value="" disabled selected>Select Coverage Plan</option>
                                @for($i = 2000; $i <= 50000; $i += 1000)
                                    <option value="{{ $i }}" {{ old('coverage_plan') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="customer_eligibility">Customer Eligibility</label>
                            <select class="form-control" id="customer_eligibility" name="customer_eligibility" required>
                                <option value="">Select Customer Eligibility</option>
                                <option value="Level" {{ old('customer_eligibility') == 'Level' ? 'selected' : '' }}>Level</option>
                                <option value="Graded" {{ old('customer_eligibility') == 'Graded' ? 'selected' : '' }}>Graded</option>
                                <option value="Modified" {{ old('customer_eligibility') == 'Modified' ? 'selected' : '' }}>Modified</option>
                                <option value="Preferred" {{ old('customer_eligibility') == 'Preferred' ? 'selected' : '' }}>Preferred</option>
                                <option value="Standard" {{ old('customer_eligibility') == 'Standard' ? 'selected' : '' }}>Standard</option>
                                <option value="Senior choice immediate" {{ old('customer_eligibility') == 'Senior choice immediate' ? 'selected' : '' }}>Senior choice immediate</option>
                                <option value="Golden solution immediate" {{ old('customer_eligibility') == 'Golden solution immediate' ? 'selected' : '' }}>Golden solution immediate</option>
                                <option value="Senior choice graded" {{ old('customer_eligibility') == 'Senior choice graded' ? 'selected' : '' }}>Senior choice graded</option>
                                <option value="Golden solution graded" {{ old('customer_eligibility') == 'Golden solution graded' ? 'selected' : '' }}>Golden solution graded</option>
                                <option value="Senior choice rop" {{ old('customer_eligibility') == 'Senior choice rop' ? 'selected' : '' }}>Senior choice rop</option>
                                <option value="Golden solution rop" {{ old('customer_eligibility') == 'Golden solution rop' ? 'selected' : '' }}>Golden solution rop</option>
                                <option value="Express select" {{ old('customer_eligibility') == 'Express select' ? 'selected' : '' }}>Express select</option>
                                <option value="Guaranteed Issue" {{ old('customer_eligibility') == 'Guaranteed Issue' ? 'selected' : '' }}>Guaranteed Issue</option>
                                <option value="Graded GTL" {{ old('customer_eligibility') == 'Graded GTL' ? 'selected' : '' }}>Graded GTL</option>
                                <option value="ROP" {{ old('customer_eligibility') == 'ROP' ? 'selected' : '' }}>ROP</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="beneficiary">Beneficiary Name</label>
                            <input type="text" class="form-control" id="beneficiary" name="beneficiary" 
                                   placeholder="Enter Beneficiary Name" value="{{ old('beneficiary') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="beneficiary_relation">Beneficiary Relation</label>
                            <input type="text" class="form-control" id="beneficiary_relation" name="beneficiary_relation" 
                                   placeholder="Beneficiary Relation" value="{{ old('beneficiary_relation') }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="beneficiary_phone">Beneficiary Phone</label>
                            <input type="number" class="form-control" id="beneficiary_phone" name="beneficiary_phone" 
                                   placeholder="Beneficiary Phone" value="{{ old('beneficiary_phone') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="beneficiary_dob">Beneficiary Date of Birth</label>
                            <input type="date" class="form-control" id="beneficiary_dob" name="beneficiary_dob" 
                                   placeholder="MM/DD/YYYY" value="{{ old('beneficiary_dob') }}" 
                                   title="Please enter a date in the format MM/DD/YYYY">
                            <div class="invalid-feedback">
                                Please enter a valid date in the format MM/DD/YYYY.
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="payor">Payor</label>
                            <input type="text" class="form-control" id="payor" name="payor" 
                                   placeholder="Payor" value="{{ old('payor') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="account_type">Account Type</label>
                            <select class="form-control" id="account_type" name="account_type" onchange="toggleFields()">
                                <option value="">Select Account Type</option>
                                <option value="Savings Account" {{ old('account_type') == 'Savings Account' ? 'selected' : '' }}>Savings Account</option>
                                <option value="Checking Account" {{ old('account_type') == 'Checking Account' ? 'selected' : '' }}>Checking Account</option>
                                <option value="Direct Express Card" {{ old('account_type') == 'Direct Express Card' ? 'selected' : '' }}>Direct Express Card</option>
                                <option value="Debit Card" {{ old('account_type') == 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select an account type.
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="bank_account_number">Bank Account Number</label>
                            <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" 
                                   placeholder="Bank Account Number" value="{{ old('bank_account_number') }}" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" 
                                   placeholder="Bank Name" value="{{ old('bank_name') }}" disabled>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="bank_address">Bank Address</label>
                            <input type="text" class="form-control" id="bank_address" name="bank_address" 
                                   placeholder="Bank Address" value="{{ old('bank_address') }}" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="debit_card_routing_number">Routing Number</label>
                            <input type="text" class="form-control" id="routing_number" name="routing_number" 
                                   placeholder="Routing Number" value="{{ old('routing_number') }}" disabled>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="debit_card_direct_express_cvv">Debit Card / Direct Express CVV</label>
                            <input type="text" class="form-control" id="debit_card_direct_express_cvv" 
                                   name="debit_card_direct_express_cvv" placeholder="Debit Card / Direct Express CVV" 
                                   value="{{ old('debit_card_direct_express_cvv') }}" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="debit_card_direct_express_no">Debit Card / Direct Express Number</label>
                            <input type="text" class="form-control" id="debit_card_direct_express_no" 
                                   name="debit_card_direct_express_no" placeholder="Debit Card / Direct Express Number" 
                                   value="{{ old('debit_card_direct_express_no') }}" disabled>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="debit_card_direct_express_expiration">Debit Card/Direct Express Expiration</label>
                            <input type="text" class="form-control" id="debit_card_direct_express_expiration" 
                                   name="debit_card_direct_express_expiration" 
                                   value="{{ old('debit_card_direct_express_expiration') }}" 
                                   title="Please enter a date in the format MM/DD/YYYY" disabled>
                            <div class="invalid-feedback">
                                Please enter a valid date in the format MM/DD/YYYY.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="initial_draft_date">Initial Draft Date</label>
                            <input type="date" class="form-control" id="initial_draft_date" name="initial_draft_date" 
                                   placeholder="MM/DD/YYYY" value="{{ old('initial_draft_date') }}" required>
                            <div class="invalid-feedback">
                                Please enter a valid date in the format MM/DD/YYYY.
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="future_draft_date">Future Draft Date</label>
                            <input type="date" class="form-control" id="future_draft_date" name="future_draft_date" 
                                   placeholder="MM/DD/YYYY" value="{{ old('future_draft_date') }}" 
                                   title="Please enter a date in the format MM/DD/YYYY" required>
                            <div class="invalid-feedback">
                                Please enter a valid date in the format MM/DD/YYYY.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" 
                                      placeholder="Remarks">{{ old('remarks') }}</textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="closer_id">Closer</label>
                            <select class="form-control" id="closer_id" name="closername" required>
                                <option value="">Select Closer</option>
                                @foreach($closers as $closer)
                                <option value="{{ $closer->name }}" {{ old('closername') == $closer->name ? 'selected' : '' }}>
                                    {{ $closer->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="junior_closer_name">Junior Closer Name</label>
                            <select class="form-control" id="junior_closer_name" name="junior_closer_name" required>
                                <option value="">Select Closer</option>
                                @foreach($closers as $closer)
                                <option value="{{ $closer->id }}" {{ old('junior_closer_name') == $closer->id ? 'selected' : '' }}>
                                    {{ $closer->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="center_name">Center Name</label>
                            <select class="form-control" id="center_name" name="center_name" required>
                                <option value="">Select Center Name</option>
                                <option value="sellerz" {{ old('center_name') == 'sellerz' ? 'selected' : '' }}>Sellerz BPO</option>
                                <option value="jsons" {{ old('center_name') == 'jsons' ? 'selected' : '' }}>J.sons Communication</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="sale_made_by">Source</label>
                            <select class="form-control" id="sale_made_by" name="sale_made_by" required>
                                <option value="">Select Source</option>
                                <option value="CallBack" {{ old('sale_made_by') == 'CallBack' ? 'selected' : '' }}>CallBack</option>
                                <option value="Junior Closer's Xfer" {{ old('sale_made_by') == "Junior Closer's Xfer" ? 'selected' : '' }}>Junior Closer's Transfer</option>
                                <option value="livetransfer" {{ old('sale_made_by') == 'livetransfer' ? 'selected' : '' }}>Live Transfer</option>
                                <option value="On Lead" {{ old('sale_made_by') == 'On Lead' ? 'selected' : '' }}>On Lead</option>
                                <option value="retention" {{ old('sale_made_by') == 'retention' ? 'selected' : '' }}>Retention's Transfer</option>
                                <option value="WinBack" {{ old('sale_made_by') == 'WinBack' ? 'selected' : '' }}>WinBack</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select how the sale was made.
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="agent_status">Sale Status</label>
                            <select class="form-control" id="agent_status" name="agent_status" required>
                                <option value="">Select Sale Status</option>
                                <option value="pending" {{ old('agent_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Dropped Call" {{ old('agent_status') == 'Dropped Call' ? 'selected' : '' }}>Dropped Call</option>
                                <option value="Sale made" {{ old('agent_status') == 'Sale made' ? 'selected' : '' }}>Sale made</option>
                                <option value="Scheduled Call Back" {{ old('agent_status') == 'Scheduled Call Back' ? 'selected' : '' }}>Scheduled Call Back</option>
                            </select>
                        </div>
                    </div>

            
                

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Submit Policy
                            </button>
                            <a href="{{ route('closer.closerview') }}" class="btn btn-secondary btn-lg ms-2">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </form>

    @endsection

    <script>
        const scheduledCallbackStatus = 'Scheduled Call Back';
        let defaultRequiredFields = [];

        function applyScheduledCallbackMode() {
            const statusField = document.getElementById('agent_status');
            if (!statusField) {
                return;
            }

            const isScheduledCallback = statusField.value === scheduledCallbackStatus;

            defaultRequiredFields.forEach((field) => {
                const keepRequired = ['phone_number', 'agent_status'].includes(field.id);
                field.required = isScheduledCallback ? keepRequired : true;
            });
        }

        // Calculate Age from DOB
        function calculateAge() {
            const dobInput = document.getElementById('dob').value;
            const ageInput = document.getElementById('calculatedage');

            if (dobInput) {
                const dob = new Date(dobInput);
                const today = new Date();
                
                let years = today.getFullYear() - dob.getFullYear();
                let months = today.getMonth() - dob.getMonth();
                let days = today.getDate() - dob.getDate();

                if (months < 0 || (months === 0 && days < 0)) {
                    years--;
                    months += 12;
                }
                if (days < 0) {
                    months--;
                    const lastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                    days += lastMonth.getDate();
                }

                ageInput.value = `${years} years, ${months} months, ${days} days`;
            } else {
                ageInput.value = '';
            }
        }

        // Update Height
        function updateHeight() {
            const feet = document.getElementById('height_feet').value || '';
            const inches = document.getElementById('height_inches').value || '';
            const height = feet && inches ? `${feet}'${inches}"` : feet ? `${feet}'` : '';
            document.getElementById('height').value = height;
        }

        // Toggle Account Type Fields
        function toggleFields() {
            const accountType = document.getElementById('account_type').value;

            const bankFields = ['bank_name', 'bank_address', 'routing_number', 'bank_account_number'];
            const debitFields = ['debit_card_direct_express_cvv', 'debit_card_direct_express_no', 'debit_card_direct_express_expiration'];

            if (accountType === 'Savings Account' || accountType === 'Checking Account') {
                setFields(bankFields, false);
                setFields(debitFields, true);
            } else if (accountType === 'Direct Express Card' || accountType === 'Debit Card') {
                setFields(debitFields, false);
                setFields(bankFields, true);
            } else {
                setFields(bankFields, true);
                setFields(debitFields, true);
            }
        }

        function setFields(fields, disable) {
            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                field.disabled = disable;
                if (disable) {
                    field.value = '';
                }
            });
        }

        // Initialize validation toggles safely (without relying on window.onload).
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('closerForm');
            if (!form) {
                return;
            }

            defaultRequiredFields = Array.from(form.querySelectorAll('[required]'));

            const statusField = document.getElementById('agent_status');
            if (statusField) {
                statusField.addEventListener('change', applyScheduledCallbackMode);
            }

            // Re-apply right before submit so browser required validation reflects final status.
            form.addEventListener('submit', function() {
                applyScheduledCallbackMode();
            });

            applyScheduledCallbackMode();
            toggleFields();
        });

        // Real-time Phone Number Validation with AJAX
        $(document).ready(function() {
            let checkTimeout;

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
                            alternate_phone_number: altPhoneNumber
                        },
                        success: function(response) {
                            if (response.exists) {
                                const message = '<i class="fas fa-exclamation-triangle text-warning"></i> This number already exists in the system!';
                                
                                if (phoneNumber === response.record.phone_number || phoneNumber === response.record.alternate_phone_number) {
                                    $('#phone_check_message').html(message);
                                }
                                
                                if (altPhoneNumber === response.record.phone_number || altPhoneNumber === response.record.alternate_phone_number) {
                                    $('#alt_phone_check_message').html(message);
                                }

                                // Show SweetAlert
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Record Already Exists!',
                                    html: `
                                        <div class="text-start">
                                            <p><strong>Record ID:</strong> ${response.record.id}</p>
                                            <p><strong>Customer:</strong> ${response.record.customer_full_name || 'N/A'}</p>
                                            <p><strong>Phone:</strong> ${response.record.phone_number || 'N/A'}</p>
                                            <p><strong>Alt Phone:</strong> ${response.record.alternate_phone_number || 'N/A'}</p>
                                            <p><strong>Address:</strong> ${response.record.address || 'N/A'}</p>
                                            <p><strong>City:</strong> ${response.record.city || 'N/A'}</p>
                                            <p><strong>State:</strong> ${response.record.state || 'N/A'}</p>
                                            <p><strong>Carrier:</strong> ${response.record.carrier || 'N/A'}</p>
                                        </div>
                                    `,
                                    showCancelButton: true,
                                    confirmButtonText: '<i class="fas fa-eye me-1"></i> View Record',
                                    cancelButtonText: 'Continue Anyway',
                                    confirmButtonColor: '#0d6efd',
                                    cancelButtonColor: '#6c757d'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = '/closer/show/' + response.record.id;
                                    }
                                });
                            } else {
                                $('#phone_check_message').html('<i class="fas fa-check-circle text-success"></i> Number available');
                                $('#alt_phone_check_message').html('<i class="fas fa-check-circle text-success"></i> Number available');
                            }
                        },
                        error: function() {
                            $('#phone_check_message').html('<i class="fas fa-times-circle text-danger"></i> Error checking number');
                            $('#alt_phone_check_message').html('<i class="fas fa-times-circle text-danger"></i> Error checking number');
                        }
                    });
                }, 800); // Wait 800ms after user stops typing
            }

            // Check on blur (when user leaves the field)
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

            // Also check while typing (optional - debounced)
            $('#phone_number, #alternate_phone_number').on('input', function() {
                const phoneNumber = $('#phone_number').val();
                const altPhoneNumber = $('#alternate_phone_number').val();
                
                if (phoneNumber.length >= 10 || altPhoneNumber.length >= 10) {
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

        function validateDecimal(input) {
            const value = input.value;
            if (!/^\d+(\.\d{0,2})?$/.test(value)) {
                input.setCustomValidity("Please enter a valid decimal number (e.g., 100.50).");
            } else {
                input.setCustomValidity("");
            }
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>
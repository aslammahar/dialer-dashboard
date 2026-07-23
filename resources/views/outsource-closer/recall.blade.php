<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($update) ? 'Edit Policy' : 'Create Policy' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js" integrity="sha512-eyHL1atYNycXNXZMDndxrDhNAegH2BDWt1TmkXJPoGf1WLlNYt08CSjkqF5lnCRmdm3IrkHid8s2jOUY4NIZVQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>
    @extends('layouts.admin')

    @section('page-title')
    <div class="create-link">
        <a href="{{ route('closers.reports') }}" class="btn btn-primary">Closers Reports</a>
    </div><br>
    {{ isset($update) ? __('Edit Policy') : __('Create Policy') }}
    @endsection

    @section('content')
    <form method="POST" action="{{ isset($update) ? route('closer-edit.update', $update->id) : route('closer.store') }}" class="needs-validation">
        @csrf
        @if(isset($update))
            @method('PUT')
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="row">
            <div class="form-group col-md-6">
                <label for="customer_full_name">Customer Full Name</label>
                <input type="text" class="form-control" id="customer_full_name" name="customer_full_name" value="{{ old('customer_full_name', isset($update) ? $update->customer_full_name : '') }}" placeholder="Customer Full Name" required>
                <div class="invalid-feedback">
                    Please enter the customer's full name.
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="phone_number">Phone Number</label>
                <input type="number" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', isset($update) ? $update->phone_number : '') }}" placeholder="Phone Number" required minlength="10" maxlength="10" oninput="this.value = this.value.slice(0, 10)">
                <div class="invalid-feedback">
                    Please enter a valid phone number.
                </div>
            </div>
        </div>

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
        </style>

        <div class="row">
            <div class="mail form-group col-md-6">
                <div class="form-group col-md-10">
                    <label for="alternate_phone_number">Alternate Phone Number</label>
                    <input type="number" class="form-control" id="alternate_phone_number" name="alternate_phone_number" value="{{ old('alternate_phone_number', isset($update) ? $update->alternate_phone_number : '') }}" placeholder="Alternate Phone Number" minlength="10" maxlength="10" oninput="this.value = this.value.slice(0, 10)">
                    <div class="invalid-feedback">
                        Please enter a valid phone number.
                    </div>
                </div>
                <div class="form-group col-md-2 radio-group">
                    <input type="checkbox" name="alternate_phone_none" id="alternate_phone_none" {{ old('alternate_phone_none', isset($update) && !$update->alternate_phone_number ? 'checked' : '') }} onchange="toggleField('alternate_phone_number', this)">
                    <label for="alternate_phone_none">None</label>
                </div>
            </div>
            <div class="mail form-group col-md-6">
                <div class="form-group col-md-10">
                    <label for="cx_email">CX Email</label>
                    <input type="email" class="form-control" id="cx_email" name="cx_email" value="{{ old('cx_email', isset($update) ? $update->cx_email : '') }}" placeholder="Email">
                    <div class="invalid-feedback">
                        Please enter a valid email.
                    </div>
                </div>
                <div class="form-group col-md-2 radio-group">
                    <input type="checkbox" name="email_none" id="email_none" {{ old('email_none', isset($update) && !$update->cx_email ? 'checked' : '') }} onchange="toggleField('cx_email', this)">
                    <label for="email_none">None</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address" placeholder="Address" rows="3">{{ old('address', isset($update) ? $update->address : '') }}</textarea>
                <div class="invalid-feedback">
                    Please enter the address.
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="city">City</label>
                <input type="text" class="form-control" id="city" name="city" value="{{ old('city', isset($update) ? $update->city : '') }}" placeholder="City" required>
                <div class="invalid-feedback">
                    Please enter the city.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="state">State</label>
                <select class="form-control" id="state" name="state">
                    <option value="">Select State</option>
                    @foreach ([
                        'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas', 'CA' => 'California',
                        'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware', 'DC' => 'District Of Columbia',
                        'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho', 'IL' => 'Illinois',
                        'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Louisiana',
                        'ME' => 'Maine', 'MD' => 'Maryland', 'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota',
                        'MS' => 'Mississippi', 'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada',
                        'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York',
                        'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma', 'OR' => 'Oregon',
                        'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina', 'SD' => 'South Dakota',
                        'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VT' => 'Vermont', 'VA' => 'Virginia',
                        'WA' => 'Washington', 'WV' => 'West Virginia', 'WI' => 'Wisconsin', 'WY' => 'Wyoming'
                    ] as $code => $name)
                        <option value="{{ $code }}" {{ old('state', isset($update) ? $update->state : '') == $code ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">
                    Please select a state.
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="zip_code">ZIP Code</label>
                <input type="text" class="form-control" id="zip_code" name="zip_code" value="{{ old('zip_code', isset($update) ? $update->zip_code : '') }}" placeholder="ZIP Code">
                <div class="invalid-feedback">
                    Please enter a valid ZIP code.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="gender">Gender</label>
                <select class="form-control" id="gender" name="gender">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender', isset($update) ? $update->gender : '') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', isset($update) ? $update->gender : '') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
                <div class="invalid-feedback">
                    Please select a gender.
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="martial_status">Marital Status</label>
                <select class="form-control" id="martial_status" name="martial_status">
                    <option value="">Select Marital Status</option>
                    <option value="single" {{ old('martial_status', isset($update) ? $update->martial_status : '') == 'single' ? 'selected' : '' }}>Single</option>
                    <option value="married" {{ old('martial_status', isset($update) ? $update->martial_status : '') == 'married' ? 'selected' : '' }}>Married</option>
                    <option value="divorced" {{ old('martial_status', isset($update) ? $update->martial_status : '') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                    <option value="widowed" {{ old('martial_status', isset($update) ? $update->martial_status : '') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                    <option value="separated" {{ old('martial_status', isset($update) ? $update->martial_status : '') == 'separated' ? 'selected' : '' }}>Separated</option>
                </select>
                <div class="invalid-feedback">
                    Please select a marital status.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="age">Age</label>
                <input type="number" class="form-control" id="age" name="age" value="{{ old('age', isset($update) ? $update->age : '') }}" placeholder="Age" required minlength="1" maxlength="2" oninput="this.value = this.value.slice(0, 2)">
            </div>
            <div class="form-group col-md-3">
                <label for="dob">Date of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob" value="{{ $update->dob ? date('Y-m-d', strtotime($update->dob)) : '' }}" placeholder="MM/DD/YYYY" title="Please enter a date in the format MM/DD/YYYY" onchange="calculateAge()">
                <div class="invalid-feedback">
                    Please enter a valid date in the format MM/DD/YYYY.
                </div>
            </div>
            <div class="form-group col-md-3">
                <label for="calculatedage">Calculated Age</label>
                <input type="text" class="form-control" id="calculatedage" name="calculatedage" value="{{ old('calculatedage', isset($update) ? $update->calculatedage : '') }}" placeholder="Age" readonly>
            </div>
        </div>

        <script>
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
        </script>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="palce_of_birth">Place of Birth</label>
                <input type="text" class="form-control" id="palce_of_birth" name="palce_of_birth" value="{{ old('palce_of_birth', isset($update) ? $update->palce_of_birth : '') }}" placeholder="Place of Birth">
            </div>
            <div class="mail form-group col-md-6">
                <div class="form-group col-md-6">
                    <label for="height">Height</label>
                    <div class="input-group">
                        <select class="form-control" id="height_feet" name="height_feet" onchange="updateHeight()">
                            <option value="" disabled {{ !old('height_feet', isset($update) ? explode("'", $update->height ?? '')[0] ?? '' : '') ? 'selected' : '' }}>Feet</option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ old('height_feet', isset($update) ? explode("'", $update->height ?? '')[0] ?? '' : '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <span class="input-group-text">ft</span>
                        <select class="form-control" id="height_inches" name="height_inches" onchange="updateHeight()">
                            <option value="" disabled {{ !old('height_inches', isset($update) ? explode("'", $update->height ?? '')[1] ?? '' : '') ? 'selected' : '' }}>Inches</option>
                            @for ($i = 0; $i <= 11; $i++)
                                <option value="{{ $i }}" {{ old('height_inches', isset($update) ? explode("'", $update->height ?? '')[1] ?? '' : '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <span class="input-group-text">in</span>
                    </div>
                    <input type="hidden" id="height" name="height" value="{{ old('height', isset($update) ? $update->height : '') }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="weight">Weight</label>
                    <input type="number" class="form-control" id="height_weight" name="weight" value="{{ old('weight', isset($update) ? $update->weight : '') }}" placeholder="Weight">
                </div>
            </div>
        </div>

        <script>
            function updateHeight() {
                const feet = document.getElementById('height_feet').value || '';
                const inches = document.getElementById('height_inches').value || '';
                const height = feet && inches ? `${feet}'${inches}"` : feet ? `${feet}'` : '';
                document.getElementById('height').value = height;
            }
        </script>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="social_security">Social Security Number</label>
                <input type="text" class="form-control" id="social_security" name="social_security" value="{{ old('social_security', isset($update) ? $update->social_security : '') }}" placeholder="Social Security Number">
                <div class="invalid-feedback">
                    Please enter social security number.
                </div>
            </div>
            <div class="form-group col-md-6">
                <label>Smoker</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="smoker" id="smoker_yes" value="yes" {{ old('smoker', isset($update) ? $update->smoker : '') == 'yes' ? 'checked' : '' }}>
                        <label class="form-check-label" for="smoker_yes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="smoker" id="smoker_no" value="no" {{ old('smoker', isset($update) ? $update->smoker : '') == 'no' ? 'checked' : '' }}>
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
                <textarea class="form-control" id="health_condition" name="health_condition" placeholder="Customer health condition" rows="3">{{ old('health_condition', isset($update) ? $update->health_condition : '') }}</textarea>
                <div class="invalid-feedback">
                    Please enter Customer health condition.
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="medication">Medication</label>
                <textarea class="form-control" id="medication" name="medication" placeholder="Enter medication" rows="3">{{ old('medication', isset($update) ? $update->medication : '') }}</textarea>
                <div class="invalid-feedback">
                    Please enter medication.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="hospital_name">Hospital Name</label>
                <input type="text" class="form-control" id="hospital_name" name="hospital_name" value="{{ old('hospital_name', isset($update) ? $update->hospital_name : '') }}" placeholder="Hospital Name">
                <div class="invalid-feedback">
                    Please enter the name of the hospital.
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="hospital_address">Hospital Address</label>
                <input type="text" class="form-control" id="hospital_address" name="hospital_address" value="{{ old('hospital_address', isset($update) ? $update->hospital_address : '') }}" placeholder="Hospital Address">
                <div class="invalid-feedback">
                    Please enter the address of the hospital.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="physician_name">Physician Name</label>
                <input type="text" class="form-control" id="physician_name" name="physician_name" value="{{ old('physician_name', isset($update) ? $update->physician_name : '') }}" placeholder="Physician Name">
            </div>
            <div class="form-group col-md-6">
                <label for="monthly_premium">Monthly Premium</label>
                <input type="text" class="form-control" id="monthly_premium" name="monthly_premium" value="{{ old('monthly_premium', isset($update) ? $update->monthly_premium : '') }}" placeholder="Enter Monthly Premium" pattern="^\d+(\.\d{1,2})?$" oninput="validateDecimal(this)">
            </div>
        </div>

        <script>
            function validateDecimal(input) {
                const value = input.value;
                if (!/^\d+(\.\d{0,2})?$/.test(value)) {
                    input.setCustomValidity("Please enter a valid decimal number (e.g., 100.50).");
                } else {
                    input.setCustomValidity("");
                }
            }
        </script>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="carrier">Carrier</label>
                <select class="form-control" id="carrier" name="carrier">
                    <option value="">Select Carrier</option>
                    @foreach ([
                        'Aetna', 'Aetna(CVS)', 'AFLAC', 'AIG', 'AmAm' => 'American Amicable (AmAm)', 'Americo',
                        'Assurant', 'C5', 'CVS', 'Foresters', 'Globe Life', 'GW' => 'Great Western (GW)',
                        'GTL (Guarantee Trust Life)', 'Liberty Banker Life (LBL)', 'Lumico', 'Mutual of Omaha',
                        'Prosperity', 'RNA', 'Security National Life (SNL)', 'Sentinel Security Life (SSL)',
                        'Sons of Norway', 'Superior Choice (CICA)'
                    ] as $key => $value)
                        <option value="{{ is_numeric($key) ? $value : $key }}" {{ old('carrier', isset($update) ? $update->carrier : '') == (is_numeric($key) ? $value : $key) ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="coverage_plan">Coverage Plan</label>
                <select class="form-control" id="coverage_plan" name="coverage_plan">
                    <option value="" disabled {{ !old('coverage_plan', isset($update) ? $update->coverage_plan : '') ? 'selected' : '' }}>Select Coverage Plan</option>
                    @for ($i = 2000; $i <= 50000; $i += 1000)
                        <option value="{{ $i }}" {{ old('coverage_plan', isset($update) ? $update->coverage_plan : '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="customer_eligibility">Customer Eligibility</label>
                <select class="form-control" id="customer_eligibility" name="customer_eligibility">
                    <option value="">Select Customer Eligibility</option>
                    <option value="level" {{ old('customer_eligibility', isset($update) ? $update->customer_eligibility : '') == 'level' ? 'selected' : '' }}>Level</option>
                    <option value="Graded/Modified" {{ old('customer_eligibility', isset($update) ? $update->customer_eligibility : '') == 'Graded/Modified' ? 'selected' : '' }}>Graded/Modified</option>
                    <option value="Guaranteed Issue" {{ old('customer_eligibility', isset($update) ? $update->customer_eligibility : '') == 'Guaranteed Issue' ? 'selected' : '' }}>Guaranteed Issue</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="beneficiary">Beneficiary Name</label>
                <input type="text" class="form-control" id="beneficiary" name="beneficiary" value="{{ old('beneficiary', isset($update) ? $update->beneficiary : '') }}" placeholder="Enter Beneficiary Name">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="beneficiary_relation">Beneficiary Relation</label>
                <input type="text" class="form-control" id="beneficiary_relation" name="beneficiary_relation" value="{{ old('beneficiary_relation', isset($update) ? $update->beneficiary_relation : '') }}" placeholder="Beneficiary Relation">
            </div>
            <div class="form-group col-md-6">
                <label for="beneficiary_phone">Beneficiary Phone</label>
                <input type="number" class="form-control" id="beneficiary_phone" name="beneficiary_phone" value="{{ old('beneficiary_phone', isset($update) ? $update->beneficiary_phone : '') }}" placeholder="Beneficiary Phone">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="beneficiary_dob">Beneficiary Date of Birth</label>
                <input type="date" class="form-control" id="beneficiary_dob" name="beneficiary_dob" value="{{ $update->beneficiary_dob ? date('Y-m-d', strtotime($update->beneficiary_dob)) : '' }}" placeholder="MM/DD/YYYY" title="Please enter a date in the format MM/DD/YYYY">
                <div class="invalid-feedback">
                    Please enter a valid date in the format MM/DD/YYYY.
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="payor">Payor</label>
                <input type="text" class="form-control" id="payor" name="payor" value="{{ old('payor', isset($update) ? $update->payor : '') }}" placeholder="Payor">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="account_type">Account Type</label>
                <select class="form-control" id="account_type" name="account_type" onchange="toggleFields()">
                    <option value="">Select Account Type</option>
                    <option value="Savings Account" {{ old('account_type', isset($update) ? $update->account_type : '') == 'Savings Account' ? 'selected' : '' }}>Savings Account</option>
                    <option value="Checking Account" {{ old('account_type', isset($update) ? $update->account_type : '') == 'Checking Account' ? 'selected' : '' }}>Checking Account</option>
                    <option value="Direct Express Card" {{ old('account_type', isset($update) ? $update->account_type : '') == 'Direct Express Card' ? 'selected' : '' }}>Direct Express Card</option>
                    <option value="Debit Card" {{ old('account_type', isset($update) ? $update->account_type : '') == 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
                </select>
                <div class="invalid-feedback">
                    Please select an account type.
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="bank_account_number">Bank Account Number</label>
                <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', isset($update) ? $update->bank_account_number : '') }}" placeholder="Bank Account Number" >
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="bank_name">Bank Name</label>
                <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', isset($update) ? $update->bank_name : '') }}" placeholder="Bank Name" >
            </div>
            <div class="form-group col-md-6">
                <label for="bank_address">Bank Address</label>
                <input type="text" class="form-control" id="bank_address" name="bank_address" value="{{ old('bank_address', isset($update) ? $update->bank_address : '') }}" placeholder="Bank Address" >
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="routing_number">Routing Number</label>
                <input type="text" class="form-control" id="routing_number" name="routing_number" value="{{ old('routing_number', isset($update) ? $update->routing_number : '') }}" placeholder="Routing Number" >
            </div>
            <div class="form-group col-md-6">
                <label for="debit_card_direct_express_cvv">Debit Card / Direct Express CVV</label>
                <input type="text" class="form-control" id="debit_card_direct_express_cvv" name="debit_card_direct_express_cvv" value="{{ old('debit_card_direct_express_cvv', isset($update) ? $update->debit_card_direct_express_cvv : '') }}" placeholder="Debit Card / Direct Express CVV" >
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="debit_card_direct_express_no">Debit Card / Direct Express Number</label>
                <input type="text" class="form-control" id="debit_card_direct_express_no" name="debit_card_direct_express_no" value="{{ old('debit_card_direct_express_no', isset($update) ? $update->debit_card_direct_express_no : '') }}" placeholder="Debit Card / Direct Express Number" >
            </div>
            <div class="form-group col-md-6">
                <label for="debit_card_direct_express_expiration">Debit Card/Direct Express Expiration</label>
                <input type="text" class="form-control" id="debit_card_direct_express_expiration" name="debit_card_direct_express_expiration" value="{{ old('debit_card_direct_express_expiration', isset($update) ? $update->debit_card_direct_express_expiration : '') }}" placeholder="MM/DD/YYYY" title="Please enter a date in the format MM/DD/YYYY" >
                <div class="invalid-feedback">
                    Please enter a valid date in the format MM/DD/YYYY.
                </div>
            </div>
        </div>

        

        <div class="row">
            <div class="form-group col-md-6">
                <label for="initial_draft_date">Initial Draft Date</label>
                <input type="date" class="form-control" id="initial_draft_date" name="initial_draft_date" value="{{ $update->initial_draft_date ? date('Y-m-d', strtotime($update->initial_draft_date)) : '' }}" placeholder="MM/DD/YYYY">
                <div class="invalid-feedback">
                    Please enter a valid date in the format MM/DD/YYYY.
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="future_draft_date">Future Draft Date</label>
                <input type="date" class="form-control" id="future_draft_date" name="future_draft_date" value="{{ $update->future_draft_date ? date('Y-m-d', strtotime($update->future_draft_date)) : '' }}" placeholder="MM/DD/YYYY" title="Please enter a date in the format MM/DD/YYYY">
                <div class="invalid-feedback">
                    Please enter a valid date in the format MM/DD/YYYY.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="remarks">Remarks</label>
                <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Remarks">{{ old('remarks', isset($update) ? $update->remarks : '') }}</textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="closer_id">Closer</label>
                <select class="form-control" id="closer_id" name="closername" required>
                    <option value="">Select Closer</option>
                    @foreach($closers as $closer)
                        <option value="{{ $closer->name }}" {{ old('closername', isset($update) ? $update->closername : '') == $closer->name ? 'selected' : '' }}>{{ $closer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="junior_closer_name">Junior Closer Name</label>
                <input type="text" class="form-control" id="junior_closer_name" name="junior_closer_name" value="{{ old('junior_closer_name', isset($update) ? $update->junior_closer_name : '') }}" placeholder="Junior Closer Name">
            </div>
            <div class="form-group col-md-6">
                <label for="center_name">Center Name</label>
                <select class="form-control" id="center_name" name="center_name">
                    <option value="">Select Center Name</option>
                    <option value="sellerz" {{ old('center_name', isset($update) ? $update->center_name : '') == 'sellerz' ? 'selected' : '' }}>Sellerz BPO</option>
                    <option value="jsons" {{ old('center_name', isset($update) ? $update->center_name : '') == 'jsons' ? 'selected' : '' }}>J.sons Communication</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="sale_made_by">Source</label>
                <select class="form-control" id="sale_made_by" name="sale_made_by">
                    <option value="">Select Source</option>
                    <option value="CallBack" {{ old('sale_made_by', isset($update) ? $update->sale_made_by : '') == 'CallBack' ? 'selected' : '' }}>CallBack</option>
                    <option value="Junior Closer's Xfer" {{ old('sale_made_by', isset($update) ? $update->sale_made_by : '') == "Junior Closer's Xfer" ? 'selected' : '' }}>Junior Closer's Transfer</option>
                    <option value="livetransfer" {{ old('sale_made_by', isset($update) ? $update->sale_made_by : '') == 'livetransfer' ? 'selected' : '' }}>Live Transfer</option>
                    <option value="On Lead" {{ old('sale_made_by', isset($update) ? $update->sale_made_by : '') == 'On Lead' ? 'selected' : '' }}>On Lead</option>
                    <option value="retention" {{ old('sale_made_by', isset($update) ? $update->sale_made_by : '') == 'retention' ? 'selected' : '' }}>Retention's Transfer</option>
                    <option value="WinBack" {{ old('sale_made_by', isset($update) ? $update->sale_made_by : '') == 'WinBack' ? 'selected' : '' }}>WinBack</option>
                </select>
                <div class="invalid-feedback">
                    Please select how the sale was made.
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="agent_status">Sale Status</label>
                <select class="form-control" id="agent_status" name="agent_status">
                    <option value="">Select Sale Status</option>
                    <option value="pending" {{ old('agent_status', isset($update) ? $update->agent_status : '') == 'pending' ? 'selected' : '' }}>Call Back</option>
                    <option value="Dropped Call" {{ old('agent_status', isset($update) ? $update->agent_status : '') == 'Dropped Call' ? 'selected' : '' }}>Dropped Call</option>
                    <option value="Sale made" {{ old('agent_status', isset($update) ? $update->agent_status : '') == 'Sale made' ? 'selected' : '' }}>Sale made</option>
                    <option value="Scheduled Call Back" {{ old('agent_status', isset($update) ? $update->agent_status : '') == 'Scheduled Call Back' ? 'selected' : '' }}>Scheduled Call Back</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">{{ isset($update) ? 'Update' : 'Submit' }}</button>
    </form>
    @endsection

    <script>
        function formatDate(input) {
            var inputValue = input.value;
            var numericValue = inputValue.replace(/\D/g, '');
            var formattedValue = numericValue.replace(/(\d{2})(\d{2})(\d{4})/, '$1/$2/$3');
            input.value = formattedValue;
        }
    </script>
</body>
</html>
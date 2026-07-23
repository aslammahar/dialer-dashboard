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


</head>

<body>

    @extends('layouts.admin')


    @section('page-title')
    <!-- Wrap the link in a div or any other container element -->
    <div class="create-link">
        <a href="{{ route('closers.reports') }}" class="btn btn-primary">Closers Reports</a>
    </div><br>

    {{ __('Create Policy') }}
    @endsection




    @section('content')



    <!-- resources/views/closed_calls/create.blade.php -->
    <form method="POST" action="{{ route('outsource.store') }}" class="needs-validation">
        @csrf


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
            <input type="text" class="form-control" id="customer_full_name" name="customer_full_name" placeholder="Customer Full Name" required>
            <div class="invalid-feedback">
                Please enter the customer's full name.
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="phone_number">Lead id / Phone Number</label>
            <input type="number" class="form-control" id="phone_number" name="phone_number" placeholder="Lead id / Phone Number" required 
            minlength="10" maxlength="10" oninput="this.value = this.value.slice(0, 10)">
            <div class="invalid-feedback">
                Please enter a valid phone number.
            </div>
        </div>
        </div>

        <style>
    .mail {
  display: flex;
  align-items: center; /* Vertically center the items */
}

.mail .form-group {
  margin-right: 20px; /* Space between the input and radio button */
}

.radio-group {
  display: flex;
  align-items: center;
}

</style>

        <div class="row">
        <div class="mail form-group col-md-6">

        <div class="form-group col-md-10">
            <label for="alternate_phone_number">Lead id / Alternate Phone Number</label>
            <input type="number" class="form-control" id="alternate_phone_number" name="alternate_phone_number" placeholder="Lead id / Alternate_phone_number"
            minlength="10" maxlength="10" oninput="this.value = this.value.slice(0, 10)">
            <div class="invalid-feedback">
                Please enter a valid phone number.
            </div>
        </div>

        <div class="form-group col-md-6">
        <div class="form-group col-md-2 radio-group">
    <input type="checkbox" name="radio"  />
    <label for="none">None</label>
  </div>
        </div>
        </div>




        







        <div class="mail form-group col-md-6">
  <div class="form-group col-md-10">
    <label for="cx_email">CX Email</label>
    <input
      type="email"
      class="form-control"
      id="cx_email"
      name="cx_email"
      placeholder="email"
    />
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
            <textarea class="form-control" id="address" name="address" placeholder="Address" rows="3"></textarea>

            <div class="invalid-feedback">
                Please enter the address.
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="city">City</label>
            <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
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
                <option value="AL">Alabama</option>
                <option value="AK">Alaska</option>
                <option value="AZ">Arizona</option>
                <option value="AR">Arkansas</option>
                <option value="CA">California</option>
                <option value="CO">Colorado</option>
                <option value="CT">Connecticut</option>
                <option value="DE">Delaware</option>
                <option value="DC">District Of Columbia</option>
                <option value="FL">Florida</option>
                <option value="GA">Georgia</option>
                <option value="HI">Hawaii</option>
                <option value="ID">Idaho</option>
                <option value="IL">Illinois</option>
                <option value="IN">Indiana</option>
                <option value="IA">Iowa</option>
                <option value="KS">Kansas</option>
                <option value="KY">Kentucky</option>
                <option value="LA">Louisiana</option>
                <option value="ME">Maine</option>
                <option value="MD">Maryland</option>
                <option value="MA">Massachusetts</option>
                <option value="MI">Michigan</option>
                <option value="MN">Minnesota</option>
                <option value="MS">Mississippi</option>
                <option value="MO">Missouri</option>
                <option value="MT">Montana</option>
                <option value="NE">Nebraska</option>
                <option value="NV">Nevada</option>
                <option value="NH">New Hampshire</option>
                <option value="NJ">New Jersey</option>
                <option value="NM">New Mexico</option>
                <option value="NY">New York</option>
                <option value="NC">North Carolina</option>
                <option value="ND">North Dakota</option>
                <option value="OH">Ohio</option>
                <option value="OK">Oklahoma</option>
                <option value="OR">Oregon</option>
                <option value="PA">Pennsylvania</option>
                <option value="RI">Rhode Island</option>
                <option value="SC">South Carolina</option>
                <option value="SD">South Dakota</option>
                <option value="TN">Tennessee</option>
                <option value="TX">Texas</option>
                <option value="UT">Utah</option>
                <option value="VT">Vermont</option>
                <option value="VA">Virginia</option>
                <option value="WA">Washington</option>
                <option value="WV">West Virginia</option>
                <option value="WI">Wisconsin</option>
                <option value="WY">Wyoming</option>
            </select>
            <div class="invalid-feedback">
                Please select a state.
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="zip_code">ZIP Code</label>
            <input type="text" class="form-control" id="zip_code" name="zip_code" placeholder="ZIP Code">
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
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
            <div class="invalid-feedback">
                Please select a gender.
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="martial_status">Marital Status</label>
            <select class="form-control" id="martial_status" name="martial_status">
                <option value="">Select Marital Status</option>
                <option value="single">Single</option>
                <option value="married">Married</option>
                <option value="divorced">Divorced</option>
                <option value="widowed">Widowed</option>
                <option value="separated">Separated</option>
            </select>
            <div class="invalid-feedback">
                Please select a marital status.
            </div>
        </div>
        </div>


        <div class="row">


        <div class="form-group col-md-6">
            <label for="age">Age</label>
            <input type="number" class="form-control" id="age" name="age" placeholder="Age" required
            minlength="1" maxlength="2" oninput="this.value = this.value.slice(0, 2)">
        </div>


        <div class="form-group col-md-3">
    <label for="dob">Date of Birth</label>
    <input type="date" class="form-control" id="dob" name="dob" placeholder="MM/DD/YYYY" title="Please enter a date in the format MM/DD/YYYY" onchange="calculateAge()">
    <div class="invalid-feedback">
        Please enter a valid date in the format MM/DD/YYYY.
    </div>
</div>

<!-- Age Field -->
<div class="form-group col-md-3">
    <label for="calculatedage">Caluculated Age</label>
    <input type="text" class="form-control" id="calculatedage" name="calculatedage" placeholder="Age" readonly>
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

            // Adjust the year and month if necessary
            if (months < 0 || (months === 0 && days < 0)) {
                years--;
                months += 12;
            }
            if (days < 0) {
                months--;
                const lastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                days += lastMonth.getDate(); // Days in the last month
            }

            // Set the calculated age in years, months, and days
            ageInput.value = `${years} years, ${months} months, ${days} days`;
        } else {
            ageInput.value = ''; // Clear the age field if DOB is not selected
        }
    }
</script>

        </div>

        <div class="row">
        <div class="form-group col-md-6">
            <label for="palce_of_birth">Place of Birth</label>
            <input type="text" class="form-control" id="palce_of_birth" name="palce_of_birth" placeholder="palce_of_birth">
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
    <!-- Hidden input to store combined height -->
    <input type="hidden" id="height" name="height">
</div>

<script>
    function updateHeight() {
        const feet = document.getElementById('height_feet').value || '';
        const inches = document.getElementById('height_inches').value || '';
        const height = feet && inches ? `${feet}'${inches}"` : feet ? `${feet}'` : '';
        document.getElementById('height').value = height;
    }
</script>

        
        <div class="form-group col-md-6">
            <label for="weight">Weight</label>
            <input type="number" class="form-control" id="height_weight" name="weight" placeholder="Weight">
        </div>
        </div>

        </div>


        <div class="row">
        <div class="form-group col-md-6">
            <label for="social_security">Social Security Number</label>
            <input type="number" class="form-control" id="social_security" name="social_security" placeholder="Social Security Number">
            <div class="invalid-feedback">
                Please enter social security number.
            </div>
        </div>

        <div class="form-group col-md-6">

            <label>Smoker</label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="smoker" id="smoker_yes" value="yes">
                    <label class="form-check-label" for="smoker_yes">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="smoker" id="smoker_no" value="no">
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
            <textarea class="form-control" id="health_condition" name="health_condition" placeholder="Customer health condition" rows="3"></textarea>
            <div class="invalid-feedback">
                Please enter Customer health condition.
            </div>
        </div>


        <div class="form-group col-md-6">
            <label for="medication">Medication</label>
            <textarea class="form-control" id="medication" name="medication" placeholder="Enter  medication" rows="3"></textarea>
            <div class="invalid-feedback">
                Please enter medication.
            </div>
        </div>
        </div>



        <div class="row">

        <div class="form-group col-md-6">
            <label for="hospital_name">Hospital Name</label>
            <input type="text" class="form-control" id="hospital_name" name="hospital_name" placeholder="Hospital Name">
            <div class="invalid-feedback">
                Please enter the name of the hospital.
            </div>
        </div>


        <div class="form-group col-md-6">
            <label for="hospital_address">Hospital Address</label>
            <input type="text" class="form-control" id="hospital_address" name="hospital_address" placeholder="Hospital Address">
            <div class="invalid-feedback">
                Please enter the address of the hospital.
            </div>
        </div>
        </div>



        <div class="row">

        <div class="form-group col-md-6">
            <label for="physician_name">Physician Name</label>
            <input type="text" class="form-control" id="physician_name" name="physician_name" placeholder="Physician Name">
        </div>

        <div class="form-group col-md-6">
    <label for="monthly_premium">Monthly Premium</label>
    <input type="number" step="any" class="form-control" id="monthly_premium" name="monthly_premium" placeholder="Enter Monthly Premium"  required>
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

        </div>


        <div class="row">

        <div class="form-group col-md-6">
    <label for="carrier">Carrier</label>
    <select class="form-control" id="carrier" name="carrier" required>
        <option value="">Select Carrier</option>
        <option value="Aetna(CVS)">Aetna(CVS)</option>
        <option value="AFLAC">AFLAC</option>
        <option value="AIG">AIG</option>
        <option value="AmAm">American Amicable (AmAm)</option>
        <option value="Americo">Americo</option>
        <option value="Assurant">Assurant</option>
        <option value="CVS">CVS</option>
        <option value="Foresters">Foresters</option>
        <option value="Globe Life">Globe Life</option>
        <option value="GW">Great Western (GW)</option>
        <option value="GTL (Guarantee Trust Life)">GTL (Guarantee Trust Life)</option>
        <option value="Liberty Banker Life (LBL)">Liberty Banker Life (LBL)</option>
        <option value="Lumico">Lumico</option>
        <option value="Mutual of Omaha">Mutual of Omaha</option>
        <option value="Prosperity">Prosperity</option>
        <option value="RNA">RNA</option>
        <option value="Security National Life (SNL)">Security National Life (SNL)</option>
        <option value="Sentinel Security Life (SSL)">Sentinel Security Life (SSL)</option>
        <option value="Sons of Norway">Sons of Norway</option>
        <option value="Superior Choice (CICA)">Superior Choice (CICA)</option>
        <option value="TransAmerica">TransAmerica</option>

    </select>
</div>

        <div class="form-group col-md-6">
  <label for="coverage_plan">Coverage Plan</label>
  <select class="form-control" id="coverage_plan" name="coverage_plan" required>
    <!-- Options from 2000 to 50000 in steps of 1000 -->
    <option value="" disabled selected>Select Coverage Plan</option>
    <option value="2000">2000</option>
    <option value="3000">3000</option>
    <option value="4000">4000</option>
    <option value="5000">5000</option>
    <option value="6000">6000</option>
    <option value="7000">7000</option>
    <option value="8000">8000</option>
    <option value="9000">9000</option>
    <option value="10000">10000</option>
    <option value="11000">11000</option>
    <option value="12000">12000</option>
    <option value="13000">13000</option>
    <option value="14000">14000</option>
    <option value="15000">15000</option>
    <option value="16000">16000</option>
    <option value="17000">17000</option>
    <option value="18000">18000</option>
    <option value="19000">19000</option>
    <option value="20000">20000</option>
    <option value="21000">21000</option>
    <option value="22000">22000</option>
    <option value="23000">23000</option>
    <option value="24000">24000</option>
    <option value="25000">25000</option>
    <option value="26000">26000</option>
    <option value="27000">27000</option>
    <option value="28000">28000</option>
    <option value="29000">29000</option>
    <option value="30000">30000</option>
    <option value="31000">31000</option>
    <option value="32000">32000</option>
    <option value="33000">33000</option>
    <option value="34000">34000</option>
    <option value="35000">35000</option>
    <option value="36000">36000</option>
    <option value="37000">37000</option>
    <option value="38000">38000</option>
    <option value="39000">39000</option>
    <option value="40000">40000</option>
    <option value="41000">41000</option>
    <option value="42000">42000</option>
    <option value="43000">43000</option>
    <option value="44000">44000</option>
    <option value="45000">45000</option>
    <option value="46000">46000</option>
    <option value="47000">47000</option>
    <option value="48000">48000</option>
    <option value="49000">49000</option>
    <option value="50000">50000</option>
  </select>
</div>


        </div>



        <div class="row">

      

        <div class="form-group col-md-6">
            <label for="customer_eligibility">Customer Eligibility</label>
            <select class="form-control" id="customer_eligibility" name="customer_eligibility" required>
                <option value="">Select Cusotmer Eligibility</option>
                <option value="Level">Level</option>
                <option value="Graded">Graded</option>
                <option value="Modified">Modified</option>
                <option value="Preferred">Preferred</option>
                <option value="Standard">Standard</option>
                <option value="Senior choice immediate">Senior choice immediate </option>
                <option value="Golden solution immediate">Golden solution immediate </option>
                <option value="Senior choice graded">Senior choice graded </option>
                <option value="Golden solution graded">Golden solution graded </option>
                <option value="Senior choice rop">Senior choice rop </option>
                <option value="Golden solution rop">Golden solution rop </option>
                <option value="Express select">Express select </option>
                <option value="Guaranteed Issue">Guaranteed Issue</option>
                <option value="Graded GTL">Graded GTL</option>
                <option value="ROP">ROP</option>

            </select>
           
        </div>


        <div class="form-group col-md-6">
            <label for="beneficiary">Beneficiary Name</label>
            <input type="text" class="form-control" id="beneficiary" name="beneficiary" placeholder="Enter Beneficiary Name">
        </div>
        </div>



        <div class="row">

        <div class="form-group col-md-6">
            <label for="beneficiary_relation">Beneficiary Relation</label>
            <input type="text" class="form-control" id="beneficiary_relation" name="beneficiary_relation" placeholder="Beneficiary Relation">
        </div>

        <div class="form-group col-md-6">
            <label for="beneficiary_phone">Beneficiary Phone</label>
            <input type="number" class="form-control" id="beneficiary_phone" name="beneficiary_phone" placeholder="Beneficiary Phone">
        </div>
        </div>


        <div class="row">


        <div class="form-group col-md-6">
            <label for="beneficiary_dob">Beneficiary Date of Birth</label>
            <input type="date" class="form-control" id="beneficiary_dob" name="beneficiary_dob" placeholder="MM/DD/YYYY"  title="Please enter a date in the format MM/DD/YYYY"  >
            <div class="invalid-feedback">
                Please enter a valid date in the format MM/DD/YYYY.
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="payor">Payor</label>
            <input type="text" class="form-control" id="payor" name="payor" placeholder="Payor">
        </div>
        </div>

        <div class="row">

    <div class="form-group col-md-6">
        <label for="account_type">Account Type</label>
        <select class="form-control" id="account_type" name="account_type" onchange="toggleFields()">
            <option value="">Select Account Type</option>
            <option value="Savings Account">Savings Account</option>
            <option value="Checking Account">Checking Account</option>
            <option value="Direct Express Card">Direct Express Card</option>
            <option value="Debit Card">Debit Card</option>
        </select>
        <div class="invalid-feedback">
            Please select an account type.
        </div>
    </div>
    <div class="form-group col-md-6">
        <label for="bank_account_number">Bank Account Number</label>
        <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" placeholder="Bank Account Number" disabled>
    </div>
   

</div>

<div class="row">

    <div class="form-group col-md-6">
        <label for="bank_name">Bank Name</label>
        <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Bank Name" disabled>
    </div>
    <div class="form-group col-md-6">
        <label for="bank_address">Bank Address</label>
        <input type="text" class="form-control" id="bank_address" name="bank_address" placeholder="Bank Address" disabled>
    </div>

</div>

<div class="row">

    <div class="form-group col-md-6">
        <label for="debit_card_routing_number">Routing Number</label>
        <input type="text" class="form-control" id="routing_number" name="routing_number" placeholder="Routing Number" disabled>
    </div>

    
    <div class="form-group col-md-6">
        <label for="debit_card_direct_express_cvv">Debit Card / Direct Express CVV</label>
        <input type="text" class="form-control" id="debit_card_direct_express_cvv" name="debit_card_direct_express_cvv" placeholder="Debit Card / Direct Express CVV" disabled>
    </div>
</div>

<div class="row">

    <div class="form-group col-md-6">
        <label for="debit_card_direct_express_no">Debit Card / Direct Express Number</label>
        <input type="text" class="form-control" id="debit_card_direct_express_no" name="debit_card_direct_express_no" placeholder="Debit Card / Direct Express Number" disabled>
    </div>

    <div class="form-group col-md-6">
        <label for="debit_card_direct_express_expiration">Debit Card/Direct Express Expiration</label>
        <input type="text" class="form-control" id="debit_card_direct_express_expiration" name="debit_card_direct_express_expiration" title="Please enter a date in the format MM/DD/YYYY" disabled>
        <div class="invalid-feedback">
            Please enter a valid date in the format MM/DD/YYYY.
        </div>
    </div>

</div>

<script>
    function toggleFields() {
        const accountType = document.getElementById('account_type').value;

        const bankFields = ['bank_name', 'bank_address', 'routing_number', 'bank_account_number'];
        const debitFields = ['debit_card_direct_express_cvv', 'debit_card_direct_express_no', 'debit_card_direct_express_expiration'];

        if (accountType === 'Savings Account' || accountType === 'Checking Account') {
            // Enable bank fields, disable debit card fields
            setFields(bankFields, false);  // Enable bank fields
            setFields(debitFields, true);  // Disable debit card fields
        } else if (accountType === 'Direct Express Card' || accountType === 'Debit Card') {
            // Enable debit card fields, disable bank fields
            setFields(debitFields, false);  // Enable debit card fields
            setFields(bankFields, true);  // Disable bank fields
        } else {
            // If no account type is selected, disable all fields
            setFields(bankFields, true);
            setFields(debitFields, true);
        }
    }

    function setFields(fields, disable) {
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            field.disabled = disable;
            if (disable) {
                field.value = ''; // Optionally clear the value when disabling
            }
        });
    }

    // Call toggleFields() on page load to ensure the correct state of the fields
    window.onload = toggleFields;
</script>

       


        <div class="row">

        <div class="form-group col-md-6">
            <label for="initial_draft_date">Initial Draft Date</label>
            <input type="date" class="form-control" id="initial_draft_date" name="initial_draft_date" placeholder="MM/DD/YYYY" required>
            <div class="invalid-feedback">
                Please enter a valid date in the format MM/DD/YYYY.
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="future_draft_date">Future Draft Date</label>
            <input type="date" class="form-control" id="future_draft_date" name="future_draft_date" placeholder="MM/DD/YYYY"  title="Please enter a date in the format MM/DD/YYYY" required>
            <div class="invalid-feedback">
                Please enter a valid date in the format MM/DD/YYYY.
            </div>
        </div>
        </div>



        <div class="row">

        <div class="form-group col-md-6">
            <label for="remarks">Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Remarks"></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="closer_id">Closer</label>
            <select class="form-control" id="closer_id" name="closername" required>
                <option value="">Select Closer</option>
                @foreach($closers as $closer)
                <option value="{{ $closer->name }}">{{ $closer->name }}</option>
                @endforeach

            </select>


        </div>
        </div>


        <div class="row">

        <div class="form-group col-md-6">
            <label for="junior_closer_name">Junior Closer Name</label>
       
        <select class="form-control"  id="junior_closer_name" name="junior_closer_name" required>
                <option value="">Select Closer</option>
                @foreach($closers as $closer)
                <option value="{{ $closer->id }}">{{ $closer->name }}</option>
                @endforeach

            </select>
       
        </div>

        <div class="form-group col-md-6">
            <label for="center_name">Center Name</label>
            <select class="form-control" id="center_name" name="center_name" required>
                <option value="">Select Center Name</option>
                <option value="sellerz">Sellerz BPO</option>
                <option value="jsons">J.sons Communication</option>
            </select>
        </div>
        </div>



        <div class="row">

        <div class="form-group col-md-6">
            <label for="sale_made_by">Source </label>
            <select class="form-control" id="sale_made_by" name="sale_made_by" required> 
                <option value="">Select Source</option>
                <option value="CallBack">CallBack</option>
                <option value="Junior Closer's Xfer">Junior Closer's Transfer</option>

                <option value="livetransfer">Live Transfer</option>
                <option value="On Lead">On Lead</option>
                <option value="retention">Retention's Transfer</option>

                <option value="WinBack">WinBack</option>

            </select>
            <div class="invalid-feedback">
                Please select how the sale was made.
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="agent_status">Sale Status</label>
            <select class="form-control" id="agent_status" name="agent_status" required>
                <option value="">Select Sale Status</option>
                <option value="pending">Call Back</option>
                <option value="Dropped Call">Dropped Call</option>
                <option value="Sale made">Sale made</option>
                <option value="Scheduled Call Back">Scheduled Call Back</option>



            </select>
        </div>

    
        </div>

       



        <button type="submit" class="btn btn-primary">Submit</button>
    </form>


    @endsection



    <script>
        function formatDate(input) {
            // Get input value
            var inputValue = input.value;

            // Remove non-numeric characters
            var numericValue = inputValue.replace(/\D/g, '');

            // Apply formatting MM/DD/YYYY
            var formattedValue = numericValue.replace(/(\d{2})(\d{2})(\d{4})/, '$1/$2/$3');

            // Update input value
            input.value = formattedValue;
        }
    </script>







</body>

</html>



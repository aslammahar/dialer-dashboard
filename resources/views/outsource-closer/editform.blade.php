<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">


</head>

<body>


    @extends('layouts.admin')



    @section('page-title')

    {{ __('Update Admin') }}
    @endsection




    @section('content')

    <div class="container mt-5">


        <div class="row">
            <div class="">
            <form action="" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="customer_full_name" class="form-label">Customer Full Name</label>
            <input type="text" class="form-control" id="customer_full_name" name="customer_full_name" value="{{$update->customer_full_name }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="number" class="form-control" id="phone_number" name="phone_number" value="{{$update->phone_number }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="alternate_phone_number">Alternate Phone Number</label>
            <input type="number" class="form-control" id="alternate_phone_number" name="alternate_phone_number" value="{{$update->alternate_phone_number }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="cx_email">CX Email</label>
            <input type="email" class="form-control" id="cx_email" name="cx_email" value="{{$update->cx_email }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="address">Address</label>
            <textarea class="form-control" id="address" name="address" rows="3">{{ $update->address }}</textarea>
        </div>
        <div class="col-md-6 mb-3">
            <label for="city">City</label>
            <input type="text" class="form-control" id="city" name="city" value="{{$update->city }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="state">State</label>
            <input type="text" class="form-control" id="state" name="state" value="{{ $update->state }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="zip_code" class="form-label">Zip Code</label>
            <input type="number" class="form-control" id="zip_code" name="zip_code" value="{{$update->zip_code }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="gender">Gender</label>
            <select class="form-control" id="gender" name="gender">
                <option value="">Select Gender</option>
                <option value="male" {{ $update->gender == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ $update->gender == 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ $update->gender == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="martial_status">Marital Status</label>
            <select class="form-control" id="martial_status" name="martial_status">
                <option value="">Select Marital Status</option>
                <option value="single" {{ $update->martial_status == 'single' ? 'selected' : '' }}>Single</option>
                <option value="married" {{ $update->martial_status == 'married' ? 'selected' : '' }}>Married</option>
                <option value="divorced" {{ $update->martial_status == 'divorced' ? 'selected' : '' }}>Divorced</option>
                <option value="widowed" {{ $update->martial_status == 'widowed' ? 'selected' : '' }}>Widowed</option>
                <option value="separated" {{ $update->martial_status == 'separated' ? 'selected' : '' }}>Separated</option>
            </select>
        </div>

       
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="age" class="form-label">Age</label>
            <input type="number" class="form-control" id="age" name="age" value="{{$update->age }}">
        </div>
        <div class="col-md-6 mb-3">
  <label for="dob">Date of Birth</label>
  <input type="date" class="form-control" id="dob" name="dob" 
         value="{{ $update->dob ? date('Y-m-d', strtotime($update->dob)) : '' }}">
</div>

        
    </div>

    
<style>
     .mail {
  display: flex;
  align-items: center; /* Vertically center the items */
}
</style>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="social_security" class="form-label">Social Security</label>
            <input type="text" class="form-control" id="social_security" name="social_security" value="{{$update->social_security }}">
        </div>
        <div class="col-md-6 mb-3">
            <label>Smoker</label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="smoker" id="smoker_yes" value="yes" {{ $update->smoker == 'yes' ? 'checked' : '' }}>
                    <label class="form-check-label" for="smoker_yes">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="smoker" id="smoker_no" value="no" {{ $update->smoker == 'no' ? 'checked' : '' }}>
                    <label class="form-check-label" for="smoker_no">No</label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="health_condition">Health Condition</label>
            <textarea class="form-control" id="health_condition" name="health_condition" rows="3">{{ $update->health_condition }}</textarea>
        </div>
        <div class="col-md-6 mb-3">
            <label for="medication" class="form-label">Medication</label>
            <input type="text" class="form-control" id="medication" name="medication" value="{{$update->medication }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="hospital_name" class="form-label">Hospital Name</label>
            <input type="text" class="form-control" id="hospital_name" name="hospital_name" value="{{$update->hospital_name }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="hospital_address">Hospital Address</label>
            <input type="text" class="form-control" id="hospital_address" name="hospital_address" value="{{$update->hospital_address }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="physician_name">Physician Name</label>
            <input type="text" class="form-control" id="physician_name" name="physician_name" value="{{$update->physician_name }}">
        </div>
        <div class="col-md-3 mb-3">
            <label for="monthly_premium">Monthly Premium</label>
            <input type="text" class="form-control" id="monthly_premium" name="monthly_premium" value="{{$update->monthly_premium }}">
        </div>

        <div class="col-md-3 mb-3">
            <label for="coverage_plan">Coverage Plan</label>
            <input type="text" class="form-control" id="coverage_plan" name="coverage_plan" value="{{$update->coverage_plan }}">
        </div>
    </div>


    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="customer_eligibility">Customer Eligibility</label>
            <input type="text" class="form-control" id="customer_eligibility" name="customer_eligibility" value="{{$update->customer_eligibility }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="beneficiary">Beneficiary</label>
            <input type="text" class="form-control" id="beneficiary" name="beneficiary" value="{{$update->beneficiary }}">
       
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="beneficiary_relation">Beneficiary Relation</label>
            <input type="text" class="form-control" id="beneficiary_relation" name="beneficiary_relation" value="{{$update->beneficiary_relation }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="beneficiary_phone">Beneficiary Phone</label>
            <input type="number" class="form-control" id="beneficiary_phone" name="beneficiary_phone" value="{{$update->beneficiary_phone }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="beneficiary_dob">Beneficiary Date of Birth</label>
            <input type="date" class="form-control" id="beneficiary_dob" name="beneficiary_dob" 
            value="{{ $update->beneficiary_dob ? date('Y-m-d', strtotime($update->beneficiary_dob)) : '' }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="payor">Payor</label>
            <input type="text" class="form-control" id="payor" name="payor" value="{{$update->payor }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="bank_name">Bank Name</label>
            <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{$update->bank_name }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="bank_address">Bank Address</label>
            <input type="text" class="form-control" id="bank_address" name="bank_address" value="{{$update->bank_address }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="routing_number">Routing Number</label>
            <input type="number" class="form-control" id="routing_number" name="routing_number" value="{{$update->routing_number }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="bank_account_number">Bank Account Number</label>
            <input type="number" class="form-control" id="bank_account_number" name="bank_account_number" value="{{$update->bank_account_number }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="debit_card_direct_express_no">Debit Card/Direct Express No</label>
            <input type="number" class="form-control" id="debit_card_direct_express_no" name="debit_card_direct_express_no" value="{{$update->debit_card_direct_express_no }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="debit_card_direct_express_expiration">Debit Card/Direct Express Expiration</label>
            <input type="text" class="form-control" id="debit_card_direct_express_expiration" name="debit_card_direct_express_expiration" value="{{$update->debit_card_direct_express_expiration }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="debit_card_direct_express_cvv">Debit Card/Direct Express CVV</label>
            <input type="number" class="form-control" id="debit_card_direct_express_cvv" name="debit_card_direct_express_cvv" value="{{$update->debit_card_direct_express_cvv }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="account_type">Account Type</label>
            <input type="text" class="form-control" id="account_type" name="account_type" value="{{$update->account_type }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="initial_draft_date">Initial Draft Date</label>
            <input type="text" class="form-control" id="initial_draft_date" name="initial_draft_date" placeholder="MM/DD/YYYY" value="{{ $update->initial_draft_date ? date('m/d/Y', strtotime($update->initial_draft_date)) : '' }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="future_draft_date">Future Draft Date</label>
            <input type="date" class="form-control" id="future_draft_date" name="future_draft_date" 
            value="{{ $update->future_draft_date ? date('Y-m-d', strtotime($update->future_draft_date)) : '' }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="underwriter_name">Underwriter Name</label>
            <input type="text" class="form-control" id="underwriter_name" name="underwriter_name" value="{{$update->underwriter_name }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="remarks">Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ $update->remarks }}</textarea>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="closer_id">Closer ID</label>
            <input type="number" class="form-control" id="closer_id" name="closer_id" value="{{$update->closer_id }}">
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
            <input type="text" class="form-control" id="junior_closer_name" name="junior_closer_name" value="{{$update->junior_closer_name }}">
        </div>

        
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="center_name">Center Name</label>
            <input type="text" class="form-control" id="center_name" name="center_name" value="{{$update->center_name }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="sale_made_by">Sale Made By</label>
            <input type="text" class="form-control" id="sale_made_by" name="sale_made_by" value="{{$update->sale_made_by }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="agent_status">Agent Status</label>
            <select class="form-control" id="agent_status" name="agent_status">
                <option value="">Select Status</option>
                <option value="active" {{ $update->agent_status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $update->agent_status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="suspended" {{ $update->agent_status == 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label for="agent_status">Status</label>
           
            <input type="text" class="form-control" id="status" name="status" value="{{$update->status }}" readonly>

        </div>
        <div class="col-md-3 mb-3">
    <label for="carrier">Carrier</label>
    
    <select class="form-control" id="carrier" name="carrier">
        <option value="">Select Carrier</option>
        <option value="Aetna(CVS)" {{ $update->carrier == 'Aetna(CVS)' ? 'selected' : '' }}>Aetna(CVS)</option>
        <option value="AFLAC" {{ $update->carrier == 'AFLAC' ? 'selected' : '' }}>AFLAC</option>
        <option value="AIG" {{ $update->carrier == 'AIG' ? 'selected' : '' }}>AIG</option>
        <option value="AmAm" {{ $update->carrier == 'AmAm' ? 'selected' : '' }}>American Amicable (AmAm)</option>
        <option value="Americo" {{ $update->carrier == 'Americo' ? 'selected' : '' }}>Americo</option>
        <option value="Assurant" {{ $update->carrier == 'Assurant' ? 'selected' : '' }}>Assurant</option>
        <option value="CVS" {{ $update->carrier == 'CVS' ? 'selected' : '' }}>CVS</option>
        <option value="Foresters" {{ $update->carrier == 'Foresters' ? 'selected' : '' }}>Foresters</option>
        <option value="Globe Life" {{ $update->carrier == 'Globe Life' ? 'selected' : '' }}>Globe Life</option>
        <option value="GW" {{ $update->carrier == 'GW' ? 'selected' : '' }}>Great Western (GW)</option>
        <option value="GTL (Guarantee Trust Life)" {{ $update->carrier == 'GTL (Guarantee Trust Life)' ? 'selected' : '' }}>GTL (Guarantee Trust Life)</option>
        <option value="Liberty Banker Life (LBL)" {{ $update->carrier == 'Liberty Banker Life (LBL)' ? 'selected' : '' }}>Liberty Banker Life (LBL)</option>
        <option value="Lumico" {{ $update->carrier == 'Lumico' ? 'selected' : '' }}>Lumico</option>
        <option value="Mutual of Omaha" {{ $update->carrier == 'Mutual of Omaha' ? 'selected' : '' }}>Mutual of Omaha</option>
        <option value="Prosperity" {{ $update->carrier == 'Prosperity' ? 'selected' : '' }}>Prosperity</option>
        <option value="RNA" {{ $update->carrier == 'RNA' ? 'selected' : '' }}>RNA</option>
        <option value="Security National Life (SNL)" {{ $update->carrier == 'Security National Life (SNL)' ? 'selected' : '' }}>Security National Life (SNL)</option>
        <option value="Sentinel Security Life (SSL)" {{ $update->carrier == 'Sentinel Security Life (SSL)' ? 'selected' : '' }}>Sentinel Security Life (SSL)</option>
        <option value="Sons of Norway" {{ $update->carrier == 'Sons of Norway' ? 'selected' : '' }}>Sons of Norway</option>
        <option value="Superior Choice (CICA)" {{ $update->carrier == 'Superior Choice (CICA)' ? 'selected' : '' }}>Superior Choice (CICA)</option>
    </select>
</div>
    </div>

    <div class="row">
        
        <div class="col-md-6 mb-3">
            <label for="remarks">Client Comment</label>
            <textarea class="form-control" id="clientscomment" name="clients_comment" rows="3" readonly>{{ $update->clients_comment }}</textarea>
        </div>
      

    <div class="col-md-3 mb-3">
        <label for="clientDropdown">Client:</label>
        <select id="clientDropdown" class="form-control" name="client_id" onchange="fetchUsers(this.value)">
            <option value="">Select Client</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}" {{ $update->client_id == $client->id ? 'selected' : '' }}>
                    {{ $client->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 mb-3">
    <label for="userDropdown">User:</label>
    <select id="userDropdown" class="form-control" name="clients_id">
        <option value="">Select User</option>
    </select>
</div>
</div>

<script>
   function fetchUsers(clientId) {
    const userDropdown = document.getElementById("userDropdown");
    userDropdown.innerHTML = '<option value="">Select User</option>'; // Reset with default option

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

            // Pre-select user if editing
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

</script>
    </div>
    




    <div class="mt-3">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>

            </div>
        </div>

    </div>
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
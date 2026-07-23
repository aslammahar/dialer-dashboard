@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Create Carriers view</h2>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('carriers.store') }}" method="POST">
        @csrf
<div class="row">
        <!-- Licensed Agency Dropdown with Select2 -->
        <div class="form-group col-md-6">
            <label for="licensed_agency">Licensed Agency</label>
            <select name="licensed_agency[]" id="licensed_agency" class="form-control select2" multiple required>
                <option value="K2">K2</option>
                <option value="W3">W3</option>
                <option value="S4">S4</option>
                <option value="D6">D6</option>
                <option value="A8">A8</option>
                <option value="B10">B10</option>
                <!-- <option value="other">Other (please specify)</option> -->
            </select>

            <!-- Textbox for Other Licensed Agency -->
            <input type="text" class="form-control mt-2" id="other_agency" name="other_agency" placeholder="Enter Other Agency" style="display: none;">
        </div>

        <!-- States Dropdown with Select2 -->
        <div class="form-group col-md-6">
            <label for="state">State</label>
            <select name="state[]" id="state" class="form-control select2" multiple required>
                <option value="AL">AL</option>
                <option value="AR">AR</option>
                <option value="AZ">AZ</option>
                <option value="CO">CO</option>
                <option value="CT">CT</option>
                <option value="ID">ID</option>
                <option value="IL">IL</option>
                <option value="KY">KY</option>
                <option value="LA">LA</option>
                <option value="MA">MA</option>
                <option value="MD">MD</option>
                <option value="MI">MI</option>
                <option value="MN">MN</option>
                <option value="MO">MO</option>
                <option value="MS">MS</option>
                <option value="NC">NC</option>
                <option value="NJ">NJ</option>
                <option value="NM">NM</option>
                <option value="NV">NV</option>
                <option value="NY">NY</option>
                <option value="ME">ME</option>
                <option value="OH">OH</option>
                <option value="OK">OK</option>
                <option value="OR">OR</option>
                <option value="PA">PA</option>
                <option value="RI">RI</option>
                <option value="SC">SC</option>
                <option value="TN">TN</option>
                <option value="TX">TX</option>
                <option value="UT">UT</option>
                <option value="VA">VA</option>
                <option value="WI">WI</option>
                <option value="WV">WV</option>
            </select>
        </div>
        </div>
        <div class="row">
        <!-- Licensed Agent Name Dropdown with Select2 -->
        <div class="form-group col-md-6">
            <label for="licensed_agent_name">Licensed Agent Name</label>
            <select name="licensed_agent_name" id="licensed_agent_name" class="form-control select2" required>
                @foreach($licensedAgents as $agent)
                    <option value="{{ $agent->name }}">{{ $agent->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            <label for="carriers">Carrier</label>
            <select class="form-control select2" id="carriers" name="carriers[]" multiple required>
                <option value="">Select Carrier</option>
                <option value="Aetna">Aetna</option>
                <option value="AIG">AIG</option>
                <option value="AmAm">American Amicable (AmAm)</option>
                <option value="Americo">Americo</option>
                <option value="Assurant">Assurant</option>
                <option value="CVS">CVS</option>
                <option value="Foresters">Foresters</option>
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
                <option value="Globe Life">Globe Life</option>
                <option value="C5">C5</option>
                <option value="Aetna(CVS)">Aetna(CVS)</option>
                <option value="Superior Choice (CICA)">Superior Choice (CICA)</option>
                <option value="AFLAC">AFLAC</option>


            </select>

        </div></div>
        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 for all dropdowns
        $('.select2').select2();

        // Show/Hide Other Agency input based on selection
        $('#licensed_agency').on('change', function () {
            const selectedOptions = $(this).val(); // Get selected values
            const otherAgencyInput = $('#other_agency');

            // Check if "Other" is selected
            if (selectedOptions && selectedOptions.includes('other')) {
                otherAgencyInput.show(); // Show the input if "Other" is selected
            } else {
                otherAgencyInput.hide(); // Hide it otherwise
                otherAgencyInput.val(''); // Clear the input value
            }
        });

        // Trigger change event to handle pre-selected values
        $('#licensed_agency').trigger('change');
    });
</script>
@endsection

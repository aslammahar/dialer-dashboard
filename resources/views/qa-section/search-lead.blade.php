@extends('layouts.admin')

@section('content')
<div class="container mt-8">
    <h1 class="display-4">Search Lead</h1>
    <div class="table-responsive">
        <form action="{{ route('leads.search') }}" method="GET">
            <div class="form-group">
                <label for="lead_id" class="font-weight-bold">Lead ID</label>
                <input type="text" id="lead_id" name="lead_id" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        @if($lead)
        <form action="{{ route('leads.update') }}" method="POST" class="mt-5">
            @csrf
            <input type="hidden" name="id" value="{{ $lead->id }}">

            <!-- Non-updatable fields -->
            <div class="form-group">
                <label for="range_id" class="font-weight-bold">Range Id</label>
                <input type="text" id="range_id" name="range_id" class="form-control" value="{{ $lead->id }}" readonly>
            </div>
            <div class="form-group">
                <label for="recording_link" class="font-weight-bold">Recording</label>
                <div class="d-flex align-items-center">

                    <audio controls>
                        <source src="{{ $lead->recording_link }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            </div>

            <div class="form-group">
                <label for="recording" class="font-weight-bold">Recording Link</label>
                <div class="d-flex align-items-center">

                    <a href="{{ $lead->recording }}" target="_blank" class="btn btn-link">Open Recording</a>
                </div>
            </div>
            <div class="form-group">
                <label for="lead_id" class="font-weight-bold">Lead Id</label>
                <input type="text" id="lead_id" name="lead_id" class="form-control" value="{{ $lead->lead_id }}" readonly>
            </div>
            <div class="form-group">
                <label for="phone_number" class="font-weight-bold">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" class="form-control" value="{{ $lead->phone_number }}" readonly>
            </div>
            <div class="form-group">
                <label for="dialer_id" class="font-weight-bold">Dialer Id</label>
                <input type="text" id="dialer_id" name="dialer_id" class="form-control" value="{{ $lead->dialer_id }}" readonly>
            </div>
            <div class="form-group">
                <label for="center" class="font-weight-bold">Center</label>
                <input type="text" id="center" name="center" class="form-control" value="{{ $lead->center }}" readonly>
            </div>

            <!-- Updatable and optionable fields -->
            <div class="row">
                <div class="col-md-6">




                    <div class="form-group">
                        <label for="Isgreetings" class="font-weight-bold">Is Greetings?</label>
                        <select id="Isgreetings" name="Isgreetings" class="form-control">
                            <option value="Yes" {{ $lead->Isgreetings == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ $lead->Isgreetings == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Ispitch_call_about" class="font-weight-bold">Is Pitch Call About?</label>
                        <select id="Ispitch_call_about" name="Ispitch_call_about" class="form-control">
                            <option value="Yes" {{ $lead->Ispitch_call_about == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ $lead->Ispitch_call_about == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Isage" class="font-weight-bold">Is Age?</label>
                        <select id="Isage" name="Isage" class="form-control">
                            <option value="Yes" {{ $lead->Isage == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ $lead->Isage == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="Issmoker" class="font-weight-bold">Is Smoker?</label>
                        <select id="Issmoker" name="Issmoker" class="form-control">
                            <option value="Yes" {{ $lead->Issmoker == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ $lead->Issmoker == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Ishealth1" class="font-weight-bold">Is Health 1?</label>
                        <select id="Ishealth1" name="Ishealth1" class="form-control">
                            <option value="Yes" {{ $lead->Ishealth1 == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ $lead->Ishealth1 == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Isbeneficiary" class="font-weight-bold">Is Beneficiary?</label>
                        <select id="Isbeneficiary" name="Isbeneficiary" class="form-control">
                            <option value="Yes" {{ $lead->Isbeneficiary == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ $lead->Isbeneficiary == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Isaccount" class="font-weight-bold">Is Account?</label>
                        <select id="Isaccount" name="Isaccount" class="form-control">
                            <option value="Yes" {{ $lead->Isaccount == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ $lead->Isaccount == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Istransfer_details" class="font-weight-bold">Is Transfer Details?</label>
                        <select id="Istransfer_details" name="Istransfer_details" class="form-control">
                            <option value="Yes" {{ $lead->Istransfer_details == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ $lead->Istransfer_details == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Isxfer_consent" class="font-weight-bold">Is Transfer Consent?</label>
                        <select id="Isxfer_consent" name="Isxfer_consent" class="form-control">
                            <option value="Yes" {{ $lead->Isxfer_consent == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ $lead->Isxfer_consent == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="rebuttals" class="font-weight-bold">rebuttals</label>
                        <select id="rebuttals" name="rebuttals" class="form-control">
                            <option value="0" {{ $lead->rebuttals == '0' ? 'selected' : '' }}>0</option>
                            <option value="1" {{ $lead->rebuttals == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ $lead->rebuttals == '2' ? 'selected' : '' }}>2</option>
                            <option value="more" {{ $lead->rebuttals == 'more' ? 'selected' : '' }}>More</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="use_of_rebuttals" class="font-weight-bold">Use of Rebuttals</label>
                        <select id="use_of_rebuttals" name="use_of_rebuttals" class="form-control">
                            <option value="0" {{ $lead->use_of_rebuttals == '0' ? 'selected' : '' }}>0</option>
                            <option value="1" {{ $lead->use_of_rebuttals == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ $lead->use_of_rebuttals == '2' ? 'selected' : '' }}>2</option>
                            <option value="more" {{ $lead->use_of_rebuttals == 'more' ? 'selected' : '' }}>More</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="no_of_refusals" class="font-weight-bold">Number of Refusals</label>
                        <select id="no_of_refusals" name="no_of_refusals" class="form-control">
                            <option value="0" {{ $lead->no_of_refusals == '0' ? 'selected' : '' }}>0</option>
                            <option value="1" {{ $lead->no_of_refusals == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ $lead->no_of_refusals == '2' ? 'selected' : '' }}>2</option>
                            <option value="more" {{ $lead->no_of_refusals == 'more' ? 'selected' : '' }}>More</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Isplan" class="font-weight-bold">Is Plan?</label>
                        <select id="Isplan" name="Isplan" class="form-control">
                            <option value="Yes" {{ $lead->Isplan == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ $lead->Isplan == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="QAstatus" class="font-weight-bold">QA Status</label>
                        <select id="QAstatus" name="QAstatus" class="form-control">
                            <option value="approved" {{ $lead->QAstatus == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $lead->QAstatus == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="on review" {{ $lead->QAstatus == 'on review' ? 'selected' : '' }}>On Review</option>
                        </select>
                    </div>

                </div>
            </div>
            <div class="form-group">
                <label for="Qacomments" class="font-weight-bold">QA Comments</label>
                <textarea id="Qacomments" name="Qacomments" class="form-control">{{ $lead->Qacomments }}</textarea>
            </div>

            <!-- Add more fields as needed -->
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
    @endif
</div>
<script src="{{ asset('js/app.js') }}"></script>
@endsection
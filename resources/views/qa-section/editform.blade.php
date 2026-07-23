<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Policyyy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">


</head>

<body>


    @extends('layouts.admin')



    @section('page-title')

    {{ __('Update Avatar Call') }}
    @endsection



    @section('content')

    <div class="container mt-5">


        <div class="row">
            <div class="col-sm-6">
                <form action="" method="POST">
                    @csrf
                    @method('PUT')


                    <div class="mb-3">
                        <label for="agent_id">Agent Name</label>
                        <select class="form-control" id="agent_id" name="agent_id">
                            <option value="">Select Agent</option>
                            @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ $update->agent_id == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="mb-3">
                        <label for="lead_id" class="form-label">Phone Number / Lead Id </label>
                        <input type="text" class="form-control" id="lead_id" name="lead_id" value="{{$update->lead_id }}">
                    </div>

                    <div class="mb-3">
                        <label for="dialer_id" class="form-label">Dialer Id</label>
                        <input type="text" class="form-control" id="dialer_id" name="dialer_id" value="{{$update->dialer_id }}">
                    </div>
                    <div class="mb-3">
                        <label for="AGE" class="form-label">AGE</label>
                        <input type="text" class="form-control" id="AGE" name="AGE" value="{{$update->AGE }}">
                    </div>

                    <div class="mb-3">
                        <label for="Smoker" class="form-label">Smoker</label>
                        <input type="text" class="form-control" id="Smoker" name="Smoker" value="{{$update->Smoker }}">
                    </div>

                    <div class="mb-3">
                        <label for="recording" class="form-label">Recording</label>
                        <input type="text" class="form-control" id="recording" name="recording" value="{{$update->recording }}">
                    </div>

                    <div class="mb-3">
                        <label for="Isgreetings" class="form-label">Is Greetings</label>
                        <input type="text" class="form-control" id="Isgreetings" name="Isgreetings" value="{{$update->Isgreetings }}">
                    </div>

                    <div class="mb-3">
                        <label for="Ispitch_call_about" class="form-label">Ispitch Call About</label>
                        <input type="text" class="form-control" id="Ispitch_call_about" name="Ispitch_call_about" value="{{$update->Ispitch_call_about }}">
                    </div>
                    <div class="mb-3">
                        <label for="Isage" class="form-label">Isage</label>
                        <input type="text" class="form-control" id="Isage" name="Isage" value="{{$update->Isage }}">
                    </div>
                    <div class="mb-3">
                        <label for="Issmoker" class="form-label">Issmoker</label>
                        <input type="text" class="form-control" id="Issmoker" name="Issmoker" value="{{$update->Issmoker }}">
                    </div>
                    <div class="mb-3">
                        <label for="Ishealth1" class="form-label">Ishealth1</label>
                        <input type="text" class="form-control" id="Ishealth1" name="Ishealth1" value="{{$update->Ishealth1 }}">
                    </div>
                    <div class="mb-3">
                        <label for="Isbeneficiary" class="form-label">Isbeneficiary</label>
                        <input type="text" class="form-control" id="Isbeneficiary" name="Isbeneficiary" value="{{$update->Isbeneficiary }}">
                    </div>
                    <div class="mb-3">
                        <label for="Isaccount" class="form-label">Isaccount</label>
                        <input type="text" class="form-control" id="Isaccount" name="Isaccount" value="{{$update->Isaccount }}">
                    </div>
                    <div class="mb-3">
                        <label for="Isplan" class="form-label">Isplan</label>
                        <input type="text" class="form-control" id="Isplan" name="Isplan" value="{{$update->Isplan }}">
                    </div>
                    <div class="mb-3">
                        <label for="Istransfer_details" class="form-label">Istransfer_details</label>
                        <input type="text" class="form-control" id="Istransfer_details" name="Istransfer_details" value="{{$update->Istransfer_details }}">
                    </div>
                    <div class="mb-3">
                        <label for="Isxfer_consent" class="form-label">Isxfer_consent</label>
                        <input type="text" class="form-control" id="Isxfer_consent" name="Isxfer_consent" value="{{$update->Isxfer_consent }}">
                    </div>
                    <div class="mb-3">
                        <label for="rebuttals" class="form-label">rebuttals</label>
                        <input type="text" class="form-control" id="rebuttals" name="rebuttals" value="{{$update->rebuttals }}">
                    </div>
                    <div class="mb-3">
                        <label for="Qacomments" class="form-label">Qacomments</label>
                        <input type="text" class="form-control" id="Qacomments" name="Qacomments" value="{{$update->Qacomments }}">
                    </div>
                    <div class="mb-3">
                        <label for="QapersonId" class="form-label">QapersonId</label>
                        <select class="form-control" id="QapersonId" name="QapersonId">
                            @foreach($Qaperson as $person)
                            <option value="{{ $person->id }}" {{ $update->QapersonId == $person->id ? 'selected' : '' }}>{{ $person->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="use_of_rebuttals" class="form-label">use_of_rebuttals</label>
                        <input type="text" class="form-control" id="use_of_rebuttals" name="use_of_rebuttals" value="{{$update->use_of_rebuttals }}">
                    </div>
                    <div class="mb-3">
                        <label for="no_of_refusals" class="form-label">no_of_refusals</label>
                        <input type="text" class="form-control" id="no_of_refusals" name="no_of_refusals" value="{{$update->no_of_refusals }}">
                    </div>


                    <div class="mb-3">
                        <label for="center">Center Name</label>
                        <select class="form-control" id="center" name="center">
                            <option value="">Select Center Name</option>
                            <option value="Sellerz" {{ $update->center == 'Sellerz' ? 'selected' : '' }}>Sellerz</option>
                            <option value="Jsons" {{ $update->center == 'Jsons' ? 'selected' : '' }}>Jsons</option>
                        </select>
                    </div>



                    <div class="mb-3">
                        <label for="QAstatus" class="form-label">Qa Status</label>
                        <select class="form-select" id="QAstatus" name="QAstatus">
                            <option value="pending" {{$update->QAstatus == 'pending' ? 'selected' : ''}}>Pending</option>
                            <option value="approved" {{$update->QAstatus == 'approved' ? 'selected' : ''}}>Approved
                            </option>
                            <option value="rejected" {{$update->QAstatus == 'rejected' ? 'selected' : ''}}>Rejected
                            </option>

                        </select>
                    </div>


                    <button type="submit" class="btn btn-primary">Update

                    </button>


                </form>
            </div>
        </div>

    </div>
    @endsection




</body>

</html>
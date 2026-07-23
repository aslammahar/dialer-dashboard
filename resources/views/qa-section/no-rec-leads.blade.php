@extends('layouts.admin')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('HRM') }}</li>
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-/3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-success">
                                    <i class="ti ti-cast"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Assign Leads') }} </small>
                                    <li><a href="avatar-q-a-leads" class="btn btn-sm btn-success">Assign Leads</a></li>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <h3 class="m-0"> </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>








    </div>

    <div class="container">
        <h1>No Recording Leads</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Lead Id</th>
                    <th>Recording</th>
                    <th>QAstatus</th>
                    <th>Enter Recording Link</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leads as $lead)
                    <tr>
                        <td>{{ $lead->id }}</td>
                        <td>{{ $lead->lead_id }}</td>
                        <td>{{ $lead->recording }}</td>
                        <td>{{ $lead->QAstatus }}</td>
                        <td>
                            <form method="POST" action="{{ route('no-recording-update') }}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $lead->id }}">
                                <input type="hidden" name="lead_id" value="{{ $lead->lead_id }}">
                               
                                <input type="text" name="recording" id="recording" value="" oninput="updateRecordingLink()">  

                                <!-- Hidden fields to store extracted values -->
                                <input type="hidden" name="recording_filename" id="recording_filename" value="">
                                <input type="hidden" name="recording_link" id="recording_link" class="form-control" value="">

                                <script>
                                function updateRecordingLink() {
                                    let recordingInput = document.getElementById('recording').value;
                                    let filenameInput = document.getElementById('recording_filename');
                                    let hiddenInput = document.getElementById('recording_link');

                                    if (recordingInput) {
                                        // Extract filename by finding the last '/'
                                        let lastSlashIndex = recordingInput.lastIndexOf('/');
                                        if (lastSlashIndex !== -1) {
                                            let recording_filename = recordingInput.substring(lastSlashIndex + 1).replace('-all.mp3', ''); // Remove "-all.mp3"
                                            
                                            filenameInput.value = recording_filename;
                                            // Use the original recording input as the recording_link (no hardcoded URL)
                                            hiddenInput.value = recordingInput;
                                        } else {
                                            filenameInput.value = "";
                                            hiddenInput.value = "";
                                        }
                                    } else {
                                        filenameInput.value = "";
                                        hiddenInput.value = "";
                                    }
                                }
                                </script>


                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
@endsection('content')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Policy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body>

@extends('layouts.admin')

@section('page-title')
{{ __('Update Policy') }}
@endsection

@section('content')

<div class="container mt-5">
    <div class="row">
        <div class="">
            <form action="" method="POST">
                @csrf
                <!-- Other form fields here -->
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="customer_full_name" class="form-label">Customer Full Name</label>
                        <input type="text" class="form-control" id="customer_full_name" name="customer_full_name" value="{{$update->customer_full_name }}" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="number" class="form-control" id="phone_number" name="phone_number" value="{{$update->phone_number }}" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="alternate_phone_number">Alternate Phone Number</label>
                        <input type="number" class="form-control" id="alternate_phone_number" name="alternate_phone_number" value="{{$update->alternate_phone_number }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="cx_email">CX Email</label>
                        <input type="email" class="form-control" id="cx_email" name="cx_email" value="{{$update->cx_email }}" readonly>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="address">Address</label>
                        <input class="form-control" id="address" name="address" value="{{ $update->address }}" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="{{$update->city }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="state">State</label>
                        <input type="text" class="form-control" id="state" name="state" value="{{ $update->state }}" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="zip_code" class="form-label">Zip Code</label>
                        <input type="number" class="form-control" id="zip_code" name="zip_code" value="{{$update->zip_code }}" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="status">Status</label>
                        <input type="text" class="form-control" id="status" name="status" value="{{$update->status }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="remarks">Client Comment</label>
                        <textarea class="form-control" id="clientscomment" name="clients_comment" rows="3" readonly>{{ $update->clients_comment }}</textarea>
                    </div>
                </div>

                <!-- sales and agent record -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="list_id_1" class="form-label">List ID 1</label>
                        <input type="text" class="form-control" id="list_id_1" name="list_id_1" value="{{ $update->list_id_1 ?? '' }}" placeholder="Enter List ID 1">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="list_id_2" class="form-label">List ID 2</label>
                        <input type="text" class="form-control" id="list_id_2" name="list_id_2" value="{{ $update->list_id_2 ?? '' }}" placeholder="Enter List ID 2">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="dialername" class="form-label">Dialer ID</label>
                        <select class="form-control" id="dialername" name="dialername">
                            <option value="">Select Dialer</option>
                            @foreach($dialers as $dialer)
                                <option value="{{ $dialer->id }}" {{ ($update->dialername == $dialer->id) ? 'selected' : '' }}>
                                    {{ $dialer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="agentname" class="form-label">Agent Name</label>
                        <select class="form-control" id="agentname" name="agentname">
                            <option value="">Select Agent</option>
                            @if($update->agentname && !$agents->contains('name', $update->agentname))
                                <option value="{{ $update->agentname }}" selected>{{ $update->agentname }}</option>
                            @endif
                            @foreach($agents as $agent)
                                <option value="{{ $agent->name }}" {{ ($update->agentname == $agent->name) ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Lead ID, Closer, and Junior Closer details -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="lead_id" class="form-label">Lead ID</label>
                        <input type="text" class="form-control" id="lead_id" name="lead_id" value="{{ $update->lead_id ?? '' }}" placeholder="Enter Lead ID">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="closer_id">Closer Name</label>
                        <select class="form-control" id="closer_id" name="closername" required>
                            <option value="">Select Closer</option>
                            @if($update->closername)
                                <option value="{{$update->closername }}" selected>{{$update->closername }}</option>
                            @endif
                            @foreach($closers as $closer)
                                @if($closer->name != $update->closername)
                                    <option value="{{ $closer->name }}">{{ $closer->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="juniorcloser2" class="form-label">Junior Closer 2</label>
                        <input type="text" class="form-control" id="juniorcloser2" name="juniorcloser2" value="{{ $update->juniorcloser2 ?? '' }}" placeholder="Enter Junior Closer 2">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="teamname" class="form-label">Team Name</label>
                        <select class="form-control" id="teamname" name="teamname">
                            <option value="">Select Team</option>
                            @if($update->teamname && !$teams->contains('name', $update->teamname))
                                <option value="{{ $update->teamname }}" selected>{{ $update->teamname }}</option>
                            @endif
                            @foreach($teams as $team)
                                <option value="{{ $team->name }}" {{ ($update->teamname == $team->name) ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                 <div class="col-md-4 mb-3">
                            <label for="dialer_name_new" class="form-label fw-bold text-indigo">☎️ Dialer Name</label>
                            <select class="form-control border-indigo" id="dialer_name_new" name="dialer_name_new">
                                <option value="">Select Dialer</option>
                                <option value="dialer1" {{ ($update->dialer_name_new ?? '') == 'dialer1' ? 'selected' : '' }}>📱 Dialer 1</option>
                                <option value="dialer2" {{ ($update->dialer_name_new ?? '') == 'dialer2' ? 'selected' : '' }}>📱 Dialer 2</option>
                                <option value="dialer3" {{ ($update->dialer_name_new ?? '') == 'dialer3' ? 'selected' : '' }}>📱 Dialer 3</option>
                                <option value="dialer4" {{ ($update->dialer_name_new ?? '') == 'dialer4' ? 'selected' : '' }}>📱 Dialer 4</option>
                                <option value="dialer5" {{ ($update->dialer_name_new ?? '') == 'dialer5' ? 'selected' : '' }}>📱 Dialer 5</option>
                            </select>
                            <small class="text-muted">Select dialer system used</small>
                        </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('closer.salesagentshow') }}" class="btn btn-secondary">Cancel</a>
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

    // Enhanced form functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Add form validation
        const form = document.querySelector('form');
        const requiredFields = form.querySelectorAll('[required]');
        
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });

        // Remove validation styling on input
        requiredFields.forEach(field => {
            field.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                }
            });
        });
    });
</script>

<style>
    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .form-control.is-valid {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
    }
    
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        padding: 10px 30px;
    }
    
    .btn-secondary {
        padding: 10px 30px;
    }
    
    .container {
        max-width: 1200px;
    }
    
    select.form-control {
        cursor: pointer;
    }
    
    select.form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>

</body>
</html>
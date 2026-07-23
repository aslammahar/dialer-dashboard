@extends('layouts.admin')

@section('content')
<div class="container mt-5">
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <h1 class="text-center mb-4">{{ __('Create Monitoring Record') }}</h1>
    
    <form action="{{ route('monitoring.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf

        <div class="mb-3">
            <label for="employee_id" class="form-label">Employee:</label>
            <select name="employee_id" id="employee_id" class="form-select" required>
                <option value="">Select an Employee</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="monitor_from" class="form-label">Monitor Time (From):</label>
                <input type="time" name="monitor_from" id="monitor_from" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="monitor_to" class="form-label">Monitor Time (To):</label>
                <input type="time" name="monitor_to" id="monitor_to" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="monitor_date" class="form-label">Monitor Date:</label>
            <input type="date" name="monitor_date" id="monitor_date" class="form-control" required>
        </div>

        <!-- Textareas -->
        <div class="mb-3">
            <label for="call_rapport_building" class="form-label">Call Rapport Building:</label>
            <textarea name="call_rapport_building" id="call_rapport_building" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="qualifying_part" class="form-label">Qualifying Part:</label>
            <textarea name="qualifying_part" id="qualifying_part" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="agents_efforts" class="form-label">Agents Efforts:</label>
            <textarea name="agents_efforts" id="agents_efforts" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="rebuttals" class="form-label">Rebuttals:</label>
            <textarea name="rebuttals" id="rebuttals" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="overall_call_details" class="form-label">Overall Call Details:</label>
            <textarea name="overall_call_details" id="overall_call_details" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="vocabulary" class="form-label">Vocabulary:</label>
            <textarea name="vocabulary" id="vocabulary" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="customer_response" class="form-label">Customer Response:</label>
            <textarea name="customer_response" id="customer_response" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="suggestions" class="form-label">Suggestions:</label>
            <textarea name="suggestions" id="suggestions" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="score" class="form-label">Score:</label>
            <select name="score" id="score" class="form-select" required>
                <option value="">Select a Score</option>
                <option value="Good">Good</option>
                <option value="Avg">Average</option>
                <option value="Bad">Bad</option>
                <option value="Worst">Worst</option>
            </select>
        </div>
<!-- Textarea for Greeting -->
<div class="mb-3">
    <label for="greeting" class="form-label">Greeting:</label>
    <textarea name="greeting" id="greeting" class="form-control" rows="3"></textarea>
</div>

<!-- Textarea for Energy -->
<div class="mb-3">
    <label for="energy" class="form-label">Energy:</label>
    <textarea name="energy" id="energy" class="form-control" rows="3"></textarea>
</div>

<!-- Textarea for Q/A -->
<div class="mb-3">
    <label for="qa" class="form-label">Q/A:</label>
    <textarea name="qa" id="qa" class="form-control" rows="3"></textarea>
</div>

        <!-- Additional Dropdowns -->
        @php
            $dropdownFields = ['focus', 'positivity', 'confidence', 'motivation', 'energy_level', 'smile'];
        @endphp

        @foreach ($dropdownFields as $field)
            <div class="mb-3">
                <label for="{{ $field }}" class="form-label">{{ ucfirst(str_replace('_', ' ', $field)) }}:</label>
                <select name="{{ $field }}" id="{{ $field }}" class="form-select" required>
                    <option value="">Select a Value</option>
                    <option value="Excellent">Excellent</option>
                    <option value="Satisfied">Satisfied</option>
                    <option value="Not Satisfied">Not Satisfied</option>
                </select>
            </div>
        @endforeach

        <!-- Notify To -->
        <div class="mb-3">
            <label for="notify_to" class="form-label">Notify To:</label>
            <select name="notify_to[]" id="notify_to" class="form-select stretchable-select" multiple>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit</button>
    </form>
</div>

<style>
    .stretchable-select {
        height: 10em;
        min-height: 50px;
        max-height: 300px;
        overflow-y: auto;
        resize: vertical;
        border: 1px solid #ccc;
        padding: 5px;
        font-size: 14px;
        width: 100%;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var multipleChoices = new Choices('#notify_to', {
            removeItemButton: true,
            searchEnabled: true,
            placeholder: true,
            placeholderValue: 'Select employees to notify',
            searchPlaceholderValue: 'Search employees'
        });
    });
</script>
@endsection

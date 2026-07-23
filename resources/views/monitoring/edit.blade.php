@extends('layouts.admin')



@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">{{ __('Edit Monitoring Record') }}</h1>
    
    <form action="{{ route('monitoring.update', $monitoring->id) }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="employee_id" class="form-label">Employee:</label>
            <select name="employee_id" id="employee_id" class="form-select" required>
                <option value="">Select an Employee</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $monitoring->employee_id == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="monitor_from" class="form-label">Monitor Time (From):</label>
                <input type="time" name="monitor_from" id="monitor_from" class="form-control" value="{{ $monitoring->monitor_from }}" required>
            </div>
            <div class="col-md-6">
                <label for="monitor_to" class="form-label">Monitor Time (To):</label>
                <input type="time" name="monitor_to" id="monitor_to" class="form-control" value="{{ $monitoring->monitor_to }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="monitor_date" class="form-label">Monitor Date:</label>
            <input type="date" name="monitor_date" id="monitor_date" class="form-control" value="{{ $monitoring->monitor_date }}" required>
        </div>

        <div class="mb-3">
            <label for="call_rapport_building" class="form-label">Call Rapport Building:</label>
            <textarea name="call_rapport_building" id="call_rapport_building" class="form-control" rows="3">{{ $monitoring->call_rapport_building }}</textarea>
        </div>

        <div class="mb-3">
            <label for="qualifying_part" class="form-label">Qualifying Part:</label>
            <textarea name="qualifying_part" id="qualifying_part" class="form-control" rows="3">{{ $monitoring->qualifying_part }}</textarea>
        </div>

        <div class="mb-3">
            <label for="agents_efforts" class="form-label">Agents Efforts:</label>
            <textarea name="agents_efforts" id="agents_efforts" class="form-control" rows="3">{{ $monitoring->agents_efforts }}</textarea>
        </div>

        <div class="mb-3">
            <label for="rebuttals" class="form-label">Rebuttals:</label>
            <textarea name="rebuttals" id="rebuttals" class="form-control" rows="3">{{ $monitoring->rebuttals }}</textarea>
        </div>

        <div class="mb-3">
            <label for="overall_call_details" class="form-label">Overall Call Details:</label>
            <textarea name="overall_call_details" id="overall_call_details" class="form-control" rows="3">{{ $monitoring->overall_call_details }}</textarea>
        </div>

        <div class="mb-3">
            <label for="vocabulary" class="form-label">Vocabulary:</label>
            <textarea name="vocabulary" id="vocabulary" class="form-control" rows="3">{{ $monitoring->vocabulary }}</textarea>
        </div>

        <div class="mb-3">
            <label for="customer_response" class="form-label">Customer Response:</label>
            <textarea name="customer_response" id="customer_response" class="form-control" rows="3">{{ $monitoring->customer_response }}</textarea>
        </div>

        <div class="mb-3">
            <label for="suggestions" class="form-label">Suggestions:</label>
            <textarea name="suggestions" id="suggestions" class="form-control" rows="3">{{ $monitoring->suggestions }}</textarea>
        </div>

        <div class="mb-3">
            <label for="score" class="form-label">Score:</label>
            <select name="score" id="score" class="form-select" required>
                <option value="">Select a Score</option>
                <option value="Good" {{ $monitoring->score == 'Good' ? 'selected' : '' }}>Good</option>
                <option value="Avg" {{ $monitoring->score == 'Avg' ? 'selected' : '' }}>Average</option>
                <option value="Bad" {{ $monitoring->score == 'Bad' ? 'selected' : '' }}>Bad</option>
                <option value="Worst" {{ $monitoring->score == 'Worst' ? 'selected' : '' }}>Worst</option>
            </select>
        </div>
<!-- Textarea for Greeting -->
<div class="mb-3">
    <label for="greeting" class="form-label">Greeting:</label>
    <textarea name="greeting" id="greeting" class="form-control" rows="3">{{ $monitoring->greeting }}</textarea>
</div>

<!-- Textarea for Energy -->
<div class="mb-3">
    <label for="energy" class="form-label">Energy:</label>
    <textarea name="energy" id="energy" class="form-control" rows="3">{{ $monitoring->energy }}</textarea>
</div>

<!-- Textarea for Q/A -->
<div class="mb-3">
    <label for="qa" class="form-label">Q/A:</label>
    <textarea name="qa" id="qa" class="form-control" rows="3">{{ $monitoring->qa }}</textarea>
</div>

        <!-- Additional Dropdowns -->
        @php
            $dropdownFields = ['focus', 'positivity', 'confidence', 'motivation', 'energy_level', 'smile'];
        @endphp

        @foreach ($dropdownFields as $field)
            <div class="mb-3">
                <label for="{{ $field }}" class="form-label">{{ ucfirst(str_replace('_', ' ', $field)) }}:</label>
                <select name="{{ $field }}" id="{{ $field }}" class="form-select" >
                    <option value="">Select a Value</option>
                    <option value="Excellent">Excellent</option>
                    <option value="Satisfied">Satisfied</option>
                    <option value="Not Satisfied">Not Satisfied</option>
                </select>
            </div>
        @endforeach

        <!-- Notify To -->
       
        <button type="submit" class="btn btn-primary w-100">Update</button>
    </form>
</div>
@endsection

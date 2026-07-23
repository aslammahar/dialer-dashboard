@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">{{ __('Create Avatar Monitoring Record') }}</h1>
    
    <form action="{{ route('avatar_monitoring.store') }}" method="POST" class="card p-4 shadow-sm">
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
                <input type="time" name="monitor_from" id="monitor_from" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="monitor_to" class="form-label">Monitor Time (To):</label>
                <input type="time" name="monitor_to" id="monitor_to" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label for="monitor_date" class="form-label">Monitor Date:</label>
            <input type="date" name="monitor_date" id="monitor_date" class="form-control" required>
        </div>

        @foreach (['greeting', 'response_on_answering_machine', 'response_time', 'customer_response', 'leave_3_way', 'questions', 'dispositions', 'comments_suggestions'] as $field)
            <div class="mb-3">
                <label for="{{ $field }}" class="form-label">{{ ucfirst(str_replace('_', ' ', $field)) }}:</label>
                <textarea name="{{ $field }}" id="{{ $field }}" class="form-control" rows="3"></textarea>
            </div>
        @endforeach

        <div class="mb-3">
            <label for="disposition_records" class="form-label">Disposition Records:</label>
            <div id="disposition-container">
                <div class="row mb-2">
                    <div class="col-md-3">
                        <input type="text" name="disposition_records[0][serial_number]" placeholder="Serial #" class="form-control">
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="disposition_records[0][lead_id]" placeholder="Lead ID" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <select name="disposition_records[0][type]" class="form-select">
                            @foreach (['A', 'B', 'CALLBK', 'DAIR', 'DEC', 'DNC', 'DNQ', 'DT', 'LB', 'N', 'NC', 'NI', 'OA', 'SALE', 'UA', 'XFER'] as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <button type="button" id="add-disposition" class="btn btn-secondary btn-sm">Add More</button>
        </div>

        <!-- Notify To Dropdown (Multi-Select) -->
      

        <div class="form-group" id="user_div">
    <label for="notify_to" class="form-label">Notify To:</label>
    <select class="form-control stretchable-select" id="notify_to" name="notify_to[]" multiple>
        @foreach ($employees as $employee)
            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
        @endforeach
    </select>
</div>


        <div class="mb-3">
            <label for="score" class="form-label">Score:</label>
            <select name="score" id="score" class="form-select">
                <option value="">Select a Score</option>
                <option value="Good">Good</option>
                <option value="Avg">Average</option>
                <option value="Bad">Bad</option>
                <option value="Worst">Worst</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit</button>
    </form>
</div>

<script>
    // Add More Disposition Fields
    document.getElementById('add-disposition').addEventListener('click', function() {
        const container = document.getElementById('disposition-container');
        const count = container.children.length;
        const row = `
            <div class="row mb-2">
                <div class="col-md-3">
                    <input type="text" name="disposition_records[${count}][serial_number]" placeholder="Serial #" class="form-control">
                </div>
                <div class="col-md-5">
                    <input type="text" name="disposition_records[${count}][lead_id]" placeholder="Lead ID" class="form-control">
                </div>
                <div class="col-md-4">
                    <select name="disposition_records[${count}][type]" class="form-select">
                        @foreach (['A', 'B', 'CALLBK', 'DAIR', 'DEC', 'DNC', 'DNQ', 'DT', 'LB', 'N', 'NC', 'NI', 'OA', 'SALE', 'UA', 'XFER'] as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', row);
    });


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

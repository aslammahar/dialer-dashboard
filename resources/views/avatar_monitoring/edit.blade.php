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

    <h1 class="text-center mb-4">{{ __('Edit Avatar Monitoring Record') }}</h1>
    
    <form action="{{ route('avatar_monitoring.update', $avatarMonitoring->id) }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="employee_id" class="form-label">Employee:</label>
            <select name="employee_id" id="employee_id" class="form-select" required>
                <option value="">Select an Employee</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" 
                        {{ $avatarMonitoring->employee_id == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="monitor_from" class="form-label">Monitor Time (From):</label>
                <input type="time" name="monitor_from" id="monitor_from" class="form-control" 
                       value="{{ $avatarMonitoring->monitor_from }}" required>
            </div>
            <div class="col-md-6">
                <label for="monitor_to" class="form-label">Monitor Time (To):</label>
                <input type="time" name="monitor_to" id="monitor_to" class="form-control" 
                       value="{{ $avatarMonitoring->monitor_to }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="monitor_date" class="form-label">Monitor Date:</label>
            <input type="date" name="monitor_date" id="monitor_date" class="form-control" 
                   value="{{ $avatarMonitoring->monitor_date }}" required>
        </div>

        <div class="mb-3">
            <label for="greeting" class="form-label">Greeting:</label>
            <textarea name="greeting" id="greeting" class="form-control" rows="3">{{ $avatarMonitoring->greeting }}</textarea>
        </div>

        <div class="mb-3">
            <label for="response_on_answering_machine" class="form-label">Response on Answering Machine:</label>
            <textarea name="response_on_answering_machine" id="response_on_answering_machine" class="form-control" rows="3">
                {{ $avatarMonitoring->response_on_answering_machine }}
            </textarea>
        </div>

        <div class="mb-3">
            <label for="response_time" class="form-label">Response Time:</label>
            <textarea name="response_time" id="response_time" class="form-control" rows="3">
                {{ $avatarMonitoring->response_time }}
            </textarea>
        </div>

        <div class="mb-3">
            <label for="customer_response" class="form-label">Customer Response:</label>
            <textarea name="customer_response" id="customer_response" class="form-control" rows="3">
                {{ $avatarMonitoring->customer_response }}
            </textarea>
        </div>

        <div class="mb-3">
            <label for="leave_3_way" class="form-label">Leave 3 Way:</label>
            <textarea name="leave_3_way" id="leave_3_way" class="form-control" rows="3">
                {{ $avatarMonitoring->leave_3_way }}
            </textarea>
        </div>

        <div class="mb-3">
            <label for="questions" class="form-label">Questions:</label>
            <textarea name="questions" id="questions" class="form-control" rows="3">
                {{ $avatarMonitoring->questions }}
            </textarea>
        </div>

        <div class="mb-3">
            <label for="dispositions" class="form-label">Dispositions:</label>
            <textarea name="dispositions" id="dispositions" class="form-control" rows="3">
                {{ $avatarMonitoring->dispositions }}
            </textarea>
        </div>

        <div class="mb-3">
            <label for="comments" class="form-label">Comments & Suggestions:</label>
            <textarea name="comments" id="comments" class="form-control" rows="3">
                {{ $avatarMonitoring->comments }}
            </textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Record</button>
    </form>
</div>
@endsection

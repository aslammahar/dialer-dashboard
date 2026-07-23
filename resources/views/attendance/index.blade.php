@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Attendance List') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Attendance') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('attendance.index') }}">
                            <div class="row">
                                <!-- Employee Dropdown -->
                                <div class="col-md-4">
                                    <label>{{ __('Select Employee') }}</label>
                                    <select name="employee" class="form-control">
                                        @foreach ($employees as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ request('employee') == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Date Range Selection -->
                                <div class="col-md-3">
                                    <label>{{ __('From Date') }}</label>
                                    <input type="date" name="from" class="form-control"
                                        value="{{ request('from') }}">
                                </div>

                                <div class="col-md-3">
                                    <label>{{ __('To Date') }}</label>
                                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                                </div>

                                <div class="col-md-2" style="margin-top: 30px;">
                                    <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                                </div>
                            </div>
                        </form>

                        @if ($attendanceEmployee->isEmpty())
                            <div class="alert alert-info mt-4">
                                {{ __('No attendance records found.') }}
                            </div>
                        @else
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>{{ __('Employee') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Clock In') }}</th>
                                        <th>{{ __('Clock Out') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendanceEmployee as $attendance)
                                        <tr>
                                            <td>{{ $attendance->employee ? $attendance->employee->name : '' }}</td>
                                            <td>{{ $attendance->created_at }}</td>
                                            <td>{{ $attendance->status }}</td>
                                            <td>{{ $attendance->clock_in }}</td>
                                            <td>{{ $attendance->clock_out }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
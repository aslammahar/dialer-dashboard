@extends('layouts.admin')

@section('page-title')
    {{ __('Employee Attendances') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Employee Attendance') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">

                        <form method="GET" action="{{ route('attendances.index') }}">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label>{{ __('From Date') }}</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                                </div>
                                <div class="col-md-4">
                                    <label>{{ __('To Date') }}</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary mt-3 w-100">{{ __('Filter') }}</button>
                                </div>
                            </div>
                        </form>

                        @if (session('info'))
                            <div class="alert alert-info mt-4">
                                {{ session('info') }}
                            </div>
                        @endif

                        @if (empty($attendanceData))
                            <div class="alert alert-info mt-4">
                                {{ __('No attendance records found between') }} {{ $startDate }} {{ __('and') }} {{ $endDate }}.
                            </div>
                        @else
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered table-striped datatable text-center">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>{{ __('Employee Name') }}</th>
                                            @foreach ($dates as $date)
                                                <th>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attendanceData as $employee => $records)
                                            <tr>
                                                <td class="fw-bold">{{ $employee }}</td>
                                                @foreach ($dates as $date)
                                                    @php
                                                        $record = $records[$date] ?? null;
                                                    @endphp
                                                    @if ($record)
                                                        <td>
                                                            <div><strong>{{ $record['check_in_out'] }}</strong></div>
                                                            <div class="text-success small">Late: {{ $record['late_by'] }}</div>
                                                            <div class="text-danger small">Early: {{ $record['early_by'] }}</div>
                                                        </td>
                                                    @else
                                                        <td><span class="text-muted">N/A</span></td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
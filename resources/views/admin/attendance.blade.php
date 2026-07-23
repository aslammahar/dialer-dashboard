@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Attendance List') }}
@endsection

@push('script-page')
    <script>
        $(document).ready(function () {
            $('#filter-from, #filter-to, #filter-user').on('change', function () {
                var fromDate = $('#filter-from').val();
                var toDate = $('#filter-to').val();
                var selectedUser = $('#filter-user').val();
                window.location.href = "?from=" + fromDate + "&to=" + toDate + "&user=" + selectedUser;
            });
        });
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Attendance') }}</li>
@endsection

@section('content')
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="filter-from">{{ __('From Date') }}</label>
            <input type="date" id="filter-from" class="form-control" value="{{ $fromDate }}">
        </div>
        <div class="col-md-3">
            <label for="filter-to">{{ __('To Date') }}</label>
            <input type="date" id="filter-to" class="form-control" value="{{ $toDate }}">
        </div>
    
   
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Employee Name') }}</th>
                                    @foreach ($dates as $date)
                                        <th>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendanceEmployee->groupBy('employee_id') as $employeeId => $attendanceRecords)
                                    @php
                                        $employeeName = $employees[$employeeId] ?? 'Unknown';
                                    @endphp
                                    <tr>
                                        <td>{{ $employeeName }}</td>
                                        @foreach ($dates as $date)
                                            @php
                                                $attendance = $attendanceRecords->firstWhere('attendance_date', $date);
                                                $timeIn = $attendance->attendance_time ?? 'N/A';
                                            @endphp
                                            <td>{{ $timeIn }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            $('.table-responsive').responsiveTable({
                addDisplayAllBtn: 'btn btn-secondary'
            });
        });
    </script>
@endsection

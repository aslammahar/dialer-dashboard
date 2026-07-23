@extends('layouts.admin')

@section('page-title')
    {{ __('Recruiter Head Dashboard') }}
@endsection

@push('script-page')
<script>
// Team Daily Activity Chart
(function () {
    var options = {
        chart: { height: 230, type: 'area', toolbar: { show: false } },
        dataLabels: { enabled: false },
        stroke: { width: 2, curve: 'smooth' },
        series: [{ name: 'Total Calls', data: {!! json_encode($chartCounts) !!} }],
        xaxis: { categories: {!! json_encode($chartDates) !!}, labels: { rotate: -45, style: { fontSize: '10px' } } },
        colors: ['#3ec9d6'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
        grid: { strokeDashArray: 4 },
        tooltip: { y: { formatter: function(val) { return val + ' calls'; } } }
    };
    new ApexCharts(document.querySelector("#team-activity-chart"), options).render();
})();

// Recruiter Performance Bar Chart
(function () {
    var recruiterNames = {!! json_encode(array_column($recruiterData, 'recruiter')) !!};
    var recruiterTotals = {!! json_encode(array_column($recruiterData, 'total')) !!};
    var recruiterHired = {!! json_encode(array_map(fn($r) => $r['statuses']['Hired'], $recruiterData)) !!};

    var options = {
        chart: { height: 280, type: 'bar', toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 5, columnWidth: '60%', dataLabels: { position: 'top' } } },
        colors: ['#7367f0', '#28a745'],
        dataLabels: { enabled: true, style: { fontSize: '11px' } },
        series: [
            { name: 'Total Calls', data: recruiterTotals },
            { name: 'Hired', data: recruiterHired }
        ],
        xaxis: { categories: recruiterNames, labels: { style: { fontSize: '12px' } } },
        legend: { position: 'top' },
        tooltip: { shared: true, intersect: false }
    };
    new ApexCharts(document.querySelector("#recruiter-performance-chart"), options).render();
})();

// Status Stacked Chart
(function () {
    var recruiterNames = {!! json_encode(array_column($recruiterData, 'recruiter')) !!};
    var options = {
        chart: { height: 260, type: 'bar', stacked: true, toolbar: { show: false } },
        plotOptions: { bar: { horizontal: true, borderRadius: 3 } },
        colors: ['#6c757d', '#dc3545', '#ffc107', '#17a2b8', '#28a745', '#e83e8c', '#fd7e14', '#6f42c1'],
        series: [
            { name: 'No Answer', data: {!! json_encode(array_map(fn($r) => $r['statuses']['No Answer'], $recruiterData)) !!} },
            { name: 'Not Interested', data: {!! json_encode(array_map(fn($r) => $r['statuses']['Not Interested'], $recruiterData)) !!} },
            { name: 'Call Back', data: {!! json_encode(array_map(fn($r) => $r['statuses']['Call Back'], $recruiterData)) !!} },
            { name: 'Interested', data: {!! json_encode(array_map(fn($r) => $r['statuses']['Interested'], $recruiterData)) !!} },
            { name: 'Hired', data: {!! json_encode(array_map(fn($r) => $r['statuses']['Hired'], $recruiterData)) !!} },
            { name: 'Rejected', data: {!! json_encode(array_map(fn($r) => $r['statuses']['Rejected'], $recruiterData)) !!} },
            { name: 'Pending', data: {!! json_encode(array_map(fn($r) => $r['statuses']['Pending'], $recruiterData)) !!} },
            { name: 'Left', data: {!! json_encode(array_map(fn($r) => $r['statuses']['Left'], $recruiterData)) !!} },
        ],
        xaxis: { categories: recruiterNames },
        legend: { position: 'bottom', fontSize: '12px' },
        fill: { opacity: 1 },
        tooltip: { y: { formatter: function(val) { return val; } } }
    };
    new ApexCharts(document.querySelector("#stacked-status-chart"), options).render();
})();
</script>
@endpush

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item active">{{ __('Recruiter Head Dashboard') }}</li>
@endsection

@section('content')
<div class="row">

    {{-- Page Header + Filter --}}
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('recruiter.head.dashboard') }}" class="row align-items-end g-2">
                    <div class="col-auto">
                        <h5 class="mb-0 fw-bold">Recruiter Head Dashboard</h5>
                        <small class="text-muted">Team Recruitment Overview</small>
                    </div>
                    <div class="col-md-2 ms-auto">
                        <label class="form-label small mb-1">Start Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">End Date</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ti ti-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('recruiter.head.dashboard', ['filter' => 'weekly']) }}" class="btn btn-outline-secondary btn-sm">Weekly</a>
                        <a href="{{ route('recruiter.head.dashboard', ['filter' => 'monthly']) }}" class="btn btn-outline-secondary btn-sm">Monthly</a>
                        <a href="{{ route('recruitments.reports') }}" class="btn btn-outline-info btn-sm">
                            <i class="ti ti-file-analytics me-1"></i> Full Reports
                        </a>
                        <a href="{{ route('recruitments.exportExcel', request()->query()) }}" class="btn btn-outline-success btn-sm">
                            <i class="ti ti-file-spreadsheet me-1"></i> Export
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Team Stat Cards --}}
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Team Total Calls</p>
                        <h3 class="mb-0 fw-bold">{{ $teamTotal }}</h3>
                        <small class="text-muted">{{ $startDate }} → {{ $endDate }}</small>
                    </div>
                    <div class="theme-avtar bg-primary">
                        <i class="ti ti-phone-call f-20"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Total Hired</p>
                        <h3 class="mb-0 fw-bold text-success">{{ $teamHired }}</h3>
                        <small class="text-success">{{ $teamConversion }}% conversion rate</small>
                    </div>
                    <div class="theme-avtar bg-success">
                        <i class="ti ti-user-check f-20"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Total Rejected</p>
                        <h3 class="mb-0 fw-bold text-danger">{{ $teamRejected }}</h3>
                        <small class="text-muted">{{ $teamTotal > 0 ? round(($teamRejected / $teamTotal) * 100, 1) : 0 }}% of total</small>
                    </div>
                    <div class="theme-avtar bg-danger">
                        <i class="ti ti-user-x f-20"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Active Recruiters</p>
                        <h3 class="mb-0 fw-bold text-info">{{ count($recruiterData) }}</h3>
                        <small class="text-muted">{{ $hrUsers->count() }} HR users total</small>
                    </div>
                    <div class="theme-avtar bg-info">
                        <i class="ti ti-users f-20"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Team Activity Chart + Stacked Chart --}}
    <div class="col-lg-7 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Team Daily Activity</h5>
            </div>
            <div class="card-body">
                <div id="team-activity-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-5 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recruiter Performance (Calls vs Hired)</h5>
            </div>
            <div class="card-body">
                <div id="recruiter-performance-chart"></div>
            </div>
        </div>
    </div>

    {{-- Status Stacked Chart --}}
    @if(count($recruiterData) > 0)
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Status Breakdown by Recruiter</h5>
            </div>
            <div class="card-body">
                <div id="stacked-status-chart"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Recruiter Performance Table --}}
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Recruiter Performance Table</h5>
                <span class="badge bg-primary">{{ $startDate }} to {{ $endDate }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Recruiter</th>
                                <th>Total</th>
                                @foreach($statuses as $status)
                                    <th class="text-center">{{ $status }}</th>
                                @endforeach
                                <th class="text-center">Conversion %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recruiterData as $i => $r)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $r['recruiter'] }}</td>
                                <td><strong>{{ $r['total'] }}</strong></td>
                                @php
                                    $cellColors = [
                                        'No Answer' => '', 'Not Interested' => 'table-danger',
                                        'Call Back' => 'table-warning', 'Interested' => 'table-info',
                                        'Hired' => 'table-success', 'Rejected' => 'table-danger',
                                        'Left' => '', 'Pending' => 'table-warning',
                                    ];
                                @endphp
                                @foreach($statuses as $status)
                                    <td class="text-center {{ $r['statuses'][$status] > 0 ? ($cellColors[$status] ?? '') : '' }}">
                                        {{ $r['statuses'][$status] }}
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    @php $pct = $r['conversion']; @endphp
                                    <span class="badge bg-{{ $pct >= 20 ? 'success' : ($pct >= 10 ? 'warning' : 'danger') }}">
                                        {{ $pct }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 4 + count($statuses) }}" class="text-center text-muted py-4">
                                    No recruitment data found for this period
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if(count($recruiterData) > 0)
                        <tfoot class="table-secondary fw-bold">
                            <tr>
                                <td colspan="2">Team Total</td>
                                <td>{{ $teamTotal }}</td>
                                @foreach($statuses as $status)
                                    <td class="text-center">
                                        {{ array_sum(array_column(array_map(fn($r) => $r['statuses'], $recruiterData), $status)) }}
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    <span class="badge bg-{{ $teamConversion >= 20 ? 'success' : ($teamConversion >= 10 ? 'warning' : 'danger') }}">
                                        {{ $teamConversion }}%
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's All Interviews --}}
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Today's Interviews (All Team) <span class="badge bg-primary ms-2">{{ $todaysAllInterviews->count() }}</span></h5>
                <small class="text-muted">{{ now()->format('d F Y') }}</small>
            </div>
            <div class="card-body p-0">
                @if($todaysAllInterviews->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Candidate</th>
                                <th>Contact</th>
                                <th>Designation</th>
                                <th>Recruiter</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $badgeColors = [
                                    'No Answer' => 'secondary', 'Not Interested' => 'danger',
                                    'Call Back' => 'warning', 'Interested' => 'info',
                                    'Hired' => 'success', 'Rejected' => 'dark',
                                    'Left' => 'purple', 'Pending' => 'orange',
                                ];
                            @endphp
                            @foreach($todaysAllInterviews as $i => $interview)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $interview->name }}</td>
                                <td><small>{{ $interview->contact_no }}</small></td>
                                <td><small>{{ $interview->designation }}</small></td>
                                <td><small class="text-muted">{{ $interview->interview_taken_by }}</small></td>
                                <td>
                                    <span class="badge bg-{{ $badgeColors[$interview->status] ?? 'secondary' }}">
                                        {{ $interview->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('recruitments.show', $interview->id) }}" class="btn btn-xs btn-outline-info">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('recruitments.final', $interview->id) }}" class="btn btn-xs btn-outline-primary">
                                        <i class="ti ti-clipboard-check"></i> Final
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="ti ti-calendar-off f-50"></i>
                    <p class="mt-2">No interviews scheduled for today across the team</p>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

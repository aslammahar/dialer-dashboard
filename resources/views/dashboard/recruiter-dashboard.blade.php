@extends('layouts.admin')

@section('page-title')
    {{ __('Recruiter Dashboard') }}
@endsection

@push('script-page')
<script>
// Daily Activity Chart
(function () {
    var options = {
        chart: { height: 220, type: 'area', toolbar: { show: false } },
        dataLabels: { enabled: false },
        stroke: { width: 2, curve: 'smooth' },
        series: [{ name: 'Calls', data: {!! json_encode($chartCounts) !!} }],
        xaxis: { categories: {!! json_encode($chartDates) !!}, labels: { rotate: -45, style: { fontSize: '11px' } } },
        colors: ['#7367f0'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } },
        grid: { strokeDashArray: 4 },
        tooltip: { y: { formatter: function(val) { return val + ' calls'; } } }
    };
    new ApexCharts(document.querySelector("#recruiter-activity-chart"), options).render();
})();

// Status Donut Chart
(function () {
    var options = {
        chart: { height: 260, type: 'donut' },
        series: [
            {{ $statusCounts['Hired'] }},
            {{ $statusCounts['Interested'] }},
            {{ $statusCounts['Call Back'] }},
            {{ $statusCounts['Not Interested'] }},
            {{ $statusCounts['No Answer'] }},
            {{ $statusCounts['Rejected'] }},
            {{ $statusCounts['Pending'] }},
            {{ $statusCounts['Left'] }}
        ],
        labels: ['Hired', 'Interested', 'Call Back', 'Not Interested', 'No Answer', 'Rejected', 'Pending', 'Left'],
        colors: ['#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6c757d', '#e83e8c', '#fd7e14', '#6f42c1'],
        legend: { position: 'bottom', fontSize: '12px' },
        plotOptions: { pie: { donut: { size: '60%' } } },
        dataLabels: { enabled: false },
    };
    new ApexCharts(document.querySelector("#status-donut-chart"), options).render();
})();
</script>
@endpush

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item active">{{ __('Recruiter Dashboard') }}</li>
@endsection

@section('content')
<div class="row">

    {{-- Page Header --}}
    <div class="col-12 mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h4 class="mb-1 fw-bold">Welcome, {{ $recruiterName }}</h4>
                <span class="text-muted">{{ now()->format('l, d F Y') }}</span>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('recruitment.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> New Initial Form
                </a>
                <a href="{{ route('recruitments.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-list me-1"></i> All Recruitments
                </a>
                <a href="{{ route('recruitments.reports') }}" class="btn btn-outline-info">
                    <i class="ti ti-chart-bar me-1"></i> Full Reports
                </a>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Total Calls (All Time)</p>
                        <h3 class="mb-0 fw-bold">{{ $totalCalls }}</h3>
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
                        <p class="text-muted mb-1 small">Today's Calls</p>
                        <h3 class="mb-0 fw-bold text-info">{{ $todaysCalls }}</h3>
                    </div>
                    <div class="theme-avtar bg-info">
                        <i class="ti ti-calendar-event f-20"></i>
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
                        <p class="text-muted mb-1 small">This Month</p>
                        <h3 class="mb-0 fw-bold text-warning">{{ $thisMonthCalls }}</h3>
                    </div>
                    <div class="theme-avtar bg-warning">
                        <i class="ti ti-calendar-month f-20"></i>
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
                        <p class="text-muted mb-1 small">Hired / Conversion</p>
                        <h3 class="mb-0 fw-bold text-success">{{ $statusCounts['Hired'] }} <small class="fs-6 text-muted">({{ $conversionRate }}%)</small></h3>
                    </div>
                    <div class="theme-avtar bg-success">
                        <i class="ti ti-user-check f-20"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Summary Badges --}}
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header py-2">
                <h5 class="mb-0">Status Breakdown (All Time)</h5>
            </div>
            <div class="card-body py-3">
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $badgeColors = [
                            'No Answer' => 'secondary', 'Not Interested' => 'danger',
                            'Call Back' => 'warning', 'Interested' => 'info',
                            'Hired' => 'success', 'Rejected' => 'dark',
                            'Left' => 'purple', 'Pending' => 'orange',
                        ];
                    @endphp
                    @foreach($statuses as $status)
                        <span class="badge bg-{{ $badgeColors[$status] ?? 'secondary' }} fs-6 px-3 py-2">
                            {{ $status }}: <strong>{{ $statusCounts[$status] }}</strong>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Chart + Status Donut --}}
    <div class="col-lg-8 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Daily Activity (Last 30 Days)</h5>
            </div>
            <div class="card-body">
                <div id="recruiter-activity-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Status Distribution</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div id="status-donut-chart" style="width:100%;"></div>
            </div>
        </div>
    </div>

    {{-- Today's Interviews --}}
    <div class="col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Today's Interviews <span class="badge bg-primary ms-2">{{ $todaysInterviews->count() }}</span></h5>
            </div>
            <div class="card-body p-0">
                @if($todaysInterviews->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Candidate</th>
                                <th>Designation</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todaysInterviews as $interview)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $interview->name }}</div>
                                    <small class="text-muted">{{ $interview->contact_no }}</small>
                                </td>
                                <td><small>{{ $interview->designation }}</small></td>
                                <td>
                                    <span class="badge bg-{{ $badgeColors[$interview->status] ?? 'secondary' }}">
                                        {{ $interview->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('recruitments.final', $interview->id) }}" class="btn btn-xs btn-primary">
                                        <i class="ti ti-clipboard-check"></i> Final
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="ti ti-calendar-off f-40"></i>
                    <p class="mt-2">No interviews scheduled for today</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Upcoming Interviews --}}
    <div class="col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Upcoming Interviews (Next 7 Days) <span class="badge bg-info ms-2">{{ $upcomingInterviews->count() }}</span></h5>
            </div>
            <div class="card-body p-0">
                @if($upcomingInterviews->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Candidate</th>
                                <th>Designation</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingInterviews as $interview)
                            <tr>
                                <td class="fw-semibold">{{ $interview->name }}</td>
                                <td><small>{{ $interview->designation }}</small></td>
                                <td><small class="text-info">{{ \Carbon\Carbon::parse($interview->date)->format('d M, Y') }}</small></td>
                                <td>
                                    <span class="badge bg-{{ $badgeColors[$interview->status] ?? 'secondary' }}">
                                        {{ $interview->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="ti ti-calendar f-40"></i>
                    <p class="mt-2">No upcoming interviews in next 7 days</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Recruits --}}
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">My Recent Recruits</h5>
                <a href="{{ route('recruitments.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Candidate</th>
                                <th>Designation</th>
                                <th>Source</th>
                                <th>Status</th>
                                <th>Interview Date</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRecruits as $i => $r)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $r->name }}</td>
                                <td><small>{{ $r->designation }}</small></td>
                                <td><small>{{ $r->source }}</small></td>
                                <td>
                                    <span class="badge bg-{{ $badgeColors[$r->status] ?? 'secondary' }}">
                                        {{ $r->status }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('d M Y') : '-' }}</small>
                                </td>
                                <td><small class="text-muted">{{ $r->created_at->format('d M Y') }}</small></td>
                                <td>
                                    <a href="{{ route('recruitments.show', $r->id) }}" class="btn btn-xs btn-outline-info">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('recruitments.final', $r->id) }}" class="btn btn-xs btn-outline-primary">
                                        <i class="ti ti-clipboard-check"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No recruitment records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-dollar-sign text-success"></i> Commission Management</h2>
                    <p class="text-muted mb-0">Upload statements, configure agents, and view comprehensive reports</p>
                </div>
                <div>
                    <span class="badge bg-primary fs-6">{{ \App\Models\CommissionStatement::count() }} Total Records</span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-file-excel fa-3x text-primary"></i>
                    </div>
                    <h3 class="mb-0">{{ $months->count() }}</h3>
                    <p class="text-muted mb-0">Uploaded Months</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-users fa-3x text-success"></i>
                    </div>
                    <h3 class="mb-0">{{ $agents->count() }}</h3>
                    <p class="text-muted mb-0">Configured Agents</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-file-contract fa-3x text-info"></i>
                    </div>
                    <h3 class="mb-0">{{ \App\Models\CommissionStatement::distinct('policy_no')->count('policy_no') }}</h3>
                    <p class="text-muted mb-0">Unique Policies</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-dollar-sign fa-3x text-warning"></i>
                    </div>
                    <h3 class="mb-0">${{ number_format(\App\Models\CommissionStatement::sum('commission_credit'), 2) }}</h3>
                    <p class="text-muted mb-0">Total Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h5 class="mb-0 text-white"><i class="fas fa-cloud-upload-alt me-2"></i>Upload Monthly Statement</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('commission.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="file" class="form-label fw-bold">
                            <i class="fas fa-file-excel text-success me-1"></i>Excel File *
                        </label>
                        <input type="file" class="form-control form-control-lg" id="file" name="file" accept=".xlsx,.xls" required>
                        <small class="text-muted">Supported: .xlsx, .xls</small>
                    </div>
                    <div class="col-md-3">
                        <label for="month" class="form-label fw-bold">
                            <i class="fas fa-calendar-alt text-primary me-1"></i>Month *
                        </label>
                        <select class="form-select form-select-lg" id="month" name="month" required>
                            <option value="">Select Month</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="year" class="form-label fw-bold">
                            <i class="fas fa-calendar text-info me-1"></i>Year *
                        </label>
                        <select class="form-select form-select-lg" id="year" name="year" required>
                            @for($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow">
                            <i class="fas fa-upload me-1"></i> Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Available Statements -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-database me-2"></i>Available Statements</h5>
                </div>
                <div class="card-body">
                    @if($months->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-calendar me-1"></i>Month</th>
                                        <th><i class="fas fa-calendar-check me-1"></i>Year</th>
                                        <th><i class="fas fa-list-ol me-1"></i>Records</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($months as $m)
                                        <tr>
                                            <td><strong>{{ $m->month }}</strong></td>
                                            <td>{{ $m->year }}</td>
                                            <td>
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ \App\Models\CommissionStatement::byMonth($m->year, $m->month_no)->count() }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('commission.report', ['year' => $m->year, 'month' => $m->month_no]) }}" 
                                                   class="btn btn-sm btn-primary shadow-sm">
                                                    <i class="fas fa-chart-bar me-1"></i> View Report
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No statements uploaded yet</p>
                            <small class="text-muted">Upload your first statement above to get started</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Agent Config -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-cog me-2"></i>Agent Configuration</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('commission.config.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="agent_name" class="form-label fw-bold">
                                <i class="fas fa-user me-1"></i>Agent Name *
                            </label>
                            <input type="text" class="form-control" id="agent_name" name="agent_name" placeholder="Enter agent name" required>
                        </div>
                        <div class="mb-3">
                            <label for="advance_months" class="form-label fw-bold">
                                <i class="fas fa-calendar-plus me-1"></i>Advance Months *
                            </label>
                            <input type="number" class="form-control" id="advance_months" name="advance_months" min="1" max="60" placeholder="e.g., 12" required>
                            <small class="text-muted">Total months paid in advance</small>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-bold">
                                <i class="fas fa-sticky-note me-1"></i>Notes
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Optional notes"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100 shadow">
                            <i class="fas fa-plus-circle me-1"></i> Add Agent Configuration
                        </button>
                    </form>

                    @if($agents->count() > 0)
                        <hr class="my-4">
                        <h6 class="text-muted mb-3"><i class="fas fa-list me-2"></i>Configured Agents ({{ $agents->count() }})</h6>
                        <div class="list-group">
                            @foreach($agents as $agent)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $agent->agent_name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>{{ $agent->advance_months }} months
                                            @if($agent->notes)
                                                | <i class="fas fa-sticky-note me-1"></i>{{ $agent->notes }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('commission.pending', ['agent' => $agent->agent_name]) }}" 
                                           class="btn btn-sm btn-info" title="View Pending">
                                            <i class="fas fa-calculator"></i>
                                        </a>
                                        <form action="{{ route('commission.config.delete', $agent->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Delete this configuration?')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="{{ route('commission.comprehensive') }}" class="btn btn-success btn-lg w-100 shadow-sm">
                        <i class="fas fa-chart-line fa-2x mb-2"></i><br>
                        <strong>Comprehensive Report</strong><br>
                        <small>View all months combined</small>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('commission.report') }}" class="btn btn-primary btn-lg w-100 shadow-sm">
                        <i class="fas fa-calendar-alt fa-2x mb-2"></i><br>
                        <strong>Monthly Report</strong><br>
                        <small>View single month data</small>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('commission.pending') }}" class="btn btn-warning btn-lg w-100 shadow-sm">
                        <i class="fas fa-hourglass-half fa-2x mb-2"></i><br>
                        <strong>Pending Commissions</strong><br>
                        <small>View agent pending amounts</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .list-group-item {
        border-left: 3px solid #28a745;
    }
    .btn-lg {
        transition: all 0.3s;
    }
    .btn-lg:hover {
        transform: scale(1.05);
    }
</style>
@endpush
@endsection
@extends('layouts.admin')

@section('title', 'Tax Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tax Reports</h2>
    <div>
        <form action="{{ route('tax-reports.export') }}" method="GET" class="d-inline">
            <input type="hidden" name="year" value="{{ request('year', $currentYear) }}">
            <input type="hidden" name="month" value="{{ request('month', $currentMonth) }}">
            <input type="hidden" name="salary_department_id" value="{{ request('salary_department_id') }}">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export to Excel
            </button>
        </form>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        <h6 class="mb-0 text-white"><i class="fas fa-filter text-white"></i> Filter Tax Records</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('tax-reports.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Year <span class="text-danger">*</span></label>
                <select name="year" class="form-select" required>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Month <span class="text-danger">*</span></label>
                <select name="month" class="form-select" required>
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ request('month', $currentMonth) == $num ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Department</label>
                <select name="salary_department_id" class="form-select">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('salary_department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('tax-reports.index') }}" class="btn btn-secondary" title="Reset">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title  text-white"><i class="fas fa-money-bill-wave"></i> Total Tax Deducted</h6>
                <h3 class="mb-0">{{ number_format($totalTaxDeducted, 0) }}</h3>
                <small>PKR</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title  text-white"><i class="fas fa-users"></i> Total Employees</h6>
                <h3 class="mb-0">{{ $totalEmployees }}</h3>
                <small>With tax deduction</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title  text-white"><i class="fas fa-chart-line"></i> Gross Salary</h6>
                <h3 class="mb-0">{{ number_format($totalGrossSalary, 0) }}</h3>
                <small>PKR</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title  text-white"><i class="fas fa-hand-holding-usd"></i> Net Salary</h6>
                <h3 class="mb-0">{{ number_format($totalNetSalary, 0) }}</h3>
                <small>PKR (After tax)</small>
            </div>
        </div>
    </div>
</div>

<!-- Tax Records Table -->
<div class="card">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0 text-white">
            <i class="fas fa-receipt text-white text-white"></i> Tax Deduction Records
            @if($taxRecords->total() > 0)
                <span class="badge bg-light text-dark">{{ $taxRecords->total() }} Records</span>
            @endif
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Period</th>
                        <th>Basic Salary</th>
                        <th>Allowances</th>
                        <th>Deductions</th>
                        <th>Gross Salary</th>
                        <th>Tax Slab</th>
                        <th>Tax %</th>
                        <th>Tax Amount</th>
                        <th>Net Salary</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taxRecords as $record)
                        @php
                            $grossSalary = $record->basic_salary + $record->total_allowances - $record->total_deductions;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $record->user->userDetail->full_name ?? $record->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $record->user->email }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $record->salaryDepartment->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <strong>{{ $months[$record->month] }}</strong>
                                <br>
                                <small class="text-muted">{{ $record->year }}</small>
                            </td>
                            <td>PKR {{ number_format($record->basic_salary, 0) }}</td>
                            <td>PKR {{ number_format($record->total_allowances, 0) }}</td>
                            <td>PKR {{ number_format($record->total_deductions, 0) }}</td>
                            <td><strong>PKR {{ number_format($grossSalary, 0) }}</strong></td>
                            <td>
                                @if($record->taxSlab)
                                    <small class="text-muted">{{ $record->taxSlab->range }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td><span class="badge bg-warning text-dark">{{ $record->tax_percentage }}%</span></td>
                            <td><strong class="text-danger">PKR {{ number_format($record->tax_amount, 0) }}</strong></td>
                            <td><strong class="text-success">PKR {{ number_format($record->net_salary, 0) }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">No Tax Records Found</h5>
                                <p class="text-muted">No tax deductions found for the selected period and department</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($taxRecords->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="9" class="text-end">TOTAL:</th>
                            <th class="text-danger">PKR {{ number_format($totalTaxDeducted, 0) }}</th>
                            <th class="text-success">PKR {{ number_format($totalNetSalary, 0) }}</th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <div class="mt-3">
            {{ $taxRecords->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        vertical-align: middle;
        font-size: 0.9rem;
    }
    
    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }
    
    .card {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        border: none;
    }
</style>
@endpush
@extends('layouts.admin')

@section('title', 'Salary Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Salary Details</h2>
    <div>
        <a href="{{ route('monthly-salaries.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        @if($monthlySalary->status == 'draft')
            <a href="{{ route('monthly-salaries.edit', $monthlySalary) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('monthly-salaries.approve', $monthlySalary) }}" 
                  method="POST" 
                  class="d-inline"
                  onsubmit="return confirm('Are you sure you want to approve this salary?')">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check"></i> Approve
                </button>
            </form>
        @endif
        @if($monthlySalary->status == 'approved')
            <a href="{{ route('salary-slips.download', $monthlySalary) }}" class="btn btn-primary">
                <i class="fas fa-download"></i> Download Slip
            </a>
        @endif
    </div>
</div>

<!-- Employee Information Card -->
<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-user"></i> Employee Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%" class="fw-bold">Employee Name:</td>
                        <td>{{ $monthlySalary->user->userDetail->full_name ?? $monthlySalary->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Employee ID:</td>
                        <td>EMP-{{ str_pad($monthlySalary->user->id, 4, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Email:</td>
                        <td>{{ $monthlySalary->user->email }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Department:</td>
                        <td>{{ $monthlySalary->salaryDepartment->name ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%" class="fw-bold">Designation:</td>
                        <td>{{ $monthlySalary->user->userDetail->designation ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Pay Period:</td>
                        <td>{{ $monthlySalary->period }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Status:</td>
                        <td>
                            @if($monthlySalary->status == 'draft')
                                <span class="badge bg-warning">Draft</span>
                            @elseif($monthlySalary->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-info">Paid</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Created On:</td>
                        <td>{{ $monthlySalary->created_at->format('d M, Y h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Summary Card -->
<div class="card mb-3">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Attendance Summary</h5>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="p-3 border rounded">
                    <h6 class="text-muted mb-2">Total Working Days</h6>
                    <h3 class="mb-0">{{ $monthlySalary->working_days }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded bg-success bg-opacity-10">
                    <h6 class="text-muted mb-2">Present Days</h6>
                    <h3 class="mb-0 text-success">{{ $monthlySalary->present_days }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded bg-danger bg-opacity-10">
                    <h6 class="text-muted mb-2">Absent Days</h6>
                    <h3 class="mb-0 text-danger">{{ $monthlySalary->absent_days }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded bg-warning bg-opacity-10">
                    <h6 class="text-muted mb-2">Leave Days</h6>
                    <h3 class="mb-0 text-warning">{{ $monthlySalary->leave_days }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add this in your salary detail view -->
<div class="row">
    <div class="col-md-6">
        <h6>Tax Information</h6>
        <table class="table table-sm">
            <tr>
                <td>Tax Slab:</td>
                <td><strong>{{ $salary->taxSlab ? $salary->taxSlab->range : 'N/A' }}</strong></td>
            </tr>
            <tr>
                <td>Tax Percentage:</td>
                <td><strong>{{ $salary->tax_percentage }}%</strong></td>
            </tr>
            <tr>
                <td>Tax Amount:</td>
                <td><strong class="text-danger">PKR {{ number_format($salary->tax_amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>
</div>
<!-- Salary Breakdown -->
<div class="row">
    <!-- Earnings -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Earnings</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <td class="fw-bold">Basic Salary ({{ $monthlySalary->present_days }}/{{ $monthlySalary->working_days }} days)</td>
                        <td class="text-end">{{ number_format($monthlySalary->basic_salary / $monthlySalary->working_days * $monthlySalary->present_days, 2) }}</td>
                    </tr>
                    @if($monthlySalary->punctuality > 0)
                    <tr>
                        <td class="fw-bold">Punctuality Allowance</td>
                        <td class="text-end">{{ number_format($monthlySalary->punctuality, 2) }}</td>
                    </tr>
                    @endif
                    @if($monthlySalary->total_allowances > 0)
                    <tr>
                        <td class="fw-bold">Other Allowances</td>
                        <td class="text-end">{{ number_format($monthlySalary->total_allowances, 2) }}</td>
                    </tr>
                    @endif
                    @if($monthlySalary->bonus > 0)
                    <tr>
                        <td class="fw-bold">Bonus / Incentive</td>
                        <td class="text-end">{{ number_format($monthlySalary->bonus, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="table-success">
                        <td class="fw-bold">GROSS SALARY</td>
                        <td class="text-end fw-bold">{{ number_format($monthlySalary->gross_salary, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Deductions -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-minus-circle"></i> Deductions</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    @if($monthlySalary->total_deductions > 0)
                    <tr>
                        <td class="fw-bold">Total Deductions</td>
                        <td class="text-end">{{ number_format($monthlySalary->total_deductions, 2) }}</td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="2" class="text-center text-muted">No deductions</td>
                    </tr>
                    @endif
                    <tr class="table-danger">
                        <td class="fw-bold">TOTAL DEDUCTIONS</td>
                        <td class="text-end fw-bold">{{ number_format($monthlySalary->total_deductions, 2) }}</td>
                    </tr>
                </table>

                <!-- Salary Components Details (if available) -->
                @if($monthlySalary->salaryStructure)
                    <div class="mt-3">
                        <h6 class="text-muted mb-2">Salary Structure Details:</h6>
                        <small class="text-muted">
                            <strong>Base Structure:</strong> {{ $monthlySalary->salaryStructure->name ?? 'N/A' }}
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Net Salary Card -->
<div class="card mb-3">
    <div class="card-body text-center py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <h5 class="text-white mb-2">NET SALARY (PKR)</h5>
        <h1 class="text-white mb-0 display-4 fw-bold">{{ number_format($monthlySalary->net_salary, 2) }}</h1>
    </div>
</div>

<!-- Remarks -->
@if($monthlySalary->remarks)
<div class="card mb-3">
    <div class="card-header bg-warning">
        <h5 class="mb-0"><i class="fas fa-comment"></i> Remarks</h5>
    </div>
    <div class="card-body">
        <p class="mb-0">{{ $monthlySalary->remarks }}</p>
    </div>
</div>
@endif

<!-- Approval Information -->
@if($monthlySalary->status == 'approved' && $monthlySalary->approved_at)
<div class="card mb-3">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="fas fa-check-circle"></i> Approval Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <strong>Approved By:</strong> {{ $monthlySalary->approver->name ?? 'N/A' }}
            </div>
            <div class="col-md-6">
                <strong>Approved At:</strong> {{ $monthlySalary->approved_at->format('d M, Y h:i A') }}
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .table-borderless td {
        padding: 0.5rem 0;
    }
</style>
@endpush
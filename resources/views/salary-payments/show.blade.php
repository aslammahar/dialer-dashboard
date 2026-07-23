@extends('layouts.admin')

@section('page-title')
    {{__('Payment Details')}}
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h5 class="mb-0 text-white"><i class="ti ti-file-invoice me-2"></i>Payment Details</h5>
                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('salary.payments.index') }}" class="btn btn-light btn-sm">
                            <i class="ti ti-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <!-- Employee Information -->
                    <div class="col-md-6 mb-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="border-bottom pb-2 mb-3"><i class="ti ti-user me-2"></i>Employee Information</h6>
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td width="40%"><strong>Name:</strong></td>
                                        <td>{{ $payment->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Employee ID:</strong></td>
                                        <td>{{ $payment->user->userDetail->employee_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Department:</strong></td>
                                        <td>{{ $payment->monthlySalary->salaryDepartment->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Designation:</strong></td>
                                        <td>{{ $payment->user->userDetail->designation ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Information -->
                    <div class="col-md-6 mb-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="border-bottom pb-2 mb-3"><i class="ti ti-building-bank me-2"></i>Bank Account Details</h6>
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td width="40%"><strong>Bank Name:</strong></td>
                                        <td>{{ $payment->bank_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Account Title:</strong></td>
                                        <td>{{ $payment->account_title }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Account Number:</strong></td>
                                        <td>{{ $payment->account_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Priority:</strong></td>
                                        <td>
                                            <span class="badge bg-info">
                                                Priority {{ $payment->bankDetail->priority ?? 'N/A' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="col-md-6 mb-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="border-bottom pb-2 mb-3"><i class="ti ti-receipt me-2"></i>Payment Information</h6>
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td width="40%"><strong>Salary Period:</strong></td>
                                        <td>{{ $payment->monthlySalary->period }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Amount:</strong></td>
                                        <td><h5 class="text-success mb-0">Rs. {{ number_format($payment->payment_amount, 2) }}</h5></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Status:</strong></td>
                                        <td>
                                            <span class="badge {{ $payment->status_badge_class }} px-3 py-2">
                                                {{ ucfirst($payment->payment_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Processed By:</strong></td>
                                        <td>{{ $payment->processor->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Processed At:</strong></td>
                                        <td>{{ $payment->processed_at ? $payment->processed_at->format('d/m/Y H:i A') : 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Screenshot -->
                    <div class="col-md-6 mb-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="border-bottom pb-2 mb-3"><i class="ti ti-photo me-2"></i>Payment Screenshot</h6>
                                @if($payment->paymentScreenshot)
                                    <div class="text-center">
                                        <img src="{{ $payment->paymentScreenshot->url }}" 
                                             alt="Payment Screenshot" 
                                             class="img-fluid rounded shadow-sm"
                                             style="max-height: 400px; cursor: pointer;"
                                             onclick="window.open(this.src, '_blank')">
                                        <div class="mt-3">
                                            <a href="{{ $payment->paymentScreenshot->url }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-primary">
                                                <i class="ti ti-download me-1"></i>Download Screenshot
                                            </a>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            Uploaded: {{ $payment->paymentScreenshot->created_at->format('d/m/Y H:i A') }}
                                        </small>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="ti ti-photo-off" style="font-size: 48px; color: #ccc;"></i>
                                        <p class="text-muted mt-2">No screenshot available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Remarks -->
                    @if($payment->remarks)
                    <div class="col-12 mb-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="border-bottom pb-2 mb-3"><i class="ti ti-note me-2"></i>Remarks</h6>
                                <p class="mb-0">{{ $payment->remarks }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Salary Breakdown with Tax -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0 text-white"><i class="ti ti-calculator me-2"></i>Complete Salary Breakdown</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Earnings Section -->
                                    <div class="col-md-6">
                                        <h6 class="text-success mb-3"><i class="ti ti-trending-up me-2"></i>Earnings</h6>
                                        <table class="table table-sm table-bordered">
                                            <tr class="table-light">
                                                <td><strong>Basic Salary</strong></td>
                                                <td class="text-end">Rs. {{ number_format($payment->monthlySalary->basic_salary, 2) }}</td>
                                            </tr>
                                            @if($payment->monthlySalary->punctuality > 0)
                                            <tr>
                                                <td>Punctuality Allowance</td>
                                                <td class="text-end text-success">+ Rs. {{ number_format($payment->monthlySalary->punctuality, 2) }}</td>
                                            </tr>
                                            @endif
                                            @if($payment->monthlySalary->total_allowances > 0)
                                            <tr>
                                                <td>Other Allowances</td>
                                                <td class="text-end text-success">+ Rs. {{ number_format($payment->monthlySalary->total_allowances, 2) }}</td>
                                            </tr>
                                            @endif
                                            @if($payment->monthlySalary->bonus > 0)
                                            <tr>
                                                <td>Bonus / Incentive</td>
                                                <td class="text-end text-success">+ Rs. {{ number_format($payment->monthlySalary->bonus, 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr class="table-success">
                                                <td><strong>Gross Salary (Before Tax)</strong></td>
                                                <td class="text-end"><strong>Rs. {{ number_format($payment->monthlySalary->gross_salary, 2) }}</strong></td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Deductions Section -->
                                    <div class="col-md-6">
                                        <h6 class="text-danger mb-3"><i class="ti ti-trending-down me-2"></i>Deductions</h6>
                                        <table class="table table-sm table-bordered">
                                            @if($payment->monthlySalary->total_deductions > 0)
                                            <tr>
                                                <td>Other Deductions</td>
                                                <td class="text-end text-danger">- Rs. {{ number_format($payment->monthlySalary->total_deductions, 2) }}</td>
                                            </tr>
                                            @endif
                                            
                                            @if($payment->monthlySalary->tax_amount > 0)
                                            <tr class="table-info">
                                                <td>
                                                    <strong>Income Tax ({{ number_format($payment->monthlySalary->tax_percentage, 2) }}%)</strong>
                                                    @if($payment->monthlySalary->taxSlab)
                                                        <br><small class="text-muted" style="font-size: 0.85rem;">
                                                            Tax Slab: PKR {{ number_format($payment->monthlySalary->taxSlab->min_salary, 0) }} - 
                                                            {{ $payment->monthlySalary->taxSlab->max_salary ? 'PKR ' . number_format($payment->monthlySalary->taxSlab->max_salary, 0) : 'Above' }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="text-end text-primary">- Rs. {{ number_format($payment->monthlySalary->tax_amount, 2) }}</td>
                                            </tr>
                                            @endif
                                            
                                            <tr class="table-danger">
                                                <td><strong>Total Deductions</strong></td>
                                                <td class="text-end"><strong>Rs. {{ number_format(($payment->monthlySalary->total_deductions ?? 0) + ($payment->monthlySalary->tax_amount ?? 0), 2) }}</strong></td>
                                            </tr>
                                            
                                            <!-- Empty rows to match height -->
                                            @php
                                                $earningsRows = 1; // Basic
                                                if($payment->monthlySalary->punctuality > 0) $earningsRows++;
                                                if($payment->monthlySalary->total_allowances > 0) $earningsRows++;
                                                if($payment->monthlySalary->bonus > 0) $earningsRows++;
                                                $earningsRows++; // Gross total
                                                
                                                $deductionsRows = 0;
                                                if($payment->monthlySalary->total_deductions > 0) $deductionsRows++;
                                                if($payment->monthlySalary->tax_amount > 0) $deductionsRows++;
                                                $deductionsRows++; // Total deductions
                                                
                                                $emptyRows = $earningsRows - $deductionsRows;
                                            @endphp
                                            
                                            @for($i = 0; $i < $emptyRows; $i++)
                                            <tr>
                                                <td colspan="2">&nbsp;</td>
                                            </tr>
                                            @endfor
                                        </table>
                                    </div>

                                    <!-- Net Salary -->
                                    <div class="col-12 mt-3">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body text-center py-4">
                                                <h6 class="mb-2 text-white">FINAL NET SALARY (After All Deductions Including Tax)</h6>
                                                <h2 class="mb-0 text-white">Rs. {{ number_format($payment->monthlySalary->net_salary, 2) }}</h2>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Summary Breakdown -->
                                    <div class="col-12 mt-3">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="mb-3"><i class="ti ti-clipboard-list me-2"></i>Salary Calculation Summary</h6>
                                                <div class="row text-center">
                                                    <div class="col-md-3">
                                                        <div class="p-3 border rounded">
                                                            <small class="text-muted d-block mb-1">Gross Salary</small>
                                                            <h5 class="mb-0 text-success">{{ number_format($payment->monthlySalary->gross_salary, 2) }}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="p-3 border rounded">
                                                            <small class="text-muted d-block mb-1">Other Deductions</small>
                                                            <h5 class="mb-0 text-danger">{{ number_format($payment->monthlySalary->total_deductions ?? 0, 2) }}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="p-3 border rounded">
                                                            <small class="text-muted d-block mb-1">Tax Deducted ({{ number_format($payment->monthlySalary->tax_percentage ?? 0, 2) }}%)</small>
                                                            <h5 class="mb-0 text-primary">{{ number_format($payment->monthlySalary->tax_amount ?? 0, 2) }}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="p-3 border rounded bg-primary text-white">
                                                            <small class="d-block mb-1 text-white">Net Payable</small>
                                                            <h5 class="mb-0 text-white">{{ number_format($payment->monthlySalary->net_salary, 2) }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.table td {
    padding: 8px 12px;
}
.table-sm td {
    font-size: 0.9rem;
}
</style>
@endsection
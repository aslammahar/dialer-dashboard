@extends('layouts.admin')

@section('page-title')
    {{__('Salary Slip Details')}}
@endsection

@push('script-page')
<style>
@media print {
    .btn, .modal, .alert button, .no-print { display: none !important; }
    .card { border: none; box-shadow: none; }
}
.payment-proof-img {
    max-height: 600px;
    cursor: pointer;
    transition: transform 0.3s ease;
}
.payment-proof-img:hover {
    transform: scale(1.02);
}
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h5 class="mb-0 text-white">
                            <i class="ti ti-file-invoice me-2"></i>{{__('Salary Slip')}} - {{ $salary->period }}
                        </h5>
                    </div>
                    <div class="col-6 text-end no-print">
                        <a href="{{ route('employee.salary-slips') }}" class="btn btn-light btn-sm">
                            <i class="ti ti-arrow-left me-1"></i>{{__('Back to List')}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                
                <!-- Payment Status Alert -->
                <div class="row mb-4 no-print">
                    <div class="col-12">
                        @if($salary->payment)
                            @if($salary->payment->isSent())
                                <div class="alert alert-success border-0 shadow-sm">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-check-circle me-3" style="font-size: 32px;"></i>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{__('Payment Sent Successfully!')}}</h5>
                                            <p class="mb-1">{{__('Your salary has been transferred to your bank account.')}}</p>
                                            <small>
                                                <strong>{{__('Bank')}}:</strong> {{ $salary->payment->bank_name }} | 
                                                <strong>{{__('Account')}}:</strong> {{ $salary->payment->account_number }} | 
                                                <strong>{{__('Amount')}}:</strong> Rs. {{ number_format($salary->payment->payment_amount, 2) }}
                                            </small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentProofModal">
                                            <i class="ti ti-file-invoice me-1"></i>{{__('View Payment Proof')}}
                                        </button>
                                    </div>
                                </div>
                            @elseif($salary->payment->isDeclined())
                                <div class="alert alert-danger border-0 shadow-sm">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-alert-circle me-3" style="font-size: 32px;"></i>
                                        <div>
                                            <h5 class="mb-1">{{__('Payment Declined')}}</h5>
                                            <p class="mb-0">{{__('Please contact HR department for more information.')}}</p>
                                            @if($salary->payment->remarks)
                                                <small class="text-muted"><strong>{{__('Reason')}}:</strong> {{ $salary->payment->remarks }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning border-0 shadow-sm">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-clock me-3" style="font-size: 32px;"></i>
                                        <div>
                                            <h5 class="mb-1">{{__('Payment Pending')}}</h5>
                                            <p class="mb-0">{{__('Your salary payment is being processed. You will be notified once it\'s completed.')}}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info border-0 shadow-sm">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-info-circle me-3" style="font-size: 32px;"></i>
                                    <div>
                                        <h5 class="mb-1">{{__('Payment Processing')}}</h5>
                                        <p class="mb-0">{{__('Your salary payment will be processed soon. Please check back later.')}}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Salary Details -->
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">{{__('Employee Information')}}</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>{{__('Name')}}:</strong></td>
                                <td>{{ $salary->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{__('Employee ID')}}:</strong></td>
                                <td>{{ $salary->user->userDetail->employee_id ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{__('Department')}}:</strong></td>
                                <td>{{ $salary->salaryDepartment->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{__('Designation')}}:</strong></td>
                                <td>{{ $salary->user->userDetail->designation ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">{{__('Salary Period')}}</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>{{__('Month/Year')}}:</strong></td>
                                <td>{{ $salary->period }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{__('Working Days')}}:</strong></td>
                                <td>{{ $salary->working_days }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{__('Present Days')}}:</strong></td>
                                <td>{{ $salary->present_days }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{__('Absent Days')}}:</strong></td>
                                <td>{{ $salary->absent_days }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Salary Breakdown -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">{{__('Salary Breakdown')}}</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{__('Component')}}</th>
                                        <th class="text-end">{{__('Amount (Rs.)')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{__('Basic Salary')}}</td>
                                        <td class="text-end">{{ number_format($salary->basic_salary, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Total Allowances')}}</td>
                                        <td class="text-end text-success">+ {{ number_format($salary->total_allowances, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Bonus')}}</td>
                                        <td class="text-end text-success">+ {{ number_format($salary->bonus, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Total Deductions')}}</td>
                                        <td class="text-end text-danger">- {{ number_format($salary->total_deductions, 2) }}</td>
                                    </tr>
                                    <tr class="table-secondary">
                                        <td><strong>{{__('Gross Salary')}}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($salary->gross_salary, 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td><strong>{{__('Net Salary (Payable)')}}</strong></td>
                                        <td class="text-end"><h5 class="mb-0 text-primary">{{ number_format($salary->net_salary, 2) }}</h5></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment Bank Details (if payment sent) -->
                @if($salary->payment && $salary->payment->isSent())
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="border-bottom pb-2 mb-3">{{__('Payment Details')}}</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>{{__('Bank Name')}}:</strong> {{ $salary->payment->bank_name }}</p>
                                        <p class="mb-1"><strong>{{__('Account Title')}}:</strong> {{ $salary->payment->account_title }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>{{__('Account Number')}}:</strong> {{ $salary->payment->account_number }}</p>
                                        <p class="mb-1"><strong>{{__('Payment Date')}}:</strong> {{ $salary->payment->processed_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="row mt-4 no-print">
                    <div class="col-12 text-center">
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="ti ti-printer me-1"></i>{{__('Print Salary Slip')}}
                        </button>
                        <a href="{{ route('employee.salary-slip.download', $salary->id) }}" class="btn btn-success">
                            <i class="ti ti-download me-1"></i>{{__('Download PDF')}}
                        </a>
                        @if($salary->payment && $salary->payment->isSent())
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#paymentProofModal">
                                <i class="ti ti-file-invoice me-1"></i>{{__('View Payment Proof')}}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Proof Modal -->
@if($salary->payment && $salary->payment->paymentScreenshot)
<div class="modal fade" id="paymentProofModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white"><i class="ti ti-file-invoice me-2"></i>{{__('Payment Proof')}}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ $salary->payment->paymentScreenshot->url }}" 
                     alt="Payment Screenshot" 
                     class="img-fluid rounded shadow payment-proof-img"
                     onclick="window.open(this.src, '_blank')">
                <div class="mt-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>{{__('Payment Amount')}}:</strong> Rs. {{ number_format($salary->payment->payment_amount, 2) }}<br>
                                <strong>{{__('Paid To')}}:</strong> {{ $salary->payment->account_title }} ({{ $salary->payment->account_number }})<br>
                                <strong>{{__('Payment Date')}}:</strong> {{ $salary->payment->processed_at->format('d F Y, h:i A') }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ $salary->payment->paymentScreenshot->url }}" 
                       target="_blank" 
                       download
                       class="btn btn-primary mt-2">
                        <i class="ti ti-download me-1"></i>{{__('Download Proof')}}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
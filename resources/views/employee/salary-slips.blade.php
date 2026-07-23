@extends('layouts.admin')

@section('page-title')
    {{__('My Salary Slips')}}
@endsection

@push('script-page')
<style>
.payment-badge {
    font-size: 0.75rem;
    padding: 4px 10px;
}
.salary-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
    transition: all 0.3s ease;
}
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white"><i class="ti ti-file-invoice me-2"></i>{{__('My Salary Slips')}}</h5>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">{{__('Year')}}</label>
                                    <select name="year" class="form-select">
                                        <option value="">{{__('All Years')}}</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{__('Month')}}</label>
                                    <select name="month" class="form-select">
                                        <option value="">{{__('All Months')}}</option>
                                        @foreach($months as $num => $name)
                                            <option value="{{ $num }}" {{ $currentMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-filter me-1"></i>{{__('Filter')}}
                                    </button>
                                    <a href="{{ route('employee.salary-slips') }}" class="btn btn-secondary ms-2">
                                        <i class="ti ti-refresh me-1"></i>{{__('Reset')}}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Salary Slips Table -->
                    @if($salaries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{__('Period')}}</th>
                                        <th>{{__('Department')}}</th>
                                        <th>{{__('Net Salary')}}</th>
                                        <th>{{__('Payment Status')}}</th>
                                        <th class="text-center">{{__('Actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salaries as $salary)
                                        <tr class="salary-card">
                                            <td>
                                                <strong>{{ date('F Y', mktime(0, 0, 0, $salary->month, 1, $salary->year)) }}</strong>
                                            </td>
                                            <td>{{ $salary->salaryDepartment->name ?? 'N/A' }}</td>
                                            <td>
                                                <h6 class="mb-0 text-success">Rs. {{ number_format($salary->net_salary, 2) }}</h6>
                                            </td>
                                            <td>
                                                @if($salary->payment)
                                                    @if($salary->payment->isSent())
                                                        <span class="badge payment-badge bg-success">
                                                            <i class="ti ti-check-circle me-1"></i>{{__('Paid')}}
                                                        </span>
                                                    @elseif($salary->payment->isDeclined())
                                                        <span class="badge payment-badge bg-danger">
                                                            <i class="ti ti-x-circle me-1"></i>{{__('Declined')}}
                                                        </span>
                                                    @else
                                                        <span class="badge payment-badge bg-warning">
                                                            <i class="ti ti-clock me-1"></i>{{__('Pending')}}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge payment-badge bg-info">
                                                        <i class="ti ti-info-circle me-1"></i>{{__('Processing')}}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('employee.salary-slip.show', $salary->id) }}" 
                                                       class="btn btn-sm btn-info" 
                                                       title="View Details">
                                                        <i class="ti ti-eye"></i> {{__('View')}}
                                                    </a>
                                                    <a href="{{ route('employee.salary-slip.download', $salary->id) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="Download PDF">
                                                        <i class="ti ti-download"></i> {{__('Download')}}
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $salaries->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ti ti-file-off" style="font-size: 64px; color: #ccc;"></i>
                            <h5 class="mt-3 text-muted">{{__('No Salary Slips Found')}}</h5>
                            <p class="text-muted">{{__('You don\'t have any approved salary slips yet.')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.admin')

@section('title', 'Salary Structure Details')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Salary Structure Details</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('salary-structures.edit', $salaryStructure) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('salary-structures.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Employee Information -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user"></i> Employee Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Full Name</small>
                    <div class="fw-bold">{{ $salaryStructure->user->userDetail->full_name ?? $salaryStructure->user->name }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Email</small>
                    <div>{{ $salaryStructure->user->email }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Department</small>
                    <div class="fw-bold">{{ $salaryStructure->salaryDepartment->name }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Role Type</small>
                    <div>
                        <span class="badge bg-info">{{ ucfirst($salaryStructure->salaryDepartment->role_type) }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Status</small>
                    <div>
                        @if($salaryStructure->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Breakdown -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Salary Breakdown</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <small class="text-muted">Basic Salary</small>
                                <h4 class="mb-0">{{ number_format($salaryStructure->basic_salary, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <small class="text-muted">Working Days</small>
                                <h4 class="mb-0">{{ $salaryStructure->working_days }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <small class="text-muted">Punctuality</small>
                                <h4 class="mb-0">{{ number_format($salaryStructure->punctuality, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Allowances -->
                <h6 class="text-success mb-3"><i class="fas fa-plus-circle"></i> Allowances</h6>
                @if($salaryStructure->allowances->count() > 0)
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead class="table-success">
                                <tr>
                                    <th>Component Name</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center">Taxable</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($salaryStructure->punctuality > 0)
                                    <tr>
                                        <td>Punctuality Bonus</td>
                                        <td class="text-end">{{ number_format($salaryStructure->punctuality, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-success">Yes</span>
                                        </td>
                                    </tr>
                                @endif
                                @foreach($salaryStructure->allowances as $allowance)
                                    <tr>
                                        <td>{{ $allowance->component_name }}</td>
                                        <td class="text-end">{{ number_format($allowance->amount, 2) }}</td>
                                        <td class="text-center">
                                            @if($allowance->is_taxable)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-success fw-bold">
                                    <td>Total Allowances</td>
                                    <td class="text-end">{{ number_format($salaryStructure->total_allowances + $salaryStructure->punctuality, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No allowances added</p>
                @endif

                <!-- Deductions -->
                <h6 class="text-danger mb-3"><i class="fas fa-minus-circle"></i> Deductions</h6>
                @if($salaryStructure->deductions->count() > 0)
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead class="table-danger">
                                <tr>
                                    <th>Component Name</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center">Taxable</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salaryStructure->deductions as $deduction)
                                    <tr>
                                        <td>{{ $deduction->component_name }}</td>
                                        <td class="text-end">{{ number_format($deduction->amount, 2) }}</td>
                                        <td class="text-center">
                                            @if($deduction->is_taxable)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-danger fw-bold">
                                    <td>Total Deductions</td>
                                    <td class="text-end">{{ number_format($salaryStructure->total_deductions, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No deductions added</p>
                @endif

                <hr>

                <!-- Summary -->
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="mb-0">Net Salary</h6>
                                <small>Basic + Allowances - Deductions</small>
                            </div>
                            <div class="col-md-4 text-end">
                                <h3 class="mb-0">{{ number_format($salaryStructure->net_salary, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Information -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Additional Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <small class="text-muted">Effective From</small>
                        <div class="fw-bold">{{ $salaryStructure->effective_from->format('d M, Y') }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <small class="text-muted">Effective To</small>
                        <div class="fw-bold">
                            {{ $salaryStructure->effective_to ? $salaryStructure->effective_to->format('d M, Y') : 'Ongoing' }}
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <small class="text-muted">Created By</small>
                        <div>{{ $salaryStructure->creator->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <small class="text-muted">Created At</small>
                        <div>{{ $salaryStructure->created_at->format('d M, Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Button -->
<div class="mt-3">
    <form action="{{ route('salary-structures.destroy', $salaryStructure) }}" method="POST" 
          onsubmit="return confirm('Are you sure you want to delete this salary structure?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash"></i> Delete Salary Structure
        </button>
    </form>
</div>
@endsection
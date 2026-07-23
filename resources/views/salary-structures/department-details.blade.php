@extends('layouts.admin')

@section('title', $department->name . ' - Salary Structures')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('salary-structures.index') }}">Salary Structures</a></li>
            <li class="breadcrumb-item active">{{ $department->name }}</li>
        </ol>
    </nav>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>
            <i class="fas fa-building"></i> {{ $department->name }}
            <span class="badge bg-primary">{{ ucfirst($department->role_type) }}</span>
        </h2>
        <p class="text-muted mb-0">{{ $salaryStructures->count() }} Active Salary Structures</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('salary-structures.create-bulk') }}?department={{ $department->id }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Manage Department
        </a>
        <a href="{{ route('salary-structures.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Employees</h6>
                <h3 class="mb-0">{{ $salaryStructures->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total Basic Salary</h6>
                <h3 class="mb-0">{{ number_format($salaryStructures->sum('basic_salary'), 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Total Allowances</h6>
                <h3 class="mb-0">{{ number_format($salaryStructures->sum(function($s) { return $s->total_allowances + $s->punctuality; }), 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Total Net Salary</h6>
                <h3 class="mb-0">{{ number_format($salaryStructures->sum('net_salary'), 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Salary Structures Table -->
<div class="card">
    <div class="card-body">
        @if($salaryStructures->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Basic Salary</th>
                            <th>Working Days</th>
                            <th>Punctuality</th>
                            <th>Allowances</th>
                            <th>Deductions</th>
                            <th>Gross Salary</th>
                            <th>Net Salary</th>
                            <th>Effective From</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salaryStructures as $structure)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $structure->user->userDetail->full_name ?? $structure->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $structure->user->email }}</small>
                                    </div>
                                </td>
                                <td>{{ number_format($structure->basic_salary, 2) }}</td>
                                <td class="text-center">{{ $structure->working_days }}</td>
                                <td>{{ number_format($structure->punctuality, 2) }}</td>
                                <td>
                                    <span class="text-success">{{ number_format($structure->total_allowances, 2) }}</span>
                                    @if($structure->components->where('component_type', 'allowance')->count() > 0)
                                        <button class="btn btn-sm btn-link p-0" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#allowances-{{ $structure->id }}">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <div class="collapse mt-2" id="allowances-{{ $structure->id }}">
                                            <small>
                                                @foreach($structure->components->where('component_type', 'allowance') as $comp)
                                                    <div>{{ $comp->component_name }}: {{ number_format($comp->amount, 2) }}</div>
                                                @endforeach
                                            </small>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-danger">{{ number_format($structure->total_deductions, 2) }}</span>
                                    @if($structure->components->where('component_type', 'deduction')->count() > 0)
                                        <button class="btn btn-sm btn-link p-0" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#deductions-{{ $structure->id }}">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <div class="collapse mt-2" id="deductions-{{ $structure->id }}">
                                            <small>
                                                @foreach($structure->components->where('component_type', 'deduction') as $comp)
                                                    <div>{{ $comp->component_name }}: {{ number_format($comp->amount, 2) }}</div>
                                                @endforeach
                                            </small>
                                        </div>
                                    @endif
                                </td>
                                <td><strong>{{ number_format($structure->gross_salary, 2) }}</strong></td>
                                <td><strong class="text-primary">{{ number_format($structure->net_salary, 2) }}</strong></td>
                                <td>{{ $structure->effective_from->format('d M Y') }}</td>
                                <td>
                                    @if($structure->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('salary-structures.show', $structure) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('salary-structures.edit', $structure) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('salary-structures.destroy', $structure) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="2">Department Total</td>
                            <td>{{ number_format($salaryStructures->sum('basic_salary'), 2) }}</td>
                            <td>-</td>
                            <td>{{ number_format($salaryStructures->sum('punctuality'), 2) }}</td>
                            <td class="text-success">{{ number_format($salaryStructures->sum('total_allowances'), 2) }}</td>
                            <td class="text-danger">{{ number_format($salaryStructures->sum('total_deductions'), 2) }}</td>
                            <td><strong>{{ number_format($salaryStructures->sum('gross_salary'), 2) }}</strong></td>
                            <td class="text-primary"><strong>{{ number_format($salaryStructures->sum('net_salary'), 2) }}</strong></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Salary Structures Found</h4>
                <p class="text-muted">Create salary structures for employees in this department</p>
                <a href="{{ route('salary-structures.create-bulk') }}?department={{ $department->id }}" 
                   class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Salary Structures
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
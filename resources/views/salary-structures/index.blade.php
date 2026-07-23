@extends('layouts.admin')

@section('title', 'Salary Structures')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Salary Structures by Department</h2>
    <div class="btn-group">
        <a href="{{ route('salary-structures.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Setup Single Salary
        </a>
        <a href="{{ route('salary-structures.create-bulk') }}" class="btn btn-success">
            <i class="fas fa-users"></i> Bulk Setup/Edit
        </a>

        <a href="{{ route('salary-structures.inactive') }}" class="btn btn-warning">
            <i class="fas fa-archive"></i> View Inactive
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@forelse($departments as $department)
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-building"></i> {{ $department->name }}
                        <span class="badge bg-light text-dark ms-2">{{ ucfirst($department->role_type) }}</span>
                    </h5>
                    <small>{{ $department->salary_structures_count }} Active Salary Structures</small>
                </div>
                <div class="text-end">
                    <a href="{{ route('salary-structures.create-bulk') }}?department={{ $department->id }}" 
                       class="btn btn-sm btn-light">
                        <i class="fas fa-edit"></i> Manage Department
                    </a>
                    <button class="btn btn-sm btn-light toggle-dept" data-dept-id="{{ $department->id }}">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card-body dept-details" id="dept-{{ $department->id }}" style="display: none;">
            @if($department->salaryStructures->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Basic Salary</th>
                                <th>Working Days</th>
                                <th>Punctuality</th>
                                <th>Allowances</th>
                                <th>Deductions</th>
                                <th>Net Salary</th>
                                <th>Effective From</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($department->salaryStructures as $structure)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $structure->user->userDetail->full_name ?? $structure->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $structure->user->email }}</small>
                                    </td>
                                    <td>{{ number_format($structure->basic_salary, 2) }}</td>
                                    <td>{{ $structure->working_days }}</td>
                                    <td>{{ number_format($structure->punctuality, 2) }}</td>
                                    <td class="text-success">{{ number_format($structure->total_allowances, 2) }}</td>
                                    <td class="text-danger">{{ number_format($structure->total_deductions, 2) }}</td>
                                    <td><strong>{{ number_format($structure->net_salary, 2) }}</strong></td>
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
                                <td>{{ number_format($department->total_basic, 2) }}</td>
                                <td>-</td>
                                <td>-</td>
                                <td class="text-success">{{ number_format($department->total_allowances, 2) }}</td>
                                <td class="text-danger">{{ number_format($department->total_deductions, 2) }}</td>
                                <td class="text-primary"><strong>{{ number_format($department->total_net, 2) }}</strong></td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No salary structures found for this department</p>
                    <a href="{{ route('salary-structures.create-bulk') }}?department={{ $department->id }}" 
                       class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Salary Structures
                    </a>
                </div>
            @endif
        </div>
    </div>
@empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-building fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No Departments Found</h4>
            <p class="text-muted">Create departments first to setup salary structures</p>
        </div>
    </div>
@endforelse

<!-- Summary Card -->
@if($departments->count() > 0)
    <div class="card bg-light">
        <div class="card-body">
            <h5 class="mb-3">Overall Summary</h5>
            <div class="row text-center">
                <div class="col-md-3">
                    <h3 class="text-primary">{{ $departments->count() }}</h3>
                    <small class="text-muted">Departments</small>
                </div>
                <div class="col-md-3">
                    <h3 class="text-info">{{ $departments->sum('salary_structures_count') }}</h3>
                    <small class="text-muted">Total Employees</small>
                </div>
                <div class="col-md-3">
                    <h3 class="text-success">{{ number_format($departments->sum('total_basic'), 2) }}</h3>
                    <small class="text-muted">Total Basic Salary</small>
                </div>
                <div class="col-md-3">
                    <h3 class="text-primary">{{ number_format($departments->sum('total_net'), 2) }}</h3>
                    <small class="text-muted">Total Net Salary</small>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle department details
    document.querySelectorAll('.toggle-dept').forEach(button => {
        button.addEventListener('click', function() {
            const deptId = this.dataset.deptId;
            const details = document.getElementById('dept-' + deptId);
            const icon = this.querySelector('i');
            
            if (details.style.display === 'none') {
                details.style.display = 'block';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                details.style.display = 'none';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        });
    });

    // Auto-expand if URL has department parameter
    const urlParams = new URLSearchParams(window.location.search);
    const deptParam = urlParams.get('department');
    if (deptParam) {
        const details = document.getElementById('dept-' + deptParam);
        if (details) {
            details.style.display = 'block';
            const button = document.querySelector(`[data-dept-id="${deptParam}"]`);
            if (button) {
                button.querySelector('i').classList.remove('fa-chevron-down');
                button.querySelector('i').classList.add('fa-chevron-up');
            }
            // Scroll to department
            details.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
});
</script>

<style>
.card-header {
    cursor: pointer;
}
.dept-details {
    animation: slideDown 0.3s ease-in-out;
}
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection
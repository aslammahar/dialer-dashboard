@extends('layouts.admin')

@section('title', 'Orphaned Salary Structures')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Orphaned Salary Structures</h2>
        <p class="text-muted mb-0">Salary structures with deleted or missing departments</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('salary-structures.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Active
        </a>
        <button type="button" class="btn btn-danger" id="bulkDeleteBtn" style="display: none;">
            <i class="fas fa-trash-alt"></i> Delete Selected (<span id="selectedCount">0</span>)
        </button>
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

@if($inactiveSalaries->count() > 0)
    <!-- Bulk Actions Form -->
    <form id="bulkDeleteForm" action="{{ route('salary-structures.bulk-delete') }}" method="POST">
        @csrf
        @method('DELETE')
        
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <input type="checkbox" id="selectAll" class="form-check-input me-2">
                        <label for="selectAll" class="form-check-label">
                            <strong>Select All ({{ $inactiveSalaries->count() }} orphaned records)</strong>
                        </label>
                    </div>
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-exclamation-triangle text-danger"></i> Groups: {{ $departments->count() }}
                    </span>
                </div>
            </div>
        </div>

        @forelse($departments as $department)
            <div class="card mb-4">
                <div class="card-header bg-light border-danger border-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <input type="checkbox" class="form-check-input dept-select me-2" data-dept-id="{{ $department->id }}">
                            <strong>
                                @if($department->id === 0)
                                    <i class="fas fa-unlink text-danger"></i> {{ $department->name }}
                                    <span class="badge bg-danger ms-2">NULL DEPARTMENT</span>
                                @else
                                    <i class="fas fa-building text-danger"></i> {{ $department->name }}
                                    <span class="badge bg-danger ms-2">{{ ucfirst($department->role_type) }}</span>
                                    <span class="badge bg-secondary ms-2">DELETED</span>
                                @endif
                            </strong>
                            <small class="text-muted ms-2">
                                {{ $department->salaryStructures->count() }} Record(s)
                                @if($department->id !== 0 && $department->deleted_at)
                                    | Deleted on: {{ $department->deleted_at->format('d M Y') }}
                                @endif
                            </small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary toggle-dept" data-dept-id="{{ $department->id }}">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
                
                <div class="card-body dept-details" id="dept-{{ $department->id }}" style="display: none;">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Warning:</strong> 
                        @if($department->id === 0)
                            These salary structures have no department assigned.
                        @else
                            This department has been deleted. These salary structures are orphaned records.
                        @endif
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input select-dept-all" data-dept-id="{{ $department->id }}">
                                    </th>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Basic Salary</th>
                                    <th>Working Days</th>
                                    <th>Allowances</th>
                                    <th>Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Effective From</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->salaryStructures as $structure)
                                    <tr class="dept-{{ $department->id }}-row">
                                        <td>
                                            <input type="checkbox" 
                                                   class="form-check-input structure-checkbox dept-{{ $department->id }}-checkbox" 
                                                   name="structure_ids[]" 
                                                   value="{{ $structure->id }}">
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $structure->user->userDetail->full_name ?? $structure->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $structure->user->email }}</small>
                                        </td>
                                        <td>{{ number_format($structure->basic_salary, 2) }}</td>
                                        <td>{{ $structure->working_days }}</td>
                                        <td class="text-success">{{ number_format($structure->total_allowances + $structure->punctuality, 2) }}</td>
                                        <td class="text-danger">{{ number_format($structure->total_deductions, 2) }}</td>
                                        <td><strong>{{ number_format($structure->net_salary, 2) }}</strong></td>
                                        <td>{{ $structure->effective_from->format('d M Y') }}</td>
                                        <td>
                                            <span class="text-muted">
                                                {{ $structure->updated_at->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('salary-structures.show', $structure) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger delete-single" 
                                                        data-structure-id="{{ $structure->id }}"
                                                        data-employee-name="{{ $structure->user->userDetail->full_name ?? $structure->user->name }}"
                                                        title="Delete Permanently">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td colspan="3">Group Total</td>
                                    <td>{{ number_format($department->salaryStructures->sum('basic_salary'), 2) }}</td>
                                    <td>-</td>
                                    <td class="text-success">{{ number_format($department->salaryStructures->sum(function($s) { return $s->total_allowances + $s->punctuality; }), 2) }}</td>
                                    <td class="text-danger">{{ number_format($department->salaryStructures->sum('total_deductions'), 2) }}</td>
                                    <td class="text-primary"><strong>{{ number_format($department->salaryStructures->sum('net_salary'), 2) }}</strong></td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4 class="text-muted">No Orphaned Salary Structures</h4>
                    <p class="text-muted">All salary structures have valid departments</p>
                    <a href="{{ route('salary-structures.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Active Structures
                    </a>
                </div>
            </div>
        @endforelse
    </form>

    <!-- Summary Card -->
    <div class="card bg-light border-danger">
        <div class="card-body">
            <h5 class="mb-3">
                <i class="fas fa-exclamation-triangle text-danger"></i> Orphaned Records Summary
            </h5>
            <div class="row text-center">
                <div class="col-md-3">
                    <h3 class="text-danger">{{ $departments->count() }}</h3>
                    <small class="text-muted">Department Groups</small>
                </div>
                <div class="col-md-3">
                    <h3 class="text-warning">{{ $inactiveSalaries->count() }}</h3>
                    <small class="text-muted">Orphaned Structures</small>
                </div>
                <div class="col-md-3">
                    <h3 class="text-muted">{{ number_format($inactiveSalaries->sum('basic_salary'), 2) }}</h3>
                    <small class="text-muted">Total Basic Salary</small>
                </div>
                <div class="col-md-3">
                    <h3 class="text-muted">{{ number_format($inactiveSalaries->sum('net_salary'), 2) }}</h3>
                    <small class="text-muted">Total Net Salary</small>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
            <h4 class="text-muted">No Orphaned Salary Structures</h4>
            <p class="text-muted">All salary structures have valid departments</p>
            <a href="{{ route('salary-structures.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Active Structures
            </a>
        </div>
    </div>
@endif

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Permanent Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage"></p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Warning:</strong> This will PERMANENTLY delete the record(s). This action cannot be undone!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash"></i> Delete Permanently
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Single Delete Form (hidden) -->
<form id="singleDeleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const selectAllCheckbox = document.getElementById('selectAll');
    const structureCheckboxes = document.querySelectorAll('.structure-checkbox');
    const selectedCountSpan = document.getElementById('selectedCount');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteMessage = document.getElementById('deleteMessage');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    let deleteAction = null;

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

    // Update selected count and show/hide bulk delete button
    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.structure-checkbox:checked').length;
        selectedCountSpan.textContent = selectedCount;
        bulkDeleteBtn.style.display = selectedCount > 0 ? 'block' : 'none';
    }

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            structureCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            
            document.querySelectorAll('.dept-select').forEach(deptCheckbox => {
                deptCheckbox.checked = this.checked;
            });
            
            document.querySelectorAll('.select-dept-all').forEach(deptAllCheckbox => {
                deptAllCheckbox.checked = this.checked;
            });
            
            updateSelectedCount();
        });
    }

    // Department select all
    document.querySelectorAll('.dept-select').forEach(deptCheckbox => {
        deptCheckbox.addEventListener('change', function() {
            const deptId = this.dataset.deptId;
            const deptCheckboxes = document.querySelectorAll(`.dept-${deptId}-checkbox`);
            deptCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            
            const selectDeptAll = document.querySelector(`.select-dept-all[data-dept-id="${deptId}"]`);
            if (selectDeptAll) {
                selectDeptAll.checked = this.checked;
            }
            
            updateSelectedCount();
        });
    });

    // Select all within department
    document.querySelectorAll('.select-dept-all').forEach(selectAll => {
        selectAll.addEventListener('change', function() {
            const deptId = this.dataset.deptId;
            const deptCheckboxes = document.querySelectorAll(`.dept-${deptId}-checkbox`);
            deptCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            
            const deptSelect = document.querySelector(`.dept-select[data-dept-id="${deptId}"]`);
            if (deptSelect) {
                deptSelect.checked = this.checked;
            }
            
            updateSelectedCount();
        });
    });

    // Individual checkbox change
    structureCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            
            const allChecked = Array.from(structureCheckboxes).every(cb => cb.checked);
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
            }
        });
    });

    // Bulk delete button click
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const selectedCount = document.querySelectorAll('.structure-checkbox:checked').length;
            deleteMessage.textContent = `Are you sure you want to PERMANENTLY delete ${selectedCount} orphaned salary structure(s)? This action cannot be undone.`;
            deleteAction = 'bulk';
            deleteModal.show();
        });
    }

    // Single delete button click
    document.querySelectorAll('.delete-single').forEach(button => {
        button.addEventListener('click', function() {
            const structureId = this.dataset.structureId;
            const employeeName = this.dataset.employeeName;
            deleteMessage.textContent = `Are you sure you want to PERMANENTLY delete the salary structure for ${employeeName}? This action cannot be undone.`;
            deleteAction = { type: 'single', id: structureId };
            deleteModal.show();
        });
    });

    // Confirm delete
    confirmDeleteBtn.addEventListener('click', function() {
        if (deleteAction === 'bulk') {
            bulkDeleteForm.submit();
        } else if (deleteAction && deleteAction.type === 'single') {
            const form = document.getElementById('singleDeleteForm');
            form.action = `/salary-structures/${deleteAction.id}`;
            form.submit();
        }
        deleteModal.hide();
    });
});
</script>

<style>
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

.structure-checkbox:checked {
    background-color: #dc3545;
    border-color: #dc3545;
}

.table tbody tr:hover {
    background-color: #fff3cd;
}

.border-danger {
    border-left: 4px solid #dc3545 !important;
}
</style>
@endsection
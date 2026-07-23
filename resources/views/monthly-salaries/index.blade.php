@extends('layouts.admin')

@section('title', 'Monthly Salaries')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Monthly Salaries</h2>
    <div>
        <a href="{{ route('monthly-salaries.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Generate Salaries
        </a>

         
        <!-- NEW BUTTON - Add this -->
        <a href="{{ route('monthly-salaries.inactive') }}" class="btn btn-warning">
            <i class="fas fa-archive"></i> View Inactive
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        <h6 class="mb-0"><i class="fas fa-filter"></i> Filter Salaries</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('monthly-salaries.index') }}" class="row g-3">
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
                    @foreach(\App\Models\SalaryDepartment::where('is_active', true)->orderBy('name')->get() as $dept)
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
                    <a href="{{ route('monthly-salaries.index') }}" class="btn btn-secondary" title="Reset">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
        
        @if(request()->has('year') || request()->has('month') || request()->has('salary_department_id'))
            <div class="mt-3">
                <strong>Showing:</strong> 
                <span class="badge bg-primary">{{ $months[request('month', $currentMonth)] }} {{ request('year', $currentYear) }}</span>
                @if(request('salary_department_id'))
                    @php
                        $selectedDept = \App\Models\SalaryDepartment::find(request('salary_department_id'));
                    @endphp
                    @if($selectedDept)
                        <span class="badge bg-info">{{ $selectedDept->name }}</span>
                    @endif
                @else
                    <span class="badge bg-secondary">All Departments</span>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Quick Stats -->
@php
    $approvedCount = $monthlySalaries->where('status', 'approved')->count();
    $draftCount = $monthlySalaries->where('status', 'draft')->count();
    $totalNetSalary = $monthlySalaries->sum('net_salary');
@endphp

@if($monthlySalaries->count() > 0)
<div class="row mb-3">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-check-circle"></i> Approved Salaries</h6>
                <h3 class="mb-0">{{ $approvedCount }}</h3>
                @if($approvedCount > 0)
                    <small>Ready to download</small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-clock"></i> Draft Salaries</h6>
                <h3 class="mb-0">{{ $draftCount }}</h3>
                @if($draftCount > 0)
                    <small>Pending approval</small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-users"></i> Total Employees</h6>
                <h3 class="mb-0">{{ $monthlySalaries->total() }}</h3>
                <small>In this period</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-money-bill-wave"></i> Total Net Salary</h6>
                <h3 class="mb-0">{{ number_format($totalNetSalary, 0) }}</h3>
                <small>PKR</small>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Salaries Table -->
<div class="card">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-list"></i> Salary Records
            @if($monthlySalaries->total() > 0)
                <span class="badge bg-light text-dark">{{ $monthlySalaries->total() }} Records</span>
            @endif
        </h5>
        @if($approvedCount > 0)
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#bulkDownloadModal">
                <i class="fas fa-download"></i> Download All Approved
            </button>
        @endif
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-light">
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Period</th>
                        <th>Basic Salary</th>
                        <th>Days</th>
                        <th>Allowances</th>
                        <th>Deductions</th>
                        <th>Net Salary</th>
                        <th>Status</th>
                        <th width="230">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monthlySalaries as $salary)
                        <tr>
                            <td>
                                <input type="checkbox" 
                                       class="salary-checkbox" 
                                       value="{{ $salary->id }}" 
                                       data-status="{{ $salary->status }}"
                                       {{ $salary->status != 'draft' && $salary->status != 'approved' ? 'disabled' : '' }}>
                            </td>
                            <td>
                                <strong>{{ $salary->user->userDetail->full_name ?? $salary->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $salary->user->email }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $salary->salaryDepartment->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <strong>{{ $months[$salary->month] }}</strong>
                                <br>
                                <small class="text-muted">{{ $salary->year }}</small>
                            </td>
                            <td>PKR {{ number_format($salary->basic_salary, 0) }}</td>
                            <td>
                                <small>
                                    <span class="badge bg-success" title="Present">P: {{ $salary->present_days }}</span>
                                    <span class="badge bg-danger" title="Absent">A: {{ $salary->absent_days }}</span>
                                    <span class="badge bg-warning text-dark" title="Leave">L: {{ $salary->leave_days }}</span>
                                </small>
                            </td>
                            <td>PKR {{ number_format($salary->total_allowances, 0) }}</td>
                            <td>PKR {{ number_format($salary->total_deductions, 0) }}</td>
                            <td><strong class="text-success">PKR {{ number_format($salary->net_salary, 0) }}</strong></td>
                            <td>
                                @if($salary->status == 'draft')
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @elseif($salary->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-info">Paid</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- View Details -->
                                    <a href="{{ route('monthly-salaries.show', $salary) }}" 
                                       class="btn btn-info" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Draft Actions -->
                                    @if($salary->status == 'draft')
                                        <a href="{{ route('monthly-salaries.edit', $salary) }}" 
                                           class="btn btn-warning"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('monthly-salaries.approve', $salary) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to approve this salary?')">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-success" 
                                                    title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Download Slip (Approved Only) -->
                                    @if($salary->status == 'approved')
                                        <a href="{{ route('salary-slips.download', $salary) }}" 
                                           class="btn btn-primary"
                                           title="Download Slip">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">No Salaries Found</h5>
                                <p class="text-muted">No salary records found for the selected period and department</p>
                                <a href="{{ route('monthly-salaries.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Generate Salaries
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($monthlySalaries->count() > 0)
            <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <!-- Bulk Approve (Draft only) -->
                    <button type="button" class="btn btn-success" id="bulkApprove">
                        <i class="fas fa-check-double"></i> Approve Selected
                    </button>
                    
                    <!-- Bulk Download (Approved only) -->
                    <button type="button" class="btn btn-primary" id="bulkDownloadSelected">
                        <i class="fas fa-download"></i> Download Selected
                    </button>
                    
                    <span class="ms-3 text-muted fw-bold" id="selectedCount">0 selected</span>
                </div>
            </div>
        @endif
        
        <div id="paginationLinks" class="d-flex justify-content-center mt-3">
            {{ $monthlySalaries->appends(request()->query())->links('pagination::bootstrap-5')}}
        </div>
    </div>
</div>

<!-- Bulk Download Modal -->
<div class="modal fade" id="bulkDownloadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('salary-slips.bulk-download') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-download"></i> Download All Salary Slips</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="year" value="{{ request('year', $currentYear) }}">
                    <input type="hidden" name="month" value="{{ request('month', $currentMonth) }}">
                    
                    <div class="mb-3">
                        <label class="form-label">Department (Optional)</label>
                        <select name="salary_department_id" class="form-select">
                            <option value="">All Departments</option>
                            @foreach(\App\Models\SalaryDepartment::where('is_active', true)->orderBy('name')->get() as $dept)
                                <option value="{{ $dept->id }}" {{ request('salary_department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i>
                        <strong>On-Demand Generation:</strong> Salary slips will be generated instantly and downloaded as a ZIP file. 
                        Only approved salaries will be included.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download"></i> Download ZIP
                    </button>
                </div>
            </form>
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
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .salary-checkbox:disabled {
        cursor: not-allowed;
    }
    
    #selectedCount {
        font-weight: 600;
        font-size: 1rem;
    }

    .card {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        border: none;
    }
    
    .card-header {
        border-bottom: 2px solid rgba(0,0,0,0.1);
    }
    
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Update selected count
    function updateSelectedCount() {
        const count = $('.salary-checkbox:checked:not(:disabled)').length;
        const draftCount = $('.salary-checkbox:checked[data-status="draft"]').length;
        const approvedCount = $('.salary-checkbox:checked[data-status="approved"]').length;
        
        let text = count + ' selected';
        if (draftCount > 0) text += ` (${draftCount} draft)`;
        if (approvedCount > 0) text += ` (${approvedCount} approved)`;
        
        $('#selectedCount').text(text);
    }

    // Select All
    $('#selectAll').change(function() {
        $('.salary-checkbox:not(:disabled)').prop('checked', $(this).prop('checked'));
        updateSelectedCount();
    });

    // Individual checkbox change
    $('.salary-checkbox').change(function() {
        updateSelectedCount();
        
        const total = $('.salary-checkbox:not(:disabled)').length;
        const checked = $('.salary-checkbox:checked:not(:disabled)').length;
        $('#selectAll').prop('checked', total === checked && total > 0);
    });

    // Bulk Approve (Draft only)
    $('#bulkApprove').click(function() {
        let selectedIds = [];
        $('.salary-checkbox:checked[data-status="draft"]').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Draft Salaries Selected',
                text: 'Please select at least one draft salary to approve',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        Swal.fire({
            title: 'Confirm Approval',
            text: `Are you sure you want to approve ${selectedIds.length} selected ${selectedIds.length === 1 ? 'salary' : 'salaries'}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Approve',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we approve the salaries',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route("monthly-salaries.bulk-approve") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        salary_ids: selectedIds
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Salaries approved successfully',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Error approving salaries';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    });

    // Bulk Download Selected (Approved only)
    $('#bulkDownloadSelected').click(function() {
        let selectedIds = [];
        $('.salary-checkbox:checked[data-status="approved"]').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Approved Salaries Selected',
                text: 'Please select at least one approved salary to download',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Generating PDFs...',
            text: `Please wait while we generate ${selectedIds.length} salary slip(s)`,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Create form and submit
        const form = $('<form>', {
            'method': 'POST',
            'action': '{{ route("salary-slips.bulk-download-selected") }}'
        });

        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': '{{ csrf_token() }}'
        }));

        selectedIds.forEach(id => {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'salary_ids[]',
                'value': id
            }));
        });

        $('body').append(form);
        form.submit();

        // Close loading after 3 seconds
        setTimeout(() => {
            Swal.close();
        }, 3000);
    });

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush
@extends('layouts.admin')

@section('title', 'Salary Slips')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Salary Slips</h2>
    <div>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkDownloadModal">
            <i class="fas fa-download"></i> Bulk Download
        </button>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Salary Slips</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('salary-slips.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Year</label>
                <select name="year" class="form-select" onchange="this.form.submit()">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Month</label>
                <select name="month" class="form-select" onchange="this.form.submit()">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ request('month', $currentMonth) == $num ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Department</label>
                <select name="salary_department_id" class="form-select" onchange="this.form.submit()">
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
                <a href="{{ route('salary-slips.index') }}" class="btn btn-secondary d-block w-100">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Info Alert -->
<div class="alert alert-info alert-dismissible fade show">
    <i class="fas fa-info-circle"></i>
    <strong>Quick Download:</strong> Salary slips are generated instantly when you click download. 
    Filter by department to view specific groups.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Department-wise Summary Cards -->
@if($departmentSummary->count() > 0)
<div class="row mb-3">
    @foreach($departmentSummary as $summary)
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-3">{{ $summary->department_name }}</h6>
                    <h3 class="mb-2">{{ $summary->employee_count }} <small class="text-muted">employees</small></h3>
                    <p class="mb-2">
                        <strong>Total:</strong> PKR {{ number_format($summary->total_salary, 2) }}
                    </p>
                    <a href="{{ route('salary-slips.index', ['salary_department_id' => $summary->department_id, 'year' => request('year', $currentYear), 'month' => request('month', $currentMonth)]) }}" 
                       class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <form action="{{ route('salary-slips.bulk-download') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="year" value="{{ request('year', $currentYear) }}">
                        <input type="hidden" name="month" value="{{ request('month', $currentMonth) }}">
                        <input type="hidden" name="salary_department_id" value="{{ $summary->department_id }}">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-download"></i> Download All
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif

<!-- Salary Slips Table -->
<div class="card">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">
            <i class="fas fa-file-invoice-dollar"></i> 
            @if(request('salary_department_id'))
                {{ \App\Models\SalaryDepartment::find(request('salary_department_id'))->name ?? 'Department' }} - 
            @endif
            Salary Slips ({{ $salaries->total() }})
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Period</th>
                        <th>Days (P/A/L)</th>
                        <th>Net Salary</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaries as $salary)
                        <tr>
                            <td>
                                <input type="checkbox" class="salary-checkbox" value="{{ $salary->id }}">
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
                                <small>{{ $salary->user->userDetail->designation ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $salary->period }}</td>
                            <td>
                                <small>
                                    <span class="badge bg-success">P: {{ $salary->present_days }}</span>
                                    <span class="badge bg-danger">A: {{ $salary->absent_days }}</span>
                                    <span class="badge bg-warning">L: {{ $salary->leave_days }}</span>
                                </small>
                            </td>
                            <td><strong>PKR {{ number_format($salary->net_salary, 2) }}</strong></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('salary-slips.download', $salary) }}" 
                                       class="btn btn-sm btn-success"
                                       title="Download Slip">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="{{ route('monthly-salaries.show', $salary) }}" 
                                       class="btn btn-sm btn-info"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No approved salaries found for the selected filters</p>
                                @if(request('salary_department_id'))
                                    <a href="{{ route('salary-slips.index', ['year' => request('year'), 'month' => request('month')]) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-list"></i> View All Departments
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($salaries->count() > 0)
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div>
                    <button type="button" class="btn btn-success" id="bulkDownloadSelected">
                        <i class="fas fa-download"></i> Download Selected
                    </button>
                    <span class="ms-3 text-muted" id="selectedCount">0 selected</span>
                </div>
                <div>
                    <strong>Total Net Salary: </strong>
                    <span class="text-primary fs-5">PKR {{ number_format($salaries->sum('net_salary'), 2) }}</span>
                </div>
            </div>
        @endif
        
        <div class="mt-3">
            {{ $salaries->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Bulk Download Modal -->
<div class="modal fade" id="bulkDownloadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('salary-slips.bulk-download') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Download Salary Slips</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Year <span class="text-danger">*</span></label>
                        <select name="year" class="form-select" required>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Month <span class="text-danger">*</span></label>
                        <select name="month" class="form-select" required>
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ request('month', $currentMonth) == $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
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
                        Slips will be generated instantly and downloaded as a ZIP file.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download"></i> Download ZIP
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update selected count
    function updateSelectedCount() {
        const count = $('.salary-checkbox:checked').length;
        $('#selectedCount').text(count + ' selected');
    }

    // Select All
    $('#selectAll').change(function() {
        $('.salary-checkbox').prop('checked', $(this).prop('checked'));
        updateSelectedCount();
    });

    // Individual checkbox change
    $('.salary-checkbox').change(function() {
        updateSelectedCount();
        
        const total = $('.salary-checkbox').length;
        const checked = $('.salary-checkbox:checked').length;
        $('#selectAll').prop('checked', total === checked && total > 0);
    });

    // Download Selected
    $('#bulkDownloadSelected').click(function() {
        let selectedIds = [];
        $('.salary-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Selection',
                text: 'Please select at least one salary slip',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Generating PDFs...',
            text: 'Please wait while we generate ' + selectedIds.length + ' salary slip(s)',
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
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);
});
</script>
@endpush

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    .card {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    .badge {
        font-size: 0.75rem;
    }
</style>
@endpush
{{-- resources/views/reporting/data-management.blade.php --}}

@extends('layouts.admin')

@section('title', 'Data Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Data Management</h3>
                    <div class="btn-group">
                        <a href="{{ route('reporting.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                        <a href="{{ route('reporting.upload.form') }}" class="btn btn-success">
                            <i class="fas fa-upload"></i> Upload New Data
                        </a>
                        <button type="button" class="btn btn-warning" onclick="refreshData()">
                            <i class="fas fa-refresh"></i> Refresh
                        </button>
                    </div>
                </div>

                <div class="card-body">
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

                    <!-- Filter Section -->
                    <form method="GET" action="{{ route('reporting.data-management') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="month_filter" class="form-label">Filter by Month:</label>
                                <select name="month" id="month_filter" class="form-select">
                                    <option value="">All Months</option>
                                    @foreach($availableMonths as $month)
                                        <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="employee_filter" class="form-label">Filter by Employee:</label>
                                <select name="employee" id="employee_filter" class="form-select">
                                    <option value="">All Employees</option>
                                    @foreach($availableEmployees as $employee)
                                        <option value="{{ $employee }}" {{ request('employee') == $employee ? 'selected' : '' }}>
                                            {{ $employee }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Apply Filter
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <a href="{{ route('reporting.data-management') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Info -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Summary:</strong>
                                Total Records: <span class="badge bg-primary">{{ $uploadedData->count() }}</span> |
                                Unique Dates: <span class="badge bg-success">{{ $uploadedData->pluck('report_date')->unique()->count() }}</span> |
                                Unique Employees: <span class="badge bg-warning text-dark">{{ $uploadedData->pluck('employee_id')->unique()->count() }}</span>
                                
                                @if(request('month') || request('employee'))
                                    <br><small class="text-muted mt-2 d-block">
                                        <i class="fas fa-filter"></i> 
                                        Filters Applied: 
                                        @if(request('month'))
                                            Month: {{ Carbon\Carbon::parse(request('month') . '-01')->format('F Y') }}
                                        @endif
                                        @if(request('employee'))
                                            @if(request('month')) | @endif
                                            Employee: {{ request('employee') }}
                                        @endif
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-triangle"></i> Bulk Actions (Use with Caution)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-danger w-100" onclick="confirmBulkDelete('all')">
                                                <i class="fas fa-trash-alt"></i> Delete All Records
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            @if(request('month'))
                                                <button type="button" class="btn btn-warning w-100" onclick="confirmBulkDelete('month', '{{ request('month') }}')">
                                                    <i class="fas fa-calendar-times"></i> Delete All Records for {{ Carbon\Carbon::parse(request('month') . '-01')->format('F Y') }}
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-secondary w-100" disabled>
                                                    <i class="fas fa-calendar-times"></i> Select Month to Enable Month Delete
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Employee ID</th>
                                    <th>Employee Name</th>
                                    <th>Talk Time</th>
                                    <th>Total Calls</th>
                                    <th>Avatar Calls</th>
                                    <th>JCs Calls</th>
                                    <th>Recording Samples</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($uploadedData as $record)
                                    <tr>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ Carbon\Carbon::parse($record->report_date)->format('M j, Y') }}
                                            </span>
                                        </td>
                                        <td><strong>{{ $record->employee_id }}</strong></td>
                                        <td>{{ $record->name ?: 'N/A' }}</td>
                                        <td>{{ $record->talktime ?: '0:00:00' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $record->total_avatar_jcs_xfers ?: 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $record->avatar_xfer ?: 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">{{ $record->jcs_xfers ?: 0 }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $hasRec1 = !empty($record->rec_1_200_sec_duration) && $record->rec_1_200_sec_duration !== 'N/A';
                                                $hasRec2 = !empty($record->rec_2_400_sec_duration) && $record->rec_2_400_sec_duration !== 'N/A';
                                                $hasRec3 = !empty($record->rec_3_600_sec_duration) && $record->rec_3_600_sec_duration !== 'N/A';
                                                $recordingCount = ($hasRec1 ? 1 : 0) + ($hasRec2 ? 1 : 0) + ($hasRec3 ? 1 : 0);
                                            @endphp
                                            
                                            @if($recordingCount > 0)
                                                <span class="badge bg-success">{{ $recordingCount }} Recording{{ $recordingCount > 1 ? 's' : '' }}</span>
                                            @else
                                                <span class="badge bg-secondary">No Recordings</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info" onclick="viewRecord({{ $record->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $record->id }}, '{{ $record->employee_id }}', '{{ $record->report_date }}')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="alert alert-warning mb-0">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>No data records found</strong>
                                                @if(request('month') || request('employee'))
                                                    <br><small class="text-muted">Try adjusting your filters or clearing them to see all records</small>
                                                @else
                                                    <br><small class="text-muted">Upload some data to see records here</small>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($uploadedData->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $uploadedData->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Record Modal -->
<div class="modal fade" id="viewRecordModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="recordDetails">
                <!-- Content loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This action cannot be undone.
                </div>
                <p>Are you sure you want to delete this record?</p>
                <div id="deleteDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt"></i> Delete Record
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Bulk Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>DANGER:</strong> This will permanently delete multiple records and cannot be undone.
                </div>
                <p id="bulkDeleteMessage"></p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmBulkCheckbox">
                    <label class="form-check-label" for="confirmBulkCheckbox">
                        I understand this action cannot be undone
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmBulkDeleteBtn" disabled>
                    <i class="fas fa-trash-alt"></i> Delete Records
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
let deleteRecordId = null;
let bulkDeleteType = null;
let bulkDeleteValue = null;

function viewRecord(recordId) {
    document.getElementById('recordDetails').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    
    const modal = new bootstrap.Modal(document.getElementById('viewRecordModal'));
    modal.show();
    
    fetch(`{{ route('reporting.data-management.view', '') }}/${recordId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('recordDetails').innerHTML = formatRecordDetails(data.record);
            } else {
                document.getElementById('recordDetails').innerHTML = '<div class="alert alert-danger">Error loading record details</div>';
            }
        })
        .catch(error => {
            document.getElementById('recordDetails').innerHTML = '<div class="alert alert-danger">Error loading record details</div>';
        });
}

function formatRecordDetails(record) {
    return `
        <div class="row">
            <div class="col-md-6">
                <h6>Basic Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Employee ID:</strong></td><td>${record.employee_id}</td></tr>
                    <tr><td><strong>Name:</strong></td><td>${record.name || 'N/A'}</td></tr>
                    <tr><td><strong>Report Date:</strong></td><td>${record.report_date}</td></tr>
                    <tr><td><strong>Working Days:</strong></td><td>${record.working_days || 0}</td></tr>
                </table>
                
                <h6>Call Statistics</h6>
                <table class="table table-sm">
                    <tr><td><strong>Talk Time:</strong></td><td>${record.talktime || '0:00:00'}</td></tr>
                    <tr><td><strong>Avg Talk Time:</strong></td><td>${record.avg_talktime || '0:00:00'}</td></tr>
                    <tr><td><strong>Total Calls:</strong></td><td>${record.total_avatar_jcs_xfers || 0}</td></tr>
                    <tr><td><strong>Avatar Calls:</strong></td><td>${record.avatar_xfer || 0}</td></tr>
                    <tr><td><strong>JCs Calls:</strong></td><td>${record.jcs_xfers || 0}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Call Duration Categories</h6>
                <table class="table table-sm">
                    <tr><td><strong>&lt; 200 secs:</strong></td><td>${record.calls_dur_less_than_200_secs || 0}</td></tr>
                    <tr><td><strong>200-400 secs:</strong></td><td>${record.calls_dur_between_200_400_secs || 0}</td></tr>
                    <tr><td><strong>&gt; 400 secs:</strong></td><td>${record.calls_dur_greater_than_400_secs || 0}</td></tr>
                </table>
                
                <h6>Recording Samples</h6>
                <div class="mb-2">
                    <strong>Rec 1 (200s):</strong><br>
                    ${record.rec_1_200_sec_duration ? `<small class="text-break">${record.rec_1_200_sec_duration}</small>` : '<small class="text-muted">No recording</small>'}
                </div>
                <div class="mb-2">
                    <strong>Rec 2 (400s):</strong><br>
                    ${record.rec_2_400_sec_duration ? `<small class="text-break">${record.rec_2_400_sec_duration}</small>` : '<small class="text-muted">No recording</small>'}
                </div>
                <div class="mb-2">
                    <strong>Rec 3 (600s):</strong><br>
                    ${record.rec_3_600_sec_duration ? `<small class="text-break">${record.rec_3_600_sec_duration}</small>` : '<small class="text-muted">No recording</small>'}
                </div>
            </div>
        </div>
    `;
}

function confirmDelete(recordId, employeeId, reportDate) {
    deleteRecordId = recordId;
    
    document.getElementById('deleteDetails').innerHTML = `
        <strong>Employee ID:</strong> ${employeeId}<br>
        <strong>Report Date:</strong> ${reportDate}
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function confirmBulkDelete(type, value = null) {
    bulkDeleteType = type;
    bulkDeleteValue = value;
    
    let message = '';
    if (type === 'all') {
        message = 'Are you sure you want to delete ALL records? This will remove all uploaded data from the system.';
    } else if (type === 'month') {
        const monthName = new Date(value + '-01').toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        message = `Are you sure you want to delete all records for ${monthName}?`;
    }
    
    document.getElementById('bulkDeleteMessage').innerHTML = message;
    
    // Reset checkbox
    document.getElementById('confirmBulkCheckbox').checked = false;
    document.getElementById('confirmBulkDeleteBtn').disabled = true;
    
    const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
    modal.show();
}

// Handle delete confirmation
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteRecordId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('reporting.data-management.delete', '') }}/${deleteRecordId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
});

// Handle bulk delete confirmation
document.getElementById('confirmBulkDeleteBtn').addEventListener('click', function() {
    if (bulkDeleteType && document.getElementById('confirmBulkCheckbox').checked) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('reporting.data-management.bulk-delete') }}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const typeField = document.createElement('input');
        typeField.type = 'hidden';
        typeField.name = 'type';
        typeField.value = bulkDeleteType;
        
        form.appendChild(csrfToken);
        form.appendChild(typeField);
        
        if (bulkDeleteValue) {
            const valueField = document.createElement('input');
            valueField.type = 'hidden';
            valueField.name = 'value';
            valueField.value = bulkDeleteValue;
            form.appendChild(valueField);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
});

// Enable/disable bulk delete button based on checkbox
document.getElementById('confirmBulkCheckbox').addEventListener('change', function() {
    document.getElementById('confirmBulkDeleteBtn').disabled = !this.checked;
});

function refreshData() {
    location.reload();
}
</script>

@endsection
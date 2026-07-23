{{-- resources/views/reporting/upload.blade.php --}}

@extends('layouts.admin')

@section('title', 'Upload Excel File')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Upload Excel File</h4>
                    <a href="{{ route('reporting.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Reporting
                    </a>
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

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('reporting.upload.excel') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="report_date" class="form-label">Report Date *</label>
                            <input type="date" 
                                   name="report_date" 
                                   id="report_date" 
                                   class="form-control @error('report_date') is-invalid @enderror"
                                   value="{{ old('report_date', date('Y-m-d')) }}"
                                   required>
                            @error('report_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Select the date for this report data. If data exists for this date, it will be added to existing records.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="file_type" class="form-label">File Type *</label>
                            <select name="file_type" id="file_type" class="form-select @error('file_type') is-invalid @enderror" required>
                                <option value="">Select file type...</option>
                                <option value="talktime" {{ old('file_type') == 'talktime' ? 'selected' : '' }}>
                                    Talktime Data (AGENT, CALLS, TIME H:M:S, AVERAGE)
                                </option>
                                <option value="avatar" {{ old('file_type') == 'avatar' ? 'selected' : '' }}>
                                    Avatar Export (call_date, phone_number_dialed, user, length_in_sec, recording_location)
                                </option>
                                <option value="jcs" {{ old('file_type') == 'jcs' ? 'selected' : '' }}>
                                    JCs Export (Jr/Sr Closer Export - Count Only)
                                </option>
                                <option value="sales" {{ old('file_type') == 'sales' ? 'selected' : '' }}>
                                    Sales Data (Coming Soon)
                                </option>
                                <option value="duration" {{ old('file_type') == 'duration' ? 'selected' : '' }}>
                                    Duration Data (Coming Soon)
                                </option>
                            </select>
                            @error('file_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" id="fileTypeHelp"></div>
                        </div>

                        <div class="mb-3">
                            <label for="excel_file" class="form-label">Excel File *</label>
                            <input type="file" 
                                   name="excel_file" 
                                   id="excel_file" 
                                   class="form-control @error('excel_file') is-invalid @enderror"
                                   accept=".xlsx,.xls,.csv,.txt"
                                   required>
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Accepted formats: .xlsx, .xls, .csv (Max size: 10MB)<br>
                                <small class="text-muted">Note: Some CSV files may appear as .txt files - this is normal and will work.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">File Format Guidelines</h6>
                                </div>
                                <div class="card-body" id="formatGuidelines">
                                    <p class="text-muted">Please select a file type to see the expected format.</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-secondary me-md-2" onclick="window.history.back()">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-warning me-md-2" onclick="debugUpload()">
                                🐛 Debug File
                            </button>
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                <i class="fas fa-upload"></i> Upload & Process
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Progress Modal -->
            <div class="modal fade" id="progressModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Processing Excel file... Please wait.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileTypeSelect = document.getElementById('file_type');
    const fileTypeHelp = document.getElementById('fileTypeHelp');
    const formatGuidelines = document.getElementById('formatGuidelines');
    const uploadForm = document.getElementById('uploadForm');
    const progressModal = new bootstrap.Modal(document.getElementById('progressModal'));

    // File type descriptions and format guidelines
    const fileTypeInfo = {
        talktime: {
            help: 'Upload file with agent talktime statistics',
            format: `
                <h6>Expected Format:</h6>
                <div class="alert alert-info">
                    <small><strong>CSV Structure:</strong></small>
                    <ul class="list-unstyled mb-0 mt-2">
                        <li>Row 1: "AGENT STATS" (title - will be ignored)</li>
                        <li>Row 2: Headers - "AGENT", "CALLS", "TIME H:M:S", "AVERAGE"</li>
                        <li>Row 3+: Data rows like "EMP0000137 - Daniel Garcia", "4", "0:33:02", "0:08:16"</li>
                    </ul>
                </div>
                <div class="alert alert-warning">
                    <small><i class="fas fa-info-circle"></i> Employee IDs must start with EMP or SLZ to be processed.</small>
                </div>
            `,
        },
        avatar: {
            help: 'Upload Avatar Export file with call details and durations',
            format: `
                <h6>Expected Format:</h6>
                <div class="alert alert-info">
                    <small><strong>Excel/CSV Structure:</strong></small>
                    <ul class="list-unstyled mb-0 mt-2">
                        <li><strong>Headers:</strong> call_date, phone_number_dialed, status, user, full_name, ..., length_in_sec, ..., recording_location</li>
                        <li><strong>Key Fields:</strong> user (closer identifier), length_in_sec (call duration), recording_location</li>
                        <li><strong>Processing:</strong> Groups calls by user, categorizes by duration, picks random recordings</li>
                    </ul>
                </div>
                <div class="alert alert-success">
                    <small><strong>What it populates:</strong></small>
                    <ul class="list-unstyled mb-0 mt-1">
                        <li>• Avatar Xfer: Total call count</li>
                        <li>• Duration breakdowns: < 200s, 200-400s, > 400s</li>
                        <li>• Recording samples: Random recordings for each duration category</li>
                    </ul>
                </div>
            `,
        },
        jcs: {
            help: 'Upload JCs Export file (Jr/Sr Closer Export) - counts total calls only',
            format: `
                <h6>Expected Format:</h6>
                <div class="alert alert-info">
                    <small><strong>Excel/CSV Structure:</strong></small>
                    <ul class="list-unstyled mb-0 mt-2">
                        <li><strong>Headers:</strong> Any headers containing user/agent/employee/closer identifier</li>
                        <li><strong>Key Field:</strong> user (closer identifier column)</li>
                        <li><strong>Processing:</strong> Simply counts total records per closer</li>
                    </ul>
                </div>
                <div class="alert alert-primary">
                    <small><strong>What it populates:</strong></small>
                    <ul class="list-unstyled mb-0 mt-1">
                        <li>• <strong>JCs Xfers:</strong> Total count of records for each closer</li>
                        <li>• <em>Note:</em> No duration categorization or recordings - just the total count</li>
                    </ul>
                </div>
                <div class="alert alert-warning">
                    <small><i class="fas fa-lightbulb"></i> <strong>Tip:</strong> This works exactly like Avatar Export but only stores the count in "JCs Xfers" field. All other processing (duration categories, recordings) is skipped.</small>
                </div>
            `,
        },
        sales: {
            help: 'Upload file with sales data (Feature coming soon)',
            format: `
                <div class="alert alert-warning">
                    <i class="fas fa-construction"></i> Sales data processing is under development.
                    <br><small>This will include submitted sales, approved sales, conversion rates, etc.</small>
                </div>
            `,
        },
        duration: {
            help: 'Upload file with call duration statistics (Feature coming soon)',
            format: `
                <div class="alert alert-warning">
                    <i class="fas fa-construction"></i> Duration data processing is under development.
                    <br><small>This will include call duration breakdowns (< 200s, 200-400s, > 400s), etc.</small>
                </div>
            `,
        }
    };

    // Update help text and format guidelines when file type changes
    fileTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        
        if (selectedType && fileTypeInfo[selectedType]) {
            fileTypeHelp.textContent = fileTypeInfo[selectedType].help;
            formatGuidelines.innerHTML = fileTypeInfo[selectedType].format;
        } else {
            fileTypeHelp.textContent = '';
            formatGuidelines.innerHTML = '<p class="text-muted">Please select a file type to see the expected format.</p>';
        }
    });

    // Show progress modal on form submit
    uploadForm.addEventListener('submit', function(e) {
        const fileInput = document.getElementById('excel_file');
        const fileTypeInput = document.getElementById('file_type');
        
        if (fileInput.files.length > 0 && fileTypeInput.value) {
            progressModal.show();
        }
    });
});

// Debug function to test file upload
function debugUpload() {
    const fileInput = document.getElementById('excel_file');
    const fileTypeInput = document.getElementById('file_type');
    const reportDateInput = document.getElementById('report_date');
    
    if (!fileInput.files.length) {
        alert('Please select a file first');
        return;
    }
    
    if (!fileTypeInput.value) {
        alert('Please select a file type');
        return;
    }
    
    const formData = new FormData();
    formData.append('excel_file', fileInput.files[0]);
    formData.append('file_type', fileTypeInput.value);
    formData.append('report_date', reportDateInput.value);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch('/reporting/debug-upload', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Show debug info in a modal or console
        console.log('Debug Info:', data);
        
        // Create a popup with debug info
        const debugWindow = window.open('', 'debug', 'width=800,height=600');
        debugWindow.document.write(`
            <html>
                <head><title>File Debug Info</title></head>
                <body>
                    <h2>File Debug Information</h2>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                </body>
            </html>
        `);
    })
    .catch(error => {
        console.error('Debug error:', error);
        alert('Debug request failed: ' + error.message);
    });
}
</script>
@endpush
@endsection
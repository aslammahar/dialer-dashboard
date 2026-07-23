@extends('layouts.admin')

@section('page-title')
    {{ __('View Closed Call Details') }}
@endsection

@section('content')
<div class="create-link mb-4">
    <a href="{{ route('closers.stats') }}" class="btn btn-primary">View Closers Stats</a>
</div>

<div class="container mt-4">
    <!-- Search and Filter Form -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🔍 Search & Filter Options</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('closer.salesagentshow') }}" id="filterForm" class="row g-3">
                        <!-- Search Input -->
                        <div class="col-md-4">
                            <label for="search" class="form-label">
                                <i class="fas fa-search"></i> Search
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search by customer name, state, closer, status, etc...">
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-2">
                            <label for="start_date" class="form-label">
                                <i class="fas fa-calendar-alt"></i> Start Date
                            </label>
                            <input type="date" 
                                   class="form-control" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ request('start_date') }}">
                        </div>

                        <!-- End Date -->
                        <div class="col-md-2">
                            <label for="end_date" class="form-label">
                                <i class="fas fa-calendar-alt"></i> End Date
                            </label>
                            <input type="date" 
                                   class="form-control" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ request('end_date') }}">
                        </div>

                        <!-- Records per page -->
                        <div class="col-md-2">
                            <label for="per_page" class="form-label">
                                <i class="fas fa-list"></i> Records per page
                            </label>
                            <select class="form-select" id="per_page" name="per_page">
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                                <option value="500" {{ request('per_page') == '500' ? 'selected' : '' }}>500</option>
                                <option value="1000" {{ request('per_page') == '1000' ? 'selected' : '' }}>1000</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="btn-group w-100" role="group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="{{ route('closer.salesagentshow') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Export Section -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <div>
                                    <strong>📥 Export Data:</strong>
                                    <span class="text-muted">Export current filtered results to Excel</span>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-success" onclick="exportData()">
                                        <i class="fas fa-file-excel"></i> Export to Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Info -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">
                        Showing {{ $closedCalls->firstItem() ?? 0 }} to {{ $closedCalls->lastItem() ?? 0 }} 
                        of {{ $closedCalls->total() }} results
                        @if(request('search'))
                            for "<strong>{{ request('search') }}</strong>"
                        @endif
                        @if(request('start_date') || request('end_date'))
                            <br>
                            <small class="text-info">
                                📅 Date Filter: 
                                @if(request('start_date') && request('end_date'))
                                    {{ request('start_date') }} to {{ request('end_date') }}
                                @elseif(request('start_date'))
                                    From {{ request('start_date') }}
                                @elseif(request('end_date'))
                                    Until {{ request('end_date') }}
                                @endif
                            </small>
                        @endif
                    </span>
                </div>
                <div>
                    <span class="badge bg-info">Pending This Month: {{ $pendingCount }}</span>
                    <span class="badge bg-secondary ms-2">🕐 Pakistan Time (PKT)</span>
                    @if($closedCalls->total() > 0)
                        <span class="badge bg-success ms-2">📊 Total Records: {{ $closedCalls->total() }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Table Container with Fixed Header -->
    <div class="table-container">
        <div class="table-wrapper">
            <table id="closedCallsTable" class="table table-bordered table-striped align-middle sticky-header-table">
                <thead class="sticky-header">
                    <tr>
                        <th>Time (PKT)</th>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>State</th>
                        <th>Insurance</th>
                        <th>Closer</th>
                        <th>Client Comments</th>
                        <th>Underwriter</th>
                        <th>Status</th>
                        <th>Center Name</th>
                        <th>Junior Closer</th>
                        <th>Team Name</th>
                        <th>Agent Name</th>
                        <th>Lead Id</th>
                        <th>Dialer</th>
                        <th>List 1</th>
                        <th>List 2</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($closedCalls as $closedCall)
                        <tr>
                            <td class="time-column">
                                <div class="time-display">
                                    <strong>US Time : <br> {{ $closedCall->created_at->setTimezone('America/Los_Angeles') }}</strong>
                                    <small class="d-block text-muted">Pak Time : {{ $closedCall->created_at->setTimezone('Asia/Karachi')->format('h:i A') }}</small>
                                </div>
                            </td>
                            <td>{{ $closedCall->id }}</td>
                            <td>{{ $closedCall->customer_full_name ?? 'N/A' }}</td>
                            <td>{{ $closedCall->state ?? 'N/A' }}</td>
                            <td>
                                <strong>Premium:</strong> ${{ $closedCall->monthly_premium }}<br>
                                <strong>Plan:</strong> {{ $closedCall->coverage_plan }}<br>
                                <strong>Eligibility:</strong> {{ $closedCall->customer_eligibility }}<br>
                                <strong>Carrier:</strong> {{ $closedCall->carrier ?? 'N/A' }}<br>
                                <strong>Initial draft date:</strong> {{ $closedCall->initial_draft_date ? $closedCall->initial_draft_date->format('F j, Y') : 'N/A' }}<br>
                                <strong>Future draft date:</strong> {{ $closedCall->future_draft_date ? $closedCall->future_draft_date->format('F j, Y') : 'N/A' }}
                            </td>
                            <td>{{ $closedCall->closername ?? 'N/A'}}</td>
                            <td>{{ $closedCall->clients_comment ?? 'N/A' }}</td>
                            <td>{{ $closedCall->client->name ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $closedCall->status ?? 'unknown')) }}">
                                    {{ $closedCall->status ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $closedCall->center_name ?? 'N/A' }}</td>
                            <td>{{ $closedCall->juniorcloser->name ?? $closedCall->junior_closer_name }}</td>
                            <td>{{ $closedCall->teamname ?? 'N/A' }}</td>
                            <td>{{ $closedCall->agentname ?? 'N/A' }}</td>
                            <td>{{ $closedCall->lead_id ?? 'N/A' }}</td>
                            <td>{{ $closedCall->dialer_name_new ?? 'N/A' }}</td>
                            <td>{{ $closedCall->list_id_1 ?? 'N/A' }}</td>
                            <td>{{ $closedCall->list_id_2 ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="17" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Records Found</h5>
                                    @if(request('search') || request('start_date') || request('end_date'))
                                        <p class="text-muted">No records found matching your search criteria.</p>
                                        <a href="{{ route('closer.salesagentshow') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-refresh"></i> Clear Filters
                                        </a>
                                    @else
                                        <p class="text-muted">No sales agent records available.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Links with Custom Styles -->
    @if($closedCalls->hasPages())
        <div class="d-flex justify-content-center mt-3">
            <nav>
                <ul class="pagination">
                    {{ $closedCalls->links('pagination::bootstrap-5') }}
                </ul>
            </nav>
        </div>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
$(document).ready(function() {
    // Auto-submit form when per_page changes
    $('#per_page').change(function() {
        $(this).closest('form').submit();
    });

    // Enable Enter key submission for search
    $('#search, #start_date, #end_date').keypress(function(e) {
        if (e.which == 13) {
            $(this).closest('form').submit();
        }
    });

    // Date validation
    $('#start_date, #end_date').change(function() {
        validateDateRange();
    });

    // Add tooltip showing exact time on hover
    $('.time-display').each(function() {
        var $this = $(this);
        var row = $this.closest('tr');
        var createdAt = '{{ $closedCall->created_at ?? "" }}';
        
        if (createdAt) {
            $this.attr('title', 'Exact time: ' + createdAt);
            $this.css('cursor', 'help');
        }
    });

    // Quick date range buttons
    addQuickDateButtons();
});

// Export function
function exportData() {
    // Show loading state
    var exportBtn = $('button[onclick="exportData()"]');
    var originalText = exportBtn.html();
    exportBtn.html('<i class="fas fa-spinner fa-spin"></i> Exporting...').prop('disabled', true);

    // Get current form data
    var formData = $('#filterForm').serialize();
    
    // Create export URL
    var exportUrl = '{{ route("sales.agent.export") }}?' + formData;
    
    // Create temporary link and click it
    var link = document.createElement('a');
    link.href = exportUrl;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Reset button after a delay
    setTimeout(function() {
        exportBtn.html(originalText).prop('disabled', false);
    }, 2000);
}

// Date range validation
function validateDateRange() {
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    
    if (startDate && endDate) {
        if (new Date(startDate) > new Date(endDate)) {
            alert('Start date cannot be later than end date.');
            $('#end_date').val('');
            return false;
        }
    }
    return true;
}

// Add quick date range buttons
function addQuickDateButtons() {
    var quickDateHtml = `
        <div class="row mt-2">
            <div class="col-md-12">
                <small class="text-muted">Quick Date Ranges:</small>
                <div class="btn-group btn-group-sm ms-2" role="group">
                    <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('today')">Today</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('yesterday')">Yesterday</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('thisWeek')">This Week</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('lastWeek')">Last Week</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('thisMonth')">This Month</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('lastMonth')">Last Month</button>
                </div>
            </div>
        </div>
    `;
    
    $('#filterForm').append(quickDateHtml);
}

// Set date range based on quick selection
function setDateRange(range) {
    var today = new Date();
    var startDate, endDate;
    
    switch(range) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'yesterday':
            var yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate = endDate = yesterday.toISOString().split('T')[0];
            break;
        case 'thisWeek':
            var firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
            var lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 6));
            startDate = firstDay.toISOString().split('T')[0];
            endDate = lastDay.toISOString().split('T')[0];
            break;
        case 'lastWeek':
            var lastWeekStart = new Date(today.setDate(today.getDate() - today.getDay() - 7));
            var lastWeekEnd = new Date(today.setDate(today.getDate() - today.getDay() - 1));
            startDate = lastWeekStart.toISOString().split('T')[0];
            endDate = lastWeekEnd.toISOString().split('T')[0];
            break;
        case 'thisMonth':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
            break;
        case 'lastMonth':
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), today.getMonth(), 0).toISOString().split('T')[0];
            break;
    }
    
    $('#start_date').val(startDate);
    $('#end_date').val(endDate);
}
</script>

<style>
    /* Enhanced Card Styling */
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom: none;
    }

    .card-header .card-title {
        margin: 0;
        font-weight: 600;
    }

    /* Form Enhancements */
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-label i {
        color: #6c757d;
        margin-right: 0.25rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }

    /* Export Section Styling */
    .bg-light {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        border: 1px solid #dee2e6;
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
    }

    /* Quick Date Buttons */
    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: #fff;
    }

    /* Empty State Styling */
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
    }

    .empty-state i {
        opacity: 0.3;
    }

    /* Table Container Styles */
    .table-container {
        position: relative;
        max-height: 70vh;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background: white;
    }

    .table-wrapper {
        position: relative;
        min-height: 200px;
    }

    /* Sticky Header Styles */
    .sticky-header-table {
        margin-bottom: 0;
    }

    .sticky-header-table thead.sticky-header {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #1a1a1a !important;
        color: #ffffff !important;
        font-weight: bold !important;
    }

    .sticky-header-table thead.sticky-header th {
        background-color: #1a1a1a !important;
        color: #ffffff !important;
        font-weight: bold !important;
        border-top: none;
        border-bottom: 2px solid #000;
        padding: 12px 8px;
        white-space: nowrap;
        min-width: 120px;
    }

    /* Time Column Specific Styling */
    .time-column {
        min-width: 140px !important;
        max-width: 160px !important;
        white-space: nowrap !important;
        text-align: center !important;
        vertical-align: middle !important;
        background-color: #f8f9fa !important;
        border-right: 2px solid #dee2e6 !important;
    }

    .time-display {
        padding: 4px;
        border-radius: 4px;
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: 1px solid #90caf9;
        transition: all 0.2s ease;
    }

    .time-display:hover {
        background: linear-gradient(135deg, #bbdefb 0%, #90caf9 100%);
        transform: scale(1.02);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .time-display strong {
        color: #1565c0;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .time-display small {
        color: #424242 !important;
        font-size: 0.75rem;
        font-weight: 500;
        margin-top: 2px;
    }

    /* Status Badge Styling */
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d1edff; color: #0c5460; }
    .status-rejected { background: #f8d7da; color: #721c24; }
    .status-need-to-reach { background: #e2e3e5; color: #383d41; }
    .status-nsf { background: #f5c6cb; color: #721c24; }
    .status-charge-back { background: #ffeaa7; color: #6c5ce7; }
    .status-cancelled { background: #ffcccc; color: #d63031; }
    .status-dnc { background: #ddd6fe; color: #5b21b6; }
    .status-underwriting { background: #bfdbfe; color: #1e40af; }
    .status-funded { background: #bbf7d0; color: #047857; }
    .status-unknown { background: #f1f3f4; color: #5f6368; }

    /* Table Body Styles */
    .sticky-header-table tbody tr {
        background-color: white;
        height: auto !important;
    }

    .sticky-header-table tbody tr:hover {
        background-color: #f0f0f0 !important;
    }

    .sticky-header-table tbody tr:hover .time-column {
        background-color: #e3f2fd !important;
    }

    .sticky-header-table tbody td {
        padding: 8px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }

    /* Client Comment Column Specific Styling */
    .sticky-header-table tbody td:nth-child(7), 
    .sticky-header-table thead th:nth-child(7) {
        min-width: 600px !important;
        max-width: 800px !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow: visible !important;
        text-overflow: clip !important;
        vertical-align: top !important;
        line-height: 1.4 !important;
        padding: 12px 8px !important;
    }

    .sticky-header-table tbody td:nth-child(7) {
        font-size: 0.85rem;
        color: #333;
        background-color: #f8f9fa;
    }

    .sticky-header-table thead th:nth-child(7) {
        text-align: center !important;
        font-weight: bold !important;
        background-color: #1a1a1a !important;
        color: #ffffff !important;
    }

    /* Insurance Column Styling */
    .sticky-header-table tbody td:nth-child(5), 
    .sticky-header-table thead th:nth-child(5) {
        min-width: 300px !important;
        max-width: 600px !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow: visible !important;
        text-overflow: clip !important;
        vertical-align: top !important;
        line-height: 1.4 !important;
        padding: 12px 8px !important;
    }

    .sticky-header-table tbody td:nth-child(5) {
        font-size: 0.85rem;
        color: #333;
        background-color: #f8f9fa;
    }

    .sticky-header-table thead th:nth-child(5) {
        text-align: center !important;
        font-weight: bold !important;
        background-color: #1a1a1a !important;
        color: #ffffff !important;
    }

    /* Badge Styling */
    .badge {
        font-size: 0.75rem;
        padding: 6px 10px;
        border-radius: 12px;
    }

    .bg-secondary {
        background-color: #6c757d !important;
    }

    .bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }

    .bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%) !important;
    }

    /* Scrollbar Styling */
    .table-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Pagination Styles */
    .pagination .page-item .page-link {
        color: #333;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
    }

    .pagination .page-item .page-link:hover {
        color: #fff;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: #fff;
    }

    /* Button Group Enhancements */
    .btn-group .btn {
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border: none;
        box-shadow: 0 2px 4px rgba(108, 117, 125, 0.3);
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #5a6268 0%, #3d4043 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.4);
    }

    /* Loading Animation */
    .fa-spin {
        animation: fa-spin 1s infinite linear;
    }

    @keyframes fa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .table-container {
            max-height: 60vh;
        }
        
        .sticky-header-table thead.sticky-header th {
            min-width: 100px;
            font-size: 0.875rem;
            padding: 8px 4px;
        }
        
        .sticky-header-table tbody td {
            font-size: 0.875rem;
            padding: 6px 4px;
            max-width: 150px;
        }

        .time-column {
            min-width: 120px !important;
            max-width: 140px !important;
        }

        .time-display strong {
            font-size: 0.8rem;
        }

        .time-display small {
            font-size: 0.7rem;
        }

        .sticky-header-table tbody td:nth-child(7), 
        .sticky-header-table thead th:nth-child(7) {
            min-width: 250px !important;
            max-width: 300px !important;
            font-size: 0.8rem !important;
        }

        .sticky-header-table tbody td:nth-child(5), 
        .sticky-header-table thead th:nth-child(5) {
            min-width: 200px !important;
            max-width: 250px !important;
            font-size: 0.8rem !important;
        }

        .btn-group {
            flex-direction: column;
        }

        .btn-group .btn {
            border-radius: 0.375rem !important;
            margin-bottom: 0.25rem;
        }
    }

    @media (max-width: 576px) {
        .time-column {
            min-width: 100px !important;
            max-width: 120px !important;
        }

        .sticky-header-table tbody td:nth-child(7), 
        .sticky-header-table thead th:nth-child(7) {
            min-width: 200px !important;
            max-width: 250px !important;
            font-size: 0.75rem !important;
            padding: 8px 4px !important;
        }

        .sticky-header-table tbody td:nth-child(5), 
        .sticky-header-table thead th:nth-child(5) {
            min-width: 150px !important;
            max-width: 200px !important;
            font-size: 0.75rem !important;
        }

        .card-body {
            padding: 1rem;
        }

        .form-label {
            font-size: 0.875rem;
        }

        .btn-group-sm .btn {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
    }

    /* Additional Enhancements */
    .table-responsive {
        border: none;
        padding: 0;
    }

    .table-container.loading {
        opacity: 0.7;
        pointer-events: none;
    }

    .table-container.loading::after {
        content: "Loading...";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.9);
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: bold;
        z-index: 100;
    }

    /* Filter Summary Styling */
    .text-info {
        font-weight: 500;
    }

    /* Card hover effects */
    .card {
        transition: all 0.3s ease;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
</style>

@endsection
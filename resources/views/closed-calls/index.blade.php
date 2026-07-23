@extends('layouts.admin')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Closed Calls Management')}}</li>
@endsection

@section('content')
<style>
    .stats-card {
        transition: transform 0.2s;
        border-left: 4px solid;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .stats-card.total { border-left-color: #3b82f6; }
    .stats-card.pending { border-left-color: #f59e0b; }
    .stats-card.approved { border-left-color: #10b981; }
    .stats-card.rejected { border-left-color: #ef4444; }
    .stats-card.funded { border-left-color: #8b5cf6; }
    .stats-card.charged { border-left-color: #ec4899; }
    
    .badge-status {
        padding: 0.35em 0.65em;
        font-size: 0.85em;
    }
    
    .status-pending { background-color: #fef3c7; color: #92400e; }
    .status-approved { background-color: #d1fae5; color: #065f46; }
    .status-rejected { background-color: #fee2e2; color: #991b1b; }
    .status-funded { background-color: #e9d5ff; color: #6b21a8; }
    .status-charged_backed { background-color: #fce7f3; color: #9f1239; }
    
    .search-form {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .table-actions {
        white-space: nowrap;
    }
    
    .pagination-wrapper {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-top: 1rem;
    }
    
    .customer-name {
        font-weight: 600;
        color: #1f2937;
    }
    
    .view-details-btn {
        white-space: nowrap;
    }
    
    .modal-xl {
        max-width: 90%;
    }
    
    #callDetailsContent .table {
        margin-bottom: 0;
    }
    
    #callDetailsContent .table td {
        padding: 0.5rem;
        font-size: 0.9rem;
    }
    
    #callDetailsContent .table td:first-child {
        width: 200px;
        vertical-align: top;
    }
    
    #callDetailsContent .table td:last-child {
        word-wrap: break-word;
        white-space: pre-wrap;
        max-width: 600px;
    }
    /* Ensure full value visible for SSN, routing number, bank account, etc. */
    #callDetailsContent .table td.full-value-cell {
        max-width: none;
        word-break: break-all;
        overflow-wrap: anywhere;
        white-space: pre-wrap;
    }
    
    #callDetailsContent h6 {
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .long-text-field {
        word-wrap: break-word;
        white-space: pre-wrap;
        min-height: 50px;
        max-width: 100%;
        padding: 0.75rem;
        line-height: 1.6;
    }
    
    #callDetailsContent .table tr td:last-child.long-text-field {
        max-width: 800px;
        min-width: 400px;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="ti ti-phone-call me-2"></i>Closed Calls Management - Sellerz Center</h2>
            <form action="{{ route('center_reports.clear-cache') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Clear cache to refresh data">
                    <i class="ti ti-refresh me-1"></i>Clear Cache
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stats-card total">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Total</h6>
                                <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                            </div>
                            <div class="theme-avtar bg-primary">
                                <i class="ti ti-list"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stats-card pending">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Pending</h6>
                                <h3 class="mb-0">{{ number_format($stats['pending']) }}</h3>
                            </div>
                            <div class="theme-avtar bg-warning">
                                <i class="ti ti-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stats-card approved">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Approved</h6>
                                <h3 class="mb-0">{{ number_format($stats['approved']) }}</h3>
                            </div>
                            <div class="theme-avtar bg-success">
                                <i class="ti ti-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stats-card rejected">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Rejected</h6>
                                <h3 class="mb-0">{{ number_format($stats['rejected']) }}</h3>
                            </div>
                            <div class="theme-avtar bg-danger">
                                <i class="ti ti-x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stats-card funded">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Funded</h6>
                                <h3 class="mb-0">{{ number_format($stats['funded']) }}</h3>
                            </div>
                            <div class="theme-avtar bg-info">
                                <i class="ti ti-currency-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stats-card charged">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Charged Back</h6>
                                <h3 class="mb-0">{{ number_format($stats['charged_backed']) }}</h3>
                            </div>
                            <div class="theme-avtar bg-secondary">
                                <i class="ti ti-alert-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('center_reports.index') }}" class="search-form">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ $search }}" placeholder="Search by Name, Phone, Email, Lead ID, Policy ID, HIPAA ID, Closer, Agent...">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="funded" {{ $status == 'funded' ? 'selected' : '' }}>Funded</option>
                                <option value="charged_backed" {{ $status == 'charged_backed' ? 'selected' : '' }}>Charged Back</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="sort_by" class="form-label">Sort By</label>
                            <select class="form-select" id="sort_by" name="sort_by">
                                <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Created Date</option>
                                <option value="customer_full_name" {{ $sortBy == 'customer_full_name' ? 'selected' : '' }}>Customer Name</option>
                                <option value="phone_number" {{ $sortBy == 'phone_number' ? 'selected' : '' }}>Phone Number</option>
                                <option value="status" {{ $sortBy == 'status' ? 'selected' : '' }}>Status</option>
                                <option value="lead_id" {{ $sortBy == 'lead_id' ? 'selected' : '' }}>Lead ID</option>
                                <option value="policy_id" {{ $sortBy == 'policy_id' ? 'selected' : '' }}>Policy ID</option>
                                <option value="closername" {{ $sortBy == 'closername' ? 'selected' : '' }}>Closer Name</option>
                                <option value="updated_at" {{ $sortBy == 'updated_at' ? 'selected' : '' }}>Updated Date</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="sort_order" class="form-label">Order</label>
                            <select class="form-select" id="sort_order" name="sort_order">
                                <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Descending</option>
                                <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Ascending</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-2">
                            <label for="per_page" class="form-label">Per Page</label>
                            <select class="form-select" id="per_page" name="per_page">
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                        <div class="col-md-10 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ti ti-search me-1"></i>Search
                            </button>
                            <a href="{{ route('center_reports.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-refresh me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Closed Calls Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Closed Calls List ({{ $closedCalls->total() }} total)</h5>
                <small class="text-muted">Click "View" to see full details</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>Lead ID</th>
                                <th>Policy ID</th>
                                <th>HIPAA ID</th>
                                <th>Status</th>
                                <th>Closer Name</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($closedCalls as $call)
                            <tr>
                                <td>{{ $call->id }}</td>
                                <td><span class="customer-name">{{ $call->customer_full_name ?? 'N/A' }}</span></td>
                                <td>{{ $call->lead_id ?? 'N/A' }}</td>
                                <td>{{ $call->policy_id ?? 'N/A' }}</td>
                                <td>{{ $call->hippa_id ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $statusClass = 'status-' . str_replace('_', '-', $call->status ?? 'pending');
                                        $statusLabel = ucfirst(str_replace('_', ' ', $call->status ?? 'pending'));
                                    @endphp
                                    <span class="badge badge-status {{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td>{{ $call->closername ?? $call->junior_closer_name ?? 'N/A' }}</td>
                                <td><small>{{ $call->created_at ? $call->created_at->format('Y-m-d H:i') : 'N/A' }}</small></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary view-details-btn" 
                                            data-call-data="{{ base64_encode(json_encode($call->toArray())) }}">
                                        <i class="ti ti-eye me-1"></i>View
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ti ti-inbox" style="font-size: 3rem;"></i>
                                        <p class="mt-2">No closed calls found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($closedCalls->hasPages())
                <div class="pagination-wrapper mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">
                                Showing {{ $closedCalls->firstItem() }} to {{ $closedCalls->lastItem() }} of {{ $closedCalls->total() }} results
                            </p>
                        </div>
                        <div>
                            {{ $closedCalls->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="callDetailsModal" tabindex="-1" aria-labelledby="callDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="callDetailsModalLabel">Closed Call Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="callDetailsContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.view-details-btn');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const callDataEncoded = this.getAttribute('data-call-data');
            
            if (!callDataEncoded) {
                console.error('No call data found');
                return;
            }
            
            let callData;
            try {
                const decoded = atob(callDataEncoded);
                callData = JSON.parse(decoded);
            } catch (e) {
                console.error('Error parsing call data:', e);
                alert('Error loading call details. Please try again.');
                return;
            }
            
            showCallDetails(callData);
        });
    });
});

function showCallDetails(callData) {
    const modalElement = document.getElementById('callDetailsModal');
    const modalContent = document.getElementById('callDetailsContent');
    
    if (!modalElement || !modalContent) {
        alert('Modal elements not found');
        return;
    }
    
    // Format dates
    const formatDate = (dateStr) => {
        if (!dateStr) return 'N/A';
        try {
            const date = new Date(dateStr);
            return date.toISOString().split('T')[0];
        } catch (e) {
            return dateStr || 'N/A';
        }
    };

    // Escape HTML to prevent XSS
    const escapeHtml = (text) => {
        if (!text) return 'N/A';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };
    
    // Build modal content
    let html = `
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted mb-2">Basic Information</h6>
                    <table class="table table-sm table-bordered">
                        <tr><td><strong>ID:</strong></td><td>${escapeHtml(callData.id)}</td></tr>
                        <tr><td><strong>Customer Full Name:</strong></td><td>${escapeHtml(callData.customer_full_name)}</td></tr>
                        <tr><td><strong>Phone Number:</strong></td><td>${escapeHtml(callData.phone_number)}</td></tr>
                        <tr><td><strong>Alternate Phone:</strong></td><td>${escapeHtml(callData.alternate_phone_number)}</td></tr>
                        <tr><td><strong>Email:</strong></td><td>${escapeHtml(callData.cx_email)}</td></tr>
                        <tr><td><strong>Address:</strong></td><td>${escapeHtml(callData.address)}</td></tr>
                        <tr><td><strong>City:</strong></td><td>${escapeHtml(callData.city)}</td></tr>
                        <tr><td><strong>State:</strong></td><td>${escapeHtml(callData.state)}</td></tr>
                        <tr><td><strong>Zip Code:</strong></td><td>${escapeHtml(callData.zip_code)}</td></tr>
                    </table>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted mb-2">Personal Details</h6>
                    <table class="table table-sm table-bordered">
                        <tr><td><strong>Gender:</strong></td><td>${escapeHtml(callData.gender)}</td></tr>
                        <tr><td><strong>Marital Status:</strong></td><td>${escapeHtml(callData.martial_status)}</td></tr>
                        <tr><td><strong>Age:</strong></td><td>${escapeHtml(callData.age)}</td></tr>
                        <tr><td><strong>DOB:</strong></td><td>${formatDate(callData.dob)}</td></tr>
                        <tr><td><strong>Place of Birth:</strong></td><td>${escapeHtml(callData.palce_of_birth)}</td></tr>
                        <tr><td><strong>Height:</strong></td><td>${escapeHtml(callData.height)}</td></tr>
                        <tr><td><strong>Weight:</strong></td><td>${escapeHtml(callData.weight)}</td></tr>
                        <tr><td><strong>Social Security:</strong></td><td class="full-value-cell">${escapeHtml(callData.social_security) || 'N/A'}</td></tr>
                        <tr><td><strong>Smoker:</strong></td><td>${escapeHtml(callData.smoker)}</td></tr>
                    </table>
                </div>
                <div class="col-md-12 mb-3">
                    <h6 class="text-muted mb-2">Health Information</h6>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <td style="width: 200px; vertical-align: top;"><strong>Health Condition:</strong></td>
                            <td class="long-text-field">${escapeHtml(callData.health_condition) || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td style="width: 200px; vertical-align: top;"><strong>Medication:</strong></td>
                            <td class="long-text-field">${escapeHtml(callData.medication) || 'N/A'}</td>
                        </tr>
                        <tr><td><strong>Hospital Name:</strong></td><td>${escapeHtml(callData.hospital_name) || 'N/A'}</td></tr>
                        <tr><td><strong>Hospital Address:</strong></td><td>${escapeHtml(callData.hospital_address) || 'N/A'}</td></tr>
                        <tr><td><strong>Physician Name:</strong></td><td>${escapeHtml(callData.physician_name) || 'N/A'}</td></tr>
                    </table>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted mb-2">Insurance Details</h6>
                    <table class="table table-sm table-bordered">
                        <tr><td><strong>Monthly Premium:</strong></td><td>${escapeHtml(callData.monthly_premium) || 'N/A'}</td></tr>
                        <tr><td><strong>Carrier:</strong></td><td>${escapeHtml(callData.carrier) || 'N/A'}</td></tr>
                        <tr><td><strong>Coverage Plan:</strong></td><td>${escapeHtml(callData.coverage_plan) || 'N/A'}</td></tr>
                        <tr><td><strong>Customer Eligibility:</strong></td><td>${escapeHtml(callData.customer_eligibility) || 'N/A'}</td></tr>
                        <tr><td><strong>Underwriter Name:</strong></td><td>${escapeHtml(callData.underwriter_name) || 'N/A'}</td></tr>
                    </table>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted mb-2">Beneficiary Information</h6>
                    <table class="table table-sm table-bordered">
                        <tr><td><strong>Beneficiary:</strong></td><td>${escapeHtml(callData.beneficiary) || 'N/A'}</td></tr>
                        <tr><td><strong>Beneficiary Relation:</strong></td><td>${escapeHtml(callData.beneficiary_relation) || 'N/A'}</td></tr>
                        <tr><td><strong>Beneficiary Phone:</strong></td><td>${escapeHtml(callData.beneficiary_phone) || 'N/A'}</td></tr>
                        <tr><td><strong>Beneficiary DOB:</strong></td><td>${formatDate(callData.beneficiary_dob)}</td></tr>
                    </table>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted mb-2">Banking Information</h6>
                    <table class="table table-sm table-bordered">
                        <tr><td><strong>Payor:</strong></td><td>${escapeHtml(callData.payor) || 'N/A'}</td></tr>
                        <tr><td><strong>Bank Name:</strong></td><td>${escapeHtml(callData.bank_name) || 'N/A'}</td></tr>
                        <tr><td><strong>Bank Address:</strong></td><td>${escapeHtml(callData.bank_address) || 'N/A'}</td></tr>
                        <tr><td><strong>Routing Number:</strong></td><td class="full-value-cell">${escapeHtml(callData.routing_number) || 'N/A'}</td></tr>
                        <tr><td><strong>Bank Account Number:</strong></td><td class="full-value-cell">${escapeHtml(callData.bank_account_number) || 'N/A'}</td></tr>
                        <tr><td><strong>Debit Card No:</strong></td><td class="full-value-cell">${escapeHtml(callData.debit_card_direct_express_no) || 'N/A'}</td></tr>
                        <tr><td><strong>Debit Expiration:</strong></td><td class="full-value-cell">${escapeHtml(callData.debit_card_direct_express_expiration) || 'N/A'}</td></tr>
                        <tr><td><strong>Debit CVV:</strong></td><td>${escapeHtml(callData.debit_card_direct_express_cvv) || 'N/A'}</td></tr>
                        <tr><td><strong>Account Type:</strong></td><td>${escapeHtml(callData.account_type) || 'N/A'}</td></tr>
                        <tr><td><strong>Initial Draft Date:</strong></td><td>${formatDate(callData.initial_draft_date)}</td></tr>
                        <tr><td><strong>Future Draft Date:</strong></td><td>${formatDate(callData.future_draft_date)}</td></tr>
                    </table>
                </div>
                <div class="col-md-12 mb-3">
                    <h6 class="text-muted mb-2">Sales Information</h6>
                    <table class="table table-sm table-bordered">
                        <tr><td style="width: 200px;"><strong>Closer ID:</strong></td><td>${escapeHtml(callData.closer_id)}</td></tr>
                        <tr><td><strong>Closer Name:</strong></td><td>${escapeHtml(callData.closername)}</td></tr>
                        <tr><td><strong>Junior Closer Name:</strong></td><td>${escapeHtml(callData.junior_closer_name)}</td></tr>
                        <tr><td><strong>Junior Closer 2:</strong></td><td>${escapeHtml(callData.juniorcloser2)}</td></tr>
                        <tr><td><strong>Center Name:</strong></td><td>${escapeHtml(callData.center_name)}</td></tr>
                        <tr><td><strong>Sale Made By:</strong></td><td>${escapeHtml(callData.sale_made_by)}</td></tr>
                        <tr><td><strong>Status:</strong></td><td><span class="badge badge-status status-${(callData.status || 'pending').replace('_', '-')}">${(callData.status || 'pending').replace('_', ' ').replace(/\\b\\w/g, l => l.toUpperCase())}</span></td></tr>
                        <tr>
                            <td style="width: 200px; vertical-align: top;"><strong>Client's Comment:</strong></td>
                            <td style="word-wrap: break-word; white-space: pre-wrap; max-width: 600px;">${escapeHtml(callData.clients_comment) || 'N/A'}</td>
                        </tr>
                        <tr><td><strong>Client ID:</strong></td><td>${escapeHtml(callData.clients_id)}</td></tr>
                        <tr>
                            <td style="width: 200px; vertical-align: top;"><strong>Remarks:</strong></td>
                            <td style="word-wrap: break-word; white-space: pre-wrap; max-width: 600px;">${escapeHtml(callData.remarks) || 'N/A'}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted mb-2">Lead & Team Information</h6>
                    <table class="table table-sm table-bordered">
                        <tr><td><strong>Lead ID:</strong></td><td>${escapeHtml(callData.lead_id) || 'N/A'}</td></tr>
                        <tr><td><strong>Policy ID:</strong></td><td>${escapeHtml(callData.policy_id) || 'N/A'}</td></tr>
                        <tr><td><strong>HIPAA ID:</strong></td><td>${escapeHtml(callData.hippa_id) || 'N/A'}</td></tr>
                        <tr><td><strong>Recording ID:</strong></td><td>${escapeHtml(callData.recording_id) || 'N/A'}</td></tr>
                        <tr><td><strong>Team Name:</strong></td><td>${escapeHtml(callData.teamname) || 'N/A'}</td></tr>
                        <tr><td><strong>Agent Name:</strong></td><td>${escapeHtml(callData.agentname) || 'N/A'}</td></tr>
                        <tr><td><strong>Dialer Agent Name:</strong></td><td>${escapeHtml(callData.dialeragentname) || 'N/A'}</td></tr>
                        <tr><td><strong>Dialer Name:</strong></td><td>${escapeHtml(callData.dialername) || 'N/A'}</td></tr>
                        <tr><td><strong>List ID 1:</strong></td><td>${escapeHtml(callData.list_id_1) || 'N/A'}</td></tr>
                        <tr><td><strong>List ID 2:</strong></td><td>${escapeHtml(callData.list_id_2) || 'N/A'}</td></tr>
                        <tr><td><strong>Created At:</strong></td><td>${callData.created_at ? new Date(callData.created_at).toLocaleString() : 'N/A'}</td></tr>
                        <tr><td><strong>Updated At:</strong></td><td>${callData.updated_at ? new Date(callData.updated_at).toLocaleString() : 'N/A'}</td></tr>
                    </table>
                </div>
            `;
    
    modalContent.innerHTML = html;
    
    // Show modal using jQuery if available, otherwise use Bootstrap
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery(modalElement).modal('show');
    } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        // Fallback: just show the modal by adding class
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        document.body.classList.add('modal-open');
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'modalBackdrop';
        document.body.appendChild(backdrop);
    }
}
</script>
@endsection

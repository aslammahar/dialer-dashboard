@extends('layouts.admin')

@section('page-title')
    {{ __('My Policies Dashboard') }}
@endsection

@section('content')

<style> 

.stat-approved  { background: rgba(209, 237, 255, 0.25); border: 1px solid rgba(12, 84, 96, 0.3); }
.stat-pending   { background: rgba(255, 243, 205, 0.25); border: 1px solid rgba(133, 100, 4, 0.3); }
.stat-rejected  { background: rgba(248, 215, 218, 0.25); border: 1px solid rgba(114, 28, 36, 0.3); }
.stat-individual { min-width: 100px; padding: 10px 16px; }
.stat-individual .stat-label { font-size: 0.78em; }


</style>

<div class="policies-dashboard">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="welcome-section">
                <h1 class="dashboard-title">My Policies</h1>
                <p class="dashboard-subtitle">Manage and view all your insurance policies in one place</p>
                

                <div class="stats-bar">
                    <div class="stat-item">
                        <span class="stat-number">{{ $totalCount }}</span>
                        <span class="stat-label">Total Policies</span>
                    </div>

                    @foreach($statusCounts as $status => $count)
                        <div class="stat-item">
                            <span class="stat-number">{{ $count }}</span>
                            <span class="stat-label">
                                @switch($status)
                                    @case('Funded')         💰 @break
                                    @case('Approved')       ✅ @break
                                    @case('Pending')        ⏳ @break
                                    @case('Underwriting')   📝 @break
                                    @case('Need to Reach')  📞 @break
                                    @case('NSF')            💸 @break
                                    @case('Rejected')       ❌ @break
                                    @case('DNC')            🔕 @break
                                    @case('Charge Back')    ↩️ @break
                                    @case('Cancelled')      🚫 @break
                                    @default                🔹 @break
                                @endswitch
                                {{ $status }}
                            </span>
                        </div>
                    @endforeach
                </div>




            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="controls-section">
        <div class="search-container">
            <div class="search-box">
                <svg class="search-icon" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <input type="text" id="search" placeholder="Search policies..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="filter-container">
            <!-- Status Filter -->
            <select name="status_filter" id="status_filter" class="filter-select">
                <option value="all" {{ ($statusFilter ?? 'all') == 'all' ? 'selected' : '' }}>All Statuses</option>
                @foreach($allStatuses as $status)
                    <option value="{{ $status }}" {{ ($statusFilter ?? '') == $status ? 'selected' : '' }}>
                        @switch($status)
                            @case('pending')
                                ⏳ Pending
                                @break
                            @case('approved')
                                ✅ Approved
                                @break
                            @case('rejected')
                                ❌ Rejected
                                @break
                            @case('Need to Reach')
                                📞 Need to Reach
                                @break
                            @case('NSF')
                                💸 NSF
                                @break
                            @case('Charge Back')
                                ↩️ Charge Back
                                @break
                            @case('Cancelled')
                                🚫 Cancelled
                                @break
                            @case('DNC')
                                🔕 DNC
                                @break
                            @case('Underwriting')
                                📝 Underwriting
                                @break
                            @case('Funded')
                                💰 Funded
                                @break
                            @default
                                {{ ucfirst($status) }}
                        @endswitch
                    </option>
                @endforeach
            </select>
            
            <!-- Per Page Filter -->
            <select name="per_page" id="per_page" class="filter-select">
                <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5 per page</option>
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 per page</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 per page</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 per page</option>
            </select>
        </div>
    </div>

    <!-- Active Filters Display -->
    @if($search || ($statusFilter && $statusFilter !== 'all'))
    <div class="active-filters">
        <h4>Active Filters:</h4>
        <div class="filter-tags">
            @if($search)
                <span class="filter-tag">
                    🔍 Search: "{{ $search }}"
                    <a href="{{ request()->url() }}?per_page={{ $perPage }}&status_filter={{ $statusFilter ?? 'all' }}" class="remove-filter">×</a>
                </span>
            @endif
            @if($statusFilter && $statusFilter !== 'all')
                <span class="filter-tag">
                    📊 Status: {{ ucfirst($statusFilter) }}
                    <a href="{{ request()->url() }}?per_page={{ $perPage }}&search={{ $search }}" class="remove-filter">×</a>
                </span>
            @endif
            <a href="{{ request()->url() }}?per_page={{ $perPage }}" class="clear-all-filters">Clear All Filters</a>
        </div>
    </div>
    @endif

    <!-- Policies List -->
    <div class="policies-list" id="policiesContainer">
        @forelse($closedCalls as $closedCall)
        <div class="policy-card">
            <!-- Card Header -->
            <div class="card-header">
                <div class="policy-info">
                    <div class="policy-details">
                        <h3 class="policy-title">{{ $closedCall->customer_full_name }}</h3>
                        <div class="policy-meta">
                            <span class="policy-id">Policy #{{ str_pad($closedCall->id, 6, '0', STR_PAD_LEFT) }}</span>
                            <span class="policy-date">{{ $closedCall->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    <!-- Premium Display -->
                    <div class="premium-section">
                        <div class="premium-amount">${{ $closedCall->monthly_premium ?: '—' }}</div>
                        <div class="premium-label">Monthly Premium</div>
                    </div>
                </div>
                <div class="header-actions">
                    <div class="status-badge status-{{ strtolower(str_replace(' ', '-', $closedCall->status)) }}">
                        {{ ucfirst($closedCall->status) }}
                    </div>
                    <div class="action-buttons">
                        <a href="{{ route('closed-calls.show', $closedCall->id) }}" class="btn-view">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                            View
                        </a>
                        <a href="{{ url('/edit-closed-policy', $closedCall->id) }}" class="btn btn-info btn-sm">Client Edit Policy</a>
                    </div>
                </div>
            </div>

            <!-- Key Information Grid -->
            <div class="info-grid">
                <!-- Contact Info -->
                <div class="info-section">
                    <h4 class="section-title">📱 Contact</h4>
                    <div class="info-items">
                        <div class="info-row">
                            <span class="label">Phone:</span>
                            <span class="value">{{ $closedCall->phone_number ?: '—' }}</span>
                        </div>
                        @if($closedCall->alternate_phone_number)
                        <div class="info-row">
                            <span class="label">Alt Phone:</span>
                            <span class="value">{{ $closedCall->alternate_phone_number }}</span>
                        </div>
                        @endif
                        @if($closedCall->cx_email)
                        <div class="info-row">
                            <span class="label">Email:</span>
                            <span class="value">{{ $closedCall->cx_email }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Address Info -->
                <div class="info-section">
                    <h4 class="section-title">📍 Address</h4>
                    <div class="address-text">
                        {{ $closedCall->address ?: '—' }}<br>
                        {{ $closedCall->city }}, {{ $closedCall->state }} {{ $closedCall->zip_code }}
                    </div>
                </div>

                <!-- Personal Info -->
                <div class="info-section">
                    <h4 class="section-title">👤 Personal</h4>
                    <div class="info-items">
                        <div class="info-row">
                            <span class="label">Age:</span>
                            <span class="value">{{ $closedCall->age ? $closedCall->age . ' years' : '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">DOB:</span>
                            <span class="value">{{ $closedCall->dob ? $closedCall->dob->format('M d, Y') : '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Gender:</span>
                            <span class="value">{{ $closedCall->gender ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Marital:</span>
                            <span class="value">{{ $closedCall->martial_status ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Smoker:</span>
                            <span class="value {{ strtolower($closedCall->smoker) === 'yes' ? 'text-danger' : 'text-success' }}">
                                {{ $closedCall->smoker ?: '—' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Physical Info -->
                <div class="info-section">
                    <h4 class="section-title">⚖️ Physical</h4>
                    <div class="info-items">
                        <div class="info-row">
                            <span class="label">Height:</span>
                            <span class="value">{{ $closedCall->height ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Weight:</span>
                            <span class="value">{{ $closedCall->weight ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">SSN:</span>
                            <span class="value">{{ $closedCall->social_security ? '***-**-' . substr($closedCall->social_security, -4) : '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Medical Info -->
                <div class="info-section">
                    <h4 class="section-title">🏥 Medical</h4>
                    <div class="info-items">
                        <div class="info-row">
                            <span class="label">Health:</span>
                            <span class="value">{{ $closedCall->health_condition ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Medications:</span>
                            <span class="value">{{ $closedCall->medication ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Hospital:</span>
                            <span class="value">{{ $closedCall->hospital_name ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Physician:</span>
                            <span class="value">{{ $closedCall->physician_name ?: '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Insurance Info -->
                <div class="info-section">
                    <h4 class="section-title">🛡️ Insurance</h4>
                    <div class="info-items">
                        <div class="info-row">
                            <span class="label">Carrier:</span>
                            <span class="value">{{ $closedCall->carrier ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Plan:</span>
                            <span class="value">{{ $closedCall->coverage_plan ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Eligibility:</span>
                            <span class="value">{{ $closedCall->customer_eligibility ?: '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Beneficiary Info -->
                <div class="info-section">
                    <h4 class="section-title">👥 Beneficiary</h4>
                    <div class="info-items">
                        <div class="info-row">
                            <span class="label">Name:</span>
                            <span class="value">{{ $closedCall->beneficiary ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Relation:</span>
                            <span class="value">{{ $closedCall->beneficiary_relation ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Phone:</span>
                            <span class="value">{{ $closedCall->beneficiary_phone ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">DOB:</span>
                            <span class="value">{{ $closedCall->beneficiary_dob ? $closedCall->beneficiary_dob->format('M d, Y') : '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="info-section">
                    <h4 class="section-title">💳 Payment</h4>
                    <div class="info-items">
                        <div class="info-row">
                            <span class="label">Payor:</span>
                            <span class="value">{{ $closedCall->payor ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Bank:</span>
                            <span class="value">{{ $closedCall->bank_name ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Account:</span>
                            <span class="value">{{ $closedCall->bank_account_number ? '****' . substr($closedCall->bank_account_number, -4) : '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Type:</span>
                            <span class="value">{{ $closedCall->account_type ?: '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="info-section">
                    <h4 class="section-title">🔧 System Info</h4>
                    <div class="info-items">
                        <div class="info-row">
                            <span class="label">Recording:</span>
                            <span class="value {{ strtolower($closedCall->recording_status) === 'yes' ? 'text-success' : 'text-danger' }}">
                                {{ $closedCall->recording_status ?: '—' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="label">Recording ID:</span>
                            <span class="value">{{ $closedCall->recording_id ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">HIPAA ID:</span>
                            <span class="value">{{ $closedCall->hippa_id ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Policy No:</span>
                            <span class="value">{{ $closedCall->policy_id ?: '—' }}</span>
                        </div>

                        <div class="info-row">
                            <span class="label">Signature :</span>
                            <span class="value">{{ $closedCall->signature_type ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Call Id:</span>
                            <span class="value">{{ $closedCall->call_id ?: '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Draft Dates -->
                <div class="info-section">
                    <h4 class="section-title">📅 Drafts</h4>
                    <div class="info-items">
                        <div class="info-row">
                            <span class="label">Initial:</span>
                            <span class="value">{{ $closedCall->initial_draft_date ? $closedCall->initial_draft_date->format('M d, Y') : '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Future:</span>
                            <span class="value">{{ $closedCall->future_draft_date ? $closedCall->future_draft_date->format('M d, Y') : '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Processing Team -->
                <div class="info-section">
                    <h4 class="section-title">👨‍💼 Team</h4>
                    <div class="info-items">
                        <div class="info-row">
                            <span class="label">Underwriter:</span>
                            <span class="value">{{ $closedCall->underwriter_name ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Closer:</span>
                            <span class="value">{{ $closedCall->closername ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Jr. Closer:</span>
                            <span class="value">{{ $closedCall->juniorcloser->name ?? $closedCall->junior_closer_name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Center:</span>
                            <span class="value">{{ $closedCall->center_name ?: '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Sale By:</span>
                            <span class="value">{{ $closedCall->sale_made_by ?: '—' }}</span>
                        </div>
                    </div>
                </div>

             <div class="info-section">
                    <h4 class="section-title">👨‍💼 Client</h4>
                    <div class="info-items">
                        <div class="info-row">
                            <span class="label">Client Name:</span>
                            <span class="value">{{ $closedCall->client_name_2 ?: '—' }}</span>
                        </div>
                      
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            @if($closedCall->remarks || $closedCall->clients_comment)
            <div class="comments-section">
                @if($closedCall->remarks)
                <div class="comment-block">
                    <h5>📝 Remarks</h5>
                    <p>{{ $closedCall->remarks }}</p>
                </div>
                @endif
                @if($closedCall->clients_comment)
                <div class="comment-block">
                    <h5>💬 Client Comments</h5>
                    <p>{{ $closedCall->clients_comment }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">📄</div>
            <h3>No Policies Found</h3>
            <p>{{ ($search || ($statusFilter && $statusFilter !== 'all')) ? 'No policies match your current filters.' : 'You don\'t have any insurance policies yet.' }}</p>
            @if($search || ($statusFilter && $statusFilter !== 'all'))
                <a href="{{ request()->url() }}?per_page={{ $perPage }}" class="btn-clear-filters">Clear Filters</a>
            @endif
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($closedCalls->hasPages())
    <div class="pagination-container">
        {{ $closedCalls->appends(['per_page' => $perPage, 'search' => request('search'), 'status_filter' => request('status_filter')])->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function () {
    // Per page change - immediate reload
    $('#per_page').change(function () {
        fetchData();
    });

    // Search with debounce to avoid too many requests
    let searchTimeout;
    $('#search').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            fetchData();
        }, 800); // Wait 800ms after user stops typing
    });

    function fetchData() {
        let perPage = $('#per_page').val();
        let search = $('#search').val();
        
        // Instead of AJAX, reload the page with new parameters to maintain styling
        let url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        if (search) {
            url.searchParams.set('search', search);
        } else {
            url.searchParams.delete('search');
        }
        
        window.location.href = url.toString();
    }
});


$(document).ready(function () {
    // Per page change - immediate reload
    $('#per_page').change(function () {
        fetchData();
    });

    // Status filter change - immediate reload
    $('#status_filter').change(function () {
        fetchData();
    });

    // Search with debounce to avoid too many requests
    let searchTimeout;
    $('#search').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            fetchData();
        }, 800); // Wait 800ms after user stops typing
    });

    function fetchData() {
        let perPage = $('#per_page').val();
        let search = $('#search').val();
        let statusFilter = $('#status_filter').val();
        
        // Instead of AJAX, reload the page with new parameters to maintain styling
        let url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        
        if (search) {
            url.searchParams.set('search', search);
        } else {
            url.searchParams.delete('search');
        }
        
        if (statusFilter && statusFilter !== 'all') {
            url.searchParams.set('status_filter', statusFilter);
        } else {
            url.searchParams.delete('status_filter');
        }
        
        window.location.href = url.toString();
    }
});
</script>

<style>
.policies-dashboard {
    min-height: 100vh;
    padding: 20px;
}


.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.dashboard-title {
    font-size: 2.5em;
    font-weight: 700;
    color: white;
    margin: 0 0 10px 0;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.dashboard-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1em;
    margin: 0 0 25px 0;
}

.stats-bar {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: rgba(255, 255, 255, 0.15);
    padding: 15px 25px;
    border-radius: 15px;
    min-width: 120px;
}

.stat-number {
    font-size: 2em;
    font-weight: 700;
    color: white;
    line-height: 1;
}

.stat-label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9em;
    margin-top: 5px;
}

.controls-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    gap: 20px;
    flex-wrap: wrap;
}

.search-container {
    flex: 1;
    max-width: 400px;
}

.search-box {
    position: relative;
    background: white;
    border-radius: 50px;
    padding: 0 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.search-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

.search-box input {
    width: 100%;
    padding: 15px 15px 15px 50px;
    border: none;
    background: transparent;
    font-size: 1em;
    outline: none;
}

.filter-select {
    background: white;
    border: none;
    padding: 15px 20px;
    border-radius: 50px;
    font-weight: 500;
    cursor: pointer;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    outline: none;
}

.policies-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 30px;
}

.policy-card {
    background: white;
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: 100%;
}

.policy-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f8f9fa;
}

.policy-title {
    font-size: 1.3em;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 8px 0;
}

.policy-meta {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.policy-id, .policy-date {
    background: #f8f9fa;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.8em;
    color: #6c757d;
    font-weight: 500;
}

.status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.8em;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-approved { background: #d1edff; color: #0c5460; }
.status-rejected { background: #f8d7da; color: #721c24; }

.premium-section {
    text-align: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 20px;
}

.premium-amount {
    font-size: 2.2em;
    font-weight: 700;
    line-height: 1;
}

.premium-label {
    font-size: 0.9em;
    opacity: 0.9;
    margin-top: 5px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.info-section {
    background: #f8f9fa;
    padding: 8px 10px;
    border-radius: 8px;
}

.section-title {
    font-size: 0.8em;
    font-weight: 600;
    color: #495057;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 4px;
}

.info-items {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
}

.label {
    font-size: 0.75em;
    color: #6c757d;
    font-weight: 500;
    flex-shrink: 0;
}

.value {
    font-size: 0.8em;
    color: #2c3e50;
    font-weight: 500;
    text-align: right;
    word-break: break-word;
}

.address-text {
    color: #495057;
    font-size: 0.85em;
    line-height: 1.4;
}

.text-danger { color: #dc3545 !important; }
.text-success { color: #28a745 !important; }

.comments-section {
    background: #e9ecef;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 20px;
}

.comment-block {
    margin-bottom: 15px;
}

.comment-block:last-child {
    margin-bottom: 0;
}

.comment-block h5 {
    font-size: 0.9em;
    font-weight: 600;
    color: #495057;
    margin: 0 0 8px 0;
}

.comment-block p {
    font-size: 0.85em;
    color: #6c757d;
    margin: 0;
    line-height: 1.4;
}

.header-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 15px;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.btn-view, .btn-edit {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.85em;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-view {
    background: #e9ecef;
    color: #495057;
}

.btn-view:hover {
    background: #dee2e6;
    color: #495057;
    text-decoration: none;
}

.btn-edit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-edit:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    color: white;
}

.empty-icon {
    font-size: 4em;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 1.5em;
    margin-bottom: 10px;
}

.pagination-container {
    display: flex;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 20px;
}

.pagination .page-link {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    margin: 0 2px;
    border-radius: 8px;
}

.pagination .page-link:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white;
}

.pagination .page-item.active .page-link {
    background: white;
    color: #667eea;
}

@media (max-width: 768px) {
    .policies-dashboard {
        padding: 15px;
    }
    
    .controls-section {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-container {
        max-width: none;
    }
    
    .dashboard-title {
        font-size: 2em;
    }
    
    .stats-bar {
        justify-content: center;
    }
    
    .policy-info {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .header-actions {
        align-items: center;
    }
    
    .info-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}

@media (max-width: 480px) {
    .policy-card {
        padding: 12px;
    }
    
    .card-header {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
    }
    
    .header-actions {
        align-items: stretch;
    }
    
    .action-buttons {
        justify-content: center;
    }
    
    .premium-section {
        padding: 8px 15px;
    }
    
    .premium-amount {
        font-size: 1.3em;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
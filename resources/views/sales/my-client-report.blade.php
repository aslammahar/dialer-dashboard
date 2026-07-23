@extends('layouts.admin')

@section('title', 'My Reports')

@section('content')
<style>
    /* My Reports Dashboard Styles */
    .my-reports-dashboard {
        min-height: 100vh;
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    }

    .main-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    .header-section {
        text-align: center;
        margin-bottom: 3rem;
    }

    .main-title {
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(135deg, #0284c7, #0ea5e9, #38bdf8);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1rem;
    }

    .subtitle {
        font-size: 1.25rem;
        color: #64748b;
        margin-bottom: 0.5rem;
    }

    .timezone-info {
        font-size: 0.875rem;
        color: #94a3b8;
        margin-bottom: 2rem;
    }

    /* Filter Buttons */
    .filter-container {
        display: inline-flex;
        background: white;
        border-radius: 16px;
        padding: 6px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .filter-btn {
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        color: #64748b;
        border: none;
        background: transparent;
        cursor: pointer;
    }

    .filter-btn.active {
        background: linear-gradient(135deg, #0284c7, #0ea5e9);
        color: white;
        box-shadow: 0 4px 16px rgba(2, 132, 199, 0.3);
        transform: translateY(-2px);
    }

    .filter-btn:hover:not(.active) {
        background: #f1f5f9;
        color: #334155;
        transform: translateY(-1px);
    }

    /* Summary Cards */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .summary-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #0284c7, #38bdf8);
    }

    .summary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 48px rgba(0,0,0,0.15);
    }

    .summary-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .summary-card-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .summary-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0284c7;
        font-size: 1.25rem;
    }

    .summary-card-value {
        font-size: 2.5rem;
        font-weight: bold;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .summary-card-desc {
        font-size: 0.875rem;
        color: #64748b;
    }

    /* Action Bar */
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    .action-bar h2 {
        font-size: 1.5rem;
        font-weight: bold;
        color: #1e293b;
        margin: 0;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0284c7, #0ea5e9);
        color: white;
        box-shadow: 0 4px 16px rgba(2, 132, 199, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(2, 132, 199, 0.4);
    }

    .btn-secondary {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #f1f5f9;
        transform: translateY(-1px);
    }

    /* Carrier Grid */
    .carrier-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 3rem;
        background: white;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }

    .carrier-card {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        text-align: center;
    }

    .carrier-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        border-color: #0ea5e9;
    }

    .carrier-name {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .carrier-count {
        font-size: 1.5rem;
        font-weight: bold;
        color: #0284c7;
    }

    .carrier-count.zero {
        color: #cbd5e1;
    }

    /* Submissions Table */
    .submissions-table-wrapper {
        width: 100%;
        overflow-x: auto;
        margin-bottom: 3rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }

    .submissions-table {
        width: 100%;
        min-width: 1200px;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .submissions-table th {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 1rem 0.75rem;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .submissions-table td {
        padding: 1rem 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.3s ease;
    }

    .submissions-table tr:hover {
        background: #f8fafc;
    }

    .submissions-table tr:last-child td {
        border-bottom: none;
    }

    /* Status Badge */
    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        white-space: nowrap;
    }

    .status-approved {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #16a34a;
        border: 1px solid #86efac;
    }

    .status-pending {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #d97706;
        border: 1px solid #fbbf24;
    }

    .status-rejected {
        background: linear-gradient(135deg, #fecaca, #fca5a5);
        color: #dc2626;
        border: 1px solid #f87171;
    }

    /* Comment Icon */
    .comment-icon {
        cursor: pointer;
        color: #3b82f6;
        font-size: 1.1rem;
        padding: 0.25rem;
        border-radius: 4px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
    }

    .comment-icon:hover {
        background: #dbeafe;
        transform: scale(1.1);
    }

    .no-comment {
        color: #cbd5e1;
        font-size: 0.75rem;
    }

    /* Comment Modal */
    .comment-modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
    }

    .comment-modal-content {
        background: white;
        margin: 10% auto;
        padding: 0;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        max-height: 70vh;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        animation: commentModalSlideIn 0.3s ease;
    }

    @keyframes commentModalSlideIn {
        from { opacity: 0; transform: translateY(-30px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .comment-modal-header {
        padding: 1.5rem;
        background: linear-gradient(135deg, #0284c7, #0ea5e9);
        color: white;
        position: relative;
    }

    .comment-modal-header h4 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
    }

    .comment-close {
        position: absolute;
        right: 1rem;
        top: 1rem;
        color: white;
        font-size: 1.5rem;
        font-weight: bold;
        cursor: pointer;
        transition: opacity 0.3s ease;
        border: none;
        background: none;
        padding: 0.25rem;
        border-radius: 4px;
    }

    .comment-close:hover {
        opacity: 0.7;
        background: rgba(255,255,255,0.1);
    }

    .comment-modal-body {
        padding: 1.5rem;
        max-height: 50vh;
        overflow-y: auto;
    }

    .comment-section {
        margin-bottom: 1.5rem;
    }

    .comment-section:last-child {
        margin-bottom: 0;
    }

    .comment-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }

    .comment-text {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        font-size: 0.9rem;
        line-height: 1.5;
        color: #374151;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .no-comment-text {
        text-align: center;
        color: #9ca3af;
        font-style: italic;
        padding: 2rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #64748b;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: #374151;
    }

    .empty-state p {
        font-size: 1rem;
        margin-bottom: 2rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .main-container {
            padding: 1rem;
        }

        .main-title {
            font-size: 2rem;
        }

        .subtitle {
            font-size: 1rem;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .action-bar {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .action-buttons {
            justify-content: center;
            flex-wrap: wrap;
        }

        .carrier-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            padding: 1rem;
        }

        .filter-container {
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 16px;
            font-size: 0.875rem;
        }

        .submissions-table {
            min-width: 1000px;
        }
    }

    /* Scrollbar Styles */
    .submissions-table-wrapper::-webkit-scrollbar {
        height: 8px;
    }

    .submissions-table-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .submissions-table-wrapper::-webkit-scrollbar-thumb {
        background: #0284c7;
        border-radius: 4px;
    }

    .submissions-table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #0369a1;
    }
</style>

<div class="my-reports-dashboard">
    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <h1 class="main-title">📊 My Performance Dashboard</h1>
            <p class="subtitle">Track your submissions, approvals, and performance metrics<br>
            <small style="color: #0284c7; font-weight: 600;">Welcome, {{ auth()->user()->name }}!</small></p>
            <p class="timezone-info">🕐 All times shown in Eastern Time (America/New_York)</p>
            
            <!-- Filter Buttons -->
            <div class="filter-container">
                <a href="{{ route('my.reports', ['filter' => 'daily']) }}" 
                   class="filter-btn {{ $filter === 'daily' ? 'active' : '' }}">
                    📅 Daily
                </a>
                <a href="{{ route('my.reports', ['filter' => 'weekly']) }}" 
                   class="filter-btn {{ $filter === 'weekly' ? 'active' : '' }}">
                    📊 Weekly
                </a>
                <a href="{{ route('my.reports', ['filter' => 'monthly']) }}" 
                   class="filter-btn {{ $filter === 'monthly' ? 'active' : '' }}">
                    📈 Monthly
                </a>
                <form method="GET" action="{{ route('my.reports') }}" style="display:inline-block; margin-left:1rem;">
                    <input type="month" name="month_year" value="{{ request('month_year') }}" class="filter-btn" onchange="this.form.submit()">
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">Total Submissions</div>
                    <div class="summary-card-icon">📋</div>
                </div>
                <div class="summary-card-value">{{ number_format($myData['total_submissions']) }}</div>
                <div class="summary-card-desc">{{ $period }}</div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">Approved</div>
                    <div class="summary-card-icon">✅</div>
                </div>
                <div class="summary-card-value">{{ number_format($myData['approved_count']) }}</div>
                <div class="summary-card-desc">{{ $myData['conversion_rate'] }}% conversion</div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">Pending</div>
                    <div class="summary-card-icon">⏳</div>
                </div>
                <div class="summary-card-value">{{ number_format($myData['pending_count']) }}</div>
                <div class="summary-card-desc">Awaiting approval</div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">Average Premium</div>
                    <div class="summary-card-icon">💰</div>
                </div>
                <div class="summary-card-value">${{ number_format($myData['avg_premium'], 2) }}</div>
                <div class="summary-card-desc">Monthly (approved only)</div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">Yearly Estimate</div>
                    <div class="summary-card-icon">📈</div>
                </div>
                <div class="summary-card-value">${{ number_format($myData['yearly_premium_estimate'], 0) }}</div>
                <div class="summary-card-desc">Annual projection</div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">Level Coverage</div>
                    <div class="summary-card-icon">🎯</div>
                </div>
                <div class="summary-card-value">{{ $myData['level_percent'] }}%</div>
                <div class="summary-card-desc">Approved only</div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">GI Coverage</div>
                    <div class="summary-card-icon">🛡️</div>
                </div>
                <div class="summary-card-value">{{ $myData['gi_percent'] }}%</div>
                <div class="summary-card-desc">Approved only</div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <h2>My Carrier Performance - {{ $period }}</h2>
            <div class="action-buttons">
                <a href="{{ route('my.reports.export', ['filter' => $filter]) }}" class="btn btn-primary">
                    📥 Export My Report
                </a>
                <button onclick="window.location.reload()" class="btn btn-secondary">
                    🔄 Refresh
                </button>
            </div>
        </div>

      

        <!-- My Submissions Section -->
        <div class="action-bar">
            <h2>My Submissions Details - {{ $period }}</h2>
            <div style="color: #64748b; font-size: 0.875rem;">
                <span style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    🕐 Eastern Time (ET)
                </span>
            </div>
        </div>

        <div class="submissions-table-wrapper">
            @if($mySubmissions->count() > 0)
                <table class="submissions-table">
                    <thead>
                        <tr>
                            <th>Date & Time (ET)</th>
                            <th>Customer Name</th>
                            <th>Status</th>
                            <th>Closer</th>
                            <th>Carrier</th>
                            <th>Premium</th>
                            <th>Eligibility</th>
                            <th style="text-align: center;">Client Comment</th>
                            <th style="text-align: center;">Other Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mySubmissions as $submission)
                            @php
                                $isApproved = in_array($submission->status, ['Funded', 'charged_backed', 'Approved']);
                                $isPending = in_array($submission->status, ['Pending', 'Underwriting', 'Need to Reach', 'NSF']);
                                $isRejected = in_array($submission->status, ['Rejected', 'DNC']);
                                
                                $statusClass = 'status-badge ';
                                if ($isApproved) {
                                    $statusClass .= 'status-approved';
                                } elseif ($isPending) {
                                    $statusClass .= 'status-pending';
                                } else {
                                    $statusClass .= 'status-rejected';
                                }
                                
                                $hasClientComment = $submission->clients_comment && trim($submission->clients_comment) !== '' && $submission->clients_comment !== 'null';
                                $hasOtherComment = $submission->comments && trim($submission->comments) !== '' && $submission->comments !== 'null';
                            @endphp
                            <tr>
                                <td style="color: #64748b; white-space: nowrap;">
                                    {{ $submission->created_at->format('M d, Y') }}<br>
                                    <small style="color: #94a3b8;">{{ $submission->created_at->format('h:i A') }}</small>
                                </td>
                                <td style="font-weight: 600; color: #1e293b;">
                                    {{ $submission->customer_full_name ?? 'N/A' }}
                                </td>
                                <td>
                                    <span class="{{ $statusClass }}">
                                        @if($isApproved) ✅ @elseif($isPending) ⏳ @else ❌ @endif
                                        {{ $submission->status }}
                                    </span>
                                </td>
                                <td style="color: #64748b;">
                                    {{ $submission->closername ?? 'N/A' }}
                                </td>
                                <td style="color: #0284c7; font-weight: 500;">
                                    {{ $submission->carrier ?? 'N/A' }}
                                </td>
                                <td style="color: #059669; font-weight: 600;">
                                    @if($submission->monthly_premium)
                                        ${{ number_format($submission->monthly_premium, 2) }}
                                    @else
                                        <span style="color: #cbd5e1;">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($submission->customer_eligibility)
                                        @php
                                            $isLevel = in_array($submission->customer_eligibility, ['Level', 'Graded', 'Modified', 'Standard', 'Preferred', 'Senior choice immediate', 'Golden solution immediate', 'Senior choice graded', 'Golden solution graded', 'Senior choice rop', 'Golden solution rop', 'Express select', 'ROP']);
                                            $isGI = in_array($submission->customer_eligibility, ['Guaranteed Issue', 'Graded GTL']);
                                        @endphp
                                        <span style="
                                            padding: 3px 8px;
                                            border-radius: 10px;
                                            font-size: 0.65rem;
                                            font-weight: 600;
                                            text-transform: uppercase;
                                            letter-spacing: 0.05em;
                                            display: inline-block;
                                            {{ $isLevel ? 'background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1d4ed8; border: 1px solid #3b82f6;' : '' }}
                                            {{ $isGI ? 'background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706; border: 1px solid #f59e0b;' : '' }}
                                            {{ !$isLevel && !$isGI ? 'background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;' : '' }}
                                        ">
                                            {{ $submission->customer_eligibility }}
                                        </span>
                                    @else
                                        <span style="color: #cbd5e1; font-size: 0.75rem;">N/A</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($hasClientComment)
                                        <span class="comment-icon" onclick="showCommentModal('{{ addslashes($submission->clients_comment) }}', 'Client Comment', '{{ $submission->customer_full_name ?? 'N/A' }}')" title="View client comment">
                                            💬
                                        </span>
                                    @else
                                        <span class="no-comment">-</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($hasOtherComment)
                                        <span class="comment-icon" onclick="showCommentModal('{{ addslashes($submission->comments) }}', 'Other Comment', '{{ $submission->customer_full_name ?? 'N/A' }}')" title="View other comment">
                                            📝
                                        </span>
                                    @else
                                        <span class="no-comment">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📋</div>
                    <h3>No Submissions Found</h3>
                    <p>No submission data found for {{ $period }}.</p>
                    <p style="font-size: 0.875rem; color: #94a3b8;">Try selecting a different time period or check back later.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Comment Modal -->
<div id="commentModal" class="comment-modal">
    <div class="comment-modal-content">
        <div class="comment-modal-header">
            <button class="comment-close">&times;</button>
            <h4 id="commentModalTitle">Comment</h4>
        </div>
        <div class="comment-modal-body">
            <div class="comment-section">
                <div class="comment-label">Customer</div>
                <div class="comment-text" id="commentCustomerName" style="background: #dbeafe; border-color: #93c5fd; color: #1e40af; font-weight: 600;"></div>
            </div>
            <div class="comment-section">
                <div class="comment-label">Comment</div>
                <div class="comment-text" id="commentContent"></div>
            </div>
        </div>
    </div>
</div>

<script>
console.log('My Reports Dashboard Loaded');
console.log('Current Filter:', '{{ $filter }}');
console.log('Period:', '{{ $period }}');
console.log('Total Submissions:', {{ $mySubmissions->count() }});
console.log('Timezone: America/New_York (Eastern Time)');

// Comment modal functions
function showCommentModal(comment, title, customerName) {
    const modal = document.getElementById('commentModal');
    const modalTitle = document.getElementById('commentModalTitle');
    const commentContent = document.getElementById('commentContent');
    const commentCustomerName = document.getElementById('commentCustomerName');
    
    modalTitle.textContent = title;
    commentCustomerName.textContent = customerName;
    
    if (!comment || comment.trim() === '' || comment === 'null') {
        commentContent.innerHTML = '<div class="no-comment-text">💭 No comment available</div>';
    } else {
        commentContent.textContent = comment;
    }
    
    modal.style.display = 'block';
}

// Close modal functionality
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('commentModal');
    const closeBtn = document.querySelector('.comment-close');
    
    if (closeBtn) {
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }
    }
    
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'block') {
            modal.style.display = 'none';
        }
    });
    
    // Animate summary cards
    document.querySelectorAll('.summary-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Animate carrier cards
    document.querySelectorAll('.carrier-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'scale(0.9)';
        setTimeout(() => {
            card.style.transition = 'all 0.4s ease';
            card.style.opacity = '1';
            card.style.transform = 'scale(1)';
        }, 200 + (index * 30));
    });
    
    // Animate table rows
    document.querySelectorAll('.submissions-table tbody tr').forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            row.style.transition = 'all 0.4s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, 400 + (index * 50));
    });
});

// Auto refresh every 5 minutes
setInterval(() => {
    console.log('Auto-refreshing my reports...');
    window.location.reload();
}, 300000);

console.log('My Reports Dashboard JavaScript Loaded Successfully!');
</script>
@endsection
@extends('layouts.admin')

@section('title', 'Closer Reports')

@section('content')
<style>
    /* Closer Reports Styles */
    .closer-reports-dashboard {
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
        background: linear-gradient(135deg, #047857, #10b981, #34d399);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.75rem;
        letter-spacing: -0.025em;
    }

    .subtitle {
        font-size: 1.125rem;
        color: #6b7280;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    /* Filter Buttons */
    .filter-container {
        display: inline-flex;
        background: #ffffff;
        border-radius: 12px;
        padding: 6px;
        box-shadow: 0 6px 24px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .filter-btn {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.875rem;
        color: #6b7280;
        transition: all 0.2s ease;
    }

    .filter-btn.active {
        background: linear-gradient(135deg, #047857, #10b981);
        color: #ffffff;
        box-shadow: 0 3px 12px rgba(4, 120, 87, 0.2);
    }

    .filter-btn:hover:not(.active) {
        background: #f1f5f9;
        color: #1f2937;
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
        background: #ffffff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 6px 24px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
        background: linear-gradient(90deg, #047857, #34d399);
        transition: transform 0.3s ease;
        transform: scaleX(0);
        transform-origin: left;
    }

    .summary-card:hover::before {
        transform: scaleX(1);
    }

    .summary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 36px rgba(0,0,0,0.12);
    }

    .summary-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .summary-card-title {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .summary-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #047857;
        font-size: 1.25rem;
    }

    .summary-card-value {
        font-size: 2.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .summary-card-desc {
        font-size: 0.875rem;
        color: #6b7280;
    }

    /* Competition Section */
    .competition-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .competition-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 6px 24px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .competition-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #047857, #34d399);
    }

    .competition-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 36px rgba(0,0,0,0.12);
    }

    .competition-rank {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .competition-rank::before {
        content: '🏆';
        font-size: 1.5rem;
    }

    .competition-name {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .competition-metric {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
        display: flex;
        justify-content: space-between;
    }

    .competition-metric span:last-child {
        font-weight: 600;
        color: #1f2937;
    }

    /* Action Bar */
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        background: #ffffff;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
    }

    .action-bar h2 {
        font-size: 1.375rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #047857, #10b981);
        color: #ffffff;
        box-shadow: 0 3px 12px rgba(4, 120, 87, 0.2);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #065f46, #059669);
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(4, 120, 87, 0.3);
    }

    .btn-secondary {
        background: #f8fafc;
        color: #6b7280;
        border: 1px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #f1f5f9;
        color: #1f2937;
        transform: translateY(-1px);
    }

    /* Closer Reports Table */
    .reports-table-container {
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 24px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
    }

    .reports-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .reports-table th {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 1.25rem 1rem;
        text-align: left;
        font-weight: 600;
        color: #374151;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        position: relative;
        cursor: pointer;
        user-select: none;
    }

    .reports-table th.sortable:hover {
        background: #e5e7eb;
    }

    .reports-table th.sortable::after {
        content: '↕';
        position: absolute;
        right: 0.5rem;
        font-size: 0.75rem;
        color: #9ca3af;
    }

    .reports-table th.sort-asc::after {
        content: '↑';
    }

    .reports-table th.sort-desc::after {
        content: '↓';
    }

    .reports-table td {
        padding: 1.25rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1f2937;
    }

    .reports-table tr:hover {
        background: #f8fafc;
    }

    .reports-table tr:last-child td {
        border-bottom: none;
    }

    .closer-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .closer-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #047857, #34d399);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .closer-details h4 {
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.25rem 0;
        font-size: 0.875rem;
    }

    .closer-details span {
        color: #6b7280;
        font-size: 0.75rem;
        display: block;
    }

    .metric-value {
        font-weight: 600;
        font-size: 1rem;
        color: #1f2937;
    }

    .metric-approved {
        color: #047857;
    }

    .metric-pending {
        color: #dc2626;
    }

    /* NEW: Premium display styles */
    .premium-cell {
        text-align: center;
        padding: 1rem !important;
    }
    
    .premium-value {
        font-size: 1.25rem;
        font-weight: bold;
        color: #0f766e;
        margin-bottom: 2px;
    }
    
    .premium-desc {
        font-size: 0.7rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .performance-rate {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .performance-badge {
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .performance-excellent {
        background: #d1fae5;
        color: #047857;
    }

    .performance-good {
        background: #fef3c7;
        color: #b45309;
    }

    .performance-average {
        background: #ffedd5;
        color: #c2410c;
    }

    .performance-needs-improvement {
        background: #fee2e2;
        color: #b91c1c;
    }

    .performance-inactive {
        background: #e5e7eb;
        color: #6b7280;
    }

    .action-cell {
        text-align: right;
    }

    .view-details-btn {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1d4ed8;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .view-details-btn:hover {
        background: linear-gradient(135deg, #bfdbfe, #93c5fd);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(29, 78, 216, 0.2);
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background: #ffffff;
        margin: 5% auto;
        padding: 0;
        border-radius: 12px;
        width: 90%;
        max-width: 800px;
        max-height: 80vh;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(-50px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .modal-header {
        padding: 1.5rem;
        background: linear-gradient(135deg, #047857, #10b981);
        color: #ffffff;
    }

    .modal-header h3 {
        margin: 0 0 0.5rem 0;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .modal-header p {
        margin: 0;
        font-size: 0.875rem;
        opacity: 0.9;
    }

    .modal-body {
        padding: 1.5rem;
        max-height: 50vh;
        overflow-y: auto;
    }

    .close {
        position: absolute;
        right: 1.5rem;
        top: 1.5rem;
        color: #ffffff;
        font-size: 1.5rem;
        font-weight: 700;
        cursor: pointer;
        transition: opacity 0.2s ease;
    }

    .close:hover {
        opacity: 0.7;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6b7280;
    }

    .empty-state-icon {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        opacity: 0.4;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #374151;
    }

    .empty-state p {
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    /* Loading States */
    .loading-container {
        text-align: center;
        padding: 2.5rem;
    }

    .loading-spinner {
        width: 28px;
        height: 28px;
        border: 3px solid #e2e8f0;
        border-top: 3px solid #047857;
        border-radius: 50%;
        margin: 0 auto 0.75rem;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .main-container {
            padding: 1rem;
        }

        .main-title {
            font-size: 2rem;
        }

        .summary-grid, .competition-container {
            grid-template-columns: 1fr;
        }

        .action-bar {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .action-buttons {
            justify-content: center;
        }

        .reports-table-container {
            overflow-x: auto;
        }

        .reports-table {
            min-width: 800px;
        }

        .modal-content {
            width: 95%;
            margin: 10% auto;
        }
    }
</style>

<div class="closer-reports-dashboard">
    <header style="
    background: linear-gradient(135deg,rgb(135, 163, 208), #334155);
    padding: 0.5rem 1rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 100;
    border-bottom: 3px solid #3b82f6;
">
    <div style="
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    ">
        <!-- Logo/Brand -->
        <div style="
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        ">
            📊 Reports 
        </div>

        <!-- Navigation Links -->
        <nav style="
            display: flex;
            gap: 0;
            background: rgba(255,255,255,0.1);
            padding: 4px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
        ">
            <a href="{{ route('center.competition') }}" 
               style="
                   padding: 10px 20px;
                   color: #ffffff;
                   text-decoration: none;
                   border-radius: 6px;
                   font-weight: 500;
                   font-size: 0.875rem;
                   transition: all 0.3s ease;
                   background: {{ request()->routeIs('center.competition') ? 'rgba(59, 130, 246, 0.8)' : 'transparent' }};
                   display: flex;
                   align-items: center;
                   gap: 0.5rem;
               "
               onmouseover="this.style.background='rgba(59, 130, 246, 0.6)'; this.style.transform='translateY(-1px)'"
               onmouseout="this.style.background='{{ request()->routeIs('center.competition') ? 'rgba(59, 130, 246, 0.8)' : 'transparent' }}'; this.style.transform='translateY(0)'">
                🏆 Dashboard
            </a>

            <a href="{{ route('client.reports') }}" 
               style="
                   padding: 10px 20px;
                   color: #ffffff;
                   text-decoration: none;
                   border-radius: 6px;
                   font-weight: 500;
                   font-size: 0.875rem;
                   transition: all 0.3s ease;
                   background: {{ request()->routeIs('client.reports') ? 'rgba(16, 185, 129, 0.8)' : 'transparent' }};
                   display: flex;
                   align-items: center;
                   gap: 0.5rem;
               "
               onmouseover="this.style.background='rgba(16, 185, 129, 0.6)'; this.style.transform='translateY(-1px)'"
               onmouseout="this.style.background='{{ request()->routeIs('client.reports') ? 'rgba(16, 185, 129, 0.8)' : 'transparent' }}'; this.style.transform='translateY(0)'">
                👥 Client Reports
            </a>

            <a href="{{ route('closer.reports') }}" 
               style="
                   padding: 10px 20px;
                   color: #ffffff;
                   text-decoration: none;
                   border-radius: 6px;
                   font-weight: 500;
                   font-size: 0.875rem;
                   transition: all 0.3s ease;
                   background: {{ request()->routeIs('closer.reports') ? 'rgba(239, 68, 68, 0.8)' : 'transparent' }};
                   display: flex;
                   align-items: center;
                   gap: 0.5rem;
               "
               onmouseover="this.style.background='rgba(239, 68, 68, 0.6)'; this.style.transform='translateY(-1px)'"
               onmouseout="this.style.background='{{ request()->routeIs('closer.reports') ? 'rgba(239, 68, 68, 0.8)' : 'transparent' }}'; this.style.transform='translateY(0)'">
                🎯 Closer Reports
            </a>
        </nav>
    </div>

    <!-- Mobile Menu Toggle -->
    <button id="mobileMenuToggle" 
            style="
                display: none;
                background: none;
                border: none;
                color: #ffffff;
                font-size: 1.5rem;
                cursor: pointer;
                padding: 0.5rem;
            "
            onclick="toggleMobileMenu()">
        ☰
    </button>
</header>

<!-- Mobile Navigation -->
<nav id="mobileNav" 
     style="
         display: none;
         background: linear-gradient(135deg, #1e293b, #334155);
         padding: 1rem;
         box-shadow: 0 2px 8px rgba(0,0,0,0.1);
         position: sticky;
         top: 70px;
         z-index: 99;
     ">
    <div style="
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        max-width: 1200px;
        margin: 0 auto;
    ">
        <a href="{{ route('center.competition') }}" 
           style="
               padding: 12px 16px;
               color: #ffffff;
               text-decoration: none;
               border-radius: 6px;
               font-weight: 500;
               background: {{ request()->routeIs('center.competition') ? 'rgba(59, 130, 246, 0.8)' : 'rgba(255,255,255,0.1)' }};
               display: flex;
               align-items: center;
               gap: 0.5rem;
           ">
            🏆 Dashboard
        </a>

        <a href="{{ route('client.reports') }}" 
           style="
               padding: 12px 16px;
               color: #ffffff;
               text-decoration: none;
               border-radius: 6px;
               font-weight: 500;
               background: {{ request()->routeIs('client.reports') ? 'rgba(16, 185, 129, 0.8)' : 'rgba(255,255,255,0.1)' }};
               display: flex;
               align-items: center;
               gap: 0.5rem;
           ">
            👥 Client Reports
        </a>

        <a href="{{ route('closer.reports') }}" 
           style="
               padding: 12px 16px;
               color: #ffffff;
               text-decoration: none;
               border-radius: 6px;
               font-weight: 500;
               background: {{ request()->routeIs('closer.reports') ? 'rgba(239, 68, 68, 0.8)' : 'rgba(255,255,255,0.1)' }};
               display: flex;
               align-items: center;
               gap: 0.5rem;
           ">
            🎯 Closer Reports
        </a>
    </div>
</nav>

    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <h1 class="main-title">🏆 Closer Reports Dashboard</h1>
            <p class="subtitle">Track closer performance, sales metrics, and premium generation by day, week, and month</p>
            
            <!-- Filter Buttons -->
            <!-- Filter Buttons -->
<div class="filter-container">
    <a href="{{ route('closer.reports', ['filter' => 'daily']) }}" 
       class="filter-btn {{ $filter === 'daily' ? 'active' : '' }}">
        📅 Daily
    </a>
    <a href="{{ route('closer.reports', ['filter' => 'weekly']) }}" 
       class="filter-btn {{ $filter === 'weekly' ? 'active' : '' }}">
        📊 Weekly
    </a>
    <a href="{{ route('closer.reports', ['filter' => 'monthly']) }}" 
       class="filter-btn {{ $filter === 'monthly' ? 'active' : '' }}">
        📈 Monthly
    </a>
    
    <!-- Month Picker -->
    <form method="GET" action="{{ route('closer.reports') }}" style="display:inline-block; margin-left:1rem;">
        <input type="month" name="month_year" value="{{ request('month_year') }}" class="filter-btn" onchange="this.form.submit()">
    </form>

    <!-- NEW: Date Picker for Specific Day -->
    <form method="GET" action="{{ route('closer.reports') }}" style="display:inline-block; margin-left:0.5rem;">
        <input type="date" 
               name="specific_date" 
               value="{{ request('specific_date') }}" 
               class="filter-btn" 
               onchange="this.form.submit()"
               placeholder="Pick a date"
               style="cursor: pointer;">
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
                <div class="summary-card-value">{{ number_format($summary['total_submissions']) }}</div>
                <div class="summary-card-desc">{{ $period }}</div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">Approved/Sales</div>
                    <div class="summary-card-icon">✅</div>
                </div>
                <div class="summary-card-value">{{ number_format($summary['total_approved']) }}</div>
                <div class="summary-card-desc">{{ $summary['average_conversion'] }}% avg conversion</div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">Pending</div>
                    <div class="summary-card-icon">⏳</div>
                </div>
                <div class="summary-card-value">{{ number_format($summary['total_pending']) }}</div>
                <div class="summary-card-desc">Awaiting approval</div>
            </div>

            <!-- NEW: Average Premium Summary Card -->
            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">Average Premium</div>
                    <div class="summary-card-icon">💰</div>
                </div>
                <div class="summary-card-value">${{ number_format($summary['average_premium'], 2) }}</div>
                <div class="summary-card-desc">Monthly average</div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">Active Closers</div>
                    <div class="summary-card-icon">👥</div>
                </div>
                <div class="summary-card-value">{{ number_format($summary['active_closers']) }}</div>
                <div class="summary-card-desc">Across {{ $summary['center_stats']->count() }} centers</div>
            </div>
        </div>

        <!-- Competition Section -->
        <div class="competition-container">
            @foreach($competition as $competitor)
                <div class="competition-card">
                    <div class="competition-rank">Rank #{{ $competitor['rank'] }}</div>
                    <div class="competition-name">{{ $competitor['name'] }}</div>
                    <div class="competition-metric">Center: <span>{{ $competitor['center'] }}</span></div>
                    <div class="competition-metric">Submissions: <span>{{ number_format($competitor['submissions']) }}</span></div>
                    <div class="competition-metric">Approved: <span>{{ number_format($competitor['approved']) }}</span></div>
                    <div class="competition-metric">Conversion: <span>{{ $competitor['conversion_rate'] }}%</span></div>
                    <div class="competition-metric">Pending: <span>{{ number_format($competitor['pending']) }}</span></div>
                </div>
            @endforeach
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <h2>Closer Performance Report - {{ $period }}</h2>
            <div class="action-buttons">
                <a href="{{ route('closer.reports.export', ['filter' => $filter]) }}" class="btn btn-primary">
                    📥 Export CSV
                </a>
                <button onclick="window.location.reload()" class="btn btn-secondary">
                    🔄 Refresh
                </button>
            </div>
        </div>

        <!-- UPDATED CLOSER REPORTS TABLE WITH PREMIUM COLUMN -->
        <div class="reports-table-container">
            @if($closerReports->isNotEmpty())
                <table class="reports-table">
                    <thead>
                        <tr>
                            <th>Closer</th>
                            <th>Designation</th>
                            <th>Company/Center</th>
                            <th>Submissions</th>
                            <th>Approved/Sales</th>
                            <th>Pending</th>
                            <th style="text-align: center;">Avg Premium</th>
                            <th>Performance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($closerReports as $report)
                            <tr>
                                <!-- Closer Info -->
                                <td>
                                    <div class="closer-info">
                                        <div class="closer-avatar">
                                            {{ strtoupper(substr($report->closer_name, 0, 1)) }}
                                        </div>
                                        <div class="closer-details">
                                            <h4>{{ $report->closer_name }}</h4>
                                           
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Designation -->
                                <td>{{ $report->designation }}</td>
                                
                                <!-- Company/Center -->
                                <td>{{ $report->company }}</td>
                                
                                <!-- Submissions -->
                                <td>
                                    <div class="metric-value">{{ number_format($report->total_submissions) }}</div>
                                </td>
                                
                                <!-- Approved/Sales -->
                                <td>
                                    <div class="metric-value metric-approved">{{ number_format($report->approved_count) }}</div>
                                </td>
                                
                                <!-- Pending -->
                                <td>
                                    <div class="metric-value metric-pending">{{ number_format($report->pending_count) }}</div>
                                </td>
                                
                                <!-- NEW: Average Premium Column -->
                                <td class="premium-cell">
                                    <div class="premium-value">${{ number_format($report->avg_premium, 2) }}</div>
                                    <div class="premium-desc">Monthly</div>
                                    @if($report->yearly_premium_estimate > 0)
                                        <div style="font-size: 0.65rem; color: #10b981; margin-top: 2px;">
                                            Est. ${{{ number_format($report->yearly_premium_estimate, 0) }}}/year
                                        </div>
                                    @endif
                                </td>
                                
                                <!-- Performance -->
                                <td>
                                    <div class="performance-rate">
                                        <span class="metric-value">{{ number_format($report->conversion_rate, 2) }}%</span>
                                        <span class="performance-badge 
                                            @if($report->performance_rating == 'Excellent') performance-excellent
                                            @elseif($report->performance_rating == 'Good') performance-good
                                            @elseif($report->performance_rating == 'Average') performance-average
                                            @elseif($report->performance_rating == 'Needs Improvement') performance-needs-improvement
                                            @else performance-inactive
                                            @endif">
                                            {{ $report->performance_rating }}
                                        </span>
                                    </div>
                                </td>
                                
                                <!-- Actions -->
                                <td class="action-cell">
                                    <button class="view-details-btn" 
                                            onclick="viewCloserDetails({{ $report->closer_id ?: 'null' }}, '{{ addslashes($report->closer_name) }}')">
                                        👁️ View Details
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📊</div>
                    <h3>No Closer Data Found</h3>
                    <p>No data found for the selected closer in {{ $period }}.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- UPDATED CLOSER DETAILS MODAL WITH PREMIUM INFORMATION -->
<div id="closerModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">×</span>
            <h3 id="modalCloserName">Closer Details</h3>
            <p id="modalCloserInfo">Loading closer information...</p>
        </div>
        <div class="modal-body">
            <div id="modalContent">
                <div class="loading-container">
                    <div class="loading-spinner"></div>
                    <p>Loading closer details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let currentFilter = '{{ $filter }}';
let modalElement = document.getElementById('closerModal');
let closeButton = document.querySelector('.close');

console.log('Closer Reports Dashboard Loaded');
console.log('Current Filter:', currentFilter);
console.log('Closer Reports Count:', {{ $closerReports->count() }});

// Mobile menu toggle function
function toggleMobileMenu() {
    const mobileNav = document.getElementById('mobileNav');
    const isVisible = mobileNav.style.display === 'block';
    mobileNav.style.display = isVisible ? 'none' : 'block';
}

// Responsive behavior
function handleResize() {
    const nav = document.querySelector('nav');
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const mobileNav = document.getElementById('mobileNav');
    
    if (window.innerWidth <= 768) {
        nav.style.display = 'none';
        mobileToggle.style.display = 'block';
    } else {
        nav.style.display = 'flex';
        mobileToggle.style.display = 'none';
        mobileNav.style.display = 'none';
    }
}

// Table Sorting Functionality
let tableData = @json($closerReports);
let sortDirection = {};
let currentSortColumn = null;

function sortTable(column, isNumeric = false) {
    if (currentSortColumn === column) {
        sortDirection[column] = sortDirection[column] === 'asc' ? 'desc' : 'asc';
    } else {
        sortDirection[column] = 'asc';
        currentSortColumn = column;
    }

    // Update sort indicators
    document.querySelectorAll('.sortable').forEach(th => {
        th.classList.remove('sort-asc', 'sort-desc');
        if (th.getAttribute('data-sort') === column) {
            th.classList.add(`sort-${sortDirection[column]}`);
        }
    });

    // Sort data
    tableData.sort((a, b) => {
        let valA = a[column], valB = b[column];
        if (isNumeric) {
            valA = parseFloat(valA) || 0;
            valB = parseFloat(valB) || 0;
        } else {
            valA = (valA || '').toString().toLowerCase();
            valB = (valB || '').toString().toLowerCase();
        }

        if (valA < valB) return sortDirection[column] === 'asc' ? -1 : 1;
        if (valA > valB) return sortDirection[column] === 'asc' ? 1 : -1;
        return 0;
    });

    // Render sorted table
    renderTable();
}

function renderTable() {
    const tbody = document.querySelector('.reports-table tbody');
    tbody.innerHTML = '';

    tableData.forEach(report => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div class="closer-info">
                    <div class="closer-avatar">${escapeHtml(report.closer_name.charAt(0).toUpperCase())}</div>
                    <div class="closer-details">
                        <h4>${escapeHtml(report.closer_name)}</h4>
                        <span>ID: ${report.closer_id || 'N/A'}</span>
                        ${report.closer_email ? `<br><span>${escapeHtml(report.closer_email)}</span>` : ''}
                    </div>
                </div>
            </td>
            <td>${escapeHtml(report.designation)}</td>
            <td>${escapeHtml(report.company)}</td>
            <td><div class="metric-value">${Number(report.total_submissions).toLocaleString()}</div></td>
            <td><div class="metric-value metric-approved">${Number(report.approved_count).toLocaleString()}</div></td>
            <td><div class="metric-value metric-pending">${Number(report.pending_count).toLocaleString()}</div></td>
            <td class="premium-cell">
                <div class="premium-value">${Number(report.avg_premium || 0).toFixed(2)}</div>
                <div class="premium-desc">Monthly</div>
                ${(report.yearly_premium_estimate || 0) > 0 ? `<div style="font-size: 0.65rem; color: #10b981; margin-top: 2px;">Est. ${Number(report.yearly_premium_estimate).toLocaleString()}/year</div>` : ''}
            </td>
            <td>
                <div class="performance-rate">
                    <span class="metric-value">${Number(report.conversion_rate).toFixed(2)}%</span>
                    <span class="performance-badge performance-${report.performance_rating.toLowerCase().replace(/\s+/g, '-')}">
                        ${escapeHtml(report.performance_rating)}
                    </span>
                </div>
            </td>
            <td class="action-cell">
                <button class="view-details-btn" onclick="viewCloserDetails(${report.closer_id || 'null'}, '${addslashes(report.closer_name)}')">
                    👁️ View Details
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Escape HTML to prevent XSS
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Modal functionality
function viewCloserDetails(closerId, closerName) {
    console.log('Viewing details for closer:', closerId, closerName);
    
    // Show modal
    modalElement.style.display = 'block';
    document.getElementById('modalCloserName').textContent = closerName;
    document.getElementById('modalCloserInfo').textContent = `Loading details for ${closerName}...`;
    
    // Show loading state
    document.getElementById('modalContent').innerHTML = `
        <div class="loading-container">
            <div class="loading-spinner"></div>
            <p>Loading closer details...</p>
        </div>
    `;
    
    // Fetch closer details
    fetchCloserDetails(closerId, closerName);
}

function fetchCloserDetails(closerId, closerName) {
    const url = `{{ route('closer.details') }}?closer_id=${closerId || ''}&closer_name=${encodeURIComponent(closerName)}&filter=${currentFilter}`;
    
    console.log('Fetching closer details from:', url);
    
    fetch(url)
        .then(response => {
            console.log('Closer details response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Closer details data:', data);
            renderCloserDetails(data);
        })
        .catch(error => {
            console.error('Error fetching closer details:', error);
            document.getElementById('modalContent').innerHTML = `
                <div style="text-align: center; padding: 2rem; color: #dc2626;">
                    <h4>Error Loading Details</h4>
                    <p>${error.message}</p>
                    <button onclick="fetchCloserDetails(${closerId || 'null'}, '${addslashes(closerName)}')" class="btn btn-primary" style="margin-top: 1rem;">
                        🔄 Retry
                    </button>
                </div>
            `;
        });
}

function renderCloserDetails(data) {
    const { closer, stats, submissions, period } = data;
    
    // Update modal header
    document.getElementById('modalCloserName').textContent = closer.name;
    document.getElementById('modalCloserInfo').textContent = 
        `${closer.email || 'No email'} • ${closer.designation} • ${period.filter.charAt(0).toUpperCase() + period.filter.slice(1)} Report`;
    
    // Render modal content with ENHANCED premium breakdown
    let html = `
        <div style="margin-bottom: 2rem;">
            <h4 style="color: #1f2937; margin-bottom: 1rem; font-weight: 600;">📊 Performance Summary</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <div style="background: #f0fdf4; padding: 1rem; border-radius: 8px; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 600; color: #047857;">${stats.total_submissions}</div>
                    <div style="font-size: 0.875rem; color: #6b7280;">Total Submissions</div>
                </div>
                <div style="background: #ecfdf5; padding: 1rem; border-radius: 8px; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 600; color: #047857;">${stats.approved_count}</div>
                    <div style="font-size: 0.875rem; color: #6b7280;">Approved/Sales</div>
                </div>
                <div style="background: #fef2f2; padding: 1rem; border-radius: 8px; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 600; color: #dc2626;">${stats.pending_count}</div>
                    <div style="font-size: 0.875rem; color: #6b7280;">Pending</div>
                </div>
                <div style="background: #fffbeb; padding: 1rem; border-radius: 8px; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 600; color: #b45309;">${stats.conversion_rate}%</div>
                    <div style="font-size: 0.875rem; color: #6b7280;">Conversion</div>
                </div>
            </div>
            
            <!-- NEW: PREMIUM PERFORMANCE BREAKDOWN -->
            ${stats.avg_premium !== undefined ? `
            <div style="margin-bottom: 2rem;">
                <h4 style="color: #1f2937; margin-bottom: 1rem; font-weight: 600;">💰 Premium Performance</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); padding: 1.5rem; border-radius: 16px; text-align: center; border: 2px solid #10b981; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #047857, #10b981);"></div>
                        <div style="font-size: 2rem; font-weight: bold; color: #047857; margin-bottom: 0.5rem;">${stats.avg_premium}</div>
                        <div style="font-size: 1rem; color: #064e3b; font-weight: 600;">Average Premium</div>
                        <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.5rem;">Monthly average</div>
                    </div>
                    <div style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe); padding: 1.5rem; border-radius: 16px; text-align: center; border: 2px solid #0ea5e9; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #0284c7, #0ea5e9);"></div>
                        <div style="font-size: 2rem; font-weight: bold; color: #0284c7; margin-bottom: 0.5rem;">${stats.yearly_estimate}</div>
                        <div style="font-size: 1rem; color: #0c4a6e; font-weight: 600;">Yearly Estimate</div>
                        <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.5rem;">Total volume projection</div>
                    </div>
                </div>
                
                ${stats.premium_submissions > 0 ? `
                <div style="margin-top: 1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div style="font-size: 1.25rem; font-weight: bold; color: #1f2937;">${stats.min_premium}</div>
                        <div style="font-size: 0.875rem; color: #6b7280;">Minimum Premium</div>
                    </div>
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div style="font-size: 1.25rem; font-weight: bold; color: #1f2937;">${stats.max_premium}</div>
                        <div style="font-size: 0.875rem; color: #6b7280;">Maximum Premium</div>
                    </div>
                </div>
                ` : ''}
            </div>
            ` : ''}
        </div>
    `;
    
    if (submissions && submissions.length > 0) {
        html += `
            <div>
                <h4 style="color: #1f2937; margin-bottom: 1rem; font-weight: 600;">📋 Recent Submissions</h4>
                <div style="max-height: 300px; overflow-y: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280; border-bottom: 1px solid #e2e8f0;">Customer</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280; border-bottom: 1px solid #e2e8f0;">Status</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280; border-bottom: 1px solid #e2e8f0;">Premium</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280; border-bottom: 1px solid #e2e8f0;">Date</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280; border-bottom: 1px solid #e2e8f0;">Center</th>
                            </tr>
                        </thead>
                        <tbody>
        `;
        
        submissions.forEach((submission, index) => {
            const approvedStatuses = ['DNF', 'Funded', 'Charge Back', 'Approved', 'Underwriting'];
            const isApproved = approvedStatuses.includes(submission.status);
            const statusColor = isApproved ? '#047857' : '#dc2626';
            const statusBg = isApproved ? '#d1fae5' : '#fee2e2';
            const date = new Date(submission.created_at).toLocaleDateString();
            const premium = submission.monthly_premium ? `${parseFloat(submission.monthly_premium).toFixed(2)}` : 'N/A';
            const rowBg = index % 2 === 0 ? '#ffffff' : '#f9fafb';
            
            html += `
                <tr style="border-bottom: 1px solid #f1f5f9; background: ${rowBg};">
                    <td style="padding: 0.75rem; font-size: 0.875rem;">${escapeHtml(submission.customer_full_name || 'N/A')}</td>
                    <td style="padding: 0.75rem;">
                        <span style="background: ${statusBg}; color: ${statusColor}; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                            ${isApproved ? '✅' : '⏳'} ${submission.status.charAt(0).toUpperCase() + submission.status.slice(1)}
                        </span>
                    </td>
                    <td style="padding: 0.75rem; font-size: 0.875rem; color: #047857; font-weight: 600;">${premium}</td>
                    <td style="padding: 0.75rem; font-size: 0.875rem; color: #6b7280;">${date}</td>
                    <td style="padding: 0.75rem; font-size: 0.875rem; color: #6b7280;">${escapeHtml(submission.center_name || 'N/A')}</td>
                </tr>
            `;
        });
        
        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    } else {
        html += `
            <div style="text-align: center; padding: 2rem; color: #6b7280;">
                <p>No submissions found for this period.</p>
            </div>
        `;
    }
    
    document.getElementById('modalContent').innerHTML = html;
}

// Close modal functionality
closeButton.onclick = function() {
    modalElement.style.display = 'none';
}

window.onclick = function(event) {
    if (event.target === modalElement) {
        modalElement.style.display = 'none';
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && modalElement.style.display === 'block') {
        modalElement.style.display = 'none';
    }
});

// Initialize responsive behavior
window.addEventListener('resize', handleResize);
window.addEventListener('load', handleResize);

// Auto refresh every 5 minutes
setInterval(() => {
    console.log('Auto-refreshing closer reports...');
    window.location.reload();
}, 300000);

// Add entrance animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate summary cards
    document.querySelectorAll('.summary-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Animate competition cards
    document.querySelectorAll('.competition-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 200 + (index * 100));
    });
    
    // Animate table rows
    document.querySelectorAll('.reports-table tbody tr').forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            row.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, 400 + (index * 50));
    });

    // Initialize sort handlers
    document.querySelectorAll('.sortable').forEach(th => {
        const column = th.getAttribute('data-sort');
        const isNumeric = ['total_submissions', 'approved_count', 'pending_count', 'conversion_rate', 'avg_premium'].includes(column);
        th.onclick = () => sortTable(column, isNumeric);
    });
});

console.log('Closer Reports Dashboard JavaScript Loaded Successfully! 🎉');
</script>

<style>
/* Additional responsive styles */
@media (max-width: 768px) {
    header > div {
        flex-direction: row !important;
        justify-content: space-between !important;
    }
    
    header nav {
        display: none !important;
    }
    
    #mobileMenuToggle {
        display: block !important;
    }
}

/* Smooth animations */
header a {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

/* Active link glow effect */
header a[style*="rgba(59, 130, 246, 0.8)"] {
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.4) !important;
}

header a[style*="rgba(16, 185, 129, 0.8)"] {
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.4) !important;
}

header a[style*="rgba(239, 68, 68, 0.8)"] {
    box-shadow: 0 0 20px rgba(239, 68, 68, 0.4) !important;
}
</style>

@endsection)
@extends('layouts.admin')

@section('title', 'Complete Junior Closer Reports with All Status Columns and Carrier Breakdown')

@section('content')
<!-- SheetJS Library for Excel Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<style>
    .detailed-reports-dashboard {
        min-height: 100vh;
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 0;
        margin: 0;
    }

    .main-container {
        max-width: 100%;
        margin: 0;
        padding: 1rem;
        overflow-x: hidden;
    }

    .header-section {
        text-align: center;
        margin-bottom: 2rem;
        background: white;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .main-title {
        font-size: 3rem;
        font-weight: 900;
        background: linear-gradient(135deg, #7c3aed, #a855f7, #c084fc);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .subtitle {
        font-size: 1.2rem;
        color: #64748b;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .client-count {
        display: inline-block;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 700;
        margin-top: 1rem;
    }

    .filter-container {
        display: inline-flex;
        background: white;
        border-radius: 16px;
        padding: 8px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .filter-btn {
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s ease;
        color: #64748b;
        font-size: 0.95rem;
        position: relative;
        overflow: hidden;
    }

    .filter-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s;
    }

    .filter-btn:hover::before {
        left: 100%;
    }

    .filter-btn.active {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: white;
        box-shadow: 0 6px 20px rgba(124, 58, 237, 0.4);
        transform: translateY(-2px);
    }

    .filter-btn:hover:not(.active) {
        background: #f1f5f9;
        color: #334155;
        transform: translateY(-1px);
    }

    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        background: white;
        padding: 2rem 2.5rem;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
    }

    .action-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #7c3aed, #a855f7, #c084fc);
    }

    .action-bar h2 {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
        background: linear-gradient(135deg, #1e293b, #334155);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.3s, height 0.3s;
    }

    .btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: white;
        box-shadow: 0 4px 16px rgba(124, 58, 237, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(124, 58, 237, 0.4);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6b7280, #9ca3af);
        color: white;
        box-shadow: 0 4px 16px rgba(107, 114, 128, 0.3);
    }

    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(107, 114, 128, 0.4);
    }

    .detailed-table-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        position: relative;
    }

    .detailed-table-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #7c3aed, #a855f7, #c084fc, #ddd6fe);
        z-index: 1;
    }

    .table-wrapper {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 80vh;
        position: relative;
        scroll-behavior: smooth;
    }

    .detailed-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.75rem;
        min-width: fit-content;
        background: white;
    }

    .detailed-table thead {
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .detailed-table th {
        background: linear-gradient(135deg, #5b21b6 0%, #7c3aed 50%, #8b5cf6 100%);
        color: white;
        padding: 12px 6px;
        text-align: center;
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-right: 1px solid #8b5cf6;
        white-space: nowrap;
        line-height: 1.2;
        height: 50px;
        vertical-align: middle;
        min-width: 60px;
        position: relative;
    }

    .detailed-table th:last-child {
        border-right: none;
    }

    .detailed-table th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #a855f7, #c084fc);
    }

    .junior-closer-name-cell {
        text-align: left !important;
        font-weight: 700;
        color: #1e293b;
        background: linear-gradient(135deg, #faf5ff, #f3e8ff) !important;
        min-width: 180px !important;
        max-width: 180px !important;
        padding-left: 16px !important;
        position: sticky !important;
        left: 0 !important;
        z-index: 50 !important;
        border-right: 2px solid #7c3aed !important;
    }

    .center-cell {
        min-width: 100px !important;
        max-width: 100px !important;
        font-weight: 600;
        position: sticky !important;
        left: 180px !important;
        z-index: 50 !important;
        background: white !important;
        border-right: 2px solid #7c3aed !important;
    }

    .junior-closer-email {
        font-size: 0.65rem;
        color: #64748b;
        font-weight: 500;
        margin-top: 2px;
        opacity: 0.8;
    }

    .detailed-table th.junior-closer-name-cell {
        position: sticky !important;
        left: 0 !important;
        z-index: 101 !important;
        border-right: 2px solid #a855f7 !important;
    }

    .detailed-table th.center-cell {
        position: sticky !important;
        left: 180px !important;
        z-index: 101 !important;
        border-right: 2px solid #a855f7 !important;
    }

    .client-header {
        background: linear-gradient(135deg, #7c3aed, #a855f7) !important;
        color: white !important;
        font-weight: 900 !important;
        font-size: 0.8rem !important;
        border-left: 2px solid #c084fc !important;
        position: relative;
    }

    .client-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .client-subheader {
        background: linear-gradient(135deg, #a855f7, #c084fc) !important;
        color: white !important;
        font-weight: 700 !important;
        font-size: 0.65rem !important;
        min-width: 40px !important;
        padding: 8px 4px !important;
        border-right: 1px solid #7c3aed !important;
    }

    .carrier-subheader {
        background: linear-gradient(135deg, #c084fc, #ddd6fe) !important;
        color: #5b21b6 !important;
        font-weight: 600 !important;
        font-size: 0.6rem !important;
        min-width: 40px !important;
        padding: 8px 4px !important;
        border-right: 1px solid #7c3aed !important;
    }

    .metric-cell {
        font-weight: 700;
        min-width: 70px;
        font-size: 0.75rem;
        padding: 8px 6px;
    }

    .metric-submissions { 
        color: #7c3aed; 
        background: linear-gradient(135deg, #faf5ff, #f3e8ff); 
    }
    .metric-approved { 
        color: #059669; 
        background: linear-gradient(135deg, #f0fdf4, #dcfce7); 
    }
    .metric-pending { 
        color: #d97706; 
        background: linear-gradient(135deg, #fef3c7, #fed7aa); 
    }
    .metric-rejected { 
        color: #dc2626; 
        background: linear-gradient(135deg, #fef2f2, #fecaca); 
    }
    .metric-level { 
        color: #1d4ed8; 
        background: linear-gradient(135deg, #dbeafe, #bfdbfe); 
    }
    .metric-gi { 
        color: #d97706; 
        background: linear-gradient(135deg, #fef3c7, #fed7aa); 
    }

    .percentage-cell {
        font-weight: 700;
        min-width: 60px;
        font-size: 0.7rem;
        padding: 8px 4px;
        border-radius: 4px;
    }

    .percentage-high { 
        color: #059669; 
        background: linear-gradient(135deg, #dcfce7, #bbf7d0); 
    }
    .percentage-medium { 
        color: #d97706; 
        background: linear-gradient(135deg, #fef3c7, #fed7aa); 
    }
    .percentage-low { 
        color: #dc2626; 
        background: linear-gradient(135deg, #fecaca, #fca5a5); 
    }

    .premium-cell {
        font-weight: 700;
        color: #7c3aed;
        background: linear-gradient(135deg, #faf5ff, #f3e8ff);
        min-width: 80px;
        font-size: 0.75rem;
    }

    .client-data-cell {
        min-width: 40px;
        font-weight: 600;
        font-size: 0.7rem;
        background: #fafbfc;
        padding: 8px 4px;
        text-align: center;
        border-right: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }

    .client-data-cell:not(:empty) {
        background: linear-gradient(135deg, #faf5ff, #f3e8ff) !important;
        font-weight: 800 !important;
        color: #7c3aed !important;
    }

    .client-data-cell[data-value="0"] {
        background: linear-gradient(135deg, #fafafa, #f4f4f5) !important;
        color: #a1a1aa !important;
        font-weight: 500 !important;
        font-style: italic;
    }

    .client-data-cell:not([data-value="0"]) {
        background: linear-gradient(135deg, #faf5ff, #f3e8ff) !important;
        font-weight: 800 !important;
        color: #7c3aed !important;
    }

    .client-data-cell.client-submissions:not(:empty) { 
        color: #7c3aed !important; 
        background: linear-gradient(135deg, #faf5ff, #f3e8ff) !important;
    }
    .client-data-cell.client-approved:not(:empty) { 
        color: #059669 !important; 
        background: linear-gradient(135deg, #f0fdf4, #dcfce7) !important;
    }
    .client-data-cell.client-pending:not(:empty) { 
        color: #d97706 !important; 
        background: linear-gradient(135deg, #fef3c7, #fed7aa) !important;
    }
    .client-data-cell.client-rejected:not(:empty) { 
        color: #dc2626 !important; 
        background: linear-gradient(135deg, #fef2f2, #fecaca) !important;
    }
    .client-data-cell.client-level:not(:empty) { 
        color: #1d4ed8 !important; 
        background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important;
    }
    .client-data-cell.client-gi:not(:empty) { 
        color: #d97706 !important; 
        background: linear-gradient(135deg, #fef3c7, #fed7aa) !important;
    }
    .client-data-cell.client-app-percent:not(:empty) { 
        color: #059669 !important; 
        font-size: 0.65rem !important;
        background: linear-gradient(135deg, #f0fdf4, #dcfce7) !important;
    }
    .client-data-cell.client-pen-percent:not(:empty) { 
        color: #d97706 !important; 
        font-size: 0.65rem !important;
        background: linear-gradient(135deg, #fef3c7, #fed7aa) !important;
    }
    .client-data-cell.client-carrier:not(:empty) { 
        color: #1e40af !important; 
        background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important;
    }

    .detailed-table td {
        padding: 8px 6px;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
        text-align: center;
        vertical-align: middle;
        height: 45px;
        font-size: 0.75rem;
        line-height: 1.3;
        transition: all 0.2s ease;
    }

    .detailed-table td:last-child {
        border-right: none;
    }

    .detailed-table tbody tr {
        transition: all 0.3s ease;
        position: relative;
    }

    .detailed-table tbody tr:hover {
        background: linear-gradient(135deg, #faf5ff, #f3e8ff);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transform: translateY(-1px);
    }

    .detailed-table tbody tr:hover .client-data-cell:not(:empty) {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(124, 58, 237, 0.2);
    }

    .summary-row {
        background: linear-gradient(135deg, #5b21b6, #7c3aed, #8b5cf6) !important;
        color: white !important;
        font-weight: 800 !important;
        position: sticky !important;
        bottom: 0 !important;
        z-index: 90 !important;
        box-shadow: 0 -4px 16px rgba(0,0,0,0.2) !important;
    }

    .summary-row td {
        padding: 12px 6px !important;
        border-color: #8b5cf6 !important;
        font-size: 0.8rem !important;
        height: 50px !important;
        font-weight: 800 !important;
        border-top: 3px solid #a855f7 !important;
    }

    .summary-row .junior-closer-name-cell {
        position: sticky !important;
        left: 0 !important;
        z-index: 91 !important;
        background: linear-gradient(135deg, #5b21b6, #7c3aed) !important;
    }

    .summary-row .center-cell {
        position: sticky !important;
        left: 180px !important;
        z-index: 91 !important;
        background: linear-gradient(135deg, #5b21b6, #7c3aed) !important;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #64748b;
        background: linear-gradient(135deg, #faf5ff, #f3e8ff);
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        opacity: 0.6;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 0.6; }
        50% { opacity: 0.8; }
    }

    .stats-grid {
        margin-top: 2rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .stat-card {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        text-align: center;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #7c3aed, #a855f7, #c084fc);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    }

    .stat-card h4 {
        color: #1e293b;
        margin: 0 0 1rem 0;
        font-size: 1.1rem;
        font-weight: 700;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .stat-label {
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 500;
    }

    @media (max-width: 1600px) {
        .main-container { padding: 0.5rem; }
        .detailed-table { font-size: 0.65rem; }
        .client-subheader, .carrier-subheader { font-size: 0.6rem !important; padding: 6px 2px !important; }
        .client-data-cell { padding: 6px 3px; font-size: 0.65rem; }
        .client-header { min-width: 280px !important; }
        .junior-closer-name-cell { min-width: 150px !important; max-width: 150px !important; }
        .center-cell { left: 150px !important; }
        .detailed-table th.center-cell { left: 150px !important; }
        .summary-row .center-cell { left: 150px !important; }
    }

    @media (max-width: 1200px) {
        .main-title { font-size: 2.2rem; }
        .action-bar { flex-direction: column; gap: 1.5rem; text-align: center; padding: 1.5rem; }
        .filter-container { flex-direction: column; width: 100%; }
        .filter-btn { text-align: center; margin: 2px 0; }
        .table-wrapper { max-height: 70vh; }
    }

    .table-wrapper::-webkit-scrollbar {
        width: 12px;
        height: 12px;
    }

    .table-wrapper::-webkit-scrollbar-track {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        border-radius: 6px;
    }

    .table-wrapper::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        border-radius: 6px;
        border: 2px solid #f1f5f9;
    }

    .table-wrapper::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #5b21b6, #7c3aed);
    }

    .table-wrapper::-webkit-scrollbar-corner {
        background: #f1f5f9;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .loading-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e2e8f0;
        border-top: 4px solid #a855f7;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .scroll-hint {
        position: absolute;
        top: 15px;
        right: 20px;
        background: rgba(124, 58, 237, 0.9);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        z-index: 200;
        animation: fadeInOut 6s ease-in-out;
        pointer-events: none;
    }

    @keyframes fadeInOut {
        0% { opacity: 0; transform: translateY(-10px); }
        15% { opacity: 1; transform: translateY(0); }
        85% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-10px); }
    }

    @keyframes slideInFromRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutToRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .date-filter-container {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        background: linear-gradient(135deg, #faf5ff, #f3e8ff);
        padding: 8px 12px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .date-filter-container:hover {
        border-color: #a855f7;
        box-shadow: 0 4px 12px rgba(168, 85, 247, 0.1);
    }

    .date-filter-container input[type="date"] {
        padding: 6px 8px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.8rem;
        background: white;
        transition: all 0.2s ease;
    }

    .date-filter-container input[type="date"]:focus {
        border-color: #a855f7;
        box-shadow: 0 0 0 2px rgba(168, 85, 247, 0.1);
        outline: none;
    }

    @media (max-width: 1400px) {
        .action-bar {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }
        
        .action-bar > div:last-child {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .date-filter-container {
            flex-direction: column;
            gap: 0.25rem;
        }
    }
</style>

<div class="detailed-reports-dashboard">
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="main-container">
        <!-- Enhanced Header Section -->
        <div class="header-section">
            <h1 class="main-title">🌟 Junior Closer Analytics Dashboard</h1>
            <p class="subtitle">
                Comprehensive junior closer performance with status and carrier breakdown
                <br>Submissions • Approved • Pending • Rejected • Level • GI • Carriers
            </p>
            
            @if(isset($allClients) && $allClients->count() > 0)
                <div class="client-count">
                    📈 {{ $allClients->count() }} Active Clients | 
                    👨‍💼 {{ isset($juniorCloserReports) ? $juniorCloserReports->count() : 0 }} Junior Closers |
                    📅 {{ $period }} | 
                    📊 {{ count($activeCarriers) }} Active Carriers
                </div>
            @endif
            
            <!-- Enhanced Filter Buttons -->
            <div class="filter-container" style="margin-top: 2rem;">
                <a href="{{ route('jc.detailed.report', ['filter' => 'daily']) }}" 
                   class="filter-btn {{ $filter === 'daily' ? 'active' : '' }}">
                    📅 Daily Analysis
                </a>
                <a href="{{ route('jc.detailed.report', ['filter' => 'weekly']) }}" 
                   class="filter-btn {{ $filter === 'weekly' ? 'active' : '' }}">
                    📊 Weekly Breakdown
                </a>
                <a href="{{ route('jc.detailed.report', ['filter' => 'monthly']) }}" 
                   class="filter-btn {{ $filter === 'monthly' ? 'active' : '' }}">
                    📈 Monthly Deep Dive
                </a>
            </div>
        </div>

        <!-- Enhanced Action Bar -->
        <div class="action-bar">
            <div>
                <h2>🌟 Junior Closer Performance Report - {{ $period }}</h2>
                <div style="font-size: 0.9rem; color: #64748b; margin-top: 0.5rem;">
                    Updated: {{ now()->format('M d, Y - g:i A') }} | Full Status & Carrier Breakdown
                </div>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <!-- Date Filter -->
                <div class="date-filter-container">
                    <label style="font-size: 0.8rem; font-weight: 600; color: #64748b;">📅 Date Range:</label>
                    <input type="date" id="startDate" value="{{ $startDate->format('Y-m-d') }}" 
                           style="padding: 6px 8px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.8rem;">
                    <span style="color: #64748b;">to</span>
                    <input type="date" id="endDate" value="{{ $endDate->format('Y-m-d') }}" 
                           style="padding: 6px 8px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.8rem;">
                    <button onclick="applyDateFilter()" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.8rem;">
                        Apply
                    </button>
                </div>
                
                <input type="text" id="juniorCloserSearch" placeholder="🔍 Search junior closers..." 
                       style="padding: 10px 15px; border: 2px solid #e2e8f0; border-radius: 10px; 
                              font-size: 0.9rem; width: 200px; transition: all 0.3s ease;">
                
                <button onclick="exportToExcel()" class="btn btn-primary" id="exportBtn">
                    📊 Export Complete Data
                </button>
                <a href="{{ route('jc.detailed.report') }}" class="btn btn-secondary">
                    ← Back to Summary
                </a>
                <button onclick="refreshData()" class="btn btn-primary" id="refreshBtn">
                    🔄 Refresh Data
                </button>
            </div>
        </div>

        <!-- Complete Dynamic Client Columns Table -->
        <div class="detailed-table-container">
            @if(isset($juniorCloserReports) && $juniorCloserReports->count() > 0 && isset($allClients) && $allClients->count() > 0)
                <div class="table-wrapper" id="tableWrapper">
                    <!-- Scroll Hint -->
                    <div class="scroll-hint" id="scrollHint">
                        ← Scroll to see complete breakdown for all {{ $allClients->count() }} clients and {{ count($activeCarriers) }} carriers →
                    </div>
                    
                    <table class="detailed-table" id="dynamicTable">
                        <thead>
                            <!-- Main Header Row -->
                            <tr>
                                <th rowspan="2" class="metric-cell">👨‍💼 Junior Closer Name</th>
                                <th rowspan="2" class="metric-cell">🏢 Center</th>
                                <th rowspan="2" class="metric-cell metric-submissions">📝 Total<br>Sub</th>
                                <th rowspan="2" class="metric-cell metric-approved">✅ Total<br>App</th>
                                <th rowspan="2" class="metric-cell metric-pending">⏳ Total<br>Pen</th>
                                <th rowspan="2" class="metric-cell metric-rejected">❌ Total<br>Rej</th>
                                <th rowspan="2" class="metric-cell metric-level">📊 Total<br>Level</th>
                                <th rowspan="2" class="metric-cell metric-gi">🎯 Total<br>GI</th>
                                <th rowspan="2" class="percentage-cell">📈 App<br>Rate %</th>
                                <th rowspan="2" class="percentage-cell">⏳ Pen<br>Rate %</th>
                                <th rowspan="2" class="percentage-cell">❌ Rej<br>Rate %</th>
                                <th rowspan="2" class="premium-cell">💰 Yearly<br>Premium</th>
                                <th rowspan="2" class="premium-cell">💵 Avg<br>Premium</th>
                                <th rowspan="2" class="percentage-cell">🎯 GI<br>%</th>
                                <th rowspan="2" class="percentage-cell">📊 Level<br>%</th>
                                
                                <!-- Dynamic Client Headers -->
                                @foreach($allClients as $client)
                                    <th colspan="{{ 8 + count($activeCarriers) }}" class="client-header">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                            <span style="font-size: 1rem;">🏢</span>
                                            <strong>{{ $client->name }}</strong>
                                            <span style="font-size: 0.7rem; opacity: 0.8;">(Complete + Carriers)</span>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                            
                            <!-- Sub Header Row -->
                            <tr>
                                @foreach($allClients as $client)
                                    <th class="client-subheader" title="Submissions">📝 Submitted</th>
                                    <th class="client-subheader" title="Approved">✅ Approved</th>
                                    <th class="client-subheader" title="Pending">⏳ Pending</th>
                                    <th class="client-subheader" title="Rejected">❌ Rejected</th>
                                    <th class="client-subheader" title="Level Approved">📊 Level</th>
                                    <th class="client-subheader" title="GI Approved">🎯 GI</th>
                                    <th class="client-subheader" title="Approval Percentage">📈 Approved %</th>
                                    <th class="client-subheader" title="Pending Percentage">⏳ Pending %</th>
                                    @foreach($activeCarriers as $carrier)
                                        <th class="carrier-subheader" title="{{ $carrier }}">{!! str_replace(['(', ')'], ['<br>(', ')'], $carrier) !!}</th>
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @foreach($juniorCloserReports as $index => $report)
                                <tr class="data-row" data-junior-closer="{{ strtolower($report->junior_closer_name) }}" style="animation-delay: {{ $index * 0.05 }}s;">
                                    <!-- Fixed Junior Closer Info Columns -->
                                    <td class="junior-closer-name-cell">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <div style="width: 8px; height: 8px; border-radius: 50%; background: linear-gradient(135deg, #7c3aed, #a855f7);"></div>
                                            <div>
                                                <div style="font-weight: 700;">{{ $report->junior_closer_name }}</div>
                                                @if($report->junior_closer_email)
                                                    <div class="junior-closer-email">{{ $report->junior_closer_email }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="center-cell">
                                        <span style="padding: 4px 8px; background: linear-gradient(135deg, #f1f5f9, #e2e8f0); border-radius: 12px; font-weight: 600;">
                                            {{ $report->center_name }}
                                        </span>
                                    </td>
                                    
                                    <!-- Status Metrics -->
                                    <td class="metric-cell metric-submissions">{{ number_format($report->total_submissions) }}</td>
                                    <td class="metric-cell metric-approved">{{ number_format($report->approved_count) }}</td>
                                    <td class="metric-cell metric-pending">{{ number_format($report->pending_count) }}</td>
                                    <td class="metric-cell metric-rejected">{{ number_format($report->rejected_count) }}</td>
                                    <td class="metric-cell metric-level">{{ number_format($report->level_count) }}</td>
                                    <td class="metric-cell metric-gi">{{ number_format($report->gi_count) }}</td>
                                    
                                    <!-- Status Rate Percentages -->
                                    <td class="percentage-cell 
                                        @if($report->conversion_rate >= 70) percentage-high
                                        @elseif($report->conversion_rate >= 40) percentage-medium
                                        @else percentage-low
                                        @endif">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                            @if($report->conversion_rate >= 70)
                                                <span style="color: #059669;">🔥</span>
                                            @elseif($report->conversion_rate >= 40)
                                                <span style="color: #d97706;">⚡</span>
                                            @else
                                                <span style="color: #dc2626;">📈</span>
                                            @endif
                                            {{ $report->conversion_rate }}%
                                        </div>
                                    </td>
                                    
                                    <td class="percentage-cell 
                                        @if($report->pending_percent <= 10) percentage-high
                                        @elseif($report->pending_percent <= 25) percentage-medium
                                        @else percentage-low
                                        @endif">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                            @if($report->pending_percent <= 10)
                                                <span style="color: #059669;">⚡</span>
                                            @elseif($report->pending_percent <= 25)
                                                <span style="color: #d97706;">⏳</span>
                                            @else
                                                <span style="color: #dc2626;">⚠️</span>
                                            @endif
                                            {{ $report->pending_percent }}%
                                        </div>
                                    </td>
                                    
                                    <td class="percentage-cell 
                                        @if($report->rejected_percent <= 10) percentage-high
                                        @elseif($report->rejected_percent <= 25) percentage-medium
                                        @else percentage-low
                                        @endif">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                            @if($report->rejected_percent <= 10)
                                                <span style="color: #059669;">✅</span>
                                            @elseif($report->rejected_percent <= 25)
                                                <span style="color: #d97706;">⚠️</span>
                                            @else
                                                <span style="color: #dc2626;">❌</span>
                                            @endif
                                            {{ $report->rejected_percent }}%
                                        </div>
                                    </td>
                                    
                                    <td class="premium-cell">
                                        <div style="font-weight: 800;">${{ number_format($report->yearly_premium, 0) }}</div>
                                    </td>
                                    <td class="premium-cell">
                                        <div style="font-weight: 800;">${{ number_format($report->avg_premium ?: 0, 0) }}</div>
                                    </td>
                                    <td class="percentage-cell 
                                        @if($report->gi_percent >= 60) percentage-high
                                        @elseif($report->gi_percent >= 30) percentage-medium
                                        @else percentage-low
                                        @endif">
                                        {{ $report->gi_percent }}%
                                    </td>
                                    <td class="percentage-cell 
                                        @if($report->level_percent >= 40) percentage-high
                                        @elseif($report->level_percent >= 20) percentage-medium
                                        @else percentage-low
                                        @endif">
                                        {{ $report->level_percent }}%
                                    </td>
                                    
                                    <!-- Dynamic Client Data Columns -->
                                    @foreach($allClients as $client)
                                        @php
                                            $clientData = isset($report->client_data[$client->id]) ? $report->client_data[$client->id] : null;
                                            $submissions = $clientData ? $clientData->submissions : 0;
                                            $approved = $clientData ? $clientData->approved : 0;
                                            $pending = $clientData ? $clientData->pending : 0;
                                            $rejected = $clientData ? $clientData->rejected : 0;
                                            $levelApproved = $clientData ? $clientData->level_approved : 0;
                                            $giApproved = $clientData ? $clientData->gi_approved : 0;
                                            $approvedPercent = $clientData ? $clientData->approved_percent : 0;
                                            $pendingPercent = $clientData ? $clientData->pending_percent : 0;
                                            $carrierBreakdown = $clientData ? $clientData->carrier_breakdown : [];
                                        @endphp
                                        
                                        <!-- Status Columns -->
                                        <td class="client-data-cell client-submissions" 
                                            title="{{ $client->name }}: {{ $submissions }} submissions"
                                            data-value="{{ $submissions }}">
                                            {{ $submissions ?: '0' }}
                                        </td>
                                        <td class="client-data-cell client-approved" 
                                            title="{{ $client->name }}: {{ $approved }} approved"
                                            data-value="{{ $approved }}">
                                            {{ $approved ?: '0' }}
                                        </td>
                                        <td class="client-data-cell client-pending" 
                                            title="{{ $client->name }}: {{ $pending }} pending"
                                            data-value="{{ $pending }}">
                                            {{ $pending ?: '0' }}
                                        </td>
                                        <td class="client-data-cell client-rejected" 
                                            title="{{ $client->name }}: {{ $rejected }} rejected"
                                            data-value="{{ $rejected }}">
                                            {{ $rejected ?: '0' }}
                                        </td>
                                        <td class="client-data-cell client-level" 
                                            title="{{ $client->name }}: {{ $levelApproved }} level approved"
                                            data-value="{{ $levelApproved }}">
                                            {{ $levelApproved ?: '0' }}
                                        </td>
                                        <td class="client-data-cell client-gi" 
                                            title="{{ $client->name }}: {{ $giApproved }} GI approved"
                                            data-value="{{ $giApproved }}">
                                            {{ $giApproved ?: '0' }}
                                        </td>
                                        <td class="client-data-cell client-app-percent" 
                                            title="{{ $client->name }}: {{ $approvedPercent }}% approval rate"
                                            data-value="{{ $approvedPercent }}">
                                            {{ $approvedPercent ?: '0' }}%
                                        </td>
                                        <td class="client-data-cell client-pen-percent" 
                                            title="{{ $client->name }}: {{ $pendingPercent }}% pending rate"
                                            data-value="{{ $pendingPercent }}">
                                            {{ $pendingPercent ?: '0' }}%
                                        </td>
                                        
                                        <!-- Carrier Columns -->
                                        @foreach($activeCarriers as $carrier)
                                            @php
                                                $carrierCount = isset($carrierBreakdown[$carrier]) ? $carrierBreakdown[$carrier] : 0;
                                            @endphp
                                            <td class="client-data-cell client-carrier" 
                                                title="{{ $client->name }}: {{ $carrier }} - {{ $carrierCount }} submissions"
                                                data-value="{{ $carrierCount }}">
                                                {{ $carrierCount ?: '0' }}
                                            </td>
                                        @endforeach
                                    @endforeach
                                </tr>
                            @endforeach
                            
                            <!-- Summary Row -->
                            @php
                                $total = [
                                    'submissions' => $juniorCloserReports->sum('total_submissions'),
                                    'approved' => $juniorCloserReports->sum('approved_count'),
                                    'pending' => $juniorCloserReports->sum('pending_count'),
                                    'rejected' => $juniorCloserReports->sum('rejected_count'),
                                    'level' => $juniorCloserReports->sum('level_count'),
                                    'gi' => $juniorCloserReports->sum('gi_count'),
                                    'yearly_premium' => $juniorCloserReports->sum('yearly_premium'),
                                    'avg_premium' => $juniorCloserReports->where('avg_premium', '>', 0)->avg('avg_premium'),
                                ];
                                $total['conversion_rate'] = $total['submissions'] > 0 ? round(($total['approved'] / $total['submissions']) * 100, 2) : 0;
                                $total['pending_rate'] = $total['submissions'] > 0 ? round(($total['pending'] / $total['submissions']) * 100, 2) : 0;
                                $total['rejected_rate'] = $total['submissions'] > 0 ? round(($total['rejected'] / $total['submissions']) * 100, 2) : 0;
                                $total['gi_percent'] = $total['approved'] > 0 ? round(($total['gi'] / $total['approved']) * 100) : 0;
                                $total['level_percent'] = $total['approved'] > 0 ? round(($total['level'] / $total['approved']) * 100) : 0;
                            @endphp
                            <tr class="summary-row" id="summaryRow">
                                <td class="junior-closer-name-cell">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="font-size: 1.2rem;">📊</span>
                                        <strong>GRAND TOTAL</strong>
                                    </div>
                                </td>
                                <td class="center-cell"><strong>All Centers</strong></td>
                                <td><strong>{{ number_format($total['submissions']) }}</strong></td>
                                <td><strong>{{ number_format($total['approved']) }}</strong></td>
                                <td><strong>{{ number_format($total['pending']) }}</strong></td>
                                <td><strong>{{ number_format($total['rejected']) }}</strong></td>
                                <td><strong>{{ number_format($total['level']) }}</strong></td>
                                <td><strong>{{ number_format($total['gi']) }}</strong></td>
                                <td><strong>{{ $total['conversion_rate'] }}%</strong></td>
                                <td><strong>{{ $total['pending_rate'] }}%</strong></td>
                                <td><strong>{{ $total['rejected_rate'] }}%</strong></td>
                                <td><strong>${{ number_format($total['yearly_premium'], 0) }}</strong></td>
                                <td><strong>${{ number_format($total['avg_premium'] ?: 0, 0) }}</strong></td>
                                <td><strong>{{ $total['gi_percent'] }}%</strong></td>
                                <td><strong>{{ $total['level_percent'] }}%</strong></td>
                                
                                <!-- Client Summary -->
                                @foreach($allClients as $client)
                                    @php
                                        $clientTotalSub = 0;
                                        $clientTotalApp = 0;
                                        $clientTotalPen = 0;
                                        $clientTotalRej = 0;
                                        $clientTotalLevel = 0;
                                        $clientTotalGI = 0;
                                        $clientCarrierTotals = array_fill_keys($activeCarriers, 0);
                                        
                                        foreach($juniorCloserReports as $report) {
                                            if(isset($report->client_data[$client->id])) {
                                                $clientData = $report->client_data[$client->id];
                                                $clientTotalSub += $clientData->submissions;
                                                $clientTotalApp += $clientData->approved;
                                                $clientTotalPen += $clientData->pending;
                                                $clientTotalRej += $clientData->rejected;
                                                $clientTotalLevel += $clientData->level_approved;
                                                $clientTotalGI += $clientData->gi_approved;
                                                foreach($activeCarriers as $carrier) {
                                                    $clientCarrierTotals[$carrier] += isset($clientData->carrier_breakdown[$carrier]) ? $clientData->carrier_breakdown[$carrier] : 0;
                                                }
                                            }
                                        }
                                        
                                        $clientAppPercent = $clientTotalSub > 0 ? round(($clientTotalApp / $clientTotalSub) * 100, 1) : 0;
                                        $clientPenPercent = $clientTotalSub > 0 ? round(($clientTotalPen / $clientTotalSub) * 100, 1) : 0;
                                    @endphp
                                    
                                    <td><strong>{{ $clientTotalSub ?: '0' }}</strong></td>
                                    <td><strong>{{ $clientTotalApp ?: '0' }}</strong></td>
                                    <td><strong>{{ $clientTotalPen ?: '0' }}</strong></td>
                                    <td><strong>{{ $clientTotalRej ?: '0' }}</strong></td>
                                    <td><strong>{{ $clientTotalLevel ?: '0' }}</strong></td>
                                    <td><strong>{{ $clientTotalGI ?: '0' }}</strong></td>
                                    <td><strong>{{ $clientAppPercent ?: '0' }}%</strong></td>
                                    <td><strong>{{ $clientPenPercent ?: '0' }}%</strong></td>
                                    @foreach($activeCarriers as $carrier)
                                        <td><strong>{{ $clientCarrierTotals[$carrier] ?: '0' }}</strong></td>
                                    @endforeach
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📊</div>
                    <h3 style="margin: 0 0 1rem 0; font-size: 1.5rem; font-weight: 700;">No Data Available</h3>
                    <p style="margin: 0 0 2rem 0; font-size: 1rem;">
                        @if(!isset($juniorCloserReports) || $juniorCloserReports->count() === 0)
                            No junior closer performance data found for {{ $period }}.
                        @elseif(!isset($allClients) || $allClients->count() === 0)
                            No clients found in the system.
                        @else
                            Unable to load report data.
                        @endif
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <button onclick="refreshData()" class="btn btn-primary">
                            🔄 Refresh Data
                        </button>
                        <a href="{{ route('jc.detailed.report') }}" class="btn btn-secondary">
                            ← Back to Reports
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Statistics Summary -->
        @if(isset($juniorCloserReports) && $juniorCloserReports->count() > 0 && isset($allClients))
            <div class="stats-grid">
                <div class="stat-card">
                    <h4>👨‍💼 Active Junior Closers</h4>
                    <div class="stat-value">{{ $juniorCloserReports->count() }}</div>
                    <div class="stat-label">Total Junior Closers Tracked</div>
                </div>
                
                <div class="stat-card">
                    <h4>🏢 Active Clients</h4>
                    <div class="stat-value">{{ $allClients->count() }}</div>
                    <div class="stat-label">Clients in System</div>
                </div>
                
                <div class="stat-card">
                    <h4>📊 Complete Breakdown</h4>
                    <div class="stat-value">{{ ($juniorCloserReports->count() ?? 0) * ($allClients->count() ?? 0) * (8 + count($activeCarriers)) }}</div>
                    <div class="stat-label">Total Data Points</div>
                </div>
                
                <div class="stat-card">
                    <h4>✅ Approval Rate</h4>
                    <div class="stat-value">{{ $total['conversion_rate'] ?? 0 }}%</div>
                    <div class="stat-label">Overall Success Rate</div>
                </div>
                
                <div class="stat-card">
                    <h4>⏳ Pending Rate</h4>
                    <div class="stat-value">{{ $total['pending_rate'] ?? 0 }}%</div>
                    <div class="stat-label">Processing Pipeline</div>
                </div>
                
                <div class="stat-card">
                    <h4>❌ Rejection Rate</h4>
                    <div class="stat-value">{{ $total['rejected_rate'] ?? 0 }}%</div>
                    <div class="stat-label">Quality Improvement Area</div>
                </div>
                
                <div class="stat-card">
                    <h4>🏢 Active Carriers</h4>
                    <div class="stat-value">{{ count($activeCarriers) }}</div>
                    <div class="stat-label">Carriers with Submissions</div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Global variables
let refreshTimer;
let isRefreshing = false;

console.log('🚀 Junior Closer Analytics Dashboard Loaded');
console.log('Period:', '{{ $period }}');
console.log('Total Junior Closers:', {{ isset($juniorCloserReports) ? $juniorCloserReports->count() : 0 }});
console.log('Total Clients:', {{ isset($allClients) ? $allClients->count() : 0 }});
console.log('Active Carriers:', {{ count($activeCarriers) }});
console.log('Filter:', '{{ $filter }}');
console.log('📊 Columns per client: 8 status + {{ count($activeCarriers) }} carriers');

// Page initialization
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    setupSearchFunctionality();
    setupAutoRefresh();
    setupTableInteractions();
    animateTableEntrance();
});

// Initialize dashboard components
function initializeDashboard() {
    console.log('🎯 Initializing Junior Closer Dashboard Components...');
    
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.classList.add('active');
        setTimeout(() => {
            loadingOverlay.classList.remove('active');
        }, 800);
    }
    
    addScrollHint();
    setupResponsiveTable();
}

// Setup search functionality
function setupSearchFunctionality() {
    const searchInput = document.getElementById('juniorCloserSearch');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', debounce(function() {
        const searchTerm = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.data-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const juniorCloserName = row.dataset.juniorCloser || '';
            const isVisible = juniorCloserName.includes(searchTerm);
            
            if (isVisible) {
                row.style.display = '';
                row.style.animation = 'fadeIn 0.3s ease';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        const summaryRow = document.getElementById('summaryRow');
        if (summaryRow) {
            summaryRow.style.display = visibleCount > 0 ? '' : 'none';
        }
        
        console.log(`🔍 Search: "${searchTerm}" - ${visibleCount} results`);
    }, 300));
    
    searchInput.addEventListener('focus', function() {
        this.style.borderColor = '#a855f7';
        this.style.boxShadow = '0 0 0 3px rgba(168, 85, 247, 0.1)';
        this.style.background = '#faf5ff';
    });
    
    searchInput.addEventListener('blur', function() {
        this.style.borderColor = '#e2e8f0';
        this.style.boxShadow = 'none';
        this.style.background = 'white';
    });
}

// Setup auto refresh
function setupAutoRefresh() {
    refreshTimer = setInterval(() => {
        console.log('🔄 Auto-refreshing junior closer dashboard...');
        refreshData();
    }, 600000);
    
    window.addEventListener('beforeunload', () => {
        if (refreshTimer) {
            clearInterval(refreshTimer);
        }
    });
}

// Refresh data
function refreshData() {
    if (isRefreshing) return;
    
    isRefreshing = true;
    const refreshBtn = document.getElementById('refreshBtn');
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    if (refreshBtn) {
        refreshBtn.innerHTML = '⏳ Refreshing...';
        refreshBtn.disabled = true;
    }
    
    if (loadingOverlay) {
        loadingOverlay.classList.add('active');
    }
    
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Table interactions
function setupTableInteractions() {
    const tableWrapper = document.getElementById('tableWrapper');
    if (!tableWrapper) return;
    
    const clientCells = document.querySelectorAll('.client-data-cell');
    clientCells.forEach(cell => {
        cell.addEventListener('mouseenter', function() {
            const value = this.dataset.value;
            const isZero = value === '0';
            const cellType = this.className.includes('pending') ? 'pending' : 
                           this.className.includes('rejected') ? 'rejected' : 
                           this.className.includes('approved') ? 'approved' : 
                           this.className.includes('carrier') ? 'carrier' : 'default';
            
            if (isZero) {
                this.style.transform = 'scale(1.05)';
                this.style.background = 'linear-gradient(135deg, #f3f4f6, #e5e7eb)';
                this.style.color = '#6b7280';
                this.style.fontStyle = 'italic';
            } else {
                this.style.transform = 'scale(1.1)';
                this.style.zIndex = '10';
                
                if (cellType === 'pending') {
                    this.style.boxShadow = '0 4px 12px rgba(217, 119, 6, 0.3)';
                } else if (cellType === 'rejected') {
                    this.style.boxShadow = '0 4px 12px rgba(220, 38, 38, 0.3)';
                } else if (cellType === 'carrier') {
                    this.style.boxShadow = '0 4px 12px rgba(30, 64, 175, 0.3)';
                } else {
                    this.style.boxShadow = '0 4px 12px rgba(124, 58, 237, 0.3)';
                }
            }
            
            const cellIndex = Array.from(this.parentNode.children).indexOf(this);
            const table = document.getElementById('dynamicTable');
            if (table) {
                const columnCells = table.querySelectorAll(`tr td:nth-child(${cellIndex + 1})`);
                columnCells.forEach(colCell => {
                    if (colCell !== this && colCell.textContent.trim()) {
                        colCell.style.background = 'rgba(124, 58, 237, 0.05)';
                    }
                });
            }
        });
        
        cell.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.zIndex = '';
            this.style.boxShadow = '';
            this.style.background = '';
            this.style.color = '';
            this.style.fontStyle = '';
            
            const cellIndex = Array.from(this.parentNode.children).indexOf(this);
            const table = document.getElementById('dynamicTable');
            if (table) {
                const columnCells = table.querySelectorAll(`tr td:nth-child(${cellIndex + 1})`);
                columnCells.forEach(colCell => {
                    if (colCell !== this) {
                        colCell.style.background = '';
                    }
                });
            }
        });
    });
    
    let scrollTimeout;
    tableWrapper.addEventListener('scroll', function() {
        const scrollHint = document.getElementById('scrollHint');
        if (scrollHint && scrollHint.parentNode) {
            scrollHint.remove();
        }
        
        const table = document.getElementById('dynamicTable');
        if (table) {
            const scrollLeft = this.scrollLeft;
            if (scrollLeft > 0) {
                table.style.boxShadow = 'inset 10px 0 10px -10px rgba(0,0,0,0.1)';
            } else {
                table.style.boxShadow = '';
            }
        }
        
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        
        scrollTimeout = setTimeout(() => {
            console.log(`📊 Table scrolled to: ${this.scrollLeft}px`);
        }, 500);
    });
}

// Animate table entrance
function animateTableEntrance() {
    const rows = document.querySelectorAll('.data-row');
    const summaryRow = document.getElementById('summaryRow');
    
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            row.style.transition = 'all 0.5s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 50);
    });
    
    if (summaryRow) {
        summaryRow.style.opacity = '0';
        summaryRow.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            summaryRow.style.transition = 'all 0.6s ease';
            summaryRow.style.opacity = '1';
            summaryRow.style.transform = 'scale(1)';
        }, rows.length * 50 + 300);
    }
    
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, rows.length * 50 + 500 + (index * 100));
    });
}

// Add scroll hint
function addScrollHint() {
    const tableWrapper = document.getElementById('tableWrapper');
    if (!tableWrapper) return;
    
    const scrollHint = document.createElement('div');
    scrollHint.id = 'scrollHint';
    scrollHint.className = 'scroll-hint';
    scrollHint.innerHTML = '← Scroll to see complete breakdown for all {{ isset($allClients) ? $allClients->count() : 0 }} clients and {{ count($activeCarriers) }} carriers →';
    
    tableWrapper.appendChild(scrollHint);
    
    setTimeout(() => {
        if (scrollHint.parentNode) {
            scrollHint.remove();
        }
    }, 6000);
}

// Setup responsive table
function setupResponsiveTable() {
    const table = document.getElementById('dynamicTable');
    if (!table) return;
    
    const clientCount = {{ isset($allClients) ? $allClients->count() : 0 }};
    const carrierCount = {{ count($activeCarriers) }};
    const baseWidth = 600;
    const clientColumnWidth = 40 * (8 + carrierCount);
    const totalWidth = baseWidth + (clientCount * clientColumnWidth);
    
    table.style.minWidth = totalWidth + 'px';
    
    console.log(`📏 Junior Closer Table configured: ${clientCount} clients, ${8 + carrierCount} cols each, ${totalWidth}px total width`);
}

// Debounce utility
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        e.preventDefault();
        refreshData();
    }
    
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        const searchInput = document.getElementById('juniorCloserSearch');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
    
    if (e.key === 'Escape') {
        const searchInput = document.getElementById('juniorCloserSearch');
        if (searchInput && searchInput.value) {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
        }
    }
});

// Apply date filter
function applyDateFilter() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
        alert('Start date cannot be later than end date');
        return;
    }
    
    console.log(`📅 Applying date filter: ${startDate} to ${endDate}`);
    
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.classList.add('active');
    }
    
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('start_date', startDate);
    currentUrl.searchParams.set('end_date', endDate);
    currentUrl.searchParams.set('filter', 'custom');
    
    window.location.href = currentUrl.toString();
}

// Export to Excel
function exportToExcel() {
    const exportBtn = document.getElementById('exportBtn');
    if (!exportBtn) return;
    
    exportBtn.innerHTML = '⏳ Exporting Complete Data...';
    exportBtn.disabled = true;
    
    console.log('📊 Starting Excel export with junior closer status and carrier columns...');
    
    try {
        const table = document.getElementById('dynamicTable');
        if (!table) {
            throw new Error('Table not found');
        }
        
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.table_to_sheet(table);
        
        const range = XLSX.utils.decode_range(ws['!ref']);
        const colWidths = [];
        for (let c = 0; c <= range.e.c; c++) {
            if (c === 0) colWidths.push({ width: 25 }); // Junior closer name
            else if (c === 1) colWidths.push({ width: 15 }); // Center
            else if (c < 15) colWidths.push({ width: 12 }); // Fixed columns
            else colWidths.push({ width: 10 }); // Client and carrier columns
        }
        ws['!cols'] = colWidths;
        
        XLSX.utils.book_append_sheet(wb, ws, 'Junior Closer Performance');
        
        const now = new Date();
        const dateStr = now.toISOString().split('T')[0];
        const filename = `junior_closer_performance_with_carriers_{{ $filter }}_${dateStr}.xlsx`;
        
        XLSX.writeFile(wb, filename);
        
        console.log(`✅ Excel file exported: ${filename}`);
        showNotification('✅ Junior closer Excel file with status and carrier columns downloaded!', 'success');
        
    } catch (error) {
        console.error('❌ Export error:', error);
        showNotification('❌ Export failed. Please try again.', 'error');
    } finally {
        setTimeout(() => {
            exportBtn.innerHTML = '📊 Export Complete Data';
            exportBtn.disabled = false;
        }, 1000);
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const existingNotification = document.getElementById('notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    const notification = document.createElement('div');
    notification.id = 'notification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        z-index: 10000;
        box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        animation: slideInFromRight 0.3s ease;
        max-width: 300px;
    `;
    
    if (type === 'success') {
        notification.style.background = 'linear-gradient(135deg, #7c3aed, #a855f7)';
        notification.style.color = 'white';
    } else if (type === 'error') {
        notification.style.background = 'linear-gradient(135deg, #dc2626, #ef4444)';
        notification.style.color = 'white';
    } else {
        notification.style.background = 'linear-gradient(135deg, #3b82f6, #60a5fa)';
        notification.style.color = 'white';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutToRight 0.3s ease';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }, 3000);
}

console.log('✨ Junior Closer Analytics Dashboard Fully Loaded! 🎯');
console.log('📊 Features: Junior closer status tracking, carrier breakdown');
console.log('🎹 Keyboard shortcuts: Ctrl+R (refresh), Ctrl+F (search), Esc (clear search)');
console.log('💾 Data points per client: 8 status metrics + {{ count($activeCarriers) }} carriers');
</script>

@endsection
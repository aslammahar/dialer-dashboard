@extends('layouts.admin')

@section('title', 'Client Reports')

@section('content')
<style>
    /* Client Reports Styles */
    .client-reports-dashboard {
        min-height: 100vh;
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .main-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        overflow-x: visible;
    }

    .header-section {
        text-align: center;
        margin-bottom: 3rem;
    }

    .main-title {
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(135deg, #059669, #10b981, #34d399);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1rem;
    }

    .subtitle {
        font-size: 1.25rem;
        color: #64748b;
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
    }

    .filter-btn.active {
        background: linear-gradient(135deg, #059669, #10b981);
        color: white;
        box-shadow: 0 4px 16px rgba(5, 150, 105, 0.3);
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
        background: linear-gradient(90deg, #059669, #34d399);
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
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #059669;
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
        background: linear-gradient(135deg, #059669, #10b981);
        color: white;
        box-shadow: 0 4px 16px rgba(5, 150, 105, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(5, 150, 105, 0.4);
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

    /* FIXED: Client Reports Table */
    .reports-table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: visible;
        margin-bottom: 3rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }

    .reports-table {
        width: 100%;
        min-width: 1400px;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .reports-table th {
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

    .reports-table td {
        padding: 1rem 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.3s ease;
        vertical-align: middle;
    }

    .reports-table tr:hover {
        background: #f8fafc;
    }

    .reports-table tr:last-child td {
        border-bottom: none;
    }

    /* Column specific styling */
    .reports-table th:nth-child(1) { width: 240px; text-align: left; }
    .reports-table th:nth-child(2) { width: 120px; text-align: center; }
    .reports-table th:nth-child(3) { width: 120px; text-align: center; }
    .reports-table th:nth-child(4) { width: 120px; text-align: center; }
    .reports-table th:nth-child(5) { width: 160px; text-align: center; }
    .reports-table th:nth-child(6) { width: 140px; text-align: center; }
    .reports-table th:nth-child(7) { width: 140px; text-align: center; }
    .reports-table th:nth-child(8) { width: 120px; text-align: center; }
    .reports-table th:nth-child(9) { width: 120px; text-align: center; }
    .reports-table th:nth-child(10) { width: 120px; text-align: center; }

    .client-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 200px;
    }

    .client-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #059669, #34d399);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .client-details h4 {
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 0.25rem 0;
        font-size: 0.9rem;
    }

    .client-details span {
        color: #64748b;
        font-size: 0.75rem;
    }

    .metric-value {
        font-weight: bold;
        font-size: 1.1rem;
        color: #1e293b;
        text-align: center;
    }

    .metric-approved {
        color: #059669;
    }

    .metric-pending {
        color: #dc2626;
    }

    .premium-cell {
        text-align: center;
        padding: 1rem !important;
    }
    
    .premium-value {
        font-size: 1.2rem;
        font-weight: bold;
        color: #0f766e;
        margin-bottom: 2px;
    }
    
    .premium-desc {
        font-size: 0.7rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .conversion-rate {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
    }

    .conversion-badge {
        padding: 3px 8px;
        border-radius: 16px;
        font-size: 0.65rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .conversion-excellent {
        background: #dcfce7;
        color: #16a34a;
    }

    .conversion-good {
        background: #fef3c7;
        color: #d97706;
    }

    .conversion-poor {
        background: #fecaca;
        color: #dc2626;
    }

    .eligibility-cell {
        text-align: center;
        padding: 1rem !important;
    }
    
    .eligibility-breakdown {
        display: flex;
        flex-direction: column;
        gap: 4px;
        align-items: center;
    }
    
    .eligibility-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .level-badge {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1d4ed8;
        border: 1px solid #3b82f6;
    }
    
    .gi-badge {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #d97706;
        border: 1px solid #f59e0b;
    }

    .eligibility-value {
        font-size: 1.2rem;
        font-weight: bold;
        color: #1e293b;
        margin-bottom: 2px;
    }

    .action-cell {
        text-align: center;
    }

    .view-details-btn {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1d4ed8;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .view-details-btn:hover {
        background: linear-gradient(135deg, #bfdbfe, #93c5fd);
        transform: translateY(-1px);
    }

    /* Carrier Table Styles - KEPT AS IS */
    .carrier-table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: visible;
        margin-bottom: 3rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }

    .carrier-table {
        width: 100%;
        min-width: 2000px;
        border-collapse: collapse;
        font-size: 0.8rem;
        table-layout: fixed;
    }

    .carrier-table th {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 0.75rem 0.5rem;
        text-align: center;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .carrier-table td {
        padding: 0.75rem 0.5rem;
        border-bottom: 1px solid #f1f5f9;
        text-align: center;
        transition: background-color 0.3s ease;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .carrier-table tr:hover {
        background: #f8fafc;
    }

    .carrier-table tr:last-child td {
        border-bottom: none;
    }

    /* Fixed column widths for carrier table */
    .carrier-table th:nth-child(1),
    .carrier-table td:nth-child(1) { 
        width: 200px; 
        text-align: left !important;
        position: sticky;
        left: 0;
        background: white;
        z-index: 15;
        border-right: 2px solid #e2e8f0;
    }
    
    .carrier-table th:nth-child(2),
    .carrier-table td:nth-child(2) { 
        width: 100px;
        position: sticky;
        left: 200px;
        background: #f8fafc;
        z-index: 15;
        border-right: 2px solid #e2e8f0;
    }
    
    /* All other carrier columns */
    .carrier-table th:nth-child(n+3),
    .carrier-table td:nth-child(n+3) { 
        width: 80px;
        min-width: 80px;
    }

    .carrier-count {
        font-weight: bold;
        color: #059669;
    }

    .carrier-count.zero {
        color: #9ca3af;
    }

    .carrier-client-info {
        text-align: left;
        padding: 1rem !important;
    }

    /* Scrollbar Styles */
    .reports-table-wrapper::-webkit-scrollbar,
    .carrier-table-wrapper::-webkit-scrollbar {
        height: 8px;
    }

    .reports-table-wrapper::-webkit-scrollbar-track,
    .carrier-table-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .reports-table-wrapper::-webkit-scrollbar-thumb,
    .carrier-table-wrapper::-webkit-scrollbar-thumb {
        background: #059669;
        border-radius: 4px;
    }

    .reports-table-wrapper::-webkit-scrollbar-thumb:hover,
    .carrier-table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #047857;
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
        background: white;
        margin: 5% auto;
        padding: 0;
        border-radius: 16px;
        width: 90%;
        max-width: 900px;
        max-height: 80vh;
        overflow: hidden;
        box-shadow: 0 24px 48px rgba(0,0,0,0.2);
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(-50px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .modal-header {
        padding: 2rem;
        background: linear-gradient(135deg, #059669, #10b981);
        color: white;
    }

    .modal-header h3 {
        margin: 0 0 0.5rem 0;
        font-size: 1.5rem;
        font-weight: bold;
    }

    .modal-header p {
        margin: 0;
        opacity: 0.9;
    }

    .modal-body {
        padding: 2rem;
        max-height: 50vh;
        overflow-y: auto;
    }

    .close {
        position: absolute;
        right: 1.5rem;
        top: 1.5rem;
        color: white;
        font-size: 2rem;
        font-weight: bold;
        cursor: pointer;
        transition: opacity 0.3s ease;
    }

    .close:hover {
        opacity: 0.7;
    }

    /* Comment Modal Styles */
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
        max-width: 500px;
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
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
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
        max-height: 40vh;
        overflow-y: auto;
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

    .no-comment {
        text-align: center;
        color: #9ca3af;
        font-style: italic;
        padding: 2rem;
    }

    .comment-eye-icon {
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

    .comment-eye-icon:hover {
        background: #dbeafe;
        transform: scale(1.1);
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

    /* Loading States */
    .loading-container {
        text-align: center;
        padding: 3rem;
    }

    .loading-spinner {
        width: 32px;
        height: 32px;
        border: 3px solid #e2e8f0;
        border-top: 3px solid #059669;
        border-radius: 50%;
        margin: 0 auto 1rem;
        animation: spin 1s linear infinite;
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
        }

        .modal-content {
            width: 95%;
            margin: 10% auto;
        }
        
        .reports-table {
            min-width: 1400px;
        }
        
        .carrier-table {
            min-width: 2000px;
        }
    }

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

    /* Active link glow effects */
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

<div class="client-reports-dashboard">
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
            <h1 class="main-title">📊 Client Reports Dashboard</h1>
            <p class="subtitle">Track client submissions, approvals, customer eligibility, and premium performance by day, week, and month<br>
            <small style="color: #e11d48; font-weight: 600;">Premium data and eligibility percentages based on APPROVED records only</small></p>
            
            <!-- Filter Buttons -->
<div class="filter-container">
    <a href="{{ route('client.reports', ['filter' => 'daily']) }}" 
       class="filter-btn {{ $filter === 'daily' ? 'active' : '' }}">
        📅 Daily
    </a>
    <a href="{{ route('client.reports', ['filter' => 'weekly']) }}" 
       class="filter-btn {{ $filter === 'weekly' ? 'active' : '' }}">
        📊 Weekly
    </a>
    <a href="{{ route('client.reports', ['filter' => 'monthly']) }}" 
       class="filter-btn {{ $filter === 'monthly' ? 'active' : '' }}">
        📈 Monthly
    </a>
    
    <!-- Month Picker -->
    <form method="GET" action="{{ route('client.reports') }}" style="display:inline-block;">
        <div class="date-picker-wrapper" style="display: inline-block; background: #ffffff; border-radius: 8px; padding: 10px 20px; border: 1px solid #e2e8f0; transition: all 0.2s ease;">
            <input type="month" 
                   name="month_year" 
                   value="{{ request('month_year') }}" 
                   class="filter-btn" 
                   onchange="this.form.submit()"
                   placeholder="Pick Month"
                   style="border: none; background: transparent; font-family: inherit; font-size: inherit; font-weight: inherit; color: inherit; cursor: pointer; padding: 0;">
        </div>
    </form>

    <!-- ✅ NEW: Date Picker for Specific Day -->
    <form method="GET" action="{{ route('client.reports') }}" style="display:inline-block;">
        <div class="date-picker-wrapper" style="display: inline-block; background: #ffffff; border-radius: 8px; padding: 10px 20px; border: 1px solid #e2e8f0; transition: all 0.2s ease;">
            <input type="date" 
                   name="specific_date" 
                   value="{{ request('specific_date') }}" 
                   class="filter-btn" 
                   onchange="this.form.submit()"
                   placeholder="Pick Date"
                   style="border: none; background: transparent; font-family: inherit; font-size: inherit; font-weight: inherit; color: inherit; cursor: pointer; padding: 0;">
        </div>
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
                    <div class="summary-card-title">Approved</div>
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

            <!-- UPDATED: Average Premium Summary Card -->
            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">Average Premium</div>
                    <div class="summary-card-icon">💰</div>
                </div>
                <div class="summary-card-value">${{ number_format($summary['average_premium'], 2) }}</div>
                <div class="summary-card-desc">Monthly (approved only)</div>
            </div>

            <!-- NEW: Yearly Premium Estimate Summary Card -->
            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">Yearly Estimate</div>
                    <div class="summary-card-icon">📈</div>
                </div>
                <div class="summary-card-value">${{ number_format($summary['yearly_premium_estimate'], 0) }}</div>
                <div class="summary-card-desc">Annual projection</div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-title">Active Clients</div>
                    <div class="summary-card-icon">👥</div>
                </div>
                <div class="summary-card-value">{{ number_format($summary['total_clients']) }}</div>
                <div class="summary-card-desc">Top: {{ $summary['top_client_name'] }}</div>
            </div>
        </div>

       <!-- Action Bar -->
<div class="action-bar">
    <h2>Client Performance Report - {{ $period }}</h2>
    <div class="action-buttons">
        <a href="{{ route('client.reports.export', array_filter(['filter' => $filter, 'specific_date' => request('specific_date'), 'month_year' => request('month_year')])) }}" 
           class="btn btn-primary">
            📥 Export CSV
        </a>
        <button onclick="window.location.reload()" class="btn btn-secondary">
            🔄 Refresh
        </button>
    </div>
</div>

        <!-- FIXED: CLIENT REPORTS TABLE -->
        <div class="reports-table-wrapper">
            @if($clientReports->count() > 0)
                <table class="reports-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Submissions</th>
                            <th>Approved</th>
                            <th>Pending</th>
                            <th>Conversion Rate</th>
                            <th>Avg Premium</th>
                            <th>Yearly Est.</th>
                            <th>Level %</th>
                            <th>GI %</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientReports as $report)
                            <tr>
                                <!-- Client Info -->
                                <td>
                                    <div class="client-info">
                                        <div class="client-avatar">
                                            {{ strtoupper(substr($report->client_name, 0, 1)) }}
                                        </div>
                                        <div class="client-details">
                                            <h4>{{ $report->client_name }}</h4>
                                            <span>ID: {{ $report->clients_id }}</span>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Submissions -->
                                <td>
                                    <div class="metric-value">{{ number_format($report->total_submissions) }}</div>
                                </td>
                                
                                <!-- Approved -->
                                <td>
                                    <div class="metric-value metric-approved">{{ number_format($report->approved_count) }}</div>
                                </td>
                                
                                <!-- Pending -->
                                <td>
                                    <div class="metric-value metric-pending">{{ number_format($report->pending_count) }}</div>
                                </td>
                                
                                <!-- Conversion Rate -->
                                <td>
                                    <div class="conversion-rate">
                                        <span class="metric-value">{{ $report->conversion_rate }}%</span>
                                        <span class="conversion-badge 
                                            @if($report->conversion_rate >= 70) conversion-excellent
                                            @elseif($report->conversion_rate >= 40) conversion-good
                                            @else conversion-poor
                                            @endif">
                                            @if($report->conversion_rate >= 70) Excellent
                                            @elseif($report->conversion_rate >= 40) Good
                                            @else Poor
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                
                                <!-- Average Premium Column -->
                                <td class="premium-cell">
                                    <div class="premium-value">${{ number_format($report->avg_premium, 2) }}</div>
                                    <div class="premium-desc">Monthly</div>
                                </td>
                                
                                <!-- Yearly Premium Estimate Column -->
                                <td class="premium-cell">
                                    <div class="premium-value">${{ number_format($report->yearly_premium_estimate, 0) }}</div>
                                    <div class="premium-desc">Annual</div>
                                </td>
                                
                                <!-- Level Percentage Column -->
                                <td class="eligibility-cell">
                                    <div class="eligibility-breakdown">
                                        <div class="eligibility-value">{{ $report->level_percent ?? 0 }}%</div>
                                        @if(($report->level_percent ?? 0) > 0)
                                            <span class="eligibility-badge level-badge">Level</span>
                                        @else
                                            <span style="color: #9ca3af; font-size: 0.6rem;">None</span>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- GI Percentage Column -->
                                <td class="eligibility-cell">
                                    <div class="eligibility-breakdown">
                                        <div class="eligibility-value">{{ $report->gi_percent ?? 0 }}%</div>
                                        @if(($report->gi_percent ?? 0) > 0)
                                            <span class="eligibility-badge gi-badge">GI</span>
                                        @else
                                            <span style="color: #9ca3af; font-size: 0.6rem;">None</span>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Actions -->
                                <td class="action-cell">
                                    <button class="view-details-btn" onclick="viewClientDetails({{ $report->clients_id }}, '{{ addslashes($report->client_name) }}')">
                                        👁️ Details
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📊</div>
                    <h3>No Client Data Found</h3>
                    <p>No client submissions found for {{ $period }}. Try selecting a different time period.</p>
                    <div class="action-buttons" style="justify-content: center;">
                        <a href="{{ route('client.reports', ['filter' => 'monthly']) }}" class="btn btn-primary">
                            View Monthly Report
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Action Bar for Carrier Section -->
        <div class="action-bar">
            <h2>🏢 Client Carrier Performance - {{ $period }}</h2>
            <div class="action-buttons">
                <span style="color: #64748b; font-size: 0.875rem;">Showing approved records only</span>
            </div>
        </div>

        <!-- CARRIER COUNTS TABLE -->
        <div class="carrier-table-wrapper">
            @if(count($carrierCounts) > 0)
                <table class="carrier-table">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Client</th>
                            <th>Total Approved</th>
                            @php
                                $carriers = [
                                    'Aetna', 'Aetna(CVS)', 'AFLAC', 'AIG', 'AmAm', 'Americo', 'Assurant', 
                                    'C5', 'CVS', 'Foresters', 'Globe Life', 'GW', 'GTL (Guarantee Trust Life)', 
                                    'Liberty Banker Life (LBL)', 'Lumico', 'Mutual of Omaha', 'Prosperity', 
                                    'RNA', 'Security National Life (SNL)', 'Sentinel Security Life (SSL)', 
                                    'Sons of Norway', 'Superior Choice (CICA)', 'TransAmerica'
                                ];
                            @endphp
                            @foreach($carriers as $carrier)
                                <th title="{{ $carrier }}">{{ $carrier }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carrierCounts as $clientData)
                            <tr>
                                <td class="carrier-client-info">
                                    <div class="client-info">
                                        <div class="client-avatar">
                                            {{ strtoupper(substr($clientData['client_name'], 0, 1)) }}
                                        </div>
                                        <div class="client-details">
                                            <h4>{{ $clientData['client_name'] }}</h4>
                                            <span>ID: {{ $clientData['client_id'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="carrier-count">{{ $clientData['total_approved'] }}</div>
                                </td>
                                @foreach($carriers as $carrier)
                                    <td>
                                        <div class="carrier-count {{ $clientData['carriers'][$carrier] === '-' ? 'zero' : '' }}">
                                            {{ $clientData['carriers'][$carrier] }}
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🏢</div>
                    <h3>No Carrier Data Found</h3>
                    <p>No client carrier data found for {{ $period }}. Try selecting a different time period.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- CLIENT DETAILS MODAL -->
<div id="clientModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h3 id="modalClientName">Client Details</h3>
            <p id="modalClientInfo">Loading client information...</p>
        </div>
        <div class="modal-body">
            <div id="modalContent">
                <div class="loading-container">
                    <div class="loading-spinner"></div>
                    <p>Loading client details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let currentFilter = '{{ $filter }}';
let modalElement = document.getElementById('clientModal');
let closeButton = document.querySelector('.close');

console.log('Client Reports Dashboard Loaded');
console.log('Current Filter:', currentFilter);
console.log('Client Reports Count:', {{ $clientReports->count() }});
console.log('Carrier Counts:', {{ json_encode(count($carrierCounts)) }});

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

// Comment modal functions
function createCommentModal() {
    if (document.getElementById('commentModal')) return;
    
    const commentModalHTML = `
        <div id="commentModal" class="comment-modal">
            <div class="comment-modal-content">
                <div class="comment-modal-header">
                    <button class="comment-close">&times;</button>
                    <h4>Client Comment</h4>
                </div>
                <div class="comment-modal-body">
                    <div id="commentContent"></div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', commentModalHTML);
    
    // Add event listeners after modal is created
    setupCommentModalEvents();
}

function setupCommentModalEvents() {
    const commentModal = document.getElementById('commentModal');
    const commentClose = document.querySelector('#commentModal .comment-close');
    
    if (commentClose) {
        commentClose.onclick = function() {
            commentModal.style.display = 'none';
        }
    }
    
    if (commentModal) {
        commentModal.onclick = function(event) {
            if (event.target === commentModal) {
                commentModal.style.display = 'none';
            }
        }
    }
    
    // Add escape key listener
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && commentModal && commentModal.style.display === 'block') {
            commentModal.style.display = 'none';
        }
    });
}

function showCommentModal(comment) {
    console.log('showCommentModal called with:', comment);
    createCommentModal();
    
    const commentModal = document.getElementById('commentModal');
    const commentContent = document.getElementById('commentContent');
    
    if (!commentModal || !commentContent) {
        console.error('Comment modal elements not found');
        return;
    }
    
    if (!comment || comment.trim() === '' || comment === 'null' || comment === null) {
        commentContent.innerHTML = `
            <div class="no-comment">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">💬</div>
                <p>No comment available</p>
            </div>
        `;
    } else {
        commentContent.innerHTML = `
            <div class="comment-text">${comment}</div>
        `;
    }
    
    commentModal.style.display = 'block';
    console.log('Comment modal should now be visible');
}

// Modal functionality
function viewClientDetails(clientId, clientName) {
    console.log('Viewing details for client:', clientId, clientName);
    
    // Show modal
    modalElement.style.display = 'block';
    document.getElementById('modalClientName').textContent = clientName;
    document.getElementById('modalClientInfo').textContent = `Loading details for ${clientName}...`;
    
    // Show loading state
    document.getElementById('modalContent').innerHTML = `
        <div class="loading-container">
            <div class="loading-spinner"></div>
            <p>Loading client details...</p>
        </div>
    `;
    
    // Fetch client details
    fetchClientDetails(clientId, clientName);
}

function fetchClientDetails(clientId, clientName) {
    // ✅ Build URL with all parameters
    let url = `{{ route('client.details') }}?client_id=${clientId}&filter=${currentFilter}`;
    
    const specificDate = '{{ request("specific_date") }}';
    const monthYear = '{{ request("month_year") }}';
    
    if (specificDate) {
        url += `&specific_date=${specificDate}`;
    }
    if (monthYear) {
        url += `&month_year=${monthYear}`;
    }
    
    console.log('Fetching client details from:', url);
    
    fetch(url)
        .then(response => {
            console.log('Client details response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Client details data:', data);
            renderClientDetails(data);
        })
        .catch(error => {
            console.error('Error fetching client details:', error);
            document.getElementById('modalContent').innerHTML = `
                <div style="text-align: center; padding: 2rem; color: #dc2626;">
                    <h4>Error Loading Details</h4>
                    <p>${error.message}</p>
                    <button onclick="fetchClientDetails(${clientId}, '${clientName}')" class="btn btn-primary" style="margin-top: 1rem;">
                        🔄 Retry
                    </button>
                </div>
            `;
        });
}



function renderClientDetails(data) {
    const { client, stats, submissions, period } = data;
    
    console.log('Stats received:', stats);
    console.log('Submissions received:', submissions);
    
    document.getElementById('modalClientName').textContent = client.name;
    document.getElementById('modalClientInfo').textContent = 
        `${client.email} • ${period.filter.charAt(0).toUpperCase() + period.filter.slice(1)} Report`;
    
    const APPROVED_STATUSES = ['funded', 'charge Back', 'approved'];
    const PENDING_STATUSES = ['pending', 'underwriting', 'Need to Reach', 'NSF'];
    
    let totalSubmissions = submissions ? submissions.length : 0;
    let approvedCount = 0;
    let pendingCount = 0;
    
    if (submissions && submissions.length > 0) {
        submissions.forEach(submission => {
            if (APPROVED_STATUSES.includes(submission.status)) {
                approvedCount++;
            } else if (PENDING_STATUSES.includes(submission.status)) {
                pendingCount++;
            }
        });
    }
    
    const conversionRate = totalSubmissions > 0 ? Math.round((approvedCount / totalSubmissions) * 100) : 0;
    
    let avgPremium = 0;
    let yearlyEstimate = 0;
    let approvedSubmissions = submissions ? submissions.filter(s => APPROVED_STATUSES.includes(s.status)) : [];
    
    if (approvedSubmissions.length > 0) {
        let totalPremium = approvedSubmissions.reduce((sum, submission) => {
            return sum + (parseFloat(submission.monthly_premium) || 0);
        }, 0);
        avgPremium = totalPremium / approvedSubmissions.length;
        yearlyEstimate = avgPremium * 12;
    }
    
    const numberFormat = (value) => {
        if (value === null || value === undefined) return '0';
        return parseInt(value).toLocaleString();
    };

    let html = `
        <div style="margin-bottom: 2rem;">
            <h4 style="color: #1e293b; margin-bottom: 1rem;">📊 Performance Summary</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                
                <div style="background: linear-gradient(135deg, #f0fdf4, #ecfdf5); padding: 1rem; border-radius: 12px; text-align: center; border: 1px solid #bbf7d0;">
                    <div class="metric-value" style="font-size: 1.5rem; font-weight: bold; color: #16a34a;">${numberFormat(totalSubmissions)}</div>
                    <div style="font-size: 0.875rem; color: #64748b;">Total Submissions</div>
                </div>
                
                <div style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); padding: 1rem; border-radius: 12px; text-align: center; border: 1px solid #86efac;">
                    <div class="metric-value metric-approved" style="font-size: 1.5rem; font-weight: bold; color: #059669;">${numberFormat(approvedCount)}</div>
                    <div style="font-size: 0.875rem; color: #64748b;">Approved</div>
                </div>
                
                <div style="background: linear-gradient(135deg, #fef2f2, #fee2e2); padding: 1rem; border-radius: 12px; text-align: center; border: 1px solid #fca5a5;">
                    <div class="metric-value metric-pending" style="font-size: 1.5rem; font-weight: bold; color: #dc2626;">${numberFormat(pendingCount)}</div>
                    <div style="font-size: 0.875rem; color: #64748b;">Pending</div>
                </div>
                
                <div style="background: linear-gradient(135deg, #fffbeb, #fef3c7); padding: 1rem; border-radius: 12px; text-align: center; border: 1px solid #fde68a;">
                    <div class="metric-value" style="font-size: 1.5rem; font-weight: bold; color: #d97706;">${conversionRate}%</div>
                    <div style="font-size: 0.875rem; color: #64748b;">Conversion</div>
                </div>
            </div>
            
            ${(avgPremium > 0) ? `
            <div style="margin-bottom: 2rem;">
                <h4 style="color: #1e293b; margin-bottom: 1rem;">💰 Premium Performance</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); padding: 1.5rem; border-radius: 16px; text-align: center; border: 2px solid #10b981; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #059669, #10b981);"></div>
                        <div class="premium-value" style="font-size: 2rem; font-weight: bold; color: #0f766e; margin-bottom: 2px;">$${avgPremium.toFixed(2)}</div>
                        <div class="premium-desc" style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Monthly</div>
                    </div>
                    <div style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe); padding: 1.5rem; border-radius: 16px; text-align: center; border: 2px solid #0ea5e9; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #0284c7, #0ea5e9);"></div>
                        <div class="premium-value" style="font-size: 2rem; font-weight: bold; color: #0f766e; margin-bottom: 2px;">$${Math.round(yearlyEstimate)}</div>
                        <div class="premium-desc" style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Annual</div>
                    </div>
                </div>
            </div>
            ` : ''}
        </div>
    `;
    
    if (submissions && submissions.length > 0) {
        html += `
            <div>
                <h4 style="color: #1e293b; margin-bottom: 1rem;">📋 Recent Submissions</h4>
                <div style="max-height: 300px; overflow-y: auto; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="position: sticky; top: 0; background: white; z-index: 10;">
                            <tr style="background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #64748b; border-bottom: 1px solid #e2e8f0; font-weight: 600;">Customer</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #64748b; border-bottom: 1px solid #e2e8f0; font-weight: 600;">Status</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #64748b; border-bottom: 1px solid #e2e8f0; font-weight: 600;">Premium</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #64748b; border-bottom: 1px solid #e2e8f0; font-weight: 600;">Date</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #64748b; border-bottom: 1px solid #e2e8f0; font-weight: 600;">Closer</th>
                                <th style="padding: 0.75rem; text-align: center; font-size: 0.875rem; color: #64748b; border-bottom: 1px solid #e2e8f0; font-weight: 600;">Comment</th>
                            </tr>
                        </thead>
                        <tbody>
        `;
        
        submissions.forEach((submission, index) => {
            const isApproved = APPROVED_STATUSES.includes(submission.status);
            const isPending = PENDING_STATUSES.includes(submission.status);
            
            let statusColor = '#6b7280';
            let statusBg = 'linear-gradient(135deg, #f3f4f6, #e5e7eb)';
            let statusIcon = '⚪';
            
            if (isApproved) {
                statusColor = '#16a34a';
                statusBg = 'linear-gradient(135deg, #dcfce7, #bbf7d0)';
                statusIcon = '✅';
            } else if (isPending) {
                statusColor = '#dc2626';
                statusBg = 'linear-gradient(135deg, #fecaca, #fca5a5)';
                statusIcon = '⏳';
            }
            
            const date = new Date(submission.created_at).toLocaleDateString();
            const closerName = submission.closername || 'Unknown';
            const rowBg = index % 2 === 0 ? '#ffffff' : '#f9fafb';
            const premium = submission.monthly_premium ? `$${parseFloat(submission.monthly_premium).toFixed(2)}` : 'N/A';
            
            const hasComment = submission.clients_comment && submission.clients_comment.trim() !== '' && submission.clients_comment !== 'null';
            
            // Better comment escaping for HTML safety
            let escapedComment = '';
            if (hasComment) {
                escapedComment = submission.clients_comment
                    .replace(/\\/g, '\\\\')
                    .replace(/'/g, "\\'")
                    .replace(/"/g, '\\"')
                    .replace(/\n/g, '\\n')
                    .replace(/\r/g, '\\r');
            }
            
            const commentCell = hasComment 
                ? `<span class="comment-eye-icon" onclick="showCommentModal('${escapedComment}'); return false;" title="View comment" style="cursor: pointer;">👁️</span>`
                : `<span style="color: #9ca3af; font-size: 0.75rem;">-</span>`;
            
            html += `
                <tr style="border-bottom: 1px solid #f1f5f9; background: ${rowBg};">
                    <td style="padding: 0.75rem; font-size: 0.875rem; font-weight: 500;">${submission.customer_full_name || 'N/A'}</td>
                    <td style="padding: 0.75rem;">
                        <span style="background: ${statusBg}; color: ${statusColor}; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; border: 1px solid ${statusColor}33;">
                            ${statusIcon} ${submission.status.charAt(0).toUpperCase() + submission.status.slice(1)}
                        </span>
                    </td>
                    <td style="padding: 0.75rem; font-size: 0.875rem; color: #059669; font-weight: 600;">${premium}</td>
                    <td style="padding: 0.75rem; font-size: 0.875rem; color: #64748b;">${date}</td>
                    <td style="padding: 0.75rem; font-size: 0.875rem; color: #64748b;">${closerName}</td>
                    <td style="padding: 0.75rem; text-align: center;">${commentCell}</td>
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
            <div style="text-align: center; padding: 3rem; color: #64748b; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">📋</div>
                <p style="font-size: 1.1rem; margin: 0;">No submissions found for this period.</p>
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
    console.log('Auto-refreshing client reports...');
    window.location.reload();
}, 300000);

// Add entrance animations
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Animate table rows
    document.querySelectorAll('.reports-table tbody tr').forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            row.style.transition = 'all 0.4s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, 200 + (index * 50));
    });
    
    // Animate carrier table rows
    document.querySelectorAll('.carrier-table tbody tr').forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            row.style.transition = 'all 0.4s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, 400 + (index * 50));
    });
});

console.log('Client Reports Dashboard JavaScript Loaded Successfully!');
</script>
@endsection
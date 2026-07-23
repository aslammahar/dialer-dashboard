@extends('layouts.admin')

@section('title', 'Center Competition Report')

@section('content')
<style>
    /* Competition Dashboard Styles */
    .competition-dashboard {
        min-height: 100vh;
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        font-size: 3.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #1e40af, #7c3aed, #db2777);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1rem;
        text-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
        transform: translateY(-2px);
    }

    .filter-btn:hover:not(.active) {
        background: #f1f5f9;
        color: #334155;
        transform: translateY(-1px);
    }

    /* VS Section */
    .vs-section {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 3rem 0;
        padding: 2rem;
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }

    .center-logo {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .logo-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        color: white;
        margin-bottom: 1rem;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        transition: transform 0.3s ease;
    }

    .logo-circle:hover {
        transform: scale(1.1) rotate(5deg);
    }

    .logo-jsons {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }

    .logo-sellerz {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .vs-divider {
        margin: 0 3rem;
        display: flex;
        align-items: center;
    }

    .vs-line {
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #ef4444);
        border-radius: 2px;
    }

    .vs-text {
        margin: 0 1rem;
        font-size: 2rem;
        font-weight: bold;
        color: #64748b;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 2rem;
        margin: 3rem 0;
    }

    .stats-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 32px 64px rgba(0,0,0,0.15);
    }

    .card-header {
        padding: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        transform: translate(30px, -30px);
    }

    .header-jsons {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }

    .header-sellerz {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .card-title {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .card-subtitle {
        opacity: 0.9;
        font-size: 1rem;
    }

    .header-icon {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-content {
        padding: 2rem;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .metric-box {
        text-align: center;
        padding: 1.5rem;
        border-radius: 16px;
        transition: all 0.3s ease;
    }

    .metric-box:hover {
        transform: translateY(-4px);
    }

    .metric-jsons {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    }

    .metric-sellerz {
        background: linear-gradient(135deg, #fecaca, #fca5a5);
    }

    .metric-approved {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    }

    .metric-value {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .metric-value.blue { color: #1d4ed8; }
    .metric-value.red { color: #dc2626; }
    .metric-value.green { color: #16a34a; }

    .metric-label {
        color: #64748b;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .growth-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .growth-positive {
        background: #dcfce7;
        color: #16a34a;
    }

    .growth-negative {
        background: #fecaca;
        color: #dc2626;
    }

    .conversion-section {
        margin-bottom: 1.5rem;
    }

    .conversion-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        color: #64748b;
    }

    .progress-bar {
        height: 12px;
        background: #e2e8f0;
        border-radius: 6px;
        overflow: hidden;
        position: relative;
    }

    .progress-fill {
        height: 100%;
        border-radius: 6px;
        transition: width 1s ease;
        position: relative;
        overflow: hidden;
    }

    .progress-jsons {
        background: linear-gradient(90deg, #3b82f6, #16a34a);
    }

    .progress-sellerz {
        background: linear-gradient(90deg, #ef4444, #16a34a);
    }

    .progress-fill::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: shimmer 2s infinite;
    }

    /* Customer Eligibility Bar Styles */
    .eligibility-section {
        margin-bottom: 1.5rem;
        margin-top: 1rem;
    }
    
    .eligibility-bar {
        position: relative;
        overflow: visible;
    }
    
    .progress-fill-level {
        height: 100%;
        position: absolute;
        left: 0;
        border-radius: 6px 0 0 6px;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        transition: width 1s ease;
    }
    
    .progress-fill-gi {
        height: 100%;
        position: absolute;
        border-radius: 0 6px 6px 0;
        background: linear-gradient(90deg, #8b5cf6, #7c3aed);
        transition: width 1s ease;
    }
    
    .level-label {
        color: #16a34a;
        font-weight: 600;
    }
    
    .gi-label {
        color: #7c3aed;
        font-weight: 600;
    }
    
    /* If both are 0%, show a placeholder */
    .eligibility-bar:empty::after {
        content: 'No data available';
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        text-align: center;
        font-size: 0.75rem;
        color: #94a3b8;
    }
    
    /* Additional Metrics Styles */
    .additional-metrics {
        margin: 1.5rem 0;
        padding-top: 0.5rem;
        border-top: 1px solid #e2e8f0;
    }
    
    .metric-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
    }
    
    .metric-item {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    
    .metric-item:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .metric-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        color: white;
    }
    
    .metric-content {
        flex: 1;
    }
    
    .metric-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }
    
    .metric-label {
        font-size: 0.875rem;
        color: #64748b;
    }
    
    .metric-sublabel {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.25rem;
    }

    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    .pending-text {
        text-align: center;
        color: #64748b;
        font-size: 0.875rem;
        margin-top: 1rem;
    }

    /* Winner Banner */
    .winner-banner {
        text-align: center;
        margin: 3rem 0;
    }

    .winner-badge {
        display: inline-flex;
        align-items: center;
        padding: 1.5rem 3rem;
        border-radius: 24px;
        font-size: 1.5rem;
        font-weight: bold;
        color: white;
        box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        animation: winner-pulse 2s infinite;
    }

    .winner-jsons {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }

    .winner-sellerz {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .winner-tie {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }

    @keyframes winner-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .winner-icon {
        margin-right: 0.75rem;
        font-size: 2rem;
    }

    /* Chart Container */
    .chart-container {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        margin: 3rem 0;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }

    .chart-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #1e293b;
        margin-bottom: 2rem;
        text-align: center;
    }

    .chart-wrapper {
        height: 400px;
        position: relative;
    }

    /* Top Performers */
    .performers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 2rem;
        margin: 3rem 0;
    }

    .performers-card {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }

    .performers-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: #1e293b;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
    }

    .title-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 0.75rem;
    }

    .dot-jsons { background: #3b82f6; }
    .dot-sellerz { background: #ef4444; }

    .performer-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border-radius: 16px;
        transition: all 0.3s ease;
        background: #f8fafc;
        border: 2px solid transparent;
    }

    .performer-item:hover {
        background: white;
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        border-color: #e2e8f0;
    }

    .performer-left {
        display: flex;
        align-items: center;
        flex: 1;
    }

    .performer-rank {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: bold;
        color: white;
        margin-right: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .rank-jsons { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .rank-sellerz { background: linear-gradient(135deg, #ef4444, #dc2626); }

    .performer-info {
        flex: 1;
    }

    .performer-info h4 {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .performer-info .details {
        color: #64748b;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .performer-info .email {
        color: #9ca3af;
        font-size: 0.75rem;
    }

    .performer-stats {
        text-align: right;
        min-width: 120px;
    }

    .performer-stats .approved {
        font-weight: bold;
        font-size: 1.5rem;
        color: #16a34a;
        margin-bottom: 0.25rem;
    }

    .performer-stats .total {
        color: #64748b;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .performer-stats .pending {
        color: #9ca3af;
        font-size: 0.75rem;
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
        border-top: 3px solid #3b82f6;
        border-radius: 50%;
        margin: 0 auto 1rem;
        animation: spin 1s linear infinite;
    }

    .loading-sellerz .loading-spinner {
        border-top-color: #ef4444;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-text {
        color: #64748b;
        font-size: 0.875rem;
    }

    /* Error States */
    .error-container {
        text-align: center;
        padding: 2rem;
        background: #fef2f2;
        border-radius: 12px;
        border: 1px solid #fecaca;
    }

    .error-text {
        color: #dc2626;
        font-size: 0.875rem;
    }

    /* Debug Info */
    .debug-info {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        font-size: 0.75rem;
        color: #0369a1;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .main-container {
            padding: 1rem;
        }

        .main-title {
            font-size: 2.5rem;
        }

        .vs-section {
            flex-direction: column;
            gap: 1rem;
        }

        .vs-divider {
            margin: 1rem 0;
            flex-direction: column;
        }

        .vs-text {
            margin: 0.5rem 0;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .metrics-grid {
            grid-template-columns: 1fr;
        }

        .performers-grid {
            grid-template-columns: 1fr;
        }

        .performer-item {
            flex-direction: column;
            text-align: center;
        }

        .performer-left {
            flex-direction: column;
            margin-bottom: 1rem;
        }

        .performer-rank {
            margin-right: 0;
            margin-bottom: 1rem;
        }
    }
</style>

<div class="competition-dashboard">
    <!-- Simple Header Navigation -->
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

    <!-- Mobile Menu Toggle (Optional) -->
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

<!-- Mobile Navigation (Hidden by default) -->
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

<script>
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

// Initialize responsive behavior
window.addEventListener('resize', handleResize);
window.addEventListener('load', handleResize);
</script>

<style>
/* Responsive CSS for mobile */
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
    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <h1 class="main-title">Center Competition Dashboard</h1>
            <p class="subtitle">Track performance between JSONS vs SELLERZ centers</p>
            
            <!-- Filter Buttons -->
            <div class="filter-container">
                <a href="{{ route('center.competition', ['filter' => 'daily']) }}" 
                   class="filter-btn {{ $filter === 'daily' ? 'active' : '' }}">
                    Daily
                </a>
                <a href="{{ route('center.competition', ['filter' => 'weekly']) }}" 
                   class="filter-btn {{ $filter === 'weekly' ? 'active' : '' }}">
                    Weekly
                </a>
                <a href="{{ route('center.competition', ['filter' => 'monthly']) }}" 
                   class="filter-btn {{ $filter === 'monthly' ? 'active' : '' }}">
                    Monthly
                </a>
                <form method="GET" action="{{ route('center.competition') }}" style="display:inline-block; margin-left:1rem;">
                <input type="month" name="month_year" value="{{ request('month_year') }}" class="filter-btn" onchange="this.form.submit()">
            </form>
            </div>
        </div>

        <!-- VS Competition Header -->
        <div class="vs-section">
            <div class="center-logo">
                <div class="logo-circle logo-jsons">JS</div>
                <h3 style="font-weight: bold; font-size: 1.125rem; color: #1e293b;">JSONS</h3>
            </div>
            
            <div class="vs-divider">
                <div class="vs-line"></div>
                <span class="vs-text">VS</span>
                <div class="vs-line"></div>
            </div>
            
            <div class="center-logo">
                <div class="logo-circle logo-sellerz">SZ</div>
                <h3 style="font-weight: bold; font-size: 1.125rem; color: #1e293b;">SELLERZ</h3>
            </div>
        </div>

        <!-- Main Stats Cards -->
        <div class="stats-grid">
            <!-- JSONS Card -->
            <div class="stats-card">
                <div class="card-header header-jsons">
                    <div>
                        <h2 class="card-title">JSONS</h2>
                        <p class="card-subtitle">{{ $period }} Performance</p>
                    </div>
                    <div class="header-icon">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                
                <div class="card-content">
                    <div class="metrics-grid">
                        <div class="metric-box metric-jsons">
                            <div class="metric-value blue">{{ number_format($currentData['jsons']['submissions']) }}</div>
                            <div class="metric-label">Total Submissions</div>
                            @if($jsonsGrowth != 0)
                                <div class="growth-badge {{ $jsonsGrowth > 0 ? 'growth-positive' : 'growth-negative' }}">
                                    {{ $jsonsGrowth > 0 ? '+' : '' }}{{ $jsonsGrowth }}%
                                </div>
                            @endif
                        </div>
                        
                        <div class="metric-box metric-approved">
                            <div class="metric-value green">{{ number_format($currentData['jsons']['approved']) }}</div>
                            <div class="metric-label">Approved</div>
                            <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.25rem;">{{ $currentData['jsons']['conversion_rate'] }}% rate</div>
                        </div>
                    </div>
                    
                    <div class="conversion-section">
                        <div class="conversion-header">
                            <span>Conversion Rate</span>
                            <span>{{ $currentData['jsons']['conversion_rate'] }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill progress-jsons" style="width: {{ $currentData['jsons']['conversion_rate'] }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Customer Eligibility Section for JSONS -->
                    <div class="eligibility-section">
                        <div class="conversion-header">
                            <span>Customer Eligibility</span>
                            <span><span class="level-label">{{ $currentData['jsons']['level_percent'] ?? 0 }}% Level</span> | <span class="gi-label">{{ $currentData['jsons']['gi_percent'] ?? 0 }}% GI</span></span>
                        </div>
                        <div class="progress-bar eligibility-bar">
                            <div class="progress-fill-level" style="width: {{ $currentData['jsons']['level_percent'] ?? 0 }}%"></div>
                            <div class="progress-fill-gi" style="width: {{ $currentData['jsons']['gi_percent'] ?? 0 }}%; left: {{ $currentData['jsons']['level_percent'] ?? 0 }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Average Premium and Submissions Per Closer for JSONS -->
                    <div class="additional-metrics">
                        <div class="metric-row">
                            <div class="metric-item">
                                <div class="metric-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-value">${{ number_format($currentData['jsons']['avg_premium'] ?? 0, 2) }}</div>
                                    <div class="metric-label">Avg. Premium</div>
                                </div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-value">{{ $currentData['jsons']['submissions_per_closer'] ?? 0 }}</div>
                                    <div class="metric-label">Submissions/Closer</div>
                                    <div class="metric-sublabel">{{ $currentData['jsons']['active_closers'] ?? 0 }} active closers</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pending-text">
                        {{ number_format($currentData['jsons']['pending']) }} pending submissions
                    </div>
                </div>
            </div>

            <!-- SELLERZ Card -->
            <div class="stats-card">
                <div class="card-header header-sellerz">
                    <div>
                        <h2 class="card-title">SELLERZ</h2>
                        <p class="card-subtitle">{{ $period }} Performance</p>
                    </div>
                    <div class="header-icon">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                
                <div class="card-content">
                    <div class="metrics-grid">
                        <div class="metric-box metric-sellerz">
                            <div class="metric-value red">{{ number_format($currentData['sellerz']['submissions']) }}</div>
                            <div class="metric-label">Total Submissions</div>
                            @if($sellerzGrowth != 0)
                                <div class="growth-badge {{ $sellerzGrowth > 0 ? 'growth-positive' : 'growth-negative' }}">
                                    {{ $sellerzGrowth > 0 ? '+' : '' }}{{ $sellerzGrowth }}%
                                </div>
                            @endif
                        </div>
                        
                        <div class="metric-box metric-approved">
                            <div class="metric-value green">{{ number_format($currentData['sellerz']['approved']) }}</div>
                            <div class="metric-label">Approved</div>
                            <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.25rem;">{{ $currentData['sellerz']['conversion_rate'] }}% rate</div>
                        </div>
                    </div>
                    
                    <div class="conversion-section">
                        <div class="conversion-header">
                            <span>Conversion Rate</span>
                            <span>{{ $currentData['sellerz']['conversion_rate'] }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill progress-sellerz" style="width: {{ $currentData['sellerz']['conversion_rate'] }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Customer Eligibility Section for SELLERZ -->
                    <div class="eligibility-section">
                        <div class="conversion-header">
                            <span>Customer Eligibility</span>
                            <span><span class="level-label">{{ $currentData['sellerz']['level_percent'] ?? 0 }}% Level</span> | <span class="gi-label">{{ $currentData['sellerz']['gi_percent'] ?? 0 }}% GI</span></span>
                        </div>
                        <div class="progress-bar eligibility-bar">
                            <div class="progress-fill-level" style="width: {{ $currentData['sellerz']['level_percent'] ?? 0 }}%"></div>
                            <div class="progress-fill-gi" style="width: {{ $currentData['sellerz']['gi_percent'] ?? 0 }}%; left: {{ $currentData['sellerz']['level_percent'] ?? 0 }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Average Premium and Submissions Per Closer for SELLERZ -->
                    <div class="additional-metrics">
                        <div class="metric-row">
                            <div class="metric-item">
                                <div class="metric-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-value">${{ number_format($currentData['sellerz']['avg_premium'] ?? 0, 2) }}</div>
                                    <div class="metric-label">Avg. Premium</div>
                                </div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-value">{{ $currentData['sellerz']['submissions_per_closer'] ?? 0 }}</div>
                                    <div class="metric-label">Submissions/Closer</div>
                                    <div class="metric-sublabel">{{ $currentData['sellerz']['active_closers'] ?? 0 }} active closers</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pending-text">
                        {{ number_format($currentData['sellerz']['pending']) }} pending submissions
                    </div>
                </div>
            </div>
        </div>

        <!-- Winner Banner -->
        @php
            $jsonsTotal = $currentData['jsons']['approved'];
            $sellerzTotal = $currentData['sellerz']['approved'];
            $winner = $jsonsTotal > $sellerzTotal ? 'jsons' : ($sellerzTotal > $jsonsTotal ? 'sellerz' : 'tie');
        @endphp
        
        <div class="winner-banner">
            @if($winner !== 'tie')
                <div class="winner-badge winner-{{ $winner }}">
                    <span class="winner-icon">🏆</span>
                    {{ strtoupper($winner) }} is leading this {{ strtolower($period) }}!
                </div>
            @else
                <div class="winner-badge winner-tie">
                    <span class="winner-icon">🤝</span>
                    It's a tie this {{ strtolower($period) }}!
                </div>
            @endif
        </div>

        <!-- Trend Chart -->
        <div class="chart-container">
            <h3 class="chart-title">Performance Trend</h3>
            <div class="chart-wrapper">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Top Performers Section -->
        <div class="performers-grid">
            <!-- JSONS Top Performers -->
            <div class="performers-card">
                <h3 class="performers-title">
                    <div class="title-dot dot-jsons"></div>
                    JSONS Top Closers
                </h3>
                <div id="jsons-performers">
                    <div class="loading-container">
                        <div class="loading-spinner"></div>
                        <p class="loading-text">Loading JSONS closers...</p>
                    </div>
                </div>
            </div>

            <!-- SELLERZ Top Performers -->
            <div class="performers-card">
                <h3 class="performers-title">
                    <div class="title-dot dot-sellerz"></div>
                    SELLERZ Top Closers
                </h3>
                <div id="sellerz-performers">
                    <div class="loading-container loading-sellerz">
                        <div class="loading-spinner"></div>
                        <p class="loading-text">Loading SELLERZ closers...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overall Leaderboard -->
        <div style="margin: 3rem 0;">
            <h2 style="text-align: center; font-size: 2rem; font-weight: bold; color: #1e293b; margin-bottom: 2rem;">
                🏆 Overall Top Closers Leaderboard
            </h2>
            
            <div class="performers-grid">
                <!-- Top by Submissions -->
                <div class="performers-card" style="background: linear-gradient(135deg, #f8fafc, #e2e8f0);">
                    <h3 class="performers-title" style="color: #1e40af;">
                        <div style="width: 12px; height: 12px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #1d4ed8); margin-right: 0.75rem;"></div>
                        Top by Submissions
                    </h3>
                    <div id="top-by-submissions">
                        <div class="loading-container">
                            <div class="loading-spinner"></div>
                            <p class="loading-text">Loading submissions data...</p>
                        </div>
                    </div>
                </div>

                <!-- Top by Approved -->
                <div class="performers-card" style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
                    <h3 class="performers-title" style="color: #16a34a;">
                        <div style="width: 12px; height: 12px; border-radius: 50%; background: linear-gradient(135deg, #22c55e, #16a34a); margin-right: 0.75rem;"></div>
                        Top by Approved
                    </h3>
                    <div id="top-by-approved">
                        <div class="loading-container">
                            <div class="loading-spinner"></div>
                            <p class="loading-text">Loading approved data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
// Global variables for debugging
let chartData = @json($chartData);
let currentFilter = '{{ $filter }}';

console.log('=== CENTER COMPETITION DASHBOARD DEBUG ===');
console.log('Chart Data:', chartData);
console.log('Current Filter:', currentFilter);
console.log('Routes:', {
    topPerformers: '{{ route('center.top-performers') }}',
    topClosers: '{{ route('center.top-closers') }}'
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing dashboard...');
    
    // Initialize trend chart
    initializeChart();
    
    // Load data with a small delay to ensure everything is ready
    setTimeout(function() {
        console.log('Starting data load...');
        loadTopPerformers();
        loadTopClosers();
    }, 1000);
    
    // Initialize the customer eligibility bars animation
    initializeEligibilityBars();
});

function initializeEligibilityBars() {
    document.querySelectorAll('.eligibility-bar .progress-fill-level, .eligibility-bar .progress-fill-gi').forEach(el => {
        const width = el.style.width;
        el.style.width = '0%';
        setTimeout(() => {
            el.style.width = width;
        }, 300);
    });
}

function initializeChart() {
    console.log('Initializing chart with data:', chartData);
    
    const ctx = document.getElementById('trendChart');
    if (!ctx) {
        console.error('Chart canvas not found!');
        return;
    }
    
    const context = ctx.getContext('2d');
    
    if (!chartData || chartData.length === 0) {
        document.querySelector('.chart-wrapper').innerHTML = `
            <div style="display: flex; align-items: center; justify-content: center; height: 400px; color: #64748b; flex-direction: column;">
                <p style="font-size: 1.2rem; margin-bottom: 1rem;">No chart data available</p>
                <p style="font-size: 0.9rem;">Filter: ${currentFilter}</p>
            </div>
        `;
        return;
    }

    // Create gradients
    const jsonsGradient = context.createLinearGradient(0, 0, 0, 400);
    jsonsGradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
    jsonsGradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');
    
    const sellerzGradient = context.createLinearGradient(0, 0, 0, 400);
    sellerzGradient.addColorStop(0, 'rgba(239, 68, 68, 0.3)');
    sellerzGradient.addColorStop(1, 'rgba(239, 68, 68, 0.05)');
    
    try {
        new Chart(context, {
            type: 'line',
            data: {
                labels: chartData.map(item => item.date),
                datasets: [
                    {
                        label: 'JSONS Submissions',
                        data: chartData.map(item => parseInt(item.jsons) || 0),
                        borderColor: '#3b82f6',
                        backgroundColor: jsonsGradient,
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 3,
                        pointRadius: 8,
                        pointHoverRadius: 12
                    },
                    {
                        label: 'SELLERZ Submissions',
                        data: chartData.map(item => parseInt(item.sellerz) || 0),
                        borderColor: '#ef4444',
                        backgroundColor: sellerzGradient,
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 3,
                        pointRadius: 8,
                        pointHoverRadius: 12
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 25,
                            font: { size: 16, weight: 'bold' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        cornerRadius: 12,
                        padding: 16
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.08)' },
                        ticks: { color: '#64748b' }
                    },
                    x: {
                        grid: { color: 'rgba(0, 0, 0, 0.08)' },
                        ticks: { color: '#64748b' }
                    }
                }
            }
        });
        console.log('Chart initialized successfully');
    } catch (error) {
        console.error('Chart initialization error:', error);
        document.querySelector('.chart-wrapper').innerHTML = `
            <div style="text-align: center; padding: 2rem; color: #dc2626;">
                <p>Chart initialization failed: ${error.message}</p>
            </div>
        `;
    }
}

function loadTopPerformers() {
    console.log('=== LOADING TOP PERFORMERS ===');
    
    const url = `{{ route('center.top-performers') }}?filter=${currentFilter}`;
    console.log('Fetching from URL:', url);
    
    // Show loading state
    updateLoadingState('jsons-performers', 'Loading JSONS performers...');
    updateLoadingState('sellerz-performers', 'Loading SELLERZ performers...');
    
    fetch(url)
        .then(response => {
            console.log('Top performers response status:', response.status);
            console.log('Top performers response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.text();
        })
        .then(text => {
            console.log('Raw response text:', text.substring(0, 500) + '...');
            try {
                const data = JSON.parse(text);
                console.log('Parsed top performers data:', data);
                
                handlePerformersData(data);
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                throw new Error('Invalid JSON response');
            }
        })
        .catch(error => {
            console.error('Top performers fetch error:', error);
            showError('jsons-performers', 'Failed to load JSONS data: ' + error.message);
            showError('sellerz-performers', 'Failed to load SELLERZ data: ' + error.message);
        });
}

function loadTopClosers() {
    console.log('=== LOADING TOP CLOSERS ===');
    
    const url = `{{ route('center.top-closers') }}?filter=${currentFilter}`;
    console.log('Fetching from URL:', url);
    
    // Show loading state
    updateLoadingState('top-by-submissions', 'Loading submissions leaderboard...');
    updateLoadingState('top-by-approved', 'Loading approved leaderboard...');
    
    fetch(url)
        .then(response => {
            console.log('Top closers response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.text();
        })
        .then(text => {
            console.log('Raw closers response:', text.substring(0, 500) + '...');
            try {
                const data = JSON.parse(text);
                console.log('Parsed top closers data:', data);
                
                handleClosersData(data);
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                throw new Error('Invalid JSON response');
            }
        })
        .catch(error => {
            console.error('Top closers fetch error:', error);
            showError('top-by-submissions', 'Failed to load submissions data: ' + error.message);
            showError('top-by-approved', 'Failed to load approved data: ' + error.message);
        });
}

function handlePerformersData(data) {
    console.log('Handling performers data:', data);
    
    // Handle JSONS data
    if (data.jsons && Array.isArray(data.jsons)) {
        console.log(`Rendering ${data.jsons.length} JSONS performers`);
        renderPerformers('jsons-performers', data.jsons, 'jsons');
    } else {
        console.warn('Invalid JSONS data:', data.jsons);
        showError('jsons-performers', 'No JSONS data available');
    }
    
    // Handle SELLERZ data
    if (data.sellerz && Array.isArray(data.sellerz)) {
        console.log(`Rendering ${data.sellerz.length} SELLERZ performers`);
        renderPerformers('sellerz-performers', data.sellerz, 'sellerz');
    } else {
        console.warn('Invalid SELLERZ data:', data.sellerz);
        showError('sellerz-performers', 'No SELLERZ data available');
    }
}

function handleClosersData(data) {
    console.log('Handling closers data:', data);
    
    // Handle submissions data
    if (data.top_by_submissions && Array.isArray(data.top_by_submissions)) {
        console.log(`Rendering ${data.top_by_submissions.length} top by submissions`);
        renderClosers('top-by-submissions', data.top_by_submissions, 'submissions');
    } else {
        console.warn('Invalid submissions data:', data.top_by_submissions);
        showError('top-by-submissions', 'No submissions data available');
    }
    
    // Handle approved data
    if (data.top_by_approved && Array.isArray(data.top_by_approved)) {
        console.log(`Rendering ${data.top_by_approved.length} top by approved`);
        renderClosers('top-by-approved', data.top_by_approved, 'approved');
    } else {
        console.warn('Invalid approved data:', data.top_by_approved);
        showError('top-by-approved', 'No approved data available');
    }
}

function renderPerformers(containerId, performers, type) {
    console.log(`Rendering performers for ${containerId}:`, performers);
    
    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`Container ${containerId} not found!`);
        return;
    }
    
    if (!performers || performers.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: #64748b;">
                <p>No ${type.toUpperCase()} closers found for this period</p>
                <div class="debug-info">Filter: ${currentFilter}</div>
            </div>
        `;
        return;
    }
    
    let html = '';
    
    performers.forEach((performer, index) => {
        console.log(`Processing performer ${index + 1}:`, performer);
        
        // Get closer name with fallbacks
        let closerName = 'Unknown Closer';
        if (performer.closer && performer.closer.name) {
            closerName = performer.closer.name;
        } else if (performer.closername) {
            closerName = performer.closername;
        }
        
        // Get email
        let email = '';
        if (performer.closer && performer.closer.email) {
            email = performer.closer.email;
        }
        
        // Convert data types
        const approvedCount = parseInt(performer.approved_count) || 0;
        const totalSubmissions = parseInt(performer.total_submissions) || 0;
        const conversionRate = parseFloat(performer.conversion_rate) || 0;
        const pendingCount = totalSubmissions - approvedCount;
        
        const rankColor = type === 'jsons' ? '#3b82f6' : '#ef4444';
        
        html += `
            <div class="performer-item">
                <div class="performer-left">
                    <div class="performer-rank rank-${type}">
                        ${index + 1}
                    </div>
                    <div class="performer-info">
                        <h4>${closerName}</h4>
                        <div class="details">ID: ${performer.closername} | ${conversionRate}% conversion</div>
                        ${email ? `<div class="email">📧 ${email}</div>` : ''}
                    </div>
                </div>
                <div class="performer-stats">
                    <div class="approved">✅ ${approvedCount}</div>
                    <div class="total">📋 ${totalSubmissions} total</div>
                    <div class="pending">${pendingCount} pending</div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    console.log(`Successfully rendered ${performers.length} performers for ${type}`);
    
    // Add animation
    setTimeout(() => {
        container.querySelectorAll('.performer-item').forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            setTimeout(() => {
                item.style.transition = 'all 0.4s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }, 100);
}

function renderClosers(containerId, closers, type) {
    console.log(`Rendering closers for ${containerId}:`, closers);
    
    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`Container ${containerId} not found!`);
        return;
    }
    
    if (!closers || closers.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: #64748b;">
                <p>No closers found for ${type}</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    
    closers.forEach((closer, index) => {
        // Get closer name
        let closerName = 'Unknown Closer';
        if (closer.closer && closer.closer.name) {
            closerName = closer.closer.name;
        } else if (closer.closername) {
            closerName = closer.closername;
        }
        
        // Convert data
        const approvedCount = parseInt(closer.approved_count) || 0;
        const totalSubmissions = parseInt(closer.total_submissions) || 0;
        const conversionRate = parseFloat(closer.conversion_rate) || 0;
        
        const mainValue = type === 'submissions' ? totalSubmissions : approvedCount;
        const secondaryValue = type === 'submissions' ? `${approvedCount} approved` : `${totalSubmissions} total`;
        
        // Rank styling
        const rankClass = index === 0 ? 'gold' : (index === 1 ? 'silver' : (index === 2 ? 'bronze' : 'regular'));
        const crown = index === 0 ? '👑' : '';
        
        html += `
            <div class="performer-item" style="background: ${index < 3 ? 'linear-gradient(135deg, #fef3c7, #fbbf24)' : '#f8fafc'};">
                <div class="performer-left">
                    <div class="performer-rank" style="background: ${index === 0 ? '#fbbf24' : index === 1 ? '#9ca3af' : index === 2 ? '#fb923c' : '#64748b'};">
                        ${index + 1} ${crown}
                    </div>
                    <div class="performer-info">
                        <h4>${closerName}</h4>
                        <div class="details">
                            <span style="background: ${closer.center_name === 'jsons' ? '#3b82f6' : '#ef4444'}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; margin-right: 8px;">
                                ${closer.center_name.toUpperCase()}
                            </span>
                            ${conversionRate}%
                        </div>
                    </div>
                </div>
                <div class="performer-stats">
                    <div class="approved" style="color: ${type === 'submissions' ? '#1d4ed8' : '#16a34a'};">${mainValue}</div>
                    <div class="total">${secondaryValue}</div>
                    <div class="pending">${type === 'submissions' ? 'Submissions' : 'Approved'}</div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    console.log(`Successfully rendered ${closers.length} closers for ${type}`);
}

function updateLoadingState(containerId, message) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <div class="loading-container">
                <div class="loading-spinner"></div>
                <p class="loading-text">${message}</p>
            </div>
        `;
    }
}

function showError(containerId, message) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <div class="error-container">
                <p class="error-text">${message}</p>
                <div class="debug-info">
                    Filter: ${currentFilter}<br>
                    Time: ${new Date().toLocaleTimeString()}
                </div>
            </div>
        `;
    }
}

// Function to calculate customer eligibility statistics (sample implementation)
function calculateEligibilityStats(submissions) {
    // Example of how you might implement this in the future:
    if (!submissions || submissions.length === 0) return { levelPercent: 0, giPercent: 0 };
    
    const totalCount = submissions.length;
    const giCount = submissions.filter(item => item.customer_eligibility === 'Guaranteed Issue').length;
    const levelCount = submissions.filter(item => 
        item.customer_eligibility === 'Level' || 
        item.customer_eligibility === 'Graded/Modified'
    ).length;
    
    const giPercent = Math.round((giCount / totalCount) * 100) || 0;
    const levelPercent = Math.round((levelCount / totalCount) * 100) || 0;
    
    return {
        levelPercent,
        giPercent
    };
}

// Auto refresh every 5 minutes
setInterval(() => {
    console.log('Auto-refreshing dashboard...');
    window.location.reload();
}, 300000);

console.log('=== DASHBOARD SCRIPT LOADED ===');
</script>

@endsection
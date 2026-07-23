@extends('layouts.admin')

@section('title', 'Performance Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="hero-title">
                        <i class="fas fa-chart-line me-3"></i>
                        Performance Dashboard
                    </h1>
                    <p class="hero-subtitle">Real-time competition leaderboards and performance analytics</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="time-selector">
                        <div class="btn-group shadow" role="group">
                            <a href="{{ route('reports.dashboard', ['period' => 'daily']) }}" 
                               class="btn {{ $period === 'daily' ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas fa-calendar-day me-1"></i>Daily
                            </a>
                            <a href="{{ route('reports.dashboard', ['period' => 'weekly']) }}" 
                               class="btn {{ $period === 'weekly' ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas fa-calendar-week me-1"></i>Weekly
                            </a>
                            <a href="{{ route('reports.dashboard', ['period' => 'monthly']) }}" 
                               class="btn {{ $period === 'monthly' ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas fa-calendar-alt me-1"></i>Monthly
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-section">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-md-2">
                    <div class="stat-card stat-submissions">
                        <div class="stat-icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $stats['total_submissions'] ?? 0 }}</h3>
                            <p>Total Submissions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card stat-approved">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $stats['total_approved'] ?? 0 }}</h3>
                            <p>Approved</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card stat-clients">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $stats['active_clients'] ?? 0 }}</h3>
                            <p>Active Clients</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card stat-centers">
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $stats['active_centers'] ?? 0 }}</h3>
                            <p>Centers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card stat-closers">
                        <div class="stat-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $stats['active_closers'] ?? 0 }}</h3>
                            <p>Closers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card stat-rate">
                        <div class="stat-icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $stats['total_submissions'] > 0 ? round(($stats['total_approved'] / $stats['total_submissions']) * 100, 1) : 0 }}%</h3>
                            <p>Success Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Center Competition Section -->
    <div class="competition-section">
        <div class="container-fluid">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-trophy me-2"></i>
                    Center Competition Arena
                </h2>
                <a href="{{ route('reports.center', ['period' => $period]) }}" class="btn btn-outline-warning">
                    View Full Report <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>

            @if(count($centerReports) >= 2)
            <div class="competition-arena">
                <div class="row">
                    @foreach($centerReports->take(2) as $index => $center)
                    <div class="col-md-6">
                        <div class="competitor-card {{ $index === 0 ? 'winner' : 'runner-up' }}">
                            <div class="competitor-rank">
                                @if($index === 0)
                                    <i class="fas fa-crown"></i>
                                    <span>CHAMPION</span>
                                @else
                                    <i class="fas fa-medal"></i>
                                    <span>RUNNER-UP</span>
                                @endif
                            </div>
                            <div class="competitor-info">
                                <h3 class="competitor-name">{{ $center['name'] }}</h3>
                                <div class="competitor-stats">
                                    <div class="stat-item">
                                        <span class="stat-number">{{ $center['submissions'] }}</span>
                                        <span class="stat-label">Submissions</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">{{ $center['approved'] }}</span>
                                        <span class="stat-label">Approved</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">{{ $center['approval_rate'] }}%</span>
                                        <span class="stat-label">Success Rate</span>
                                    </div>
                                </div>
                                <div class="progress-wrapper">
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $center['approval_rate'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="no-competition">
                <i class="fas fa-building fa-3x mb-3"></i>
                <h4>No Center Competition Data</h4>
                <p>Need at least 2 centers with data to show competition</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Access Navigation -->
    <div class="navigation-section">
        <div class="container-fluid">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-compass me-2"></i>
                    Quick Access Reports
                </h2>
            </div>

            <div class="row g-4">
                <!-- Client Reports -->
                <div class="col-lg-4">
                    <div class="nav-card client-card">
                        <div class="nav-card-header">
                            <div class="nav-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h4>Client Performance</h4>
                        </div>
                        <div class="nav-card-body">
                            <p>Track client submissions and approval rates across all periods</p>
                            <div class="mini-stats">
                                <div class="mini-stat">
                                    <span class="number">{{ count($clientReports) }}</span>
                                    <span class="label">Active Clients</span>
                                </div>
                                <div class="mini-stat">
                                    <span class="number">{{ $clientReports->sum('submissions') }}</span>
                                    <span class="label">Submissions</span>
                                </div>
                            </div>
                        </div>
                        <div class="nav-card-footer">
                            <a href="{{ route('reports.client', ['period' => $period]) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-chart-line me-2"></i>View Client Reports
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Center Reports -->
                <div class="col-lg-4">
                    <div class="nav-card center-card">
                        <div class="nav-card-header">
                            <div class="nav-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <h4>Center Competition</h4>
                        </div>
                        <div class="nav-card-body">
                            <p>Monitor center-to-center competition and performance metrics</p>
                            <div class="mini-stats">
                                <div class="mini-stat">
                                    <span class="number">{{ count($centerReports) }}</span>
                                    <span class="label">Active Centers</span>
                                </div>
                                <div class="mini-stat">
                                    <span class="number">{{ $centerReports->sum('submissions') }}</span>
                                    <span class="label">Submissions</span>
                                </div>
                            </div>
                        </div>
                        <div class="nav-card-footer">
                            <a href="{{ route('reports.center', ['period' => $period]) }}" class="btn btn-success btn-block">
                                <i class="fas fa-trophy me-2"></i>View Center Competition
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Closer Reports -->
                <div class="col-lg-4">
                    <div class="nav-card closer-card">
                        <div class="nav-card-header">
                            <div class="nav-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h4>Closer Leaderboard</h4>
                        </div>
                        <div class="nav-card-body">
                            <p>Analyze individual closer performance and sales achievements</p>
                            <div class="mini-stats">
                                <div class="mini-stat">
                                    <span class="number">{{ count($closerReports) }}</span>
                                    <span class="label">Active Closers</span>
                                </div>
                                <div class="mini-stat">
                                    <span class="number">{{ $closerReports->sum('sales') ?? 0 }}</span>
                                    <span class="label">Sales Made</span>
                                </div>
                            </div>
                        </div>
                        <div class="nav-card-footer">
                            <a href="{{ route('reports.closer', ['period' => $period]) }}" class="btn btn-info btn-block">
                                <i class="fas fa-medal me-2"></i>View Closer Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Dashboard Styles */
.dashboard-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 0;
}

.hero-section {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.hero-title {
    color: white;
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.hero-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.2rem;
    margin-bottom: 0;
}

.time-selector .btn-group .btn {
    border-radius: 25px;
}

.stats-section {
    margin-bottom: 3rem;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    text-align: center;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-submissions .stat-icon { background: linear-gradient(135deg, #667eea, #764ba2); }
.stat-approved .stat-icon { background: linear-gradient(135deg, #11998e, #38ef7d); }
.stat-clients .stat-icon { background: linear-gradient(135deg, #fd746c, #ff9068); }
.stat-centers .stat-icon { background: linear-gradient(135deg, #f093fb, #f5576c); }
.stat-closers .stat-icon { background: linear-gradient(135deg, #4facfe, #00f2fe); }
.stat-rate .stat-icon { background: linear-gradient(135deg, #ffecd2, #fcb69f); }

.stat-content h3 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #333;
}

.stat-content p {
    color: #666;
    margin-bottom: 0;
    font-weight: 500;
}

.competition-section {
    margin-bottom: 3rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 0 1rem;
}

.section-title {
    color: white;
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 0;
}

.competition-arena {
    margin-bottom: 2rem;
}

.competitor-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1rem;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.competitor-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
}

.competitor-card.winner::before {
    background: linear-gradient(90deg, #ffd700, #ffed4e);
}

.competitor-card.runner-up::before {
    background: linear-gradient(90deg, #c0c0c0, #e5e5e5);
}

.competitor-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.competitor-rank {
    text-align: center;
    margin-bottom: 1.5rem;
}

.competitor-rank i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

.competitor-card.winner .competitor-rank i {
    color: #ffd700;
}

.competitor-card.runner-up .competitor-rank i {
    color: #c0c0c0;
}

.competitor-rank span {
    display: block;
    font-weight: 700;
    font-size: 0.9rem;
    letter-spacing: 1px;
}

.competitor-name {
    text-align: center;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: #333;
}

.competitor-stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 1.5rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
}

.stat-label {
    display: block;
    font-size: 0.8rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.progress-wrapper {
    margin-top: 1rem;
}

.progress {
    height: 10px;
    border-radius: 10px;
    background: #f8f9fa;
}

.progress-bar {
    background: linear-gradient(90deg, #11998e, #38ef7d);
    border-radius: 10px;
    transition: width 1s ease;
}

.no-competition {
    text-align: center;
    color: white;
    padding: 3rem;
}

.navigation-section {
    margin-bottom: 2rem;
}

.nav-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.nav-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.nav-card-header {
    padding: 2rem 2rem 1rem;
    text-align: center;
}

.nav-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.client-card .nav-icon { background: linear-gradient(135deg, #667eea, #764ba2); }
.center-card .nav-icon { background: linear-gradient(135deg, #11998e, #38ef7d); }
.closer-card .nav-icon { background: linear-gradient(135deg, #4facfe, #00f2fe); }

.nav-card-header h4 {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 0;
    color: #333;
}

.nav-card-body {
    padding: 0 2rem 1rem;
    text-align: center;
}

.nav-card-body p {
    color: #666;
    margin-bottom: 1.5rem;
}

.mini-stats {
    display: flex;
    justify-content: space-around;
}

.mini-stat {
    text-align: center;
}

.mini-stat .number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
}

.mini-stat .label {
    display: block;
    font-size: 0.8rem;
    color: #666;
    text-transform: uppercase;
}

.nav-card-footer {
    padding: 1rem 2rem 2rem;
}

.btn-block {
    width: 100%;
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .competitor-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .section-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}
</style>

<script>
// Auto-refresh data every 30 seconds
setInterval(function() {
    // Add auto-refresh logic here if needed
}, 30000);

// Add smooth scrolling and animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate stat cards on load
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 100);
    });
});
</script>
@endsection
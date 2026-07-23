@extends('layouts.admin')

@section('title', 'Closer Teams')

@section('content')
<!-- Bootstrap CDN Links for Index Page - Add to layout head -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* ==================== INDEX PAGE PROFESSIONAL STYLES ==================== */
:root {
    --primary-color: #2563eb;
    --primary-dark: #1d4ed8;
    --secondary-color: #64748b;
    --success-color: #059669;
    --danger-color: #dc2626;
    --warning-color: #d97706;
    --light-bg: #f8fafc;
    --card-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --card-shadow-hover: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --border-radius: 8px;
    --transition: all 0.2s ease-in-out;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background-color: var(--light-bg);
    color: #334155;
    line-height: 1.6;
}

/* ==================== PAGE LAYOUT ==================== */
.index-container {
    min-height: 100vh;
    padding: 2rem 0;
}

.page-header-index {
    background: white;
    border-bottom: 1px solid #e2e8f0;
    padding: 2.5rem 0;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05);
}

.page-title-index {
    font-size: 2.25rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    letter-spacing: -0.025em;
}

.page-subtitle-index {
    color: var(--secondary-color);
    margin-top: 0.5rem;
    font-size: 1.1rem;
    font-weight: 400;
}

.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

/* ==================== STATS SECTION ==================== */
.stats-section {
    margin-bottom: 2.5rem;
}

.stats-grid-index {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.stat-card-index {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: var(--border-radius);
    padding: 2rem 1.5rem;
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.stat-card-index:hover {
    box-shadow: var(--card-shadow-hover);
    transform: translateY(-2px);
}

.stat-card-index::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), #3b82f6);
}

.stat-icon-index {
    width: 3.5rem;
    height: 3.5rem;
    background: linear-gradient(135deg, var(--primary-color), #3b82f6);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.stat-value-index {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label-index {
    color: var(--secondary-color);
    font-size: 0.9rem;
    font-weight: 500;
}

/* ==================== TEAMS GRID ==================== */
.teams-section {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    padding: 1.5rem;
}

.team-card {
    border: 1px solid #e2e8f0;
    border-radius: var(--border-radius);
    background: white;
    transition: var(--transition);
    overflow: hidden;
}

.team-card:hover {
    box-shadow: var(--card-shadow-hover);
    transform: translateY(-2px);
}

.team-card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.team-card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.team-card-body {
    padding: 1.5rem;
}

.team-description-index {
    color: var(--secondary-color);
    font-size: 0.875rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.team-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.team-stat-item {
    flex: 1;
    text-align: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 6px;
}

.team-stat-value {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1e293b;
}

.team-stat-label {
    font-size: 0.875rem;
    color: var(--secondary-color);
}

/* ==================== TEAM LEAD DISPLAY ==================== */
.team-lead-display {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.team-lead-avatar {
    width: 2rem;
    height: 2rem;
    background: linear-gradient(135deg, var(--warning-color), #f59e0b);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.75rem;
}

.team-lead-name {
    font-weight: 500;
    color: #1e293b;
}

/* ==================== STATUS BADGES ==================== */
.status-badge-index {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    gap: 0.25rem;
}

.status-active {
    background-color: #dcfce7;
    color: #166534;
}

.status-inactive {
    background-color: #fecaca;
    color: #991b1b;
}

/* ==================== ACTION BUTTONS ==================== */
.actions-cell {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.btn-action-index {
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.8rem;
    border: none;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.btn-view-index {
    background-color: #eff6ff;
    color: var(--primary-color);
}

.btn-view-index:hover {
    background-color: #dbeafe;
    color: var(--primary-dark);
}

.btn-edit-index {
    background-color: #f0fdf4;
    color: var(--success-color);
}

.btn-edit-index:hover {
    background-color: #dcfce7;
    color: #047857;
}

.btn-delete-index {
    background-color: #fef2f2;
    color: var(--danger-color);
}

.btn-delete-index:hover {
    background-color: #fecaca;
    color: #b91c1c;
}

/* ==================== MAIN CREATE BUTTON ==================== */
.btn-create-main {
    background: linear-gradient(135deg, var(--primary-color), #3b82f6);
    color: white;
    padding: 0.875rem 2rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.2);
}

.btn-create-main:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px 0 rgba(37, 99, 235, 0.3);
    color: white;
}

/* ==================== ALERTS ==================== */
.alert-index {
    padding: 1rem 1.25rem;
    border-radius: var(--border-radius);
    border: 1px solid;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success-index {
    background-color: #f0fdf4;
    border-color: #bbf7d0;
    color: #166534;
}

.alert-danger-index {
    background-color: #fef2f2;
    border-color: #fecaca;
    color: #991b1b;
}

/* ==================== EMPTY STATE ==================== */
.empty-state-index {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border: 2px dashed #d1d5db;
    border-radius: var(--border-radius);
    margin: 2rem 0;
}

.empty-state-icon-index {
    font-size: 4rem;
    color: #9ca3af;
    margin-bottom: 1.5rem;
}

.empty-state-title {
    color: #374151;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.empty-state-text {
    color: var(--secondary-color);
    margin-bottom: 2rem;
    font-size: 1rem;
}

/* ==================== PAGINATION ==================== */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
    padding: 1.5rem;
    background: white;
    border-top: 1px solid #e2e8f0;
}

/* ==================== RESPONSIVE DESIGN ==================== */
@media (max-width: 768px) {
    .index-container {
        padding: 1rem 0;
    }
    
    .page-header-index {
        padding: 1.5rem 0;
    }
    
    .page-title-index {
        font-size: 1.875rem;
    }
    
    .header-actions {
        flex-direction: column;
        gap: 0.75rem;
        width: 100%;
    }
    
    .stats-grid-index {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .stat-card-index {
        padding: 1.5rem 1rem;
    }
    
    .actions-cell {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .btn-action-index {
        font-size: 0.75rem;
        padding: 0.3rem 0.[width:0.6rem] 0.6rem;
    }
}

@media (max-width: 576px) {
    .page-title-index {
        font-size: 1.5rem;
    }
    
    .stat-value-index {
        font-size: 2rem;
    }
    
    .team-card-header {
        padding: 1rem;
    }
}
</style>

<div class="index-container">
    <div class="container mx-auto px-4">
        <!-- Header Section -->
        <div class="page-header-index">
            <div class="text-center">
                <h1 class="page-title-index">Closer Teams</h1>
                <p class="page-subtitle-index">Manage your sales teams efficiently</p>
                <div class="header-actions justify-content-center mt-4">
                    <a href="{{ route('closer-teams.create') }}" class="btn-create-main">
                        <i class="bi bi-plus-circle"></i> Create New Team
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-section">
            <div class="stats-grid-index">
                <div class="stat-card-index">
                    <div class="stat-icon-index"><i class="bi bi-people"></i></div>
                    <div class="stat-value-index">{{ $teams->total() }}</div>
                    <div class="stat-label-index">Total Teams</div>
                </div>
                <div class="stat-card-index">
                    <div class="stat-icon-index"><i class="bi bi-check-circle"></i></div>
                    <div class="stat-value-index">{{ $teams->where('is_active', true)->count() }}</div>
                    <div class="stat-label-index">Active Teams</div>
                </div>
                <div class="stat-card-index">
                    <div class="stat-icon-index"><i class="bi bi-person-circle"></i></div>
                    <div class="stat-value-index">{{ $teams->sum('members_count') }}</div>
                    <div class="stat-label-index">Total Members</div>
                </div>
                <div class="stat-card-index">
                    <div class="stat-icon-index"><i class="bi bi-graph-up"></i></div>
                    <div class="stat-value-index">{{ number_format($teams->sum('members_count') / max($teams->where('is_active', true)->count(), 1), 1) }}</div>
                    <div class="stat-label-index">Avg Team Size</div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert-index alert-success-index">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert-index alert-danger-index">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Teams Grid -->
        <div class="teams-section">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($teams as $team)
                    <div class="team-card">
                        <div class="team-card-header">
                            <div class="flex items-center justify-between">
                                <h3 class="team-card-title">{{ $team->name }}</h3>
                                <span class="status-badge-index {{ $team->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $team->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="team-card-body">
                            @if($team->description)
                                <p class="team-description-index">{{ Str::limit($team->description, 100) }}</p>
                            @endif

                            @if($team->teamLead)
                                <div class="team-lead-display">
                                    <div class="team-lead-avatar">{{ strtoupper(substr($team->teamLead->name, 0, 2)) }}</div>
                                    <span class="team-lead-name">{{ $team->teamLead->name }} <i class="bi bi-crown crown-icon-index"></i></span>
                                </div>
                            @endif

                            <div class="team-stats">
                                <div class="team-stat-item">
                                    <div class="team-stat-value">{{ $team->members_count }}</div>
                                    <div class="team-stat-label">Team Members</div>
                                </div>
                            </div>

                            @if($team->members->count() > 0)
                                <div class="team-members-info">
                                    <div class="members-count">Recent Members:</div>
                                    <div class="flex -space-x-2">
                                        @foreach($team->members->take(4) as $member)
                                            <div class="team-lead-avatar" title="{{ $member->name }}">
                                                {{ strtoupper(substr($member->name, 0, 2)) }}
                                            </div>
                                        @endforeach
                                        @if($team->members_count > 4)
                                            <div class="team-lead-avatar bg-gray-500">
                                                +{{ $team->members_count - 4 }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="actions-cell">
                                <a href="{{ route('closer-teams.show', $team) }}" class="btn-action-index btn-view-index">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('closer-teams.edit', $team) }}" class="btn-action-index btn-edit-index">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('closer-teams.destroy', $team) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-index btn-delete-index" onclick="return confirm('Are you sure you want to delete this team?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-index">
                        <div class="empty-state-icon-index"><i class="bi bi-rocket-takeoff"></i></div>
                        <h3 class="empty-state-title">No Teams Yet</h3>
                        <p class="empty-state-text">Create your first closer team to get started on your journey to success!</p>
                        <a href="{{ route('closer-teams.create') }}" class="btn-create-main">
                            <i class="bi bi-plus-circle"></i> Create First Team
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        @if($teams->hasPages())
            <div class="pagination-wrapper">
                {{ $teams->links() }}
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.team-card');
    
    cards.forEach(card => {
        card.addEventListener26.0; 0.6rem; 0.6rem;
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script>
@endsection
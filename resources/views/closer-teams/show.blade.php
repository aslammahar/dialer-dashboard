@extends('layouts.admin')

@section('title', $closerTeam->name)

@section('content')
<!-- Bootstrap CDN Links for Show Page - Add to layout head -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* ==================== SHOW PAGE PROFESSIONAL STYLES ==================== */
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
.show-container {
    min-height: 100vh;
    padding: 2rem 0;
}

.page-header {
    background: white;
    border-bottom: 1px solid #e2e8f0;
    padding: 1.5rem 0;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05);
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-active {
    background-color: #dcfce7;
    color: #166534;
}

.status-inactive {
    background-color: #fecaca;
    color: #991b1b;
}

/* ==================== INFO CARD ==================== */
.info-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--card-shadow);
    transition: var(--transition);
}

.info-card:hover {
    box-shadow: var(--card-shadow-hover);
}

.info-item {
    margin-bottom: 1rem;
}

.info-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--secondary-color);
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 1rem;
    color: #1e293b;
}

/* ==================== MEMBERS SECTION ==================== */
.members-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--card-shadow);
    transition: var(--transition);
}

.members-card:hover {
    box-shadow: var(--card-shadow-hover);
}

.member-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 6px;
    margin-bottom: 0.5rem;
    transition: var(--transition);
}

.member-item:hover {
    background: #f1f5f9;
}

.member-avatar {
    width: 2.5rem;
    height: 2.5rem;
    background: linear-gradient(135deg, var(--primary-color), #3b82f6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
}

.member-type {
    display: inline-flex;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    background: #eff6ff;
    color: var(--primary-color);
}

/* ==================== STATS SECTION ==================== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.stat-item {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 6px;
    text-align: center;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1e293b;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--secondary-color);
}

/* ==================== BUTTONS ==================== */
.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), #3b82f6);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.3);
}

.btn-danger {
    background: linear-gradient(135deg, var(--danger-color), #b91c1c);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px 0 rgba(220, 38, 38, 0.3);
}

.btn-back {
    color: var(--secondary-color);
    font-weight: 500;
    font-size: 0.875rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: var(--transition);
}

.btn-back:hover {
    color: var(--primary-color);
}

/* ==================== EMPTY STATE ==================== */
.empty-state {
    text-align: center;
    padding: 2rem;
    background: #f8fafc;
    border: 2px dashed #d1d5db;
    border-radius: var(--border-radius);
}

.empty-state-icon {
    font-size: 3rem;
    color: #9ca3af;
    margin-bottom: 1rem;
}

.empty-state-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
}

.empty-state-text {
    font-size: 0.875rem;
    color: var(--secondary-color);
}

/* ==================== RESPONSIVE DESIGN ==================== */
@media (max-width: 768px) {
    .show-container {
        padding: 1rem 0;
    }

    .page-title {
        font-size: 1.75rem;
    }

    .member-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .btn-primary,
    .btn-danger {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .page-title {
        font-size: 1.5rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="show-container">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="page-header">
                <div class="flex items-center gap-3">
                    <a href="{{ route('closer-teams.index') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Back to Teams
                    </a>
                    <h1 class="page-title">{{ $closerTeam->name }}</h1>
                    <span class="status-badge {{ $closerTeam->is_active ? 'status-active' : 'status-inactive' }}">
                        {{ $closerTeam->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('closer-teams.edit', $closerTeam) }}" class="btn-primary">
                        <i class="bi bi-pencil"></i> Edit Team
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Team Information -->
                <div class="lg:col-span-1">
                    <div class="info-card">
                        <h2 class="text-xl font-semibold text-#1e293b mb-4">Team Information</h2>
                        <div class="space-y-4">
                            <div class="info-item">
                                <label class="info-label">Team Name</label>
                                <p class="info-value">{{ $closerTeam->name }}</p>
                            </div>
                            @if($closerTeam->description)
                                <div class="info-item">
                                    <label class="info-label">Description</label>
                                    <p class="info-value">{{ $closerTeam->description }}</p>
                                </div>
                            @endif
                            <div class="info-item">
                                <label class="info-label">Status</label>
                                <p class="info-value">{{ $closerTeam->is_active ? 'Active' : 'Inactive' }}</p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">Total Members</label>
                                <p class="info-value">{{ $closerTeam->members->count() }}</p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">Created</label>
                                <p class="info-value">{{ $closerTeam->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">Last Updated</label>
                                <p class="info-value">{{ $closerTeam->updated_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Members -->
                <div class="lg:col-span-2">
                    <div class="members-card">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-#1e293b">Team Members</h2>
                            <span class="text-sm text-var(--secondary-color)">{{ $closerTeam->members->count() }} members</span>
                        </div>
                        
                        @if($closerTeam->members->count() > 0)
                            <div class="space-y-3">
                                @foreach($closerTeam->members as $member)
                                    <div class="member-item">
                                        <div class="member-avatar">{{ strtoupper(substr($member->name, 0, 2)) }}</div>
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h3 class="text-sm font-medium text-#1e293b">{{ $member->name }}</h3>
                                                    <p class="text-sm text-var(--secondary-color)">{{ $member->email }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs text-var(--secondary-color)">Joined</p>
                                                    <p class="text-sm text-#1e293b">
{{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('M j, Y') : 'N/A' }}                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <span class="member-type">{{ $member->type }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Team Statistics -->
                            <div class="mt-6 pt-6 border-t border-#e2e8f0">
                                <h3 class="text-lg font-medium text-#1e293b mb-4">Team Statistics</h3>
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <div class="stat-value">{{ $closerTeam->members->count() }}</div>
                                        <div class="stat-label">Total Members</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-value">{{ $closerTeam->members->where('pivot.joined_at', '>=', now()->subMonth())->count() }}</div>
                                        <div class="stat-label">Joined This Month</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-people"></i></div>
                                <h3 class="empty-state-title">No Team Members Yet</h3>
                                <p class="empty-state-text">Add some closers to this team to get started.</p>
                                <a href="{{ route('closer-teams.edit', $closerTeam) }}" class="btn-primary">
                                    <i class="bi bi-plus-circle"></i> Add Members
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="mt-6 flex justify-between items-center">
                <div class="flex gap-3">
                    <a href="{{ route('closer-teams.edit', $closerTeam) }}" class="btn-primary">
                        <i class="bi bi-pencil"></i> Edit Team
                    </a>
                    @if($closerTeam->members->count() === 0)
                        <form action="{{ route('closer-teams.destroy', $closerTeam) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger" onclick="return confirm('Are you sure you want to delete this team? This action cannot be undone.')">
                                <i class="bi bi-trash"></i> Delete Team
                            </button>
                        </form>
                    @endif
                </div>
                <div class="text-sm text-var(--secondary-color)">
                    Team created {{ $closerTeam->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
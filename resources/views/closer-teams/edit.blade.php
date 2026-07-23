@extends('layouts.admin')

@section('title', 'Edit Closer Team')

@section('content')
<!-- Bootstrap CDN Links - Add to layout head -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* ==================== EDIT PAGE PROFESSIONAL STYLES ==================== */
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

/* ==================== FORM CONTAINER ==================== */
.form-container {
    padding: 1.5rem 0;
}

.form-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--card-shadow);
    transition: var(--transition);
}

.form-card:hover {
    box-shadow: var(--card-shadow-hover);
}

/* ==================== HEADER STYLES ==================== */
.page-header {
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.page-subtitle {
    font-size: 0.875rem;
    color: var(--secondary-color);
    margin-top: 0.25rem;
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

/* ==================== FORM ELEMENTS ==================== */
.form-group {
    margin-bottom: 1.25rem;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
    display: block;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    color: #1e293b;
    transition: var(--transition);
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-input::placeholder {
    color: #9ca3af;
}

.form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    color: #1e293b;
    resize: vertical;
    min-height: 100px;
    transition: var(--transition);
}

.form-textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    color: #1e293b;
    background: white;
    transition: var(--transition);
}

.form-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.checkbox-custom {
    appearance: none;
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    position: relative;
    transition: var(--transition);
    cursor: pointer;
}

.checkbox-custom:checked {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.checkbox-custom:checked::before {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 0.75rem;
    font-weight: bold;
}

/* ==================== ERROR STYLES ==================== */
.form-error {
    border-color: var(--danger-color);
}

.error-message {
    color: var(--danger-color);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

/* ==================== MEMBER CARDS ==================== */
.member-item, .available-member {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    transition: var(--transition);
}

.member-item:hover, .available-member:hover {
    background: #f1f5f9;
    transform: translateY(-1px);
}

.member-item.team-lead {
    background: #fefce8;
    border-color: var(--warning-color);
}

.member-avatar {
    width: 2rem;
    height: 2rem;
    background: linear-gradient(135deg, var(--primary-color), #3b82f6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.75rem;
}

.available-member .member-avatar {
    background: linear-gradient(135deg, var(--success-color), #10b981);
}

.team-lead-badge {
    display: inline-flex;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    background: var(--warning-color);
    color: white;
}

.member-type, .available-badge {
    display: inline-flex;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    background: #eff6ff;
    color: var(--primary-color);
}

.available-badge {
    background: #d1fae5;
    color: var(--success-color);
}

/* ==================== SCROLLABLE CONTAINERS ==================== */
.scroll-container {
    max-height: 300px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #d1d5db #f8fafc;
}

.scroll-container::-webkit-scrollbar {
    width: 8px;
}

.scroll-container::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 4px;
}

.scroll-container::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}

.scroll-container::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
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
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.75rem;
    border: none;
    cursor: pointer;
    transition: var(--transition);
}

.btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px 0 rgba(220, 38, 38, 0.3);
}

.btn-success {
    background: linear-gradient(135deg, var(--success-color), #10b981);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.75rem;
    border: none;
    cursor: pointer;
    transition: var(--transition);
}

.btn-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px 0 rgba(5, 150, 105, 0.3);
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

/* ==================== NOTIFICATIONS ==================== */
.notification {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 1000;
    max-width: 400px;
    padding: 1rem;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    color: white;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.notification-success {
    background: #dcfce7;
    color: #166534;
}

.notification-error {
    background: #fecaca;
    color: #991b1b;
}

/* ==================== LOADING OVERLAY ==================== */
.loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.loading-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    text-align: center;
    box-shadow: var(--card-shadow);
}

.spinner {
    border: 3px solid #e2e8f0;
    border-top: 3px solid var(--primary-color);
    border-radius: 50%;
    width: 2rem;
    height: 2rem;
    animation: spin 1s linear infinite;
    margin: 0 auto 0.75rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ==================== EMPTY STATE ==================== */
.empty-state {
    text-align: center;
    padding: 1.5rem;
    background: #f8fafc;
    border: 2px dashed #d1d5db;
    border-radius: var(--border-radius);
}

.empty-state-icon {
    font-size: 2.5rem;
    color: #9ca3af;
    margin-bottom: 0.75rem;
}

.empty-state-text {
    font-size: 0.875rem;
    color: var(--secondary-color);
}

/* ==================== ANIMATIONS ==================== */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideOutUp {
    to {
        opacity: 0;
        transform: translateY(-100%);
    }
}

/* ==================== RESPONSIVE DESIGN ==================== */
@media (max-width: 768px) {
    .form-container {
        padding: 1rem 0;
    }

    .page-title {
        font-size: 1.5rem;
    }

    .form-card {
        padding: 1rem;
    }

    .btn-primary, .btn-danger, .btn-success {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .page-title {
        font-size: 1.25rem;
    }

    .page-subtitle {
        font-size: 0.75rem;
    }

    .member-item, .available-member {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<div class="form-container">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="page-header">
                <div class="flex items-center gap-3">
                    <a href="{{ route('closer-teams.index') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Back to Teams
                    </a>
                    <div>
                        <h1 class="page-title">Edit Team</h1>
                        <p class="page-subtitle">{{ $closerTeam->name }}</p>
                    </div>
                    <span class="status-badge {{ $closerTeam->is_active ? 'status-active' : 'status-inactive' }}">
                        {{ $closerTeam->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <!-- Notifications -->
            @if(session('success'))
                <div class="notification notification-success">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="notification notification-error">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Team Details Form -->
                <div class="lg:col-span-2 form-card">
                    <h2 class="text-lg font-semibold text-#1e293b mb-4 flex items-center">
                        <i class="bi bi-pencil-square mr-2"></i> Team Details
                    </h2>
                    <form action="{{ route('closer-teams.update', $closerTeam) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div class="form-group">
                                <label for="name" class="form-label">Team Name *</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', $closerTeam->name) }}"
                                       class="form-input @error('name') form-error @enderror"
                                       required>
                                @error('name')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" 
                                          id="description" 
                                          class="form-textarea @error('description') form-error @enderror"
                                          placeholder="Describe your team's purpose and goals...">{{ old('description', $closerTeam->description) }}</textarea>
                                @error('description')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="team_lead_id" class="form-label">Team Lead</label>
                                <select name="team_lead_id" 
                                        id="team_lead_id" 
                                        class="form-select @error('team_lead_id') form-error @enderror">
                                    <option value="">Select Team Lead (Optional)</option>
                                    @foreach($closerTeam->members as $member)
                                        <option value="{{ $member->id }}" {{ old('team_lead_id', $closerTeam->team_lead_id) == $member->id ? 'selected' : '' }}>
                                            {{ $member->name }} ({{ $member->email }})
                                        </option>
                                    @endforeach
                                    @foreach($availableClosers as $closer)
                                        <option value="{{ $closer->id }}" {{ old('team_lead_id', $closerTeam->team_lead_id) == $closer->id ? 'selected' : '' }}>
                                            {{ $closer->name }} ({{ $closer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_lead_id')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           value="1"
                                           class="checkbox-custom mr-2"
                                           {{ old('is_active', $closerTeam->is_active) ? 'checked' : '' }}>
                                    <span class="text-sm text-#374151">Team is active</span>
                                </label>
                            </div>
                            <button type="submit" class="btn-primary w-full">
                                <i class="bi bi-save"></i> Update Team Details
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Member Management -->
                <div class="form-card">
                    <!-- Current Members -->
                    <h3 class="text-md font-medium text-#1e293b mb-3 flex items-center">
                        <i class="bi bi-person-check mr-2"></i> Current Members
                        <span class="ml-2 text-sm text-var(--secondary-color)">
                            ({{ $closerTeam->members->count() }})
                        </span>
                    </h3>
                    <div class="scroll-container mb-4">
                        @if($closerTeam->members->count() > 0)
                            <div class="space-y-2" id="current-members">
                                @foreach($closerTeam->members as $member)
                                    <div class="member-item {{ $closerTeam->team_lead_id == $member->id ? 'team-lead' : '' }}" data-user-id="{{ $member->id }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="member-avatar mr-2">{{ strtoupper(substr($member->name, 0, 2)) }}</div>
                                                <div>
                                                    <div class="flex items-center">
                                                        <h4 class="text-sm font-semibold text-#1e293b">{{ $member->name }}</h4>
                                                        @if($closerTeam->team_lead_id == $member->id)
                                                            <span class="team-lead-badge ml-2">LEAD</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs text-var(--secondary-color)">{{ $member->email }}</p>
                                                    <p class="text-xs text-var(--secondary-color)">
                                                        Joined {{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('M j, Y') : 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <button type="button" 
                                                    class="remove-member-btn btn-danger"
                                                    data-user-id="{{ $member->id }}"
                                                    data-user-name="{{ $member->name }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-people"></i></div>
                                <p class="empty-state-text">No members in this team yet.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Available Closers -->
                    <h3 class="text-md font-medium text-#1e293b mb-3 flex items-center">
                        <i class="bi bi-person-plus mr-2"></i> Add New Members
                        <span class="ml-2 text-sm text-var(--secondary-color)">
                            ({{ $availableClosers->count() }})
                        </span>
                    </h3>
                    <div class="scroll-container">
                        @if($availableClosers->count() > 0)
                            <div class="space-y-2" id="available-closers">
                                @foreach($availableClosers as $closer)
                                    <div class="available-member" data-user-id="{{ $closer->id }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="member-avatar mr-2">{{ strtoupper(substr($closer->name, 0, 2)) }}</div>
                                                <div>
                                                    <h4 class="text-sm font-semibold text-#1e293b">{{ $closer->name }}</h4>
                                                    <p class="text-xs text-var(--secondary-color)">{{ $closer->email }}</p>
                                                    <span class="available-badge mt-1">Available</span>
                                                </div>
                                            </div>
                                            <button type="button" 
                                                    class="add-member-btn btn-success"
                                                    data-user-id="{{ $closer->id }}"
                                                    data-user-name="{{ $closer->name }}">
                                                <i class="bi bi-plus-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-check-circle"></i></div>
                                <p class="empty-state-text">No available closers.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





<script>
document.addEventListener('DOMContentLoaded', function() {
    const teamId = {{ $closerTeam->id }};
    const csrfToken = '{{ csrf_token() }}';
    const loadingOverlay = document.getElementById('loading-overlay');

    console.log('Team ID:', teamId);
    console.log('CSRF Token:', csrfToken);

    function showLoading() {
        if (loadingOverlay) {
            loadingOverlay.classList.remove('hidden');
        }
    }

    function hideLoading() {
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
        }
    }

    function showNotification(message, type = 'success') {
        // Create Bootstrap alert instead
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1050; max-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Add member functionality
    document.body.addEventListener('click', function(e) {
        if (e.target.closest('.add-member-btn')) {
            e.preventDefault();
            console.log('Add button clicked');
            
            const btn = e.target.closest('.add-member-btn');
            const userId = btn.dataset.userId;
            const userName = btn.dataset.userName;

            console.log('Adding user:', userId, userName);

            if (confirm(`Add ${userName} to this team?`)) {
                showLoading();
                
                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('user_id', userId);

                fetch(`/closer-teams/${teamId}/add-member`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    hideLoading();
                    
                    if (data.success) {
                        showNotification(data.success, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification(data.error || 'Failed to add member', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideLoading();
                    showNotification('Network error occurred', 'error');
                });
            }
        }
    });

    // Remove member functionality
    document.body.addEventListener('click', function(e) {
        if (e.target.closest('.remove-member-btn')) {
            e.preventDefault();
            console.log('Remove button clicked');
            
            const btn = e.target.closest('.remove-member-btn');
            const userId = btn.dataset.userId;
            const userName = btn.dataset.userName;

            console.log('Removing user:', userId, userName);

            if (confirm(`Remove ${userName} from this team?`)) {
                showLoading();
                
                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('_method', 'DELETE');
                formData.append('user_id', userId);

                fetch(`/closer-teams/${teamId}/remove-member`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    hideLoading();
                    
                    if (data.success) {
                        showNotification(data.success, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification(data.error || 'Failed to remove member', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideLoading();
                    showNotification('Network error occurred', 'error');
                });
            }
        }
    });
});
</script>


@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@endsection
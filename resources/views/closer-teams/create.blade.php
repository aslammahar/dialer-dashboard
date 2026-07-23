@extends('layouts.admin')

@section('title', 'Create Closer Team')

@section('content')
<style>
/* ==================== CREATE PAGE PROFESSIONAL STYLES ==================== */
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
    min-height: 100vh;
    padding: 2rem 0;
}

.form-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    padding: 2rem;
}

.form-card:hover {
    box-shadow: var(--card-shadow-hover);
}

/* ==================== HEADER STYLES ==================== */
.page-header {
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.page-subtitle {
    font-size: 1rem;
    color: var(--secondary-color);
    margin-top: 0.5rem;
}

/* ==================== FORM ELEMENTS ==================== */
.form-group {
    margin-bottom: 1.5rem;
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

/* ==================== ERROR STYLES ==================== */
.form-error {
    border-color: var(--danger-color);
}

.error-message {
    color: var(--danger-color);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

/* ==================== MEMBER SELECTION ==================== */
.member-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 0.5rem;
    transition: var(--transition);
    cursor: pointer;
}

.member-card:hover {
    background: #f1f5f9;
    transform: translateY(-2px);
}

.member-card.selected {
    background: #e0f2fe;
    border-color: var(--primary-color);
}

.checkbox-custom {
    appearance: none;
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    position: relative;
    transition: var(--transition);
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

.btn-secondary {
    background: #f1f5f9;
    color: var(--secondary-color);
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.875rem;
    border: 1px solid #d1d5db;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-secondary:hover {
    background: #e2e8f0;
    transform: translateY(-1px);
}

.btn-select-all {
    background: linear-gradient(135deg, var(--success-color), #10b981);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: var(--transition);
}

.btn-select-all:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px 0 rgba(5, 150, 105, 0.3);
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
    .form-container {
        padding: 1rem 0;
    }

    .form-card {
        padding: 1.5rem;
    }

    .page-title {
        font-size: 1.75rem;
    }

    .form-input,
    .form-textarea,
    .form-select {
        padding: 0.5rem 0.75rem;
    }
}

@media (max-width: 576px) {
    .page-title {
        font-size: 1.5rem;
    }

    .page-subtitle {
        font-size: 0.875rem;
    }

    .btn-primary,
    .btn-secondary {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
}
</style>

<div class="form-container">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="page-header">
                <a href="{{ route('closer-teams.index') }}" class="btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <div>
                    <h1 class="page-title">Create New Team</h1>
                    <p class="page-subtitle">Build your dream sales team</p>
                </div>
            </div>

            <form action="{{ route('closer-teams.store') }}" method="POST" class="form-card">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Team Information -->
                    <div>
                        <h2 class="text-xl font-semibold text-#1e293b mb-4 flex items-center">
                            <i class="bi bi-info-circle mr-2"></i> Team Information
                        </h2>
                        <div class="space-y-4">
                            <div class="form-group">
                                <label for="name" class="form-label">Team Name *</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name') }}"
                                       class="form-input @error('name') form-error @enderror"
                                       placeholder="Enter team name..."
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
                                          placeholder="Describe your team's purpose and goals...">{{ old('description') }}</textarea>
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
                                    @foreach($availableClosers as $closer)
                                        <option value="{{ $closer->id }}" {{ old('team_lead_id') == $closer->id ? 'selected' : '' }}>
                                            {{ $closer->name }} ({{ $closer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_lead_id')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Team Members Selection -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-#1e293b flex items-center">
                                <i class="bi bi-people mr-2"></i> Select Members
                            </h2>
                            @if($availableClosers->count() > 0)
                                <button type="button" 
                                        id="selectAllBtn"
                                        class="btn-select-all">
                                    Select All
                                </button>
                            @endif
                        </div>

                        <div class="max-h-96 overflow-y-auto space-y-2">
                            @if($availableClosers->count() > 0)
                                @foreach($availableClosers as $closer)
                                    <div class="member-card" onclick="toggleMember({{ $closer->id }})">
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                   name="members[]" 
                                                   value="{{ $closer->id }}"
                                                   id="member_{{ $closer->id }}"
                                                   class="checkbox-custom mr-3"
                                                   {{ in_array($closer->id, old('members', [])) ? 'checked' : '' }}>
                                            <div class="member-avatar mr-3">{{ strtoupper(substr($closer->name, 0, 2)) }}</div>
                                            <div>
                                                <h3 class="font-semibold text-#1e293b">{{ $closer->name }}</h3>
                                                <p class="text-sm text-var(--secondary-color)">{{ $closer->email }}</p>
                                                <span class="inline-block bg-#eff6ff text-var(--primary-color) text-xs px-2 py-1 rounded-full mt-1">
                                                    {{ $closer->type }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="bi bi-emoji-frown"></i></div>
                                    <h3 class="empty-state-title">No Available Closers</h3>
                                    <p class="empty-state-text">All closers are already assigned to teams.</p>
                                </div>
                            @endif
                        </div>
                        
                        @error('members')
                            <p class="error-message mt-2">{{ $message }}</p>
                        @enderror
                        @error('members.*')
                            <p class="error-message mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('closer-teams.index') }}" class="btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Team
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="members[]"]');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const memberCards = document.querySelectorAll('.member-card');
    
    // Toggle member selection
    window.toggleMember = function(closerId) {
        const checkbox = document.getElementById(`member_${closerId}`);
        const card = checkbox.closest('.member-card');
        
        checkbox.checked = !checkbox.checked;
        
        if (checkbox.checked) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
        
        updateSelectAllButton();
    };
    
    // Select/Deselect all functionality
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
                const card = cb.closest('.member-card');
                if (cb.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            });
            
            updateSelectAllButton();
        });
    }
    
    function updateSelectAllButton() {
        if (selectAllBtn) {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            selectAllBtn.textContent = allChecked ? 'Deselect All' : 'Select All';
        }
    }
    
    // Initialize card states
    checkboxes.forEach(cb => {
        if (cb.checked) {
            cb.closest('.member-card').classList.add('selected');
        }
    });
    
    updateSelectAllButton();
    
    // Add animation
    memberCards.forEach((card, index) => {
        card.style.animation = `fadeInUp 0.6s ease-out ${index * 0.1}s forwards`;
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
    });
});

// Add CSS animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection
@extends('layouts.admin')

@section('title', 'Create Department')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .user-selection-container {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        background: #f8f9fa;
    }
    .user-list-container {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background: white;
        padding: 0.5rem;
    }
    .user-checkbox-item {
        padding: 0.5rem;
        border-bottom: 1px solid #e9ecef;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .user-checkbox-item:hover {
        background-color: #e9ecef;
    }
    .user-checkbox-item:last-child {
        border-bottom: none;
    }
    .selection-stats {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endsection

@section('content')
<div class="mb-4">
    <h2>Create Department</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('salary-departments.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="role_type" class="form-label">Select Role Type <span class="text-danger">*</span></label>
                <select class="form-select @error('role_type') is-invalid @enderror" 
                        id="role_type" name="role_type" required>
                    <option value="">-- Select Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ old('role_type') == $role ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
                @error('role_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Select Users <span class="text-danger">*</span></label>
                
                <div class="user-selection-container">
                    <div class="mb-3">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary" id="selectAll">
                                <i class="fas fa-check-square"></i> Select All
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="deselectAll">
                                <i class="fas fa-square"></i> Deselect All
                            </button>
                        </div>
                        <div class="selection-stats" id="selectionStats">
                            No users selected
                        </div>
                    </div>

                    <div class="user-list-container" id="userListContainer">
                        <div class="text-center text-muted py-3">
                            Please select a role first to see users
                        </div>
                    </div>

                    <!-- Hidden inputs to store selected user IDs as array -->
                    <div id="hiddenInputsContainer">
                        <!-- Dynamic hidden inputs will be added here -->
                    </div>
                </div>

                @error('user_ids')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="text-muted">Click on users to select/deselect them individually</small>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Department
                </button>
                <a href="{{ route('salary-departments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let selectedUsers = new Set();
    let allUsers = [];

    // Load users when role changes
    $('#role_type').on('change', function() {
        const roleType = $(this).val();
        const userListContainer = $('#userListContainer');
        
        if (roleType) {
            userListContainer.html(`
                <div class="text-center py-3">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading users...
                </div>
            `);

            $.ajax({
                url: '{{ route("salary-departments.users-by-role") }}',
                type: 'GET',
                data: { role_type: roleType },
                dataType: 'json',
                success: function(response) {
                    allUsers = response;
                    selectedUsers.clear();
                    renderUserList();
                    updateSelectionStats();
                    updateHiddenInputs();
                },
                error: function(xhr, status, error) {
                    userListContainer.html(`
                        <div class="text-center text-danger py-3">
                            <i class="fas fa-exclamation-triangle"></i> Error loading users
                        </div>
                    `);
                    console.error('Error loading users:', error);
                }
            });
        } else {
            userListContainer.html(`
                <div class="text-center text-muted py-3">
                    Please select a role first to see users
                </div>
            `);
            allUsers = [];
            selectedUsers.clear();
            updateSelectionStats();
            updateHiddenInputs();
        }
    });

    // Render user list with checkboxes
    function renderUserList() {
        const userListContainer = $('#userListContainer');
        
        if (allUsers.length === 0) {
            userListContainer.html(`
                <div class="text-center text-muted py-3">
                    No users found for this role
                </div>
            `);
            return;
        }

        let userListHTML = '';
        allUsers.forEach(user => {
            const isSelected = selectedUsers.has(user.id.toString());
            userListHTML += `
                <div class="user-checkbox-item ${isSelected ? 'bg-light' : ''}" 
                     data-user-id="${user.id}">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" 
                               id="user_${user.id}" 
                               ${isSelected ? 'checked' : ''}>
                        <label class="form-check-label w-100" for="user_${user.id}" 
                               style="cursor: pointer;">
                            <strong>${user.full_name}</strong> 
                            <small class="text-muted">(${user.email})</small>
                        </label>
                    </div>
                </div>
            `;
        });

        userListContainer.html(userListHTML);

        // Add click handlers for checkboxes and list items
        userListContainer.find('.user-checkbox-item').on('click', function(e) {
            const userId = $(this).data('user-id').toString();
            const checkbox = $(this).find('input[type="checkbox"]');
            
            if (selectedUsers.has(userId)) {
                selectedUsers.delete(userId);
                checkbox.prop('checked', false);
                $(this).removeClass('bg-light');
            } else {
                selectedUsers.add(userId);
                checkbox.prop('checked', true);
                $(this).addClass('bg-light');
            }
            
            updateSelectionStats();
            updateHiddenInputs();
        });

        // Prevent checkbox click from triggering the item click twice
        userListContainer.find('input[type="checkbox"]').on('click', function(e) {
            e.stopPropagation();
        });
    }

    // Select all users
    $('#selectAll').on('click', function() {
        if (allUsers.length === 0) return;
        
        selectedUsers.clear();
        allUsers.forEach(user => {
            selectedUsers.add(user.id.toString());
        });
        
        renderUserList();
        updateSelectionStats();
        updateHiddenInputs();
    });

    // Deselect all users
    $('#deselectAll').on('click', function() {
        selectedUsers.clear();
        renderUserList();
        updateSelectionStats();
        updateHiddenInputs();
    });

    // Update selection statistics
    function updateSelectionStats() {
        const statsElement = $('#selectionStats');
        const totalUsers = allUsers.length;
        const selectedCount = selectedUsers.size;
        
        if (totalUsers === 0) {
            statsElement.text('No users available');
        } else if (selectedCount === 0) {
            statsElement.text('No users selected');
        } else if (selectedCount === totalUsers) {
            statsElement.text(`All ${totalUsers} users selected`);
        } else {
            statsElement.text(`${selectedCount} of ${totalUsers} users selected`);
        }
    }

    // Update hidden inputs with selected user IDs as array
    function updateHiddenInputs() {
        const container = $('#hiddenInputsContainer');
        container.empty(); // Clear existing inputs
        
        // Create a hidden input for each selected user ID
        selectedUsers.forEach(userId => {
            container.append(
                $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'user_ids[]')
                    .val(userId)
            );
        });
    }

    // Initialize with old data if exists
    @if(old('user_ids'))
    try {
        const oldUserIds = {!! json_encode(old('user_ids', [])) !!};
        if (Array.isArray(oldUserIds)) {
            oldUserIds.forEach(id => {
                if (id) selectedUsers.add(id.toString());
            });
            // Re-render if we have old data
            if (selectedUsers.size > 0) {
                updateHiddenInputs();
                updateSelectionStats();
            }
        }
    } catch (e) {
        console.error('Error parsing old user_ids:', e);
    }
    @endif
});
</script>
@endsection

@push('scripts')
@endpush
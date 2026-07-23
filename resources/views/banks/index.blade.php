{{-- resources/views/banks/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Bank Management')

@section('styles')
<style>
    .bank-card {
        transition: all 0.3s ease;
    }
    .bank-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .category-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    .status-toggle-checkbox {
        cursor: pointer;
    }
    .table-actions {
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h5 class="mb-0 text-white">
                            <i class="ti ti-building-bank me-2"></i>Bank Management
                        </h5>
                    </div>
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addBankModal">
                            <i class="ti ti-plus me-1"></i>Add New Bank
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="30%">Bank Name</th>
                                <th width="15%">Bank Code</th>
                                <th width="20%">Category</th>
                                <th width="10%" class="text-center">Status</th>
                                <th width="20%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banks as $index => $bank)
                                <tr id="bank-row-{{ $bank->id }}">
                                    <td>{{ $banks->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $bank->name }}</strong>
                                    </td>
                                    <td>
                                        <code>{{ $bank->code }}</code>
                                    </td>
                                    <td>
                                        @if($bank->category)
                                            <span class="badge bg-info category-badge">
                                                {{ $bank->category }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input status-toggle-checkbox" 
                                                   type="checkbox" 
                                                   id="status-{{ $bank->id }}" 
                                                   data-bank-id="{{ $bank->id }}"
                                                   {{ $bank->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-center table-actions">
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="editBank({{ $bank->id }}, '{{ addslashes($bank->name) }}', '{{ $bank->code }}', '{{ $bank->category }}', {{ $bank->is_active ? 'true' : 'false' }})">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteBank({{ $bank->id }}, '{{ addslashes($bank->name) }}')">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="ti ti-building-bank fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted">No banks found</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $banks->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Bank Modal -->
<div class="modal fade" id="addBankModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">
                    <i class="ti ti-plus me-2"></i>Add New Bank
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addBankForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required 
                               placeholder="e.g., Habib Bank Limited">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bank Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" required 
                               placeholder="e.g., HBL">
                        <small class="text-muted">Unique identifier for the bank</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">Select Category</option>
                            <option value="Traditional Commercial Banks">Traditional Commercial Banks</option>
                            <option value="Islamic Banking">Islamic Banking</option>
                            <option value="Digital Banks & Mobile Wallets">Digital Banks & Mobile Wallets</option>
                            <option value="Microfinance Banks">Microfinance Banks</option>
                            <option value="Specialized & Development Banks">Specialized & Development Banks</option>
                            <option value="Foreign Banks">Foreign Banks</option>
                            <option value="Other Financial Institutions">Other Financial Institutions</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" 
                                   id="add_is_active" checked>
                            <label class="form-check-label" for="add_is_active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>Add Bank
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Bank Modal -->
<div class="modal fade" id="editBankModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">
                    <i class="ti ti-edit me-2"></i>Edit Bank
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBankForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_bank_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bank Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="edit_code" class="form-control" required>
                        <small class="text-muted">Unique identifier for the bank</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" id="edit_category" class="form-select">
                            <option value="">Select Category</option>
                            <option value="Traditional Commercial Banks">Traditional Commercial Banks</option>
                            <option value="Islamic Banking">Islamic Banking</option>
                            <option value="Digital Banks & Mobile Wallets">Digital Banks & Mobile Wallets</option>
                            <option value="Microfinance Banks">Microfinance Banks</option>
                            <option value="Specialized & Development Banks">Specialized & Development Banks</option>
                            <option value="Foreign Banks">Foreign Banks</option>
                            <option value="Other Financial Institutions">Other Financial Institutions</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" 
                                   id="edit_is_active">
                            <label class="form-check-label" for="edit_is_active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>Update Bank
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Add Bank Form Submit
    $('#addBankForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        // Explicitly set is_active value based on checkbox state
        if ($('#add_is_active').is(':checked')) {
            formData.set('is_active', '1');
        } else {
            formData.set('is_active', '0');
        }
        
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="ti ti-loader me-1"></i> Adding...');
        
        $.ajax({
            url: '{{ route("banks.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                submitBtn.prop('disabled', false).html(originalText);
                if (response.success) {
                    alert(response.message);
                    $('#addBankModal').modal('hide');
                    $('#addBankForm')[0].reset();
                    location.reload();
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalText);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value[0] + '\n';
                    });
                    alert('Validation Error:\n' + errorMessage);
                } else {
                    alert('An error occurred. Please try again.');
                    console.error('Error:', xhr.responseText);
                }
            }
        });
    });
    
    // Edit Bank Form Submit
    $('#editBankForm').submit(function(e) {
        e.preventDefault();
        
        var bankId = $('#edit_bank_id').val();
        var formData = new FormData(this);
        
        // Explicitly set is_active value based on checkbox state
        if ($('#edit_is_active').is(':checked')) {
            formData.set('is_active', '1');
        } else {
            formData.set('is_active', '0');
        }
        
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="ti ti-loader me-1"></i> Updating...');
        
        $.ajax({
            url: '/banks/' + bankId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                submitBtn.prop('disabled', false).html(originalText);
                if (response.success) {
                    alert(response.message);
                    $('#editBankModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalText);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value[0] + '\n';
                    });
                    alert('Validation Error:\n' + errorMessage);
                } else {
                    alert('An error occurred. Please try again.');
                    console.error('Error:', xhr.responseText);
                }
            }
        });
    });

    // Status Toggle Handler - FIXED VERSION
    $(document).on('change', '.status-toggle-checkbox', function() {
        var bankId = $(this).data('bank-id');
        var checkbox = $(this);
        var currentState = checkbox.is(':checked');
        
        $.ajax({
            url: '/banks/' + bankId + '/toggle-status',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                checkbox.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    console.log('Status updated to:', response.is_active ? 'Active' : 'Inactive');
                    // Optional: Show a success toast notification
                }
            },
            error: function(xhr) {
                alert('Failed to update status');
                // Revert the checkbox to its previous state
                checkbox.prop('checked', !currentState);
            },
            complete: function() {
                checkbox.prop('disabled', false);
            }
        });
    });
});

function editBank(id, name, code, category, isActive) {
    $('#edit_bank_id').val(id);
    $('#edit_name').val(name);
    $('#edit_code').val(code);
    $('#edit_category').val(category);
    $('#edit_is_active').prop('checked', isActive);
    $('#editBankModal').modal('show');
}

function deleteBank(id, name) {
    if (!confirm('Are you sure you want to delete "' + name + '"?')) {
        return;
    }
    
    $.ajax({
        url: '/banks/' + id,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#bank-row-' + id).fadeOut(300, function() {
                    $(this).remove();
                });
                alert(response.message);
            }
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Failed to delete bank';
            alert(message);
        }
    });
}
</script>

@endsection
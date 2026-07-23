@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Add New Salary</h2>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('salaries.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">User <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Agent Name <span class="text-danger">*</span></label>
                            <input type="text" name="agent_name" class="form-control @error('agent_name') is-invalid @enderror" value="{{ old('agent_name') }}" placeholder="Enter agent name">
                            @error('agent_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Designation <span class="text-danger">*</span></label>
                            <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation') }}" placeholder="Enter designation">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Account Number <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number') }}" placeholder="Enter account number">
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name') }}" placeholder="Enter bank name">
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Account Title <span class="text-danger">*</span></label>
                            <input type="text" name="account_title" class="form-control @error('account_title') is-invalid @enderror" value="{{ old('account_title') }}" placeholder="Enter account title">
                            @error('account_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Salary <span class="text-danger">*</span></label>
                            <input type="number" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary') }}" placeholder="Enter salary amount" step="0.01">
                            @error('salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Salary Month <span class="text-danger">*</span></label>
                            <input type="text" name="salary_month" class="form-control @error('salary_month') is-invalid @enderror" value="{{ old('salary_month') }}" placeholder="e.g., March 2025">
                            @error('salary_month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Bank Code <span class="text-danger">*</span></label>
                            <input type="text" name="bank_code" class="form-control @error('bank_code') is-invalid @enderror" value="{{ old('bank_code') }}" placeholder="Enter bank code">
                            @error('bank_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Save
                    </button>
                    <a href="{{ route('salaries.index') }}" class="btn btn-secondary px-4">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0,123,255,0.5);
    }
    .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
    }
    .form-label {
        margin-bottom: 5px;
    }
</style>
@endsection

@section('scripts')
<script>
console.log('javascript working');
// Add this to your create.blade.php file after the form
document.addEventListener('DOMContentLoaded', function() {

    console.log('javascript working');
    // Get the user dropdown element
    const userDropdown = document.querySelector('select[name="user_id"]');
    
    // Listen for changes on the user dropdown
    userDropdown.addEventListener('change', function() {
        const userId = this.value;
        
        // Remove any existing info display
        const existingInfo = document.getElementById('previous-salary-info');
        if (existingInfo) {
            existingInfo.remove();
        }
        
        // Clear previous info if user is deselected
        if (!userId) {
            return;
        }
        
        // Show loading indicator
        const loadingElement = document.createElement('div');
        loadingElement.id = 'previous-salary-loading';
        loadingElement.className = 'mt-3';
        loadingElement.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading previous records...';
        
        // Insert after the first row
        const firstRow = document.querySelector('.row');
        firstRow.parentNode.insertBefore(loadingElement, firstRow.nextSibling);
        
        // Fetch the user's previous salary record
        fetch(`/get-previous-salary/${userId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Remove loading indicator
                document.getElementById('previous-salary-loading').remove();
                
                // Create info element
                const infoElement = document.createElement('div');
                infoElement.id = 'previous-salary-info';
                infoElement.className = 'mt-3 mb-4 p-3 border rounded bg-light';
                
                if (data.exists) {
                    // Create HTML for existing record
                    infoElement.innerHTML = `
                        <h5 class="text-primary mb-3"><i class="fas fa-history me-2"></i>Previous Salary Record Found</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Account Number:</strong> ${data.account_number}</p>
                                <p><strong>Bank Name:</strong> ${data.bank_name}</p>
                                <p><strong>Account Title:</strong> ${data.account_title}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Bank Code:</strong> ${data.bank_code}</p>
                                <p><strong>Last Salary:</strong> ${data.salary}</p>
                                <p><strong>For Month:</strong> ${data.salary_month}</p>
                            </div>
                        </div>
                        <div class="d-flex mt-2">
                            <button type="button" id="auto-fill-btn" class="btn btn-sm btn-info me-2">
                                <i class="fas fa-sync-alt me-1"></i>Auto-fill Details
                            </button>
                            <small class="text-muted d-flex align-items-center">
                                <i class="fas fa-info-circle me-1"></i>Click to fill the form with these details
                            </small>
                        </div>
                    `;
                } else {
                    // Create HTML for no record found
                    infoElement.innerHTML = `
                        <p class="mb-0 text-muted">
                            <i class="fas fa-info-circle me-2"></i>No previous salary records found for this user.
                        </p>
                    `;
                }
                
                // Insert the info element after the first row
                firstRow.parentNode.insertBefore(infoElement, firstRow.nextSibling);
                
                // Add event listener for auto-fill button if it exists
                const autoFillBtn = document.getElementById('auto-fill-btn');
                if (autoFillBtn) {
                    autoFillBtn.addEventListener('click', function() {
                        document.querySelector('input[name="account_number"]').value = data.account_number;
                        document.querySelector('input[name="bank_name"]').value = data.bank_name;
                        document.querySelector('input[name="account_title"]').value = data.account_title;
                        document.querySelector('input[name="bank_code"]').value = data.bank_code;
                        document.querySelector('input[name="agent_name"]').value = data.agent_name;
                        document.querySelector('input[name="designation"]').value = data.designation;
                    });
                }
            })
            .catch(error => {
                // Remove loading indicator
                if (document.getElementById('previous-salary-loading')) {
                    document.getElementById('previous-salary-loading').remove();
                }
                
                // Show error message
                const errorElement = document.createElement('div');
                errorElement.id = 'previous-salary-info';
                errorElement.className = 'mt-3 mb-4 p-3 border rounded bg-danger-subtle';
                errorElement.innerHTML = `
                    <p class="mb-0 text-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>Error loading previous records. Please try again.
                    </p>
                `;
                
                firstRow.parentNode.insertBefore(errorElement, firstRow.nextSibling);
            });
    });
});
</script>
@endsection
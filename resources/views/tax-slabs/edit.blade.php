@extends('layouts.admin')

@section('title', 'Edit Tax Slab')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Edit Tax Slab</h2>
        <a href="{{ route('tax-slabs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('tax-slabs.update', $taxSlab) }}" method="POST" id="taxSlabForm">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="min_salary" class="form-label">
                        Minimum Salary (Yearly) <span class="text-danger">*</span>
                    </label>
                    <input type="number" 
                           step="0.01" 
                           class="form-control @error('min_salary') is-invalid @enderror" 
                           id="min_salary" 
                           name="min_salary" 
                           value="{{ old('min_salary', $taxSlab->min_salary) }}"
                           required>
                    <small class="text-muted">Enter yearly salary amount</small>
                    @error('min_salary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="max_salary" class="form-label">
                        Maximum Salary (Yearly)
                    </label>
                    <input type="number" 
                           step="0.01" 
                           class="form-control @error('max_salary') is-invalid @enderror" 
                           id="max_salary" 
                           name="max_salary" 
                           value="{{ old('max_salary', $taxSlab->max_salary) }}">
                    <small class="text-muted">Leave empty for "& Above"</small>
                    @error('max_salary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="fixed_amount" class="form-label">
                        Fixed Tax Amount
                    </label>
                    <input type="number" 
                           step="0.01" 
                           class="form-control @error('fixed_amount') is-invalid @enderror" 
                           id="fixed_amount" 
                           name="fixed_amount" 
                           value="{{ old('fixed_amount', $taxSlab->fixed_amount) }}">
                    <small class="text-muted">Fixed amount in the tax formula (e.g., Rs. 6,000)</small>
                    @error('fixed_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tax_percentage" class="form-label">
                        Tax Percentage <span class="text-danger">*</span>
                    </label>
                    <input type="number" 
                           step="0.01" 
                           class="form-control @error('tax_percentage') is-invalid @enderror" 
                           id="tax_percentage" 
                           name="tax_percentage" 
                           value="{{ old('tax_percentage', $taxSlab->tax_percentage) }}"
                           required>
                    <small class="text-muted">Percentage to apply on exceeding amount</small>
                    @error('tax_percentage')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          rows="3">{{ old('description', $taxSlab->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" 
                           type="checkbox" 
                           role="switch"
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', $taxSlab->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        <strong>Active Status</strong>
                        <small class="d-block text-muted">Toggle to activate/deactivate this tax slab</small>
                    </label>
                </div>
            </div>

            <hr>

            <!-- Preview Card -->
            <div class="card bg-light mb-3">
                <div class="card-body">
                    <h6>Tax Formula Preview</h6>
                    <div id="formulaPreview" class="fw-bold text-primary">
                        {{ $taxSlab->tax_formula }}
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Tax Slab
                </button>
                <a href="{{ route('tax-slabs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const minSalary = document.getElementById('min_salary');
    const maxSalary = document.getElementById('max_salary');
    const fixedAmount = document.getElementById('fixed_amount');
    const taxPercentage = document.getElementById('tax_percentage');
    const preview = document.getElementById('formulaPreview');

    function updatePreview() {
        const min = parseFloat(minSalary.value) || 0;
        const max = parseFloat(maxSalary.value);
        const fixed = parseFloat(fixedAmount.value) || 0;
        const percentage = parseFloat(taxPercentage.value) || 0;

        let formula = '';
        
        if (percentage === 0) {
            formula = 'No Tax';
        } else {
            if (fixed > 0) {
                formula = `Rs. ${fixed.toLocaleString()} + ${percentage}% of amount exceeding Rs. ${min.toLocaleString()}`;
            } else {
                formula = `${percentage}% of amount exceeding Rs. ${min.toLocaleString()}`;
            }
        }

        const range = max ? `Rs. ${min.toLocaleString()} - Rs. ${max.toLocaleString()}` : `Rs. ${min.toLocaleString()} & Above`;
        
        preview.innerHTML = `<strong>Range:</strong> ${range}<br><strong>Formula:</strong> ${formula}`;
    }

    minSalary.addEventListener('input', updatePreview);
    maxSalary.addEventListener('input', updatePreview);
    fixedAmount.addEventListener('input', updatePreview);
    taxPercentage.addEventListener('input', updatePreview);

    updatePreview();

    // Debug: Log form data on submit
    document.getElementById('taxSlabForm').addEventListener('submit', function(e) {
        console.log('Form submitting...');
        console.log('is_active checked:', document.getElementById('is_active').checked);
    });
});
</script>
@endsection
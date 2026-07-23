@extends('layouts.admin')

@section('title', 'Edit Salary Structure')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Edit Salary Structure</h2>
        <a href="{{ route('salary-structures.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('salary-structures.update', $salaryStructure) }}" method="POST" id="salaryForm">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Department</label>
                    <input type="text" class="form-control" value="{{ $salaryStructure->salaryDepartment->name }} ({{ ucfirst($salaryStructure->salaryDepartment->role_type) }})" disabled>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Employee</label>
                    <input type="text" class="form-control" value="{{ $salaryStructure->user->userDetail->full_name ?? $salaryStructure->user->name }} ({{ $salaryStructure->user->email }})" disabled>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Basic Salary Information</h5>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="basic_salary" class="form-label">Basic Salary <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control @error('basic_salary') is-invalid @enderror" 
                           id="basic_salary" name="basic_salary" 
                           value="{{ old('basic_salary', $salaryStructure->basic_salary) }}" required>
                    @error('basic_salary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="working_days" class="form-label">Working Days <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('working_days') is-invalid @enderror" 
                           id="working_days" name="working_days" 
                           value="{{ old('working_days', $salaryStructure->working_days) }}" required>
                    @error('working_days')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="punctuality" class="form-label">Punctuality Bonus</label>
                    <input type="number" step="0.01" class="form-control @error('punctuality') is-invalid @enderror" 
                           id="punctuality" name="punctuality" 
                           value="{{ old('punctuality', $salaryStructure->punctuality) }}">
                    @error('punctuality')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="effective_from" class="form-label">Effective From <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('effective_from') is-invalid @enderror" 
                           id="effective_from" name="effective_from" 
                           value="{{ old('effective_from', $salaryStructure->effective_from->format('Y-m-d')) }}" required>
                    @error('effective_from')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                               {{ old('is_active', $salaryStructure->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active Status
                        </label>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Additional Components</h5>
                <button type="button" class="btn btn-sm btn-success" id="addComponent">
                    <i class="fas fa-plus"></i> Add Component
                </button>
            </div>

            <div id="componentsContainer">
                @foreach(old('components', $salaryStructure->components) as $index => $component)
                    <div class="card mb-2 component-item">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Component Name</label>
                                    <input type="text" class="form-control" 
                                           name="components[{{ $index }}][name]" 
                                           value="{{ is_array($component) ? $component['name'] : $component->component_name }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Type</label>
                                    <select class="form-select component-type" 
                                            name="components[{{ $index }}][type]" required>
                                        <option value="allowance" {{ (is_array($component) ? $component['type'] : $component->component_type) == 'allowance' ? 'selected' : '' }}>Allowance</option>
                                        <option value="deduction" {{ (is_array($component) ? $component['type'] : $component->component_type) == 'deduction' ? 'selected' : '' }}>Deduction</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" step="0.01" class="form-control component-amount" 
                                           name="components[{{ $index }}][amount]" 
                                           value="{{ is_array($component) ? $component['amount'] : $component->amount }}" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm removeComponent w-100">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <hr>

            <div class="card bg-light mb-3">
                <div class="card-body">
                    <h6>Salary Summary</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <small>Basic Salary:</small>
                            <div id="summaryBasic" class="fw-bold">{{ number_format($salaryStructure->basic_salary, 2) }}</div>
                        </div>
                        <div class="col-md-3">
                            <small>Total Allowances:</small>
                            <div id="summaryAllowances" class="fw-bold text-success">{{ number_format($salaryStructure->total_allowances + $salaryStructure->punctuality, 2) }}</div>
                        </div>
                        <div class="col-md-3">
                            <small>Total Deductions:</small>
                            <div id="summaryDeductions" class="fw-bold text-danger">{{ number_format($salaryStructure->total_deductions, 2) }}</div>
                        </div>
                        <div class="col-md-3">
                            <small>Net Salary:</small>
                            <div id="summaryNet" class="fw-bold text-primary fs-5">{{ number_format($salaryStructure->net_salary, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Salary Structure
                </button>
                <a href="{{ route('salary-structures.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<template id="componentTemplate">
    <div class="card mb-2 component-item">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Component Name</label>
                    <input type="text" class="form-control" name="components[INDEX][name]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select class="form-select component-type" name="components[INDEX][type]" required>
                        <option value="allowance">Allowance</option>
                        <option value="deduction">Deduction</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Amount</label>
                    <input type="number" step="0.01" class="form-control component-amount" name="components[INDEX][amount]" value="0" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm removeComponent w-100">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let componentIndex = {{ count(old('components', $salaryStructure->components)) }};
    const basicSalaryInput = document.getElementById('basic_salary');
    const punctualityInput = document.getElementById('punctuality');
    const addComponentBtn = document.getElementById('addComponent');
    const componentsContainer = document.getElementById('componentsContainer');

    // Add component
    addComponentBtn.addEventListener('click', function() {
        const template = document.getElementById('componentTemplate').innerHTML;
        const newComponent = template.replace(/INDEX/g, componentIndex);
        componentsContainer.insertAdjacentHTML('beforeend', newComponent);
        componentIndex++;
        calculateSalary();
    });

    // Remove component
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('removeComponent') || e.target.closest('.removeComponent')) {
            const btn = e.target.classList.contains('removeComponent') ? e.target : e.target.closest('.removeComponent');
            btn.closest('.component-item').remove();
            calculateSalary();
        }
    });

    // Calculate on input
    basicSalaryInput.addEventListener('input', calculateSalary);
    punctualityInput.addEventListener('input', calculateSalary);
    
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('component-amount')) {
            calculateSalary();
        }
    });
    
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('component-type')) {
            calculateSalary();
        }
    });

    function calculateSalary() {
        const basicSalary = parseFloat(basicSalaryInput.value) || 0;
        const punctuality = parseFloat(punctualityInput.value) || 0;
        let totalAllowances = punctuality;
        let totalDeductions = 0;

        document.querySelectorAll('.component-item').forEach(item => {
            const amount = parseFloat(item.querySelector('.component-amount').value) || 0;
            const type = item.querySelector('.component-type').value;
            
            if (type === 'allowance') {
                totalAllowances += amount;
            } else {
                totalDeductions += amount;
            }
        });

        const netSalary = basicSalary + totalAllowances - totalDeductions;

        document.getElementById('summaryBasic').textContent = basicSalary.toFixed(2);
        document.getElementById('summaryAllowances').textContent = totalAllowances.toFixed(2);
        document.getElementById('summaryDeductions').textContent = totalDeductions.toFixed(2);
        document.getElementById('summaryNet').textContent = netSalary.toFixed(2);
    }

    calculateSalary();
});
</script>
@endsection
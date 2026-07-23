@extends('layouts.admin')

@section('title', 'Setup Salary Structure')

@section('content')
<div class="mb-4">
    <h2>Setup Salary Structure</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('salary-structures.store') }}" method="POST" id="salaryForm">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                    <select class="form-select" id="department_id" name="department_id" required>
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }} ({{ ucfirst($dept->role_type) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="user_id" class="form-label">Employee <span class="text-danger">*</span></label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        <option value="">-- Select Department First --</option>
                    </select>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Basic Salary Information</h5>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="basic_salary" class="form-label">Basic Salary <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="basic_salary" name="basic_salary" value="0" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="working_days" class="form-label">Working Days <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="working_days" name="working_days" value="26" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="punctuality" class="form-label">Punctuality Bonus</label>
                    <input type="number" step="0.01" class="form-control" id="punctuality" name="punctuality" value="0">
                </div>
            </div>

            <div class="mb-3">
                <label for="effective_from" class="form-label">Effective From <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="effective_from" name="effective_from" value="{{ date('Y-m-01') }}" required>
            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Additional Components</h5>
                <button type="button" class="btn btn-sm btn-success" id="addComponent">
                    <i class="fas fa-plus"></i> Add Component
                </button>
            </div>

            <div id="componentsContainer"></div>

            <hr>

            <div class="card bg-light mb-3">
                <div class="card-body">
                    <h6>Salary Summary</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <small>Basic Salary:</small>
                            <div id="summaryBasic" class="fw-bold">0.00</div>
                        </div>
                        <div class="col-md-3">
                            <small>Total Allowances:</small>
                            <div id="summaryAllowances" class="fw-bold text-success">0.00</div>
                        </div>
                        <div class="col-md-3">
                            <small>Total Deductions:</small>
                            <div id="summaryDeductions" class="fw-bold text-danger">0.00</div>
                        </div>
                        <div class="col-md-3">
                            <small>Net Salary:</small>
                            <div id="summaryNet" class="fw-bold text-primary fs-5">0.00</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Salary Structure
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
    console.log('DOM loaded');
    
    let componentIndex = 0;
    const departmentSelect = document.getElementById('department_id');
    const userSelect = document.getElementById('user_id');
    const basicSalaryInput = document.getElementById('basic_salary');
    const punctualityInput = document.getElementById('punctuality');
    const addComponentBtn = document.getElementById('addComponent');
    const componentsContainer = document.getElementById('componentsContainer');

    // Department change handler
    departmentSelect.addEventListener('change', function() {
        const departmentId = this.value;
        console.log('Department selected:', departmentId);
        
        if (!departmentId) {
            userSelect.innerHTML = '<option value="">-- Select Department First --</option>';
            return;
        }
        
        userSelect.disabled = true;
        userSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch('{{ route("salary-structures.users-by-department") }}?department_id=' + departmentId)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                
                userSelect.innerHTML = '<option value="">-- Select Employee --</option>';
                
                if (data && data.length > 0) {
                    data.forEach(user => {
                        const badge = user.has_salary_structure ? ' ✓' : '';
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = user.full_name + ' (' + user.email + ')' + badge;
                        userSelect.appendChild(option);
                    });
                } else {
                    userSelect.innerHTML = '<option value="">No employees found</option>';
                }
                
                userSelect.disabled = false;
            })
            .catch(error => {
                console.error('Fetch error:', error);
                userSelect.innerHTML = '<option value="">Error loading users</option>';
                userSelect.disabled = false;
                alert('Error loading users: ' + error.message);
            });
    });

    // Add component
    addComponentBtn.addEventListener('click', function() {
        console.log('Adding component');
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
        console.log('Calculating salary');
        
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

        console.log('Basic:', basicSalary, 'Allowances:', totalAllowances, 'Deductions:', totalDeductions, 'Net:', netSalary);

        document.getElementById('summaryBasic').textContent = basicSalary.toFixed(2);
        document.getElementById('summaryAllowances').textContent = totalAllowances.toFixed(2);
        document.getElementById('summaryDeductions').textContent = totalDeductions.toFixed(2);
        document.getElementById('summaryNet').textContent = netSalary.toFixed(2);
    }

    calculateSalary();
});
</script>
@endsection
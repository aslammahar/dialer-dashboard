@extends('layouts.admin')

@section('title', 'Bulk Salary Structure Setup')

@section('content')
<div class="mb-4">
    <h2>Bulk Salary Structure Setup</h2>
    <p class="text-muted">Setup or edit salary structures for multiple employees at once</p>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label for="department_id" class="form-label">Select Department <span class="text-danger">*</span></label>
                <select class="form-select" id="department_id">
                    <option value="">-- Select Department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ ucfirst($dept->role_type) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="button" class="btn btn-primary" id="loadDepartment">
                    <i class="fas fa-download"></i> Load Department Data
                </button>
                <button type="button" class="btn btn-success ms-2" id="addNewEmployee" style="display: none;">
                    <i class="fas fa-user-plus"></i> Add New Employee
                </button>
            </div>
        </div>
    </div>
</div>

<div id="loadingSpinner" style="display: none;">
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading department data...</p>
    </div>
</div>

<form action="{{ route('salary-structures.store-bulk') }}" method="POST" id="bulkSalaryForm" style="display: none;">
    @csrf
    <input type="hidden" name="department_id" id="form_department_id">
    
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5>Employee Salary Structures</h5>
            <div>
                <button type="button" class="btn btn-sm btn-info" id="expandAll">
                    <i class="fas fa-expand"></i> Expand All
                </button>
                <button type="button" class="btn btn-sm btn-info" id="collapseAll">
                    <i class="fas fa-compress"></i> Collapse All
                </button>
            </div>
        </div>
    </div>

    <div id="employeesContainer"></div>

    <div class="card mt-3 bg-light">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h5>Summary</h5>
                    <p>Total Employees: <span id="totalEmployees" class="fw-bold">0</span></p>
                    <p>Total Gross Salary: <span id="totalGross" class="fw-bold">0.00</span></p>
                    <p>Total Net Salary: <span id="totalNet" class="fw-bold">0.00</span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save All Salary Structures
        </button>
        <a href="{{ route('salary-structures.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Cancel
        </a>
    </div>
</form>

<!-- Employee Card Template -->
<template id="employeeTemplate">
    <div class="card mb-3 employee-card" data-employee-index="INDEX">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong class="employee-name">Employee Name</strong>
                <small class="text-muted employee-email d-block">email@example.com</small>
                <span class="badge bg-success structure-badge" style="display: none;">Has Structure</span>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary toggle-card">
                    <i class="fas fa-chevron-down"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger remove-employee">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="card-body employee-body">
            <input type="hidden" name="structures[INDEX][id]" class="structure-id">
            <input type="hidden" name="structures[INDEX][user_id]" class="user-id">
            
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Basic Salary <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control basic-salary" name="structures[INDEX][basic_salary]" value="0" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Working Days <span class="text-danger">*</span></label>
                    <input type="number" class="form-control working-days" name="structures[INDEX][working_days]" value="26" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Punctuality Bonus</label>
                    <input type="number" step="0.01" class="form-control punctuality" name="structures[INDEX][punctuality]" value="0">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Effective From <span class="text-danger">*</span></label>
                    <input type="date" class="form-control effective-from" name="structures[INDEX][effective_from]" value="{{ date('Y-m-01') }}" required>
                </div>
            </div>

            <div class="mb-2">
                <button type="button" class="btn btn-sm btn-success add-component">
                    <i class="fas fa-plus"></i> Add Component
                </button>
            </div>

            <div class="components-container"></div>

            <div class="card bg-light mt-3">
                <div class="card-body py-2">
                    <div class="row small">
                        <div class="col-md-3">
                            <small>Basic: <span class="summary-basic">0.00</span></small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-success">Allowances: <span class="summary-allowances">0.00</span></small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-danger">Deductions: <span class="summary-deductions">0.00</span></small>
                        </div>
                        <div class="col-md-3">
                            <small><strong>Net: <span class="summary-net">0.00</span></strong></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Component Template -->
<template id="componentTemplate">
    <div class="card mb-2 component-item">
        <div class="card-body py-2">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Component Name</label>
                    <input type="text" class="form-control form-control-sm" name="structures[EMPINDEX][components][COMPINDEX][name]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Type</label>
                    <select class="form-select form-select-sm component-type" name="structures[EMPINDEX][components][COMPINDEX][type]" required>
                        <option value="allowance">Allowance</option>
                        <option value="deduction">Deduction</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Amount</label>
                    <input type="number" step="0.01" class="form-control form-control-sm component-amount" name="structures[EMPINDEX][components][COMPINDEX][amount]" value="0" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-component w-100">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.employee-body.collapsed {
    display: none;
}
.toggle-card i {
    transition: transform 0.3s;
}
.toggle-card.collapsed i {
    transform: rotate(-90deg);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let employeeIndex = 0;
    let departmentEmployees = [];
    
    const departmentSelect = document.getElementById('department_id');
    const loadDepartmentBtn = document.getElementById('loadDepartment');
    const addNewEmployeeBtn = document.getElementById('addNewEmployee');
    const bulkForm = document.getElementById('bulkSalaryForm');
    const employeesContainer = document.getElementById('employeesContainer');
    const loadingSpinner = document.getElementById('loadingSpinner');

    // Load Department
    loadDepartmentBtn.addEventListener('click', function() {
        const departmentId = departmentSelect.value;
        
        if (!departmentId) {
            alert('Please select a department first');
            return;
        }

        loadingSpinner.style.display = 'block';
        bulkForm.style.display = 'none';
        employeesContainer.innerHTML = '';

        fetch('{{ route("salary-structures.department-structures") }}?department_id=' + departmentId)
            .then(response => response.json())
            .then(data => {
                console.log('Department data:', data);
                departmentEmployees = data.employees;
                
                document.getElementById('form_department_id').value = departmentId;
                
                employeeIndex = 0;
                data.employees.forEach(employee => {
                    addEmployeeCard(employee);
                });
                
                loadingSpinner.style.display = 'none';
                bulkForm.style.display = 'block';
                addNewEmployeeBtn.style.display = 'inline-block';
                
                calculateTotals();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading department data: ' + error.message);
                loadingSpinner.style.display = 'none';
            });
    });

    // Add new employee
    addNewEmployeeBtn.addEventListener('click', function() {
        const departmentId = departmentSelect.value;
        if (!departmentId) return;
        
        // Show modal or dropdown to select from available employees
        showEmployeeSelector();
    });

    function showEmployeeSelector() {
        const existingUserIds = Array.from(document.querySelectorAll('.user-id'))
                                    .map(input => parseInt(input.value));
        
        const availableEmployees = departmentEmployees.filter(emp => 
            !existingUserIds.includes(emp.id)
        );
        
        if (availableEmployees.length === 0) {
            alert('All employees in this department already have salary structures assigned.');
            return;
        }
        
        const options = availableEmployees.map(emp => 
            `<option value="${emp.id}">${emp.full_name} (${emp.email})</option>`
        ).join('');
        
        const html = `
            <div class="modal fade" id="employeeSelectorModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Select Employee</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <select class="form-select" id="newEmployeeSelect">
                                <option value="">-- Select Employee --</option>
                                ${options}
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmAddEmployee">Add</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('employeeSelectorModal');
        if (existingModal) existingModal.remove();
        
        document.body.insertAdjacentHTML('beforeend', html);
        const modal = new bootstrap.Modal(document.getElementById('employeeSelectorModal'));
        modal.show();
        
        document.getElementById('confirmAddEmployee').addEventListener('click', function() {
            const selectedId = document.getElementById('newEmployeeSelect').value;
            if (selectedId) {
                const employee = departmentEmployees.find(emp => emp.id == selectedId);
                if (employee) {
                    addEmployeeCard(employee);
                    calculateTotals();
                }
            }
            modal.hide();
        });
    }

    function addEmployeeCard(employee) {
        const template = document.getElementById('employeeTemplate').innerHTML;
        const cardHtml = template.replace(/INDEX/g, employeeIndex);
        
        employeesContainer.insertAdjacentHTML('beforeend', cardHtml);
        
        const card = employeesContainer.lastElementChild;
        
        // Set employee data
        card.querySelector('.employee-name').textContent = employee.full_name;
        card.querySelector('.employee-email').textContent = employee.email;
        card.querySelector('.user-id').value = employee.id;
        
        if (employee.has_salary_structure && employee.salary_structure) {
            const struct = employee.salary_structure;
            card.querySelector('.structure-id').value = struct.id;
            card.querySelector('.basic-salary').value = struct.basic_salary;
            card.querySelector('.working-days').value = struct.working_days;
            card.querySelector('.punctuality').value = struct.punctuality;
            card.querySelector('.effective-from').value = struct.effective_from;
            card.querySelector('.structure-badge').style.display = 'inline-block';
            
            // Add existing components
            if (struct.components && struct.components.length > 0) {
                struct.components.forEach(comp => {
                    addComponent(card, comp);
                });
            }
        }
        
        // Setup event listeners for this card
        setupCardEvents(card, employeeIndex);
        calculateEmployeeSalary(card);
        
        employeeIndex++;
    }

    function setupCardEvents(card, empIndex) {
        // Toggle card
        card.querySelector('.toggle-card').addEventListener('click', function() {
            const body = card.querySelector('.employee-body');
            body.classList.toggle('collapsed');
            this.classList.toggle('collapsed');
        });
        
        // Remove employee
        card.querySelector('.remove-employee').addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this employee?')) {
                card.remove();
                calculateTotals();
            }
        });
        
        // Add component
        card.querySelector('.add-component').addEventListener('click', function() {
            addComponent(card);
            calculateEmployeeSalary(card);
        });
        
        // Calculate on input
        card.querySelectorAll('.basic-salary, .punctuality').forEach(input => {
            input.addEventListener('input', () => {
                calculateEmployeeSalary(card);
                calculateTotals();
            });
        });
    }

    function addComponent(card, componentData = null) {
        const empIndex = card.dataset.employeeIndex;
        const componentsContainer = card.querySelector('.components-container');
        const componentIndex = componentsContainer.children.length;
        
        const template = document.getElementById('componentTemplate').innerHTML;
        const componentHtml = template.replace(/EMPINDEX/g, empIndex).replace(/COMPINDEX/g, componentIndex);
        
        componentsContainer.insertAdjacentHTML('beforeend', componentHtml);
        
        const componentCard = componentsContainer.lastElementChild;
        
        if (componentData) {
            componentCard.querySelector('input[type="text"]').value = componentData.name;
            componentCard.querySelector('.component-type').value = componentData.type;
            componentCard.querySelector('.component-amount').value = componentData.amount;
        }
        
        // Remove component
        componentCard.querySelector('.remove-component').addEventListener('click', function() {
            componentCard.remove();
            calculateEmployeeSalary(card);
            calculateTotals();
        });
        
        // Calculate on change
        componentCard.querySelector('.component-amount').addEventListener('input', () => {
            calculateEmployeeSalary(card);
            calculateTotals();
        });
        
        componentCard.querySelector('.component-type').addEventListener('change', () => {
            calculateEmployeeSalary(card);
            calculateTotals();
        });
    }

    function calculateEmployeeSalary(card) {
        const basic = parseFloat(card.querySelector('.basic-salary').value) || 0;
        const punctuality = parseFloat(card.querySelector('.punctuality').value) || 0;
        
        let allowances = punctuality;
        let deductions = 0;
        
        card.querySelectorAll('.component-item').forEach(comp => {
            const amount = parseFloat(comp.querySelector('.component-amount').value) || 0;
            const type = comp.querySelector('.component-type').value;
            
            if (type === 'allowance') {
                allowances += amount;
            } else {
                deductions += amount;
            }
        });
        
        const net = basic + allowances - deductions;
        
        card.querySelector('.summary-basic').textContent = basic.toFixed(2);
        card.querySelector('.summary-allowances').textContent = allowances.toFixed(2);
        card.querySelector('.summary-deductions').textContent = deductions.toFixed(2);
        card.querySelector('.summary-net').textContent = net.toFixed(2);
    }

    function calculateTotals() {
        let totalGross = 0;
        let totalNet = 0;
        let count = 0;
        
        document.querySelectorAll('.employee-card').forEach(card => {
            const basic = parseFloat(card.querySelector('.basic-salary').value) || 0;
            const allowances = parseFloat(card.querySelector('.summary-allowances').textContent) || 0;
            const net = parseFloat(card.querySelector('.summary-net').textContent) || 0;
            
            totalGross += basic + allowances;
            totalNet += net;
            count++;
        });
        
        document.getElementById('totalEmployees').textContent = count;
        document.getElementById('totalGross').textContent = totalGross.toFixed(2);
        document.getElementById('totalNet').textContent = totalNet.toFixed(2);
    }

    // Expand/Collapse all
    document.getElementById('expandAll').addEventListener('click', function() {
        document.querySelectorAll('.employee-body').forEach(body => {
            body.classList.remove('collapsed');
        });
        document.querySelectorAll('.toggle-card').forEach(btn => {
            btn.classList.remove('collapsed');
        });
    });

    document.getElementById('collapseAll').addEventListener('click', function() {
        document.querySelectorAll('.employee-body').forEach(body => {
            body.classList.add('collapsed');
        });
        document.querySelectorAll('.toggle-card').forEach(btn => {
            btn.classList.add('collapsed');
        });
    });
});
</script>
@endsection
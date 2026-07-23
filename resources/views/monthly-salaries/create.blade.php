@extends('layouts.admin')

@section('title', 'Generate Monthly Salaries')

@section('content')
<div class="mb-4">
    <h2>Generate Monthly Salaries</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('monthly-salaries.index') }}">Monthly Salaries</a></li>
            <li class="breadcrumb-item active">Generate</li>
        </ol>
    </nav>
</div>

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Step 1: Select Month and Department -->
<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Step 1: Select Month & Department</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('monthly-salaries.create') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Year <span class="text-danger">*</span></label>
                <select name="year" class="form-select" required>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Month <span class="text-danger">*</span></label>
                <select name="month" class="form-select" required>
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Department <span class="text-danger">*</span></label>
                <select name="department_id" class="form-select" required>
                    <option value="">-- Select Department --</option>
                    @foreach($allDepartments as $dept)
                        <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block w-100">
                    <i class="fas fa-arrow-right"></i> Next
                </button>
            </div>
        </form>
        
        @if(!$departmentId)
            <div class="alert alert-info mt-3 mb-0">
                <i class="fas fa-info-circle"></i> <strong>Instructions:</strong>
                <ul class="mb-0 mt-2">
                    <li>Select the year and month for which you want to generate salaries</li>
                    <li>Choose a department to generate salaries for its employees</li>
                    <li>You can generate salaries department by department</li>
                    <li>Click "Next" to load employee data for the selected department</li>
                </ul>
            </div>
        @endif
    </div>
</div>

<!-- Step 2: Generate Salaries (Only shown when department is selected) -->
@if($departmentId && $selectedDepartment && $employees->count() > 0)
<form action="{{ route('monthly-salaries.store') }}" method="POST" id="salaryGenerateForm">
    @csrf
    <input type="hidden" name="year" value="{{ $year }}">
    <input type="hidden" name="month" value="{{ $month }}">
    <input type="hidden" name="department_id" value="{{ $departmentId }}">

    <div class="card mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-users"></i> Step 2: {{ $selectedDepartment->name }} - {{ $employees->count() }} Employees
            </h5>
            <span class="badge bg-light text-dark">
                Period: {{ $months[$month] }} {{ $year }}
            </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="18%">Employee</th>
                            <th width="10%">Basic Salary</th>
                            <th width="9%">Present Days</th>
                            <th width="7%">Absent</th>
                            <th width="7%">Leave</th>
                            <th width="10%">Bonus</th>
                            <th width="11%">Add. Deduction</th>
                            <th width="11%">Net Salary</th>
                            <th width="17%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $user)
                            @php
                                $structure = $user->salaryStructures()
                                                  ->where('is_active', true)
                                                  ->first();
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $user->userDetail->full_name ?? $user->name }}</strong>
                                    <br><small class="text-muted">{{ $user->email }}</small>
                                    <input type="hidden" name="salaries[{{ $loop->index }}][user_id]" value="{{ $user->id }}">
                                    <input type="hidden" name="salaries[{{ $loop->index }}][salary_structure_id]" value="{{ $structure->id }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" 
                                           class="form-control form-control-sm basic-salary" 
                                           value="{{ $structure->basic_salary }}" 
                                           readonly>
                                    <small class="text-muted">Days: {{ $structure->working_days }}</small>
                                </td>
                                <td>
                                    <input type="number" 
                                           class="form-control form-control-sm present-days" 
                                           name="salaries[{{ $loop->index }}][present_days]" 
                                           value="{{ $structure->working_days }}" 
                                           min="0" 
                                           max="{{ $structure->working_days }}" 
                                           required>
                                </td>
                                <td>
                                    <input type="number" 
                                           class="form-control form-control-sm absent-days" 
                                           name="salaries[{{ $loop->index }}][absent_days]" 
                                           value="0" 
                                           min="0">
                                </td>
                                <td>
                                    <input type="number" 
                                           class="form-control form-control-sm leave-days" 
                                           name="salaries[{{ $loop->index }}][leave_days]" 
                                           value="0" 
                                           min="0">
                                </td>
                                <td>
                                    <input type="number" step="0.01" 
                                           class="form-control form-control-sm bonus" 
                                           name="salaries[{{ $loop->index }}][bonus]" 
                                           value="0" 
                                           min="0">
                                </td>
                                <td>
                                    <input type="number" step="0.01" 
                                           class="form-control form-control-sm additional-deduction" 
                                           name="salaries[{{ $loop->index }}][additional_deductions]" 
                                           value="0" 
                                           min="0">
                                </td>
                                <td>
                                    <input type="text" 
                                           class="form-control form-control-sm net-salary bg-light" 
                                           readonly
                                           data-basic="{{ $structure->basic_salary }}"
                                           data-working-days="{{ $structure->working_days }}"
                                           data-punctuality="{{ $structure->punctuality }}"
                                           data-allowances="{{ $structure->total_allowances }}"
                                           data-deductions="{{ $structure->total_deductions }}">
                                </td>
                                <td>
                                    <textarea class="form-control form-control-sm" 
                                              name="salaries[{{ $loop->index }}][remarks]" 
                                              rows="2"
                                              placeholder="Optional notes"></textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="7" class="text-end"><strong>Total Net Salary:</strong></td>
                            <td colspan="2">
                                <strong class="text-primary" id="totalNetSalary">PKR 0.00</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-success btn-lg">
            <i class="fas fa-save"></i> Generate Salaries for {{ $selectedDepartment->name }}
        </button>
        <a href="{{ route('monthly-salaries.create', ['year' => $year, 'month' => $month]) }}" class="btn btn-warning btn-lg">
            <i class="fas fa-undo"></i> Change Department
        </a>
        <a href="{{ route('monthly-salaries.index') }}" class="btn btn-secondary btn-lg">
            <i class="fas fa-times"></i> Cancel
        </a>
    </div>
</form>

@elseif($departmentId && $employees->count() === 0)
<div class="card mb-4">
    <div class="card-body text-center py-5">
        <i class="fas fa-user-slash fa-4x text-muted mb-3"></i>
        <h4>No Employees Found</h4>
        <p class="text-muted">The selected department has no active employees with salary structures.</p>
        <a href="{{ route('monthly-salaries.create', ['year' => $year, 'month' => $month]) }}" class="btn btn-primary">
            <i class="fas fa-undo"></i> Select Another Department
        </a>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .table th {
        background-color: #343a40;
        color: white;
        font-weight: 600;
        vertical-align: middle;
        font-size: 0.9rem;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .form-control-sm {
        font-size: 0.875rem;
    }
    
    .net-salary {
        font-weight: bold;
        color: #28a745;
    }
    
    .card {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    .breadcrumb {
        background-color: #f8f9fa;
        padding: 0.75rem 1rem;
        border-radius: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate net salary on input change
    $(document).on('input', '.present-days, .absent-days, .leave-days, .bonus, .additional-deduction', function() {
        let row = $(this).closest('tr');
        calculateNetSalary(row);
        calculateTotalNetSalary();
    });

    function calculateNetSalary(row) {
        let netSalaryInput = row.find('.net-salary');
        let basicSalary = parseFloat(netSalaryInput.data('basic'));
        let workingDays = parseFloat(netSalaryInput.data('working-days'));
        let punctuality = parseFloat(netSalaryInput.data('punctuality'));
        let allowances = parseFloat(netSalaryInput.data('allowances'));
        let deductions = parseFloat(netSalaryInput.data('deductions'));
        
        let presentDays = parseFloat(row.find('.present-days').val()) || 0;
        let bonus = parseFloat(row.find('.bonus').val()) || 0;
        let additionalDeduction = parseFloat(row.find('.additional-deduction').val()) || 0;
        
        // Calculate per day salary
        let perDaySalary = basicSalary / workingDays;
        let calculatedBasicSalary = perDaySalary * presentDays;
        
        // Calculate net salary
        let netSalary = calculatedBasicSalary + punctuality + allowances - deductions - additionalDeduction + bonus;
        
        netSalaryInput.val(netSalary.toFixed(2));
    }

    function calculateTotalNetSalary() {
        let total = 0;
        $('.net-salary').each(function() {
            let value = parseFloat($(this).val()) || 0;
            total += value;
        });
        $('#totalNetSalary').text('PKR ' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
    }

    // Initial calculation for all rows
    $('.net-salary').each(function() {
        calculateNetSalary($(this).closest('tr'));
    });
    calculateTotalNetSalary();

    // Form validation before submit
    $('#salaryGenerateForm').on('submit', function(e) {
        let hasError = false;
        
        $('.present-days').each(function() {
            let presentDays = parseFloat($(this).val()) || 0;
            let maxDays = parseFloat($(this).attr('max'));
            
            if (presentDays > maxDays) {
                hasError = true;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (hasError) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Present days cannot exceed working days for some employees',
                confirmButtonColor: '#dc3545'
            });
            return false;
        }
        
        // Show loading
        Swal.fire({
            title: 'Generating Salaries...',
            text: 'Please wait while we process the data',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });

    // Auto-dismiss alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush
@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0" style="border-radius: 15px;">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center p-3" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
            <h2 class="mb-0 fw-bold">Salaries Management</h2>
            <div>
                <a href="{{ route('salaries.create') }}" class="btn btn-light btn-sm shadow-sm">
                    <i class="fas fa-plus me-2"></i>Add New Salary
                </a>
            </div>
        </div>
        
        <div class="card-body p-4">
            <!-- Filter and Export Section -->
            <div class="row mb-4 align-items-end">
                <div class="col-md-4">
                    <form method="GET" action="{{ route('salaries.index') }}" class="d-flex align-items-end gap-2">
                        <div class="flex-grow-1">
                            <label class="form-label fw-bold text-muted">Filter by Month</label>
                            <select name="month" class="form-select shadow-sm @error('month') is-invalid @enderror" onchange="this.form.submit()">
                                <option value="">All Months</option>
                                @foreach($months as $monthOption)
                                    <option value="{{ $monthOption }}" {{ $month == $monthOption ? 'selected' : '' }}>
                                        {{ $monthOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="col-md-8 text-end">
                    <div class="d-flex justify-content-end gap-2 align-items-center">
                        <div class="card bg-gradient-success text-black p-2 rounded shadow-sm" style="min-width: 150px;">
                            <strong>Total Salary:</strong> {{ number_format($totalSalary, 2) }}
                        </div>
                        <a href="{{ route('salaries.export', ['month' => $month]) }}" class="btn btn-success btn-sm shadow-sm px-3">
                            <i class="fas fa-file-excel me-2"></i>Export to Excel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Salaries Table -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle shadow-sm" style="border-radius: 10px; overflow: hidden;">
                    <thead class="bg-gradient-light text-dark">
                        <tr>
                            <th class="text-center py-3">User</th>
                            <th class="text-center py-3">Agent Name</th>
                            <th class="text-center py-3">Designation</th>
                            <th class="text-center py-3">Account Number</th>
                            <th class="text-center py-3">Bank Name</th>
                            <th class="text-center py-3">Salary</th>
                            <th class="text-center py-3">Month</th>
                            <th class="text-center py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $salary)
                        <tr class="transition-all">
                            <td>{{ $salary->user->name }}</td>
                            <td>{{ $salary->agent_name }}</td>
                            <td>{{ $salary->designation }}</td>
                            <td>{{ $salary->account_number }}</td>
                            <td>{{ $salary->bank_name }}</td>
                            <td class="text-end">{{ number_format($salary->salary, 2) }}</td>
                            <td>{{ $salary->salary_month }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('salaries.edit', $salary) }}" class="btn btn-warning btn-sm shadow-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('salaries.destroy', $salary) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm shadow-sm" onclick="return confirm('Are you sure you want to delete this salary record?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No salary records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $salaries->appends(['month' => $month])->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Card Styling */
    .card {
        border-radius: 15px;
        transition: all 0.3s ease;
        background: #fff;
    }
    .card:hover {
        box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
    }

    /* Gradient Backgrounds */
    .bg-gradient-primary {
        background: linear-gradient(45deg, #007bff, #00b4ff);
    }
    .bg-gradient-success {
        background: linear-gradient(45deg, #28a745, #34ce57);
    }
    .bg-gradient-light {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    }

    /* Form Elements */
    .form-select {
        border-radius: 10px;
        padding: 12px;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
        background: #fff;
    }
    .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 8px rgba(40,167,69,0.3);
    }
    .form-label {
        font-size: 0.9rem;
        color: #495057;
    }

    /* Buttons */
    .btn {
        border-radius: 10px;
        padding: 8px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .btn-sm {
        padding: 6px 16px;
    }

    /* Table Styling */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        background: #fff;
    }
    .table th {
        font-weight: 700;
        color: #343a40;
        border-bottom: 2px solid #dee2e6;
    }
    .table td {
        vertical-align: middle;
        border-color: #e9ecef;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f3f5;
        transition: background-color 0.2s ease;
    }
    .transition-all {
        transition: all 0.2s ease;
    }

    /* Pagination */
    .pagination .page-link {
        border-radius: 8px;
        margin: 0 2px;
        transition: all 0.3s ease;
    }
    .pagination .page-link:hover {
        transform: scale(1.05);
    }
</style>
@endsection
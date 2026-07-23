@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-warning text-white">
            <h2 class="mb-0">Edit Salary</h2>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('salaries.update', $salary) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">User <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $salary->user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
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
                            <input type="text" name="agent_name" class="form-control @error('agent_name') is-invalid @enderror" value="{{ $salary->agent_name }}" placeholder="Enter agent name">
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
                            <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ $salary->designation }}" placeholder="Enter designation">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Account Number <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ $salary->account_number }}" placeholder="Enter account number">
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
                            <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ $salary->bank_name }}" placeholder="Enter bank name">
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Account Title <span class="text-danger">*</span></label>
                            <input type="text" name="account_title" class="form-control @error('account_title') is-invalid @enderror" value="{{ $salary->account_title }}" placeholder="Enter account title">
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
                            <input type="number" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ $salary->salary }}" placeholder="Enter salary amount" step="0.01">
                            @error('salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Salary Month <span class="text-danger">*</span></label>
                            <input type="text" name="salary_month" class="form-control @error('salary_month') is-invalid @enderror" value="{{ $salary->salary_month }}" placeholder="e.g., March 2025">
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
                            <input type="text" name="bank_code" class="form-control @error('bank_code') is-invalid @enderror" value="{{ $salary->bank_code }}" placeholder="Enter bank code">
                            @error('bank_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-warning px-4 text-white">
                        <i class="fas fa-save me-2"></i>Update
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
        border-color: #ffc107;
        box-shadow: 0 0 5px rgba(255,193,7,0.5);
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
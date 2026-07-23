<!-- resources/views/salaries/show.blade.php -->
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Salary Details</h2>
            <a href="{{ route('salaries.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>User:</strong> {{ $salary->user->name }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Agent Name:</strong> {{ $salary->agent_name }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Designation:</strong> {{ $salary->designation }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Account Number:</strong> {{ $salary->account_number }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Bank Name:</strong> {{ $salary->bank_name }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Account Title:</strong> {{ $salary->account_title }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Salary:</strong> {{ number_format($salary->salary, 2) }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Salary Month:</strong> {{ $salary->salary_month }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Bank Code:</strong> {{ $salary->bank_code }}
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('salaries.edit', $salary) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                <form action="{{ route('salaries.destroy', $salary) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this salary record?')">
                        <i class="fas fa-trash me-2"></i>Delete
                    </button>
                </form>
            </div>
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
    .btn {
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>
@endsection
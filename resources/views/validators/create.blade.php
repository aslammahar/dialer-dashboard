
@extends('layouts.admin')

@section('title', 'Create Validator')



@section('content')

<style>
    .form-container {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 0.5rem;
        padding: 1.5rem;
        max-width: 500px;
        margin: 0 auto;
    }
    .header {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1.5rem;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.25rem;
    }
    .form-input {
        width: 100%;
        padding: 0.5rem;
        font-size: 0.875rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        transition: border-color 0.2s;
    }
    .form-input:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
    }
    .btn-primary {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #ffffff;
        background-color: #4f46e5;
        border: none;
        border-radius: 0.375rem;
        transition: background-color 0.2s;
    }
    .btn-primary:hover {
        background-color: #4338ca;
    }
    .btn-secondary {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        background-color: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        transition: background-color 0.2s;
    }
    .btn-secondary:hover {
        background-color: #f9fafb;
    }
    .error-message {
        color: #dc2626;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }
</style>

    <div class="form-container">
        <h1 class="header">Create Validator</h1>
        <form action="{{ route('validators.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="code" class="form-label">Code (e.g., D6-Agent1)</label>
                <input type="text" name="code" id="code" class="form-input" value="{{ old('code') }}" required>
                @error('code')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required>
                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn-primary">Save</button>
                <a href="{{ route('validators.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
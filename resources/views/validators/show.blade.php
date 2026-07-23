
@extends('layouts.admin')

@section('title', 'Validator Details')

@section('styles')
<style>
    .details-container {
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
    .details-group {
        margin-bottom: 1rem;
    }
    .details-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
    }
    .details-value {
        font-size: 0.875rem;
        color: #1f2937;
        margin-top: 0.25rem;
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
        text-decoration: none;
    }
    .btn-secondary:hover {
        background-color: #f9fafb;
    }
</style>
@endsection

@section('content')
    <div class="details-container">
        <h1 class="header">Validator Details</h1>
        <div class="details-group">
            <p class="details-label">ID</p>
            <p class="details-value">{{ $validator->id }}</p>
        </div>
        <div class="details-group">
            <p class="details-label">Code</p>
            <p class="details-value">{{ $validator->code }}</p>
        </div>
        <div class="details-group">
            <p class="details-label">Name</p>
            <p class="details-value">{{ $validator->name }}</p>
        </div>
        <a href="{{ route('validators.index') }}" class="btn-secondary">Back</a>
    </div>
@endsection
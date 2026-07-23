
@extends('layouts.admin')

@section('title', 'Edit Queue Sale')




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
    .form-select {
        width: 100%;
        padding: 0.5rem;
        font-size: 0.875rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        transition: border-color 0.2s;
        background-color: #ffffff;
    }
    .form-select:focus {
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
        text-decoration: none;
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
        <h1 class="header">Edit Queue Sale Record #{{ $queueSale->id }}</h1>
        <form action="{{ route('queue-sales.update', $queueSale->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="validator_id" class="form-label">Choose Validator</label>
                <select name="validator_id" id="validator_id" class="form-select" required>
                    <option value="">Select...</option>
                    @foreach ($validators as $validator)
                        <option value="{{ $validator->id }}" {{ $queueSale->validator_id == $validator->id ? 'selected' : '' }}>
                            {{ $validator->code }} - {{ $validator->name }}
                        </option>
                    @endforeach
                </select>
                @error('validator_id')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="pending" {{ $queueSale->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $queueSale->status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $queueSale->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                @error('status')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn-primary">Update</button>
                <a href="{{ route('queue-sales.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
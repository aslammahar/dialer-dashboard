@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow border-0 rounded-4">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Create Monthly Expense</h4>
        </div>

        <div class="card-body px-4 py-4">
            <form action="{{ route('expense.monthly.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="accountant_id" class="form-label">Accountant</label>
                        <select name="accountant_id" id="accountant_id" class="form-select" required>
                            <option value="">Select Accountant</option>
                            @foreach($accountants as $accountant)
                                <option value="{{ $accountant->id }}">{{ $accountant->description }} — {{ $accountant->user->name }}</option>
                            @endforeach
                        </select>
                        @error('accountant_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="month_year" class="form-label">Month / Year</label>
                        <input type="month" name="month_year" id="month_year" class="form-control" required>
                        @error('month_year')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="expense_category" class="form-label">Expense Category</label>
                        <select name="expense_category" id="expense_category" class="form-select" required>
                            <option value="">Select Category</option>
                            <option value="internet">Internet</option>
                            <option value="electricity">Electricity</option>
                            <option value="rent">Rent</option>
                            <option value="water">Water</option>
                            <option value="phone">Phone</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="supplies">Supplies</option>
                            <option value="other">Other</option>
                        </select>
                        @error('expense_category')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="debit">Debit (Outgoing )</option>
                            <option value="credit">Credit (Incomming )</option>
                        </select>
                        @error('type')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control" required></textarea>
                    @error('description')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                    @error('amount')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4 py-2">Create Monthly Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

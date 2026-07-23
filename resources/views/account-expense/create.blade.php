@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow border-0 rounded-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0 card-header bg-primary text-white">Create Expense Entry</h4>
        </div>

        <div class="card-body px-4 py-4">
            <form action="{{ route('expense.entries.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="expense_type_id" class="form-label">Accounts</label>
                        <select name="expense_type_id" id="expense_type_id" class="form-select" required>
                            <option value="">Select Account</option>
                            @foreach($expenseTypes as $expenseType)
                                <option value="{{ $expenseType->id }}">{{ $expenseType->description }} — {{ $expenseType->user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" name="description" id="description" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="credit">Credit (Incomming )</option>
                            <option value="debit">Debit (Outgoing )</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea name="remarks" id="remarks" rows="3" class="form-control" placeholder="Optional..."></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4 py-2">Create Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

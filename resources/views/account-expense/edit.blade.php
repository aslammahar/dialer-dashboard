@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Edit Expense Entry</h1>

    <form action="{{ route('expense.entries.update', $expenseEntry->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="expense_type_id">Accounts</label>
            <select name="expense_type_id" id="expense_type_id" class="form-control" required>
                <option value="">Select Account</option>
                @foreach($expenseTypes as $expenseType)
                    <option value="{{ $expenseType->id }}" {{ $expenseEntry->expense_type_id == $expenseType->id ? 'selected' : '' }}>
                        {{ $expenseType->description }} ({{ $expenseType->accountant_title }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ $expenseEntry->date }}" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" name="description" id="description" class="form-control" value="{{ $expenseEntry->description }}" required>
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="credit" {{ $expenseEntry->type == 'credit' ? 'selected' : '' }}>Credit</option>
                <option value="debit" {{ $expenseEntry->type == 'debit' ? 'selected' : '' }}>Debit</option>
            </select>
        </div>
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ $expenseEntry->amount }}" required>
        </div>
        <div class="form-group">
            <label for="remarks">Remarks</label>
            <textarea name="remarks" id="remarks" class="form-control">{{ $expenseEntry->remarks }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Entry</button>
        <a href="{{ route('expense.entries.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
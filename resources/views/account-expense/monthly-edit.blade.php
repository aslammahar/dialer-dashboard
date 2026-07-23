@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Edit Monthly Expense</h1>

    <form action="{{ route('expense.monthly.update', $monthlyExpense) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="accountant_id">Accountant</label>
            <select name="accountant_id" id="accountant_id" class="form-control" required>
                <option value="">Select Accountant</option>
                @foreach($accountants as $accountant)
                    <option value="{{ $accountant->id }}" {{ $monthlyExpense->accountant_id == $accountant->id ? 'selected' : '' }}>
                        {{ $accountant->description }} -- {{ $accountant->user->name}}
                    </option>
                @endforeach
            </select>
            @error('accountant_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="month_year">Month/Year</label>
            <input type="month" name="month_year" id="month_year" class="form-control" 
            value="{{ \Carbon\Carbon::parse($monthlyExpense->month_year)->format('Y-m') }}" required>
            @error('month_year')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="expense_category">Expense Category</label>
            <select name="expense_category" id="expense_category" class="form-control" required>
                <option value="internet" {{ $monthlyExpense->expense_category == 'internet' ? 'selected' : '' }}>Internet</option>
                <option value="electricity" {{ $monthlyExpense->expense_category == 'electricity' ? 'selected' : '' }}>Electricity</option>
                <option value="rent" {{ $monthlyExpense->expense_category == 'rent' ? 'selected' : '' }}>Rent</option>
                <option value="water" {{ $monthlyExpense->expense_category == 'water' ? 'selected' : '' }}>Water</option>
                <option value="phone" {{ $monthlyExpense->expense_category == 'phone' ? 'selected' : '' }}>Phone</option>
                <option value="maintenance" {{ $monthlyExpense->expense_category == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="supplies" {{ $monthlyExpense->expense_category == 'supplies' ? 'selected' : '' }}>Supplies</option>
                <option value="other" {{ $monthlyExpense->expense_category == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('expense_category')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" required>{{ $monthlyExpense->description }}</textarea>
            @error('description')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" 
                   value="{{ $monthlyExpense->amount }}" required>
            @error('amount')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Monthly Expense</button>
        <a href="{{ route('expense.monthly.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
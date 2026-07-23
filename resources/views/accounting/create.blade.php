@extends('layouts.admin') <!-- Assuming you have a master layout -->

@section('content')
<div class="container">
    <h1>Add Accounting Expense</h1>
    <form action="{{ route('accounting.expenses.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="type">Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="daily">Daily</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="transaction_type">Transaction Type</label>
            <select name="transaction_type" id="transaction_type" class="form-control" required>
                <option value="credit">Credit</option>
                <option value="debit">Debit</option>
            </select>
        </div>

        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Expense</button>
    </form>
</div>
@endsection
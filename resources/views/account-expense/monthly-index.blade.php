@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Monthly Expenses</h1>
    
    <!-- Current Month Total Summary Card -->
    <div class="card mb-4 ">
        <div class="card-header bg-primary text-white">
            <h4>{{ now()->format('F Y') }} - Total Expense</h4>
        </div>

        <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    
                    <h4>{{ $selectedMonth }} - Total Debit</h4>
                </div>
                <div class="card-body text-center">
                <h5>Debit (Outgoing) Total</h5>
                    <h2>{{ number_format($monthlyDebitTotal, 2) }}</h2>
                </div>
            </div>
        
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    
                    <h4>{{ $selectedMonth }} - Total Credit</h4>
                </div>
                <div class="card-body text-center">
                <h5>Debit (Outgoing) Total</h5>
                    <h2>{{ number_format($monthlyCreditTotal, 2) }}</h2>
                </div>
            </div>
        </div>
        </div>

        


       
    </div>
    
    <!-- Month Selection Form -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <form method="GET" action="{{ route('expense.monthly.index') }}">
                        <label for="month_year">Select Month:</label>
                        <input type="month" name="month_year" id="month_year" class="form-control" 
                               value="{{ $selectedMonth }}">
                        <button type="submit" class="btn btn-primary mt-2">Filter</button>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="GET" action="{{ route('expense.monthly.export') }}">
                        <input type="hidden" name="month_year" value="{{ $selectedMonth }}">
                        <button type="submit" class="btn btn-success">Export Monthly Report</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Month Header -->
    <div class="alert alert-info mb-4">
        <h5>Showing expenses for: {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}</h5>
    </div>

    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Accountant</th>
                <th>Month/Year</th>
                <th>Category</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthlyExpenses as $expense)
                <tr>
                    <td>{{ $userNames[$accountingUserIds[$expense->accountant_id]] ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($expense->month_year)->format('F Y') }}</td>
                    <td>{{ ucfirst($expense->expense_category) }}</td>
                    <td>{{ $expense->description ?? '-' }}</td>
                    <td>PKR : {{ number_format($expense->amount, 2) }}</td>
                    <td>
                        <a href="{{ route('expense.monthly.edit', $expense) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('expense.monthly.destroy', $expense) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No expenses found for the selected month.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="table-primary">
                <td colspan="4" class="text-right font-weight-bold">Total:</td>
                <td class="font-weight-bold">PKR : {{ number_format($selectedMonthTotal, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Add New Expense Button -->
    <div class="mt-4">
        <a href="{{ route('expense.monthly.create') }}" class="btn btn-primary">Add New Monthly Expense</a>
    </div>
</div>
@endsection
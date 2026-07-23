@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Daily Expense Entries</h1>

    <!-- Today's Total Expense Summary Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="card-header bg-primary text-white">{{ $selectedDate ? date('F d, Y', strtotime($selectedDate)) : 'Today' }} - Total Expense</h4>
        </div>
        <div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4>{{ $selectedDate ? date('F d, Y', strtotime($selectedDate)) : 'Today' }} - Total Debit</h4>
            </div>
            <div class="card-body text-center">
                <h2>{{ number_format($totalDebit, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4>{{ $selectedDate ? date('F d, Y', strtotime($selectedDate)) : 'Today' }} - Total Credit</h4>
            </div>
            <div class="card-body text-center">
                <h2>{{ number_format($totalCredit, 2) }}</h2>
            </div>
        </div>
    </div>
</div>

    </div>

    <!-- Filter by date -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('expense.entries.index') }}" method="GET" class="row align-items-end">
                <div class="col-md-4">
                    <label for="date">Select Date:</label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ $selectedDate ?? now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('expense.entries.index') }}" class="btn btn-secondary">Show Today</a>
                </div>
                <div class="col-md-4 text-right">
                    <a href="{{ route('expense.entries.create') }}" class="btn btn-success">Create New Entry</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table to display entries -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Account</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                    <tr>
                        <!-- Display user name from users table via accounting entries table -->
                        <td>
                            @php
                                $accountingUserId = $accountingUserIds[$entry->expense_type_id] ?? null;
                                $userName = $accountingUserId ? ($userNames[$accountingUserId] ?? 'Unknown') : 'N/A';
                            @endphp
                            {{ $userName }}
                        </td>
                        <td>{{ $entry->date ? \Carbon\Carbon::parse($entry->date)->format('d F Y, h:i A') : 'N/A' }}</td>
                        <td>{{ $entry->description ?? 'N/A' }}</td>
                        <td>{{ ucfirst($entry->type) ?? 'N/A' }}</td>
                        <td>{{ number_format($entry->amount, 2) }}</td>
                        <td>{{ $entry->remarks ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('expense.entries.edit', $entry->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('expense.entries.destroy', $entry->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this entry?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No expense entries found for this date.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-primary">
                    <td colspan="4" class="text-right font-weight-bold">Total:</td>
                    <td class="font-weight-bold">{{ number_format($totalDailyExpense, 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Expense Report</h2>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('expense.report.filter') }}" class="row g-3 mb-4">
    <div class="col-md-3">
        <label for="start_date" class="form-label">Start Date</label>
        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
    </div>
    <div class="col-md-3">
        <label for="end_date" class="form-label">End Date</label>
        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
    </div>
    <div class="col-md-3">
    <label for="accountant_id" class="form-label">Accountant</label>
    <select name="accountant_id" class="form-control">
        <option value="">All Accountants</option>
        @foreach($accountants as $accountant)
            <option value="{{ $accountant->id }}" {{ request('accountant_id') == $accountant->id ? 'selected' : '' }}>
                {{ $accountant->user->name ?? 'Unknown' }}
            </option>
        @endforeach
    </select>
</div>
    <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>
</form>
    <div class="row mb-3">
        <div class="col-md-12">
        <a href="{{ route('expense.report.export', ['start_date' => request('start_date'), 'end_date' => request('end_date'), 'accountant_id' => request('accountant_id')]) }}" class="btn btn-success">
    Export to Excel
</a>
        </div>
    </div>
    {{-- Totals --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card text-bg-success">
                <div class="card-body">
                    <strong>Total Credit (Incomming ):</strong> Rs. {{ number_format($totalCredit) }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-danger">
                <div class="card-body">
                    <strong>Total Debit (Outgoing ):</strong> Rs. {{ number_format($totalDebit) }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-info">
                <div class="card-body">
                    <strong>Net Balance:</strong> Rs. {{ number_format($totalCredit - $totalDebit) }}
                </div>
            </div>
        </div>
    </div>

    {{-- Entries Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Entered By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                    <tr>
                        <td>{{ $entry->date }}</td>
                        <td>{{ $entry->description }}</td>
                        <td>
                            <span class="badge bg-{{ $entry->type === 'debit' ? 'danger' : 'success' }}">
                                {{ ucfirst($entry->type) }}
                            </span>
                        </td>
                        <td>Rs. {{ number_format($entry->amount) }}</td>
                        <td>@php
                                $accountingUserId = $accountingUserIds[$entry->expense_type_id] ?? null;
                                $userName = $accountingUserId ? ($userNames[$accountingUserId] ?? 'Unknown') : 'N/A';
                            @endphp
                            {{ $userName }}</td>
                        
                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No entries found for the selected range.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

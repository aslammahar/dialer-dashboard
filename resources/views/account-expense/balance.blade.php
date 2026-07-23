@extends('layouts.admin')

@section('content')
<style>
    .balances-container {
        padding: 2rem;
        background: #f9fafb;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .balances-heading {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 2rem;
        text-align: center;
        color: #2c3e50;
    }

    .balances-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.75rem;
    }

    .balances-table th {
        background-color: #34495e;
        color: white;
        padding: 1rem;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        text-align: center;
    }

    .balances-table td {
        background-color: white;
        padding: 1rem;
        text-align: center;
        font-weight: 500;
        color: #333;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
    }

    .balances-table tr:hover td {
        background-color: #f1f2f6;
        transition: background-color 0.3s ease;
    }
</style>

<div class="container balances-container">
    <h1 class="balances-heading">📊 Account Balances Overview</h1>

    <table class="table balances-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Total Credit (Incoming )</th>
                <th>Total Debit (Outgoing )</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($balances as $balance)
                <tr>
                    <td>
                        {{ $balance['expenseType']->user->name  }}<br>
                        <small class="text-muted">({{ $balance['expenseType']->accountant_title }})</small>
                    </td>
                    <td class="text-success">PKR : {{ number_format($balance['credit'], 2) }}</td>
                    <td class="text-danger">PKR : {{ number_format($balance['debit'], 2) }}</td>
                    <td class="{{ $balance['balance'] >= 0 ? 'text-success' : 'text-primary' }}">
                    PKR : {{ number_format($balance['balance'], 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<style>
    /* Inline CSS */
    h1 {
        font-weight: bold;
    }

    .table th,
    .table td {
        font-weight: bold;
    }

    .filters {
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        width: 50%;
    }

    .filters .form-group {
        margin-bottom: 15px;
    }

    .filters label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .filters select,
    .filters input[type="date"] {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .filters .btn {
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        border: none;
        border-radius: 4px;
        color: #fff;
        font-weight: bold;
        cursor: pointer;
    }

    .filters .btn:hover {
        background-color: #0056b3;
    }

    .table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .table th {
        background-color: #f2f2f2;
    }
</style>

<div class="container">
    <h1>Dialer Stats</h1>
    <div class="filters">
        <form method="GET" action="{{ route('dialer.stats') }}">
            <div class="form-group">
                <label for="entry_list_id">List Id:</label>
                <select id="entry_list_id" name="entry_list_id">
                    <option value="">All</option>
                    @foreach($listIds as $listId)
                    <option value="{{ $listId }}" {{ request('entry_list_id') == $listId ? 'selected' : '' }}>
                        {{ $listId }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="list_name">List Name:</label>
                <select id="list_name" name="list_name">
                    <option value="">All</option>
                    @foreach($listNames as $listName)
                    <option value="{{ $listName }}" {{ request('list_name') == $listName ? 'selected' : '' }}>
                        {{ $listName }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="centername">Center Name:</label>
                <select id="centername" name="centername">
                    <option value="">All</option>
                    @foreach($centerNames as $centerName)
                    <option value="{{ $centerName }}" {{ request('centername') == $centerName ? 'selected' : '' }}>
                        {{ $centerName }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="dialername">Dialer Name:</label>
                <select id="dialername" name="dialername">
                    <option value="">All</option>
                    @foreach($dialerNames as $dialerName)
                    <option value="{{ $dialerName }}" {{ request('dialername') == $dialerName ? 'selected' : '' }}>
                        {{ $dialerName }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Entry List ID</th>
                <th>List Name</th>
                <th>Center Name</th>
                <th>Dialer Name</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entryListIds as $entryListId)
            <tr>
                <td>{{ $entryListId->entry_list_id }}</td>
                <td>{{ $entryListId->list_name }}</td>
                <td>{{ $entryListId->centername }}</td>
                <td>{{ $entryListId->dialername }}</td>
                <td>{{ $entryListId->count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script src="{{ asset('js/app.js') }}"></script>

@endsection
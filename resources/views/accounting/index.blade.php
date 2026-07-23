@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Accounting Entries</h1>

    <form action="{{ route('accounting.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="user_id">User</label>
            <select name="user_id" id="user_id" class="form-control" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" name="description" id="description" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="accountant_title">Accountant Title</label>
            <input type="text" name="accountant_title" id="accountant_title" class="form-control" required>
        </div>
       
        <button type="submit" class="btn btn-primary">Add Entry</button>
    </form>

    <h2>Entries</h2>
    <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Description</th>
                <th>Accountant Title</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
                <tr>
                    <td>{{ $entry->user->name }}</td>
                    <td>{{ $entry->description }}</td>
                    <td>{{ $entry->accountant_title }}</td>
                    <td>{{ $entry->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('accounting.edit', $entry->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('accounting.destroy', $entry->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this entry?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination">
        {{ $entries->links() }}
    </div>
</div>
@endsection
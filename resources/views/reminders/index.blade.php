@extends('layouts.admin')

@section('content')

<div class="container">
    <h1>My Reminders</h1>

    <a href="{{ route('reminders.create') }}" class="btn btn-primary mb-3">Create New Reminder</a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 20%;">Title</th>
                    <th style="width: 40%;">Description</th>
                    <th style="width: 15%;">Reminder Time</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 15%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reminders as $reminder)
                <tr>
                    <td style="word-wrap: break-word; white-space: normal;">{{ $reminder->title }}</td>
                    <td style="word-wrap: break-word; white-space: normal;">{{ $reminder->description }}</td>
                    <td>{{ $reminder->reminder_time->format('Y-m-d H:i') }}</td>
                    <td>{{ ucfirst($reminder->status) }}</td>
                    <td>
                        <a href="{{ route('reminders.edit', $reminder) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('reminders.destroy', $reminder) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $reminders->links() }}
</div>
@endsection

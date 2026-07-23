@extends('layouts.admin')

@section('content')
    <h1>Assign Users to {{ $vendor->vendor_name }}</h1>

    <form action="{{ route('data-vendor.assign-users', $vendor->id) }}" method="POST">
        @csrf
        <table class="table">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}">
                        </td>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Assign Selected Users</button>
    </form>
@endsection
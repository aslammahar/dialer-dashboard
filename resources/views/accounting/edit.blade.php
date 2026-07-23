@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Edit Accounting Entry</h1>

    <form action="{{ route('accounting.update', $entry->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="user_id">User</label>
            <select name="user_id" id="user_id" class="form-control" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $entry->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" name="description" id="description" class="form-control" value="{{ $entry->description }}" required>
        </div>
        <div class="form-group">
            <label for="accountant_title">Accountant Title</label>
            <input type="text" name="accountant_title" id="accountant_title" class="form-control" value="{{ $entry->accountant_title }}" required>
        </div>
       
        <button type="submit" class="btn btn-primary">Update Entry</button>
        <a href="{{ route('accounting.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
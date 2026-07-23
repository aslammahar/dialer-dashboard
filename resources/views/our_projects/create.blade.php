@extends('layouts.admin')
@section('content')
<h1>Create Project</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('our_projects.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea name="description" id="description" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Create</button>
    <a href="{{ route('our_projects.index') }}" class="btn btn-secondary">Cancel</a>
</form>


@endsection
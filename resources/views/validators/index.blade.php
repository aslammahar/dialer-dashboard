
@extends('layouts.admin')

@section('title', 'Validators')



@section('content')

<style>
    .header {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1.5rem;
    }
    .btn-primary {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #ffffff;
        background-color: #4f46e5;
        border: none;
        border-radius: 0.375rem;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    .btn-primary:hover {
        background-color: #4338ca;
    }
    .btn-warning {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #ffffff;
        background-color: #d97706;
        border: none;
        border-radius: 0.375rem;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    .btn-warning:hover {
        background-color: #b45309;
    }
    .btn-danger {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #ffffff;
        background-color: #dc2626;
        border: none;
        border-radius: 0.375rem;
        transition: background-color 0.2s;
    }
    .btn-danger:hover {
        background-color: #b91c1c;
    }
    .table-container {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th {
        padding: 0.75rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 500;
        color: #6b7280;
        text-transform: uppercase;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
    .table td {
        padding: 1rem 1.5rem;
        font-size: 0.875rem;
        color: #1f2937;
        border-bottom: 1px solid #e5e7eb;
    }
    .table tr:hover {
        background-color: #f9fafb;
    }
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    .pagination {
        margin-top: 1rem;
        display: flex;
        justify-content: center;
        gap: 0.5rem;
    }
    .pagination a, .pagination span {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        color: #4f46e5;
        text-decoration: none;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        transition: background-color 0.2s;
    }
    .pagination a:hover {
        background-color: #f9fafb;
    }
    .pagination .current {
        background-color: #4f46e5;
        color: #ffffff;
        border-color: #4f46e5;
    }
</style>

    <div class="header">Validators</div>
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('validators.create') }}" class="btn-primary">Create Validator</a>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($validators as $validator)
                    <tr>
                        <td>{{ $validator->id }}</td>
                        <td>{{ $validator->code }}</td>
                        <td>{{ $validator->name }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('validators.edit', $validator->id) }}" class="btn-warning">Edit</a>
                                <form action="{{ route('validators.destroy', $validator->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">
        {{ $validators->links() }}
    </div>
@endsection
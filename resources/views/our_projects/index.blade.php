@extends('layouts.admin')

@section('content')
    <h1 class="mb-4 text-center text-primary font-weight-bold">📌 Projects</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif


    @can('opmanage')

    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('our_projects.create') }}" class="btn btn-success shadow-sm">
            ➕ Create New Project
        </a>
    </div>
    @endcan


    @if ($projects->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-uppercase">Project Name</th>
                        <th class="text-uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                        <tr>
                            <td>
                                <a href="{{ route('our_projects.show', $project) }}" 
                                   class="d-block font-weight-bold text-primary text-decoration-none" 
                                   style="font-size: 1.2rem; transition: color 0.3s;">
                                    🎯 {{ strtoupper($project->name) }}
                                </a>
                            </td>


                            @can('opmanage')

                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('our_projects.edit', $project) }}" 
                                       class="btn btn-sm btn-warning">
                                        ✏️ Edit
                                    </a>
                                    <form action="{{ route('our_projects.destroy', $project) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure?')">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endcan




                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning text-center">🚀 No projects found. Start by creating a new one!</div>
    @endif
@endsection
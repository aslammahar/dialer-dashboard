@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dialer Management</h2>
            <div>
                <a href="{{ route('dialers-unified.index') }}" class="btn btn-outline-primary me-2">Dialers Unified Table</a>
                <a href="{{ route('dialers.create') }}" class="btn btn-primary">Create New Dialer</a>
            </div>
        </div>

        <!-- Dialer Lists Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Dialer Lists</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Dialer IP</th>
                                <th>Weblink</th>
                                <th>Access</th>
                                <th>Dialer No</th>
                                <th>Team</th>
                                <th>Recording Link</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dialerLists as $dialer)
                            <tr>
                                <td>{{ $dialer->id }}</td>
                                <td>{{ $dialer->dialer_ip }}</td>
                                <td>{{ $dialer->dialer_weblink }}</td>
                                <td>{{ $dialer->dialer_access }}</td>
                                <td>{{ $dialer->dialer_no }}</td>
                                <td>{{ $dialer->dialer_team }}</td>
                                <td>
                                    @if($dialer->recording_link)
                                        <a href="{{ $dialer->recording_link }}" target="_blank" title="{{ $dialer->recording_link }}">
                                            {{ strlen($dialer->recording_link) > 50 ? substr($dialer->recording_link, 0, 50) . '...' : $dialer->recording_link }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('dialers.edit', $dialer->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('dialers.destroy', $dialer->id) }}" method="POST" class="d-inline">
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
            </div>
        </div>

        <!-- Dialer Servers Table -->
        <div class="card">
            <div class="card-header">
                <h5>Dialer Servers</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Dialer Name</th>
                                <th>Server No</th>
                                <th>Server IP</th>
                                <th>Folder Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dialerServers as $server)
                            <tr>
                                <td>{{ $server->id }}</td>
                                <td>{{ $server->dialer_name }}</td>
                                <td>{{ $server->server_no }}</td>
                                <td>{{ $server->server_ip }}</td>
                                <td>{{ $server->folder_name }}</td>
                                <td>
                                    <span class="badge bg-{{ $server->server_status ? 'success' : 'danger' }}">
                                        {{ $server->server_status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
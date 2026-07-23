@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dialers Unified</h2>
            <a href="{{ route('dialers-unified.create') }}" class="btn btn-primary">Add New</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5>All Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Dialer IP</th>
                                <th>Dialer No</th>
                                <th>Dialer Name</th>
                                <th>Server IP</th>
                                <th>Folder</th>
                                <th>Status</th>
                                <th>Recording Link</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dialers as $d)
                            <tr>
                                <td>{{ $d->id }}</td>
                                <td>{{ $d->dialer_ip ?? '—' }}</td>
                                <td>{{ $d->dialer_no ?? '—' }}</td>
                                <td>{{ $d->dialer_name ?? '—' }}</td>
                                <td>{{ $d->server_ip ?? '—' }}</td>
                                <td>{{ $d->folder_name ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ ($d->server_status ?? '0') == '1' ? 'success' : 'secondary' }}">
                                        {{ ($d->server_status ?? '0') == '1' ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    @if(!empty($d->recording_link))
                                        <a href="{{ $d->recording_link }}" target="_blank" title="{{ $d->recording_link }}">
                                            {{ strlen($d->recording_link) > 40 ? substr($d->recording_link, 0, 40) . '...' : $d->recording_link }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('dialers-unified.edit', $d->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('dialers-unified.destroy', $d->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No records yet. <a href="{{ route('dialers-unified.create') }}">Add one</a>.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

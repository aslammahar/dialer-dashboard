@extends('layouts.admin')

@section('content')
    <!-- Back Button -->

    <a href="{{ route('our_projects.index') }}" class="btn btn-danger mb-3 shadow-sm mt-3">Back to Projects</a>

    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary font-weight-bold">
            📢 Campaigns for Project: <span class="text-dark">{{ strtoupper($our_project->name) }}</span>
        </h2>

        @can('opmanage')
        <a href="{{ route('our_projects.our_campaigns.create', $our_project) }}" class="btn btn-success shadow-sm">
            ➕ Create New Campaign
        </a>
        @endcan

    </div>

    <!-- Success & Error Messages -->
    @if (session('success'))
        <div class="alert alert-success shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger shadow-sm">
            ❌ {{ session('error') }}
        </div>
    @endif

    @if ($campaigns->isEmpty())
        <div class="alert alert-warning text-center">
            🚀 No campaigns found for this project.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover shadow-lg rounded-lg">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                        <th>Responses</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($campaigns as $campaign)
                        <tr>
                            <td class="font-weight-bold text-primary">
                                📌 {{ $campaign->name }}
                            </td>
                            <td>
                                📝 {{ $campaign->description ?? 'No description available' }}
                            </td>
                            <td>
                                <a href="{{ route('our_campaigns.form', $campaign) }}" 
                                   class="btn btn-sm btn-info shadow-sm" target="_blank">📄 View Form</a>

                                   @can('opmanage')

                                <a href="{{ route('our_projects.our_campaigns.edit', [$our_project, $campaign]) }}" 
                                   class="btn btn-sm btn-warning shadow-sm">✏️ Edit</a>


                                <form action="{{ route('our_projects.our_campaigns.destroy', [$our_project, $campaign]) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger shadow-sm"
                                            onclick="return confirm('Are you sure you want to delete this campaign?')">
                                        🗑️ Delete
                                    </button>
                                </form>
                            @endcan

                            </td>
                            <td>
                                @if ($campaign->responses->isEmpty())
                                    <span class="text-muted">No responses yet.</span>
                                @else
                                    <a href="{{ route('our_campaigns.responses.index', $campaign) }}" 
                                       class="btn btn-sm btn-secondary shadow-sm">📊 View Responses</a>
                                    <div class="collapse mt-2" id="responses-{{ $campaign->id }}">
                                        <div class="card card-body">
                                            <ul class="list-unstyled">
                                                @foreach ($campaign->responses as $response)
                                                    <li class="border-bottom pb-2 mb-2">
                                                        <pre class="bg-light p-2 rounded">{{ json_encode($response->submission_data, JSON_PRETTY_PRINT) }}</pre>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
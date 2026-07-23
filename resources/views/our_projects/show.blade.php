@extends('layouts.admin')

@section('content')
    <!-- Back Button -->
    <button class="btn btn-danger mb-3 shadow-sm" onclick="window.history.back();">
        ⬅️ Back
    </button>

    <!-- Project Details Card -->
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-primary text-white text-center">
            <h2 class="font-weight-bold">📌 Project Details</h2>
        </div>
        <div class="card-body">
            <h4 class="text-dark font-weight-bold">
                🏷️ Name: <span class="text-primary">{{ strtoupper($our_project->name) }}</span>
            </h4>
            <p class="mt-2" style="font-size: 1.1rem;">
                📝 <strong>Description:</strong> 
                <span class="text-muted">{{ $our_project->description ?? 'No description provided.' }}</span>
            </p>
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('our_projects.our_campaigns.index', $our_project) }}" 
               class="btn btn-success shadow-sm px-4">
                🎯 View Campaigns
            </a>
        </div>
    </div>
@endsection
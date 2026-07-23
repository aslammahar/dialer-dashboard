
@php
    $user = auth()->user();
@endphp
@extends('layouts.admin')

@section('content')
    <h1 class="mb-4">Responses for Campaign: {{ $our_campaign->name }}</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <button class="btn btn-danger mb-3" onclick="window.history.back();">Back</button>

    @if ($responses->isEmpty())
        <div class="alert alert-warning">No responses found for this campaign.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Submitted Data (First Few Fields)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($responses as $response)
                        <tr>
                            <td>{{ $response->id }}</td>
                            <td>
                                @if (is_array($response->submission_data))
                                    <ul class="list-unstyled mb-0">
                                        @foreach (array_slice($response->submission_data, 0, 3) as $key => $value)
                                            <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">No data submitted for this response.</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('our_campaigns.responses.show', [$our_campaign, $response]) }}" class="btn btn-sm btn-info">View</a>

                                    @can('compaign qa edit')
                                    <a href="{{ route('our_campaigns.responses.edit', [$our_campaign, $response]) }}" class="btn btn-sm btn-primary">QA Edit</a>
                                    @endcan


                                    @can('compaign admin edit')
                                    <a href="{{ route('our_campaigns.responses.admin_edit', [$our_campaign, $response]) }}" class="btn btn-sm btn-primary">Admin Edit</a>
                                    @endcan

                                    @if ($user->type === 'client')
                                    <a href="{{ route('our_campaigns.responses.client_edit', [$our_campaign, $response]) }}" class="btn btn-sm btn-primary">Client Edit</a>
                                    @endif
                                    @can('compaign closer edit')
                                    <a href="{{ route('our_campaigns.responses.closer_edit', [$our_campaign, $response]) }}" class="btn btn-sm btn-primary">CLoser Edit</a>
                                    @endcan


                                    @can('compaign delete button')
                                    <form action="{{ route('our_campaigns.responses.destroy', [$our_campaign, $response]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                    @endcan


                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $responses->links() }} {{-- Pagination links --}}
    @endif
@endsection
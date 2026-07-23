@extends('layouts.admin')
@section('content')

<style>
    .table-container {
        max-width: 900px;
        margin: auto;
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }
    h1 {
        text-align: center;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
    }
    .table {
        border-radius: 10px;
        overflow: hidden;
    }
    .table th {
        background-color: #4285f4;
        color: white;
        text-align: center;
        padding: 12px;
        font-size: 16px;
    }
    .table td {
        padding: 10px;
        font-size: 15px;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f8f9fa;
    }
    .btn-back {
        display: block;
        width: 200px;
        margin: 20px auto;
        background-color: #6c757d;
        color: white;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
        transition: 0.3s ease-in-out;
    }
    .btn-back:hover {
        background-color: #5a6268;
        text-decoration: none;
    }
</style>

<div class="table-container">
    <h1>Response Details</h1>

    @if ($campaign_response->submission_data)
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Field Name</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($campaign_response->submission_data as $key => $value)
                        @php
                            // Convert underscores to spaces for better readability
                            $searchKey = str_replace('_', ' ', $key);
                            $formField = $formFields->where('name', $searchKey)->first();
                            
                            if ($formField) {
                                $showTo = explode(',', $formField->show_to);
                                $userRole = auth()->user()->type;
                                $canView = in_array(trim($userRole), array_map('trim', $showTo));
                            }
                        @endphp

                        @if ($formField && $canView)
                            <tr>
                                <td><strong>{{ $formField->label }}</strong></td>
                                <td>{{ $value }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning text-center">No data submitted for this response.</div>
    @endif

    <a href="{{ route('our_campaigns.responses.index', $campaign_response->campaign->id) }}" class="btn-back">⬅ Back to Responses</a>
</div>

@endsection

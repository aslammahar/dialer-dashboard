@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Carriers List</h2>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('carriers.index') }}" class="mb-4">
        <div class="form-group">
            <label for="state">Filter by State</label>
            <select name="state" id="state" class="form-control" required>
                <option value="">Select State</option>
                @foreach($states as $state)
                    <option value="{{ $state }}" {{ request('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('carriers.index') }}" class="btn btn-secondary">Reset Filter</a>
    </form>

    <!-- Carriers Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Licensed Agent Name</th>
                <th>Licensed Agency</th>
                <th>State</th>
                <th>Carriers</th>
            </tr>
        </thead>
        <tbody>
    @forelse($carriers as $carrier)
    <tr>
        <td>{{ $carrier->licensed_agent_name }}</td>
        <td>{{ is_array(json_decode($carrier->licensed_agency, true)) ? implode(', ', json_decode($carrier->licensed_agency, true)) : $carrier->licensed_agency }}</td>

        <!-- Display all states when no filter is applied, or only the selected state if a filter is applied -->
        <td>
            @php
                $statesArray = json_decode($carrier->state, true);
            @endphp

            @if(request('state')) <!-- If a filter is applied -->
                {{ in_array(request('state'), $statesArray) ? request('state') : '' }}
            @else <!-- If no filter is applied -->
                {{ implode(', ', $statesArray) }} <!-- Show all states -->
            @endif
        </td>

        <td>{{ is_array(json_decode($carrier->carriers, true)) ? implode(', ', json_decode($carrier->carriers, true)) : $carrier->carriers }}</td>
    </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center">No carriers found for the selected state.</td>
        </tr>
    @endforelse
</tbody>

    </table>
</div>
@endsection
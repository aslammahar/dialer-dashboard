@extends('layouts.admin')

@section('page-title')
    {{ __('Leaderboard') }}
@endsection

@section('content')

<br>


<a href="{{ route('leaderboard.daily') }}" class="link-button">Daily Leads</a><br>
<a href="{{ route('my-voice-leads') }}" class="link-button">My Voice Leads</a><br><br>


<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>{{ $sectionTitle }}</h2>
        
    </div>

    <div class="table-responsive">
        <table class="table table-bordered leaderboard-table">
            <thead>
                <tr class="table-header">
                    <th class="header__item">User</th>
                    <th class="header__item">Daily</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Sort the leaderboard array in descending order based on leads_count
                    $sortedLeaderboard = $leaderboard->sortByDesc('leads_count');
                @endphp

                @foreach ($sortedLeaderboard as $user)
                    <tr class="table-row">
                        <td class="table-data">{{ $user->name }}</td>
                        <td class="table-data">{{ $user->leads_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

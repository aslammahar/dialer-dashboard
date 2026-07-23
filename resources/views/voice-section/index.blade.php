



<!-- resources/views/leaderboard/index.blade.php -->



<!-- resources/views/leaderboard/index.blade.php -->

@extends('layouts.admin')


@section('title', 'Voice Section')


@section ('content' )
<br>


<a href="{{ route('leaderboard.daily') }}" class="link-button">Daily Leads</a><br>
<a href="{{ route('leaderboard.monthly') }}" class="link-button">Monthly Leads</a><br>
<a href="{{ route('my-voice-leads') }}" class="link-button">My Voice Leads</a><br><br>
<a href="imported-leads"> My Voice Leads</a>




@endsection



@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Edit Avatar QA Lead</h1>

    <form method="POST" action="{{ route('avatar-qa-leads.update', ['lead' => $lead->id]) }}">
        @csrf
        @method('PATCH')

        <!-- Editable fields -->
        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="{{ $lead->phone_number }}">
        </div>

        <div class="form-group">
            <label for="dialer_id">Dialer ID:</label>
            <input type="text" id="dialer_id" name="dialer_id" value="{{ $lead->dialer_id }}">
        </div>

        <div class="form-group">
            <label for="verifiers">Verifiers:</label>
            <input type="text" id="verifiers" name="verifiers" value="{{ $lead->verifiers }}">
        </div>

        <!-- Add more fields for other columns as needed -->

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection

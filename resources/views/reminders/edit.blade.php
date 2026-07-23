@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Edit Reminder</h1>

    <form action="{{ route('reminders.update', $reminder) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                   id="title" name="title" value="{{ old('title', $reminder->title) }}" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">
                {{ old('description', $reminder->description) }}
            </textarea>
        </div>

        <div class="form-group">
            <label for="reminder_time">Reminder Time</label>
            <input type="datetime-local" 
                   class="form-control @error('reminder_time') is-invalid @enderror" 
                   id="reminder_time" 
                   name="reminder_time" 
                   value="{{ old('reminder_time', $defaultDateTime) }}" 
                   required>
            @error('reminder_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Timezone Selection -->
        <div class="form-group">
            <label for="timezone">Timezone</label>
            <select class="form-control @error('timezone') is-invalid @enderror" 
                    id="timezone" name="timezone" required>
                <option value="Asia/Karachi" {{ old('timezone', $reminder->timezone) === 'Asia/Karachi' ? 'selected' : '' }}>Pakistan Time (UTC+05:00)</option>
                <option value="America/New_York" {{ old('timezone', $reminder->timezone) === 'America/New_York' ? 'selected' : '' }}>American Time (UTC-05:00)</option>
            </select>
            @error('timezone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Reminder</button>
    </form>
</div>
@endsection
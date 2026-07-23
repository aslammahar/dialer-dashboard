<!-- resources/views/assign-schedule.blade.php -->
@extends('layouts.admin')

@section('content')
   

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Filter Form -->
                    <form action="{{ route('assign.schedule.form') }}" method="GET" class="mb-3">
                        <div class="form-group">
                            <label for="type">Filter by User Type</label>
                            <select name="type" id="type" class="form-control" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                @foreach($types as $typeOption)
                                    <option value="{{ $typeOption }}" {{ request('type') === $typeOption ? 'selected' : '' }}>
                                        {{ ucfirst($typeOption) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <!-- Main Form -->
                    <form action="{{ route('assign.schedule') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="schedule_id">Select Schedule</label>
                            <select name="schedule_id" id="schedule_id" class="form-control">
                                <option value="">Select Schedule</option>
                                @foreach($schedules as $schedule)
                                    <option value="{{ $schedule->id }}">{{ $schedule->slug }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="users">Select Users</label>
                            <div>
                                @foreach($users as $user)
                                    <div class="form-check">
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input" id="user{{ $user->id }}">
                                        <label class="form-check-label" for="user{{ $user->id }}">{{ $user->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Assign Schedule</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

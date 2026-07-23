@extends('layouts.admin')
@section('page-title')
{{ __('Dashboard') }}
@endsection
@push('script-page')
<script>
    (function() {
        var etitle;
        var etype;
        var etypeclass;
        var calendar = new FullCalendar.Calendar(document.getElementById('event_calendar'), {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridDay,timeGridWeek,dayGridMonth'
            },
            buttonText: {
                timeGridDay: "{{ __('Day') }}",
                timeGridWeek: "{{ __('Week') }}",
                dayGridMonth: "{{ __('Month') }}"
            },
            themeSystem: 'bootstrap',
            navLinks: true,
            droppable: true,
            selectable: true,
            selectMirror: true,
            editable: true,
            dayMaxEvents: true,
            handleWindowResize: true,
            events: {
                !!json_encode($arrEvents) !!
            },
            locale: '{{ basename(App::getLocale()) }}',
            dayClick: function(e) {
                var t = moment(e).toISOString();
                $("#new-event").modal("show"), $(".new-event--title").val(""), $(".new-event--start")
                    .val(t), $(".new-event--end").val(t)
            },
            eventResize: function(event) {
                var eventObj = {
                    start: event.start.format(),
                    end: event.end.format(),
                };
            },
            viewRender: function(t) {
                e.fullCalendar("getDate").month(), $(".fullcalendar-title").html(t.title)
            },
            eventClick: function(e, t) {
                var title = e.title;
                var url = e.url;

                if (typeof url != 'undefined') {
                    $("#commonModal .modal-title").html(title);
                    $("#commonModal .modal-dialog").addClass('modal-md');
                    $("#commonModal").modal('show');
                    $.get(url, {}, function(data) {
                        $('#commonModal .modal-body').html(data);
                    });
                    return false;
                }
            }
        });
        calendar.render();
    })();
</script>
@endpush
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('HRM') }}</li>
@endsection
@push('css-page')
<style>
    .dashboard-quick-link {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }
    .dashboard-quick-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.35rem 1rem rgba(0, 0, 0, 0.08);
    }
    .dashboard-quick-link .theme-avtar {
        width: 48px;
        height: 48px;
    }
</style>
@endpush
@section('content')
<br>






@include('dashboard.partials.quick-links')









<div class="card ">
    <div class="card-header">
        <h4>{{ __('Warnings') }}</h4>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('Warning By') }}</th>
                <th>{{ __('Warning To') }}</th>
                <th>{{ __('Subject') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Warning Date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($warnings as $warning)
            <tr>
                <td>{{ $warning->employeeBy->name ?? 'N/A' }}</td>
                <td>{{ $warning->employeeTo->name ?? 'N/A' }}</td>
                <td>{{ $warning->subject }}</td>
                <td>{{ $warning->description }}</td>
                <td>{{ $warning->warning_date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>










@if (\Auth::user()->type != 'client' && \Auth::user()->type != 'company')
<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-xxl-6">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Mark Attendance') }}</h4>
                    </div>
                    <div class="card-body dash-card-body">
                        <p class="text-muted pb-0-5">{{ __('My Office Time: ' . $officeTime['startTime'] . ' to ' . $officeTime['endTime']) }}</p>
                        <center>
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Clock In/Out Button for Avatar and Voice Users -->
                                    {{ Form::open(['url' => 'wfh/attendance', 'method' => 'post']) }}
                                    @if(Auth::user()->type == 'Avatar' || Auth::user()->type == 'voice')
                                    @if(empty($employeeAttendance) || (!empty($employeeAttendance) && $employeeAttendance->clock_out != '00:00:00'))
                                    <!-- Show CLOCK IN button if clock_out is not '00:00:00' -->
                                    <button type="submit" value="0" name="in" id="clock_in" class="btn btn-success">
                                        {{ __('CLOCK IN') }}
                                    </button>
                                    @else
                                    <!-- Show CLOCK OUT button if clock_out is '00:00:00' -->
                                    <button type="submit" value="1" name="out" id="clock_out" class="btn btn-danger">
                                        {{ __('CLOCK OUT') }}
                                    </button>
                                    @endif
                                    @endif
                                    {{ Form::close() }}
                                </div>

                                <div class="col-md-6 text-center">
                                    <!-- Clock In/Out Button for Other Users (Non-Avatar/Voice) -->
                                    {{ Form::open(['url' => 'attendanceemployee/attendance', 'method' => 'post']) }}
                                    @if(Auth::user()->type != 'Avatar' && Auth::user()->type != 'voice')
                                    <!-- Always display both buttons -->
                                    <div class="d-flex justify-content-between">
                                        <!-- Clock In Button -->
                                        <button type="submit" value="0" name="in" id="clock_in" class="btn btn-primary me-2">
                                            {{ __('CLOCK IN ') }}
                                        </button>

                                        <!-- Clock Out Button -->
                                        <button type="submit" value="1" name="out" id="clock_out" class="btn btn-danger">
                                            {{ __('CLOCK OUT') }}
                                        </button>
                                    </div>
                                    @endif
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </center>
                    </div>

                </div>

<div class="card shadow-sm border-0">
    <div class="card-header text-center bg-info text-white">
        <h4 class="mb-0">{{ __('Monitoring Notifications') }}</h4>
    </div>
    <a href="{{ route('monitoring.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-eye"></i> View All
    </a>
    <div class="card-body dash-card-body p-3">
        <!-- Check if monitoring has more than 3 records -->
        <div class="{{ $monitoring->count() > 3 ? 'overflow-auto' : 'overflow-hidden' }}" style="max-height: {{ $monitoring->count() > 3 ? '200px' : 'unset' }};">
            @if($monitoring->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="bi bi-bell-slash-fill"></i> No new notifications for you.
                </div>
            @else
                <ul class="list-group">
                    @foreach($monitoring as $item)
                        <li class="list-group-item d-flex align-items-start border-light mb-2 shadow-sm">
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1">Monitoring ID: {{ $item->id }}</h5>
                                <p class="mb-0">
                                    <i class="bi bi-person-fill"></i> Employee ID: {{ $item->employee_id }}
                                </p>
                                <p class="mb-0">
                                    <i class="bi bi-calendar-check-fill"></i> Monitoring Date: 
                                    {{ \Carbon\Carbon::parse($item->monitor_date)->format('d M Y, h:i A') }}
                                </p>
                                <p class="mb-0">
                                    <i class="bi bi-check-circle-fill"></i> Score: {{ $item->score }}
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('monitoring.show', $item->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>



<div class="card shadow-sm border-0">
    <div class="card-header text-center bg-info text-white">
        <h4 class="mb-0">{{ __('Avatar Notifications') }}</h4>
    </div>
    <a href="{{ route('avatar_monitoring.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-eye"></i> View All
    </a>
    <div class="card-body dash-card-body p-3">
        <!-- Check if notifications has more than 3 records -->
        <div class="{{ $notifications->count() > 3 ? 'overflow-auto' : 'overflow-hidden' }}" style="max-height: {{ $notifications->count() > 3 ? '200px' : 'unset' }};">
            @if($notifications->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="bi bi-bell-slash-fill"></i> No new notifications for you.
                </div>
            @else
                <ul class="list-group">
                    @foreach($notifications as $notification)
                        <li class="list-group-item d-flex align-items-start border-light mb-2 shadow-sm">
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1">{{ $notification->employee->name ?? __('N/A') }}</h5>
                                <p class="mb-0">
                                    <i class="bi bi-person-fill"></i> Employee ID: {{ $notification->employee_id }}
                                </p>
                                <p class="mb-0">
                                    <i class="bi bi-calendar-check-fill"></i> Notification Time: 
                                    {{ \Carbon\Carbon::parse($notification->created_at)->format('d M Y, h:i A') }}
                                </p>
                                <p class="mb-0">
                                    <i class="bi bi-check-circle-fill"></i> Status: {{ $notification->status ?? 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('avatar_monitoring.show', $notification->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>





<div class="card shadow-sm border-0">
    <div class="card-header text-center bg-success text-white">
        <h4 class="mb-0">{{ __('Interview Scheduled') }}</h4>
    </div>
    
    <div class="card-body dash-card-body p-3">
        <div class="overflow-hidden">
            @if($interviews->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="bi bi-calendar-x-fill"></i> No interviews scheduled for you.
                </div>
            @else
                <ul class="list-group">
                    @foreach($interviews as $interview)
                        @if(!in_array($interview->status, ['hired', 'rejected']) && (\Carbon\Carbon::parse($interview->date)->isFuture() || \Carbon\Carbon::parse($interview->date)->isToday() || \Carbon\Carbon::parse($interview->date)->isYesterday()))
                            <li class="list-group-item d-flex align-items-start border-light mb-2 shadow-sm">
                                <div class="flex-grow-1">
                                   
                                    <h5 class="fw-bold mb-1">{{ $interview->name }}</h5>
                                    <p class="mb-0">
                                        <i class="bi bi-briefcase-fill"></i> Designation: {{ $interview->designation }}
                                    </p>
                                    <p class="mb-0">
                                        <i class="bi bi-briefcase-fill"></i> Status: {{$interview->status}}
                                    </p>
                                    <p class="mb-0">
                                        <i class="bi bi-calendar-check-fill"></i> Interview Date: 
                                        {{ \Carbon\Carbon::parse($interview->date)->format('d M Y, h:i A') }}
                                    </p>
                                </div>
                                <div>
                                    <a href="{{ route('recruitments.final', $interview->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> Take Interview
                                    </a>
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>




                <div class="card ">
                    <div class="card-header">
                        <h4>{{ __('Event View') }}</h4>
                    </div>
                    <div class="card-body dash-card-body">
                        <div class="overflow-hidden widget-calendar">
                            <div class="calendar e-height" data-toggle="event_calendar" id="event_calendar">
                            </div>
                        </div>
                    </div>
                </div>


              
            </div>
            <div class="col-xxl-6">
                <div class="card list_card">
                    <div class="card-header">
                        <h4>{{ __('Announcement List') }}</h4>
                    </div>
                    <div class="card-body dash-card-body">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Start Date') }}</th>
                                        <th>{{ __('End Date') }}</th>
                                        <th>{{ __('description') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($announcements as $announcement)
                                    <tr>
                                        <td>{{ $announcement->title }}</td>
                                        <td>{{ \Auth::user()->dateFormat($announcement->start_date) }}</td>
                                        <td>{{ \Auth::user()->dateFormat($announcement->end_date) }}</td>
                                        <td>{{ $announcement->description }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-center">
                                                <h6>{{ __('There is no Announcement List') }}</h6>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card list_card">
                    <div class="card-header">
                        <h4>{{ __('Meeting List') }}</h4>
                    </div>
                    <div class="card-body dash-card-body">
                        @if (count($meetings) > 0)
                        <div class="table-responsive">

                            <table class="table align-items-center">

                                <thead>
                                    <tr>
                                        <th>{{ __('Meeting title') }}</th>
                                        <th>{{ __('Meeting Date') }}</th>
                                        <th>{{ __('Meeting Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($meetings as $meeting)
                                    <tr>
                                        <td>{{ $meeting->title }}</td>
                                        <td>{{ \Auth::user()->dateFormat($meeting->date) }}</td>
                                        <td>{{ \Auth::user()->timeFormat($meeting->time) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                        @else
                        <div class="p-2">
                            {{ __('No meeting scheduled yet.') }}
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header">
                <h5>{{ __("Today's Not Clock In") }}</h5>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-12">
                        <div class="row g-3 flex-nowrap team-lists horizontal-scroll-cards">
                            @foreach ($notClockIns as $notClockIn)

                            <div class="col-auto">
                                <img src="{{ !empty($notClockIn->user) ? $notClockIn->user->profile : asset(Storage::url('uploads/avatar/avatar.png')) }}"
                                    alt="">

                                <p class="mt-2">{{ $notClockIn->name }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-sm-12">
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Event') }}</h5>
                    </div>
                    <div class="card-body">
                        <div id='event_calendar' class='calendar'></div>
                    </div>
                </div>

            </div>
            <div class="col-md-3">
                <div class="col-xxl-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>{{ __('Staff') }}</h5>
                            <div class="row  mt-4">
                                <div class="col-md-6 col-sm-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="theme-avtar bg-primary">
                                            <i class="ti ti-users"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{ __('Total Staff') }}</p>
                                            <h4 class="mb-0 text-success">{{ $countUser + $countClient }}</h4>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 my-3 my-sm-0">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="theme-avtar bg-info">
                                            <i class="ti ti-user"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{ __('Employee') }}</p>
                                            <h4 class="mb-0 text-primary">{{ $countUser }}</h4>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="theme-avtar bg-danger">
                                            <i class="ti ti-user"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{ __('Client') }}</p>
                                            <h4 class="mb-0 text-danger">{{ $countClient }}</h4>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>{{ __('Job') }}</h5>
                            <div class="row  mt-4">
                                <div class="col-md-6 col-sm-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="theme-avtar bg-primary">
                                            <i class="ti ti-award"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{ __('Total Jobs') }}</p>
                                            <h4 class="mb-0 text-success">{{ $activeJob + $inActiveJOb }}</h4>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 my-3 my-sm-0">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="theme-avtar bg-info">
                                            <i class="ti ti-check"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{ __('Active Job') }}</p>
                                            <h4 class="mb-0 text-primary">{{ $activeJob }}</h4>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="theme-avtar bg-danger">
                                            <i class="ti ti-x"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{ __('Inactive Job ') }}</p>
                                            <h4 class="mb-0 text-danger">{{ $inActiveJOb }}</h4>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>{{ __('Training') }}</h5>
                            <div class="row  mt-4">
                                <div class="col-md-6 col-sm-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="theme-avtar bg-primary">
                                            <i class="ti ti-users"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{ __('Total Training') }}</p>
                                            <h4 class="mb-0 text-success">{{ $onGoingTraining + $doneTraining }}
                                            </h4>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 my-3 my-sm-0">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="theme-avtar bg-info">
                                            <i class="ti ti-user"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{ __('Trainer') }}</p>
                                            <h4 class="mb-0 text-primary">{{ $countTrainer }}</h4>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="theme-avtar bg-danger">
                                            <i class="ti ti-user-check"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{ __('Active Training') }}</p>
                                            <h4 class="mb-0 text-danger">{{ $onGoingTraining }}</h4>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="theme-avtar bg-secondary">
                                            <i class="ti ti-user-minus"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{ __('Done Training') }}</p>
                                            <h4 class="mb-0 text-secondary">{{ $doneTraining }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">

                        <h5>{{ __('Announcement List') }}</h5>
                    </div>
                    <div class="card-body" style="min-height: 295px;">
                        <div class="table-responsive">
                            @if (count($announcements) > 0)
                            <table class="table align-items-center">
                                <thead>
                                    <tr>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Start Date') }}</th>
                                        <th>{{ __('End Date') }}</th>

                                    </tr>
                                </thead>
                                <tbody class="list">
                                    @foreach ($announcements as $announcement)
                                    <tr>
                                        <td>{{ $announcement->title }}</td>
                                        <td>{{ \Auth::user()->dateFormat($announcement->start_date) }}</td>
                                        <td>{{ \Auth::user()->dateFormat($announcement->end_date) }}</td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <div class="p-2">
                                No accouncement present yet.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Meeting schedule') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @if (count($meetings) > 0)
                            <table class="table align-items-center">
                                <thead>
                                    <tr>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    @foreach ($meetings as $meeting)
                                    <tr>
                                        <td>{{ $meeting->title }}</td>
                                        <td>{{ \Auth::user()->dateFormat($meeting->date) }}</td>
                                        <td>{{ \Auth::user()->timeFormat($meeting->time) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <div class="p-2">
                                No meeting scheduled yet.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ sample-page ] end -->
</div>
@endif
@endsection
@extends('layouts.admin')

@section('title')
    {{ __('Dashboard')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a> <br><a href="manage-polocies">Manage polocies</a></li>
    
@endsection



@push('theme-script')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
@endpush

@push('script-page')


    <script>
            @if($calenderTasks)
            (function () {
                var etitle;
                var etype;
                var etypeclass;
                var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    themeSystem: 'bootstrap',
                    initialDate: '{{ $transdate }}',
                    slotDuration: '00:10:00',
                    navLinks: true,
                    droppable: true,
                    selectable: true,
                    selectMirror: true,
                    editable: true,
                    dayMaxEvents: true,
                    handleWindowResize: true,
                    events:{!! json_encode($calenderTasks) !!},

                });
                calendar.render();
            })();
        @endif

        $(document).on('click', '.fc-day-grid-event', function (e) {
            if (!$(this).hasClass('deal')) {
                e.preventDefault();
                var event = $(this);
                var title = $(this).find('.fc-content .fc-title').html();
                var size = 'md';
                var url = $(this).attr('href');
                $("#commonModal .modal-title").html(title);
                $("#commonModal .modal-dialog").addClass('modal-' + size);

                $.ajax({
                    url: url,
                    success: function (data) {
                        $('#commonModal .modal-body').html(data);
                        $("#commonModal").modal('show');
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        show_toastr('error', data.error, 'error')
                    }
                });
            }
        });



    </script>
    <script>


        (function () {
            var chartBarOptions = {
                series: {!! json_encode($taskData['dataset']) !!},


                chart: {
                    height: 250,
                    type: 'area',
                    // type: 'line',
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                title: {
                    text: '',
                    align: 'left'
                },
                xaxis: {
                    categories:{!! json_encode($taskData['label']) !!},
                    title: {
                        text: "{{__('Days')}}"
                    }
                },
                colors: ['#6fd944', '#883617','#4e37b9','#8f841b'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },
                // markers: {
                //     size: 4,
                //     colors: ['#3b6b1d', '#be7713' ,'#2037dc','#cbbb27'],
                //     opacity: 0.9,
                //     strokeWidth: 2,
                //     hover: {
                //         size: 7,
                //     }
                // },
                yaxis: {
                    title: {
                        text: "{{__('Amount')}}"
                    },

                }

            };
            var arChart = new ApexCharts(document.querySelector("#chart-sales"), chartBarOptions);
            arChart.render();
        })();



        (function () {
            var options = {
                chart: {
                    height: 140,
                    type: 'donut',
                },
                dataLabels: {
                    enabled: false,
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                        }
                    }
                },
                series: {!! json_encode(array_values($projectData)) !!},
                colors:["#bd9925", "#2f71bd", "#720d3a","#ef4917"],
                labels:   {!! json_encode($project_status) !!},
                legend: {
                    show: true
                }
            };
            var chart = new ApexCharts(document.querySelector("#chart-doughnut"), options);
            chart.render();
        })();
    </script>
@endpush

@section('content')
@php

  $project_task_percentage = $project['project_task_percentage'];
  $label='';
        if($project_task_percentage<=15){
            $label='bg-danger';
        }else if ($project_task_percentage > 15 && $project_task_percentage <= 33) {
            $label='bg-warning';
        } else if ($project_task_percentage > 33 && $project_task_percentage <= 70) {
            $label='bg-primary';
        } else {
            $label='bg-success';
        }


  $project_percentage = $project['project_percentage'];
  $label1='';
        if($project_percentage<=15){
            $label1='bg-danger';
        }else if ($project_percentage > 15 && $project_percentage <= 33) {
            $label1='bg-warning';
        } else if ($project_percentage > 33 && $project_percentage <= 70) {
            $label1='bg-primary';
        } else {
            $label1='bg-success';
        }

  $project_bug_percentage = $project['project_bug_percentage'];
  $label2='';
      if($project_bug_percentage<=15){
        $label2='bg-danger';
      }else if ($project_bug_percentage > 15 && $project_bug_percentage <= 33) {
        $label2='bg-warning';
      } else if ($project_bug_percentage > 33 && $project_bug_percentage <= 70) {
        $label2='bg-primary';
      } else {
        $label2='bg-success';
      }
@endphp

    <div class="row">
        @if(!empty($arrErr))
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                @if(!empty($arrErr['system']))
                    <div class="alert alert-danger text-xs">
                         {{ __('are required in') }} <a href="{{ route('settings') }}" class=""><u> {{ __('System Setting') }}</u></a>
                    </div>
                @endif
                @if(!empty($arrErr['user']))
                    <div class="alert alert-danger text-xs">
                         <a href="{{ route('users') }}" class=""><u>{{ __('here') }}</u></a>
                    </div>
                @endif
                @if(!empty($arrErr['role']))
                    <div class="alert alert-danger text-xs">
                         <a href="{{ route('roles.index') }}" class=""><u>{{ __('here') }}</u></a>
                    </div>
                @endif
            </div>
        @endif
    </div>

<div class="col-sm-12">
    <div class="row">
        <div class="col-xxl-6">
            <div class="row">
               
                        <div class="col-lg-4 col-md-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row align-items-center justify-content-between">
                                                    <div class="col-auto mb-3 mb-sm-0">
                                                        <div class="d-flex align-items-center">
                                                            <div class="theme-avtar bg-primary">
                                                                <i class="ti ti-list"></i>
                                                            </div>
                                                            <div class="ms-3">
                                                            <li
                                            class="dash-item {{ Request::segment(1) == 'form_builder' || Request::segment(1) == 'form_response' ? 'active open' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('client.index') }}">{{ __('Closed Calls') }}</a>
                                        </li>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                 
                                    <div class="col-lg-4 col-md-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row align-items-center justify-content-between">
                                                    <div class="col-auto mb-3 mb-sm-0">
                                                        <div class="d-flex align-items-center">
                                                            <div class="theme-avtar bg-primary">
                                                                <i class="ti ti-list"></i>
                                                            </div>
                                                            <div class="ms-3">
                                                            <li
                                            class="dash-item {{ Request::segment(1) == 'form_builder' || Request::segment(1) == 'form_response' ? 'active open' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('my.reports') }}">{{ __('CLient History Dashboard') }}</a>
                                        </li>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                
                
                                        <div class="col-xxl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('Calendar') }}</h5>
                        </div>
                        <div class="card-body">
                            <div id='calendar' class='calendar'></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    

</div>
@endsection

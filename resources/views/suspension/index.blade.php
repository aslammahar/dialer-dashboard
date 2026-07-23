@extends('layouts.admin')

@section('page-title')
    {{__('Manage Suspension')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Suspension')}}</li>
@endsection


@section('action-btn')
    <div class="float-end">
        @can('create suspension')
            <a href="#" data-url="{{ route('suspension.create') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Create New Suspension')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                @role('company')
                                <th>{{__('Employee Name')}}</th>
                                @endrole
                                <th>{{__('Start Date')}}</th>
                                <th>{{__('End Date')}}</th>
                                <th>{{__('Reason')}}</th>
                                @if(Gate::check('update suspension') || Gate::check('delete suspension'))
                                    <th>{{__('Action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody class="font-style">
                            @foreach ($suspensions as $suspension)
                                <tr>
                                    @role('company')
                                    @endrole
                                    <td>{{ !empty($suspension->user())?$suspension->user()->name:'' }}</td>

                                    <td>{{  \Auth::user()->dateFormat($suspension->start_date) }}</td>
                                    <td>{{  \Auth::user()->dateFormat($suspension->end_date) }}</td>
                                    <td>{{ $suspension->reason }}</td>

                                    
                                    @if(Gate::check('edit suspension') || Gate::check('delete suspension'))
                                        <td>

                                            @can('update suspension')
                                                <div class="action-btn bg-primary ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ URL::to('suspension/'.$suspension->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Termination')}}" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan

                                            @can('delete suspension')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['suspension.destroy', $suspension->id],'id'=>'delete-form-'.$suspension->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$suspension->id}}').submit();">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan

                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

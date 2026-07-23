@extends('layouts.admin')
@section('page-title')
    {{__('Manage Leads')}} @if($pipeline) - {{$pipeline->name}} @endif
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
@endpush
@push('script-page')
    <script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
    <script>
        $(document).on("change", ".change-pipeline select[name=default_pipeline_id]", function () {
            $('#change-pipeline').submit();
        });
    </script>
    <script type="text/javascript">

        $(document).ready(function () {
    $('#j_son_lead').DataTable({
        initComplete: function () {
            this.api()
                .columns()
                .every(function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
 
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
 
                    column
                        .data()
                        .unique()
                        .sort()
                        .each(function (d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        });
                });
        },
    });
});




    </script>
    <script src="{{asset('css/summernote/jquery-3.5.1.js')}}"></script>
    <script src="{{asset('css/summernote/dataTables.min.js')}}"></script>
    <script src="{{asset('css/summernote/dataTables.min.css')}}"></script>
    <script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Lead')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="{{ route('leads.index') }}" data-bs-toggle="tooltip" title="{{__('Kanban View')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-layout-grid"></i>
        </a>
        <a href="#" data-size="lg" data-url="{{ route('leads.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create New User')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')
    @if($pipeline)
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table id="j_son_lead" class="table datatables " style="width:100%">
                                <thead>
                                <tr>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Subject')}}</th>
                                    <th>{{__('Priority')}}</th>
                                    <th>{{__('Stage')}}</th>
                                    <th>{{__('Group')}}</th>
                                    <th>{{__('Users')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>


        <tfoot style="
                    display: table-header-group;">
            <tr>
                <th>{{__('Name')}}</th> 
                <th>{{__('Subject')}}</th>              
                <th>{{__('Priority')}}</th>
                <th>{{__('Stage')}}</th>
                <th>{{__('Group')}}</th>          
            </tr>
        </tfoot>







                                <tbody>
                                @if(count($leads) > 0)
                                    @foreach ($leads as $lead)
                                        <tr>
                                            <td>{{ $lead->name }}</td>
                                            <td>{{ $lead->subject }}</td>                     
                                            <td>{{  !empty($lead->priority)?$lead->priority->name:'-' }}</td>
                                            <td>{{  !empty($lead->stage)?$lead->stage->name:'-' }}</td>
                                            <td>{{  !empty($lead->group)?$lead->group->name:'-' }}</td>
                                            <td>
                                                @foreach($lead->users as $user)
                                                    <a href="#" class="btn btn-sm mr-1 p-0 rounded-circle">
                                                        <img alt="image" data-toggle="tooltip" data-original-title="{{$user->name}}" @if($user->avatar) src="{{asset('/storage/uploads/avatar/'.$user->avatar)}}" @else src="{{asset('/storage/uploads/avatar/avatar.png')}}" @endif class="rounded-circle " width="25" height="25">
                                                    </a>
                                                @endforeach
                                            </td>
                                            @if(Auth::user()->type != 'client')
                                                <td class="Action">
                                                    <span>
                                                    @can('view lead')
                                                            @if($lead->is_active)
                                                                <div class="action-btn bg-warning ms-2">
                                                                <a href="{{route('leads.show',$lead->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center"  data-size="xl" data-bs-toggle="tooltip" title="{{__('View')}}" data-title="{{__('Lead Detail')}}">
                                                                    <i class="ti ti-eye text-white"></i>
                                                                </a>
                                                            </div>
                                                            @endif
                                                        @endcan
                                                        @can('edit lead')
                                                            <div class="action-btn bg-info ms-2">
                                                                <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-url="{{ route('leads.edit',$lead->id) }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Lead Edit')}}">
                                                                    <i class="ti ti-pencil text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('delete lead')
                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['leads.destroy', $lead->id],'id'=>'delete-form-'.$lead->id]) !!}
                                                                <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" ><i class="ti ti-trash text-white"></i></a>

                                                                {!! Form::close() !!}
                                                             </div>

                                                        @endif
                                                    </span>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="font-style">
                                        <td colspan="6" class="text-center">{{ __('No data available in table') }}</td>
                                    </tr>
                                @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

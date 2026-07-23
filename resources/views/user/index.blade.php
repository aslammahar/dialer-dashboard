@extends('layouts.admin')
    @php
    // $profile=asset(Storage::url('uploads/avatar/'));
        $profile=\App\Models\Utility::get_file('uploads/avatar');
    @endphp
    @section('page-title')
        {{('Manage User')}}
    @endsection
    @push('script-page')

    @endpush
    @section('breadcrumb')
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{('Dashboard')}}</a></li>
        <li class="breadcrumb-item">{{('User')}}</li>
        
    @endsection
    
           <input class="d-none" id="header-search-field" type="text" placeholder="{{('Search by name, email or type')}}"> -
    @section('action-btn')
        <div class="float-end">
            <a href="#" data-size="lg" data-url="{{ route('users.create') }}" data-ajax-popup="true"  data-bs-toggle="tooltip" title="{{('Create')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        </div>
    @endsection
    
   @section('content')
     
    
    <div class="row">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                <tr >
                                    <th>{{('Name')}}</th>
                                    <th>{{('Email')}}</th>
                                    <th>{{('Center')}}</th>
                                    <th>{{('Type')}}</th>
                                    <th>{{('Last Login')}}</th>
                                    <th>{{('WFH')}}</th>
                                    <th>{{('Actions')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->center->center_name ?? 'N/A' }}</td> 
                                        <td>{{ ucfirst($user->type) }}</td>
                                        <td>{{ $user->last_login_at ?: __('N/A') }}</td>
                                        <td>
                                            @if($user->is_wfh)
                                                <span class="badge bg-info">{{ __('WFH') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Onsite') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @can('edit user')
                                                    <a href="#!" data-size="lg" data-url="{{ route('users.edit',$user->id) }}" data-ajax-popup="true" class="dropdown-item" data-bs-original-title="{{('Edit User')}}">
                                                                <i class="ti ti-pencil"></i>
                                                                <span>{{('Edit')}}</span>
                                                    </a>
                                                @endcan
                                                @can('delete user')
                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">{{('Delete')}}</button>
                                                    </form>
                                                @endcan
                                                <a href="#!" data-url="{{route('users.reset',\Crypt::encrypt($user->id))}}" data-ajax-popup="true" data-size="md" class="dropdown-item" data-bs-original-title="{{('Reset Password')}}">
                                                    <i class="ti ti-adjustments"></i>
                                                    <span>  {{('Reset Password')}}</span>
                                                </a>
                                                <form action="{{ route('users.reactivate', $user->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item" data-bs-original-title="{{('Re-activate User')}}">
                                                        <i class="ti ti-refresh"></i>
                                                        <span>{{('Re-activate')}}</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 

    @endsection
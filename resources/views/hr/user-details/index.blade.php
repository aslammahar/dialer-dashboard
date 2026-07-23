@extends('layouts.admin')

@section('page-title')
    {{__('User Details - HR View')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('User Details')}}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5>{{__('All Users Details')}}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{__('Employee ID')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Email')}}</th>
                                <th>{{__('Phone')}}</th>
                                <th>{{__('Designation')}}</th>
                                <th>{{__('Details Status')}}</th>
                                <th>{{__('Bank Status')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                @if($user->userDetail)
                                    <tr>
                                        <td>{{ $user->userDetail->employee_id ?? '-' }}</td>
                                        <td>{{ $user->userDetail->full_name ?? $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->userDetail->phone ?? '-' }}</td>
                                        <td>{{ $user->userDetail->designation ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-success">Complete</span>
                                        </td>
                                        <td>
                                            @php
                                                $totalBanks = $user->bankDetails->count();
                                                $verifiedBanks = $user->bankDetails->where('status', 'verified')->count();
                                            @endphp
                                            <span class="badge bg-info">{{ $verifiedBanks }}/{{ $totalBanks }} Verified</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('hr.user.details.show', $user->id) }}" class="btn btn-sm btn-primary">
                                                <i class="ti ti-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">{{__('No user details found')}}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
               <div class="mt-3">
    {{ $users->links('pagination::bootstrap-4') }}
</div>
            </div>
        </div>
    </div>
</div>
@endsection
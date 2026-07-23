@extends('layouts.admin')

@section('page-title')
    {{ __('Child Clients for ') . $client->name }}
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
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Type') }}</th>  {{-- Added Type column --}}
                                    <th>{{ __('Last Login') }}</th> {{-- Added Last Login column --}}
                                    <th>{{ __('Actions') }}</th> {{-- Added Actions column --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($children as $child)
                                    <tr>
                                        <td>{{ $child->name }}</td>
                                        <td>{{ $child->email }}</td>
                                        <td>{{ ucfirst($child->type ?? 'N/A') }}</td>  {{-- Display Type or N/A --}}
                                        <td>{{ $child->last_login_at ?? 'N/A' }}</td> {{-- Display Last Login or N/A --}}
                                        <td>
                                            <div class="btn-group">
                                                <a href="#!" data-size="lg"
                                                    data-url="{{ route('users.edit', $child->id) }}"  {{-- Corrected route --}}
                                                    data-ajax-popup="true" class="dropdown-item"
                                                    data-bs-original-title="{{ __('Edit User') }}">
                                                    <i class="ti ti-pencil"></i>
                                                    <span>{{ __('Edit') }}</span>
                                                </a>

                                                <form action="{{ route('users.destroy', $child->id) }}" method="POST">  {{-- Corrected route --}}
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                                </form>

                                                <a href="#!"
                                                    data-url="{{ route('users.reset', \Crypt::encrypt($child->id)) }}"  {{-- Corrected route --}}
                                                    data-ajax-popup="true" data-size="md" class="dropdown-item"
                                                    data-bs-original-title="{{ __('Reset Password') }}">
                                                    <i class="ti ti-adjustments"></i>
                                                    <span> {{ __('Reset Password') }}</span>
                                                </a>
                                                <form action="{{ route('clients.reactivate', $child->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item" data-bs-original-title="{{('Re-activate Client')}}">
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
@endsection
@extends('layouts.admin')

@section('page-title', __('Centers'))

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>{{ __('Centers') }}</h5>
                <a href="{{ route('centers.create') }}" class="btn btn-sm btn-primary">
                    <i class="ti ti-plus"></i> {{ __('Add Center') }}
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Center Name') }}</th>
                                <th>{{ __('Description') }}</th>
                                
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($centers as $key => $center)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $center->center_name }}</td>
                                <td>{{ $center->description ?? '-' }}</td>
                                
                                <td>
                                    <a href="{{ route('centers.edit', $center->id) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <form action="{{ route('centers.destroy', $center->id) }}" 
                                          method="POST" style="display:inline-block;"
                                          onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __('No centers found.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
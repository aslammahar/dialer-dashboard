@extends('layouts.admin')

@section('page-title', __('Edit Center'))

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>{{ __('Edit Center') }}</h5>
                <a href="{{ route('centers.index') }}" class="btn btn-sm btn-secondary">
                    {{ __('Back') }}
                </a>
            </div>
            <div class="card-body">
                {{ Form::open(['route' => ['centers.update', $center->id], 'method' => 'put']) }}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('center_name', __('Center Name'), ['class' => 'form-label']) }}
                            {{ Form::text('center_name', $center->center_name, ['class' => 'form-control', 'required' => 'required']) }}
                            @error('center_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                            {{ Form::textarea('description', $center->description, ['class' => 'form-control', 'rows' => 3]) }}
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    <a href="{{ route('centers.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection
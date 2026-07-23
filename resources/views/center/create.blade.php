@extends('layouts.admin')

@section('page-title', __('Create Center'))

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>{{ __('Create Center') }}</h5>
                <a href="{{ route('centers.index') }}" class="btn btn-sm btn-secondary">
                    {{ __('Back') }}
                </a>
            </div>
            <div class="card-body">
                {{ Form::open(['route' => 'centers.store', 'method' => 'post']) }}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('center_name', __('Center Name'), ['class' => 'form-label']) }}
                            {{ Form::text('center_name', null, ['class' => 'form-control', 'placeholder' => __('Enter Center Name'), 'required' => 'required']) }}
                            @error('center_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Description')]) }}
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                    <a href="{{ route('centers.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection
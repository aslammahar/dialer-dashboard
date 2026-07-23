{{Form::model($user,array('route' => array('users.update', $user->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('name',__('Name'),['class'=>'form-label']) }}
                {{Form::text('name',null,array('class'=>'form-control font-style','placeholder'=>__('Enter User Name')))}}
                @error('name')
                <small class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('email',__('Email'),['class'=>'form-label'])}}
                {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter User Email')))}}
                @error('email')
                <small class="invalid-email" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div>
        @if(\Auth::user()->type != 'super admin')
            <div class="form-group col-md-6">
                {{ Form::label('role', __('User Role'),['class'=>'form-label']) }}
                {!! Form::select('role', $roles, $user->roles,array('class' => 'form-control select2','required'=>'required')) !!}
                @error('role')
                <small class="invalid-role" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        @endif

        {{-- Center Dropdown (admins only). For others, center_id is fixed by tenancy. --}}
        @if(\Auth::user()->canBypassCenterScope())
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('center_id', __('Center'), ['class' => 'form-label']) }}
                    {!! Form::select('center_id', $centers->prepend('-- Select Center --', ''), $user->center_id, ['class' => 'form-control select2']) !!}
                    @error('center_id')
                    <small class="invalid-center_id" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </small>
                    @enderror
                </div>
            </div>
        @endif

        {{-- Work From Home toggle --}}
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('is_wfh', __('Work From Home'), ['class' => 'form-label']) }}
                <div class="form-check form-switch">
                    {{ Form::checkbox('is_wfh', 1, $user->is_wfh, ['class' => 'form-check-input', 'id' => 'is_wfh']) }}
                    {{ Form::label('is_wfh', __('Enable if user works from home'), ['class' => 'form-check-label']) }}
                </div>
            </div>
        </div>

        @if(!$customFields->isEmpty())
            <div class="col-md-6">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('customFields.formBuilder')
                </div>
            </div>
        @endif
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>

{{Form::close()}}
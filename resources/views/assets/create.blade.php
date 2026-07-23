{{ Form::open(array('url' => 'account-assets')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {{ Form::label('employee_id', __('Employee'),['class'=>'form-label']) }}
            {{ Form::select('employee_id[]', $employee, null, ['class' => 'form-control select2', 'id' => 'choices-multiple1', 'multiple']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Name'),['class'=>'form-label']) }}
            {{ Form::text('name', '', ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}
            {{ Form::number('amount', '', ['class' => 'form-control', 'required' => 'required', 'step' => '0.01']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('purchase_date', __('Purchase Date'),['class'=>'form-label']) }}
            {{ Form::date('purchase_date', '', ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('supported_date', __('Supported Date'),['class'=>'form-label']) }}
            {{ Form::date('supported_date', '', ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('serial_number', __('Serial Number'),['class'=>'form-label']) }}
            {{ Form::text('serial_number', '', ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('model', __('Model'),['class'=>'form-label']) }}
            {{ Form::text('model', '', ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('condition', __('condition'),['class'=>'form-label']) }}
            {{ Form::text('condition', '', ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('given_by', __('Given By'),['class'=>'form-label']) }}
            {{ Form::text('given_by', '', ['class' => 'form-control']) }}
        </div>
      {{--  <div class="form-group col-md-6">
            {{ Form::label('given_date', __('Given Date'),['class'=>'form-label']) }}
            {{ Form::date('given_date', '', ['class' => 'form-control']) }}
        </div> ---}}
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {{ Form::textarea('description', '', ['class' => 'form-control', 'rows' => 3]) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}


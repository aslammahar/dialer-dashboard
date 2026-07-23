{{Form::open(array('url'=>'suspension','method'=>'post'))}}
<div class="modal-body">
    <div class="row">
    <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('suspended_by', __('Suspended By'),['class'=>'form-label'])}}
            {{ Form::select('suspended_by', $users,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('userid', __('Employee'),['class'=>'form-label'])}}
            {{ Form::select('userid', $users,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
        
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('start_date',__('Start Date'),['class'=>'form-label'])}}
            {{Form::date('start_date',null,array('class'=>'form-control '))}}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('end_date',__('End Date'),['class'=>'form-label'])}}
            {{Form::date('end_date',null,array('class'=>'form-control '))}}
        </div>
        <div class="form-group  col-lg-12">
            {{Form::label('reason',__('Reason'),['class'=>'form-label'])}}
            {{Form::textarea('reason',null,array('class'=>'form-control','placeholder'=>__('Enter Reason')))}}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}

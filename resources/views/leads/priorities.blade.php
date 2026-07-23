{{ Form::open(array('route' => ['leads.priorities.store',$lead->id])) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            <div class="row gutters-xs">
                @foreach ($priorities as $priority)
                    <div class="col-12 custom-control custom-checkbox mt-2 mb-2">
                        {{ Form::checkbox('priorities[]',$priority->id,(array_key_exists($priority->id,$selected))?true:false,['class' => 'form-check-input','id'=>'priorities_'.$priority->id]) }}
                        {{ Form::label('priorities'.$priority->id, ucfirst($priority->name),['class'=>'custom-control-label ml-4 text-white p-2 px-3 rounded badge bg-'.$priority->color]) }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>

{{Form::close()}}


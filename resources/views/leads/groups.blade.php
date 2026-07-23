{{ Form::open(array('route' => ['leads.groups.store',$lead->id])) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            <div class="row gutters-xs">
                @foreach ($groups as $group)
                    <div class="col-12 custom-control custom-checkbox mt-2 mb-2">
                        {{ Form::checkbox('groups[]',$group->id,(array_key_exists($group->id,$selected))?true:false,['class' => 'form-check-input','id'=>'groups_'.$group->id]) }}
                        {{ Form::label('groups'.$group->id, ucfirst($group->name),['class'=>'custom-control-label ml-4 text-white p-2 px-3 rounded badge bg-'.$group->color]) }}
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


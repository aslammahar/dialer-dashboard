{{ Form::open(array('url' => 'leads')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('subject', __('Dialer ID'),['class'=>'form-label']) }}
            {{ Form::text('subject', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('user_id', __('User'),['class'=>'form-label']) }}
            {{ Form::select('user_id', $users,null, array('class' => 'form-control select','required'=>'required')) }}
            @if(count($users) == 1)
                <div class="text-muted text-xs">
                    {{__('Please create new users')}} <a href="{{route('users.index')}}">{{__('here')}}</a>.
                </div>
            @endif
        </div>


        {{-- <div class="col-6 form-group">
            {{ Form::label('email', __('Email'),['class'=>'form-label']) }}
            {{ Form::email('email', null, array('class' => 'form-control','required'=>'required')) }}
        </div> --}}

        <div class="col-6 form-group">
            {{ Form::label('name', __(' Customer Name'),['class'=>'form-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
        </div>


        
         <div class="col-6 form-group">
            {{ Form::label('phone', __('Customer Phone'),['class'=>'form-label']) }}
            {{ Form::text('phone', null, array('class' => 'form-control','required'=>'required')) }}
        </div>


       


        <div class="col-6 form-group">    


        {{ Form::label('state', __('State'),['class'=>'form-label']) }}



        <div class="controls">


                                <select name="state"  required="" class="form-control">


                                <option value="" selected="" disabled="">Select State </option>

                                <option value="AK - Alaska">AK - Alaska</option>
                                <option value="AL - Alabama">AL - Alabama</option>

                                <option value="AR - Arkansas">AR - Arkansas</option>
                                
                                <option value="AZ - Arizona">AZ - Arizona</option>
                                
                                <option value="CA - California">CA - California</option>

                                <option value="CO - Colorado">CO - Colorado</option>

                                <option value="CT - Connecticut">CT - Connecticut</option>

                                <option value="DC - Dist. of Columbia">DC - Dist. of Columbia</option>

                                <option value="DE - Delaware">DE - Delaware</option>

                                
                                <option value="FL - Florida">FL - Florida</option>
                                <option value="GA - Georgia">GA - Georgia</option>
                                <option value="HI - Hawaii">HI - Hawaii</option>

                                <option value="IA - Iowa">IA - Iowa</option>


                                <option value="ID - Idaho">ID - Idaho</option>
                                <option value="IL - Illinois">IL - Illinois</option>
                                <option value="IN - Indiana">IN - Indiana</option>
                                
                                <option value="KS - Kansas">KS - Kansas</option>
                                <option value="KY - Kentucky">KY - Kentucky</option>
                                <option value="LA - Louisiana">LA - Louisiana</option>

                                <option value="MA - Massachusetts">MA - Massachusetts</option>

                                <option value="MD - Maryland">MD - Maryland</option>



                                <option value="ME - Maine">ME - Maine</option>
                                
                                
                                <option value="MI - Michigan">MI - Michigan</option>
                                <option value="MN - Minnesota">MN - Minnesota</option>

                                <option value="MO - Missouri">MO - Missouri</option>


                                <option value="MS - Mississippi">MS - Mississippi</option>
                                
                                <option value="MT - Montana">MT - Montana</option>

                                <option value="NC - North Carolina">NC - North Carolina</option>


                                <option value="ND - North Dakota">ND - North Dakota</option>


                                <option value="NE - Nebraska">NE - Nebraska</option>


                                <option value="NH - New Hampshire">NH - New Hampshire</option>


                                <option value="NJ - New Jersey">NJ - New Jersey</option>


                                <option value="NM - New Mexico">NM - New Mexico</option>



                                <option value="NV - Nevada">NV - Nevada</option>
                                <option value="NY - New York">NY - New York</option>
                                <option value="OH - Ohio">OH - Ohio</option>
                                <option value="OK - Oklahoma">OK - Oklahoma</option>
                                <option value="OR - Oregon">OR - Oregon</option>
                                <option value="PA - Pennsylvania">PA - Pennsylvania</option>
                                <option value="RI - Rhode Island">RI - Rhode Island</option>
                                <option value="SC - South Carolina">SC - South Carolina</option>
                                <option value="SD - South Dakota">SD - South Dakota</option>
                                <option value="TN - Tennessee">TN - Tennessee</option>
                                <option value="TX - Texas">TX - Texas</option>
                                <option value="UT - Utah">UT - Utah</option>

                                <option value="VA - Virginia">VA - Virginia</option>

                                <option value="VT - Vermont">VT - Vermont</option>
                                
                                <option value="WV - West Virginia">WV - West Virginia</option>

                                <option value="WA - Washington">WA - Washington</option>
                                <option value="WI - Wisconsin">WI - Wisconsin</option>


                                <option value="WY - Wyoming">WY - Wyoming</option>


                                <option value="Other">Other</option>


                    </select>



            </div>

    </div>






































         <div class="col-6 form-group">
            {{ Form::label('city', __('City'),['class'=>'form-label']) }}
            {{ Form::text('city', null, array('class' => 'form-control','required'=>'required')) }}
        </div>


        <div class="col-6 form-group">
            {{ Form::label('zip_code', __('Zip Code'),['class'=>'form-label']) }}
            {{ Form::text('zip_code', null, array('class' => 'form-control','required'=>'required')) }}
        </div>

        <div class="col-6 form-group">
            {{ Form::label('address', __('Address'),['class'=>'form-label']) }}
            {{ Form::text('address', null, array('class' => 'form-control','required'=>'required')) }}
        </div>

        <div class="col-6 form-group">
            {{ Form::label('age', __('Age '),['class'=>'form-label']) }}
            {{ Form::text('age', null, array('class' => 'form-control','required'=>'required')) }}
        </div>

        <div class="col-6 form-group">
            {{ Form::label('spouse_age', __('Spouse Age'),['class'=>'form-label']) }}
            {{ Form::text('spouse_age', null, array('class' => 'form-control','required'=>'required')) }}
        </div>

         <div class="col-6 form-group">
            {{ Form::label('beneficiary', __('Beneficiary'),['class'=>'form-label']) }}
            {{ Form::text('beneficiary', null, array('class' => 'form-control','required'=>'required')) }}
        </div>






        <div class="col-6 form-group">    


        {{ Form::label('plan', __('Plan'),['class'=>'form-label']) }}
                    <div class="controls">
                            <select name="plan"  required="" class="form-control">
                                <option value="" selected="" disabled="">Select Plan </option>
                                <option value="$5,000">$5,000</option>
                                <option value="$10,000">$10,000</option>
                                <option value="$15,000">$15,000</option>
                                <option value="$20,000">$20,000</option>
                                <option value="$25,000">$25,000</option>
                                <option value="Other">Other</option>
                                <option value="All Plans">All Plans</option>
                                <option value="More than $25,000">More than $25,000</option>
                                <option value="Decide Later">Decide Later</option>
                            </select>

                    </div>

        </div>
        
        <div class="col-6 form-group">    


        {{ Form::label('smoker', __('Smoker'),['class'=>'form-label']) }}
                    <div class="controls">
                            <select name="smoker"  required="" class="form-control">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>


                            </select>

                    </div>

        </div>



        <div class="col-6 form-group">
            {{ Form::label('color_hobby', __('Color/Hobby'),['class'=>'form-label']) }}
            {{ Form::text('color_hobby', null, array('class' => 'form-control','required'=>'required')) }}
        </div>



        
        <div class="col-6 form-group">
            {{ Form::label('licensed_agent_name', __('Licensed Agent Name'),['class'=>'form-label']) }}
            {{ Form::text('licensed_agent_name', null, array('class' => 'form-control','required'=>'required')) }}
        </div>




         <div class="col-6 form-group">
            {{ Form::label('call_back_time', __('Call Back Time'),['class'=>'form-label']) }}
            {{ Form::text('call_back_time', null, array('class' => 'form-control','required'=>'required')) }}
        </div>















       
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>

{{Form::close()}}


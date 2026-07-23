{{ Form::model($lead, array('route' => array('leads.update', $lead->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">


        <div class="col-6 form-group">
            {{ Form::label('subject', __('Subject'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('subject', null, array('class' => 'form-control','required'=>'required')) }}
        </div>

         <div class="col-6 form-group">
            {{ Form::label('user_id', __('User'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::select('user_id', $users,null, array('class' => 'form-control select','required'=>'required')) }}

        </div>



        <div class="col-6 form-group">
            {{ Form::label('name', __(' Name'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
        </div>




      

        <div class="col-6 form-group">
            {{ Form::label('phone', __('Phone'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('phone', null, array('class' => 'form-control','required'=>'required')) }}
        </div>


          <div class="col-6 form-group">
            {{ Form::label('email', __('Email'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::email('email', null, array('class' => 'form-control','required'=>'required')) }}
        </div>


        


        <div class="col-6 form-group">    
    {{ Form::label('state', __('State'),['class'=>'form-label']) }}
    <div class="controls">
        <select name="state" required="" class="form-control">
            <option value="" selected="" disabled="">Select State</option>
            <option value="AK - Alaska" {{ $lead->state == 'AK - Alaska' ? 'selected' : '' }}>AK - Alaska</option>
            <option value="AL - Alabama" {{ $lead->state == 'AL - Alabama' ? 'selected' : '' }}>AL - Alabama</option>
            <option value="AR - Arkansas" {{ $lead->state == 'AR - Arkansas' ? 'selected' : '' }}>AR - Arkansas</option>
            <option value="AZ - Arizona" {{ $lead->state == 'AZ - Arizona' ? 'selected' : '' }}>AZ - Arizona</option>
            <option value="CA - California" {{ $lead->state == 'CA - California' ? 'selected' : '' }}>CA - California</option>
            <option value="CO - Colorado" {{ $lead->state == 'CO - Colorado' ? 'selected' : '' }}>CO - Colorado</option>
            <option value="CT - Connecticut" {{ $lead->state == 'CT - Connecticut' ? 'selected' : '' }}>CT - Connecticut</option>
            <option value="DC - Dist. of Columbia" {{ $lead->state == 'DC - Dist. of Columbia' ? 'selected' : '' }}>DC - Dist. of Columbia</option>
            <option value="DE - Delaware" {{ $lead->state == 'DE - Delaware' ? 'selected' : '' }}>DE - Delaware</option>
            <option value="FL - Florida" {{ $lead->state == 'FL - Florida' ? 'selected' : '' }}>FL - Florida</option>
            <option value="GA - Georgia" {{ $lead->state == 'GA - Georgia' ? 'selected' : '' }}>GA - Georgia</option>
            <option value="HI - Hawaii" {{ $lead->state == 'HI - Hawaii' ? 'selected' : '' }}>HI - Hawaii</option>
            <option value="IA - Iowa" {{ $lead->state == 'IA - Iowa' ? 'selected' : '' }}>IA - Iowa</option>
            <option value="ID - Idaho" {{ $lead->state == 'ID - Idaho' ? 'selected' : '' }}>ID - Idaho</option>
            <option value="IL - Illinois" {{ $lead->state == 'IL - Illinois' ? 'selected' : '' }}>IL - Illinois</option>
            <option value="IN - Indiana" {{ $lead->state == 'IN - Indiana' ? 'selected' : '' }}>IN - Indiana</option>
            <option value="KS - Kansas" {{ $lead->state == 'KS - Kansas' ? 'selected' : '' }}>KS - Kansas</option>
            <option value="KY - Kentucky" {{ $lead->state == 'KY - Kentucky' ? 'selected' : '' }}>KY - Kentucky</option>
            <option value="LA - Louisiana" {{ $lead->state == 'LA - Louisiana' ? 'selected' : '' }}>LA - Louisiana</option>
            <option value="MA - Massachusetts" {{ $lead->state == 'MA - Massachusetts' ? 'selected' : '' }}>MA - Massachusetts</option>
            <option value="MD - Maryland" {{ $lead->state == 'MD - Maryland' ? 'selected' : '' }}>MD - Maryland</option>
            <option value="ME - Maine" {{ $lead->state == 'ME - Maine' ? 'selected' : '' }}>ME - Maine</option>
            <option value="MI - Michigan" {{ $lead->state == 'MI - Michigan' ? 'selected' : '' }}>MI - Michigan</option>
            <option value="MN - Minnesota" {{ $lead->state == 'MN - Minnesota' ? 'selected' : '' }}>MN - Minnesota</option>
            <option value="MO - Missouri" {{ $lead->state == 'MO - Missouri' ? 'selected' : '' }}>MO - Missouri</option>
            <option value="MS - Mississippi" {{ $lead->state == 'MS - Mississippi' ? 'selected' : '' }}>MS - Mississippi</option>
            <option value="MT - Montana" {{ $lead->state == 'MT - Montana' ? 'selected' : '' }}>MT - Montana</option>
            <option value="NC - North Carolina" {{ $lead->state == 'NC - North Carolina' ? 'selected' : '' }}>NC - North Carolina</option>
            <option value="ND - North Dakota" {{ $lead->state == 'ND - North Dakota' ? 'selected' : '' }}>ND - North Dakota</option>
            <option value="NE - Nebraska" {{ $lead->state == 'NE - Nebraska' ? 'selected' : '' }}>NE - Nebraska</option>
            <option value="NH - New Hampshire" {{ $lead->state == 'NH - New Hampshire' ? 'selected' : '' }}>NH - New Hampshire</option>
            <option value="NJ - New Jersey" {{ $lead->state == 'NJ - New Jersey' ? 'selected' : '' }}>NJ - New Jersey</option>
            <option value="NM - New Mexico" {{ $lead->state == 'NM - New Mexico' ? 'selected' : '' }}>NM - New Mexico</option>
            <option value="NV - Nevada" {{ $lead->state == 'NV - Nevada' ? 'selected' : '' }}>NV - Nevada</option>
            <option value="NY - New York" {{ $lead->state == 'NY - New York' ? 'selected' : '' }}>NY - New York</option>
            <option value="OH - Ohio" {{ $lead->state == 'OH - Ohio' ? 'selected' : '' }}>OH - Ohio</option>
            <option value="OK - Oklahoma" {{ $lead->state == 'OK - Oklahoma' ? 'selected' : '' }}>OK - Oklahoma</option>
            <option value="OR - Oregon" {{ $lead->state == 'OR - Oregon' ? 'selected' : '' }}>OR - Oregon</option>
            <option value="PA - Pennsylvania" {{ $lead->state == 'PA - Pennsylvania' ? 'selected' : '' }}>PA - Pennsylvania</option>
            <option value="RI - Rhode Island" {{ $lead->state == 'RI - Rhode Island' ? 'selected' : '' }}>RI - Rhode Island</option>
      <option value="SC - South Carolina" {{ $lead->state == 'SC - South Carolina' ? 'selected' : '' }}>SC - South Carolina</option>
      <option value="SD - South Dakota" {{ $lead->state == 'SD - South Dakota' ? 'selected' : '' }}>SD - South Dakota</option>
      <option value="TN - Tennessee" {{ $lead->state == 'TN - Tennessee' ? 'selected' : '' }}>TN - Tennessee</option>
      <option value="TX - Texas" {{ $lead->state == 'TX - Texas' ? 'selected' : '' }}>TX - Texas</option>
      <option value="UT - Utah" {{ $lead->state == 'UT - Utah' ? 'selected' : '' }}>UT - Utah</option>
      <option value="VA - Virginia" {{ $lead->state == 'VA - Virginia' ? 'selected' : '' }}>VA - Virginia</option>
      <option value="VT - Vermont" {{ $lead->state == 'VT - Vermont' ? 'selected' : '' }}>VT - Vermont</option>
      <option value="WV - West Virginia" {{ $lead->state == 'WV - West Virginia' ? 'selected' : '' }}>WV - West Virginia</option>
      <option value="WA - Washington" {{ $lead->state == 'WA - Washington' ? 'selected' : '' }}>WA - Washington</option>
      <option value="WI - Wisconsin" {{ $lead->state == 'WI - Wisconsin' ? 'selected' : '' }}>WI - Wisconsin</option>
      <option value="WY - Wyoming" {{ $lead->state == 'WY - Wyoming' ? 'selected' : '' }}>WY - Wyoming</option>
      <option value="Other" {{ $lead->state == 'Other' ? 'selected' : '' }}>Other</option>

        </select>
    </div>
</div>


















        



          <div class="col-6 form-group">
            {{ Form::label('city', __('City'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('city', null, array('class' => 'form-control','required'=>'required')) }}
        </div>



         <div class="col-6 form-group">
            {{ Form::label('zip_code', __('Zip Code'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('zip_code', null, array('class' => 'form-control','required'=>'required')) }}
        </div>



        <div class="col-6 form-group">
            {{ Form::label('address', __('Address'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('address', null, array('class' => 'form-control','required'=>'required')) }}
        </div>


        


        <div class="col-6 form-group">
            {{ Form::label('age', __('Age'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('age', null, array('class' => 'form-control','required'=>'required')) }}
        </div>


            <div class="col-6 form-group">
            {{ Form::label('spouse_age', __('Spouse Age'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('spouse_age', null, array('class' => 'form-control','required'=>'required')) }}
        </div>




        <div class="col-6 form-group">
            {{ Form::label('beneficiary', __('Beneficiary'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('beneficiary', null, array('class' => 'form-control','required'=>'required')) }}
        </div>



       



        <div class="col-6 form-group">
    {{ Form::label('plan', __('Plan'),['class'=>'form-label']) }}
        <div class="controls">
            <select name="plan" required="" class="form-control">
                <option value="$5,000" {{ $lead->plan == '$5,000' ? 'selected' : '' }}>$5,000</option>


                <option value="$10,000" {{ $lead->plan == '$10,000' ? 'selected' : '' }}>$10,000</option>


                <option value="$15,000" {{ $lead->plan == '$15,000' ? 'selected' : '' }}>$15,000</option>


                <option value="$20,000" {{ $lead->plan == '$20,000' ? 'selected' : '' }}>$20,000</option>

                <option value="$25,000" {{ $lead->plan == '$25,000' ? 'selected' : '' }}>$25,000</option>

                <option value="More than $25,000" {{ $lead->plan == 'More than $25,000' ? 'selected' : '' }}>More than $25,000</option>
                <option value="All Plans" {{ $lead->plan == 'All Plans' ? 'selected' : '' }}>All Plans</option>

                <option value="Other" {{ $lead->plan == 'Other' ? 'selected' : '' }}>$Other</option>         


                <option value="Decide Later" {{ $lead->plan == 'Decide Later' ? 'selected' : '' }}>Decide Later</option>

            </select>
        </div>
</div>













        <div class="col-6 form-group">
    {{ Form::label('smoker', __('Smoker'),['class'=>'form-label']) }}
        <div class="controls">
            <select name="smoker" required="" class="form-control">
                <option value="Yes" {{ $lead->smoker == 'Yes' ? 'selected' : '' }}>Yes</option>
                <option value="No" {{ $lead->smoker == 'No' ? 'selected' : '' }}>No</option>
            </select>
        </div>
</div>


















         <div class="col-6 form-group">
            {{ Form::label('color_hobby', __('Color/Hobby'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('color_hobby', null, array('class' => 'form-control','required'=>'required')) }}
        </div>




        
        <div class="col-6 form-group">
            {{ Form::label('licensed_agent_name', __('Licensed Agent Name'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('licensed_agent_name', null, array('class' => 'form-control','required'=>'required')) }}
        </div>


        <div class="col-6 form-group">
            {{ Form::label('call_back_time', __('Call Back Time'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('call_back_time', null, array('class' => 'form-control','required'=>'required')) }}
        </div>








       



         


       


      
        
      
        
        <div class="col-6 form-group">
            {{ Form::label('pipeline_id', __('Pipeline'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::select('pipeline_id', $pipelines,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('stage_id', __('Stage'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::select('stage_id', [''=>__('Select Stage')],null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('sources', __('Sources'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::select('sources[]', $sources,null, array('class' => 'form-control select2','id'=>'choices-multiple1','multiple'=>'','required'=>'required')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('products', __('Products'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::select('products[]', $products,null, array('class' => 'form-control select2','id'=>'choices-multiple2','multiple'=>'','required'=>'required')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('notes', __('Notes'),['class'=>'form-label']) }}
            {{ Form::textarea('notes',null, array('class' => 'summernote-simple')) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>

{{Form::close()}}



<script>
    var stage_id = '{{$lead->stage_id}}';

    $(document).ready(function () {
        var pipeline_id = $('[name=pipeline_id]').val();
        getStages(pipeline_id);
    });

    $(document).on("change", "#commonModal select[name=pipeline_id]", function () {
        var currVal = $(this).val();
        console.log('current val ', currVal);
        getStages(currVal);
    });

    function getStages(id) {
        $.ajax({
            url: '{{route('leads.json')}}',
            data: {pipeline_id: id, _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                var stage_cnt = Object.keys(data).length;
                $("#stage_id").empty();
                if (stage_cnt > 0) {
                    $.each(data, function (key, data1) {
                        var select = '';
                        if (key == '{{ $lead->stage_id }}') {
                            select = 'selected';
                        }
                        $("#stage_id").append('<option value="' + key + '" ' + select + '>' + data1 + '</option>');
                    });
                }
                $("#stage_id").val(stage_id);
                $('#stage_id').select2({
                    placeholder: "{{__('Select Stage')}}"
                });
            }
        })
    }
</script>

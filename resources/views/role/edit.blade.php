{{Form::model($role,array('route' => array('roles.update', $role->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{Form::label('name',__('Name'),['class'=>'form-label'])}}
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Role Name')))}}
                @error('name')
                <small class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>

            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle"></i> 
                <strong>{{__('Managing Permissions')}}</strong> - {{__('Select permissions to assign to this role. Total available permissions:')}} <strong>{{ count($permissions) }}</strong>
            </div>
            
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-staff-tab" data-bs-toggle="pill" href="#staff" role="tab" aria-controls="pills-home" aria-selected="true">{{__('Staff')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-crm-tab" data-bs-toggle="pill" href="#crm" role="tab" aria-controls="pills-profile" aria-selected="false">{{__('CRM')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-project-tab" data-bs-toggle="pill" href="#project" role="tab" aria-controls="pills-contact" aria-selected="false">{{__('Project')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-hrmpermission-tab" data-bs-toggle="pill" href="#hrmpermission" role="tab" aria-controls="pills-contact" aria-selected="false">{{__('HRM')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-account-tab" data-bs-toggle="pill" href="#account" role="tab" aria-controls="pills-contact" aria-selected="false">{{__('Account')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-pos-tab" data-bs-toggle="pill" href="#pos" role="tab" aria-controls="pills-contact" aria-selected="false">{{__('POS')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-other-tab" data-bs-toggle="pill" href="#other" role="tab" aria-controls="pills-other" aria-selected="false">
                        <span class="badge bg-warning text-dark">{{__('Other')}}</span>
                    </a>
                </li>
            </ul>

            @php
                // Track all displayed permissions across all tabs
                $displayedPermissions = [];
                
                // Define all possible action types
                $actionTypes = [
                    'view', 'add', 'move', 'manage', 'create', 'edit', 'delete', 'show', 'send',
                    'create payment', 'delete payment', 'income', 'expense', 'income vs expense',
                    'loss & profit', 'tax', 'invoice', 'bill', 'duplicate', 'balance sheet',
                    'ledger', 'trial balance'
                ];
            @endphp

            <div class="tab-content" id="pills-tabContent">
                <!-- STAFF TAB -->
                <div class="tab-pane fade show active" id="staff" role="tabpanel" aria-labelledby="pills-staff-tab">
                    @php
                        $staffModules = ['user','role','team','client','product & service','constant unit','constant tax','constant category','company settings'];
                        if(\Auth::user()->type == 'company'){
                            $staffModules[] = 'language';
                            $staffModules[] = 'permission';
                        }
                    @endphp
                    <div class="col-md-12">
                        <div class="form-group">
                            @if(!empty($permissions))
                                <h6 class="my-3">{{__('Assign General Permission to Roles')}}</h6>
                                <table class="table table-striped mb-0">
                                    <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" class="form-check-input align-middle custom_align_middle" id="staff_checkall">
                                        </th>
                                        <th width="200">{{__('Module')}}</th>
                                        <th>{{__('Permissions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($staffModules as $module)
                                        @php
                                            $moduleHasPermissions = false;
                                            foreach($actionTypes as $action) {
                                                if(in_array($action.' '.$module, (array) $permissions)) {
                                                    $moduleHasPermissions = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        @if($moduleHasPermissions)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input align-middle ischeck staff_checkall" data-id="{{str_replace(' ', '', str_replace('&', '', $module))}}">
                                            </td>
                                            <td>
                                                <label class="ischeck staff_checkall" data-id="{{str_replace(' ', '', str_replace('&', '', $module))}}">{{ ucfirst($module) }}</label>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    @foreach($actionTypes as $action)
                                                        @if(in_array($action.' '.$module, (array) $permissions))
                                                            @php 
                                                                $key = array_search($action.' '.$module, $permissions);
                                                                if($key !== false) {
                                                                    $displayedPermissions[] = $key;
                                                                }
                                                            @endphp
                                                            @if($key !== false)
                                                                <div class="col-md-3 custom-control custom-checkbox mb-2">
                                                                    {{Form::checkbox('permissions[]', $key, $role->permission, ['class'=>'form-check-input staff_checkall isscheck_'.str_replace(' ', '', str_replace('&', '', $module)), 'id'=>'permission'.$key])}}
                                                                    {{Form::label('permission'.$key, ucwords($action), ['class'=>'custom-control-label ms-1'])}}
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- CRM TAB -->
                <div class="tab-pane fade" id="crm" role="tabpanel" aria-labelledby="pills-crm-tab">
                    @php
                        $crmModules = ['crm dashboard','lead','pipeline','lead stage','source','priority','label','group','deal','stage','task','form builder','form response','contract','contract type'];
                    @endphp
                    <div class="col-md-12">
                        <div class="form-group">
                            @if(!empty($permissions))
                                <h6 class="my-3">{{__('Assign CRM related Permission to Roles')}}</h6>
                                <table class="table table-striped mb-0">
                                    <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" class="form-check-input align-middle custom_align_middle" id="crm_checkall">
                                        </th>
                                        <th width="200">{{__('Module')}}</th>
                                        <th>{{__('Permissions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($crmModules as $module)
                                        @php
                                            $moduleHasPermissions = false;
                                            foreach($actionTypes as $action) {
                                                if(in_array($action.' '.$module, (array) $permissions)) {
                                                    $moduleHasPermissions = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        @if($moduleHasPermissions)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input align-middle ischeck crm_checkall" data-id="{{str_replace(' ', '', $module)}}">
                                            </td>
                                            <td>
                                                <label class="ischeck crm_checkall" data-id="{{str_replace(' ', '', $module)}}">{{ ucfirst($module) }}</label>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    @foreach($actionTypes as $action)
                                                        @if(in_array($action.' '.$module, (array) $permissions))
                                                            @php 
                                                                $key = array_search($action.' '.$module, $permissions);
                                                                if($key !== false) {
                                                                    $displayedPermissions[] = $key;
                                                                }
                                                            @endphp
                                                            @if($key !== false)
                                                                <div class="col-md-3 custom-control custom-checkbox mb-2">
                                                                    {{Form::checkbox('permissions[]', $key, $role->permission, ['class'=>'form-check-input crm_checkall isscheck_'.str_replace(' ', '', $module), 'id'=>'permission'.$key])}}
                                                                    {{Form::label('permission'.$key, ucwords($action), ['class'=>'custom-control-label ms-1'])}}
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- PROJECT TAB -->
                <div class="tab-pane fade" id="project" role="tabpanel" aria-labelledby="pills-project-tab">
                    @php
                        $projectModules = ['project dashboard','project','milestone','grant chart','project stage','timesheet','expense','project task','activity','CRM activity','project task stage','bug report','bug status'];
                    @endphp
                    <div class="col-md-12">
                        <div class="form-group">
                            @if(!empty($permissions))
                                <h6 class="my-3">{{__('Assign Project related Permission to Roles')}}</h6>
                                <table class="table table-striped mb-0">
                                    <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" class="form-check-input align-middle custom_align_middle" id="project_checkall">
                                        </th>
                                        <th width="200">{{__('Module')}}</th>
                                        <th>{{__('Permissions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($projectModules as $module)
                                        @php
                                            $moduleHasPermissions = false;
                                            foreach($actionTypes as $action) {
                                                if(in_array($action.' '.$module, (array) $permissions)) {
                                                    $moduleHasPermissions = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        @if($moduleHasPermissions)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input align-middle ischeck project_checkall" data-id="{{str_replace(' ', '', $module)}}">
                                            </td>
                                            <td>
                                                <label class="ischeck project_checkall" data-id="{{str_replace(' ', '', $module)}}">{{ ucfirst($module) }}</label>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    @foreach($actionTypes as $action)
                                                        @if(in_array($action.' '.$module, (array) $permissions))
                                                            @php 
                                                                $key = array_search($action.' '.$module, $permissions);
                                                                if($key !== false) {
                                                                    $displayedPermissions[] = $key;
                                                                }
                                                            @endphp
                                                            @if($key !== false)
                                                                <div class="col-md-3 custom-control custom-checkbox mb-2">
                                                                    {{Form::checkbox('permissions[]', $key, $role->permission, ['class'=>'form-check-input project_checkall isscheck_'.str_replace(' ', '', $module), 'id'=>'permission'.$key])}}
                                                                    {{Form::label('permission'.$key, ucwords($action), ['class'=>'custom-control-label ms-1'])}}
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- HRM TAB -->
                <div class="tab-pane fade" id="hrmpermission" role="tabpanel" aria-labelledby="pills-hrmpermission-tab">
                    @php
                        $hrmModules = ['hrm dashboard','employee','employee profile','department','designation','branch','document type','document','payslip type','allowance','commission','allowance option','loan option','deduction option','loan','saturation deduction','other payment','overtime','set salary','pay slip','company policy','appraisal','goal tracking','goal type','indicator','event','meeting','training','trainer','training type','award','award type','resignation','travel','promotion','complaint','warning','termination','termination type','job application','job application note','job onBoard','job category','job','job stage','custom question','interview schedule','estimation','holiday','transfer','announcement','leave','leave type','attendance'];
                    @endphp
                    <div class="col-md-12">
                        <div class="form-group">
                            @if(!empty($permissions))
                                <h6 class="my-3">{{__('Assign HRM related Permission to Roles')}}</h6>
                                <table class="table table-striped mb-0">
                                    <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" class="form-check-input align-middle custom_align_middle" id="hrm_checkall">
                                        </th>
                                        <th width="200">{{__('Module')}}</th>
                                        <th>{{__('Permissions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($hrmModules as $module)
                                        @php
                                            $moduleHasPermissions = false;
                                            foreach($actionTypes as $action) {
                                                if(in_array($action.' '.$module, (array) $permissions)) {
                                                    $moduleHasPermissions = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        @if($moduleHasPermissions)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input align-middle ischeck hrm_checkall" data-id="{{str_replace(' ', '', $module)}}">
                                            </td>
                                            <td>
                                                <label class="ischeck hrm_checkall" data-id="{{str_replace(' ', '', $module)}}">{{ ucfirst($module) }}</label>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    @foreach($actionTypes as $action)
                                                        @if(in_array($action.' '.$module, (array) $permissions))
                                                            @php 
                                                                $key = array_search($action.' '.$module, $permissions);
                                                                if($key !== false) {
                                                                    $displayedPermissions[] = $key;
                                                                }
                                                            @endphp
                                                            @if($key !== false)
                                                                <div class="col-md-3 custom-control custom-checkbox mb-2">
                                                                    {{Form::checkbox('permissions[]', $key, $role->permission, ['class'=>'form-check-input hrm_checkall isscheck_'.str_replace(' ', '', $module), 'id'=>'permission'.$key])}}
                                                                    {{Form::label('permission'.$key, ucwords($action), ['class'=>'custom-control-label ms-1'])}}
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ACCOUNT TAB -->
                <div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="pills-account-tab">
                    @php
                        $accountModules = ['account dashboard','proposal','invoice','bill','revenue','payment','proposal product','invoice product','bill product','goal','credit note','debit note','bank account','bank transfer','transaction','customer','vender','constant custom field','assets','chart of account','journal entry','report'];
                    @endphp
                    <div class="col-md-12">
                        <div class="form-group">
                            @if(!empty($permissions))
                                <h6 class="my-3">{{__('Assign Account related Permission to Roles')}}</h6>
                                <table class="table table-striped mb-0">
                                    <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" class="form-check-input align-middle custom_align_middle" id="account_checkall">
                                        </th>
                                        <th width="200">{{__('Module')}}</th>
                                        <th>{{__('Permissions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($accountModules as $module)
                                        @php
                                            $moduleHasPermissions = false;
                                            foreach($actionTypes as $action) {
                                                if(in_array($action.' '.$module, (array) $permissions)) {
                                                    $moduleHasPermissions = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        @if($moduleHasPermissions)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input align-middle ischeck account_checkall" data-id="{{str_replace(' ', '', $module)}}">
                                            </td>
                                            <td>
                                                <label class="ischeck account_checkall" data-id="{{str_replace(' ', '', $module)}}">{{ ucfirst($module) }}</label>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    @foreach($actionTypes as $action)
                                                        @if(in_array($action.' '.$module, (array) $permissions))
                                                            @php 
                                                                $key = array_search($action.' '.$module, $permissions);
                                                                if($key !== false) {
                                                                    $displayedPermissions[] = $key;
                                                                }
                                                            @endphp
                                                            @if($key !== false)
                                                                <div class="col-md-3 custom-control custom-checkbox mb-2">
                                                                    {{Form::checkbox('permissions[]', $key, $role->permission, ['class'=>'form-check-input account_checkall isscheck_'.str_replace(' ', '', $module), 'id'=>'permission'.$key])}}
                                                                    {{Form::label('permission'.$key, ucwords($action), ['class'=>'custom-control-label ms-1'])}}
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- POS TAB -->
                <div class="tab-pane fade" id="pos" role="tabpanel" aria-labelledby="pills-pos-tab">
                    @php
                        $posModules = ['warehouse','purchase','pos','barcode'];
                    @endphp
                    <div class="col-md-12">
                        <div class="form-group">
                            @if(!empty($permissions))
                                <h6 class="my-3">{{__('Assign POS related Permission to Roles')}}</h6>
                                <table class="table table-striped mb-0">
                                    <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" class="form-check-input align-middle custom_align_middle" id="pos_checkall">
                                        </th>
                                        <th width="200">{{__('Module')}}</th>
                                        <th>{{__('Permissions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($posModules as $module)
                                        @php
                                            $moduleHasPermissions = false;
                                            foreach($actionTypes as $action) {
                                                if(in_array($action.' '.$module, (array) $permissions)) {
                                                    $moduleHasPermissions = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        @if($moduleHasPermissions)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input align-middle ischeck pos_checkall" data-id="{{str_replace(' ', '', $module)}}">
                                            </td>
                                            <td>
                                                <label class="ischeck pos_checkall" data-id="{{str_replace(' ', '', $module)}}">{{ ucfirst($module) }}</label>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    @foreach($actionTypes as $action)
                                                        @if(in_array($action.' '.$module, (array) $permissions))
                                                            @php 
                                                                $key = array_search($action.' '.$module, $permissions);
                                                                if($key !== false) {
                                                                    $displayedPermissions[] = $key;
                                                                }
                                                            @endphp
                                                            @if($key !== false)
                                                                <div class="col-md-3 custom-control custom-checkbox mb-2">
                                                                    {{Form::checkbox('permissions[]', $key, $role->permission, ['class'=>'form-check-input pos_checkall isscheck_'.str_replace(' ', '', $module), 'id'=>'permission'.$key])}}
                                                                    {{Form::label('permission'.$key, ucwords($action), ['class'=>'custom-control-label ms-1'])}}
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- OTHER PERMISSIONS TAB -->
                <div class="tab-pane fade" id="other" role="tabpanel" aria-labelledby="pills-other-tab">
                    <div class="col-md-12">
                        <div class="form-group">
                            @if(!empty($permissions))
                                @php
                                    // Get all permissions that weren't displayed in other tabs
                                    $otherPermissions = [];
                                    foreach($permissions as $key => $permission) {
                                        if(!in_array($key, $displayedPermissions)) {
                                            $otherPermissions[$key] = $permission;
                                        }
                                    }
                                    
                                    // Sort alphabetically
                                    asort($otherPermissions);
                                @endphp
                                
                                @if(!empty($otherPermissions))
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">{{__('Other Permissions')}} <span class="badge bg-primary">{{ count($otherPermissions) }}</span></h6>
                                        <small class="text-muted">{{__('Sorted A-Z')}}</small>
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> {{__('These permissions do not follow the standard module patterns.')}}
                                    </div>
                                    <table class="table table-striped table-hover mb-0">
                                        <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" class="form-check-input align-middle" id="other_checkall">
                                            </th>
                                            <th>{{__('Permission Name')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($otherPermissions as $key => $permission)
                                            <tr>
                                                <td>
                                                    {{Form::checkbox('permissions[]', $key, $role->permission, ['class'=>'form-check-input other_checkall', 'id'=>'permission'.$key])}}
                                                </td>
                                                <td>
                                                    {{Form::label('permission'.$key, ucwords(str_replace(['-', '_'], ' ', $permission)), ['class'=>'form-label mb-0'])}}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i> {{__('All permissions are categorized in the module tabs above.')}}
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>

{{Form::close()}}

<script>
$(document).ready(function () {
    // Master checkbox handlers for each tab
    $("#staff_checkall").click(function(){
        $('.staff_checkall').not(this).prop('checked', this.checked);
    });
    
    $("#crm_checkall").click(function(){
        $('.crm_checkall').not(this).prop('checked', this.checked);
    });
    
    $("#project_checkall").click(function(){
        $('.project_checkall').not(this).prop('checked', this.checked);
    });
    
    $("#hrm_checkall").click(function(){
        $('.hrm_checkall').not(this).prop('checked', this.checked);
    });
    
    $("#account_checkall").click(function(){
        $('.account_checkall').not(this).prop('checked', this.checked);
    });
    
    $("#pos_checkall").click(function(){
        $('.pos_checkall').not(this).prop('checked', this.checked);
    });
    
    $("#other_checkall").click(function(){
        $('.other_checkall').not(this).prop('checked', this.checked);
    });
    
    // Module row checkbox handlers
    $(".ischeck").click(function(){
        var ischeck = $(this).data('id');
        $('.isscheck_'+ ischeck).prop('checked', this.checked);
    });
});
</script>
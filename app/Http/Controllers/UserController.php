<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\Employee;
use App\Models\ExperienceCertificate;
use App\Models\GenerateOfferLetter;
use App\Models\JoiningLetter;
use App\Models\NOC;
use App\Models\User;
use App\Models\Bank;

use App\Models\UserCompany;
use Auth;
use File;
use App\Models\Utility;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserToDo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Session;
use Spatie\Permission\Models\Role;
use Illuminate\Database\QueryException;
use App\Models\UserDetail;
use App\Models\UserBankDetail;
// Load Dashboard user's using ajax


class UserController extends Controller
{

    public function index()
    {
        $user = \Auth::user();
        if (\Auth::user()->can('manage user')) {
            if (\Auth::user()->type == 'super admin') {
                $users = User::query()
                    ->forCurrentCenter()
                    ->where('created_by', '=', $user->creatorId())
                    ->where('type', '=', 'company')
                    ->get();
            } else {
                $users = User::query()
                    ->forCurrentCenter()
                    ->where('created_by', '=', $user->creatorId())
                    ->where('type', '!=', 'client')
                    ->get();
            }

            return view('user.index')->with('users', $users);
        } else {
            return redirect()->back();
        }
    }



    public function create()
    {

        $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'user')->get();
        $user  = \Auth::user();
        $roles = Role::where('created_by', '=', $user->creatorId())->where('name', '!=', 'client')->get()->pluck('name', 'id');
        // Center list only for bypass users (admins). Other users are auto-assigned center_id.
        $centers = $user->canBypassCenterScope()
            ? \App\Models\Center::orderBy('center_name')->pluck('center_name', 'id')
            : collect();
        if (\Auth::user()->can('create user')) {
            return view('user.create', compact('roles', 'customFields', 'centers'));
        } else {
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        // 🔒 SECURITY: Restrict user creation to Director (and super admin/company)
        $currentUser = \Auth::user();
        $allowedTypes = ['Director', 'super admin', 'company'];
        if (!in_array($currentUser->type, $allowedTypes, true)) {
            Log::warning('Unauthorized user creation attempt blocked', [
                'attempted_by' => $currentUser->email,
                'attempted_by_id' => $currentUser->id,
                'attempted_by_name' => $currentUser->name,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'requested_email' => $request->email ?? 'N/A',
                'requested_name' => $request->name ?? 'N/A',
                'attempted_by_type' => $currentUser->type,
            ]);
            
            return redirect()->back()->with('error', __('You do not have permission to create users. Only Directors / administrators can perform this action.'));
        }

        if (\Auth::user()->can('create user')) {
            $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->first();
            if (\Auth::user()->type == 'super admin') {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users',
                        'password' => 'required|min:6',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $user               = new User();
                $user['name']       = $request->name;
                $user['email']      = $request->email;
                $psw                = $request->password;
                $user['password']   = Hash::make($request->password);
                $user['type']       = 'company';
                $user['default_pipeline'] = 1;
                $user['plan'] = 1;
                $user['lang']       = !empty($default_language) ? $default_language->value : '';
                $user['created_by'] = \Auth::user()->creatorId();
                $user['plan']       = Plan::first()->id;
                // center_id is auto-assigned by User model for non-bypass users
                if ($currentUser->canBypassCenterScope()) {
                    $user['center_id'] = $request->center_id ?? null;
                }
                $user['is_wfh'] = $request->boolean('is_wfh');

                $user->save();
                $role_r = Role::findByName('company');
                $user->assignRole($role_r);
                
                // 🔒 SECURITY: Log user creation
                Log::info('User created', [
                    'created_by' => $currentUser->email,
                    'created_by_id' => $currentUser->id,
                    'new_user_id' => $user->id,
                    'new_user_email' => $user->email,
                    'new_user_name' => $user->name,
                    'new_user_type' => $user->type,
                    'assigned_role' => $role_r->name,
                    'ip' => request()->ip()
                ]);
                //                $user->userDefaultData();
                $user->userDefaultDataRegister($user->id);
                $user->userWarehouseRegister($user->id);

                //default bank account for new company
                $user->userDefaultBankAccount($user->id);

                Utility::chartOfAccountTypeData($user->id);
                Utility::chartOfAccountData($user);
                // default chart of account for new company
                Utility::chartOfAccountData1($user->id);

                Utility::pipeline_lead_deal_Stage($user->id);
                Utility::project_task_stages($user->id);
                Utility::priorities($user->id);
                Utility::labels($user->id);
                Utility::groups($user->id);
                Utility::sources($user->id);
                Utility::jobStage($user->id);
                GenerateOfferLetter::defaultOfferLetterRegister($user->id);
                ExperienceCertificate::defaultExpCertificatRegister($user->id);
                JoiningLetter::defaultJoiningLetterRegister($user->id);
                NOC::defaultNocCertificateRegister($user->id);
            } else {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users',
                        'password' => 'required|min:6',
                        'role' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }


                $objUser    = \Auth::user()->creatorId();
                $objUser = User::find($objUser);
                $user = User::find(\Auth::user()->created_by);
                $total_user = $objUser->countUsers();
                $plan       = Plan::find($objUser->plan);
                if ($total_user < $plan->max_users || $plan->max_users == -1) {
                    $role_r                = Role::findById($request->role);
                    $psw                   = $request->password;
                    $request['password']   = Hash::make($request->password);
                    $request['type']       = $role_r->name;
                    $request['lang']       = !empty($default_language) ? $default_language->value : 'en';
                    $request['created_by'] = \Auth::user()->creatorId();
                    // center_id is auto-assigned by User model for non-bypass users
                    if ($currentUser->canBypassCenterScope()) {
                        $request['center_id'] = $request->center_id ?? null;
                    } else {
                        unset($request['center_id']);
                    }
                    $request['is_wfh'] = $request->boolean('is_wfh');
                    $user = User::create($request->all());
                    $user->assignRole($role_r);
                    
                    // 🔒 SECURITY: Log user creation with role assignment
                    Log::info('User created with role', [
                        'created_by' => $currentUser->email,
                        'created_by_id' => $currentUser->id,
                        'new_user_id' => $user->id,
                        'new_user_email' => $user->email,
                        'new_user_name' => $user->name,
                        'new_user_type' => $user->type,
                        'assigned_role_id' => $role_r->id,
                        'assigned_role_name' => $role_r->name,
                        'ip' => request()->ip()
                    ]);
                    
                    if ($request['type'] != 'client')
                        \App\Models\Utility::employeeDetails($user->id, \Auth::user()->creatorId());
                } else {
                    return redirect()->back()->with('error', __('Your user limit is over, .'));
                }
            }
            // Send Email
            $setings = Utility::settings();


            if ($setings['new_user'] == 1) {

                $user->password = $psw;
                $user->type = $role_r->name;

                $userArr = [
                    'email' => $user->email,
                    'password' => $user->password,
                ];
                $resp = Utility::sendEmailTemplate('new_user', [$user->id => $user->email], $userArr);


                return redirect()->route('users.index')->with('success', __('User successfully created.') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
            }
            return redirect()->route('users.index')->with('success', __('User successfully created.'));
        } else {
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $user  = \Auth::user();
        $roles = Role::where('created_by', '=', $user->creatorId())->where('name', '!=', 'client')->get()->pluck('name', 'id');
        if (\Auth::user()->can('edit user')) {
            $user              = User::findOrFail($id);
            $user->customField = CustomField::getData($user, 'user');
            $customFields      = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'user')->get();

              // Centers fetch karo
        $centers = \App\Models\Center::orderBy('center_name')->pluck('center_name', 'id');

            return view('user.edit', compact('user', 'roles', 'customFields', 'centers'));
        } else {
            return redirect()->back();
        }
    }


    public function update(Request $request, $id)
    {
        // 🔒 SECURITY: Restrict user role updates to Director (and super admin/company)
        $currentUser = \Auth::user();
        $allowedTypes = ['Director', 'super admin', 'company'];
        
        // Check if role is being updated (role field present in request OR super admin updating)
        $isRoleUpdate = $request->has('role') || (\Auth::user()->type == 'super admin');
        
        if ($isRoleUpdate && !in_array($currentUser->type, $allowedTypes, true)) {
            Log::warning('Unauthorized user role update attempt blocked', [
                'attempted_by' => $currentUser->email,
                'attempted_by_id' => $currentUser->id,
                'attempted_by_name' => $currentUser->name,
                'attempted_by_type' => $currentUser->type,
                'target_user_id' => $id,
                'target_user_email' => User::find($id)->email ?? 'N/A',
                'requested_role_id' => $request->role ?? 'N/A (super admin)',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            return redirect()->back()->with('error', __('You do not have permission to update user roles. Only Directors / administrators can perform this action.'));
        }

        if (\Auth::user()->can('edit user')) {
            if (\Auth::user()->type == 'super admin') {
                $user = User::findOrFail($id);
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users,email,' . $id,
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                //                $role = Role::findById($request->role);
                $role = Role::findByName('company');
                $input = $request->all();
                $input['type'] = $role->name;
                // Only allow center_id change for bypass users; otherwise preserve existing
                if ($currentUser->canBypassCenterScope()) {
                    $input['center_id'] = $request->center_id ?? null;
                } else {
                    $input['center_id'] = $user->center_id;
                }
                $input['is_wfh'] = $request->boolean('is_wfh');
                $user->fill($input)->save();
                CustomField::saveData($user, $request->customField);

                $roles[] = $role->id;
                $user->roles()->sync($roles);
                
                // 🔒 SECURITY: Log role assignment
                Log::info('User role updated (super admin)', [
                    'updated_by' => $currentUser->email,
                    'updated_by_id' => $currentUser->id,
                    'target_user_id' => $user->id,
                    'target_user_email' => $user->email,
                    'assigned_role_id' => $role->id,
                    'assigned_role_name' => $role->name,
                    'ip' => request()->ip()
                ]);

                return redirect()->route('users.index')->with(
                    'success',
                    'User successfully updated.'
                );
            } else {
                $user = User::findOrFail($id);
                $this->validate(
                    $request,
                    [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users,email,' . $id,
                        'role' => 'required',
                    ]
                );

                $role          = Role::findById($request->role);
                $input         = $request->all();
                $input['type'] = $role->name;
                // Only allow center_id change for bypass users; otherwise preserve existing
                if ($currentUser->canBypassCenterScope()) {
                    $input['center_id'] = $request->center_id ?? null;
                } else {
                    $input['center_id'] = $user->center_id;
                }
                $input['is_wfh'] = $request->boolean('is_wfh');
                $user->fill($input)->save();
                Utility::employeeDetailsUpdate($user->id, \Auth::user()->creatorId());
                CustomField::saveData($user, $request->customField);

                $roles[] = $request->role;
                $user->roles()->sync($roles);
                
                // 🔒 SECURITY: Log role assignment
                Log::info('User role updated', [
                    'updated_by' => $currentUser->email,
                    'updated_by_id' => $currentUser->id,
                    'target_user_id' => $user->id,
                    'target_user_email' => $user->email,
                    'assigned_role_id' => $request->role,
                    'assigned_role_name' => $role->name,
                    'ip' => request()->ip()
                ]);

                return redirect()->route('users.index')->with(
                    'success',
                    'User successfully updated.'
                );
            }
        } else {
            return redirect()->back();
        }
    }


    public function destroy($id)
    {

        if (\Auth::user()->can('delete user')) {
            $user = User::find($id);
            if ($user) {
                $deletedBy = \Auth::user();
                $userData = [
                    'deleted_by' => $deletedBy->email,
                    'deleted_by_id' => $deletedBy->id,
                    'deleted_user_id' => $user->id,
                    'deleted_user_email' => $user->email,
                    'deleted_user_name' => $user->name,
                    'deleted_user_type' => $user->type,
                    'ip' => request()->ip(),
                    'timestamp' => now()
                ];

                if (\Auth::user()->type == 'super admin') {
                    $oldStatus = $user->delete_status;
                    if ($user->delete_status == 0) {
                        $user->delete_status = 1;
                    } else {
                        $user->delete_status = 0;
                    }
                    $user->save();
                    
                    // Log status change (soft delete toggle)
                    Log::info('User delete status changed', array_merge($userData, [
                        'old_status' => $oldStatus,
                        'new_status' => $user->delete_status,
                        'action' => 'status_toggle'
                    ]));
                }
                if (\Auth::user()->type == 'company') {
                    $employee = Employee::where(['user_id' => $user->id])->delete();
                    if ($employee) {
                        $delete_user = User::where(['id' => $user->id])->delete();
                        if ($delete_user) {
                            // Log permanent deletion
                            Log::info('User permanently deleted', array_merge($userData, [
                                'action' => 'permanent_delete',
                                'employee_deleted' => true
                            ]));
                            return redirect()->route('users.index')->with('success', __('User successfully deleted .'));
                        } else {
                            return redirect()->back()->with('error', __('Something is wrong.'));
                        }
                    } else {
                        return redirect()->back()->with('error', __('Something is wrong.'));
                    }
                }

                return redirect()->route('users.index')->with('success', __('User successfully deleted .'));
            } else {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        } else {
            return redirect()->back();
        }
    }

 public function profile()
{
    $user = Auth::user();
    $userDetail = UserDetail::where('user_id', $user->id)->first();
    $bankDetails = UserBankDetail::where('user_id', $user->id)
                                ->orderBy('priority')
                                ->get();
    
    // Get active banks from database grouped by category
    $banks = Bank::active()->ordered()->get()->groupBy('category');
    
    return view('user.profile', compact('user', 'userDetail', 'bankDetails', 'banks'));
}
   

    public function editprofile(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = User::findOrFail($userDetail['id']);
        $this->validate(
            $request,
            [
                'name' => 'required|max:120',
                'email' => 'required|email|unique:users,email,' . $userDetail['id'],
            ]
        );
        if ($request->hasFile('profile')) {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if ($settings['storage_setting'] == 'local') {
                $dir        = 'uploads/avatar/';
            } else {
                $dir        = 'uploads/avatar';
            }

            $image_path = $dir . $userDetail['avatar'];

            if (File::exists($image_path)) {
                File::delete($image_path);
            }


            $url = '';
            $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);
            if ($path['flag'] == 1) {
                $url = $path['url'];
            } else {
                return redirect()->route('profile', \Auth::user()->id)->with('error', __($path['msg']));
            }

            //            $dir        = storage_path('uploads/avatar/');
            //            $image_path = $dir . $userDetail['avatar'];
            //
            //            if(File::exists($image_path))
            //            {
            //                File::delete($image_path);
            //            }
            //
            //            if(!file_exists($dir))
            //            {
            //                mkdir($dir, 0777, true);
            //            }
            //            $path = $request->file('profile')->storeAs('uploads/avatar/', $fileNameToStore);

        }

        if (!empty($request->profile)) {
            $user['avatar'] = $fileNameToStore;
        }
        $user['name']  = $request['name'];
        $user['email'] = $request['email'];
        $user->save();
        CustomField::saveData($user, $request->customField);

        return redirect()->route('dashboard')->with(
            'success',
            'Profile successfully updated.'
        );
    }

    public function updatePassword(Request $request)
    {

        if (Auth::Check()) {
            $request->validate(
                [
                    'old_password' => 'required',
                    'password' => 'required|min:6',
                    'password_confirmation' => 'required|same:password',
                ]
            );
            $objUser          = Auth::user();
            $request_data     = $request->All();
            $current_password = $objUser->password;
            if (Hash::check($request_data['old_password'], $current_password)) {
                $user_id            = Auth::User()->id;
                $obj_user           = User::find($user_id);
                $obj_user->password = Hash::make($request_data['password']);
                $obj_user->password_changed_at = now();
                $obj_user->save();

                return redirect()->route('profile', $objUser->id)->with('success', __('Password successfully updated.'));
            } else {
                return redirect()->route('profile', $objUser->id)->with('error', __('Please enter correct current password.'));
            }
        } else {
            return redirect()->route('profile', \Auth::user()->id)->with('error', __('Something is wrong.'));
        }
    }
    // User To do module
    public function todo_store(Request $request)
    {
        $request->validate(
            ['title' => 'required|max:120']
        );

        $post            = $request->all();
        $post['user_id'] = Auth::user()->id;
        $todo            = UserToDo::create($post);


        $todo->updateUrl = route(
            'todo.update',
            [
                $todo->id,
            ]
        );
        $todo->deleteUrl = route(
            'todo.destroy',
            [
                $todo->id,
            ]
        );

        return $todo->toJson();
    }

    public function todo_update($todo_id)
    {
        $user_todo = UserToDo::find($todo_id);
        if ($user_todo->is_complete == 0) {
            $user_todo->is_complete = 1;
        } else {
            $user_todo->is_complete = 0;
        }
        $user_todo->save();
        return $user_todo->toJson();
    }

    public function todo_destroy($id)
    {
        $todo = UserToDo::find($id);
        $todo->delete();

        return true;
    }

    // change mode 'dark or light'
    public function changeMode()
    {
        $usr = \Auth::user();
        if ($usr->mode == 'light') {
            $usr->mode      = 'dark';
            $usr->dark_mode = 1;
        } else {
            $usr->mode      = 'light';
            $usr->dark_mode = 0;
        }
        $usr->save();

        return redirect()->back();
    }

    public function upgradePlan($user_id)
    {
        $user = User::find($user_id);
        $plans = Plan::get();
        return view('user.plan', compact('user', 'plans'));
    }
    public function activePlan($user_id, $plan_id)
    {

        $user       = User::find($user_id);
        $assignPlan = $user->assignPlan($plan_id);
        $plan       = Plan::find($plan_id);
        if ($assignPlan['is_success'] == true && !empty($plan)) {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            Order::create(
                [
                    'order_id' => $orderID,
                    'name' => null,
                    'card_number' => null,
                    'card_exp_month' => null,
                    'card_exp_year' => null,
                    'plan_name' => $plan->name,
                    'plan_id' => $plan->id,
                    'price' => $plan->price,
                    'price_currency' => isset(\Auth::user()->planPrice()['currency']) ? \Auth::user()->planPrice()['currency'] : '',
                    'txn_id' => '',
                    'payment_status' => 'succeeded',
                    'receipt' => null,
                    'user_id' => $user->id,
                ]
            );

            return redirect()->back()->with('success', 'Plan successfully upgraded.');
        } else {
            return redirect()->back()->with('error', 'Plan fail to upgrade.');
        }
    }

    public function userPassword($id)
    {
        $eId        = \Crypt::decrypt($id);
        $user = User::find($eId);

        return view('user.reset', compact('user'));
    }

    // find user by name or email using ajax request 

    public function findUser(Request $request)
    {
        $search = $request->search;
        $users = User::where('name', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%')->get();
        $html = '<ul class="list-group">';
        foreach ($users as $user) {
            $html .= '<li class="list-group-item">' . $user->name . ' - ' . $user->email . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public function userPasswordReset(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'password' => 'required|confirmed|same:password_confirmation',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }


        $user                 = User::where('id', $id)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->route('users.index')->with(
            'success',
            'User Password successfully updated.'
        );
    }

    // Method to show the form to edit user credentials
    public function editMyCredentials()
    {
        $user = auth()->user();
        return view('user.mycredential', compact('user'));
    }

    // users credenials update here
    public function userscred()
    {
        // $users = User::all();
        // return view('user.agent_credentials', compact('users'));


        // $user = auth()->user();
        $users = User::whereIn('type', ['Avatar', 'Closer', 'Voice'])->get();
        return view('user.agent_credentials', compact('users'));
    }

    public function usercredupdate(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
        $user->dialer_id = $request->input('dialer_id');
        $user->save();
        return redirect()->back()->with('success', 'User dialer ID updated successfully.');

        } catch (QueryException $e) {
            // If a duplicate entry error occurs, catch the exception
            if ($e->errorInfo[1] === 1062) { // Check for MySQL error code for duplicate entry
                // Log the error
                \Log::error($e->getMessage());
    
                // Redirect or return a response without showing the error to the user
                return view('avatar.error', [
                    'error_type' => 'duplicate_dialer_id',
                    'error_message' => 'This dialer id has  already been taken.',
                    'dialer_id' => $request->input('dialer_id')
                ]);
            } else {
                // If it's not a duplicate entry error, rethrow the exception
                throw $e;
            }
        }
        
    }
    // users credenials update ends here

    public function updateMyCredentials(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'dialer_id' => 'nullable|string|max:191|unique:users,dialer_id,' . $user->id,
            'pseudo_name' => 'nullable|string|max:255',
        ]);

        $user->update([
            'dialer_id' => $request->dialer_id,
            'pseudo_name' => $request->pseudo_name,
        ]);

        return redirect()->back()->with('success', 'Credentials updated successfully.');
    }

    public function reactivate($id)
    {
        if (\Auth::user()->can('edit user')) {
            $user = User::findOrFail($id);
            $user->last_login_at = now();
            $user->save();
            
            return redirect()->route('users.index')->with('success', __('User has been reactivated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
// use App\Models\TenantRole as Role;
use Spatie\Permission\Models\Role;
use Auth;

class RoleController extends Controller
{
    private function isRoleMgmtBypass(): bool
    {
        $u = \Auth::user();
      
        return $u && in_array($u->type, ['company', 'super admin'], true);
    }

    public function index()
    {
        if ($this->isRoleMgmtBypass() || \Auth::user()->can('manage role'))
        {
            $creatorId = \Auth::user()->creatorId();
            $roles = Role::query()
                ->where('created_by', $creatorId)
                // Include globally seeded/system roles (some installs use NULL/0)
                ->orWhereNull('created_by')
                ->orWhere('created_by', 0)
                // Always include the base company role if it exists
                ->orWhere('name', 'company')
                ->get();

            return view('role.index')->with('roles', $roles);
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }

    }


    public function create()
    {
        if ($this->isRoleMgmtBypass() || \Auth::user()->can('create role'))
        {
            $user = \Auth::user();
            if($user->type == 'super admin')
            {
                $permissions = Permission::all()->pluck('name', 'id')->toArray();
            }
            else
            {
                $permissions = new Collection();
                foreach($user->roles as $role)
                {
                    $permissions = $permissions->merge($role->permissions);
                }
                $permissions = $permissions->pluck('name', 'id')->toArray();
            }

            return view('role.create', ['permissions' => $permissions]);
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }

    }


    public function store(Request $request)
    {
        // 🔒 SECURITY: Restrict role creation to Director (and super admin/company)
        $currentUser = \Auth::user();
        $allowedTypes = ['Director', 'super admin', 'company'];
        
        if (!in_array($currentUser->type, $allowedTypes, true)) {
            Log::warning('Unauthorized role creation attempt blocked', [
                'attempted_by' => $currentUser->email,
                'attempted_by_id' => $currentUser->id,
                'attempted_by_name' => $currentUser->name,
                'attempted_by_type' => $currentUser->type,
                'requested_role_name' => $request->name ?? 'N/A',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            return redirect()->back()->with('error', 'You do not have permission to create roles. Only Directors / administrators can perform this action.');
        }
        
        if ($this->isRoleMgmtBypass() || \Auth::user()->can('create role'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:100|unique:roles,name,NULL,id,created_by,' . \Auth::user()->creatorId() . ',center_id,' . (\Auth::user()->center_id ?? 0),
                                   'permissions' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $name             = $request['name'];
            $role             = new Role();
            $role->name       = $name;
            $role->created_by = \Auth::user()->creatorId();
            // center_id is auto-assigned by TenantRole for non-bypass users.
            if (\Auth::user()->canBypassCenterScope()) {
                $role->center_id = $request->input('center_id') ?? null;
            }
            $permissions      = $request['permissions'];
            $role->save();

            foreach($permissions as $permission)
            {
                $p = Permission::where('id', '=', $permission)->firstOrFail();
                $role->givePermissionTo($p);
            }
            
            // 🔒 SECURITY: Log role creation
            Log::info('Role created', [
                'created_by' => $currentUser->email,
                'created_by_id' => $currentUser->id,
                'role_id' => $role->id,
                'role_name' => $role->name,
                'permissions_count' => count($permissions),
                'permissions' => $permissions,
                'ip' => request()->ip()
            ]);

            return redirect()->route('roles.index')->with(
                'Role successfully created.', 'Role ' . $role->name . ' added!'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }


    }

     public function edit(Role $role)
    {
        if ($this->isRoleMgmtBypass() || \Auth::user()->can('edit role'))
        {
            // CHANGED: Always show ALL permissions to anyone who can edit roles
            // Removed the security restriction - if you can edit roles, you see everything
            $permissions = Permission::all()->pluck('name', 'id')->toArray();

            return view('role.edit', compact('role', 'permissions'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(Request $request, Role $role)
    {
        // 🔒 SECURITY: Restrict role updates to Director (and super admin/company)
        $currentUser = \Auth::user();
        $allowedTypes = ['Director', 'super admin', 'company'];
        
        if (!in_array($currentUser->type, $allowedTypes, true)) {
            Log::warning('Unauthorized role update attempt blocked', [
                'attempted_by' => $currentUser->email,
                'attempted_by_id' => $currentUser->id,
                'attempted_by_name' => $currentUser->name,
                'attempted_by_type' => $currentUser->type,
                'target_role_id' => $role->id,
                'target_role_name' => $role->name,
                'requested_role_name' => $request->name ?? 'N/A',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            return redirect()->back()->with('error', 'You do not have permission to update roles. Only Directors / administrators can perform this action.');
        }
        
        if ($this->isRoleMgmtBypass() || \Auth::user()->can('edit role'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:100|unique:roles,name,' . $role['id'] . ',id,created_by,' . \Auth::user()->creatorId() . ',center_id,' . (\Auth::user()->center_id ?? 0),
                                   'permissions' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $input       = $request->except(['permissions']);
            $permissions = $request['permissions'];
            $role->fill($input)->save();

            $p_all = Permission::all();

            foreach($p_all as $p)
            {
                $role->revokePermissionTo($p);
            }

            foreach($permissions as $permission)
            {
                $p = Permission::where('id', '=', $permission)->firstOrFail();
                $role->givePermissionTo($p);
            }
            
            // 🔒 SECURITY: Log role update
            Log::info('Role updated', [
                'updated_by' => $currentUser->email,
                'updated_by_id' => $currentUser->id,
                'role_id' => $role->id,
                'role_name' => $role->name,
                'permissions_count' => count($permissions),
                'permissions' => $permissions,
                'ip' => request()->ip()
            ]);

            return redirect()->route('roles.index')->with(
                'Role successfully updated.', 'Role ' . $role->name . ' updated!'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }

    }


    public function destroy(Role $role)
    {
        if ($this->isRoleMgmtBypass() || \Auth::user()->can('delete role'))
        {
            $role->delete();

            return redirect()->route('roles.index')->with(
                'success', 'Role successfully deleted.'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }


    }
}

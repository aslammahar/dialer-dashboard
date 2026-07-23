<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepartmentSupport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DepartmentSupportController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $supports = DepartmentSupport::with('role')->latest()->get();
        return view('department_supports.create', compact('roles', 'supports'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'role_id' => 'required',
            'user_id' => 'required|array',
        ]);

        $support = DepartmentSupport::create([
            'title' => $request->title,
            'subject' => $request->subject,
            'description' => $request->description,
            'role_id' => $request->role_id,
        ]);

        $support->users()->sync($request->user_id);

        return redirect()->back()->with('success', 'Department support successfully created!');
    }
    public function edit($id)
    {
        $roles = Role::all();
        $editSupport = DepartmentSupport::findOrFail($id);
        $supports = DepartmentSupport::with('role')->latest()->get();

        return view('department_supports.create', compact('roles', 'supports', 'editSupport'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'role_id' => 'required',
            'user_id' => 'required|array',
        ]);

        $support = DepartmentSupport::findOrFail($id);
        $support->update([
            'title' => $request->title,
            'subject' => $request->subject,
            'description' => $request->description,
            'role_id' => $request->role_id,
        ]);

        $support->users()->sync($request->user_id);

        return redirect()->route('department_support.index')->with('success', 'Department support successfully update!');
    }
    public function destroy($id)
    {
        DepartmentSupport::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Record deleted successfully!');
    }

    public function getUsersByRole($role_id)
    {
        $user_ids = DB::table('model_has_roles')
            ->where('role_id', $role_id)
            ->where('model_type', 'App\Models\User')
            ->pluck('model_id');

        $users = User::whereIn('id', $user_ids)->select('id', 'name')->get();

        return response()->json($users);
    }
}

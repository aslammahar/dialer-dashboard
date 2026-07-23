<?php

namespace App\Http\Controllers;

use App\Models\SalaryDepartment;
use App\Models\SalaryStructure;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalaryDepartmentController extends Controller
{
    public function index()
    {
        $departments = SalaryDepartment::with(['users', 'creator'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(15);
        
        return view('salary-departments.index', compact('departments'));
    }

    public function create()
    {
        // Get unique role types from users table
        $roles = User::select('type')
                    ->distinct()
                    ->whereNotNull('type')
                    ->pluck('type', 'type');
        
        return view('salary-departments.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:salary_departments,name',
            'role_type' => 'required|string',
            'description' => 'nullable|string',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        DB::beginTransaction();
        try {
            $department = SalaryDepartment::create([
                'name' => $validated['name'],
                'role_type' => $validated['role_type'],
                'description' => $validated['description'] ?? null,
                'created_by' => Auth::id(),
                'is_active' => true
            ]);

            // Attach users to department
            $userData = [];
            foreach ($validated['user_ids'] as $userId) {
                $userData[$userId] = [
                    'assigned_date' => now(),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            $department->users()->attach($userData);

            DB::commit();
            return redirect()->route('salary-departments.index')
                           ->with('success', 'Salary Department created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create salary department: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show(SalaryDepartment $salaryDepartment)
    {
        $salaryDepartment->load(['users.userDetail', 'salaryStructures']);
        return view('salary-departments.show', compact('salaryDepartment'));
    }

public function edit(SalaryDepartment $salaryDepartment)
{
    $roles = User::select('type')
                ->distinct()
                ->whereNotNull('type')
                ->pluck('type', 'type');
    
    $salaryDepartment->load('users');
    
    // Rename variable for the view
    $department = $salaryDepartment;
    
    return view('salary-departments.edit', compact('department', 'roles'));
}

public function update(Request $request, SalaryDepartment $salaryDepartment)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:salary_departments,name,' . $salaryDepartment->id,
        'role_type' => 'required|string',
        'description' => 'nullable|string',
        'user_ids' => 'required|array|min:1',
        'user_ids.*' => 'exists:users,id',
        'is_active' => 'boolean'
    ]);

    DB::beginTransaction();
    try {
        $salaryDepartment->update([
            'name' => $validated['name'],
            'role_type' => $validated['role_type'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active')
        ]);

        // Sync users
        $userData = [];
        foreach ($validated['user_ids'] as $userId) {
            $userData[$userId] = [
                'assigned_date' => now(),
                'is_active' => true,
                'updated_at' => now()
            ];
        }
        $salaryDepartment->users()->sync($userData);

        DB::commit();
        return redirect()->route('salary-departments.index')
                       ->with('success', 'Salary Department updated successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to update salary department: ' . $e->getMessage())
                    ->withInput();
    }
}
    public function destroy(SalaryDepartment $salaryDepartment)
    {
        try {
            $salaryDepartment->delete();
            return redirect()->route('salary-departments.index')
                           ->with('success', 'Salary Department deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete salary department: ' . $e->getMessage());
        }
    }

    // AJAX endpoint to get users by role
    public function getUsersByRole(Request $request)
    {
        try {
            $roleType = $request->input('role_type');
            
            if (!$roleType) {
                return response()->json([]);
            }
            
            // Query users based on 'type' field instead of 'role'
            $users = User::where('type', $roleType)
                         ->select('id', 'name', 'email')
                         ->orderBy('name')
                         ->get()
                         ->map(function($user) {
                             return [
                                 'id' => $user->id,
                                 'full_name' => $user->name,
                                 'email' => $user->email
                             ];
                         });
            
            return response()->json($users);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching users by role: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch users'], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\SalaryStructure;
use App\Models\SalaryComponent;
use App\Models\SalaryDepartment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalaryStructureController extends Controller
{
    public function index()
    {
        // Get all departments with their active salary structures count
        $departments = SalaryDepartment::where('is_active', true)
            ->withCount(['salaryStructures' => function($query) {
                $query->where('is_active', true);
            }])
            ->with(['salaryStructures' => function($query) {
                $query->where('is_active', true)
                      ->with(['user.userDetail', 'components']);
            }])
            ->get();
        
        // Calculate totals for each department
        $departments = $departments->map(function($dept) {
            $dept->total_basic = $dept->salaryStructures->sum('basic_salary');
            $dept->total_allowances = $dept->salaryStructures->sum(function($structure) {
                return $structure->total_allowances + $structure->punctuality;
            });
            $dept->total_deductions = $dept->salaryStructures->sum('total_deductions');
            $dept->total_net = $dept->salaryStructures->sum('net_salary');
            return $dept;
        });
        
        return view('salary-structures.index', compact('departments'));
    }

    // New method to show department details
    public function showDepartment(SalaryDepartment $department)
    {
        $salaryStructures = SalaryStructure::with(['user.userDetail', 'components'])
                                          ->where('salary_department_id', $department->id)
                                          ->where('is_active', true)
                                          ->orderBy('created_at', 'desc')
                                          ->get();
        
        return view('salary-structures.department-detail', compact('department', 'salaryStructures'));
    }

    public function create()
    {
        $departments = SalaryDepartment::where('is_active', true)->get();
        return view('salary-structures.create', compact('departments'));
    }

    // Bulk create page
    public function createBulk()
    {
        $departments = SalaryDepartment::where('is_active', true)->get();
        return view('salary-structures.create-bulk', compact('departments'));
    }

    // Get department details with existing structures
    public function getDepartmentStructures(Request $request)
    {
        try {
            $departmentId = $request->input('department_id');
            
            if (!$departmentId) {
                return response()->json(['error' => 'Department ID is required'], 400);
            }
            
            $department = SalaryDepartment::with([
                'users.userDetail',
                'users.salaryStructures' => function($query) {
                    $query->where('is_active', true)->with('components');
                }
            ])->find($departmentId);
            
            if (!$department) {
                return response()->json(['error' => 'Department not found'], 404);
            }

            $employees = $department->users->map(function($user) {
                $activeSalary = $user->salaryStructures->where('is_active', true)->first();
                
                return [
                    'id' => $user->id,
                    'full_name' => $user->userDetail->full_name ?? $user->name,
                    'email' => $user->email,
                    'has_salary_structure' => !is_null($activeSalary),
                    'salary_structure' => $activeSalary ? [
                        'id' => $activeSalary->id,
                        'basic_salary' => $activeSalary->basic_salary,
                        'working_days' => $activeSalary->working_days,
                        'punctuality' => $activeSalary->punctuality,
                        'effective_from' => $activeSalary->effective_from->format('Y-m-d'),
                        'components' => $activeSalary->components->map(function($comp) {
                            return [
                                'id' => $comp->id,
                                'name' => $comp->component_name,
                                'type' => $comp->component_type,
                                'amount' => $comp->amount,
                                'is_taxable' => $comp->is_taxable
                            ];
                        })
                    ] : null
                ];
            });
            
            return response()->json([
                'department' => [
                    'id' => $department->id,
                    'name' => $department->name,
                    'role_type' => $department->role_type
                ],
                'employees' => $employees
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching department structures: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch data', 'message' => $e->getMessage()], 500);
        }
    }

    // Store bulk salary structures
    public function storeBulk(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:salary_departments,id',
            'structures' => 'required|array|min:1',
            'structures.*.user_id' => 'required|exists:users,id',
            'structures.*.basic_salary' => 'required|numeric|min:0',
            'structures.*.working_days' => 'required|integer|min:1|max:31',
            'structures.*.punctuality' => 'nullable|numeric|min:0',
            'structures.*.effective_from' => 'required|date',
            'structures.*.components' => 'nullable|array',
            'structures.*.components.*.name' => 'required_with:structures.*.components|string',
            'structures.*.components.*.type' => 'required_with:structures.*.components|in:allowance,deduction',
            'structures.*.components.*.amount' => 'required_with:structures.*.components|numeric|min:0',
            'structures.*.components.*.is_taxable' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $successCount = 0;
            $errors = [];

            foreach ($validated['structures'] as $index => $structureData) {
                try {
                    // Deactivate existing salary structure
                    SalaryStructure::where('user_id', $structureData['user_id'])
                                  ->where('is_active', true)
                                  ->update([
                                      'is_active' => false,
                                      'effective_to' => now()
                                  ]);

                    // Create new salary structure
                    $salaryStructure = SalaryStructure::create([
                        'user_id' => $structureData['user_id'],
                        'salary_department_id' => $validated['department_id'],
                        'basic_salary' => $structureData['basic_salary'],
                        'working_days' => $structureData['working_days'],
                        'punctuality' => $structureData['punctuality'] ?? 0,
                        'effective_from' => $structureData['effective_from'],
                        'is_active' => true,
                        'created_by' => Auth::id()
                    ]);

                    // Add components
                    if (!empty($structureData['components'])) {
                        foreach ($structureData['components'] as $component) {
                            SalaryComponent::create([
                                'salary_structure_id' => $salaryStructure->id,
                                'component_name' => $component['name'],
                                'component_type' => $component['type'],
                                'amount' => $component['amount'],
                                'is_taxable' => $component['is_taxable'] ?? true
                            ]);
                        }
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Error for employee at position " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            DB::commit();
            
            if ($successCount > 0) {
                $message = "Successfully created {$successCount} salary structure(s).";
                if (!empty($errors)) {
                    $message .= " Errors: " . implode('; ', $errors);
                }
                return redirect()->route('salary-structures.index')
                               ->with('success', $message);
            } else {
                throw new \Exception("Failed to create any salary structures. " . implode('; ', $errors));
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating bulk salary structures: ' . $e->getMessage());
            return back()->with('error', 'Failed to create salary structures: ' . $e->getMessage())
                        ->withInput();
        }
    }

    // Update bulk salary structures
    public function updateBulk(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:salary_departments,id',
            'structures' => 'required|array|min:1',
            'structures.*.id' => 'nullable|exists:salary_structures,id',
            'structures.*.user_id' => 'required|exists:users,id',
            'structures.*.basic_salary' => 'required|numeric|min:0',
            'structures.*.working_days' => 'required|integer|min:1|max:31',
            'structures.*.punctuality' => 'nullable|numeric|min:0',
            'structures.*.effective_from' => 'required|date',
            'structures.*.is_active' => 'boolean',
            'structures.*.components' => 'nullable|array',
            'structures.*.components.*.name' => 'required_with:structures.*.components|string',
            'structures.*.components.*.type' => 'required_with:structures.*.components|in:allowance,deduction',
            'structures.*.components.*.amount' => 'required_with:structures.*.components|numeric|min:0',
            'structures.*.components.*.is_taxable' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $successCount = 0;
            $errors = [];

            foreach ($validated['structures'] as $index => $structureData) {
                try {
                    if (!empty($structureData['id'])) {
                        // Update existing structure
                        $salaryStructure = SalaryStructure::findOrFail($structureData['id']);
                        $salaryStructure->update([
                            'basic_salary' => $structureData['basic_salary'],
                            'working_days' => $structureData['working_days'],
                            'punctuality' => $structureData['punctuality'] ?? 0,
                            'effective_from' => $structureData['effective_from'],
                            'is_active' => $structureData['is_active'] ?? true
                        ]);

                        // Delete old components
                        $salaryStructure->components()->delete();
                    } else {
                        // Create new structure
                        SalaryStructure::where('user_id', $structureData['user_id'])
                                      ->where('is_active', true)
                                      ->update([
                                          'is_active' => false,
                                          'effective_to' => now()
                                      ]);

                        $salaryStructure = SalaryStructure::create([
                            'user_id' => $structureData['user_id'],
                            'salary_department_id' => $validated['department_id'],
                            'basic_salary' => $structureData['basic_salary'],
                            'working_days' => $structureData['working_days'],
                            'punctuality' => $structureData['punctuality'] ?? 0,
                            'effective_from' => $structureData['effective_from'],
                            'is_active' => true,
                            'created_by' => Auth::id()
                        ]);
                    }

                    // Add components
                    if (!empty($structureData['components'])) {
                        foreach ($structureData['components'] as $component) {
                            SalaryComponent::create([
                                'salary_structure_id' => $salaryStructure->id,
                                'component_name' => $component['name'],
                                'component_type' => $component['type'],
                                'amount' => $component['amount'],
                                'is_taxable' => $component['is_taxable'] ?? true
                            ]);
                        }
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Error for employee at position " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            DB::commit();
            
            $message = "Successfully updated {$successCount} salary structure(s).";
            if (!empty($errors)) {
                $message .= " Errors: " . implode('; ', $errors);
            }
            return redirect()->route('salary-structures.index')
                           ->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating bulk salary structures: ' . $e->getMessage());
            return back()->with('error', 'Failed to update salary structures: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'department_id' => 'required|exists:salary_departments,id',
            'basic_salary' => 'required|numeric|min:0',
            'working_days' => 'required|integer|min:1|max:31',
            'punctuality' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'components' => 'nullable|array',
            'components.*.name' => 'required_with:components|string',
            'components.*.type' => 'required_with:components|in:allowance,deduction',
            'components.*.amount' => 'required_with:components|numeric|min:0',
            'components.*.is_taxable' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            SalaryStructure::where('user_id', $validated['user_id'])
                          ->where('is_active', true)
                          ->update([
                              'is_active' => false,
                              'effective_to' => now()
                          ]);

            $salaryStructure = SalaryStructure::create([
                'user_id' => $validated['user_id'],
                'salary_department_id' => $validated['department_id'],
                'basic_salary' => $validated['basic_salary'],
                'working_days' => $validated['working_days'],
                'punctuality' => $validated['punctuality'] ?? 0,
                'effective_from' => $validated['effective_from'],
                'is_active' => true,
                'created_by' => Auth::id()
            ]);

            if (!empty($validated['components'])) {
                foreach ($validated['components'] as $component) {
                    SalaryComponent::create([
                        'salary_structure_id' => $salaryStructure->id,
                        'component_name' => $component['name'],
                        'component_type' => $component['type'],
                        'amount' => $component['amount'],
                        'is_taxable' => $component['is_taxable'] ?? true
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('salary-structures.index')
                           ->with('success', 'Salary structure created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating salary structure: ' . $e->getMessage());
            return back()->with('error', 'Failed to create salary structure: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show(SalaryStructure $salaryStructure)
    {
        $salaryStructure->load(['user.userDetail', 'salaryDepartment', 'components']);
        return view('salary-structures.show', compact('salaryStructure'));
    }

    public function edit(SalaryStructure $salaryStructure)
    {
        $departments = SalaryDepartment::where('is_active', true)->get();
        $salaryStructure->load('components');
        
        return view('salary-structures.edit', compact('salaryStructure', 'departments'));
    }

    public function update(Request $request, SalaryStructure $salaryStructure)
    {
        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'working_days' => 'required|integer|min:1|max:31',
            'punctuality' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'is_active' => 'boolean',
            'components' => 'nullable|array',
            'components.*.name' => 'required_with:components|string',
            'components.*.type' => 'required_with:components|in:allowance,deduction',
            'components.*.amount' => 'required_with:components|numeric|min:0',
            'components.*.is_taxable' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $salaryStructure->update([
                'basic_salary' => $validated['basic_salary'],
                'working_days' => $validated['working_days'],
                'punctuality' => $validated['punctuality'] ?? 0,
                'effective_from' => $validated['effective_from'],
                'is_active' => $request->has('is_active')
            ]);

            $salaryStructure->components()->delete();
            
            if (!empty($validated['components'])) {
                foreach ($validated['components'] as $component) {
                    SalaryComponent::create([
                        'salary_structure_id' => $salaryStructure->id,
                        'component_name' => $component['name'],
                        'component_type' => $component['type'],
                        'amount' => $component['amount'],
                        'is_taxable' => $component['is_taxable'] ?? true
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('salary-structures.index')
                           ->with('success', 'Salary structure updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating salary structure: ' . $e->getMessage());
            return back()->with('error', 'Failed to update salary structure: ' . $e->getMessage())
                        ->withInput();
        }
    }

public function inactive()
{
    // Get all salary structures where the department has been deleted (soft deleted)
    $inactiveStructures = SalaryStructure::whereHas('salaryDepartment', function($query) {
            $query->onlyTrashed(); // Only get structures where department is soft deleted
        })
        ->with(['user.userDetail', 'salaryDepartment' => function($query) {
            $query->withTrashed(); // Include soft deleted departments in the results
        }])
        ->get();
    
    // Group structures by deleted department
    $departments = $inactiveStructures->groupBy('salary_department_id')->map(function($structures) {
        $firstStructure = $structures->first();
        return (object)[
            'id' => $firstStructure->salaryDepartment->id,
            'name' => $firstStructure->salaryDepartment->name,
            'role_type' => $firstStructure->salaryDepartment->role_type,
            'deleted_at' => $firstStructure->salaryDepartment->deleted_at,
            'salaryStructures' => $structures
        ];
    })->values();
    
    return view('salary-structures.inactive', compact('departments', 'inactiveStructures'));
}
/**
 * Delete multiple inactive salary structures at once.
 */
public function bulkDelete(Request $request)
{
    $request->validate([
        'structure_ids' => 'required|array|min:1',
        'structure_ids.*' => 'exists:salary_structures,id'
    ]);

    try {
        // Get the structures with their departments (including trashed)
        $structures = SalaryStructure::whereIn('id', $request->structure_ids)
            ->with(['salaryDepartment' => function($query) {
                $query->withTrashed();
            }])
            ->get();

        // Verify all structures have deleted departments
        $invalidStructures = $structures->filter(function($structure) {
            return !$structure->salaryDepartment || !$structure->salaryDepartment->trashed();
        });

        if ($invalidStructures->count() > 0) {
            return redirect()
                ->route('salary-structures.inactive')
                ->with('error', 'Some salary structures have active departments and cannot be deleted from here.');
        }

        $count = $structures->count();
        
        // Force delete all selected structures (permanent delete)
        SalaryStructure::whereIn('id', $request->structure_ids)->forceDelete();

        return redirect()
            ->route('salary-structures.inactive')
            ->with('success', "Successfully deleted {$count} salary structure(s) with deleted departments.");

    } catch (\Exception $e) {
        return redirect()
            ->route('salary-structures.inactive')
            ->with('error', 'An error occurred while deleting salary structures: ' . $e->getMessage());
    }
}

/**
 * Modified destroy method - handles both active and inactive structures
 * Update your existing destroy method to this:
 */
public function destroy(SalaryStructure $salaryStructure)
{
    try {
        $employeeName = $salaryStructure->user->userDetail->full_name ?? $salaryStructure->user->name;
        $isActive = $salaryStructure->is_active;
        
        $salaryStructure->delete();

        $redirectRoute = $isActive ? 'salary-structures.index' : 'salary-structures.inactive';
        
        return redirect()
            ->route($redirectRoute)
            ->with('success', "Successfully deleted salary structure for {$employeeName}.");

    } catch (\Exception $e) {
        return back()
            ->with('error', 'An error occurred while deleting the salary structure: ' . $e->getMessage());
    }
}

    public function getUsersByDepartment(Request $request)
    {
        try {
            $departmentId = $request->input('department_id');
            
            if (!$departmentId) {
                return response()->json([]);
            }
            
            $department = SalaryDepartment::with('users.userDetail')->find($departmentId);
            
            if (!$department) {
                return response()->json(['error' => 'Department not found'], 404);
            }

            $users = $department->users->map(function($user) {
                return [
                    'id' => $user->id,
                    'full_name' => $user->userDetail->full_name ?? $user->name,
                    'email' => $user->email,
                    'has_salary_structure' => $user->salaryStructures()->where('is_active', true)->exists()
                ];
            });
            
            return response()->json($users);
            
        } catch (\Exception $e) {
            Log::error('Error fetching users by department: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch users', 'message' => $e->getMessage()], 500);
        }
    }
}
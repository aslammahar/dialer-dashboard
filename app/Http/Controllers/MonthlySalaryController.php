<?php

namespace App\Http\Controllers;

use App\Models\MonthlySalary;
use App\Models\SalaryStructure;
use App\Models\SalaryDepartment;
use App\Models\Attendance;
use App\Models\TaxSlab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlySalaryController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = $request->input('year', date('Y'));
        $currentMonth = $request->input('month', date('n'));
        $departmentId = $request->input('salary_department_id');
        
        $query = MonthlySalary::with(['user.userDetail', 'salaryDepartment'])
                              ->where('year', $currentYear)
                              ->where('month', $currentMonth);
        
        if ($departmentId) {
            $query->where('salary_department_id', $departmentId);
        }
        
        $monthlySalaries = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $years = range($currentYear, $currentYear - 5);
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
        }
        
        return view('monthly-salaries.index', compact('monthlySalaries', 'years', 'months', 'currentYear', 'currentMonth'));
    }

    public function create(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('n'));
        $departmentId = $request->input('department_id');
        
        // Get all active salary departments for selection
        $allDepartments = SalaryDepartment::where('is_active', true)
                                         ->orderBy('name')
                                         ->get();
        
        // If department is selected, load its employees
        $selectedDepartment = null;
        $employees = collect();
        
        if ($departmentId) {
            $selectedDepartment = SalaryDepartment::with(['activeUsers.salaryStructures' => function($query) {
                                                $query->where('is_active', true);
                                            }])
                                            ->findOrFail($departmentId);
            
            // Filter users who have active salary structures
            $employees = $selectedDepartment->activeUsers->filter(function($user) {
                return $user->salaryStructures()->where('is_active', true)->exists();
            });
            
            // Check if salaries already exist for this month
            $existingSalaries = MonthlySalary::where('year', $year)
                                            ->where('month', $month)
                                            ->where('salary_department_id', $departmentId)
                                            ->pluck('user_id')
                                            ->toArray();
            
            if (!empty($existingSalaries)) {
                return redirect()->route('monthly-salaries.create', [
                    'year' => $year,
                    'month' => $month
                ])->with('warning', 'Salaries for some employees in this department already exist for the selected month. Please check the monthly salaries list.');
            }
        }
        
        $years = range(date('Y'), date('Y') - 5);
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
        }
        
        return view('monthly-salaries.create', compact(
            'allDepartments', 
            'selectedDepartment', 
            'employees', 
            'years', 
            'months', 
            'year', 
            'month',
            'departmentId'
        ));
    }

    
public function store(Request $request)
{
    $validated = $request->validate([
        'year' => 'required|integer|min:2020',
        'month' => 'required|integer|min:1|max:12',
        'department_id' => 'required|exists:salary_departments,id',
        'salaries' => 'required|array|min:1',
        'salaries.*.user_id' => 'required|exists:users,id',
        'salaries.*.salary_structure_id' => 'required|exists:salary_structures,id',
        'salaries.*.present_days' => 'required|integer|min:0',
        'salaries.*.absent_days' => 'nullable|integer|min:0',
        'salaries.*.leave_days' => 'nullable|integer|min:0',
        'salaries.*.bonus' => 'nullable|numeric|min:0',
        'salaries.*.additional_deductions' => 'nullable|numeric|min:0',
        'salaries.*.remarks' => 'nullable|string'
    ]);

    DB::beginTransaction();
    try {
        $generatedCount = 0;
        $skippedCount = 0;
        
        // Get all user IDs to check existing salaries in one query
        $userIds = collect($validated['salaries'])->pluck('user_id')->toArray();
        
        $existingUserIds = MonthlySalary::where('year', $validated['year'])
                                       ->where('month', $validated['month'])
                                       ->whereIn('user_id', $userIds)
                                       ->pluck('user_id')
                                       ->toArray();
        
        // Get all structure IDs to fetch in one query
        $structureIds = collect($validated['salaries'])->pluck('salary_structure_id')->unique()->toArray();
        
        // Fetch all structures with components in one query - OPTIMIZED
        $structures = SalaryStructure::with(['allowances', 'deductions'])
                                    ->whereIn('id', $structureIds)
                                    ->get()
                                    ->keyBy('id');
        
        $salaryRecords = [];
        
        foreach ($validated['salaries'] as $salaryData) {
            // Skip if already exists
            if (in_array($salaryData['user_id'], $existingUserIds)) {
                $skippedCount++;
                continue;
            }
            
            $structure = $structures->get($salaryData['salary_structure_id']);
            
            // Calculate salary components
            $perDaySalary = floatval($structure->basic_salary) / floatval($structure->working_days);
            $calculatedBasicSalary = $perDaySalary * floatval($salaryData['present_days']);
            
            $totalAllowances = floatval($structure->allowances->sum('amount'));
            $totalDeductions = floatval($structure->deductions->sum('amount'));
            $totalDeductions += floatval($salaryData['additional_deductions'] ?? 0);
            
            $punctuality = floatval($structure->punctuality ?? 0);
            $bonus = floatval($salaryData['bonus'] ?? 0);
            
            // Calculate monthly gross salary (before tax)
            $monthlyGrossSalary = $calculatedBasicSalary + $punctuality + $totalAllowances - $totalDeductions + $bonus;
            
            // Calculate tax based on yearly salary (monthly * 12)
            $taxData = TaxSlab::calculateTax($monthlyGrossSalary);
            
            // Calculate final net salary (gross - monthly tax)
            $netSalary = $monthlyGrossSalary - floatval($taxData['tax_amount']);
            
            $salaryRecords[] = [
                'user_id' => $salaryData['user_id'],
                'salary_department_id' => $validated['department_id'],
                'salary_structure_id' => $salaryData['salary_structure_id'],
                'year' => $validated['year'],
                'month' => $validated['month'],
                'basic_salary' => $structure->basic_salary,
                'working_days' => $structure->working_days,
                'present_days' => $salaryData['present_days'],
                'absent_days' => $salaryData['absent_days'] ?? 0,
                'leave_days' => $salaryData['leave_days'] ?? 0,
                'punctuality' => $punctuality,
                'total_allowances' => $totalAllowances,
                'total_deductions' => $totalDeductions,
                'tax_amount' => floatval($taxData['tax_amount']),
                'tax_percentage' => floatval($taxData['tax_percentage']),
                'tax_slab_id' => $taxData['tax_slab_id'],
                'bonus' => $bonus,
                'gross_salary' => $monthlyGrossSalary,
                'net_salary' => $netSalary,
                'status' => 'draft',
                'remarks' => $salaryData['remarks'] ?? null,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            $generatedCount++;
        }
        
        // Batch insert - OPTIMIZED
        if (!empty($salaryRecords)) {
            MonthlySalary::insert($salaryRecords);
        }

        DB::commit();
        
        $message = "Successfully generated {$generatedCount} salary record(s).";
        if ($skippedCount > 0) {
            $message .= " Skipped {$skippedCount} record(s) that already exist.";
        }
        
        return redirect()->route('monthly-salaries.index', [
            'year' => $validated['year'],
            'month' => $validated['month'],
            'salary_department_id' => $validated['department_id']
        ])->with('success', $message);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Salary creation error: ' . $e->getMessage());
        return back()->with('error', 'Failed to create monthly salaries: ' . $e->getMessage())
                    ->withInput();
    }
}

    public function show(MonthlySalary $monthlySalary)
    {
        $monthlySalary->load(['user.userDetail', 'salaryDepartment', 'salaryStructure.components']);
        return view('monthly-salaries.show', compact('monthlySalary'));
    }

    public function edit(MonthlySalary $monthlySalary)
    {
        if ($monthlySalary->status !== 'draft') {
            return back()->with('error', 'Only draft salaries can be edited.');
        }
        
        $monthlySalary->load('salaryStructure.components');
        return view('monthly-salaries.edit', compact('monthlySalary'));
    }


    public function update(Request $request, MonthlySalary $monthlySalary)
{
    if ($monthlySalary->status !== 'draft') {
        return back()->with('error', 'Only draft salaries can be updated.');
    }
    
    $validated = $request->validate([
        'present_days' => 'required|integer|min:0',
        'absent_days' => 'nullable|integer|min:0',
        'leave_days' => 'nullable|integer|min:0',
        'bonus' => 'nullable|numeric|min:0',
        'additional_deductions' => 'nullable|numeric|min:0',
        'remarks' => 'nullable|string'
    ]);

    DB::beginTransaction();
    try {
        // Load structure with components in one query
        $structure = $monthlySalary->salaryStructure()->with(['allowances', 'deductions'])->first();
        
        // Recalculate salary
        $perDaySalary = floatval($structure->basic_salary) / floatval($structure->working_days);
        $calculatedBasicSalary = $perDaySalary * floatval($validated['present_days']);
        
        $totalAllowances = floatval($structure->allowances->sum('amount'));
        $totalDeductions = floatval($structure->deductions->sum('amount'));
        $totalDeductions += floatval($validated['additional_deductions'] ?? 0);
        
        $punctuality = floatval($structure->punctuality ?? 0);
        $bonus = floatval($validated['bonus'] ?? 0);
        
        // Calculate monthly gross salary (before tax)
        $monthlyGrossSalary = $calculatedBasicSalary + $punctuality + $totalAllowances - $totalDeductions + $bonus;
        
        // Calculate tax based on yearly salary (monthly * 12)
        $taxData = TaxSlab::calculateTax($monthlyGrossSalary);
        
        // Calculate final net salary (gross - monthly tax)
        $netSalary = $monthlyGrossSalary - floatval($taxData['tax_amount']);
        
        $monthlySalary->update([
            'present_days' => $validated['present_days'],
            'absent_days' => $validated['absent_days'] ?? 0,
            'leave_days' => $validated['leave_days'] ?? 0,
            'total_deductions' => $totalDeductions,
            'tax_amount' => floatval($taxData['tax_amount']),
            'tax_percentage' => floatval($taxData['tax_percentage']),
            'tax_slab_id' => $taxData['tax_slab_id'],
            'bonus' => $bonus,
            'gross_salary' => $monthlyGrossSalary,
            'net_salary' => $netSalary,
            'remarks' => $validated['remarks'] ?? null
        ]);

        DB::commit();
        return redirect()->route('monthly-salaries.index')
                       ->with('success', 'Monthly salary updated successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Salary update error: ' . $e->getMessage());
        return back()->with('error', 'Failed to update monthly salary: ' . $e->getMessage())
                    ->withInput();
    }
}


    public function approve(MonthlySalary $monthlySalary)
    {
        try {
            $monthlySalary->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);
            
            return back()->with('success', 'Salary approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve salary: ' . $e->getMessage());
        }
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'salary_ids' => 'required|array|min:1',
            'salary_ids.*' => 'exists:monthly_salaries,id'
        ]);

        try {
            MonthlySalary::whereIn('id', $validated['salary_ids'])
                        ->where('status', 'draft')
                        ->update([
                            'status' => 'approved',
                            'approved_by' => Auth::id(),
                            'approved_at' => now()
                        ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Salaries approved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve salaries: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get attendance data for salary calculation
    public function getAttendanceData(Request $request)
    {
        $userId = $request->input('user_id');
        $year = $request->input('year');
        $month = $request->input('month');
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $attendances = Attendance::where('employee_id', $userId)
                                ->whereBetween('date', [$startDate, $endDate])
                                ->get();
        
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $attendances->where('status', 'absent')->count();
        $leaveDays = $attendances->where('status', 'leave')->count();
        
        return response()->json([
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'leave_days' => $leaveDays,
            'total_days' => $presentDays + $absentDays + $leaveDays
        ]);
    }

public function inactive(Request $request)
{
    $currentYear = $request->input('year', date('Y'));
    $currentMonth = $request->input('month', date('n'));
    
    $inactiveSalaries = MonthlySalary::with(['user.userDetail', 'salaryDepartment' => function($query) {
            $query->withTrashed();
        }])
        ->where('year', $currentYear)
        ->where('month', $currentMonth)
        ->get()
        ->filter(function($salary) {
            return !$salary->salary_department_id || 
                   !$salary->salaryDepartment || 
                   $salary->salaryDepartment->trashed();
        });
    
    $departmentGroups = $inactiveSalaries->groupBy(function($salary) {
        if (!$salary->salary_department_id || !$salary->salaryDepartment) {
            return 'no_department';
        }
        return $salary->salary_department_id;
    });
    
    $departments = collect();
    
    foreach($departmentGroups as $key => $salaries) {
        if ($key === 'no_department') {
            $dept = (object)[
                'id' => 0,
                'name' => 'No Department',
                'role_type' => 'N/A',
                'deleted_at' => null,
                'monthlySalaries' => $salaries
            ];
        } else {
            $dept = $salaries->first()->salaryDepartment;
            $dept->monthlySalaries = $salaries;
        }
        $departments->push($dept);
    }
    
    $years = range($currentYear, $currentYear - 5);
    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
    }
    
    return view('monthly-salaries.inactive', compact('departments', 'inactiveSalaries', 'years', 'months', 'currentYear', 'currentMonth'));
}

/**
 * Delete multiple monthly salaries with deleted departments at once.
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

        // Verify all structures don't have active departments
        $invalidStructures = $structures->filter(function($structure) {
            // Invalid if has department AND department is not trashed
            return $structure->salary_department_id && 
                   $structure->salaryDepartment && 
                   !$structure->salaryDepartment->trashed();
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
            ->with('success', "Successfully deleted {$count} orphaned salary structure(s).");

    } catch (\Exception $e) {
        return redirect()
            ->route('salary-structures.inactive')
            ->with('error', 'An error occurred while deleting salary structures: ' . $e->getMessage());
    }
}

/**
 * Update your existing destroy method to handle both active and inactive
 */

public function destroy(MonthlySalary $monthlySalary)
{
    try {
        // Load the department with trashed records
        $monthlySalary->load(['salaryDepartment' => function($query) {
            $query->withTrashed();
        }]);
        
        $employeeName = $monthlySalary->user->userDetail->full_name ?? $monthlySalary->user->name;
        $period = $monthlySalary->period;
        
        // Check if salary has completed payment
        if ($monthlySalary->hasPayment() && $monthlySalary->payment->payment_status === 'completed') {
            return back()->with('error', 'Cannot delete a monthly salary with completed payment.');
        }
        
        // Check if structure is orphaned (no department, or department is deleted)
        $isOrphaned = !$monthlySalary->salary_department_id || 
                      !$monthlySalary->salaryDepartment || 
                      ($monthlySalary->salaryDepartment && $monthlySalary->salaryDepartment->trashed());
        
        // Force delete (permanent) if orphaned, otherwise soft delete
        if ($isOrphaned) {
            $monthlySalary->forceDelete();
        } else {
            $monthlySalary->delete();
        }

        $redirectRoute = $isOrphaned ? 'monthly-salaries.inactive' : 'monthly-salaries.index';
        
        return redirect()
            ->route($redirectRoute)
            ->with('success', "Successfully deleted monthly salary for {$employeeName} ({$period}).");

    } catch (\Exception $e) {
        return back()
            ->with('error', 'An error occurred while deleting the monthly salary: ' . $e->getMessage());
    }
}


}
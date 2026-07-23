<?php

namespace App\Http\Controllers;

use App\Models\MonthlySalary;
use App\Models\SalaryDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class SalarySlipController extends Controller
{
    /**
     * Display salary slips with department-wise view
     */
    public function index(Request $request)
    {
        $currentYear = request('year', date('Y'));
        $currentMonth = request('month', date('n'));

        $query = MonthlySalary::with(['user.userDetail', 'salaryDepartment'])
                              ->where('status', 'approved')
                              ->where('year', $currentYear)
                              ->where('month', $currentMonth);

        if ($request->filled('salary_department_id')) {
            $query->where('salary_department_id', $request->salary_department_id);
        }

        $salaries = $query->orderBy('salary_department_id')
                         ->orderBy('created_at', 'desc')
                         ->paginate(20);

        $departmentSummary = MonthlySalary::select(
                                'salary_department_id',
                                DB::raw('COUNT(*) as employee_count'),
                                DB::raw('SUM(net_salary) as total_salary')
                            )
                            ->where('status', 'approved')
                            ->where('year', $currentYear)
                            ->where('month', $currentMonth)
                            ->groupBy('salary_department_id')
                            ->get()
                            ->map(function($item) {
                                $dept = SalaryDepartment::find($item->salary_department_id);
                                return (object)[
                                    'department_id' => $item->salary_department_id,
                                    'department_name' => $dept ? $dept->name : 'N/A',
                                    'employee_count' => $item->employee_count,
                                    'total_salary' => $item->total_salary
                                ];
                            });

        $years = range($currentYear, $currentYear - 5);
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
        }

        return view('salary-slips.index', compact(
            'salaries', 
            'departmentSummary',
            'years', 
            'months', 
            'currentYear', 
            'currentMonth'
        ));
    }

    /**
     * Download single slip instantly
     */
   
    public function downloadDirect(MonthlySalary $monthlySalary)
{
    try {
        if ($monthlySalary->status !== 'approved') {
            return back()->with('error', 'Salary must be approved before downloading slip.');
        }

        // Load relationships INCLUDING taxSlab
        $monthlySalary->load(['user.userDetail', 'salaryDepartment', 'salaryStructure', 'taxSlab']);

        $user = $monthlySalary->user;
        $userDetail = $user->userDetail ?? (object)[
            'full_name' => $user->name ?? 'N/A', 
            'designation' => 'N/A'
        ];
        $salaryDepartment = $monthlySalary->salaryDepartment ?? (object)['name' => 'N/A'];
        $salaryStructure = $monthlySalary->salaryStructure ?? (object)[];

        // Calculate pro-rated basic salary
        $basicSalaryProRated = 0;
        $workingDays = $monthlySalary->working_days ?? 0;
        $presentDays = $monthlySalary->present_days ?? 0;
        if ($workingDays > 0) {
            $basicSalaryProRated = ($monthlySalary->basic_salary / $workingDays) * $presentDays;
        } else {
            $basicSalaryProRated = $monthlySalary->basic_salary ?? 0;
        }

        $slipNumber = $this->generateSlipNumber($monthlySalary);

        $company = [
            'name' => 'Jsons Communication',
            'address' => 'PWD, Islamabad, Pakistan',
            'phone' => '+92-XXX-XXXXXXX',
            'email' => 'info@jsons.com.pk',
        ];

        $viewData = [
            'salary' => $monthlySalary,
            'slipNumber' => $slipNumber,
            'company' => $company,
            'userDetail' => $userDetail,
            'salaryDepartment' => $salaryDepartment,
            'salaryStructure' => $salaryStructure,
            'basicSalaryProRated' => $basicSalaryProRated,
        ];

        $pdf = Pdf::loadView('salary-slips.template', $viewData);
        $fileName = 'salary_slip_' . $slipNumber . '.pdf';

        return $pdf->download($fileName);
        
    } catch (\Exception $e) {
        \Log::error('Salary slip download failed', [
            'salary_id' => $monthlySalary->id ?? 'unknown',
            'error' => $e->getMessage()
        ]);
        
        return back()->with('error', 'Failed to generate slip: ' . $e->getMessage());
    }
}

    /**
     * Bulk download by criteria
     */
    public function bulkDownloadDirect(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'salary_department_id' => 'nullable|exists:salary_departments,id'
        ]);

        try {
            $query = MonthlySalary::with(['user', 'user.userDetail', 'salaryDepartment', 'salaryStructure'])
                                  ->where('year', $validated['year'])
                                  ->where('month', $validated['month'])
                                  ->where('status', 'approved');

            if (!empty($validated['salary_department_id'])) {
                $query->where('salary_department_id', $validated['salary_department_id']);
            }

            $salaries = $query->get();

            if ($salaries->isEmpty()) {
                return back()->with('error', 'No approved salaries found.');
            }

            // Single salary? Download directly
            if ($salaries->count() === 1) {
                return $this->downloadDirect($salaries->first());
            }

            // Get department name
            $deptName = '';
            if (!empty($validated['salary_department_id'])) {
                $dept = SalaryDepartment::find($validated['salary_department_id']);
                $deptName = $dept ? '_' . str_replace(' ', '_', $dept->name) : '';
            }

            // Create ZIP
            $zip = new ZipArchive();
            $zipFileName = 'salary_slips_' . $validated['year'] . '_' . str_pad($validated['month'], 2, '0', STR_PAD_LEFT) . $deptName . '_' . time() . '.zip';
            $zipPath = storage_path('app/temp/' . $zipFileName);

            if (!file_exists(dirname($zipPath))) {
                mkdir(dirname($zipPath), 0755, true);
            }

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $company = [
                    'name' => 'Jsons Communication',
                    'address' => 'PWD, Islamabad, Pakistan',
                    'phone' => '+92-XXX-XXXXXXX',
                    'email' => 'info@jsons.com.pk',
                ];

                foreach ($salaries as $salary) {
                    if (!$salary->user) continue; // Skip if no user
                    
                    $slipNumber = $this->generateSlipNumber($salary);
                    
                    $pdf = Pdf::loadView('salary-slips.template', [
                        'salary' => $salary,
                        'slipNumber' => $slipNumber,
                        'company' => $company
                    ]);

                    $fileName = $slipNumber . '_' . str_replace(' ', '_', $salary->user->name) . '.pdf';
                    $zip->addFromString($fileName, $pdf->output());
                }

                $zip->close();
                return response()->download($zipPath)->deleteFileAfterSend(true);
            }

            return back()->with('error', 'Failed to create ZIP file.');
            
        } catch (\Exception $e) {
            Log::error('Bulk download failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->with('error', 'Failed to generate salary slips. Error: ' . $e->getMessage());
        }
    }

    /**
     * Download selected slips
     */
    public function bulkDownloadSelected(Request $request)
    {
        $validated = $request->validate([
            'salary_ids' => 'required|array|min:1',
            'salary_ids.*' => 'exists:monthly_salaries,id'
        ]);

        try {
            $salaries = MonthlySalary::with(['user', 'user.userDetail', 'salaryDepartment', 'salaryStructure'])
                                     ->whereIn('id', $validated['salary_ids'])
                                     ->where('status', 'approved')
                                     ->get();

            if ($salaries->isEmpty()) {
                return back()->with('error', 'No approved salaries found.');
            }

            if ($salaries->count() === 1) {
                return $this->downloadDirect($salaries->first());
            }

            $zip = new ZipArchive();
            $zipFileName = 'selected_salary_slips_' . time() . '.zip';
            $zipPath = storage_path('app/temp/' . $zipFileName);

            if (!file_exists(dirname($zipPath))) {
                mkdir(dirname($zipPath), 0755, true);
            }

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $company = [
                    'name' => 'Jsons Communication',
                    'address' => 'PWD, Islamabad, Pakistan',
                    'phone' => '+92-XXX-XXXXXXX',
                    'email' => 'info@jsons.com.pk',
                ];

                foreach ($salaries as $salary) {
                    if (!$salary->user) continue;
                    
                    $slipNumber = $this->generateSlipNumber($salary);
                    
                    $pdf = Pdf::loadView('salary-slips.template', [
                        'salary' => $salary,
                        'slipNumber' => $slipNumber,
                        'company' => $company
                    ]);

                    $fileName = $slipNumber . '_' . str_replace(' ', '_', $salary->user->name) . '.pdf';
                    $zip->addFromString($fileName, $pdf->output());
                }

                $zip->close();
                return response()->download($zipPath)->deleteFileAfterSend(true);
            }

            return back()->with('error', 'Failed to create ZIP file.');
            
        } catch (\Exception $e) {
            Log::error('Selected download failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->with('error', 'Failed to generate salary slips. Error: ' . $e->getMessage());
        }
    }

    /**
     * Generate slip number
     */
    private function generateSlipNumber(MonthlySalary $salary)
    {
        $prefix = 'SLIP';
        $year = $salary->year;
        $month = str_pad($salary->month, 2, '0', STR_PAD_LEFT);
        $userId = str_pad($salary->user_id, 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$month}-{$userId}";
    }
}
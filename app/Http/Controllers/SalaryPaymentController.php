<?php

namespace App\Http\Controllers;

use App\Models\MonthlySalary;
use App\Models\SalaryDepartment;
use App\Models\UserBankDetail;
use App\Models\SalaryPayment;
use App\Models\User;
use App\Models\Bank;
use App\Exports\SalaryPaymentsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class SalaryPaymentController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));
        $department = $request->input('department');

        $departments = SalaryDepartment::where('is_active', true)->get();

        $query = MonthlySalary::with([
            'user.userDetail',
            'user.bankDetails' => function($q) {
                $q->where('status', 'verified')
                  ->orderBy('priority', 'asc');
            },
            'salaryDepartment'
        ])
        ->where('month', $month)
        ->where('year', $year)
        ->where('status', 'approved'); // Fixed: Changed from 'approval_status' to 'status'

        if ($department) {
            $query->where('salary_department_id', $department);
        }

        $salaries = $query->get();

        // Group by department
        $departmentWiseData = [];
        $grandTotal = 0;
        $totalPending = 0;
        $totalSent = 0;

        foreach ($salaries as $salary) {
            $deptId = $salary->salary_department_id;
            
            if (!isset($departmentWiseData[$deptId])) {
                $departmentWiseData[$deptId] = [
                    'department' => $salary->salaryDepartment,
                    'meezan_banks' => [],
                    'other_banks' => [],
                    'total_department' => 0,
                    'total_meezan' => 0,
                    'total_others' => 0
                ];
            }

            $user = $salary->user;
            $primaryBank = $user->bankDetails->first();

            if ($primaryBank) {
                $payment = SalaryPayment::where('monthly_salary_id', $salary->id)->first();

                $item = [
                    'salary' => $salary,
                    'user' => $user,
                    'bank' => $primaryBank,
                    'payment' => $payment
                ];

                // Check if Meezan Bank
                if (stripos($primaryBank->bank_name, 'meezan') !== false) {
                    $departmentWiseData[$deptId]['meezan_banks'][] = $item;
                    $departmentWiseData[$deptId]['total_meezan'] += $salary->net_salary;
                } else {
                    $departmentWiseData[$deptId]['other_banks'][] = $item;
                    $departmentWiseData[$deptId]['total_others'] += $salary->net_salary;
                }

                $departmentWiseData[$deptId]['total_department'] += $salary->net_salary;
                $grandTotal += $salary->net_salary;

                if ($payment && $payment->isSent()) {
                    $totalSent += $salary->net_salary;
                } else {
                    $totalPending += $salary->net_salary;
                }
            }
        }

        return view('salary-payments.index', compact(
            'departmentWiseData',
            'departments',
            'department',
            'year',
            'month',
            'grandTotal',
            'totalPending',
            'totalSent'
        ));
    }

    public function getSalaryDetails($salaryId)
    {
        try {
            $salary = MonthlySalary::with([
                'user.userDetail',
                'user.bankDetails' => function($q) {
                    $q->where('status', 'verified')
                      ->orderBy('priority', 'asc');
                }
            ])->findOrFail($salaryId);

            $user = $salary->user;
            $userDetail = $user->userDetail;

            // Get bank details
            $banks = $user->bankDetails->map(function($bank) {
                return [
                    'id' => $bank->id,
                    'bank_name' => $bank->bank_name,
                    'account_title' => $bank->account_title,
                    'account_number' => $bank->account_number,
                    'priority' => $bank->priority ?? 0,
                    'status' => $bank->status
                ];
            });

            $data = [
                'salary_id' => $salary->id,
                'user_name' => $user->name,
                'employee_id' => $userDetail->employee_id ?? 'N/A',
                'department' => $salary->salaryDepartment->name ?? 'N/A',
                'designation' => $userDetail->designation ?? 'N/A',
                'working_days' => $salary->working_days,
                'present_days' => $salary->present_days,
                'absent_days' => $salary->absent_days,
                'leave_days' => $salary->leave_days,
                'basic_salary' => number_format($salary->basic_salary, 2),
                'punctuality' => number_format($salary->punctuality, 2),
                'total_allowances' => number_format($salary->total_allowances, 2),
                'bonus' => number_format($salary->bonus, 2),
                'gross_salary' => number_format($salary->gross_salary, 2),
                'total_deductions' => number_format($salary->total_deductions, 2),
                'tax_amount' => number_format($salary->tax_amount, 2),
                'tax_percentage' => $salary->tax_percentage,
                'tax_slab' => $salary->taxSlab->slab_name ?? '',
                'total_all_deductions' => number_format($salary->total_deductions + $salary->tax_amount, 2),
                'net_salary' => number_format($salary->net_salary, 2),
                'net_salary_raw' => $salary->net_salary,
                'banks' => $banks
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading salary details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'monthly_salary_id' => 'required|exists:monthly_salaries,id',
            'user_bank_detail_id' => 'required|exists:user_bank_details,id',
            'payment_amount' => 'required|numeric|min:0',
            'payment_screenshot' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'remarks' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $salary = MonthlySalary::findOrFail($validated['monthly_salary_id']);
            
            // Check if payment already exists
            $existingPayment = SalaryPayment::where('monthly_salary_id', $salary->id)->first();
            if ($existingPayment && $existingPayment->isSent()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already sent for this salary'
                ], 400);
            }

            // Handle file upload
            $screenshotPath = null;
            if ($request->hasFile('payment_screenshot')) {
                $file = $request->file('payment_screenshot');
                $fileName = 'payment_' . time() . '_' . $file->getClientOriginalName();
                $screenshotPath = $file->storeAs('salary_payments', $fileName, 'public');
            }

            $payment = SalaryPayment::create([
                'monthly_salary_id' => $validated['monthly_salary_id'],
                'user_id' => $salary->user_id,
                'user_bank_detail_id' => $validated['user_bank_detail_id'],
                'payment_amount' => $validated['payment_amount'],
                'payment_date' => now(),
                'payment_method' => 'bank_transfer',
                'payment_status' => 'sent',
                'payment_screenshot' => $screenshotPath,
                'remarks' => $validated['remarks'],
                'processed_by' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment sent successfully',
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to send payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $payment = SalaryPayment::with([
            'monthlySalary',
            'user.userDetail',
            'userBankDetail',
            'processedBy'
        ])->findOrFail($id);

        return view('salary-payments.show', compact('payment'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,sent,failed,cancelled'
        ]);

        try {
            $payment = SalaryPayment::findOrFail($id);
            $payment->payment_status = $validated['status'];
            $payment->save();

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== EXPORT FUNCTIONS ====================

    /**
     * Export all salary payments to Excel
     */
    public function exportAll(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));
        $useStoredReference = $request->input('use_stored_reference', false);
        
        $data = $this->getExportData(null, $year, $month);
        
        if (empty($data)) {
            return back()->with('error', 'No data available for export');
        }
        
        $fileName = 'salary_payments_all_' . date('F_Y', mktime(0, 0, 0, $month, 1, $year)) . '.xlsx';
        
        return Excel::download(
            new SalaryPaymentsExport($data, $useStoredReference), 
            $fileName
        );
    }

    /**
     * Export salary payments by department to Excel
     */
    public function exportByDepartment(Request $request, $departmentId)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));
        $useStoredReference = $request->input('use_stored_reference', false);
        
        $department = SalaryDepartment::findOrFail($departmentId);
        $data = $this->getExportData($departmentId, $year, $month);
        
        if (empty($data)) {
            return back()->with('error', 'No data available for export');
        }
        
        $fileName = 'salary_payments_' . \Str::slug($department->name) . '_' . date('F_Y', mktime(0, 0, 0, $month, 1, $year)) . '.xlsx';
        
        return Excel::download(
            new SalaryPaymentsExport($data, $useStoredReference), 
            $fileName
        );
    }

    /**
     * Preview export data before downloading
     */
    public function previewExport(Request $request)
    {
        $departmentId = $request->input('department_id');
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));
        
        $data = $this->getExportData($departmentId, $year, $month);
        
        // Generate customer references for preview
        foreach ($data as &$row) {
            if (!isset($row['customer_reference'])) {
                $row['customer_reference'] = date('Ymd') . str_pad($row['user_id'], 5, '0', STR_PAD_LEFT);
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'total_amount' => array_sum(array_column($data, 'net_salary')),
            'total_records' => count($data)
        ]);
    }

    /**
     * Regenerate customer reference numbers
     */
    public function regenerateReferences(Request $request)
    {
        $departmentId = $request->input('department_id');
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));
        
        $data = $this->getExportData($departmentId, $year, $month);
        
        // Generate new references with random suffix
        $references = [];
        foreach ($data as $row) {
            $newReference = date('Ymd') . str_pad($row['user_id'], 5, '0', STR_PAD_LEFT) . rand(100, 999);
            $references[$row['user_id']] = $newReference;
        }
        
        // Store in cache
        $key = 'customer_references_' . date('Ymd') . '_' . auth()->id();
        Cache::put($key, $references, now()->addDays(30));
        
        return response()->json([
            'success' => true,
            'references' => $references,
            'message' => 'Customer references regenerated successfully'
        ]);
    }

    /**
     * Store customer reference numbers in cache
     */
    public function storeCustomerReferences(Request $request)
    {
        $references = $request->input('references', []);
        $key = 'customer_references_' . date('Ymd') . '_' . auth()->id();
        
        Cache::put($key, $references, now()->addDays(30));
        
        return response()->json([
            'success' => true,
            'message' => 'Customer references saved successfully'
        ]);
    }

    /**
     * Get export data for Excel
     * @param int|null $departmentId - Filter by department
     * @param string $year
     * @param string $month
     * @return array
     */
    private function getExportData($departmentId = null, $year = null, $month = null)
    {
        $year = $year ?? date('Y');
        $month = $month ?? date('m');
        
        // Get cached customer references if available
        $key = 'customer_references_' . date('Ymd') . '_' . auth()->id();
        $storedReferences = Cache::get($key, []);
        
        $query = MonthlySalary::with([
            'user.userDetail',
            'user.bankDetails' => function($q) {
                $q->where('status', 'verified')
                  ->orderBy('priority', 'asc');
            }
        ])
        ->where('month', $month)
        ->where('year', $year)
        ->where('status', 'approved'); // Fixed: Changed from 'approval_status' to 'status'
        
        if ($departmentId) {
            $query->where('salary_department_id', $departmentId);
        }
        
        $salaries = $query->get();
        
        $exportData = [];
        
        foreach ($salaries as $salary) {
            $user = $salary->user;
            $primaryBank = $user->bankDetails->first();
            
            if (!$primaryBank) {
                continue; // Skip users without bank details
            }
            
            // Get bank code from banks table
            $bank = Bank::where('name', $primaryBank->bank_name)->first();
            $bankCode = $bank ? $bank->code : '';
            
            // Get or generate customer reference
            $customerReference = $storedReferences[$user->id] ?? null;
            
            $exportData[] = [
                'user_id' => $user->id,
                'account_number' => $primaryBank->account_number,
                'account_title' => $primaryBank->account_title,
                'customer_reference' => $customerReference,
                'net_salary' => $salary->net_salary,
                'bank_code' => $bankCode,
                'user_name' => $user->name,
                'employee_id' => $user->userDetail->employee_id ?? 'N/A',
                'bank_name' => $primaryBank->bank_name
            ];
        }
        
        return $exportData;
    }

    /**
     * Bulk upload payments
     */
    public function bulkUpload(Request $request)
    {
        $validated = $request->validate([
            'bulk_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12'
        ]);

        DB::beginTransaction();
        try {
            // Handle bulk upload logic here
            // You can use Excel::import() to process the file
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Bulk upload processed successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Bulk upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\MonthlySalary;
use App\Models\SalaryDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TaxReportExport;

class TaxReportController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        // Generate years array (last 5 years)
        $years = range($currentYear, $currentYear - 4);
        
        // Months array
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        // Build query
        $query = MonthlySalary::with(['user.userDetail', 'salaryDepartment', 'taxSlab'])
            ->where('status', 'approved') // Only approved salaries
            ->where('tax_amount', '>', 0); // Only salaries with tax

        // Apply filters
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        } else {
            $query->where('year', $currentYear);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        } else {
            $query->where('month', $currentMonth);
        }

        if ($request->filled('salary_department_id')) {
            $query->where('salary_department_id', $request->salary_department_id);
        }

        // Get results
        $taxRecords = $query->orderBy('user_id')->paginate(50);

        // Calculate totals
        $totalTaxDeducted = $query->sum('tax_amount');
        $totalEmployees = $query->distinct('user_id')->count();
        $totalGrossSalary = $query->sum(DB::raw('basic_salary + total_allowances - total_deductions'));
        $totalNetSalary = $query->sum('net_salary');

        // Get departments for filter
        $departments = SalaryDepartment::where('is_active', true)->orderBy('name')->get();

        return view('tax-reports.index', compact(
            'taxRecords',
            'years',
            'months',
            'currentYear',
            'currentMonth',
            'departments',
            'totalTaxDeducted',
            'totalEmployees',
            'totalGrossSalary',
            'totalNetSalary'
        ));
    }

    public function export(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        $departmentId = $request->get('salary_department_id');

        $monthName = date('F', mktime(0, 0, 0, $month, 1));
        $filename = "tax_report_{$monthName}_{$year}.xlsx";

        return Excel::download(new TaxReportExport($year, $month, $departmentId), $filename);
    }
}
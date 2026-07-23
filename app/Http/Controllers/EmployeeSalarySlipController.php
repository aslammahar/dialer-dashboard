<?php

namespace App\Http\Controllers;

use App\Models\MonthlySalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class EmployeeSalarySlipController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentYear = $request->input('year', date('Y'));
        $currentMonth = $request->input('month', date('n'));

        $query = MonthlySalary::with([
            'salaryDepartment',
            'payment.paymentScreenshot' // Add payment relationship
        ])
        ->where('user_id', $user->id)
        ->where('status', 'approved');

        if ($request->filled('year')) {
            $query->where('year', $currentYear);
        }
        if ($request->filled('month')) {
            $query->where('month', $currentMonth);
        }

        $salaries = $query->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(12);

        $years = range(date('Y'), date('Y') - 5);
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
        }

        return view('employee.salary-slips', compact('salaries', 'years', 'months', 'currentYear', 'currentMonth'));
    }

    public function download($id)
    {
        $user = Auth::user();
        $salary = MonthlySalary::with([
            'user.userDetail',
            'salaryDepartment',
            'salaryStructure',
            'payment.paymentScreenshot' // Add payment info to PDF
        ])
        ->where('user_id', $user->id)
        ->where('status', 'approved')
        ->findOrFail($id);

        $slipNumber = "SLIP-{$salary->year}-" . str_pad($salary->month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($salary->user_id, 4, '0', STR_PAD_LEFT);

        $company = [
            'name' => 'Jsons Communication',
            'address' => 'PWD, Islamabad, Pakistan',
            'phone' => '+92-XXX-XXXXXXX',
            'email' => 'info@jsons.com.pk',
        ];

        $pdf = Pdf::loadView('salary-slips.template', [
            'salary' => $salary,
            'slipNumber' => $slipNumber,
            'company' => $company
        ]);

        return $pdf->download('salary_slip_' . $slipNumber . '.pdf');
    }

    /**
     * View salary slip details (new method)
     */
    public function show($id)
    {
        $user = Auth::user();
        $salary = MonthlySalary::with([
            'user.userDetail',
            'salaryDepartment',
            'salaryStructure',
            'payment.paymentScreenshot'
        ])
        ->where('user_id', $user->id)
        ->where('status', 'approved')
        ->findOrFail($id);

        return view('employee.salary-slip-details', compact('salary'));
    }
}
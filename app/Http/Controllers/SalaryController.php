<?php

// app/Http/Controllers/SalaryController.php
namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month');
        $query = Salary::with('user');
        
        if ($month) {
            $query->where('salary_month', $month);
        }

        $salaries = $query->latest()->paginate(10);
        $totalSalary = $query->sum('salary');
        $months = Salary::distinct()->pluck('salary_month')->sort();

        return view('account-salary.index', compact('salaries', 'totalSalary', 'months', 'month'));
    }

    public function export(Request $request)
    {
        $month = $request->input('month');
        $query = Salary::with('user');
        
        if ($month) {
            $query->where('salary_month', $month);
        }
        
        $salaries = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = [
            'User',
            'Agent Name',
            'Designation',
            'Account Number',
            'Bank Name',
            'Account Title',
            'Salary',
            'Salary Month',
            'Bank Code'
        ];
        
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Add data
        $row = 2;
        foreach ($salaries as $salary) {
            $sheet->setCellValue('A' . $row, $salary->user->name);
            $sheet->setCellValue('B' . $row, $salary->agent_name);
            $sheet->setCellValue('C' . $row, $salary->designation);
            $sheet->setCellValue('D' . $row, $salary->account_number);
            $sheet->setCellValue('E' . $row, $salary->bank_name);
            $sheet->setCellValue('F' . $row, $salary->account_title);
            $sheet->setCellValue('G' . $row, $salary->salary);
            $sheet->setCellValue('H' . $row, $salary->salary_month);
            $sheet->setCellValue('I' . $row, $salary->bank_code);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'salaries_' . ($month ?? 'all') . '_' . now()->format('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function create()
    {
        $users = User::all();
        return view('account-salary.create', compact('users'));
    }

    // app/Http/Controllers/SalaryController.php
    public function getPreviousSalary(User $user)
    {
        $previousSalary = Salary::where('user_id', $user->id)
                                ->latest()
                                ->first();
        
        if ($previousSalary) {
            return response()->json([
                'exists' => true,
                'account_number' => $previousSalary->account_number,
                'bank_name' => $previousSalary->bank_name,
                'account_title' => $previousSalary->account_title,
                'bank_code' => $previousSalary->bank_code,
                'salary' => $previousSalary->salary,
                'salary_month' => $previousSalary->salary_month,
                'agent_name' => $previousSalary->agent_name,
                'designation' => $previousSalary->designation
            ]);
        }
        
        return response()->json(['exists' => false]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'agent_name' => 'required',
            'designation' => 'required',
            'account_number' => 'required',
            'bank_name' => 'required',
            'account_title' => 'required',
            'salary' => 'required|numeric',
            'salary_month' => 'required',
            'bank_code' => 'required',
        ]);

        Salary::create($request->all());
        return redirect()->route('salaries.index')->with('success', 'Salary record created successfully.');
    }

    public function edit(Salary $salary)
    {
        $users = User::all();
        return view('account-salary.edit', compact('salary', 'users'));
    }

    public function update(Request $request, Salary $salary)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'agent_name' => 'required',
            'designation' => 'required',
            'account_number' => 'required',
            'bank_name' => 'required',
            'account_title' => 'required',
            'salary' => 'required|numeric',
            'salary_month' => 'required',
            'bank_code' => 'required',
        ]);

        $salary->update($request->all());
        return redirect()->route('salaries.index')->with('success', 'Salary record updated successfully.');
    }

    public function destroy(Salary $salary)
    {
        $salary->delete();
        return redirect()->route('salaries.index')->with('success', 'Salary record deleted successfully.');
    }

    // app/Http/Controllers/SalaryController.php
public function show(Salary $salary)
{
    return view('account-salary.show', compact('salary'));
}
}
<?php

namespace App\Exports;

use App\Models\MonthlySalary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TaxReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $year;
    protected $month;
    protected $departmentId;

    public function __construct($year, $month, $departmentId = null)
    {
        $this->year = $year;
        $this->month = $month;
        $this->departmentId = $departmentId;
    }

    public function collection()
    {
        $query = MonthlySalary::with(['user.userDetail', 'salaryDepartment', 'taxSlab'])
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->where('status', 'approved')
            ->where('tax_amount', '>', 0);

        if ($this->departmentId) {
            $query->where('salary_department_id', $this->departmentId);
        }

        return $query->orderBy('user_id')->get();
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Employee Name',
            'Department',
            'Basic Salary',
            'Allowances',
            'Deductions',
            'Gross Salary',
            'Tax Slab',
            'Tax %',
            'Tax Amount',
            'Net Salary',
            'Period'
        ];
    }

    public function map($salary): array
    {
        $grossSalary = $salary->basic_salary + $salary->total_allowances - $salary->total_deductions;
        
        return [
            $salary->user->id,
            $salary->user->userDetail->full_name ?? $salary->user->name,
            $salary->salaryDepartment->name ?? 'N/A',
            number_format($salary->basic_salary, 2),
            number_format($salary->total_allowances, 2),
            number_format($salary->total_deductions, 2),
            number_format($grossSalary, 2),
            $salary->taxSlab ? $salary->taxSlab->range : 'N/A',
            $salary->tax_percentage . '%',
            number_format($salary->tax_amount, 2),
            number_format($salary->net_salary, 2),
            date('F Y', mktime(0, 0, 0, $salary->month, 1, $salary->year))
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Tax Report';
    }
}
<?php
namespace App\Exports;

use App\Models\AttendanceEmployee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WFHAttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $employeeId;

    public function __construct($start_date, $end_date, $employee_id = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->employee_id = $employee_id; // Include employee ID in the constructor
    }
    
    public function query()
    {
        $query = AttendanceEmployee::query()
            ->whereBetween('date', [$this->start_date, $this->end_date]);
    
        if ($this->employee_id) {
            // Apply filter for specific employee if selected
            $query->where('employee_id', $this->employee_id);
        }
    
        return $query;
    }
    

    public function collection()
    {
        // Query attendance with filters
        $attendanceQuery = AttendanceEmployee::query();

        // Filter by date range
        if (!empty($this->startDate) && !empty($this->endDate)) {
            $attendanceQuery->whereBetween('date', [$this->startDate, $this->endDate]);
        }

        // Filter by specific employee if provided
        if (!empty($this->employeeId)) {
            $attendanceQuery->where('employee_id', $this->employeeId);
        }

        // Include employee relation to access employee name
        return $attendanceQuery->with('employee')->get();
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Date',
            'Status',
            'Clock In',
            'Clock Out',
        ];
    }

    public function map($attendance): array
    {
        return [
            !empty($attendance->employee) ? $attendance->employee->name : 'N/A',  // Employee Name
            $attendance->date,
            $attendance->status,
            $attendance->clock_in,
            $attendance->clock_out,
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{

    public function collection()
    {
        $today = Carbon::today()->toDateString();

        return Attendance::with('employee')
            ->whereDate('attendance_date', $today)
            ->latest()
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->employee?->name, // Assuming 'name' is the field in Employee
            $row->uid,
            $row->state,
            $row->attendance_date,
            $row->attendance_time,
            $row->status,
            $row->type,
            optional($row->created_at)->format('Y-m-d H:i:s'),
            optional($row->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employee Name',
            'UID',
            'State',
            'Attendance Date',
            'Attendance Time',
            'Status',
            'Type',
            'Created At',
            'Updated At',
        ];
    }
}

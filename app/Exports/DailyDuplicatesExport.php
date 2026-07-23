<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DailyDuplicatesExport implements FromCollection, WithHeadings
{
    protected $report;

    public function __construct(Collection $report)
    {
        $this->report = $report;
    }

    public function collection()
    {
        return $this->report;
    }

    public function headings(): array
    {
        return ['Lead ID', 'Phone Number', 'Previous Count'];
    }
}

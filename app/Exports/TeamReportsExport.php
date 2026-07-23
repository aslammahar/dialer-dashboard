<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class TeamReportsExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $data;
    protected $dates;

    public function __construct($data, $dates)
    {
        $this->data = $data;
        $this->dates = $dates;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return array_merge(
            ['Team', 'Agent'],
            array_map(function ($date) {
                return Carbon::parse($date)->format('M d');
            }, $this->dates),
            ['Total']
        );
    }

    public function map($row): array
    {
        return $row;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:Z1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
            }
        ];
    }
}



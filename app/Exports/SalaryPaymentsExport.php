<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalaryPaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $useStoredReference;

    public function __construct($data, $useStoredReference = false)
    {
        $this->data = $data;
        $this->useStoredReference = $useStoredReference;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'ACCOUNT NUMBER',
            'BENEFICIARY NAME',
            'CUSTOMER REFERENCE NUMBER',
            'TRANS AMOUNT',
            'BANK_CODE'
        ];
    }

    public function map($row): array
    {
        // Generate or use stored customer reference number
        if ($this->useStoredReference && isset($row['customer_reference'])) {
            $customerReference = $row['customer_reference'];
        } else {
            // Generate new reference: YYYYMMDD + User ID (padded to 5 digits)
            $customerReference = date('Ymd') . str_pad($row['user_id'], 5, '0', STR_PAD_LEFT);
        }

        return [
            $row['account_number'],      // ACCOUNT NUMBER
            $row['account_title'],       // BENEFICIARY NAME
            $customerReference,           // CUSTOMER REFERENCE NUMBER
            number_format($row['net_salary'], 2, '.', ''), // TRANS AMOUNT
            $row['bank_code'] ?? ''      // BANK_CODE
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ],
        ];
    }
}
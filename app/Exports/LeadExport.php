<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadExport implements FromCollection, WithHeadings
{
    protected $leads;
    protected $endDate;

    public function __construct($leads, $endDate = null)
    {
        $this->leads = $leads;
        $this->endDate = $endDate;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Dialer ID',
            'User Names',
            'Beneficiary',
            'Plan',
            'Zip Code',
            'Phone',
            'Age',
            'State',
            'City',
            'Spouse Age',
            'Smoker',
            'Color/Hobby',
            'Licensed Agent Name',
            'Call Back Time',
            'Date',
            'Created At',
            'Qa Person',
        ];
    }

    public function collection()
    {
        // If $this->endDate is provided, use it to filter leads by date range
        if ($this->endDate) {
            $this->leads = $this->leads->where('date', '<=', $this->endDate);
        }

        // Prepare data for export from the received leads
        $data = [];
        foreach ($this->leads as $lead) {
            $data[] = [
                $lead->id,
                $lead->subject,
                $lead->users->implode('name', ', '),
                $lead->beneficiary,
                $lead->plan,
                $lead->zip_code,
                $lead->phone,
                $lead->age,
                $lead->state,
                $lead->city,
                $lead->spouse_age,
                $lead->smoker,
                $lead->color_hobby,
                $lead->licensed_agent_name,
                $lead->call_back_time,
                $lead->date,
                $lead->created_at,
            ];
        }

        return collect($data);
    }
}

